<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produto;
use App\Models\Historico;
use App\Models\Requisicoes;
use App\Models\Unidade;
use Illuminate\Support\Facades\DB;

date_default_timezone_set('Africa/Maputo');
setlocale(LC_ALL, 'pt', 'pt.utf-8', 'pt.utf-8', 'portuguese');


class ProdutosController extends Controller
{
    public function index()
    {
        $unidades = Unidade::where('estado', 1)->get();
        return view('produtos.index', compact('unidades'));
    }

    public function list(Request $request)
    {
        // Subquery for total entradas (in units)
        $entradas = DB::table('entradas_itens')
            ->select('produto_id', DB::raw('SUM(quantidade_disponivel) as total_disponivel'))
            ->where('estado', 1)
            ->groupBy('produto_id');      

        $query = DB::table('produtos as p')
            ->leftJoin('unidades as u', 'p.unidade_id', '=', 'u.id') 
            ->leftJoinSub($entradas, 'entradas', function ($join) {
                $join->on('p.id', '=', 'entradas.produto_id');
            })
            ->select(
       'p.id',
                'p.descricao',
                'p.created_at',
                'p.estado',
                'p.nome',
                'p.stock_minimo',
                'u.nome as unidade',
                'p.codigo_barras',
                DB::raw('COALESCE(entradas.total_disponivel, 0) as quantidade')
            );


        if ($request->filled('estado')) {
            $query->where('p.estado', $request->input('estado'));
        }

        if ($request->filled('descricao')) {
            $query->where('p.descricao', 'like', '%' . $request->input('descricao') . '%');
        }

        if ($request->filled('codigo_filtro')) {
            $query->where('p.codigo_barras', 'like', '%' . $request->input('codigo_filtro') . '%');
        }

        if ($request->filled('stock_minimo_filtro')) {
            $query->where('p.stock_minimo', '=', $request->input('stock_minimo_filtro'));
        }

        if ($request->filled('quantidade_filtro')) {
            $query->having('quantidade', '=', $request->input('quantidade_filtro'));
        }

        // The total count should be calculated on the filtered query before pagination.
        $total = $query->count();

        // Define o número de itens por página (você pode ajustar conforme necessário)
        $itensPorPagina = $request->input('limite', 10); // Padrão: 10 itens por página

        // Paginação dos dados retornados
        $produtos = $query->paginate($itensPorPagina);

        // Adiciona parâmetros de filtro à URL da páginação
        $produtos->appends($request->query());

        return view('produtos.tabela', compact('produtos', 'total'));
    }


    public function add(Request $request)
    {

        try {

            $json['success'] = null;
            $json['code'] = null;
            $json['message'] = null;

            $historico = new Historico();

            $data = $request->validate([
                'nome' => 'nullable',
                'descricao' => 'required|unique:produtos',
                'codigo_barras' => 'required|unique:produtos',
                'stock_minimo' => 'required',
                'unidade_id' => 'nullable',
                'imagem' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            // Handle File Upload
            if($request->hasFile('imagem')){
                // Get filename with the extension
                $filenameWithExt = $request->file('imagem')->getClientOriginalName();
                // Get just filename
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                // Get just ext
                $extension = $request->file('imagem')->getClientOriginalExtension();
                // Filename to store
                $fileNameToStore= $filename.'_'.time().'.'.$extension;
                // Upload Image
                $path = $request->file('imagem')->storeAs('public/produtosImg', $fileNameToStore);
                $data['imagem'] = $fileNameToStore;
            }

            $data['estado'] = 1;
            $data['user_id'] = auth()->user()->id;

            // print_r($data);
            // exit();

            $check = DB::selectOne("SELECT codigo_barras FROM produtos WHERE codigo_barras = '{$request->codigo}' ");
            if (empty($check)) {
                if ($produto = Produto::create($data)) {

                    $descricao = 'Registou o produto ' . $produto->descricao . '.';
                    $historico->insert($produto->getTable(), $produto->id, $descricao);

                    $json['success'] = true;
                    $json['message'] = 'O produto ' . $produto->descricao . ' foi adicionado com sucesso.';
                    $json['code'] = 200;
                } else {
                    $json['success'] = false;
                    $json['message'] = 'Erro ao adicionar o produto ' . $produto->descricao;
                    $json['code'] = 500;
                }
            } else {
                $json['success'] = false;
                $json['message'] = 'O codigo de barras do produto já existe.';
                $json['code'] = 409;
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

        $produtos = Produto::find($id);

        $historico = Historico::where('row_id', $id)
            ->where('tabela', 'produtos')->with('users')->get();


        if (!$produtos) {
            return response()->json(['error' => 'Produto não encontrado'], 404);
        }

        return response()->view('produtos.detalhes', compact('produtos', 'historico'));
    }

    public function delete()
    {
        $id = $_POST['produto_id'];
        $estado = $_POST['estado'];
        $json['success'] = false;
        $produto = Produto::find($id);
        $historico = new Historico();

        if (!empty($produto)) {
            $data = ['estado' => $estado];
            if ($produto->update($data)) {
                $json['success'] = true;
                if ($estado == '1') {
                    $json['message'] = 'Produto activado com sucesso.';

                    $descricao = 'Activou o produto ' . $produto->descricao . '.';
                    $historico->insert($produto->getTable(), $produto->id, $descricao);
                } else if ($estado == '2') {
                    $json['message'] = 'Produto removido com sucesso.';

                    $descricao = 'Removeu o produto ' . $produto->descricao . '.';
                    $historico->insert($produto->getTable(), $produto->id, $descricao);
                }
                $json['code'] = 200;
            } else {
                $json['success'] = false;
                $json['message'] = 'Ocorreu um erro ao remover o produto.';
                $json['code'] = 500;
            }
        }
        echo json_encode($json);
    }

    public function show($id)
    {
        $produto = Produto::where('id', $id)->get();
        return view('produtos.form_update', compact('produto'));
    }

    public function edit()
    {
        $id = $_POST['id'];
        $json['success'] = false;
        $json['message'] = null;
        $json['code'] = null;
        $produto = Produto::find($id);
        $historico = new Historico();


        $data = [
            'nome' => (string)$_POST['nome_update'] ?? "",
            'descricao' => (string)$_POST['descricao_update'] ?? "",
            'stock_minimo' => $_POST['stock_minimo_update'] ?? 0,
            'quantidade' => $_POST['quantidade_update'] ?? 0
        ];


        if (!empty($produto)) {
            if ($produto->update($data)) {
                $json['success'] = true;
                $json['message'] = 'Produto ' . $produto->descricao . ' actualizado com sucesso.';
                $json['code'] = 200;

                $descricao = "Actualizou o produto " . $produto->descricao . "";
                $historico->insert($produto->getTable(), $produto->id, $descricao);
            } else {
                $json['success'] = false;
                $json['message'] = 'Ocorreu um erro ao editar o produto.';
                $json['code'] = 500;
            }
        }
        echo json_encode($json);
    }



    public function add_requisicao()
    {

        try {

            $json['success'] = null;
            $json['code'] = null;
            $json['message'] = null;

            $historico = new Historico();


            $data = [
                'produto_id' => $_POST['produto_req'],
                'quantidade' => $_POST['quantidade_req'],
                'user_id' => auth()->user()->id
            ];

            if ($requisicoes = Requisicoes::create($data)) {

                $descricao = 'Registou a requisição ' . $requisicoes->id . '.';
                $historico->insert($requisicoes->getTable(), $requisicoes->id, $descricao);

                $json['success'] = true;
                $json['message'] = 'A requisição foi adicionado com sucesso.';
                $json['code'] = 200;
            } else {
                $json['success'] = false;
                $json['message'] = 'Erro ao fazer a requisição.';
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
