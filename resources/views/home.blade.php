@extends('layouts.main')

@section('title', 'Dashboard - Sistema de Gestão')


@section('content')

<div class="app-admin-wrap layout-sidebar-vertical sidebar-full">
    
    @include('components.sidebar')

    <div class="switch-overlay"></div>
    <div class="main-content-wrap mobile-menu-content bg-off-white m-0">
        @include('components.header')
        
        <!-- ============ Body content start ============= -->
        <div class="main-content pt-4">
            
            <!-- Row 1: Welcome & Quick Actions -->
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    {{-- Assuming user is authenticated and you can get their name --}}
                    <h2>Olá, {{ Auth::user()->name ?? 'Utilizador' }}!</h2>
                    <p>Bem-vindo de volta ao seu painel de controle.</p>
                </div>
                <div class="col-lg-6 col-md-12 text-lg-right">
                    <a href="#" class="btn btn-primary mb-2">Nova Venda</a>
                    <a href="#" class="btn btn-success mb-2">Nova Entrada</a>
                    <a href="{{ route('produto.list') }}" class="btn btn-info mb-2">Adicionar Produto</a>
                </div>
            </div>

            <div class="separator-breadcrumb border-top my-4"></div>

            <!-- Row 2: Stat Cards -->
            <div class="row">
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                        <div class="card-body text-center">
                            <i class="i-Library"></i>
                            <div class="content">
                                <p class="text-muted mt-2 mb-0">Total Produtos</p>
                                <p class="text-primary text-24 line-height-1 mb-2">{{ $total_produtos ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-warning o-hidden mb-4">
                        <div class="card-body text-center">
                            <i class="i-Danger"></i>
                            <div class="content">
                                <p class="text-muted mt-2 mb-0">Stock Baixo</p>
                                {{-- Pass $produtos_stock_baixo from your controller --}}
                                <p class="text-warning text-24 line-height-1 mb-2">{{ $produtos_stock_baixo ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-success o-hidden mb-4">
                        <div class="card-body text-center">
                            <i class="i-Remove-Cart"></i>
                            <div class="content">
                                <p class="text-muted mt-2 mb-0">Total Requisições</p>
                                <p class="text-success text-24 line-height-1 mb-2">{{ $total_requisicoes ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-danger o-hidden mb-4">
                        <div class="card-body text-center">
                            <i class="i-Loading-3"></i>
                            <div class="content">
                                <p class="text-muted mt-2 mb-0">Requisições Pendentes</p>
                                {{-- Pass $requisicoes_pendentes from your controller --}}
                                <p class="text-danger text-24 line-height-1 mb-2">{{ $requisicoes_pendentes ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row for Quick Reports -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-title">Relatórios Rápidos</div>
                            <a href="#" class="btn btn-outline-primary m-1">Vendas do Mês</a>
                            <a href="#" class="btn btn-outline-primary m-1">Entradas do Mês</a>
                            <a href="#" class="btn btn-outline-primary m-1">Inventário Completo</a>
                            <a href="#" class="btn btn-outline-primary m-1">Produtos Mais Requisitados</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 3: Charts -->
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-title">Requisições nos Últimos 6 Meses</div>
                            <div id="echartBar" style="height: 300px;"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-title">Estado das Requisições</div>
                            <div id="echartPie" style="height: 300px;"></div>
                            {{-- NOTE: You need to update the data source for this pie chart in your JS file --}}
                            {{-- to show stats like {value:335, name:'Aprovadas'}, {value:10, name:'Pendentes'}, etc. --}}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row 4: Low Stock & Recent Activity -->
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-title">Produtos com Stock Baixo</div>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Produto</th>
                                            <th scope="col">Qtd.</th>
                                            <th scope="col">Acção</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Loop through $lista_produtos_stock_baixo from your controller --}}
                                        @forelse ($lista_produtos_stock_baixo ?? [] as $produto)
                                            <tr>
                                                <td>{{ $produto->descricao }}</td>
                                                <td><span class="badge badge-danger">{{ $produto->quantidade }}</span></td>
                                                <td><a href="#" class="btn btn-sm btn-primary">Ver</a></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">Nenhum produto com stock baixo.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-title">Actividade Recente</div>
                            <div class="ul-activity">
                                {{-- Loop through $atividades_recentes from your controller --}}
                                {{-- Example: $atividade->icon = 'i-Add-User', $atividade->descricao = 'Novo funcionário adicionado' --}}
                                @forelse ($atividades_recentes ?? [] as $atividade)
                                    <div class="ul-activity-item">
                                        <div class="ul-activity-img"><i class="{{ $atividade->icon ?? 'i-Info-Window' }}"></i></div>
                                        <div class="ul-activity-data">
                                            <p class="font-weight-bold mb-1">{{ $atividade->descricao }}</p>
                                            <p class="text-muted text-small">{{ $atividade->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center">Nenhuma actividade recente para mostrar.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- ============ Body content End ============= -->
        
        <div class="sidebar-overlay open"></div>
        <!-- Footer Start -->
        @include('components.footer')
        <!-- fotter end -->
    </div>
</div>

@endsection


@section('scripts')

<script src="{{ asset('dist-assets/js/plugins/echarts.min.js')}}"></script>  
<script src="{{ asset('dist-assets/js/scripts/echart.options.min.js')}}"></script>  
<script src="{{ asset('dist-assets/js/plugins/apexcharts.min.js')}}"></script>  
<script src="{{ asset('js/dashboard.js')}}"></script>  

<script>
        $(document).ready(function() {

            hideLoader();
            $("#dashboard_li").addClass("nav-item-active")
            $("#dashboard_link").addClass("nav-item-active-text")

            // You will need to update your dashboard.js or add script here
            // to feed the correct data into echartBar and echartPie

        });

</script>
@endsection

