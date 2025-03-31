@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-7 intro-section">
                <div class="intro-content-wrapper" style="background-color:#3197ef; border-radius: 10px; ">
                </div>
                <div class="intro-section-footer"
                    style="background-color:#3197ef; border-radius: 10px; margin-top:10px; margin-bottom:10px">
                </div>
            </div>
            <div class="col-sm-5 form-section">
                <div class="login-wrapper">
                    <div class="mb-3" style="text-align: center;">


                    </div>
                    <h3 style="text-align: center;">Gestão de Armazéns</h3>
                    <form action="{{ route('autenticar') }}" id="frm_login" method="POST">
                        @csrf
                        <div class="row">
                            <div class="form-group mb-3 col-md-12">
                                <label for="username" class="sr-only" style="font-weight: bold;">Utilizador:</label>
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
                            <div class="form-group">
                                <label for="password" class="sr-only" style="font-weight: bold;">Senha:</label>
                                <div class="position-relative" id="show_hide_password">
                                    <div class="input-group" id="show_hide_password">
                                        <input id="password" name="password" type="password"
                                            class="form-control @error('password') is-invalid @enderror" name="password"
                                            value="{{ old('password') }}" required autocomplete="current-password"
                                            placeholder="Digite a senha">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <div class="show-password">
                                            <a href=""><i class="fa fa-eye-slash" aria-hidden="false"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12" style="text-align: right;">
                                <button type="submit" class="btn login-btn" id="entrar">
                                    <span class="ent">Entrar</span>
                                    <span class="spinner-border spinner-border-sm spin" role="status" aria-hidden="true"
                                        hidden></span>
                                    <span class="carregando" hidden>Carregando...</span>
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
