@extends('layouts.main')

@section('title', 'Editar Produto | Gestão de Armazens')

@section('content')

<div class="app-admin-wrap layout-sidebar-vertical sidebar-full">
    @include('components.sidebar')

    <div class="switch-overlay"></div>
    <div class="main-content-wrap mobile-menu-content bg-off-white m-0">
        @include('components.header')

        <!-- ============ Body content start ============= -->
        <div class="main-content pt-4">
            <div class="breadcrumb">
                <h1 class="mr-2">Editar Produto</h1>
                <ul>
                    <li><a href="{{ route('produtos') }}">Produtos</a></li>
                    <li>Editar</li>
                </ul>
            </div>
            <div class="separator-breadcrumb border-top"></div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-body">
                            <form action="{{ route('produto.edit') }}" id="form_editar_produto" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" id="id" value="{{ $produto[0]->id ?? '' }}">
                                
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label for="nome_update"><b>Nome</b><span class="obrigatorio">*</span></label>
                                        <input name="nome_update" id="nome_update" class="form-control" value="{{ $produto[0]->nome ?? '' }}" required placeholder="Nome"/>
                                    </div>
                                    
                                    <div class="col-md-6 form-group">
                                        <label for="descricao_update"><b>Descrição</b><span class="obrigatorio">*</span></label>
                                        <textarea name="descricao_update" id="descricao_update" class="form-control" cols="10" rows="3" required placeholder="Descrição">{{ $produto[0]->descricao ?? '' }}</textarea>
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="quantidade_update"><b>Quantidade<span class="obrigatorio">*</span></b></label>
                                        <input type="number" name="quantidade_update" class="form-control" id="quantidade_update" value="{{ $produto[0]->quantidade ?? 0 }}" required placeholder="Quantidade">
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="stock_minimo_update"><b>Stock mínimo<span class="obrigatorio">*</span></b></label>
                                        <input type="number" name="stock_minimo_update" class="form-control" id="stock_minimo_update" value="{{ $produto[0]->stock_minimo ?? 0 }}" required placeholder="Stock minimo">
                                    </div>

                                    <div class="col-md-6 form-group">
                                        <label for="unidade_id_update"><b>Unidade</b><span class="obrigatorio">*</span></label>
                                        <select class="form-control select2" name="unidade_id_update" id="unidade_id_update" required>
                                            <option value="">Selecione...</option>
                                            @if(isset($unidades))
                                                @foreach ($unidades as $unidade)
                                                    <option value="{{ $unidade->id }}" {{ ($produto[0]->unidade_id ?? '') == $unidade->id ? 'selected' : '' }}>
                                                        {{ $unidade->nome }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <div class="col-md-12 form-group">
                                        <label for="imagem_update"><b>Imagem do Produto</b></label>
                                        @if(isset($produto[0]->imagem) && $produto[0]->imagem)
                                            <div class="mb-2">
                                                <label class="text-muted">Imagem atual:</label>
                                                <div>
                                                    <img src="{{ asset('storage/produtosImg/' . $produto[0]->imagem) }}" alt="Imagem atual" style="max-width: 150px; max-height: 150px; border-radius: 5px; border: 1px solid #ddd;">
                                                </div>
                                            </div>
                                        @endif
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="imagem_update" name="imagem_update" accept="image/*">
                                            <label class="custom-file-label" for="imagem_update">Escolher nova imagem...</label>
                                        </div>
                                        <small class="form-text text-muted">Formatos aceites: JPEG, PNG, JPG, GIF, SVG. Tamanho máximo: 2MB</small>
                                        <div id="imagem_preview" class="mt-2" style="display: none;">
                                            <label class="text-muted">Nova imagem:</label>
                                            <div>
                                                <img id="preview_img" src="" alt="Preview" style="max-width: 150px; max-height: 150px; border-radius: 5px; border: 1px solid #ddd;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12 text-right">
                                        <a href="{{ route('produtos') }}" class="btn btn-secondary mr-2">Cancelar</a>
                                        <button class="btn btn-success" id="editar_produto" type="button">Actualizar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Inicializar select2
    $(".select2").select2({
        allowClear: true,
    });

    // Preview da imagem
    $("#imagem_update").change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $("#preview_img").attr("src", e.target.result);
                $("#imagem_preview").show();
            };
            reader.readAsDataURL(file);
        } else {
            $("#imagem_preview").hide();
        }
    });

    $("#editar_produto").click(function() {
        showLoader();
        
        // Criar FormData para suportar upload de arquivos
        const formData = new FormData($("#form_editar_produto")[0]);
        
        $.ajax({
            url: '{{ route("produto.edit") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success == true) {
                    Swal.fire({
                        icon: "success",
                        title: response.message,
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(() => {
                        window.location.href = '{{ route("produtos") }}';
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: response.message,
                        showConfirmButton: false,
                        timer: 2000,
                    });
                }
            },
            error: function(err) {
                console.log(err);
                Swal.fire({
                    icon: "error",
                    title: "Erro ao actualizar produto",
                    showConfirmButton: false,
                    timer: 2000,
                });
            }
        }).always(function() {
            hideLoader();
        });
    });
});
</script>
@endsection
