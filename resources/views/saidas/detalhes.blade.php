@extends('layouts.main')

@section('title', 'Detalhes da Saída | Gestão de Armazens')

@section('content')

    <div class="app-admin-wrap layout-sidebar-vertical sidebar-full">

        @include('components.sidebar')

        <div class="switch-overlay"></div>
        <div class="main-content-wrap mobile-menu-content bg-off-white m-0">
            @include('components.header')

            <!-- ============ Body content start ============= -->
            <div class="main-content pt-4">
                <div class="breadcrumb">
                    <h1 class="mr-2">Detalhes da Saída</h1>
                </div>
                <div class="separator-breadcrumb border-top"></div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Tipo de Saída:</strong> {{ $saida->tipoSaida->nome ?? 'N/A' }}</p>
                                <p><strong>Cliente:</strong> {{ $saida->cliente->nome ?? 'N/A' }}</p>
                                <p><strong>Nº da Factura:</strong> {{ $saida->numero_factura ?? 'N/A' }}</p>
                                <p><strong>Data da Saída:</strong>
                                    {{ $saida->data ? \Carbon\Carbon::parse($saida->data)->format('d/m/Y') : 'N/A' }}
                                </p>
                                <p><strong>Valor Total:</strong> {{ number_format($saida->valor_total, 2, ',', '.') }}
                                </p>
                                <p><strong>Valor Total IVA:</strong> {{ number_format($saida->valor_total_iva, 2, ',', '.') }}</p>
                                <p><strong>Valor Pago:</strong> {{ number_format($saida->valor_pago, 2, ',', '.') }}</p>
                                <p><strong>Valor Remanescente:</strong>
                                    {{ number_format($saida->valor_remanescente, 2, ',', '.') }}</p>
                                <p><strong>Desconto:</strong> {{ number_format($saida->desconto, 2, ',', '.') }}</p>
                                <p><strong>Valor Entregue:</strong> {{ number_format($saida->valor_entregue, 2, ',', '.') }}</p>
                                <p><strong>Trocos:</strong> {{ number_format($saida->trocos, 2, ',', '.') }}</p>
                                <p><strong>Tipo de Pagamento:</strong> {{ $saida->tipo_pagamento_id ?? 'N/A' }}</p>
                                <p><strong>Número:</strong> {{ $saida->numero ?? 'N/A' }}</p>
                                <p><strong>Número Cotação:</strong> {{ $saida->numero_cotacao ?? 'N/A' }}</p>
                                <p><strong>Validade Cotação:</strong>
                                    {{ $saida->validade_cotacao ? \Carbon\Carbon::parse($saida->validade_cotacao)->format('d/m/Y') : 'N/A' }}
                                </p>
                                <p><strong>Slip:</strong> {{ $saida->slip ?? 'N/A' }}</p>
                                <p><strong>Estado Pagamento:</strong> {{ $saida->estado_pagamento ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Registado por:</strong> {{ $saida->user->name ?? 'N/A' }}</p>
                                <p><strong>Estado:</strong>
                                    @if ($saida->activo == '1')
                                        <span class="badge badge-success">Activo</span>
                                    @elseif ($saida->activo == '0')
                                        <span class="badge badge-danger">Eliminado</span>
                                    @elseif ($saida->activo == '2')
                                        <span class="badge badge-warning">...</span>
                                    @elseif ($saida->activo == '3')
                                        <span class="badge badge-info">Devolvida</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <hr>
                        <h4>Itens da Saída</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th>Quantidade</th>
                                        <th>Preço Unitário</th>
                                        <th>Preço Compra</th>
                                        <th>IVA</th>
                                        <th>Valor IVA</th>
                                        <th>Custo</th>
                                        <th>Desconto (%)</th>
                                        <th>Desconto (Valor)</th>
                                        <th>Tipo Motivo</th>
                                        <th>Motivo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($saida->itens as $item)
                                        <tr>
                                            <td>{{ $item->produto->descricao ?? 'N/A' }}</td>
                                            <td>{{ $item->quantidade }}</td>
                                            <td>{{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                                            <td>{{ number_format($item->preco_compra, 2, ',', '.') }}</td>
                                            <td>{{ number_format($item->iva, 2, ',', '.') }}</td>
                                            <td>{{ number_format($item->valor_iva, 2, ',', '.') }}</td>
                                            <td>{{ number_format($item->custo, 2, ',', '.') }}</td>
                                            <td>{{ number_format($item->desconto_percentual, 2, ',', '.') }}</td>
                                            <td>{{ number_format($item->desconto_valor, 2, ',', '.') }}</td>
                                            <td>{{ $item->tipo_motivo ?? 'N/A' }}</td>
                                            <td>{{ $item->motivo ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11">Nenhum item registado para esta saída.</td>
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
