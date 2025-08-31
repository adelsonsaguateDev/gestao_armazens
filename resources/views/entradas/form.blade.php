<div><a style="color: red;text-align:center;">Nota: O ASTERISCO(*) indica que o campo é obrigatório.</a> </div>

<div class="card">
    <div class="mt-3">
        <div class="col-md text-left">
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="tipo_entrada_id"><b>Tipo de Entrada</b><span class="obrigatorio">*</span></label>
                            <select class="form-control select2" name="tipo_entrada_id" id="tipo_entrada_id">
                                <option value="">Selecione...</option>
                                @foreach ($tipos_entrada as $item)
                                    <option value="{{ $item->id }}">{{ $item->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="fornecedor_id"><b>Fornecedor</b></label>
                            <select class="form-control select2" name="fornecedor_id" id="fornecedor_id">
                                <option value="">Selecione...</option>
                                @foreach ($fornecedores as $item)
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
                            <label for="data_aquisicao"><b>Data de Aquisição</b><span
                                    class="obrigatorio">*</span></label>
                            <input type="date" name="data_aquisicao" class="form-control" id="data_aquisicao"
                                required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="data_factura"><b>Data da Factura</b><span class="obrigatorio">*</span></label>
                            <input type="date" name="data_factura" class="form-control" id="data_factura" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="total_factura"><b>Total Factura</b><span class="obrigatorio">*</span></label>
                            <input type="number" name="total_factura" class="form-control" id="total_factura"
                                step="0.01" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="total_desconto"><b>Total Desconto</b></label>
                            <input type="number" name="total_desconto" class="form-control" id="total_desconto"
                                step="0.01">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="total_iva"><b>Total IVA</b></label>
                            <input type="number" name="total_iva" class="form-control" id="total_iva" step="0.01">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="valor_remanescente"><b>Valor Remanescente</b></label>
                            <input type="number" name="valor_remanescente" class="form-control" id="valor_remanescente"
                                step="0.01">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="ficheiro_entrada"><b>Ficheiro da Entrada</b></label>
                            <input type="file" name="ficheiro_entrada" class="form-control-file"
                                id="ficheiro_entrada">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">Itens da Entrada</div>
    <div class="card-body">
        <button type="button" class="btn btn-primary mb-3" id="add_item_entrada">Adicionar Item</button>
        <div class="table-responsive">
            <table class="table table-bordered" id="itens_entrada_table">
                <thead>
                    <tr>
                        <th>Produto<span class="obrigatorio">*</span></th>
                        <th>Qtd. Caixas<span class="obrigatorio">*</span></th>
                        <th>Qtd. por Caixa<span class="obrigatorio">*</span></th>
                        <th>Preço Compra Caixa<span class="obrigatorio">*</span></th>
                        <th>Preço Compra Unitário<span class="obrigatorio">*</span></th>
                        <th>Preço Venda Caixa<span class="obrigatorio">*</span></th>
                        <th>Preço Venda Unitário<span class="obrigatorio">*</span></th>
                        <th>IVA (%)</th>
                        <th>Data Validade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Linhas de item serão adicionadas aqui via JS -->
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="7" style="text-align: right;">Total:</th>
                        <th id="total_iva_sum">0.00</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
