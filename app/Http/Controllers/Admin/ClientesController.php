<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Permissao;
use App\Models\Historico;
use Facade\FlareClient\Http\Client;

date_default_timezone_set('Africa/Maputo');
setlocale(LC_ALL, 'pt', 'pt.utf-8', 'pt.utf-8', 'portuguese');

class ClientesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {

        $tipo_utilizador = Permissao::all();

        return view('clientes.index', compact('tipo_utilizador'));
    }

    public function list(Request $request)
    {

        $query = Cliente::query();


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

        // Recupera os clientes paginados ordenados por ID (mais recentes primeiro)
        $clientes = $query->orderBy('id', 'desc')->paginate($itensPorPagina);

        // Adiciona parâmetros de filtro à URL da páginação
        $clientes->appends($request->query());


        return view('clientes.tabela', compact('clientes', 'total'));
    }


    public function add(Request $request)
    {

        try {

            $json['success'] = null;
            $json['code'] = null;
            $json['message'] = null;

            $historico = new Historico();

            $data = $request->validate([
                'nome' => 'required|unique:clientes',
                'nuit' => 'required|unique:clientes',
                'endereco' => 'nullable:clientes',
                'contacto' => 'required|unique:clientes',

            ]);

            $data['estado'] = 1;
            $data['user_id'] = auth()->user()->id;
            $cliente = Cliente::create($data);
            if ($cliente) {
                $descricao = 'Registou o cliente ' . $cliente->nome . '.';
                $historico->insert($cliente->getTable(), $cliente->id, $descricao);

                $json['success'] = true;
                $json['message'] = 'O cliente' . $cliente->nome . ' foi adicionado com sucesso.';
                $json['code'] = 200;
            } else {
                $json['success'] = false;
                $json['message'] = 'Erro ao adicionar o cliente ' . $cliente->nome;
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

        $cliente = Cliente::find($id);


        $historico = Historico::where('row_id', $id)
            ->where('tabela', 'clientes')->with('users')->get();

        


        if (!$cliente) {
            return response()->json(['error' => 'Cliente não encontrado'], 404);
        }

        return response()->view('clientes.detalhes', compact('cliente', 'historico'));
    }

    public function delete()
    {
        $id = $_POST['cliente_id'];
        $estado = $_POST['estado'];
        $json['success'] = false;
        $cliente = Cliente::find($id);
        $historico = new Historico();

        if (!empty($cliente)) {
            $data = ['estado' => $estado];
            if ($cliente->update($data)) {
                $json['success'] = true;
                if ($estado == '1') {
                    $json['message'] = 'Cliente activado com sucesso.';

                    $descricao = 'Activou o cliente ' . $cliente->nome . '.';
                    $historico->insert($cliente->getTable(), $cliente->id, $descricao);
                } else if ($estado == '2') {
                    $json['message'] = 'Cliente removido com sucesso.';

                    $descricao = 'Removeu o cleinte ' . $cliente->nome . '.';
                    $historico->insert($cliente->getTable(), $cliente->id, $descricao);
                }
                $json['code'] = 200;
            } else {
                $json['success'] = false;
                $json['message'] = 'Ocorreu um erro ao remover o cliente.';
                $json['code'] = 500;
            }
        }
        echo json_encode($json);
    }

    public function show($id)
    {
        $cleinte = Cliente::find($id);
        return view('cleintes.form_update', compact('cliente'));
    }



    public function edit(Request $request)
    {
        $id = $request->id;
        $json['success'] = false;
        $json['message'] = null;
        $json['code'] = null;
        $cliente = Cliente::find($id);
        $historico = new Historico();

        $data = request()->validate([
            'nome' => 'required',
            'nuit' => 'required',
            'endereco' => 'required',
            'contacto' => 'required',

        ]);

        try {


            if ($cliente->update($data)) {
                $json['success'] = true;
                $json['message'] = 'Cliente ' . $cliente->nome . ' actualizado com sucesso.';
                $json['code'] = 200;

                $descricao = "Actualizou o cliente " . $cliente->nome . "";
                $historico->insert($cliente->getTable(), $cliente->id, $descricao);
            } else {
                $json['success'] = false;
                $json['message'] = 'Ocorreu um erro ao editar o cliente.';
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
