@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-7 intro-section d-none d-sm-flex">
            <div class="intro-content-wrapper">
                <div class="mb-5">
                    <svg width="250" height="200" viewBox="0 0 250 200" xmlns="http://www.w3.org/2000/svg">
                        <style>
                            .box-part { fill: rgba(255,255,255,0.2); stroke: rgba(255,255,255,0.8); stroke-width: 1.5; stroke-linejoin: round; }
                            .scanner-body { fill: #b0c4de; } /* Light Steel Blue */
                            .scanner-dark { fill: #465a70; }
                            .scanline {
                                stroke: #00aeff;
                                stroke-width: 2.5;
                                stroke-linecap: round;
                                opacity: 0;
                                animation: scan-the-box 4s infinite ease-out;
                                animation-delay: 0.5s;
                            }
                            .scanner-g {
                                animation: move-the-scanner 4s infinite ease-in-out;
                            }

                            @keyframes move-the-scanner {
                                0%   { transform: translate(180px, -20px) rotate(20deg); opacity: 0; }
                                20%  { transform: translate(130px, 20px) rotate(-10deg); opacity: 1; }
                                80%  { transform: translate(130px, 20px) rotate(-10deg); opacity: 1; }
                                100% { transform: translate(110px, 50px) rotate(-20deg); opacity: 0; }
                            }

                            @keyframes scan-the-box {
                                0%   { opacity: 0; }
                                20%  { opacity: 0; } /* Wait for scanner to get in position */
                                30%  { opacity: 1; transform: translate(0, 0); }
                                70%  { opacity: 1; transform: translate(0, 48px); }
                                80%  { opacity: 0; transform: translate(0, 48px); }
                                100% { opacity: 0; }
                            }
                        </style>

                        <!-- Scanner -->
                        <g class="scanner-g">
                            <path class="scanner-body" d="M0 15 Q0 0 15 0 L65 0 Q80 0 80 15 L80 35 Q80 50 65 50 L15 50 Q0 50 0 35 Z" />
                            <path class="scanner-dark" d="M10 10 L70 10 L70 25 L10 25 Z" />
                            <rect class="scanner-body" x="25" y="50" width="30" height="15" rx="5" />
                        </g>

                        <!-- Cardboard Box -->
                        <g transform="translate(40, 100)">
                            <!-- Main Body -->
                            <path class="box-part" d="M0 0 L50 25 L50 75 L0 50 Z" />
                            <path class="box-part" d="M50 25 L100 0 L100 50 L50 75 Z" />
                            <path class="box-part" d="M0 0 L50 -25 L100 0 L50 25 Z" />
                            <!-- Top Flaps -->
                            <path class="box-part" d="M5, 2.5 L50, -22.5 L95, 2.5" /> <!-- Back flap slightly open -->
                            <path class="box-part" d="M50, 27.5 L95, 5 L95, 2.5" /> <!-- Right flap slightly open -->
                            <!-- Scanline on top of the box -->
                            <g class="scanline">
                                <line x1="2" y1="-2" x2="50" y2="23" />
                                <line x1="50" y1="23" x2="98" y2="-2" />
                            </g>
                        </g>
                    </svg>
                </div>
                <h1 class="intro-title">Gestão de Armazém Inteligente</h1>
                <p class="intro-text">Otimize seu estoque, controle suas vendas e impulsione seu negócio. Tudo em um só lugar.</p>
                <p class="copyright-text">&copy; {{ date('Y') }} Fenomenal Comercial. Todos os direitos reservados.</p>
            </div>
        </div>
        <div class="col-sm-5 form-section">
            <div class="login-wrapper">
                <div class="logo-container mb-4 text-center">
                    <img src="{{ asset('dist-assets/images/logo.png') }}" alt="Logo Fenomenal Comercial" style="width: 250px;">
                </div>
                <h3 class="text-center">Bem-vindo de volta!</h3>
                <p class="text-center text-muted mb-4">Faça login para continuar</p>
                <form action="{{ route('autenticar') }}" id="frm_login" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="username" class="sr-only">Utilizador</label>
                        <input id="username" name="username" type="text"
                            class="form-control @error('username') is-invalid @enderror"
                            value="{{ old('username') }}" required autocomplete="username" autofocus
                            placeholder="Nome do Utilizador">
                        @error('username')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group mb-4">
                        <label for="password" class="sr-only">Senha</label>
                        <div class="position-relative">
                            <input id="password" name="password" type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                required autocomplete="current-password"
                                placeholder="Digite a senha">
                            <div class="show-password" onclick="togglePasswordVisibility()">
                                <i class="fa fa-eye-slash" aria-hidden="true"></i>
                            </div>
                        </div>
                        @error('password')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn login-btn" id="entrar">
                            <span class="ent">Entrar</span>
                            <span class="spinner-border spinner-border-sm spin" role="status" aria-hidden="true"
                                hidden></span>
                            <span class="carregando" hidden>Carregando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const icon = document.querySelector('.show-password i');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    }
}
</script>
@endsection
