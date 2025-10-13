
<div class="modal fade" id="edit_produto" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle-2" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="exampleModalCenterTitle-2">Editar produto</h5>
              <button class="close" type="button" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
          </div>
          <div class="modal-body">

          <form action="#"  id="form_editar_produto" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" id="id">
              <div class="row"> 
                
                <div class="col-md-6 form-group">
                    <label for="nome_update"><b>Nome</b><span class="obrigatorio">*</span></label>
                    <input name="nome_update" id="nome_update" class="form-control" value="" required value="" aria-describedby="" placeholder="Nome"/>
                </div>
                      
                <div class="col-md-6 form-group">
                    <label for="descricao_update"><b>Descrição</b><span class="obrigatorio">*</span></label>
                    <textarea name="descricao_update" id="descricao_update" class="form-control" cols="10" rows="" required value="" aria-describedby="" placeholder="Descrição"></textarea>
                </div>

                <div class="col-md-6 form-group">
                    <label for="quantidade_update"><b>Quantidade<span class="obrigatorio">*</span></b></label>
                    <input type="number" name="quantidade_update" class="form-control" id="quantidade_update" value="" aria-describedby=""  required placeholder="Qunatidade" >
                </div>

                <div class="col-md-6 form-group">
                    <label for="stock_minimo_update"><b>Stock mínimo<span class="obrigatorio">*</span></b></label>
                    <input type="number" name="stock_minimo_update" class="form-control" id="stock_minimo_update" value="" aria-describedby=""  required placeholder="Stock minimo" >
                </div>

                <div class="col-md-6 form-group">
                    <label for="unidade_id_update"><b>Unidade</b><span class="obrigatorio">*</span></label>
                    <select class="form-control select2" name="unidade_id_update" id="unidade_id_update" required>
                        <option value="">Selecione...</option>
                        @if(isset($unidades))
                            @foreach ($unidades as $unidade)
                                <option value="{{ $unidade->id }}">{{ $unidade->nome }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-md-12 form-group">
                    <label for="imagem_update"><b>Imagem do Produto</b></label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="imagem_update" name="imagem_update" accept="image/*">
                        <label class="custom-file-label" for="imagem_update">Escolher nova imagem...</label>
                    </div>
                    <small class="form-text text-muted">Formatos aceites: JPEG, PNG, JPG, GIF, SVG. Tamanho máximo: 2MB</small>
                    <div id="imagem_preview" class="mt-2" style="display: none;">
                        <img id="preview_img" src="" alt="Preview" style="max-width: 150px; max-height: 150px; border-radius: 5px;">
                    </div>
                </div>
                      
             </div>

          @csrf
          </form>

             
          </div>
          <div class="modal-footer">
              <button class="btn btn-danger" type="button" data-dismiss="modal">Fechar</button>
              <button class="btn btn-success ml-2" id="editar_produto" type="button">Actualizar</button>
          </div>
      </div>
  </div>
</div>