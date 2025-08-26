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
        // Card stats
        $total_produtos = Produto::where('estado', 1)->count();
        $total_funcionarios = User::where('estado', 1)->count();
        $total_requisicoes = Requisicoes::where('estado', 1)->count();

        // Requisition status: 1 = Pendente
        $requisicoes_pendentes = Requisicoes::where('estado_requisicao', 1)->count();

        // --- Low Stock Calculation ---

        // Subquery for total entradas (in units)
        $entradas = DB::table('entradas_itens')
            ->select('produto_id', DB::raw('SUM(qtd_caixas * qtd_por_caixa) as total_entradas'))
            ->where('estado', 1)
            ->groupBy('produto_id');

        // Subquery for total saidas (in units)
        $saidas = DB::table('saidas_itens as si')
            ->join('entradas_itens as ei', 'si.entrada_item_id', '=', 'ei.id')
            ->select('si.produto_id', DB::raw('SUM((si.qtd_caixas * ei.qtd_por_caixa) + si.qtd_unidades) as total_saidas'))
            ->where('si.estado', 1)
            ->groupBy('si.produto_id');

        // Main query to get products with their current stock
        $produtos_com_stock = DB::table('produtos as p')
            ->where('p.estado', 1)
            ->leftJoinSub($entradas, 'entradas', function ($join) {
                $join->on('p.id', '=', 'entradas.produto_id');
            })
            ->leftJoinSub($saidas, 'saidas', function ($join) {
                $join->on('p.id', '=', 'saidas.produto_id');
            })
            ->select(
                'p.id',
                'p.stock_minimo',
                DB::raw('COALESCE(entradas.total_entradas, 0) - COALESCE(saidas.total_saidas, 0) as quantidade')
            );

        // Filter for low stock products
        $low_stock_products_data = DB::table(DB::raw("({$produtos_com_stock->toSql()}) as p"))
            ->mergeBindings($produtos_com_stock)
            ->whereRaw('p.quantidade < p.stock_minimo')
            ->get();

        $produtos_stock_baixo = $low_stock_products_data->count();

        // Get the full models for the top 5 low stock products
        $low_stock_product_ids = $low_stock_products_data->pluck('id');

        // Create a map of product_id => quantidade
        $quantidade_map = $low_stock_products_data->keyBy('id');

        // Fetch models and inject the calculated quantity
        $lista_produtos_stock_baixo = Produto::whereIn('id', $low_stock_product_ids)
            ->get()
            ->map(function ($produto) use ($quantidade_map) {
                if (isset($quantidade_map[$produto->id])) {
                    $produto->quantidade = $quantidade_map[$produto->id]->quantidade;
                }
                return $produto;
            })
            ->sortBy('quantidade')
            ->take(5);


        // --- Recent Activity Feed ---
        $recent_products = Produto::latest()->limit(3)->get();
        $recent_requisitions = Requisicoes::with('users', 'produtos')->latest()->limit(3)->get();
        $atividades_recentes = collect();

        foreach ($recent_products as $produto) {
            $atividades_recentes->push((object)[
                'icon' => 'i-Library',
                'descricao' => 'Novo produto: ' . $produto->descricao,
                'created_at' => $produto->created_at
            ]);
        }

        foreach ($recent_requisitions as $requisicao) {
            $atividades_recentes->push((object)[
                'icon' => 'i-Remove-Cart',
                'descricao' => 'Requisição para ' . ($requisicao->produtos->descricao ?? 'N/A'),
                'created_at' => $requisicao->created_at
            ]);
        }

        $atividades_recentes = $atividades_recentes->sortByDesc('created_at');

        return view('home', compact(
            'total_produtos',
            'total_funcionarios',
            'total_requisicoes',
            'produtos_stock_baixo',
            'requisicoes_pendentes',
            'lista_produtos_stock_baixo',
            'atividades_recentes'
        ));
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
