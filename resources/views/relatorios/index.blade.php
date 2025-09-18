@extends('layouts.main')

@section('title', 'Relatórios | Gestão de Armazens')

@section('content')
    <div class="app-admin-wrap layout-sidebar-vertical sidebar-full">

        @include('components.sidebar')

        <div class="switch-overlay"></div>
        <div class="main-content-wrap mobile-menu-content bg-off-white m-0">
            @include('components.header')

            <!-- ============ Body content start ============= -->
            <div class="main-content pt-4">
                <div class="breadcrumb">
                    <h1 class="mr-2">Secção de Relatórios</h1>
                </div>
                <div class="separator-breadcrumb border-top"></div>

                <!-- Cards de Totais -->
                <div class="row">
                    <!-- Card 1 -->
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                            <div class="card-body text-center">
                                <i class="i-Full-Cart"></i>
                                <div class="content">
                                    <p class="text-muted mt-2 mb-0">Total de Entradas</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">{{ number_format($totalEntradas, 2, ',', '.') }} MZN</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Card 2 -->
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                            <div class="card-body text-center">
                                <i class="i-Shop-4"></i>
                                <div class="content">
                                    <p class="text-muted mt-2 mb-0">Total de Saídas</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">{{ number_format($totalSaidas, 2, ',', '.') }} MZN</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Card 3 -->
                    <div class="col-lg-4 col-md-6 col-sm-6">
                        <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                            <div class="card-body text-center">
                                <i class="i-Business-Man"></i>
                                <div class="content">
                                    <p class="text-muted mt-2 mb-0">Total de Clientes</p>
                                    <p class="text-primary text-24 line-height-1 mb-2">{{ $totalClientes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Vendas -->
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-title">Vendas Mensais (Ano Corrente)</div>
                                <canvas id="salesChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Baixo Stock -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="card-title">Produtos com Baixo Stock (Abaixo ou igual ao Stock Mínimo)</div>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Produto</th>
                                                <th>Stock Mínimo</th>
                                                <th>Stock Actual</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                           @forelse ($lowStockProducts as $produto)
                                               <tr>
                                                   <td>{{ $loop->iteration }}</td>
                                                   <td>{{ $produto->descricao }}</td>
                                                   <td>{{ $produto->stock_minimo }}</td>
                                                   <td>{{ (int)($produto->itens_entrada_sum_quantidade_disponivel ?? 0) }}</td>
                                               </tr>
                                           @empty
                                               <tr>
                                                   <td colspan="4" class="text-center">Nenhum produto com baixo stock.</td>
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
            <!-- end of main-content -->

            <!-- Footer Start -->
            @include('components.footer')
            <!-- fotter end -->
        </div>
    </div>

@endsection

@section('scripts')
    <!-- Incluir Chart.js via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            hideLoader();
            $("#relatorios_li").addClass("nav-item-active");
            $("#relatorios_link").addClass("nav-item-active-text");

            // Configuração do Gráfico de Vendas
            var ctx = document.getElementById('salesChart').getContext('2d');
            var salesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($salesLabels),
                    datasets: [{
                        label: 'Total de Vendas (MZN)',
                        data: @json($salesValues),
                        backgroundColor: 'rgba(255, 102, 102, 0.8)',
                        borderColor: 'rgba(255, 102, 102, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endsection
