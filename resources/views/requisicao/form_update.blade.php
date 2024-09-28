
<form action="#"  id="form_editar_funcionario" method="post" enctype="multipart/form-data">
    <input type="hidden" name="id" id="id" value="{{ $utilizador->id  }}">
    <div class="row">
        <div class="col-md-6">
            <label for="username">Nome de utilizador<span class="obrigatorio">*</label>
        <input type="text" name="username" value="{{ $utilizador->username }}" class="form-control" id="username" aria-describedby="" placeholder="Enter username" >
        </div>
        <div class="col-md-6">
            <label for="nome">Nome<span class="obrigatorio">*</label>
            <input type="text" name="name" class="form-control" id="name" value="{{ $utilizador->name }}" aria-describedby="" placeholder="Enter name" >
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6">
            <label for="email">E-mail<span class="obrigatorio">*</label>
            <input type="email" name="email" class="form-control" value="{{ $utilizador->email }}" id="email" aria-describedby="emailHelp" placeholder="Enter email" >
        </div>
        <div class="col-md-6">
            <label for="contacto">Contacto<span class="obrigatorio">*</label>
            <input type="text" maxlength="9" name="contacto" class="form-control" id="contacto" value="{{ $utilizador->contacto }}" aria-describedby="" placeholder="Enter contact" >
        </div>
    </div>
    
    <br>
   
    
    <br>

    
    

  @csrf
  </form>