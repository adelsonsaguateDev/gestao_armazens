@extends('layouts.main')

@section('title', 'Sistema de Gestão de Armazens')


@section('content')

<div class="app-admin-wrap layout-sidebar-vertical sidebar-full">
    
    @include('components.sidebar')

    <div class="switch-overlay"></div>
    <div class="main-content-wrap mobile-menu-content bg-off-white m-0">
        @include('components.header')
        
        <!-- ============ Body content start ============= -->
        <div class="main-content pt-4">
            <div class="breadcrumb">
                <h1 class="mr-2">Dashboard</h1>
                <ul>
                    <li><a href="#">Relatórios</a></li>
                </ul>
            </div>
            <div class="separator-breadcrumb border-top"></div>
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <!-- CARD ICON-->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card card-icon mb-4">
                                <div class="card-body text-center"><i class="i-Data-Upload"></i>
                                    <p class="text-muted mt-2 mb-2">Total Produtos</p>
                                    <p class="text-primary text-24 line-height-1 m-0">{{ $total_produtos ??  0}}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-6">
                            <div class="card card-icon mb-4">
                                <div class="card-body text-center"><i class="i-Add-User"></i>
                                    <p class="text-muted mt-2 mb-2">Total Funcionários</p>
                                    <p class="text-primary text-24 line-height-1 m-0">{{ $total_funcionarios ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-6">
                            <div class="card card-icon mb-4">
                                <div class="card-body text-center"><i class="i-Loading-3"></i>
                                    <p class="text-muted mt-2 mb-2">Total Requisições</p>
                                    <p class="text-primary text-24 line-height-1 m-0"> {{ $total_requisicoes ?? 0 }} </p>
                                </div>
                            </div>
                        </div>
                        
                        
                    </div>
                </div>
            
                <div class="col-lg-6 col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-title">Requisições</div>
                            <div id="echartBar" style="height: 400px;"></div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-title">Requisições</div>
                            <div id="echartPie" style="height: 400px;"></div>
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

@endsection


@section('scripts')

<script src="{{ asset('dist-assets/js/plugins/echarts.min.js')}}"></script>  
<script src="{{ asset('dist-assets/js/scripts/echart.options.min.js')}}"></script>  
{{-- <script src="{{ asset('dist-assets/js/scripts/dashboard.v1.script.min.js')}}"></script>   --}}
{{-- <script src="{{ asset('dist-assets/js/scripts/dashboard.v2.script.min.js')}}"></script>   --}}
<script src="{{ asset('dist-assets/js/plugins/apexcharts.min.js')}}"></script>  
<script src="{{ asset('js/dashboard.js')}}"></script>  

<script>
        $(document).ready(function() {

            hideLoader();
            $("#dashboard_li").addClass("nav-item-active")
            $("#dashboard_link").addClass("nav-item-active-text")





















        });

</script>
@endsection

