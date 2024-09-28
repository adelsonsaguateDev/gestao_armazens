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
                <h1 class="mr-2">Relatórios de Movimentos</h1>
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
                                        <label for="" style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Descrição</label>
                                        <input type="text" class="form-control" name="descricao_filtro" id="descricao_filtro" />
                                    </div>
                                    <div class="col-md-3 form-group mb-3">
                                        <label>Data Início:</label>
                                        <input placeholder="yyyy-mm-dd" style="background: white !important;" name="data_inicio" value="" type="date" id="data_inicio" class="form-control" />
                                    </div>
                                    <div class="col-md-3 form-group mb-3">
                                        <label>Data Fim:</label>
                                        <input placeholder="yyyy-mm-dd" style="background: white !important;" name="data_fim" value="" type="date" id="data_fim" class="form-control" />
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="" style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Estado da Requisição</label>
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
                                    <div class="list_relatorios">
                                    
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

            function list(page, limite) {
                showLoader();
                var estado_requisicao = $("#estado_filtro").val();
                var descricao = $("#descricao_filtro").val()
                var data_inicio = $("#data_inicio").val()
                var data_fim = $("#data_fim").val()


                

                $.ajax({
                    url: '{{url("relatorios")}}?page=' + page,
                    method: 'GET',
                    data: {
                        "estado_requisicao": estado_requisicao,
                        "descricao": descricao,
                        "data_inicio": data_inicio,
                        "data_fim": data_fim,
                        "limite": limite,
                    },
                    dataType: 'html', 
                    success: function(data) {
                        $(".list_relatorios").html(data);
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

