@extends('layouts.main')

@section('title', 'Lista de Produtos | Gestão de Armazens')


@section('content')

<div class="app-admin-wrap layout-sidebar-vertical sidebar-full">
    
    @include('components.sidebar')

    <div class="switch-overlay"></div>
    <div class="main-content-wrap mobile-menu-content bg-off-white m-0">
        @include('components.header')
        
        <!-- ============ Body content start ============= -->
        <div class="main-content pt-4">
            <div class="breadcrumb">
                <h1 class="mr-2">Detalhes do Produto</h1>
            </div>
            <div class="separator-breadcrumb border-top"></div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            

                            <div class="ul-widget__item">
                                <div class="ul-widget__info">
                                    <span class="ul-widget__desc text-mute">Descrição</span>
                                    <h3 class="ul-widget1__title" style="font-size: 15px"><?= $produtos->descricao ?? "" ?></h3> 
                                </div>
                                <div class="ul-widget__info">
                                    <span class="ul-widget__desc text-mute">Data de Registo</span>
                                    <h3 class="ul-widget1__title" style="font-size: 15px"><?= $produtos->created_at ?></h3>
                                </div>
                                
                                <div class="ul-widget__info">
                                    <span class="ul-widget__desc text-mute">Estado</span>
                                    
                                    @if($produtos->estado == "1")
                                        <h3 class="ul-widget1__title" style="font-size: 15px"><span class="badge badge-success mr-1 mb-1"> Activo </span></h3>
                                    @endif

                                    @if($produtos->estado == "2")
                                        <h3 class="ul-widget1__title" style="font-size: 15px"><span class="badge badge-danger mr-1 mb-1"> Inactivo </span></h3>
                                    @endif
                                        
                                </div>

                                <div class="ul-widget__info">
                                    <span class="ul-widget__desc text-mute"></span>
                                    <h3 class="ul-widget1__title" style="font-size: 15px"></h3>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    <div class="main-content">
                        <div class="card user-profile o-hidden mb-4">

                            <div class="card-body">
                                <ul class="nav nav-tabs profile-nav mb-4" id="profileTab" role="tablist">

                                    <li class="nav-item">
                                        <a class="nav-link active" id="historico-tab" data-toggle="tab" href="#historico"role="tab" aria-controls="about" aria-selected="true">
                                            <i class="fa fa-clock mr-1"></i>
                                            Histórico
                                        </a>
                                    </li>
                                                                    
                                </ul>
                                <div class="tab-content" id="profileTabContent">
                                    
                                    <div class="tab-pane fade" id="photos" role="tabpanel" aria-labelledby="photos-tab">
                                        <div class="row anexos-pagamentos---Por fazer">


                                        </div>
                                    </div>
                                    <div class="tab-pane fade active show" id="historico" role="tabpanel" aria-labelledby="about-tab">
                                        @foreach ($historico as $item) 
                                            <div class="mb-1"><strong class="mr-1"> <i class="fa fa-user-circle mr-1"></i> {{$item->users->name}} {{$item->descricao}} </strong>
                                                <p class="text-muted"> <i class="fa fa-calendar mr-1"></i> {{$item->created_at}}</p>
                                            </div>
                                        @endforeach

                                    </div>
                                </div>
                            </div>
                        </div><!-- end of main-content -->
                    </div><!-- Footer Start -->
                </div>

            </div>
            
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

            // $(document).on('click', '.pagination a', paginaClickHandler);

            $(document).on('click', '.pagination a', function(event) {
                    event.preventDefault();
                    page = $(this).attr('href').split('page=')[1];
                    $(this).attr('href', '');
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

                //Preencher os campos do modal de upadte
                $("#id").val(id)
                $("#nome_editar").val(nome)


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
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                // Ação quando o botão de cancelamento é clicado
                                Swal.fire('', 'Operação foi cancelada!', 'error');
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

