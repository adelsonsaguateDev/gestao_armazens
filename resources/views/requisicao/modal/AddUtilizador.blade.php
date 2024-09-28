<!-- Modal -->
<div class="modal fade" id="rg_utilizador" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Registro do Utilizador</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">

            <form action="" method="POST" id="form_registrar_utilizador" enctype="multipart/form-data">
                  @csrf
                  @include('requisicao.form')
            
                <div class="row">
                    <div class="col-md-6">
                        <label for="exampleInputPassword1">Password<span class="obrigatorio">*</label>
                        <div class="input-group">
                          <input type="password" name="password" class="form-control" id="pass" placeholder="Password" >                                
                        </div>
                    </div>
                
                    <div class="col-md-6">
                        <label for="exampleInputPassword1">Confirm Password</label>
                        <div class="input-group">
                          <input type="password" name="password_confirm" class="form-control has-success" id="pass_conf" placeholder="Password" >
                        </div>
                    </div>
                </div> 
      
              @csrf
            </form>
          
          </div>
        </div>

      </div>  {{--end modal body --}}
      <div class="modal-footer">
        <button class="btn btn-danger" type="button" data-dismiss="modal">Fechar</button>
        <button class="btn btn-success ml-2" id="registrar_utilizador" type="button">Submeter</button>
    </div>
    </div>
  </div>
</div>
