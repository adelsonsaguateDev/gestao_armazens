@extends('layouts.main')

@section('title', 'Lista de Pagamentos | Gestão de Armazens')

@section('content')
    <div class="app-admin-wrap layout-sidebar-vertical sidebar-full">

        @include('components.sidebar')

        <div class="switch-overlay"></div>
        <div class="main-content-wrap mobile-menu-content bg-off-white m-0">
            @include('components.header')

            <!-- ============ Body content start ============= -->
            <div class="main-content pt-4">
                <div class="breadcrumb">
                    <h1 class="mr-2">Lista de Pagamentos</h1>
                    <ul>
                        <!-- <li><a href="#">Saídas</a></li> -->
                    </ul>
                </div>
                <div class="separator-breadcrumb border-top"></div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 text-left ">
                                        <i style="color:crimson; font-size:large"
                                            title="Estes campos permitem realizar filtros, pelos diversos paramêtros."
                                            class="fa fa-info-circle"></i>
                                    </div>
                                    <div class="col-md-12 text-right ">
                                        <button class="btn btn-danger mb-3" id="print" data-toggle="tooltip"
                                            title="Exportar lista de produtos para PDF">
                                            <span style="font-weight: bold"><i class="far fa-file-pdf"></i> 
                                                EXPORTAR PDF</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-3 mb-3">
                                        <label for="cliente_filtro"
                                            style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Cliente</label>
                                        <select name="cliente_filtro" id="cliente_filtro" class="form-control select2">
                                            <option value="">Selecione...</option>
                                            @foreach ($clientes as $cliente)
                                                <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <label for="data_inicio_saida"
                                            style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Data
                                            Início Saída</label>
                                        <input type="date" class="form-control" name="data_inicio_saida"
                                            id="data_inicio_saida" />
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="data_fim_saida"
                                            style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Data
                                            Fim Saída</label>
                                        <input type="date" class="form-control" name="data_fim_saida"
                                            id="data_fim_saida" />
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="estado_filtro"
                                            style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Estado</label>
                                        <select name="estado_filtro" id="estado_filtro" class="form-control select2">
                                            <option value=""> Selecione uma opção </option>
                                            <option value="1">Activo</option>
                                            <option value="2">Inactivo</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="limit"
                                            style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Limite</label>
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
                                    <div class="list_pagamentos">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end of main-content -->

            <div class="sidebar-overlay open"></div>
            <!-- Footer Start -->
            @include('components.footer')
            <!-- fotter end -->
        </div>

    </div>
@endsection

@section('scripts')
    <script>

        $(document).ready(function() {
            $(".select2").select2({
                allowClear: true,
            });

            hideLoader();

            var limite = $('#limit').val()
            var page = 1

            list(page, limite);

            $(document).on('click', '.pagination a', function(event) {
                event.preventDefault();
                page = $(this).attr('href').split('page=')[1];
                $(this).attr('href', '');
                list(page, limite);
            });

            $(".pesquisar").click(function() {
                let limite = $('#limit').val();
                list(page, limite);
            })

            function list(page, limite) {
                showLoader();
                var estado = $("#estado_filtro").val();
                var cliente_id = $("#cliente_filtro").val();
                var data_inicio = $("#data_inicio_saida").val();
                var data_fim = $("#data_fim_saida").val();

                $.ajax({
                    url: '{{ url('pagamentos') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        estado: estado,
                        cliente_id: cliente_id,
                        data_inicio: data_inicio,
                        data_fim: data_fim,
                        limite: limite,
                        page: page,
                    },
                    dataType: 'html',
                    success: function(data) {
                        $(".list_pagamentos").html(data);
                        hideLoader();
                    },
                    error: function(err) {
                        console.log(err);
                    }
                }).always(function() {
                    hideLoader();
                });
            }

           
            // Receipt function
            $(document).on('click', '.recibo', function() {
                var saida_id = $(this).val();
                myFunction(saida_id);
            });

            function myFunction(saida_id) {
                var rota = $(".rota" + saida_id).val();
                var TheNewWin = window.open(rota, "_blank",
                    "toolbar=yes, scrollbars=yes, resizable=yes, top=100, left=150, width=1024, height=700");
            }
        });
    </script>
@endsection
