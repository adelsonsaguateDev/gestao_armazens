<header class="main-header bg-white d-flex justify-content-between p-2">
    <div class="header-toggle">
        <div class="menu-toggle mobile-menu-icon">
            <div></div>
            <div></div>
            <div></div>
        </div>

    </div>
    <div class="header-part-right">
        <!-- Full screen toggle-->
         <i class="i-Full-Screen header-icon d-none d-sm-inline-block" data-fullscreen=""></i>
        <div class="dropdown">
            <div class="user col align-self-end">
                <img src="{{asset('./img/utilizador.png')}}" id="userDropdown" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <div class="dropdown-header">
                        <i class="i-Lock-User mr-1"></i> {{auth()->user()->name}}
                    </div>
                    <a class="dropdown-item" href="{{ route('logout') }}">Terminar sessão</a>
                </div>
            </div>
        </div>
    </div>
</header>
