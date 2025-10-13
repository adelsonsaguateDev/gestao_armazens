<!-- Modal de Pagamento -->
<div class="modal fade" id="pagamentoModal" tabindex="-1" role="dialog" aria-labelledby="pagamentoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pagamentoModalLabel">Registar Pagamento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form_pagamento">
                    @csrf
                    <input type="hidden" id="pagamento_saida_id" name="saida_id">

                    <div class="form-group">
                        <label>Valor Total da Venda</label>
                        <input type="text" id="pagamento_valor_total" class="form-control" disabled>
                    </div>

                    <div class="form-group">
                        <label>Valor Remanescente</label>
                        <input type="text" id="pagamento_valor_remanescente_display" class="form-control" disabled>
                    </div>

                    <div class="form-group">
                        <label for="pagamento_valor_a_pagar">Valor a Pagar</label>
                        <input type="number" id="pagamento_valor_a_pagar" name="valor_a_pagar" class="form-control" required step="0.01">
                    </div>

                    <div class="form-group">
                        <label for="pagamento_tipo_pagamento_id">Método de Pagamento</label>
                        <select id="pagamento_tipo_pagamento_id" name="tipo_pagamento_id" class="form-control" required>
                            <option value="">Selecione...</option>
                            @foreach ($tipos_pagamento as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->designacao }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Novo Valor Remanescente</label>
                        <input type="text" id="pagamento_novo_remanescente" class="form-control" disabled>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="btn_salvar_pagamento">Salvar Pagamento</button>
            </div>
        </div>
    </div>
</div>
