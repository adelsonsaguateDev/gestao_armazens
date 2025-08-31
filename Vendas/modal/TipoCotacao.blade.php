<!-- Modal -->
<div class="modal fade" id="confirmarCotacao" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalCenterTitle">Descrição da venda</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <div class="modal-body">
            
                <div class="row">
                  <div class="col-md-12 d-flex justify-content-center">
                    <div class="col-md-12">
                        {{-- <label for="exampleInputEmail1">Motivo <span class="obrigatorio">*</span></label> --}}
                        <textarea name="descricao_rascunho" class="form-control" id="descricao_rascunho" cols="100" rows="4" minlength="5"></textarea>
                    </div>
                      {{-- <div class="custom-control custom-radio custom-control-inline">
                          <input type="radio" id="tipo_cotacao1" name="tipo_cotacao" value="1" class="tipo_cotacao custom-control-input">
                          <label class="custom-control-label" for="tipo_cotacao1"> <span style="font-size: 0.8rem">Cotação Normal</span></label>
                      </div>
                      <div class="custom-control custom-radio custom-control-inline">
                          <input type="radio" id="tipo_cotacao2" name="tipo_cotacao" value="2" class="tipo_cotacao custom-control-input">
                          <label style="font-size: 0.5rem" class="custom-control-label" for="tipo_cotacao2"><span style="font-size: 0.8rem">Rascunho</span></label>
                      </div> --}}
                  </div>
              </div>
              
          </div>
          <div class="modal-footer">
              <button type="button" id="closeModal" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
              <button type="button" id="registar_cotacao" class="btn btn-primary">Registar</button>
          </div>
      </div>
  </div>
</div>

