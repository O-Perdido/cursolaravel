@extends('layouts.main')

@section('title', 'Login')

@section('content')

    <!-- Nome do sistema -->
    <div class="text-center">
        <h1 class="">Sistema de Integração e Gestão de Estágios</h1>
    </div>
    <hr class="my-4">
    <div class="container mt-5">
        <div class="row justify-content-center g-4">
            <!-- Coluna: Login -->
            <div class="col-12 col-md-8 col-lg-5">
                <h3>Realizar Login</h3>
                <div class="card card-body bg-light">
                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        @method('POST')
                        <div class="form-group mb-3 mt-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="password">Senha</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" style="border: none;"
                                        onclick="togglePasswordVisibility()">
                                        <img style="width: 25px; height: 25px;" src="{{ asset('images/eye_visible.png') }}"
                                            id="password-icon" alt="Mostrar Senha">
                                    </button>
                                </div>
                            </div>
                        </div>
                        <script>
                            function togglePasswordVisibility() {
                                var passwordField = document.getElementById('password');
                                var passwordIcon = document.getElementById('password-icon');
                                if (passwordField.type === 'password') {
                                    passwordField.type = 'text';
                                    passwordIcon.src = '{{ asset('images/eye_not_visible.png') }}';
                                } else {
                                    passwordField.type = 'password';
                                    passwordIcon.src = '{{ asset('images/eye_visible.png') }}';
                                }
                            }
                        </script>
                        <div class="form-group form-check mb-3 mt-3 d-flex justify-content-between align-items-center">
                            <a href="{{ route('password.request') }}" class="small">Redefinir senha</a>
                            <span class="float-right" style="font-size: 0.8em"><a
                                    href="https://api.whatsapp.com/send?phone=5548991468761&text=Ol%C3%A1%21%20Tentei%20realizar%20o%20login%20e%20esqueci%20minha%20senha%2C%20tem%20como%20me%20ajudar%3F"
                                    target="_blank">Precisa de ajuda?</a></span>
                        </div>
                        @if(session('error'))
                            <div class="alert alert-danger mt-2">{{ session('error') }}</div>
                        @endif
                        <button type="submit" class="btn btn-primary" id="save_button">Logar</button>
                    </form>
                </div>
            </div>

            <!-- Coluna: PWA Install (aparece ao lado no desktop e abaixo no mobile) -->
            <div class="col-12 col-md-8 col-lg-5">
                <h3>Instalar o SIGEBR</h3>
                <!-- PWA Install Card (mostrado quando PWA for instalável) -->
                <div class="card border-success shadow-sm" id="pwa-login-card" style="display: none; animation: slideInUp 0.5s ease-out;
                                                                                   background: linear-gradient(135deg, #198754 0%, #20c997 100%);
                                                                                   color: #fff;
                                                                                   border-radius: 10px;
                                                                                   overflow: hidden;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-download" style="font-size: 2rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-2">Instale o SIGEBR no seu celular ou computador</h5>

                                <!-- Bloco: botão padrão (Android/desktop) -->
                                <div id="pwa-install-block" style="display:none">
                                    <p class="mb-2" style="font-size: 0.9rem; line-height: 1.3;">
                                        Abre em tela cheia e carrega mais rápido.
                                    </p>
                                    <button id="install-app-login" class="btn btn-light fw-bold"
                                        style="border-radius: 20px; box-shadow: 0 3px 10px rgba(0,0,0,0.25); padding:6px 16px;">
                                        <i class="fas fa-plus-square"></i> Instalar Agora
                                    </button>
                                </div>

                                <!-- Bloco: instruções iOS (Safari) -->
                                <div id="pwa-ios-instructions" style="display:none">
                                    <p class="mb-2" style="font-size: 0.9rem; line-height: 1.3;">
                                        No iPhone/iPad, instale pelo Safari:
                                    </p>
                                    <ol class="mb-2" style="font-size: 0.85rem; padding-left: 1.1rem; line-height:1.3;">
                                        <li>Toque no botão Compartilhar (quadrado com seta para cima).</li>
                                        <li>Escolha “Adicionar à Tela de Início”.</li>
                                        <li>Confirme em “Adicionar”.</li>
                                    </ol>
                                    <div id="ios-browser-tip" class="small" style="display:none; opacity:0.9;">
                                        Dica: se estiver usando Chrome/Edge no iOS, abra este site no Safari para instalar.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script>
        (function () {
            const pwaCard = document.getElementById('pwa-login-card');
            const installBlock = document.getElementById('pwa-install-block');
            const iosBlock = document.getElementById('pwa-ios-instructions');
            const iosTip = document.getElementById('ios-browser-tip');

            const ua = navigator.userAgent || navigator.vendor || window.opera;
            const isIOS = /iphone|ipad|ipod/i.test(ua);
            const isSafari = /^((?!chrome|android).)*safari/i.test(ua);
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;

            // Se já está instalado, não mostra o card
            if (isStandalone) return;

            // iOS: exibe instruções (instalação via Safari), oculta o botão padrão
            // Em outras plataformas, o layout principal controla a exibição via beforeinstallprompt
            if (isIOS) {
                if (pwaCard) pwaCard.style.display = '';
                if (iosBlock) iosBlock.style.display = '';
                if (installBlock) installBlock.style.display = 'none';
                if (!isSafari && iosTip) iosTip.style.display = '';
            }
        })();
    </script>

@endsection