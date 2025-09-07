<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pagamento;
use App\Models\Saida;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PagamentoController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'saida_id' => 'required|exists:saidas,id',
            'valor_a_pagar' => 'required|numeric|min:0.01',
            'tipo_pagamento_id' => 'required|exists:tipos_pagamentos,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $saida = Saida::findOrFail($request->saida_id);

            $valorAPagar = floatval($request->valor_a_pagar);

            // Validar se o valor a pagar não excede o valor remanescente
            if ($valorAPagar > $saida->valor_remanescente) {
                return response()->json(['errors' => ['valor_a_pagar' => ['O valor a pagar não pode ser maior que o valor remanescente.']]], 422);
            }

            // Criar o registo de pagamento
            Pagamento::create([
                'saida_id' => $saida->id,
                'valor' => $valorAPagar,
                'tipo_pagamento_id' => $request->tipo_pagamento_id,
                'user_id' => auth()->id(),
            ]);

            // Atualizar os valores na saida
            $saida->valor_pago += $valorAPagar;
            $saida->valor_remanescente -= $valorAPagar;

            // Atualizar o estado do pagamento
            if ($saida->valor_remanescente <= 0) {
                $saida->estado_pagamento = 'pago';
                $saida->valor_remanescente = 0; // Garantir que não fica negativo
            } else {
                $saida->estado_pagamento = 'parcial';
            }

            $saida->save();

            return response()->json([
                'message' => 'Pagamento registado com sucesso!',
                'saida' => $saida
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erro ao registar pagamento: ' . $e->getMessage());
            return response()->json(['message' => 'Ocorreu um erro no servidor.'], 500);
        }
    }

    // Manter os outros métodos vazios por enquanto
    public function index() { }
    public function create() { }
    public function show($id) { }
    public function edit($id) { }
    public function update(Request $request, $id) { }
    public function destroy($id) { }
}