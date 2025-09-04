@php
    session()->start();
@endphp

<style>
    /* Hide the icon logo by default */
    .icon-logo {
        display: none;
    }

    /* When sidebar is compact, hide the full logo */
    .sidebar-compact .full-logo {
        display: none;
    }

    /* When sidebar is compact, show the icon logo */
    .sidebar-compact .icon-logo {
        display: block !important;
    }

    .Ul_li--hover.active::before {
        left: 0px;
        z-index: -1;
    }
</style>

<div class="sidebar-panel">
    <div class="gull-brand pr-3 text-center mt-4 mb-2 d-flex justify-content-center align-items-center">

        <!-- Full Logo -->
        <img src="{{ asset('dist-assets/images/logo.png') }}" alt="Logo Fenomenal Comercial" class="full-logo"
            style="width: 180px; height: auto;">

        <!-- Icon Logo for Compact Sidebar -->
        <img src="{{ asset('dist-assets/images/logo.png') }}" alt="Icon Logo Fenomenal Comercial" class="icon-logo"
            style="width: 40px; height: auto; display: none;">

        {{-- 
        <!-- Full Logo -->
        <svg class="full-logo" width="180" height="40" viewBox="0 0 340 60" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:#0056b3;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#007bff;stop-opacity:1" />
                </linearGradient>
            </defs>
            <g transform="translate(5, 5)">
                <path d="M20 0 L0 0 L0 50 L20 50 L20 30 L10 30 L10 20 L20 20 Z" fill="url(#grad1)"/>
                <path d="M25 0 L45 0 Q50 0 50 5 L50 45 Q50 50 45 50 L25 50 Z M35 10 A15 15 0 0 0 35 40 A15 15 0 0 0 35 10" fill="url(#grad1)"/>
            </g>
            <text x="65" y="40" font-family="'Segoe UI', 'Roboto', 'Helvetica Neue', sans-serif" font-size="28" font-weight="600" fill="#ffffff">
                Fenomenal <tspan font-weight="400">Comercial</tspan>
            </text>
        </svg>

        <!-- Icon Logo for Compact Sidebar -->
        <svg class="icon-logo" width="40" height="40" viewBox="0 0 55 55" xmlns="http://www.w3.org/2000/svg" style="display: none;">
            <defs>
                <linearGradient id="grad1_icon" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:#0056b3;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#007bff;stop-opacity:1" />
                </linearGradient>
            </defs>
            <g transform="translate(5, 5)">
                <path d="M20 0 L0 0 L0 50 L20 50 L20 30 L10 30 L10 20 L20 20 Z" fill="url(#grad1_icon)"/>
                <path d="M25 0 L45 0 Q50 0 50 5 L50 45 Q50 50 45 50 L25 50 Z M35 10 A15 15 0 0 0 35 40 A15 15 0 0 0 35 10" fill="url(#grad1_icon)"/>
            </g>
        </svg>
        --}}

        <div class="sidebar-compact-switch ml-auto"><span></span></div>
    </div>
    <!--  user -->
    <div class="scroll-nav ps ps--active-y" data-perfect-scrollbar="data-perfect-scrollbar"
        data-suppress-scroll-x="true">
        <div class="side-nav">
            <div class="main-menu">
                <ul class="metismenu" id="menu">
                    <li class="Ul_li--hover {{ request()->is('home') ? 'active' : '' }}"><a href="{{ route('pagina_inicial') }}"><i
                                class="i-Bar-Chart text-20 mr-2"></i><span
                                class="item-name text-15">Dashboard</span></a></li>

                    <li class="Ul_li--hover {{ request()->is('produto*') ? 'active' : '' }}"><a href="{{ route('produto.list') }}"><i
                                class="i-Library text-20 mr-2"></i><span class="item-name text-15">Produtos</span></a>
                    </li>
                    <li class="Ul_li--hover {{ request()->is('entrada*') ? 'active' : '' }}"><a href="{{ route('entrada.list') }}"><i
                                class="i-Full-Cart text-20 mr-2"></i><span class="item-name text-15">Entradas</span></a>
                    </li>
                    <li class="Ul_li--hover {{ request()->is('saida*') ? 'active' : '' }}"><a href="{{ route('saida.list') }}"><i class="i-Shop-4 text-20 mr-2"></i><span
                                class="item-name text-15">Vendas</span></a></li>
                    <li class="Ul_li--hover"><a href="#"><i class="i-Financial text-20 mr-2"></i><span
                                class="item-name text-15">Pagamentos</span></a></li>
                    <li class="Ul_li--hover {{ request()->is('requisicao*') ? 'active' : '' }}"><a href="{{ route('requisicao.list') }}"><i
                                class="i-Remove-Cart text-20 mr-2"></i><span
                                class="item-name text-15">Requisições</span></a></li>
                    @if (session('permissao_nome') == 'admin' || session('permissao_nome') == 'gestor')
                        <li class="Ul_li--hover {{ (request()->is('utilizador*') || request()->is('relatorio*') || request()->is('cliente*')) ? 'active' : '' }}">
                            <a class="has-arrow" href="#"><i class="i-Gears text-20 mr-2"></i><span
                                    class="item-name text-15">Administração</span></a>
                            <ul class="mm-collapse">
                                <li class="Ul_li--hover {{ request()->is('cliente*') ? 'active' : '' }}"><a href="{{ route('cliente.list') }}"><i
                                            class="fas fa-users text-20 mr-2"></i><span
                                            class="item-name text-15">Clientes</span></a></li>
                                <li class="Ul_li--hover {{ request()->is('utilizador*') ? 'active' : '' }}"><a href="{{ route('utilizador.list') }}"><i
                                            class="i-Administrator text-20 mr-2"></i><span
                                            class="item-name text-15">Utilizadores</span></a></li>
                                <li class="Ul_li--hover {{ request()->is('relatorio*') ? 'active' : '' }}"><a href="{{ route('relatorio.list') }}"><i
                                            class="i-Line-Chart-4 text-20 mr-2"></i><span
                                            class="item-name text-15">Relatórios</span></a></li>
                            </ul>
                        </li>
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
    </div>
    <!--  side-nav-close -->
</div>
