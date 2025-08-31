@extends('layouts.main')

@section('title', 'Detalhes da Entrada | Gestão de Armazens')

@section('content')

    <div class="app-admin-wrap layout-sidebar-vertical sidebar-full">

        @include('components.sidebar')

        <div class="switch-overlay"></div>
        <div class="main-content-wrap mobile-menu-content bg-off-white m-0">
            @include('components.header')

            <!-- ============ Body content start ============= -->
            <div class="main-content pt-4">
                <div class="breadcrumb">
                    <h1 class="mr-2">Detalhes da Entrada</h1>
                </div>
                <div class="separator-breadcrumb border-top"></div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Tipo de Entrada:</strong> {{ $entrada->tipoEntrada->nome ?? 'N/A' }}</p>
                                <p><strong>Fornecedor:</strong> {{ $entrada->fornecedor->nome ?? 'N/A' }}</p>
                                <p><strong>Nº da Factura:</strong> {{ $entrada->numero_factura ?? 'N/A' }}</p>
                                <p><strong>Data de Aquisição:</strong>
                                    {{ $entrada->data_aquisicao ? \Carbon\Carbon::parse($entrada->data_aquisicao)->format('d/m/Y') : 'N/A' }}
                                </p>
                                <p><strong>Data da Factura:</strong>
                                    {{ $entrada->data_factura ? \Carbon\Carbon::parse($entrada->data_factura)->format('d/m/Y') : 'N/A' }}
                                </p>
                                <p><strong>Ficheiro da Entrada:</strong>
                                    @if ($entrada->ficheiro_entrada)
                                        <a href="{{ asset('storage/entradas_ficheiros/' . $entrada->ficheiro_entrada) }}"
                                            target="_blank">Ver Ficheiro</a>
                                    @else
                                        N/A
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Total Factura:</strong> {{ number_format($entrada->total_factura, 2, ',', '.') }}
                                </p>
                                <p><strong>Total Desconto:</strong>
                                    {{ number_format($entrada->total_desconto, 2, ',', '.') }}</p>
                                <p><strong>Total IVA:</strong> {{ number_format($entrada->total_iva, 2, ',', '.') }}</p>
                                <p><strong>Valor Remanescente:</strong>
                                    {{ number_format($entrada->valor_remanescente, 2, ',', '.') }}</p>
                                <p><strong>Registado por:</strong> {{ $entrada->user->name ?? 'N/A' }}</p>
                                <p><strong>Estado:</strong>
                                    @if ($entrada->estado == '1')
                                        <span class="badge badge-success">Activo</span>
                                    @else
                                        <span class="badge badge-danger">Inactivo</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <hr>
                        <h4>Itens da Entrada</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Qtd. Caixas</th>
                                        <th>Qtd. por Caixa</th>
                                        <th>Preço Compra Caixa</th>
                                        <th>Preço Compra Unitário</th>
                                        <th>Preço Venda Caixa</th>
                                        <th>Preço Venda Unitário</th>
                                        <th>Data Validade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($entrada->itens as $item)
                                        <tr>
                                            <td>{{ $item->produto->descricao ?? 'N/A' }}</td>
                                            <td>{{ $item->qtd_caixas }}</td>
                                            <td>{{ $item->qtd_por_caixa }}</td>
                                            <td>{{ number_format($item->preco_compra_caixa, 2, ',', '.') }}</td>
                                            <td>{{ number_format($item->preco_compra_unitario, 2, ',', '.') }}</td>
                                            <td>{{ number_format($item->preco_venda_caixa, 2, ',', '.') }}</td>
                                            <td>{{ number_format($item->preco_venda_unitario, 2, ',', '.') }}</td>
                                            <td>{{ $item->data_validade ? \Carbon\Carbon::parse($item->data_validade)->format('d/m/Y') : 'N/A' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8">Nenhum item registado para esta entrada.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
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
