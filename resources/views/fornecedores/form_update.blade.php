
<form action="#"  id="form_editar_fornecedor" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" id="id" value="{{ $fornecedor->id  }}">
    <div class="row">
        <div class="col-md-6">
            <label for="nome">Nome<span class="obrigatorio">*</label>
            <input type="text" name="nome" value="{{ $fornecedor->nome }}" class="form-control" id="nome" aria-describedby="" placeholder="Digite o nome" >
        </div>
        <div class="col-md-6">
            <label for="telefone">Telefone<span class="obrigatorio">*</label>
            <input type="text" maxlength="9" name="telefone" class="form-control numero" id="telefone" value="{{ $fornecedor->telefone }}" aria-describedby="" placeholder="Digite o telefone" >
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $fornecedor->email }}" id="email" aria-describedby="emailHelp" placeholder="Digite o email" >
        </div>
        <div class="col-md-6">
            <label for="endereco">Endereço</label>
            <input type="text" name="endereco" class="form-control" id="endereco" value="{{ $fornecedor->endereco }}" aria-describedby="" placeholder="Digite o endereço" >
        </div>
    </div>

    <br>

  @csrf
  </form>
