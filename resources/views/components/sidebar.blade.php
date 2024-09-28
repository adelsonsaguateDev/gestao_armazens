@php
    session()->start();
@endphp

<div class="sidebar-panel">
    <div class="gull-brand pr-3 text-center mt-4 mb-2 d-flex justify-content-center align-items-center"><img class="pl-3" src="{{ asset('dist-assets/images/logo.png')}}" alt="alt" />
        <span style="font-size: 0.7rem" class=" item-name text-20 text-primary font-weight-700">GESTÃO ARMAZENS</span> 
        <div class="sidebar-compact-switch ml-auto"><span></span></div>
    </div>
    <!--  user -->
    <div class="scroll-nav ps ps--active-y" data-perfect-scrollbar="data-perfect-scrollbar" data-suppress-scroll-x="true">
        <div class="side-nav">
            <div class="main-menu">
                <ul class="metismenu" id="menu">
                    <li id="dashboard_li" class="Ul_li--hover"><a id="dashboard_link" class="has-arrow" href="{{ route('pagina_inicial')}}"><i class="i-Bar-Chart text-20 mr-2 text-muted"></i><span class="item-name text-15 text-muted">Dashboard</span></a></li>
                    <li class="Ul_li--hover"><a class="has-arrow" href="{{ route('produto.list') }}"><i class="i-Library text-20 mr-2 text-muted"></i><span class="item-name text-15 text-muted">Produtos</span></a></li>
                    <li class="Ul_li--hover"><a class="has-arrow" href="{{ route('requisicao.list') }}"><i class="i-Remove-Cart text-20 mr-2 text-muted"></i><span class="item-name text-15 text-muted">Requisições</span></a></li>
                    @if(session('permissao_nome') == 'admin' || session('permissao_nome') == 'gestor')
                    <li class="Ul_li--hover"><a class="has-arrow" href="{{ route('utilizador.list') }}" ><i class="i-Administrator text-20 mr-2 text-muted"></i><span class="item-name text-15 text-muted">Funcionarios</span></a></li>
                    <li class="Ul_li--hover"><a class="has-arrow" href="{{ route('relatorio.list') }}"><i class="i-Line-Chart-4 text-20 mr-2 text-muted"></i><span class="item-name text-15 text-muted">Relatórios</span></a></li>
                    @endif
                    
                </ul>
            </div>
        </div>
        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
        </div>
        <div class="ps__rail-y" style="top: 0px; height: 404px; right: 0px;">
            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 325px;"></div>
        </div>
        <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
            <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
        </div>
        <div class="ps__rail-y" style="top: 0px; height: 404px; right: 0px;">
            <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 325px;"></div>
        </div>
    </div>
    <!--  side-nav-close -->
</div>