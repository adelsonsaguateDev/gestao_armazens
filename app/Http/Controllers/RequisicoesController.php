<?php

namespace App\Http\Controllers;
use App\Models\Produto;
use App\Models\Requisicoes;


use Illuminate\Http\Request;

date_default_timezone_set('Africa/Maputo');
setlocale(LC_ALL, 'pt', 'pt.utf-8', 'pt.utf-8', 'portuguese');
class RequisicoesController extends Controller
{
    public function index()
    {
        $produtos = Produto::where('estado', 1)->get();
        return view('requisicao.index', compact('produtos'));
    }

    public function list(Request $request)
    {
        $query = Requisicoes::with(['users','produtos']);

        $total = $query->count();

        if($request->input('estado_requisicao') != ""){

            if ($request->has('estado_requisicao')) {
                $estado_requisicao = $request->input('estado_requisicao');
                $query->where('estado_requisicao', $estado_requisicao);


                $total = $query->count();
            }
        }

        if ($request->has('descricao')) {
            $descricao = $request->input('descricao');
            $query->whereHas('produtos', function($q) use ($descricao) {
                $q->where('descricao','like',"%{$descricao}%");
            });

            $total = $query->count();
        }

        // Define o número de itens por página (você pode ajustar conforme necessário)
        $itensPorPagina = $request->input('limite', 10); // Padrão: 10 itens por página

        // Paginação os dados buscados
        $requisicoes = $query->paginate($itensPorPagina);

        // Adiciona parâmetros de filtro à URL da páginação
        $requisicoes->appends($request->query());


        return view('requisicao.tabela', compact('requisicoes', 'total'));
    }


    public function aprovacao()
    {

        $json['success'] = false;
        $json['message'] = null;
        $json['code'] = null;

        $id = $_POST['requisicao_id'];
        $produto_id = (int)$_POST['produto_id'];

        $qnt_final = 0;

        $requisicoes = Requisicoes::find($id);
        $produto = Produto::find($produto_id);


        if ($produto->quantidade > 0) {
            $qnt_final  = (float)$produto->quantidade - (float)$_POST['quantidade'];
            $data = ['estado_requisicao' => '2'];
            $data1 = ['quantidade' => $qnt_final ];

            if (!empty($requisicoes)) {
                if ($requisicoes->update($data)) {
                    $produto->update($data1);
                    $json['success'] = true;
                    $json['message'] = 'Requisição aprovada com sucesso.';
                    $json['code'] = 200;

                }else{
                    $json['success'] = false;
                    $json['message'] = 'Ocorreu um erro ao aprovar a requisição.';
                    $json['code'] = 500;
                }
            }

        }else{
            $json['success'] = false;
            $json['message'] = 'Não existem, quantidades suficientes no armazem.';
            $json['code'] = 500;
        }
        echo json_encode($json);
    }


    public function reprovacao()
    {

        $json['success'] = false;
        $json['message'] = null;
        $json['code'] = null;

        $id = $_POST['requisicao_id'];

        $requisicoes = Requisicoes::find($id);

        $data = ['estado_requisicao' => '3'];


        if (!empty($requisicoes)) {
            if ($requisicoes->update($data)) {

                $json['success'] = true;
                $json['message'] = 'Requisição reprovada com sucesso.';
                $json['code'] = 200;

            }else{
                $json['success'] = false;
                $json['message'] = 'Ocorreu um erro ao reprovar a requisição.';
                $json['code'] = 500;
            }
        }
        echo json_encode($json);
    }



}
