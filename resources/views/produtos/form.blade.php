<div><a style="color: red;text-align:center;">Nota: O ASTERISCO(*) indica que o campo é obrigatório.</a> </div>

<div class="card">
    <div class="mt-3">
        <div class="col-md text-left">
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="nome"><b>Nome</b></label>
                            <input type="text" name="nome" class="form-control" id="" value=""
                                aria-describedby="" placeholder="Nome Comércial">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="descricao"><b>Descrição</b><span class="obrigatorio">*</span></label>
                            <textarea name="descricao" id="descricao" class="form-control" cols="10" rows="" required value=""
                                aria-describedby="" placeholder="Descrição"></textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="codigo_barras"><b>Código de Barras</b><span class="obrigatorio">*</span></label>
                            <input type="text" name="codigo_barras" class="form-control" id="codigo_barras"
                                value="" aria-describedby="" placeholder="codigo de barras" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="stock_minimo"><b>Stock mínimo<span class="obrigatorio">*</span></b></label>
                            <input type="number" name="stock_minimo" class="form-control" id="stock_minimo"
                                value="" aria-describedby="" required placeholder="Stock minimo">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for=""> <b>Unidade</b> <span class="obrigatorio">*</span></label>
                            <select class="form-control fom-select select2" value="" name="unidade_id"
                                id="unidade_id">
                                <option value="">Selecione...</option>
                                @foreach ($unidades as $item)
                                    <option value="{{ $item->id }}">{{ $item->nome }}</option>
                                @endforeach

                            </select>
                        </div>
                        <div class="col-sm-6 form-group mt-3">
                            <label class="">Logo:</label>

                            <div class="file-loading">
                                <input id="logo" name="logo[]" type="file">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
