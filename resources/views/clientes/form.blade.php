<div><a  style="color: red;text-align:center;">Nota: O ASTERISCO(<span class="text-danger">*</span>) indica a obrigatoriedade do campo.</a> </div>

<div class="row">
    <div class="col-md-6">
        <label for="exampleInputEmail1">Nome de utilizador<span class="obrigatorio">*</label>
    <input type="text" name="username" value="" class="form-control" id="" aria-describedby="" placeholder="Enter username" >
    </div>
    <div class="col-md-6">
        <label for="exampleInputEmail1">Nome<span class="obrigatorio">*</label>
        <input type="text" name="name" class="form-control" id="" value="" aria-describedby="" placeholder="Enter name" >
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-6">
        <label for="exampleInputEmail1">E-mail<span class="obrigatorio">*</label>
        <input type="email" name="email" class="form-control" value="" id="" aria-describedby="emailHelp" placeholder="Enter email" >
    </div>
    <div class="col-md-6">
        <label for="exampleInputEmail1">Contacto<span class="obrigatorio">*</label>
        <input type="text" maxlength="9" name="contacto" class="form-control numero" id="" value="" aria-describedby="" placeholder="Enter contact" >
    </div>
</div>

<br>
<div class="row">

    <div class="col-md-6">
        <label for="">Tipo de Utilizaddor<span class="obrigatorio">*</span></label>

            <select class="form-control fom-select select2" name="permissao[]">
            <option value="">Selecione...</option>
            @foreach ($tipo_utilizador as $item)
                <option value="{{$item->id}}">{{$item->nome}}</option>
            @endforeach

            </select>

    </div>

</div>

<br>


