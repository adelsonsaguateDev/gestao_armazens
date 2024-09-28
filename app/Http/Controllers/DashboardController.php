<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\Requisicoes;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

date_default_timezone_set('Africa/Maputo');
setlocale(LC_ALL, 'pt', 'pt.utf-8', 'pt.utf-8', 'portuguese');

class DashboardController extends Controller
{


    public function index()
    {
        $total_produtos = Produto::where('estado', '=',1)->count();
        $total_funcionarios = User::where('estado', '=',1)->count();
        $total_requisicoes = Requisicoes::where('estado', '=',1)->count();

        return view('home', compact('total_produtos','total_funcionarios','total_requisicoes'));

    }


    public function index1()
    {
        return view('relatorios.index');
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

        if($request->input('data_inicio') != "" || $request->input('data_fim') != ""){

            if ($request->has('data_inicio') && $request->has('data_fim')) {
                $dataInicio = $request->input('data_inicio');
                $dataFim = $request->input('data_fim');
                $query->whereBetween('created_at', [$dataInicio, $dataFim]);

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

        // Paginação dos dados retornados
        $requisicoes = $query->paginate($itensPorPagina);

        // Adiciona parâmetros de filtro à URL da páginação
        $requisicoes->appends($request->query());


        return view('relatorios.tabela', compact('requisicoes', 'total'));
    }



    public function getGraficoBarras()
{
        $dados = DB::table('requisicoes')
            ->select(
                DB::raw('MONTH(created_at) as mes'),
                DB::raw('SUM(CASE WHEN estado_requisicao = 3 THEN 1 ELSE 0 END) as total_reprovadas'),
                DB::raw('SUM(CASE WHEN estado_requisicao = 2 THEN 1 ELSE 0 END) as total_aprovadas'),
                DB::raw('SUM(CASE WHEN estado_requisicao = 1 THEN 1 ELSE 0 END) as total_pendentes')
            )
            ->groupBy('mes')
            ->get();

        return response()->json($dados);
    }


    public function getGraficoPizza()
{
    $dados = DB::table('requisicoes')
                        ->whereIn('estado_requisicao', [1, 2, 3])
                        ->selectRaw("
                            CASE 
                                WHEN estado_requisicao = 1 THEN 'Pendente'
                                WHEN estado_requisicao = 2 THEN 'Aprovado'
                                WHEN estado_requisicao = 3 THEN 'Reprovado'
                            END as estado_requisicao_nome,
                            COUNT(*) as total
                        ")
    ->groupBy('estado_requisicao_nome')
    ->get();



        return response()->json($dados);
    }


}
