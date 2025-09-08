<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Saida;
use App\Models\SaidaItem;
use App\Models\Produto;
use App\Models\Cliente;
use App\Models\TipoSaida;
use App\Models\Historico;
use App\Models\TipoPagamento;
use Illuminate\Support\Facades\DB;
use App\Models\Config; // Add this line

date_default_timezone_set('Africa/Maputo');
setlocale(LC_ALL, 'pt', 'pt.utf-8', 'pt.utf-8', 'portuguese');

class SaidasController extends Controller
{
    public function index()
    {
        $produtos = Produto::where('estado', 1)->get();
        $clientes = Cliente::where('estado', 1)->get();
        $tipos_saida = TipoSaida::where('estado', 1)->get();
        $tipos_pagamento = TipoPagamento::where('is_active', 1)->get(); // Adicionado
        return view('saidas.index', compact('produtos', 'clientes', 'tipos_saida', 'tipos_pagamento')); // Adicionado
    }

    public function create()
    {
        $produtos = Produto::where('estado', 1)->get();

        $clientes = Cliente::data('estado', 1)->get();
        $tipos_saida = TipoSaida::where('estado', 1)->get();
        $pagamentos = TipoPagamento::all();

        return view('saidas.create', compact('produtos', 'clientes', 'tipos_saida', 'pagamentos'));
    }

    public function createCredito()
    {
        $produtos = Produto::where('estado', 1)->get();
        $clientes = Cliente::where('estado', 1)->get();
        $tipos_saida = TipoSaida::where('estado', 1)->get();
        $pagamentos = TipoPagamento::all();

        return view('saidas.create_credito', compact('produtos', 'clientes', 'tipos_saida', 'pagamentos'));
    }

    public function list(Request $request)
    {
        $query = Saida::with(['tipoSaida', 'cliente', 'user', 'estadoObj']);

        if ($request->filled('estado_pagamento')) {
            $query->where('estado_pagamento', $request->input('estado_pagamento'));
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->input('estado'));
        }

        if ($request->filled('numero_factura')) {
            $query->where('numero_factura', 'like', '%' . $request->input('numero_factura') . '%');
        }

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->input('cliente_id'));
        }

        if ($request->filled('tipo_saida_id')) {
            $query->where('tipo_saida_id', $request->input('tipo_saida_id'));
        }

        if ($request->filled('data_inicio') && $request->filled('data_fim')) {
            $query->whereBetween('data_aquisicao', [$request->input('data_inicio'), $request->input('data_fim')]);
        }

        $total = $query->count();

        $itensPorPagina = $request->input('limite', 10);
        $saidas = $query->orderBy('id', 'desc')->paginate($itensPorPagina);
        $saidas->appends($request->query());

        return view('saidas.tabela', compact('saidas', 'total'));
    }
    
    public function add(Request $request)
    {
        // Se for uma venda a crédito, garantir que o tipo_pagamento_id é nulo antes da validação
        if ($request->input('tipo_saida_id') == 2) { // Assumindo 2 para 'Venda a Crédito'
            $request->merge(['tipo_pagamento_id' => null]);
        }

        DB::beginTransaction();
        try {
            $json['success'] = null;
            $json['code'] = null;
            $json['message'] = null;

            $historico = new Historico();

            // Validate main Saida data
            $dataSaida = $request->validate([
                'tipo_saida_id' => 'required|exists:tipos_saidas,id',
                'cliente_id' => 'nullable|exists:clientes,id',
                'data' => 'nullable|date',
                'valor_total' => 'required|numeric|min:0',
                'valor_total_iva' => 'nullable|numeric|min:0',
                'valor_pago' => 'nullable|numeric|min:0',
                'valor_remanescente' => 'nullable|numeric|min:0',
                'desconto' => 'nullable|numeric|min:0',
                'valor_entregue' => 'nullable|numeric|min:0',
                'trocos' => 'nullable|numeric|min:0',
                'tipo_pagamento_id' => 'nullable|exists:tipo_pagamentos,id',
                'numero' => 'nullable|string|max:255',
                'numero_cotacao' => 'nullable|string|max:50',
                'validade_cotacao' => 'nullable|date',
                'slip' => 'nullable|string|max:255',
                'estado_pagamento' => 'nullable|in:pago,nao_pago,parcial',
                'activo' => 'nullable|in:1,0,2,3',
            ]);

            if (empty($dataSaida['data'])) {
                $dataSaida['data'] = date('Y-m-d');
            }

            // Invoice Numbering & Logic based on Sale Type (tipo_saida_id)
            $ano = date("Y");
            $isCreditSale = ($dataSaida['tipo_saida_id'] == 2);

            if ($isCreditSale) {
                $tipo = 'venda_credito';
                $numeracao = $this->getInvoiceNumber($ano, $tipo) + 1;
                $numero_factura = "VC{$numeracao}/{$ano}";
                
                // Force credit sale rules
                $dataSaida['valor_pago'] = 0;
                $dataSaida['tipo_pagamento_id'] = null;
                $dataSaida['estado_pagamento'] = 'nao_pago';
                $dataSaida['valor_remanescente'] = $dataSaida['valor_total'];
                $dataSaida['valor_entregue'] = 0;
                $dataSaida['trocos'] = 0;

            } else { // Logic for other sale types
                $tipo = 'venda_dinheiro';
                $numeracao = $this->getInvoiceNumber($ano, $tipo) + 1;
                $numero_factura = "VD{$numeracao}/{$ano}";
            }

            $dataSaida['numero_factura'] = $numero_factura;
            $dataSaida['user_id'] = auth()->user()->id;
            $dataSaida['estado'] = 1;
            $dataSaida['data_aquisicao'] = $dataSaida['data'];
            $dataSaida['data_factura'] = $dataSaida['data'];

            $itens = json_decode($request->input('itens'), true);
            if (empty($itens)) {
                throw new \Exception('Nenhum item de saída foi fornecido.');
            }

            // Calculate total from items for validation
            $total_venda_calculated = 0;
            foreach ($itens as $itemData) {
                $total_venda_calculated += ($itemData['quantidade'] * $itemData['preco_unitario']);
            }

            if ($dataSaida['desconto'] > 0) {
                $total_venda_calculated -= $dataSaida['desconto'];
            }

            if ($dataSaida['valor_total_iva'] > 0) {
                $total_venda_calculated += $dataSaida['valor_total_iva'];
            }

            if (!$isCreditSale) {
                if ($dataSaida['valor_pago'] > 0 && $dataSaida['desconto'] > 0) {
                    $dataSaida['valor_pago'] -= $dataSaida['desconto'];
                }
            }

            // Validate valor_total against calculated total
            if ($dataSaida['valor_total'] < $total_venda_calculated) {
                throw new \Exception('O Valor Total da Factura não pode ser menor que o Total de Vendas dos itens.');
            }

            $saida = Saida::create($dataSaida);

            // Update invoice number
            $this->updateInvoiceNumber($ano, $tipo);

            foreach ($itens as $itemData) {
                // Validate SaidaItem data
                $itemDataValidated = validator($itemData, [
                    'produto_id' => 'required|exists:produtos,id',
                    'quantidade' => 'required|numeric|min:0.001',
                    'preco_unitario' => 'required|numeric|min:0',
                    'preco_compra' => 'required|numeric|min:0',
                    'iva' => 'nullable|numeric|min:0',
                    'valor_iva' => 'nullable|numeric|min:0',
                    'custo' => 'nullable|numeric|min:0',
                    'desconto_percentual' => 'nullable|numeric|min:0',
                    'desconto_valor' => 'nullable|numeric|min:0',
                    'tipo_motivo' => 'nullable|numeric',
                    'motivo' => 'nullable|string|max:255',
                    'entrada_item_id' => 'required|exists:entradas_itens,id', // Add this
                ])->validate();

                $itemDataValidated['saida_id'] = $saida->id;
                $itemDataValidated['user_id'] = auth()->user()->id;
                $itemDataValidated['activo'] = 1;

                SaidaItem::create($itemDataValidated);

                // Reduz o stock do lote (entrada_item)
                DB::table('entradas_itens')
                    ->where('id', $itemDataValidated['entrada_item_id'])
                    ->decrement('quantidade_disponivel', $itemDataValidated['quantidade']);
            }

            $descricao = 'Registou a saída Nº ' . $saida->numero_factura . ' com ' . count($itens) . ' itens.';
            $historico->insert($saida->getTable(), $saida->id, $descricao);

            DB::commit();
            $json['success'] = true;
            $json['message'] = 'Saída registada com sucesso.';
            $json['code'] = 200;
            $json['saida_id'] = $saida->id; // Add saida_id to the response
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $errors = $e->validator->errors()->all();
            $json['success'] = false;
            $json['message'] = $errors;
            $json['code'] = 422;
        } catch (\Exception $e) {
            DB::rollBack();
            $json['success'] = false;
            $json['message'] = $e->getMessage();
            $json['code'] = 500;
        }

        echo json_encode($json);
    }

    public function show_details($id)
    {
        $saida = Saida::with(['tipoSaida', 'cliente', 'user', 'estadoObj', 'itens.produto', 'tipoPagamento'])->find($id);

        if (!$saida) {
            return response()->json(['error' => 'Saida não encontrada'], 404);
        }

        $historico = Historico::where('row_id', $id)
            ->where('tabela', 'saidas')->with('users')->get();

        return response()->view('saidas.detalhes', compact('saida', 'historico'));
    }

    public function show($id)
    {
        $saida = Saida::with('itens')->find($id);
        $produtos = Produto::where('estado', 1)->get();
        $clientes = Cliente::where('estado', 1)->get();
        $tipos_saida = TipoSaida::where('estado', 1)->get();

        if (!$saida) {
            return response()->json(['error' => 'Saida não encontrada'], 404);
        }

        return view('saidas.form_edit', compact('saida', 'produtos', 'clientes', 'tipos_saida'));
    }

    public function edit(Request $request)
    {
        DB::beginTransaction();
        try {
            $json['success'] = null;
            $json['code'] = null;
            $json['message'] = null;

            $historico = new Historico();

            $saida = Saida::find($request->input('id'));
            if (!$saida) {
                throw new \Exception('Saida não encontrada para edição.');
            }

            $dataSaida = $request->validate([
                'tipo_saida_id' => 'required|exists:tipos_saidas,id',
                'cliente_id' => 'nullable|exists:clientes,id',
                'numero_factura' => 'nullable|string|max:45',
                'data_aquisicao' => 'required|date',
                'data_factura' => 'required|date',
                'total_factura' => 'required|numeric|min:0',
                'total_desconto' => 'nullable|numeric|min:0',
                'total_iva' => 'nullable|numeric|min:0',
                'valor_remanescente' => 'nullable|numeric|min:0',
                'ficheiro_saida' => 'nullable|string|max:255',
            ]);

            $dataSaida['user_id'] = auth()->user()->id;
            $dataSaida['estado'] = 1;

            $itens = json_decode($request->input('itens'), true);
            if (empty($itens)) {
                throw new \Exception('Nenhum item de saida foi fornecido.');
            }

            $total_venda_calculated = 0;
            foreach ($itens as $itemData) {
                $total_venda_calculated += ($itemData['qtd_caixas'] * $itemData['qtd_por_caixa']) * $itemData['preco_venda_unitario'];
            }

            if ($dataSaida['total_factura'] < $total_venda_calculated) {
                throw new \Exception('O Total da Factura não pode ser menor que o Total de Vendas dos itens.');
            }

            $saida->update($dataSaida);

            SaidaItem::where('saida_id', $saida->id)->delete();

            foreach ($itens as $itemData) {
                $itemData['saida_id'] = $saida->id;
                $itemData['user_id'] = auth()->user()->id;
                $itemData['estado'] = 1;
                SaidaItem::create($itemData);
            }

            $descricao = 'Atualizou a saida Nº ' . $saida->id . '.';
            $historico->insert($saida->getTable(), $saida->id, $descricao);

            DB::commit();
            $json['success'] = true;
            $json['message'] = 'Saida atualizada com sucesso.';
            $json['code'] = 200;
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            $errors = $e->validator->errors()->all();
            $json['success'] = false;
            $json['message'] = $errors;
            $json['code'] = 422;
        } catch (\Exception $e) {
            DB::rollBack();
            $json['success'] = false;
            $json['message'] = $e->getMessage();
            $json['code'] = 500;
        }

        echo json_encode($json);
    }

    public function delete(Request $request)
    {
        $id = $request->input('saida_id');
        $estado = $request->input('estado');
        $json['success'] = false;
        $saida = Saida::find($id);
        $historico = new Historico();

        if (!empty($saida)) {
            $data['estado'] = $estado;
            if ($saida->update($data)) {
                $json['success'] = true;
                if ($estado == '1') {
                    $json['message'] = 'Saida ativada com sucesso.';
                    $descricao = 'Ativou a saida Nº ' . $saida->id . '.';
                } else if ($estado == '2') {
                    $json['message'] = 'Saida removida com sucesso.';
                    $descricao = 'Removeu a saida Nº ' . $saida->id . '.';
                }
                $historico->insert($saida->getTable(), $saida->id, $descricao);
                $json['code'] = 200;
            } else {
                $json['success'] = false;
                $json['message'] = 'Ocorreu um erro ao remover a saida.';
                $json['code'] = 500;
            }
        }
        echo json_encode($json);
    }

    public function recibo($id)
    {
        $saida = Saida::with(['cliente', 'tipoSaida', 'user', 'itens.produto'])->findOrFail($id);

        // Assuming 'Config' model holds company details and 'TipoPagamento' for payment types
        $empresa = Config::first();
        // Fetch only the payment method used in this sale
        $tipoPagamentoUsado = TipoPagamento::find($saida->tipo_pagamento_id);

        // Prepare data for the receipt view, similar to ver_htmlRecibo.php
        $saidaData = [
            'id' => $saida->id,
            'numero_factura' => $saida->numero_factura,
            'data' => $saida->data,
            'created_at' => $saida->created_at,
            'cliente_nome' => $saida->cliente->nome ?? 'N/A',
            'cliente_endereco' => $saida->cliente->endereco ?? '',
            'cliente_nuit' => $saida->cliente->nuit ?? '',
            'cliente_contacto' => $saida->cliente->contacto ?? '',
            'tipo_saida_id' => $saida->tipo_saida_id,
            'valor_pago' => $saida->valor_pago,
            'valor_entregue' => $saida->valor_entregue,
            'trocos' => $saida->trocos,
            'desconto' => $saida->desconto,
            'valor_total' => $saida->valor_total,
            'valor_total_iva' => $saida->valor_total_iva,
            'userName' => $saida->user->name ?? 'N/A',
            'numero_membro' => $saida->numero_membro ?? '',
            'codigo_autorizacao' => $saida->codigo_autorizacao ?? '',
        ];

        $saidaItemsData = [];
        foreach ($saida->itens as $item) {
            $saidaItemsData[] = [
                'produto_descricao' => $item->produto->descricao ?? 'N/A',
                'quantidade' => $item->quantidade,
                'preco_unitario' => $item->preco_unitario,
                'preco_compra' => $item->preco_compra,
                'iva' => $item->iva,
                'valor_iva' => $item->valor_iva,
                'custo' => $item->custo,
                'desconto_percentual' => $item->desconto_percentual,
                'desconto_valor' => $item->desconto_valor,
                'taxa' => $item->iva,
                'activo' => $item->activo,
            ];
        }

        // Start PDF generation
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => [72.1, 350], // Specific size for receipt
            'orientation' => 'P',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 5,
            'margin_bottom' => 5,
            'simpleTables' => true
        ]);

        // Render the Blade view to HTML
        $html = view('saidas.recibo_pdf_content', compact('saidaData', 'saidaItemsData', 'empresa', 'tipoPagamentoUsado'))->render();

        $mpdf->writeHTML($html);

        $outputName = 'Recibo.pdf';
        return response($mpdf->Output($outputName, 'I'))
            ->header('Content-Type', 'application/pdf');
    }

    public function getBatchesByProduct(Request $request)
    {
        $productId = $request->input('product_id');

        // This now uses the 'quantidade_disponivel' column.
        $batches = DB::table('entradas_itens')
            ->where('produto_id', $productId)
            ->where('estado', 1)
            ->where('quantidade_disponivel', '>', 0)
            ->select('id as entrada_item_id', 'preco_venda_unitario as preco_actual', 'quantidade_disponivel as qnt_actual_entrada_item')
            ->orderBy('id')
            ->get();

        return response()->json($batches);
    }

    private function getInvoiceNumber($ano, $tipo)
    {
        $numeracao = DB::table('numeracao')
            ->where('ano', $ano)
            ->where('tipo', $tipo)
            ->first();
        return $numeracao ? $numeracao->numero : 0;
    }

    private function updateInvoiceNumber($ano, $tipo)
    {
        DB::table('numeracao')->updateOrInsert(
            ['ano' => $ano, 'tipo' => $tipo],
            ['numero' => DB::raw('numero + 1')]
        );
    }
}
