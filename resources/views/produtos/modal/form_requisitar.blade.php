
<div class="modal fade" id="req_produto" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle-2" aria-hidden="true">
  <div class="modal-dialog modal-xs" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalCenterTitle-2">Requisiçãoo do Produto</h5>
              <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          </div>
          <div class="modal-body">

          <form action="#"  id="form_requisitar_produto" method="post" enctype="multipart/form-data">
            <input type="hidden" name="produto_req" id="produto_req">
              <div class="row"> 
                      
                <div class="col-md-12 form-group">
                    <label for="descricao_req"><b>Descrição</b><span class="obrigatorio">*</span></label>
                    <textarea name="descricao_req" id="descricao_req" class="form-control" cols="10" rows="" required readonly  placeholder="Descrição"></textarea>
                </div>

                <div class="col-md-12 form-group">
                    <label for="quantidade_req"><b>Quantidade<span class="obrigatorio">*</span></b></label>
                    <input type="number" name="quantidade_req" class="form-control" id="quantidade_req" value="" aria-describedby=""  required placeholder="Qunatidade" >
                </div>                      
                      
             </div>

          @csrf
          </form>

             
          </div>
          <div class="modal-footer">
              <button class="btn btn-danger" type="button" data-dismiss="modal">Fechar</button>
              <button class="btn btn-success ml-2" id="registrar_req_produto" type="button">Submeter</button>
          </div>
      </div>
  </div>
</div>