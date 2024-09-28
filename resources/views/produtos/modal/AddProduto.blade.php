<!-- Modal -->
<div class="modal fade" id="rg_produto" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Registro do Produto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">

              <form action="" method="POST" id="form_registrar_produto" enctype="multipart/form-data">
                  @csrf
                  @include('produtos.form')
              </form>
          </div>
        </div>

      </div>  {{--end modal body --}}
      <div class="modal-footer">
        <button class="btn btn-danger" type="button" data-dismiss="modal">Fechar</button>
        <button class="btn btn-success ml-2" id="registrar_produto" type="button">Submeter</button>
    </div>
    </div>
  </div>
</div>
