<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pagamento;
use App\Models\Historico;
use App\Models\Saida;
use Illuminate\Support\Facades\DB;


date_default_timezone_set('Africa/Maputo');
setlocale(LC_ALL, 'pt', 'pt.utf-8', 'pt.utf-8', 'portuguese');


class PagamentoController extends Controller
{
    public function store(Request $request)
    {

        DB::beginTransaction();
        try {
            $json['success'] = null;
            $json['code'] = null;
            $json['message'] = null;

            $historico = new Historico();

            $dataPagamento = $request->validate([
                'saida_id' => 'required|exists:saidas,id',
                'valor_a_pagar' => 'required|numeric|min:0.01',
                'tipo_pagamento_id' => 'required|exists:tipo_pagamentos,id',
            ]);

            $ano = date("Y");

            $tipo = 'pagamento';
            $numeracao = $this->getInvoiceNumber($ano, $tipo) + 1;
            $numero = "P{$numeracao}/{$ano}";

            $saida = Saida::findOrFail($request->saida_id);

            $valorAPagar = floatval($request->valor_a_pagar);

            // Validar se o valor a pagar não excede o valor remanescente
            if ($valorAPagar > $saida->valor_remanescente) {
                return response()->json(['errors' => ['valor_a_pagar' => ['O valor a pagar não pode ser maior que o valor remanescente.']]], 422);
            }


            $dataPagamento = [
                'valor_pago' => $valorAPagar,
                'numero_recibo' => $saida->numero_factura,
                'data_pagamento' => date('Y-m-d'),
                'numero' => $numero,
                'tipo_pagamento_id' => $request->tipo_pagamento_id,
                'cliente_id' => $saida->cliente_id,
                'saida_id' => $saida->id,
                'user_id' => auth()->user()->id,
            ];

            $pagamento = Pagamento::create($dataPagamento);

            $this->updateInvoiceNumber($ano, $tipo);

            // Atualizar os valores na saida
            $saida->valor_pago += $valorAPagar;
            $saida->valor_remanescente -= $valorAPagar;

            // Atualizar o estado do pagamento
            if ($saida->valor_remanescente <= 0) {
                $saida->estado_pagamento = 'pago';
                $saida->valor_remanescente = 0;
            } else {
                $saida->estado_pagamento = 'parcial';
            }

            $saida->save();

            $descricao = 'Registou o pagamento Nº ' . $pagamento->numero . ' sobre a saida Nº ' . $pagamento->numero_recibo .' no valor de '.$valorAPagar;
            $historico->insert($pagamento->getTable(), $pagamento->id, $descricao);
            $historico->insert($saida->getTable(), $saida->id, $descricao);


            DB::commit();
            $json['success'] = true;
            $json['message'] = 'Pagamento registado com sucesso.';
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

    // Manter os outros métodos vazios por enquanto
    public function index() {}
    public function create() {}
    public function show($id) {}
    public function edit($id) {}
    public function update(Request $request, $id) {}
    public function destroy($id) {}
}
