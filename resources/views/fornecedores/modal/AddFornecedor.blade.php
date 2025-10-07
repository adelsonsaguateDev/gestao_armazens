<!-- Modal -->
<div class="modal fade" id="rg_fornecedor" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Registro do Fornecedor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">

                        <form action="" method="POST" id="form_registar_fornecedor" enctype="multipart/form-data">
                            @csrf
                            <div><a style="color: red;text-align:center;">Nota: O ASTERISCO(<span
                                        class="text-danger">*</span>) indica a obrigatoriedade do campo.</a> </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="nome">Nome<span class="obrigatorio">*</label>
                                    <input type="text" name="nome" class="form-control" id="" required
                                        value="" aria-describedby="" placeholder="Digite o nome">
                                </div>
                                <div class="col-md-6">
                                    <label for="telefone">Telefone<span class="obrigatorio">*</label>
                                    <input type="text" maxlength="9" name="telefone" class="form-control numero" id="" required
                                        value="" aria-describedby="" placeholder="Digite o telefone">
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" class="form-control" value=""
                                        id="" aria-describedby="" placeholder="Digite o email">
                                </div>
                                <div class="col-md-6">
                                    <label for="endereco">Endereço</label>
                                    <input type="text" name="endereco" class="form-control"
                                        id="" value="" aria-describedby="" placeholder="Digite o endereço">
                                </div>
                            </div>
                            @csrf
                        </form>

                    </div>
                </div>

            </div> {{-- end modal body --}}
            <div class="modal-footer">
                <button class="btn btn-danger" type="button" data-dismiss="modal">Fechar</button>
                <button class="btn btn-success ml-2" id="registar_fornecedor" type="button">Submeter</button>
            </div>
        </div>
    </div>
</div>
