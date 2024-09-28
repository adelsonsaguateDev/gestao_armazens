@extends('layouts.main')

@section('title', 'Lista de requisicao | Gestão de Armazens')


@section('content')

<div class="app-admin-wrap layout-sidebar-vertical sidebar-full">

    @include('components.sidebar')


    <div class="switch-overlay"></div>
    <div class="main-content-wrap mobile-menu-content bg-off-white m-0">
        @include('components.header')

        <!-- ============ Body content start ============= -->
        <div class="main-content pt-4">
            <div class="breadcrumb">
                <h1 class="mr-2">Lista de Requisições</h1>
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
                                        <button  title="Imprimir um pdf" class="btn btn-info" type="button" id="print"><i class="fa fa-print"></i> PDF</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="" style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Descrição do produto</label>
                                        <input type="text" class="form-control" name="descricao_filtro" id="descricao_filtro" />
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="" style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Estado da Requisção</label>
                                        <select name="estado_filtro" id="estado_filtro" class="form-control select2">

                                            <option value=""> Selecione uma opção </option>
                                            <option value="1">PENDENTE</option>
                                            <option value="2">APROVADA</option>
                                            <option value="3">REPROVADA</option>
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
                                    <div class="list_requisicoes">

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


            $(document).on("click", "#btn_aprovar", function(){
                        var requisicao_id = $(this).val();
                        var produto_id = $(this).attr('produto_id');
                        var estado_requisicao = $(this).attr('estado_requisicao');
                        var quantidade = $(this).attr('quantidade');

                        Swal.fire({
                                title: 'ALERTA!',
                                text: "Tem certeza que deseja aprovar a requisição?",
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
                                movimento(requisicao_id, produto_id, estado_requisicao,quantidade,'aprovacao');
                            }
                        });


            });



            $(document).on("click", "#btn_reprovar", function(){
                        var requisicao_id = $(this).val();
                        var produto_id = $(this).attr('produto_id');
                        var estado_requisicao = $(this).attr('estado_requisicao');
                        var quantidade = $(this).attr('quantidade');

                        Swal.fire({
                                title: 'ALERTA!',
                                text: "Tem certeza que deseja reprovar a requisição?",
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
                                movimento(requisicao_id, produto_id, estado_requisicao,quantidade,'reprovacao');
                            }
                        });


            });






            function movimento(requisicao_id, produto_id, estado_requisicao,quantidade,movimento) {
                showLoader();

                var url = '{{url("requisicao/movimento")}}/' + movimento;

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: '{{csrf_token()}}',
                        requisicao_id : requisicao_id,
                        produto_id : produto_id,
                        estado_requisicao : estado_requisicao,
                        quantidade : quantidade,
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


            function list(page, limite) {
                showLoader();
                var estado_requisicao = $("#estado_filtro").val();
                var descricao = $("#descricao_filtro").val();

                $.ajax({
                    url: '{{url("requisicoes")}}?page=' + page,
                    method: 'GET',
                    data: {
                        "estado_requisicao": estado_requisicao,
                        "descricao": descricao,
                        "limite": limite,
                    },
                    dataType: 'html',
                    success: function(data) {
                        $(".list_requisicoes").html(data);
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


</script>
@endsection

