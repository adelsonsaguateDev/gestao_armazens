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

date_default_timezone_set('Africa/Maputo');
setlocale(LC_ALL, 'pt', 'pt.utf-8', 'pt.utf-8', 'portuguese');

class SaidasController extends Controller
{
    public function index()
    {
        $produtos = Produto::where('estado', 1)->get();
        $clientes = Cliente::where('estado', 1)->get();
        $tipos_saida = TipoSaida::where('estado', 1)->get();
        return view('saidas.index', compact('produtos', 'clientes', 'tipos_saida'));
    }

    public function create()
    {
        $produtos = DB::select("SELECT p.id, p.descricao, p.nome, p.codigo_barras,
                                SUM(COALESCE(ei.qtd_caixas * ei.qtd_por_caixa, 0)) - SUM(COALESCE(si.quantidade, 0)) as qnt_actual,
                                MAX(ei.preco_venda_unitario) as preco_actual
                                FROM produtos p
                                LEFT JOIN entradas_itens ei ON p.id = ei.produto_id AND ei.estado = 1
                                LEFT JOIN saida_itens si ON p.id = si.produto_id AND si.activo = 1
                                WHERE p.estado = 1
                                GROUP BY p.id, p.descricao, p.nome, p.codigo_barras
                                HAVING qnt_actual > 0");

        $clientes = Cliente::where('estado', 1)->get();
        $tipos_saida = TipoSaida::where('estado', 1)->get();
        $pagamentos = TipoPagamento::all();

        return view('saidas.create', compact('produtos', 'clientes', 'tipos_saida', 'pagamentos'));
    }

    public function list(Request $request)
    {
        $query = Saida::with(['tipoSaida', 'cliente', 'user', 'estadoObj']);

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
        $saidas = $query->paginate($itensPorPagina);
        $saidas->appends($request->query());

        return view('saidas.tabela', compact('saidas', 'total'));
    }

    public function add(Request $request)
    {
        
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

            // Invoice Numbering
            $ano = date("Y");
            $tipo = 'venda_dinheiro';
            $numeracao = $this->getInvoiceNumber($ano, $tipo) + 1;
            $numero_factura = "VD {$numeracao}/{$ano}";

            $dataSaida['numero_factura'] = $numero_factura;
            $dataSaida['user_id'] = auth()->user()->id;
            $dataSaida['estado'] = 1;
            $dataSaida['data_aquisicao'] = $dataSaida['data'];
            $dataSaida['data_factura'] = $dataSaida['data'];

            // Handle payment details from modal
            $dataSaida['desconto'] = $request->input('modal_total_desconto', 0);
            $dataSaida['tipo_pagamento_id'] = $request->input('modal_forma_pagamento');

            $itens = json_decode($request->input('itens'), true);
            if (empty($itens)) {
                throw new \Exception('Nenhum item de saída foi fornecido.');
            }

            // Calculate total from items for validation
            $total_venda_calculated = 0;
            foreach ($itens as $itemData) {
                $total_venda_calculated += ($itemData['quantidade'] * $itemData['preco_unitario']);
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
                ])->validate();

                $itemDataValidated['saida_id'] = $saida->id;
                $itemDataValidated['user_id'] = auth()->user()->id;
                $itemDataValidated['activo'] = 1;

                SaidaItem::create($itemDataValidated);

                // Stock reduction
                DB::table('produtos')
                    ->where('id', $itemDataValidated['produto_id'])
                    ->decrement('stock_minimo', $itemDataValidated['quantidade']);
            }

            $descricao = 'Registou a saída Nº ' . $saida->numero_factura . ' com ' . count($itens) . ' itens.';
            $historico->insert($saida->getTable(), $saida->id, $descricao);

            DB::commit();
            $json['success'] = true;
            $json['message'] = 'Saída registada com sucesso.';
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

    public function show_details($id)
    {
        $saida = Saida::with(['tipoSaida', 'cliente', 'user', 'estadoObj', 'itens.produto'])->find($id);

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
        return view('saidas.recibo', compact('saida'));
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
