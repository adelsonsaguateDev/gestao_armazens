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
                                    <p class="text-primary text-24 line-height-1 mb-2">{{ number_format($totalEntradas, 2, ',', '.') }} MT</p>
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
                                    <p class="text-primary text-24 line-height-1 mb-2">{{ number_format($totalSaidas, 2, ',', '.') }} MT</p>
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

                <!-- Gráficos de Pizza -->
                <div class="row">
                    <!-- Entradas vs Saídas -->
                    <div class="col-lg-6 col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-title">Entradas vs Saídas</div>
                                <canvas id="entradasSaidasChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Vendas a Dinheiro vs Crédito -->
                    <div class="col-lg-6 col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-title">Vendas a Dinheiro vs Crédito</div>
                                <canvas id="vendasDinheiroCreditoChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vendas por Forma de Pagamento -->
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-title">Vendas por Forma de Pagamento</div>
                                <canvas id="vendasFormaPagamentoChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top 10 Produtos Mais Vendidos -->
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-title">Top 10 Produtos Mais Vendidos</div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Produto</th>
                                                <th>Quantidade Vendida</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($top10MaisVendidos as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->produto->descricao ?? 'N/A' }}</td>
                                                    <td>{{ number_format($item->total_vendido, 0, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Nenhum dado disponível</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top 10 Produtos Menos Vendidos -->
                    <div class="col-lg-6 col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-title">Top 10 Produtos Menos Vendidos</div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Produto</th>
                                                <th>Quantidade Vendida</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($top10MenosVendidos as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->produto->descricao ?? 'N/A' }}</td>
                                                    <td>{{ number_format($item->total_vendido, 0, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Nenhum dado disponível</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Produtos com Mais Entradas -->
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-title">Top 10 Produtos com Mais Entradas</div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Produto</th>
                                                <th>Quantidade de Entrada</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($produtosMaisEntradas as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->produto->descricao ?? 'N/A' }}</td>
                                                    <td>{{ number_format($item->total_entrada, 0, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Nenhum dado disponível</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Produtos com Menos Entradas -->
                    <div class="col-lg-6 col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-title">Top 10 Produtos com Menos Entradas</div>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Produto</th>
                                                <th>Quantidade de Entrada</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($produtosMenosEntradas as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->produto->descricao ?? 'N/A' }}</td>
                                                    <td>{{ number_format($item->total_entrada, 0, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Nenhum dado disponível</td>
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

            // Configuração do Gráfico de Vendas Mensais
            var ctx = document.getElementById('salesChart').getContext('2d');
            var salesChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($salesLabels),
                    datasets: [{
                        label: 'Total de Vendas (MT)',
                        data: @json($salesValues),
                        backgroundColor: 'rgba(255, 102, 102, 0.8)',
                        borderColor: 'rgba(255, 102, 102, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('pt-MZ', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }).format(value) + ' MT';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += new Intl.NumberFormat('pt-MZ', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }).format(context.parsed.y) + ' MT';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // Gráfico de Pizza: Entradas vs Saídas
            var ctxEntradasSaidas = document.getElementById('entradasSaidasChart').getContext('2d');
            var entradasSaidasChart = new Chart(ctxEntradasSaidas, {
                type: 'pie',
                data: {
                    labels: @json($entradasSaidasData['labels']),
                    datasets: [{
                        data: @json($entradasSaidasData['values']),
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 99, 132, 0.8)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += new Intl.NumberFormat('pt-MZ', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }).format(context.parsed) + ' MT';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // Gráfico de Pizza: Vendas a Dinheiro vs Crédito
            var ctxDinheiroCredito = document.getElementById('vendasDinheiroCreditoChart').getContext('2d');
            var vendasDinheiroCreditoChart = new Chart(ctxDinheiroCredito, {
                type: 'pie',
                data: {
                    labels: @json($vendasDinheiroVsCredito['labels']),
                    datasets: [{
                        data: @json($vendasDinheiroVsCredito['values']),
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 159, 64, 0.8)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += new Intl.NumberFormat('pt-MZ', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }).format(context.parsed) + ' MT';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });

            // Gráfico de Barras: Vendas por Forma de Pagamento
            var ctxFormaPagamento = document.getElementById('vendasFormaPagamentoChart').getContext('2d');
            var vendasFormaPagamentoChart = new Chart(ctxFormaPagamento, {
                type: 'bar',
                data: {
                    labels: @json($vendasPorFormaPagamentoData['labels']),
                    datasets: [{
                        label: 'Total de Vendas (MT)',
                        data: @json($vendasPorFormaPagamentoData['values']),
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 159, 64, 0.8)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('pt-MZ', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }).format(value) + ' MT';
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    label += new Intl.NumberFormat('pt-MZ', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }).format(context.parsed.y) + ' MT';
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
