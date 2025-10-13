@extends('layouts.main')

@section('title', 'Detalhes do Produto | Gestão de Armazens')

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

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <!-- Coluna da Imagem -->
                            <div class="col-md-4">
                                <div class="border rounded p-3 bg-light shadow-sm d-flex align-items-center justify-content-center h-100" style="min-height: 300px;">
                                    @if ($produtos->imagem)
                                        <img src="{{ asset('storage/produtosImg/' . $produtos->imagem) }}"
                                            alt="Imagem do Produto" class="img-fluid rounded">
                                    @else
                                        <div class="text-center">
                                            <i class="fa fa-image fa-5x text-muted"></i>
                                            <p class="text-muted mt-2">Sem Imagem</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Coluna dos Detalhes -->
                            <div class="col-md-8">
                                <h3 class="mb-3" style="font-weight: bold;">{{ $produtos->descricao ?? '' }}</h3>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Nome Comercial:</strong> {{ $produtos->nome ?? 'N/A' }}</p>
                                        <p><strong>Código de Barras:</strong> {{ $produtos->codigo_barras ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Data de Registo:</strong>
                                            {{ $produtos->created_at->format('d/m/Y H:i') }}</p>
                                        <p><strong>Estado:</strong>
                                            @if ($produtos->estado == '1')
                                                <span class="badge badge-success">Activo</span>
                                            @else
                                                <span class="badge badge-danger">Inactivo</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card user-profile o-hidden mb-4">
                    <div class="card-body">
                        <ul class="nav nav-tabs profile-nav mb-4" id="profileTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="historico-tab" data-toggle="tab" href="#historico"
                                    role="tab" aria-controls="historico" aria-selected="true">
                                    <i class="fa fa-clock mr-1"></i>
                                    Histórico
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content" id="profileTabContent">
                            <div class="tab-pane fade active show" id="historico" role="tabpanel"
                                aria-labelledby="historico-tab">
                                @forelse ($historico as $item)
                                    <div class="mb-2">
                                        <i class="fa fa-user-circle mr-1"></i>
                                        <strong>{{ $item->users->name }}</strong> {{ $item->descricao }}
                                        <p class="text-muted small ml-4"><i
                                                class="fa fa-calendar mr-1"></i>{{ $item->created_at->format('d/m/Y \à\s H:i') }}
                                        </p>
                                    </div>
                                @empty
                                    <p>Sem histórico para apresentar.</p>
                                @endforelse
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


    <script>
        $(document).ready(function() {
            hideLoader();

        });
    </script>

@endsection
