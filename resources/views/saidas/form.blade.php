<div><a style="color: red;text-align:center;">Nota: O ASTERISCO(*) indica que o campo é obrigatório.</a> </div>

<div class="card">
    <div class="mt-3">
        <div class="col-md text-left">
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="tipo_saida_id"><b>Tipo de Saída</b><span class="obrigatorio">*</span></label>
                            <select class="form-control select2" name="tipo_saida_id" id="tipo_saida_id">
                                <option value="">Selecione...</option>
                                @foreach ($tipos_saida as $item)
                                    <option value="{{ $item->id }}">{{ $item->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="cliente_id"><b>Cliente</b></label>
                            <select class="form-control select2" name="cliente_id" id="cliente_id">
                                <option value="">Selecione...</option>
                                @foreach ($clientes as $item)
                                    <option value="{{ $item->id }}">{{ $item->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="numero_factura"><b>Nº da Factura</b></label>
                            <input type="text" name="numero_factura" class="form-control" id="numero_factura"
                                placeholder="Número da Factura">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="data"><b>Data da Saída</b><span
                                    class="obrigatorio">*</span></label>
                            <input type="date" name="data" class="form-control" id="data"
                                required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="valor_total"><b>Valor Total</b><span class="obrigatorio">*</span></label>
                            <input type="number" name="valor_total" class="form-control" id="valor_total"
                                step="0.01" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="valor_total_iva"><b>Valor Total IVA</b></label>
                            <input type="number" name="valor_total_iva" class="form-control" id="valor_total_iva" step="0.01">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="valor_pago"><b>Valor Pago</b></label>
                            <input type="number" name="valor_pago" class="form-control" id="valor_pago" step="0.01">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="valor_remanescente"><b>Valor Remanescente</b></label>
                            <input type="number" name="valor_remanescente" class="form-control" id="valor_remanescente"
                                step="0.01">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="desconto"><b>Desconto</b></label>
                            <input type="number" name="desconto" class="form-control" id="desconto" step="0.01">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="valor_entregue"><b>Valor Entregue</b></label>
                            <input type="number" name="valor_entregue" class="form-control" id="valor_entregue" step="0.01">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="trocos"><b>Trocos</b></label>
                            <input type="number" name="trocos" class="form-control" id="trocos" step="0.01">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="tipo_pagamento_id"><b>Tipo de Pagamento</b></label>
                            <input type="number" name="tipo_pagamento_id" class="form-control" id="tipo_pagamento_id">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="numero"><b>Número</b></label>
                            <input type="number" name="numero" class="form-control" id="numero">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="numero_cotacao"><b>Número Cotação</b></label>
                            <input type="text" name="numero_cotacao" class="form-control" id="numero_cotacao">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="validade_cotacao"><b>Validade Cotação</b></label>
                            <input type="date" name="validade_cotacao" class="form-control" id="validade_cotacao">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="slip"><b>Slip</b></label>
                            <input type="text" name="slip" class="form-control" id="slip">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="estado_pagamento"><b>Estado Pagamento</b></label>
                            <select name="estado_pagamento" id="estado_pagamento" class="form-control">
                                <option value="pago">Pago</option>
                                <option value="nao_pago">Não Pago</option>
                                <option value="parcial">Parcial</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="activo"><b>Ativo</b></label>
                            <select name="activo" id="activo" class="form-control">
                                <option value="1">Ativo</option>
                                <option value="0">Eliminado</option>
                                <option value="2">...</option>
                                <option value="3">Devolvida</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header"><b>Adicionar Produtos</b></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 form-group">
                <label for="item_produto_id"><b>Produto</b><span class="obrigatorio">*</span></label>
                <select class="form-control select2" id="item_produto_id">
                    <option value="">Selecione...</option>
                    @foreach ($produtos as $produto)
                        <option value="{{ $produto->id }}">{{ $produto->descricao }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="item_quantidade"><b>Quantidade</b><span class="obrigatorio">*</span></label>
                <input type="number" class="form-control" id="item_quantidade" required>
            </div>
            <div class="col-md-2 form-group">
                <label for="item_preco_unitario"><b>Preço Unitário</b><span class="obrigatorio">*</span></label>
                <input type="number" class="form-control" id="item_preco_unitario" step="0.01" required>
            </div>
            <div class="col-md-2 form-group">
                <label for="item_preco_compra"><b>Preço Compra</b><span class="obrigatorio">*</span></label>
                <input type="number" class="form-control" id="item_preco_compra" step="0.01" required>
            </div>
            <div class="col-md-2 form-group">
                <label for="item_iva"><b>IVA</b></label>
                <input type="number" class="form-control" id="item_iva" step="0.01">
            </div>
            <div class="col-md-2 form-group">
                <label for="item_valor_iva"><b>Valor IVA</b></label>
                <input type="number" class="form-control" id="item_valor_iva" step="0.01">
            </div>
            <div class="col-md-2 form-group">
                <label for="item_custo"><b>Custo</b></label>
                <input type="number" class="form-control" id="item_custo" step="0.01">
            </div>
            <div class="col-md-2 form-group">
                <label for="item_desconto_percentual"><b>Desconto (%)</b></label>
                <input type="number" class="form-control" id="item_desconto_percentual" step="0.01">
            </div>
            <div class="col-md-2 form-group">
                <label for="item_desconto_valor"><b>Desconto (Valor)</b></label>
                <input type="number" class="form-control" id="item_desconto_valor" step="0.01">
            </div>
            <div class="col-md-2 form-group">
                <label for="item_tipo_motivo"><b>Tipo Motivo</b></label>
                <input type="number" class="form-control" id="item_tipo_motivo">
            </div>
            <div class="col-md-2 form-group">
                <label for="item_motivo"><b>Motivo</b></label>
                <input type="text" class="form-control" id="item_motivo">
            </div>
        </div>
        <div class="text-right">
            <button type="button" class="btn btn-primary btn-lg" id="add_item_to_cart"> <i
                    class="fas fa-cart-plus"></i> </button>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header"><b>Produtos Adicionados</b></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="itens_saida_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Preço Unitário</th>
                        <th>Preço Compra</th>
                        <th>IVA</th>
                        <th>Valor IVA</th>
                        <th>Custo</th>
                        <th>Desconto (%)</th>
                        <th>Desconto (Valor)</th>
                        <th>Tipo Motivo</th>
                        <th>Motivo</th>
                        <th>Acções</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Linhas de item serão adicionadas aqui via JS -->
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" style="text-align: right;">Total:</th>
                        <th id="total_venda_sum">0.00</th>
                        <th id="total_compra_sum">0.00</th>
                        <th id="total_iva_sum">0.00</th>
                        <th id="total_valor_iva_sum">0.00</th>
                        <th id="total_custo_sum">0.00</th>
                        <th id="total_desconto_percentual_sum">0.00</th>
                        <th id="total_desconto_valor_sum">0.00</th>
                        <th colspan="2"></th>
                    </tr>
                    <tr>
                        <th colspan="3" style="text-align: right;">Total Geral (Venda + IVA):</th>
                        <th id="total_geral_sum">0.00</th>
                        <th colspan="8"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
