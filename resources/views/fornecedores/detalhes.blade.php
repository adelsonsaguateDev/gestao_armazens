@extends('layouts.main')

@section('title', 'Detalhes do Fornecedor | Gestão de Armazens')



@section('content')

<div class="app-admin-wrap layout-sidebar-vertical sidebar-full">

    @include('components.sidebar')

    <div class="switch-overlay"></div>
    <div class="main-content-wrap mobile-menu-content bg-off-white m-0">
        @include('components.header')

        <!-- ============ Body content start ============= -->
        <div class="main-content pt-4">
            <div class="breadcrumb">
                <h1 class="mr-2">Detalhes do Fornecedor</h1>
            </div>
            <div class="separator-breadcrumb border-top"></div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">


                            <div class="ul-widget__item">
                                <div class="ul-widget__info">
                                    <span class="ul-widget__desc text-mute">Nome</span>
                                    <h3 class="ul-widget1__title" style="font-size: 15px"><?= $fornecedor->nome ?? "Sem nome" ?></h3>
                                </div>
                                <div class="ul-widget__info">
                                    <span class="ul-widget__desc text-mute">Telefone</span>
                                    <h3 class="ul-widget1__title" style="font-size: 15px"><?= $fornecedor->telefone ?? "Sem telefone" ?></h3>
                                </div>
                                <div class="ul-widget__info">
                                    <span class="ul-widget__desc text-mute">Email</span>
                                    <h3 class="ul-widget1__title" style="font-size: 15px"><?= $fornecedor->email ?? "Sem email" ?></h3>
                                </div>
                                <div class="ul-widget__info">
                                    <span class="ul-widget__desc text-mute">Endereço</span>
                                    <h3 class="ul-widget1__title" style="font-size: 15px"><?= $fornecedor->endereco ?? "Sem endereço" ?></h3>
                                </div>
                                <div class="ul-widget__info">
                                    <span class="ul-widget__desc text-mute">Data de Registo</span>
                                    <h3 class="ul-widget1__title" style="font-size: 15px"><?= $fornecedor->created_at ?></h3>
                                </div>

                                <div class="ul-widget__info">
                                    <span class="ul-widget__desc text-mute">Estado</span>

                                    @if($fornecedor->estado == "1")
                                        <h3 class="ul-widget1__title" style="font-size: 15px"><span class="badge badge-success mr-1 mb-1"> Activo </span></h3>
                                    @endif

                                    @if($fornecedor->estado == "2")
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
                                    <div class="tab-pane fade active show" id="historico" role="tabpanel" aria-labelledby="historico-tab">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Data</th>
                                                    <th scope="col">Utilizador</th>
                                                    <th scope="col">Descrição</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($historico as $item)
                                                <tr>
                                                    <td>{{ $item->created_at }}</td>
                                                    <td>{{ $item->users->name ?? 'N/A' }}</td>
                                                    <td>{{ $item->descricao }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Nenhum histórico disponível</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- end of main-content -->

        <!-- Footer Start -->
        @include('components.footer')
        <!-- fotter end -->
    </div>

</div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            hideLoader();
        });
    </script>
@endsection
