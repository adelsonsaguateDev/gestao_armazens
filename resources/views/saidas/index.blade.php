@extends('layouts.main')

@section('title', 'Lista de Saídas | Gestão de Armazens')

@section('content')
    <div class="app-admin-wrap layout-sidebar-vertical sidebar-full">

        @include('components.sidebar')
        @include('saidas.modal.form_edit')
        @include('saidas.modal.pagamento')

        <div class="switch-overlay"></div>
        <div class="main-content-wrap mobile-menu-content bg-off-white m-0">
            @include('components.header')

            <!-- ============ Body content start ============= -->
            <div class="main-content pt-4">
                <div class="breadcrumb">
                    <h1 class="mr-2">Lista de Vendas</h1>
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
                                        <a type="button" href="{{ route('saida.create') }}" class="btn btn-success mb-3"
                                            data-toggle="tooltip" title="Venda à Dinheiro">
                                            <span style="font-weight: bold"><i class="fas fa-plus-circle"></i> V.
                                                DINHEIRO</span>
                                        </a>
                                        <a type="button" href="{{ route('saida.create.credito') }}"
                                            class="btn btn-info mb-3" data-toggle="tooltip" title="Venda à Crédito">
                                            <span style="font-weight: bold"><i class="fas fa-plus-circle"></i> V.
                                                CRÉDITO</span>
                                        </a>
                                        <button class="btn btn-danger mb-3" id="print" data-toggle="tooltip"
                                            title="Exportar lista de produtos para PDF">
                                            <span style="font-weight: bold"><i class="far fa-file-pdf"></i> EXPORTAR
                                                PDF</span>
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
                                        <label for="tipo_saida_filtro"
                                            style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Tipo
                                            de Saída</label>
                                        <select name="tipo_saida_filtro" id="tipo_saida_filtro"
                                            class="form-control select2">
                                            <option value="">Selecione...</option>
                                            @foreach ($tipos_saida as $tipo)
                                                <option value="{{ $tipo->id }}">{{ $tipo->nome }}</option>
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
                                        <label for="estado_pagamento_filtro"
                                            style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Estado
                                            de Pagamento</label>
                                        <select name="estado_pagamento_filtro" id="estado_pagamento_filtro"
                                            class="form-control select2">
                                            <option value=""> Selecione uma opção </option>
                                            <option value="pago">Pago</option>
                                            <option value="nao_pago">Não Pago</option>
                                            <option value="parcial">Parcial</option>
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
                                    <div class="list_saidas">

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
        var storePagamentoUrl = "{{ route('pagamento.create') }}";

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

            $("#saida_li").addClass("nav-item-active")
            $("#saida_link").addClass("nav-item-active-text")

            $(".pesquisar").click(function() {
                let limite = $('#limit').val();
                list(page, limite);
            })

            function list(page, limite) {
                showLoader();
                var estado = $("#estado_filtro").val();
                var estado_pagamento = $("#estado_pagamento_filtro").val();
                var saida_id = $("#cliente_filtro").val();
                var tipo_saida_id = $("#tipo_saida_filtro").val();
                var data_inicio = $("#data_inicio_saida").val();
                var data_fim = $("#data_fim_saida").val();

                $.ajax({
                    url: '{{ url('saidas') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        estado: estado,
                        estado_pagamento: estado_pagamento,
                        saida_id: saida_id,
                        tipo_saida_id: tipo_saida_id,
                        data_inicio: data_inicio,
                        data_fim: data_fim,
                        limite: limite,
                        page: page,
                    },
                    dataType: 'html',
                    success: function(data) {
                        $(".list_saidas").html(data);
                        hideLoader();
                    },
                    error: function(err) {
                        console.log(err);
                    }
                }).always(function() {
                    hideLoader();
                });
            }

            $('#registrar_saida').click(function() {
                showLoader();
                var form = $('#form_registrar_saida');
                var itens = []; // Array para guardar os itens da saída

                // Exemplo de como obter os itens (precisará ser adaptado ao HTML do formulário)
                $('#itens_saida_table tbody tr').each(function() {
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
                var formData = new FormData(form[0]); // Use FormData to handle file uploads if any
                formData.append('itens', JSON.stringify(itens));

                $.ajax({
                    url: '{{ route('saida.add') }}',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success == true) {
                            Swal.fire({
                                icon: "success",
                                title: `${response.message}`,
                                showConfirmButton: false,
                                timer: 2000,
                            });
                            $('#rg_saida').modal('hide');
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
                            title: "Ocorreu um erro no servidor.",
                            showConfirmButton: false,
                            timer: 2000,
                        });
                    }
                }).always(function() {
                    hideLoader();
                });
            });

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

            function update_estado(saida_id, estado) {
                showLoader();
                $.ajax({
                    url: '{{ url('saida/delete') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        saida_id: saida_id,
                        estado: estado,
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success == true) {
                            Swal.fire({
                                icon: "success",
                                title: `${response.message}`,
                                showConfirmButton: false,
                                timer: 2000,
                            });

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
            }

            $(document).on("click", "#btn_delete", function() {
                var saida_id = $(this).val();
                var estado = '0'


                Swal.fire({
                    title: 'ALERTA!',
                    text: "Tem certeza que deseja apagar a venda?",
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
                        update_estado(saida_id, estado);
                    }
                });


            });

            $(document).on("click", "#btn_active", function() {
                var saida_id = $(this).val();
                var estado = '1';


                Swal.fire({
                    title: 'ALERTA!',
                    text: "Tem certeza que deseja activar a venda?",
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
                        update_estado(saida_id, estado);
                    }
                });


            });



        });
    </script>
    <script src="{{ asset('js/pagamentos.js') }}"></script> {{-- Adicionado --}}
@endsection
