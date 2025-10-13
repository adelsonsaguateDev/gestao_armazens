<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Fornecedor;
use App\Models\Permissao;
use App\Models\Historico;

date_default_timezone_set('Africa/Maputo');
setlocale(LC_ALL, 'pt', 'pt.utf-8', 'pt.utf-8', 'portuguese');

class FornecedoresController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $tipo_utilizador = Permissao::all();

        return view('fornecedores.index', compact('tipo_utilizador'));
    }

    public function list(Request $request)
    {

        $query = Fornecedor::query();


        $total = $query->count();

        if ($request->has('estado')) {
            $estado = $request->input('estado');
            $query->where('estado', $estado);

            $total = $query->count();
        }

        if ($request->has('nome')) {
            $nome = $request->input('nome');
            $query->where('nome', 'like', '%' . $nome . '%');

            $total = $query->count();
        }

        // Define o número de itens por página (você pode ajustar conforme necessário)
        $itensPorPagina = $request->input('limite', 10); // Padrão: 10 itens por página

        // Recupera os fornecedores paginados ordenados por ID (mais recentes primeiro)
        $fornecedores = $query->orderBy('id', 'desc')->paginate($itensPorPagina);

        // Adiciona parâmetros de filtro à URL da páginação
        $fornecedores->appends($request->query());


        return view('fornecedores.tabela', compact('fornecedores', 'total'));
    }


    public function add(Request $request)
    {

        try {

            $json['success'] = null;
            $json['code'] = null;
            $json['message'] = null;

            $historico = new Historico();

            $data = $request->validate([
                'nome' => 'required|unique:fornecedores',
                'telefone' => 'required|unique:fornecedores',
                'email' => 'nullable|email',
                'endereco' => 'nullable',

            ]);

            $data['estado'] = 1;
            $data['user_id'] = auth()->user()->id;
            $fornecedor = Fornecedor::create($data);
            if ($fornecedor) {
                $descricao = 'Registou o fornecedor ' . $fornecedor->nome . '.';
                $historico->insert($fornecedor->getTable(), $fornecedor->id, $descricao);

                $json['success'] = true;
                $json['message'] = 'O fornecedor ' . $fornecedor->nome . ' foi adicionado com sucesso.';
                $json['code'] = 200;
            } else {
                $json['success'] = false;
                $json['message'] = 'Erro ao adicionar o fornecedor ' . $fornecedor->nome;
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

        $fornecedor = Fornecedor::find($id);


        $historico = Historico::where('row_id', $id)
            ->where('tabela', 'fornecedores')->with('users')->get();




        if (!$fornecedor) {
            return response()->json(['error' => 'Fornecedor não encontrado'], 404);
        }

        return response()->view('fornecedores.detalhes', compact('fornecedor', 'historico'));
    }

    public function delete()
    {
        $id = $_POST['fornecedor_id'];
        $estado = $_POST['estado'];
        $json['success'] = false;
        $fornecedor = Fornecedor::find($id);
        $historico = new Historico();

        if (!empty($fornecedor)) {
            $data = ['estado' => $estado];
            if ($fornecedor->update($data)) {
                $json['success'] = true;
                if ($estado == '1') {
                    $json['message'] = 'Fornecedor activado com sucesso.';

                    $descricao = 'Activou o fornecedor ' . $fornecedor->nome . '.';
                    $historico->insert($fornecedor->getTable(), $fornecedor->id, $descricao);
                } else if ($estado == '2') {
                    $json['message'] = 'Fornecedor removido com sucesso.';

                    $descricao = 'Removeu o fornecedor ' . $fornecedor->nome . '.';
                    $historico->insert($fornecedor->getTable(), $fornecedor->id, $descricao);
                }
                $json['code'] = 200;
            } else {
                $json['success'] = false;
                $json['message'] = 'Ocorreu um erro ao remover o fornecedor.';
                $json['code'] = 500;
            }
        }
        echo json_encode($json);
    }

    public function show($id)
    {
        $fornecedor = Fornecedor::find($id);
        return view('fornecedores.form_update', compact('fornecedor'));
    }



    public function edit(Request $request)
    {
        $id = $request->id;
        $json['success'] = false;
        $json['message'] = null;
        $json['code'] = null;
        $fornecedor = Fornecedor::find($id);
        $historico = new Historico();

        $data = request()->validate([
            'nome' => 'required',
            'telefone' => 'required',
            'email' => 'nullable|email',
            'endereco' => 'required',

        ]);

        try {


            if ($fornecedor->update($data)) {
                $json['success'] = true;
                $json['message'] = 'Fornecedor ' . $fornecedor->nome . ' actualizado com sucesso.';
                $json['code'] = 200;

                $descricao = "Actualizou o fornecedor " . $fornecedor->nome . "";
                $historico->insert($fornecedor->getTable(), $fornecedor->id, $descricao);
            } else {
                $json['success'] = false;
                $json['message'] = 'Ocorreu um erro ao editar o fornecedor.';
                $json['code'] = 500;
            }
        } catch (\Illuminate\Validation\ValidationException $e) {

            $errors = $e->validator->errors()->all();

            $json['success'] = false;
            $json['message'] = $errors;
            $json['code'] = 422;
        }

        echo json_encode($json);
    }
}
