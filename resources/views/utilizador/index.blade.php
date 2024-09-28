@extends('layouts.main')

@section('title', 'Lista de Utilizadores | Gestão de Armazens')


@section('content')

<div class="app-admin-wrap layout-sidebar-vertical sidebar-full">
    
    @include('components.sidebar')
    @include('utilizador.modal.AddUtilizador')
    @include('utilizador.modal.form_edit')



    <div class="switch-overlay"></div>
    <div class="main-content-wrap mobile-menu-content bg-off-white m-0">
        @include('components.header')
        
        <!-- ============ Body content start ============= -->
        <div class="main-content pt-4">
            <div class="breadcrumb">
                <h1 class="mr-2">Lista de Funcionarios</h1>
                <ul>
                </ul>
            </div>
            <div class="separator-breadcrumb border-top"></div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-3">
                        <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 text-left ">
                                        <i style="color:crimson; font-size:large" title="Estes campos permitem realizar filtros, pelos diversos paramêtros." class="fa fa-info-circle"></i>
                                    </div>
                                    <div class="col-md-12 text-right ">
                                        <button  title="Adicionar novo produto" class="btn btn-success" type="button" data-toggle="modal" data-target="#rg_utilizador" id="btn_registar"><i class="fa fa-plus"></i> REGISTRAR FUNCIONARIO</button> 
                                        <button  title="Imprimir um pdf" class="btn btn-info" type="button" id="print"><i class="fa fa-print"></i> PDF</button> 
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="" style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Nome</label>
                                        <input type="text" class="form-control" name="nome_filtro" id="nome_filtro" />
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="" style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Estado</label>
                                        <select name="estado_filtro" id="estado_filtro" class="form-control select2">

                                            <option value=""> Selecione uma opção </option>
                                            <option value="1">Activo</option>
                                            <option value="2">Inactivo</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="" style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Limite</label>
                                        <select name="limit" id="limit" class="form-control select2">

                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="200">200</option>
                                            <option value="1000">1000</option>
                                            <option value="2000">2000</option>
                                            <option value="">Todos</option>
                                        </select>
                                    </div>
                                
                                    <div class="col-md-12 mb-3 text-right" style="text-align: right">
                                        <button class="btn btn-primary btn-lg  pesquisar">Pesquisar</button>
                                    </div>
                                </div>
                                <br><br>

                                <div class="table-responsive"> 
                                    <div class="list_utilizadores">
                                    
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            <!-- end of row-->
            
            <!-- end of main-content -->
            </div>
                <div class="sidebar-overlay open"></div>
                <!-- Footer Start -->
                @include('components.footer')
                <!-- fotter end -->
        </div>

    </div>
</div>

@endsection


@section('scripts')
<script>
        $(document).ready(function() {
            $(".select2").select2({
                allowClear: true,
            });

            var limite = $('#limit').val()
            var page = 1

            list(page,limite);

            $(document).on('click', '.pagination a', function(event) {
                    event.preventDefault();
                    page = $(this).attr('href').split('page=')[1];
                    $(this).attr('href', '');
                    list(page, limite);

                });


            $(".pesquisar").click(function() {
                let limite = $('#limit').val();
                list(page,limite);

            })

            $(document).on("click", "#btn_edit", function(){

                let id = $(this).val()
               
                var url = '{{url("utilizador")}}/' + id;


                $.ajax({
                    url: url,
                    method: 'GET',
                    data: {
                        _token: '{{csrf_token()}}',
                    },
                    dataType: 'html', 
                    success: function(response) {
                        console.log(response)

                        $('.conteudo_funcionario').html(response);
                        $('#edit_funcionario').modal('show');



                    },
                    error: function(err) {
                        // console.log(err);
                    }
                }).always(function() {
                    hideLoader();
                });


            });


            $(document).on("click", "#btn_delete", function(){
                        var utilizador_id = $(this).val();
                        var estado = '2'


                        Swal.fire({
                                title: 'ALERTA!',
                                text: "Tem certeza que deseja apagar o funcionario?",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#0CC27E',
                                cancelButtonColor: '#FF586B',
                                confirmButtonText: 'Sim, Tenho!',
                                cancelButtonText: 'Não, cancelar!',
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'btn btn-success mr-5',
                                    cancelButton: 'btn btn-danger'
                                }
                            }).then((result) => {
                            if (result.isConfirmed) {
                                update_estado(utilizador_id, estado);
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                Swal.fire('', 'Operação foi cancelada!', 'warning');
                            }
                        });


            });


            $(document).on("click", "#btn_show", function(){
                showLoader();

                var produto_id = $(this).val();
                var url = '{{url("produto")}}/' + produto_id;

                $.ajax({
                    url: url,
                    method: 'GET',
                    data: {
                        _token: '{{csrf_token()}}',
                    },
                    dataType: 'json', 
                    success: function(response) {
                        console.log(response)


                    },
                    error: function(err) {
                        console.log(err);
                    }
                }).always(function() {
                    hideLoader();
                });

            });




            function update_estado(utilizador_id, estado) {
                showLoader();
                $.ajax({
                    url: '{{url("utilizador/delete")}}',
                    method: 'POST',
                    data: {
                        _token: '{{csrf_token()}}',
                        utilizador_id : utilizador_id,
                        estado : estado,
                    },
                    dataType: 'json', 
                    success: function(response) {
                        if(response.success == true){
                            Swal.fire({
                                icon: "success",
                                title: `${response.message}`,
                                showConfirmButton: false,
                                timer: 2000,
                            });
            
                            list(page,limite);


                        }else{
                            Swal.fire({
                                icon: "error",
                                title: `${response.message}`,
                                showConfirmButton: false,
                                timer: 2000,
                            });
                        }


                    },
                    error: function(err) {
                        console.log(err);
                        Swal.fire({
                            icon: "error",
                            title: `${err}`,
                            showConfirmButton: false,
                            timer: 2000,
                        });

                    }
                }).always(function() {
                    hideLoader();
                });
            }



            $(document).on("click", "#btn_active", function(){
                        var utilizador_id = $(this).val();
                        var estado = '1';


                        Swal.fire({
                                title: 'ALERTA!',
                                text: "Tem certeza que deseja activar o funcionario?",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#0CC27E',
                                cancelButtonColor: '#FF586B',
                                confirmButtonText: 'Sim, Tenho!',
                                cancelButtonText: 'Não, cancelar!',
                                buttonsStyling: false,
                                customClass: {
                                    confirmButton: 'btn btn-success mr-5',
                                    cancelButton: 'btn btn-danger'
                                }
                            }).then((result) => {
                            if (result.isConfirmed) {
                                // Ação quando o botão de confirmação é clicado
                                update_estado(utilizador_id, estado);
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                // Ação quando o botão de cancelamento é clicado
                                Swal.fire('', 'Operação foi cancelada!', 'error');
                            }
                        });


            });




            function list(page, limite) {
                showLoader();
                var estado = $("#estado_filtro").val() == "" ? "1" : $("#estado_filtro").val();
                var nome = $("#nome_filtro").val()

                $.ajax({
                    url: '{{url("utilizadores")}}?page=' + page,
                    method: 'GET',
                    data: {
                        "estado": estado,
                        "nome": nome,
                        "limite": limite,
                    },
                    dataType: 'html', 
                    success: function(data) {
                        $(".list_utilizadores").html(data);
                        hideLoader();
                    },
                    error: function(err) {
                        console.log(err);
                    }
                }).always(function() {
                    hideLoader();
                });
            }
        });


        //fileinput

        $("#input-res-2").fileinput({
                            uploadUrl: "http://localhost/file-upload.php",
                            enableResumableUpload: true,
                            resumableUploadOptions: {
                            },
                            uploadExtraData: {
                                'uploadToken': 'SOME-TOKEN', // for access control / security
                            },
                            maxFileCount: 5,
                            allowedFileTypes: ['image'],    // allow only images
                            showCancel: true,
                            showUpload: false,
                            initialPreviewAsData: true,
                            overwriteInitial: false,
                            theme: 'fas',
                            deleteUrl: "http://localhost/file-delete.php"
                        }).on('fileuploaded', function(event, previewId, index, fileId) {
                            console.log('File Uploaded', 'ID: ' + fileId + ', Thumb ID: ' + previewId);
                        }).on('fileuploaderror', function(event, data, msg) {
                            console.log('File Upload Error', 'ID: ' + data.fileId + ', Thumb ID: ' + data.previewId);
                        }).on('filebatchuploadcomplete', function(event, preview, config, tags, extraData) {
                            console.log('File Batch Uploaded', preview, config, tags, extraData);

                        });

</script>
@endsection

