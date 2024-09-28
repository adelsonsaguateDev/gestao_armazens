<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Permissao;
use App\Models\Historico;

date_default_timezone_set('Africa/Maputo');
setlocale(LC_ALL, 'pt', 'pt.utf-8', 'pt.utf-8', 'portuguese');

class UtilizadorController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $tipo_utilizador = Permissao::all();

        return view('utilizador.index',compact('tipo_utilizador'));

    }

    public function list(Request $request)
    {

        $query = User::with(['permissoes']);


        $total = $query->count();

        if ($request->has('estado')) {
            $estado = $request->input('estado');
            $query->where('estado', $estado);

            $total = $query->count();

        }

        if ($request->has('nome')) {
            $nome = $request->input('nome');
            $query->where('name', 'like','%' . $nome . '%');

            $total = $query->count();

        }

        // Define o número de itens por página (você pode ajustar conforme necessário)
        $itensPorPagina = $request->input('limite', 10); // Padrão: 10 itens por página

        // Recupera os utilizadores paginados
        $utilizadores = $query->paginate($itensPorPagina);

        // Adiciona parâmetros de filtro à URL da páginação
        $utilizadores->appends($request->query());


        return view('utilizador.tabela', compact('utilizadores', 'total'));
    }


    public function add(Request $request)
    {

        try {

            $json['success'] = null;
            $json['code'] = null;
            $json['message'] = null;

            $historico = new Historico();

            $data = $request->validate([
                'name' => 'required|unique:users',
                'username' => 'required|unique:users',
                'email' => 'required|unique:users',
                'contacto' => 'required|unique:users',
                'password' => 'min:6|required_with:password_confirmation|same:password_confirm',
                'password_confirm' => 'min:6',

            ]);

            $data['password'] = Hash::make($data['password']);
            $data['estado'] = 1;
            $utilizador = User::create($data);
            if($utilizador){

                $utilizador->permissoes()->attach($request->permissao);
                $descricao = 'Registou o utilizador ' . $utilizador->name . '.';
                $historico->insert($utilizador->getTable(), $utilizador->id, $descricao);

                $json['success'] = true;
                $json['message'] = 'O utilizador' . $utilizador->name . ' foi adicionado com sucesso.';
                $json['code'] = 200;

            }else{
                $json['success'] = false;
                $json['message'] = 'Erro ao adicionar o utilizador '. $utilizador->name;
                $json['code'] = 500;
            }

             } catch (\Illuminate\Validation\ValidationException $e) {

            $errors = $e->validator->errors()->all();

            $json['success'] = false;
            $json['message'] = $errors;
            $json['code'] = 422; // HTTP 422 Unprocessable Entity
        }

        echo json_encode($json);


    }


    public function show_details($id)
    {

        $utilizador = User::find($id);


        $historico = Historico::where('row_id', $id)
                       ->where('tabela', 'users')->with('users')->get();


        if (!$utilizador) {
            return response()->json(['error' => 'Urilizador não encontrado'], 404);
        }

        return response()->view('utilizador.detalhes', compact('utilizador', 'historico'));
    }

    public function delete()
    {
        $id = $_POST['utilizador_id'];
        $estado = $_POST['estado'];
        $json['success'] = false;
        $utilizador = User::find($id);
        $historico = new Historico();

        if (!empty($utilizador)) {
            $data = ['estado' => $estado];
            if ($utilizador->update($data)) {
                $json['success'] = true;
                if($estado == '1'){
                    $json['message'] = 'Funcionario activado com sucesso.';

                    $descricao = 'Activou o funcionario ' . $utilizador->name . '.';
                    $historico->insert($utilizador->getTable(), $utilizador->id, $descricao);

                }else if($estado == '2'){
                    $json['message'] = 'Funcionario removido com sucesso.';

                    $descricao = 'Removeu o funcionario ' . $utilizador->name . '.';
                    $historico->insert($utilizador->getTable(), $utilizador->id, $descricao);
                }
                $json['code'] = 200;
            }else{
                $json['success'] = false;
                $json['message'] = 'Ocorreu um erro ao remover o funcionario.';
                $json['code'] = 500;
            }
        }
        echo json_encode($json);
    }

    public function show($id)
    {
        $utilizador = User::find($id);
        $tipo_utilizador = Permissao::all();
        return view('utilizador.form_update', compact('utilizador', 'tipo_utilizador'));

    }



    public function edit(Request $request)
    {
        $id = $request->id;
        $json['success'] = false;
        $json['message'] = null;
        $json['code'] = null;
        $utilizador = User::find($id);
        $historico = new Historico();

        $data = request()->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'contacto' => 'required',

        ]);


        if(!empty($request->password)){
            $data['password'] = Hash::make($request->password);
        }

        try {


            if ($utilizador->update($data)) {
                $utilizador->permissoes()->sync($request->permissao);
                $json['success'] = true;
                $json['message'] = 'Funcionario ' . $utilizador->name . ' actualizado com sucesso.';
                $json['code'] = 200;

                $descricao = "Actualizou o funcionario ". $utilizador->name ."";
                $historico->insert($utilizador->getTable(), $utilizador->id, $descricao);

            }else{
                $json['success'] = false;
                $json['message'] = 'Ocorreu um erro ao editar o funcionario.';
                $json['code'] = 500;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {

            $errors = $e->validator->errors()->all();

            $json['success'] = false;
            $json['message'] = $errors;
            $json['code'] = 422; // HTTP 422 Unprocessable Entity
        }

        echo json_encode($json);
    }

}
