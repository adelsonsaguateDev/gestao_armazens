<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrada;
use App\Models\Saida;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\SaidaItem;
use App\Models\EntradaItem;
use Illuminate\Support\Facades\DB;

class RelatorioController extends Controller
{
    public function index()
    {
        // Card Data
        $totalEntradas = Entrada::where('estado', 1)->sum('total_factura');
        $totalSaidas = Saida::where('activo', 1)->sum('valor_total');
        $totalClientes = Cliente::where('estado', 1)->count();

        // Sales Chart Data
        $salesDataQuery = Saida::select(
            DB::raw('sum(valor_total) as total'),
            DB::raw('MONTH(created_at) as month')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $salesLabels = [];
        $salesValues = [];
        $months = array_fill(1, 12, 0);

        foreach ($salesDataQuery as $data) {
            $months[$data->month] = $data->total;
        }

        for ($i = 1; $i <= 12; $i++) {
            $salesLabels[] = date('F', mktime(0, 0, 0, $i, 1));
            $salesValues[] = $months[$i];
        }

        // 1. Relatório de Entradas e Saídas (Gráfico de Pizza)
        $entradasSaidasData = [
            'labels' => ['Entradas', 'Saídas'],
            'values' => [$totalEntradas, $totalSaidas]
        ];

        // 2. Relatório de Vendas a Dinheiro vs Crédito (Gráfico de Pizza)
        // tipo_saida_id = 1: Venda Normal (a dinheiro)
        // tipo_saida_id = 2: Venda a Crédito
        $vendasDinheiro = Saida::where('activo', 1)
            ->where('tipo_saida_id', 1)
            ->sum('valor_total');

        $vendasCredito = Saida::where('activo', 1)
            ->where('tipo_saida_id', 2)
            ->sum('valor_total');

        $vendasDinheiroVsCredito = [
            'labels' => ['Vendas a Dinheiro', 'Vendas a Crédito'],
            'values' => [$vendasDinheiro, $vendasCredito]
        ];

        // 3. Relatório de Vendas por Forma de Pagamento
        $vendasPorFormaPagamento = Saida::select('tipo_pagamento_id', DB::raw('sum(valor_total) as total'))
            ->where('activo', 1)
            ->whereNotNull('tipo_pagamento_id')
            ->groupBy('tipo_pagamento_id')
            ->with('tipoPagamento')
            ->get();

        $formasPagamentoLabels = [];
        $formasPagamentoValues = [];

        foreach ($vendasPorFormaPagamento as $venda) {
            $formasPagamentoLabels[] = $venda->tipoPagamento ? $venda->tipoPagamento->designacao : 'Não Definido';
            $formasPagamentoValues[] = $venda->total;
        }

        $vendasPorFormaPagamentoData = [
            'labels' => $formasPagamentoLabels,
            'values' => $formasPagamentoValues
        ];

        // 4. Top 10 Produtos Mais Vendidos
        $top10MaisVendidos = SaidaItem::select('produto_id', DB::raw('sum(quantidade) as total_vendido'))
            ->where('activo', 1)
            ->groupBy('produto_id')
            ->orderBy('total_vendido', 'desc')
            ->limit(10)
            ->with('produto')
            ->get();

        // 5. Top 10 Produtos Menos Vendidos (que tiveram pelo menos uma venda)
        $top10MenosVendidos = SaidaItem::select('produto_id', DB::raw('sum(quantidade) as total_vendido'))
            ->where('activo', 1)
            ->groupBy('produto_id')
            ->orderBy('total_vendido', 'asc')
            ->limit(10)
            ->with('produto')
            ->get();

        // 6. Produtos com Mais Entradas
        $produtosMaisEntradas = EntradaItem::select('produto_id', DB::raw('sum(qtd_caixas * qtd_por_caixa) as total_entrada'))
            ->where('estado', 1)
            ->groupBy('produto_id')
            ->orderBy('total_entrada', 'desc')
            ->limit(10)
            ->with('produto')
            ->get();

        // 7. Produtos com Menos Entradas (que tiveram pelo menos uma entrada)
        $produtosMenosEntradas = EntradaItem::select('produto_id', DB::raw('sum(qtd_caixas * qtd_por_caixa) as total_entrada'))
            ->where('estado', 1)
            ->groupBy('produto_id')
            ->orderBy('total_entrada', 'asc')
            ->limit(10)
            ->with('produto')
            ->get();

        return view('relatorios.index', compact(
            'totalEntradas',
            'totalSaidas',
            'totalClientes',
            'salesLabels',
            'salesValues',
            'entradasSaidasData',
            'vendasDinheiroVsCredito',
            'vendasPorFormaPagamentoData',
            'top10MaisVendidos',
            'top10MenosVendidos',
            'produtosMaisEntradas',
            'produtosMenosEntradas'
        ));
    }
}
