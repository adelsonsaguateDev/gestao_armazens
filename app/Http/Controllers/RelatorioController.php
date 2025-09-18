<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Entrada;
use App\Models\Saida;
use App\Models\Cliente;
use App\Models\Produto;
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

        // Low Stock Products
        $lowStockProducts = Produto::withSum('itensEntrada', 'quantidade_disponivel')
            ->where('estado', 1)
            ->get()
            ->filter(function ($produto) {
                $stockAtual = $produto->itens_entrada_sum_quantidade_disponivel ?? 0;
                return $stockAtual <= $produto->stock_minimo;
            });


        return view('relatorios.index', compact(
            'totalEntradas',
            'totalSaidas',
            'totalClientes',
            'salesLabels',
            'salesValues',
            'lowStockProducts'
        ));
    }
}
