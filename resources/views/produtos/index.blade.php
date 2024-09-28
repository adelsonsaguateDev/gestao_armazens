@extends('layouts.main')

@section('title', 'Lista de Produtos | Gestão de Armazens')


@section('content')

<div class="app-admin-wrap layout-sidebar-vertical sidebar-full">
    
    @include('components.sidebar')
    @include('produtos.modal.AddProduto')
    @include('produtos.modal.form_edit')
    @include('produtos.modal.form_requisitar')




    <div class="switch-overlay"></div>
    <div class="main-content-wrap mobile-menu-content bg-off-white m-0">
        @include('components.header')
        
        <!-- ============ Body content start ============= -->
        <div class="main-content pt-4">
            <div class="breadcrumb">
                <h1 class="mr-2">Lista de Produtos</h1>
                <ul>
                    <!-- <li><a href="#">Relatórios</a></li> -->
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
                                        <button  title="Adicionar novo produto" class="btn btn-success" type="button" data-toggle="modal" data-target="#rg_produto" id="btn_registar"><i class="fa fa-plus"></i> REGISTRAR PRODUTO</button> 
                                        <button  title="Imprimir um pdf" class="btn btn-info" type="button" id="print"><i class="fa fa-print"></i> PDF</button> 
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="" style="font-family: 'Arial narrow'; font-size: 14px; color: #2C304D; font-weight: 600;">Descrição do produto</label>
                                        <input type="text" class="form-control" name="descricao_filtro" id="descricao_filtro" />
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
                                    <div class="list_produtos">
                                    
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

            hideLoader();


            var limite = $('#limit').val()
            var page = 1


            list(page,limite);

            // $(document).on('click', '.pagination a', paginaClickHandler);

            $(document).on('click', '.pagination a', function(event) {
                    event.preventDefault();
                    page = $(this).attr('href').split('page=')[1];
                    $(this).attr('href', '');
                    
                    var limite = $('#limit').val();
                    list(page, limite);

                });

            $("#produto_li").addClass("nav-item-active")
            $("#produto_link").addClass("nav-item-active-text")

            $(".pesquisar").click(function() {
                let limite = $('#limit').val();
                list(page,limite);

            })

            $(document).on("click", "#btn_edit", function(){

            
                let id = $(this).val()
                let nome = $(this).attr('nome');
                let descricao = $(this).attr('descricao');
                let quantidade = $(this).attr('quantidade');
                let stock_minimo = $(this).attr('stock_minimo');

                //Preencher os campos do modal de upadte
                
                $("#id").val(id)
                $("#nome_update").val(nome)
                $("#quantidade_update").val(quantidade)
                $("#stock_minimo_update").val(stock_minimo)
                $("#descricao_update").val(descricao)

            });




            $(document).on("click", "#btn_requisitar", function(){

            
                let produto_id = $(this).val()
                let descricao = $(this).attr('descricao');

                //Preencher os campos do modal de upadte

                $("#produto_req").val(produto_id)
                $("#descricao_req").val(descricao)

                $('#req_produto').modal('show');



            });


            $(document).on("click", "#btn_delete", function(){
                        var produto_id = $(this).val();
                        var estado = '2'


                        Swal.fire({
                                title: 'ALERTA!',
                                text: "Tem certeza que deseja apagar o produto?",
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
                                update_estado(produto_id, estado);
                            } 
                        });


            });



            function update_estado(produto_id, estado) {
                showLoader();
                $.ajax({
                    url: '{{url("produto/delete")}}',
                    method: 'POST',
                    data: {
                        _token: '{{csrf_token()}}',
                        produto_id : produto_id,
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
                        produto_id = $(this).val();
                        var estado = '1';


                        Swal.fire({
                                title: 'ALERTA!',
                                text: "Tem certeza que deseja activar o produto?",
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
                                update_estado(produto_id, estado);
                            } 
                        });


            });




            function list(page, limite) {
                showLoader();
                var estado = $("#estado_filtro").val() == "" ? "1" : $("#estado_filtro").val();
                var descricao = $("#descricao_filtro").val()

                $.ajax({
                    url: '{{url("produtos")}}?page=' + page,
                    method: 'GET',
                    data: {
                        "estado": estado,
                        "descricao": descricao,
                        "limite": limite,
                    },
                    dataType: 'html', 
                    success: function(data) {
                        $(".list_produtos").html(data);
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

