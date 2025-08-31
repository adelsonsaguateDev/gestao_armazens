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
                            <input type="text" name="numero_factura" class="form-control" id="numero_factura" placeholder="Número da Factura">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="data_aquisicao"><b>Data de Aquisição</b><span class="obrigatorio">*</span></label>
                            <input type="date" name="data_aquisicao" class="form-control" id="data_aquisicao" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="data_factura"><b>Data da Factura</b><span class="obrigatorio">*</span></label>
                            <input type="date" name="data_factura" class="form-control" id="data_factura" required>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="ficheiro_entrada"><b>Ficheiro da Entrada</b></label>
                            <input type="file" name="ficheiro_entrada" class="form-control-file" id="ficheiro_entrada">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">Adicionar Item</div>
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
                <label for="item_qtd"><b>Quantidade</b><span class="obrigatorio">*</span></label>
                <input type="number" class="form-control" id="item_qtd" required>
            </div>
            <div class="col-md-2 form-group">
                <label for="item_tem_iva"><b>IVA?</b></label>
                <select class="form-control" id="item_tem_iva">
                    <option value="nao">Não</option>
                    <option value="sim">Sim</option>
                </select>
            </div>
            <div class="col-md-2 form-group">
                <label for="item_preco_por_caixa"><b>Preço por Caixa?</b></label>
                <select class="form-control" id="item_preco_por_caixa">
                    <option value="nao">Não</option>
                    <option value="sim">Sim</option>
                </select>
            </div>
            <div class="col-md-2 form-group" id="item_qtd_por_caixa_div" style="display: none;">
                <label for="item_qtd_por_caixa"><b>Qtd por Caixa</b></label>
                <input type="number" class="form-control" id="item_qtd_por_caixa">
            </div>
            <div class="col-md-2 form-group" id="item_preco_compra_caixa_div" style="display: none;">
                <label for="item_preco_compra_caixa"><b>Preço Compra Caixa</b></label>
                <input type="number" class="form-control" id="item_preco_compra_caixa" step="0.01">
            </div>
            <div class="col-md-2 form-group" id="item_preco_venda_caixa_div" style="display: none;">
                <label for="item_preco_venda_caixa"><b>Preço Venda Caixa</b></label>
                <input type="number" class="form-control" id="item_preco_venda_caixa" step="0.01">
            </div>
            <div class="col-md-2 form-group">
                <label for="item_preco_compra"><b>Preço Compra</b><span class="obrigatorio">*</span></label>
                <input type="number" class="form-control" id="item_preco_compra" step="0.01" required>
            </div>
            <div class="col-md-2 form-group">
                <label for="item_preco_venda"><b>Preço Venda</b><span class="obrigatorio">*</span></label>
                <input type="number" class="form-control" id="item_preco_venda" step="0.01" required>
            </div>
            <div class="col-md-2 form-group">
                <label for="item_data_validade"><b>Data de Validade</b></label>
                <input type="date" class="form-control" id="item_data_validade">
            </div>
        </div>
        <button type="button" class="btn btn-primary" id="add_item_to_cart">Adicionar</button>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">Itens da Entrada</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="itens_entrada_table">
                <thead>
                    <tr>
                        <th>Ord</th>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Preço de compra</th>
                        <th>Total compra</th>
                        <th>Preço de venda</th>
                        <th>Total Venda</th>
                        <th>IVA(%)</th>
                        <th>Data de validade</th>
                        <th>Acções</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Linhas de item serão adicionadas aqui via JS -->
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" style="text-align: right;">Total Compra:</th>
                        <th id="total_compra_sum">0.00</th>
                        <th style="text-align: right;">Total Venda:</th>
                        <th id="total_venda_sum">0.00</th>
                        <th style="text-align: right;">Total IVA:</th>
                        <th id="total_iva_sum">0.00</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>