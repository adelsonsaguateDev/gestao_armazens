<!-- Modal -->
<div class="modal fade" id="confirmarSaidaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">Pagamento</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">

          <div class="row">
              <div class="form-group col col-md-4">
                  <label for="">Custo</label>
                  <input readonly type="text" name="modal_custo" value="0.00" id="modal_custo" class="form-control">
              </div>
              <div class="form-group col col-md-4">
                  <label for="">Taxa</label>
                  <input readonly type="text" name="modal_total_taxa" value="0.00" id="modal_total_taxa" class="form-control">
              </div>
              <div class="form-group col col-md-4">
                  <label>Desconto</label>
                  <input type="text" name="modal_total_desconto" value="0.00" id="modal_total_desconto" class="form-control">
              </div>

          </div>
          <div class="row">
            <div class="form-group col col-md-12">
              <label for="" style="text-align:center;">Total</label>
                <input readonly type="text" value="0.00" name="modal_custo_total" id="modal_custo_total" class="text-center form-control">
            </div>
          </div>
          <div class="row">
            <div class="col col-md-12">
              <label for="">Método de pagamento</label>
              <select name="modal_forma_pagamento" id="modal_forma_pagamento" class="form-control ">
                {{-- Assuming $pagamentos is passed from controller --}}
                @if(isset($pagamentos))
                    @foreach ($pagamentos as $item)
                    <option value="{{$item->id}}">{{$item->designacao}}</option>
                    @endforeach
                @endif
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="closeModal" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
          <button type="button" id="registar_venda_modal" class="registar_venda btn btn-primary">Registar</button>
        </div>
      </div>
    </div>
  </div>