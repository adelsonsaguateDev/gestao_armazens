<!-- Modal -->
<div class="modal fade" id="confirmaraida" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
                  <input readonly type="text" name="custo" value="{{$total}}" id="usto" class="form-control">
              </div>
              <div class="form-group col col-md-4">
                  <label for="">Taxa</label>
                  <input readonly type="text" name="total_taxa" value="0.00" id="total_taxa" class="form-control">
              </div>
              <div class="form-group col col-md-4">
                  <label>Desconto</label>
                  <input type="text" name="total_desconto" value="0.00" id="total_desconto" class="form-control">
              </div>
             
          </div>
          <div class="row">
            <div class="form-group col col-md-12">
              <label for="" style="text-align:center;">Total</label>
                <input readonly type="text" value="0.00" name="custo_total" id="custo_total" class="text-center form-control">
            </div>
          </div>
          <div class="row">
            <div class="col col-md-12">
              <label for="">Método de pagamento</label>
              <select name="forma_pagamento" id="forma_pagamento" class="form-control ">
                @foreach ($pagamentos as $item)
                <option value="{{$item->id}}">{{$item->designacao}}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="closeModal" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
          <button type="button" id="registar_venda" class="registar_venda btn btn-primary">Registar</button>
        </div>
      </div>
    </div>
  </div> 