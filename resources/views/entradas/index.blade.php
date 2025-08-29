@extends('layouts.main')

@section('title', 'Lista de Entradas | Gestão de Armazens')

@section('content')
    <div class="app-admin-wrap layout-sidebar-vertical sidebar-full">

        @include('components.sidebar')
        {{-- @include('entradas.modal.AddEntrada') --}}
        @include('entradas.modal.form_edit')

        <div class="switch-overlay"></div>
        <div class="main-content-wrap mobile-menu-content bg-off-white m-0">
            @include('components.header')

            <!-- ============ Body content start ============= -->
            <div class="main-content pt-4">
                <div class="breadcrumb">
                    <h1 class="mr-2">Lista de Entradas</h1>
                    <ul>
                        <!-- <li><a href="#">Entradas</a></li> -->
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
                                        <a href="{{ route('entrada.create') }}" title="Adicionar nova entrada"
                                            class="btn btn-success" type="button"><i class="fa fa-plus"></i> REGISTAR
                                            ENTRADA</a>
                                        <button title="Imprimir um pdf" class="btn btn-info" type="button"
                                            id="print"><i class="fa fa-print"></i> PDF</button>
                                    </div>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-md-3 mb-3">
                                        <label for="numero_factura_filtro"
                                            style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Nº
                                            Factura</label>
                                        <input type="text" class="form-control" name="numero_factura_filtro"
                                            id="numero_factura_filtro" />
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="fornecedor_filtro"
                                            style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Fornecedor</label>
                                        <select name="fornecedor_filtro" id="fornecedor_filtro"
                                            class="form-control select2">
                                            <option value="">Selecione...</option>
                                            @foreach ($fornecedores as $fornecedor)
                                                <option value="{{ $fornecedor->id }}">{{ $fornecedor->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="tipo_entrada_filtro"
                                            style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Tipo
                                            de Entrada</label>
                                        <select name="tipo_entrada_filtro" id="tipo_entrada_filtro"
                                            class="form-control select2">
                                            <option value="">Selecione...</option>
                                            @foreach ($tipos_entrada as $tipo)
                                                <option value="{{ $tipo->id }}">{{ $tipo->nome }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="data_inicio_filtro"
                                            style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Data
                                            Início</label>
                                        <input type="date" class="form-control" name="data_inicio_filtro"
                                            id="data_inicio_filtro" />
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="data_fim_filtro"
                                            style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Data
                                            Fim</label>
                                        <input type="date" class="form-control" name="data_fim_filtro"
                                            id="data_fim_filtro" />
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
                                    <div class="list_entradas">

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

            $("#entrada_li").addClass("nav-item-active")
            $("#entrada_link").addClass("nav-item-active-text")

            $(".pesquisar").click(function() {
                let limite = $('#limit').val();
                list(page, limite);
            })

            // Funções para adicionar/editar/deletar (serão implementadas nos próximos passos)
            // $(document).on("click", "#btn_edit", function() { /* ... */ });
            // $(document).on("click", "#btn_delete", function() { /* ... */ });
            // $(document).on("click", "#btn_active", function() { /* ... */ });

            function list(page, limite) {
                showLoader();
                var estado = $("#estado_filtro").val();
                var numero_factura = $("#numero_factura_filtro").val();
                var fornecedor_id = $("#fornecedor_filtro").val();
                var tipo_entrada_id = $("#tipo_entrada_filtro").val();
                var data_inicio = $("#data_inicio_filtro").val();
                var data_fim = $("#data_fim_filtro").val();

                $.ajax({
                    url: '{{ url('entradas') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        estado: estado,
                        numero_factura: numero_factura,
                        fornecedor_id: fornecedor_id,
                        tipo_entrada_id: tipo_entrada_id,
                        data_inicio: data_inicio,
                        data_fim: data_fim,
                        limite: limite,
                        page: page,
                    },
                    dataType: 'html',
                    success: function(data) {
                        $(".list_entradas").html(data);
                        hideLoader();
                    },
                    error: function(err) {
                        console.log(err);
                    }
                }).always(function() {
                    hideLoader();
                });
            }

            // Lógica para o formulário de adição (será implementada)
            $('#registrar_entrada').click(function() {
                showLoader();
                var form = $('#form_registrar_entrada');
                var formData = form.serialize(); // Isso não vai funcionar para itens dinâmicos
                var itens = []; // Array para guardar os itens da entrada

                // Exemplo de como obter os itens (precisará ser adaptado ao HTML do formulário)
                $('#itens_entrada_table tbody tr').each(function() {
                    var produto_id = $(this).find('.produto_id').val();
                    var qtd_caixas = $(this).find('.qtd_caixas').val();
                    var qtd_por_caixa = $(this).find('.qtd_por_caixa').val();
                    var preco_compra_caixa = $(this).find('.preco_compra_caixa').val();
                    var preco_compra_unitario = $(this).find('.preco_compra_unitario').val();
                    var preco_venda_caixa = $(this).find('.preco_venda_caixa').val();
                    var preco_venda_unitario = $(this).find('.preco_venda_unitario').val();
                    var data_validade = $(this).find('.data_validade').val();

                    itens.push({
                        produto_id: produto_id,
                        qtd_caixas: qtd_caixas,
                        qtd_por_caixa: qtd_por_caixa,
                        preco_compra_caixa: preco_compra_caixa,
                        preco_compra_unitario: preco_compra_unitario,
                        preco_venda_caixa: preco_venda_caixa,
                        preco_venda_unitario: preco_venda_unitario,
                        data_validade: data_validade
                    });
                });

                // Adicionar os itens ao formData
                formData += '&itens=' + JSON.stringify(itens);

                $.ajax({
                    url: '{{ route('entrada.add') }}',
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success == true) {
                            Swal.fire({
                                icon: "success",
                                title: `${response.message}`,
                                showConfirmButton: false,
                                timer: 2000,
                            });
                            $('#rg_entrada').modal('hide');
                            list(page, limite);
                        } else {
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
            });
        });
    </script>
@endsection
