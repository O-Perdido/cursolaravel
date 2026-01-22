<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Meu Site')</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#198754">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="SIGEBR - EBCP">
    <meta name="description" content="Sistema de Gestão de Estágios Brasileiros da EBCP">

    <!-- PWA Icons -->
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/icons/icon-192x192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/icons/icon-192x192.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">-->
    <script src="https://kit.fontawesome.com/464848a8f8.js" crossorigin="anonymous"></script>
    {{-- Carregar o JavaScript --}}
    <script src="{{ asset('build/assets/app-DpAvhFZP.js') }}" defer></script>

    <style>
        /* Estilos específicos para layout com sidebar (apenas desktop) */
        body {
            transition: margin-left 0.4s ease;
        }

        /* Navbar e Footer ajustados apenas em desktop com sidebar */
        @media (min-width: 992px) {
            body.has-sidebar .navbar {
                margin-left: 270px;
                width: calc(100% - 270px);
                transition: margin-left 0.4s ease, width 0.4s ease;
            }

            body.has-sidebar.sidebar-collapsed .navbar {
                margin-left: 85px;
                width: calc(100% - 85px);
            }

            body.has-sidebar footer {
                margin-left: 270px;
                width: calc(100% - 270px);
                transition: margin-left 0.4s ease, width 0.4s ease;
            }

            body.has-sidebar.sidebar-collapsed footer {
                margin-left: 85px;
                width: calc(100% - 85px);
            }

            body.has-sidebar main {
                margin-left: 0;
                padding-left: 15px;
                padding-right: 15px;
            }
        }

        /* Navbar e Footer sempre em 100% da largura */
        .navbar {
            width: 100%;
            position: relative;
            z-index: 1020;
        }

        footer {
            width: 100%;
            position: relative;
        }

        /* Mobile - layout normal sem sidebar */
        @media (max-width: 991.98px) {
            body.has-sidebar {
                margin-left: 0 !important;
            }

            body.has-sidebar main.container {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }
    </style>
</head>

<body style="font-family: Gothic, sans-serif;">
    <div>
        @auth
            <!-- Sidebar for authenticated users -->
            @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                <x-sidebar />
            @endif
        @endauth
    </div>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm text-center">
        <div class="container-fluid align-items-center px-2 px-md-3">
            <div class="d-flex align-items-center flex-grow-1">
                <a href="/" class="navbar-brand d-flex align-items-center" style="margin: 0;">
                    <img src="{{ asset('images/logo_branca_sem_fundo.png') }}" alt="Logo" height="45"
                        class="d-inline-block align-center">
                    <span class="d-none d-sm-inline" style="margin-left: 15px;">
                        @guest
                            <img src="{{ asset('images/sige_logo_branco.png') }}" alt="" height="50"
                                style="margin-bottom: 5px;">
                        @endguest
                        @auth
                            <img src="{{ asset('images/sige_logo_branco.png') }}" alt="" height="50"
                                style="margin-bottom: 5px;">
                        @endauth
                    </span>
                </a>
            </div>
            @guest
                <!-- TITULO COM O NOME COMPLETO DO SISTEMA (só em telas grandes) -->
                <div class="d-none d-lg-flex align-items-center justify-content-between flex-grow-1">
                    <h1 class="text-white text-center" style="font-family: Asthoria, sans-serif;">Sistema de Gestão de
                        Estágios</h1>
                    <div class="d-flex gap-2">
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm" style="font-weight: 500;">
                            <i class="fas fa-sign-in-alt me-1"></i>Acesso Geral
                        </a>
                    </div>
                </div>
                <!-- Botão de acesso para mobile -->
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm d-lg-none" style="font-weight: 500;">
                    <i class="fas fa-sign-in-alt me-1"></i>Acesso
                </a>
            @endguest
            @auth
                <!-- SE O USUARIO LOGADO FOR EMPRESA MOSTRA TAMBÉM O NOME COMPLETO DO SISTEMA (só em telas grandes) -->
                @if (Auth::user()->nivel == 'empresa' || Auth::user()->nivel == 'estagiario')
                    <div class="d-none d-lg-flex align-items-center justify-content-between gap-4">
                        <h1 class="text-white text-center mb-0" style="font-family: Asthoria, sans-serif; font-size:2rem;">
                            Sistema de Gestão de Estágios
                        </h1>
                        <div class="d-flex align-items-center gap-3">
                            <a href="{{ route('landing') }}"
                                class="btn btn-outline-light btn-sm d-flex align-items-center gap-2 shadow-sm"
                                style="font-weight: 500;">
                                <i class="fas fa-home"></i>
                                INÍCIO
                            </a>
                            <span class="text-white-50 small" style="font-size: 1rem;">
                                {{ Auth::user()->name }}
                            </span>
                        </div>
                    </div>
                    <!-- Botão INÍCIO para mobile ao lado do hambúrguer -->
                    <a href="{{ route('landing') }}"
                        class="btn btn-outline-light btn-sm d-lg-none me-2 d-flex align-items-center gap-2"
                        style="font-weight: 500;">
                        <i class="fas fa-home"></i>
                        Início
                    </a>
                @endif
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center" style="vertical-align: middle;">
                        @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')

                            <li class="nav-item dropdown" style="vertical-align: middle;">
                                <a class="nav-link" href="{{ route('empresas.index') }}" id="navbarDropdown" role="button">
                                    <i class="fa-solid fa-briefcase fa-2x"></i><br>
                                    <small>Concedentes</small>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('empresas.create') }}">Nova Concedente</a>
                                    </li>
                                </ul>
                            </li>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const dropdown = document.getElementById('navbarDropdown');
                                    const dropdownMenu = dropdown.nextElementSibling;

                                    dropdown.addEventListener('mouseenter', function () {
                                        if (dropdownMenu) {
                                            dropdownMenu.classList.add('show');
                                        }
                                    });

                                    dropdown.addEventListener('mouseleave', function () {
                                        if (dropdownMenu) {
                                            dropdownMenu.classList.remove('show');
                                        }
                                    });

                                    dropdownMenu.addEventListener('mouseenter', function () {
                                        dropdownMenu.classList.add('show');
                                    });

                                    dropdownMenu.addEventListener('mouseleave', function () {
                                        dropdownMenu.classList.remove('show');
                                    });
                                });
                            </script>

                            <li class="nav-item dropdown" style="vertical-align: middle;">
                                <a class="nav-link" href="{{ route('escolas.index') }}" id="navbarDropdownEscolas"
                                    role="button">
                                    <i class="fa-solid fa-book fa-2x"></i><br>
                                    <small>Instituições</small>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownEscolas">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('escolas.create') }}">Nova Instituição</a>
                                    </li>
                                </ul>
                            </li>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const dropdownEscolas = document.getElementById('navbarDropdownEscolas');
                                    const dropdownMenuEscolas = dropdownEscolas.nextElementSibling;

                                    dropdownEscolas.addEventListener('mouseenter', function () {
                                        if (dropdownMenuEscolas) {
                                            dropdownMenuEscolas.classList.add('show');
                                        }
                                    });

                                    dropdownEscolas.addEventListener('mouseleave', function () {
                                        if (dropdownMenuEscolas) {
                                            dropdownMenuEscolas.classList.remove('show');
                                        }
                                    });

                                    dropdownMenuEscolas.addEventListener('mouseenter', function () {
                                        dropdownMenuEscolas.classList.add('show');
                                    });

                                    dropdownMenuEscolas.addEventListener('mouseleave', function () {
                                        dropdownMenuEscolas.classList.remove('show');
                                    });
                                });
                            </script>
                            <li class="nav-item dropdown" style="vertical-align: middle;">
                                <a class="nav-link" href="{{ route('estagiarios.index') }}" id="navbarDropdownEstagiarios"
                                    role="button">
                                    <i class="fa-solid fa-user-graduate fa-2x"></i><br>
                                    <small>Estagiários</small>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownEstagiarios">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('estagiarios.create') }}">Novo
                                            Estagiário</a>
                                    </li>
                                </ul>
                            </li>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const dropdownEstagiarios = document.getElementById('navbarDropdownEstagiarios');
                                    const dropdownMenuEstagiarios = dropdownEstagiarios.nextElementSibling;

                                    dropdownEstagiarios.addEventListener('mouseenter', function () {
                                        if (dropdownMenuEstagiarios) {
                                            dropdownMenuEstagiarios.classList.add('show');
                                        }
                                    });

                                    dropdownEstagiarios.addEventListener('mouseleave', function () {
                                        if (dropdownMenuEstagiarios) {
                                            dropdownMenuEstagiarios.classList.remove('show');
                                        }
                                    });

                                    dropdownMenuEstagiarios.addEventListener('mouseenter', function () {
                                        dropdownMenuEstagiarios.classList.add('show');
                                    });

                                    dropdownMenuEstagiarios.addEventListener('mouseleave', function () {
                                        dropdownMenuEstagiarios.classList.remove('show');
                                    });
                                });
                            </script>
                            <li class="nav-item dropdown" style="vertical-align: middle;">
                                <a class="nav-link" href="{{ route('supervisores.index') }}" id="navbarDropdownSupervisores"
                                    role="button">
                                    <i class="fa-solid fa-user-tie fa-2x"></i><br>
                                    <small>Supervisores</small>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownSupervisores">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('supervisor.create') }}">Novo Supervisor</a>
                                    </li>
                                </ul>
                            </li>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const dropdownSupervisores = document.getElementById('navbarDropdownSupervisores');
                                    const dropdownMenuSupervisores = dropdownSupervisores.nextElementSibling;

                                    dropdownSupervisores.addEventListener('mouseenter', function () {
                                        if (dropdownMenuSupervisores) {
                                            dropdownMenuSupervisores.classList.add('show');
                                        }
                                    });

                                    dropdownSupervisores.addEventListener('mouseleave', function () {
                                        if (dropdownMenuSupervisores) {
                                            dropdownMenuSupervisores.classList.remove('show');
                                        }
                                    });

                                    dropdownMenuSupervisores.addEventListener('mouseenter', function () {
                                        dropdownMenuSupervisores.classList.add('show');
                                    });

                                    dropdownMenuSupervisores.addEventListener('mouseleave', function () {
                                        dropdownMenuSupervisores.classList.remove('show');
                                    });
                                });
                            </script>
                            <li class="nav-item dropdown" style="vertical-align: middle;">
                                <a class="nav-link" href="{{ route('vagas.index') }}" id="navbarDropdownVagas" role="button">
                                    <span class="position-relative d-inline-block">
                                        <i class="fa-solid fa-clipboard-list fa-2x"></i>
                                        @if(isset($vagasAbertasCount) && $vagasAbertasCount > 0)
                                            <span
                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                                style="font-size:0.7em;">
                                                {{ $vagasAbertasCount }}
                                            </span>
                                        @endif
                                    </span><br>
                                    <small>Vagas</small>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownVagas">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('vagas.create') }}">Nova Vaga</a>
                                    </li>
                                </ul>
                            </li>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const dropdownVagas = document.getElementById('navbarDropdownVagas');
                                    const dropdownMenuVagas = dropdownVagas.nextElementSibling;

                                    dropdownVagas.addEventListener('mouseenter', function () {
                                        if (dropdownMenuVagas) {
                                            dropdownMenuVagas.classList.add('show');
                                        }
                                    });

                                    dropdownVagas.addEventListener('mouseleave', function () {
                                        if (dropdownMenuVagas) {
                                            dropdownMenuVagas.classList.remove('show');
                                        }
                                    });

                                    dropdownMenuVagas.addEventListener('mouseenter', function () {
                                        dropdownMenuVagas.classList.add('show');
                                    });

                                    dropdownMenuVagas.addEventListener('mouseleave', function () {
                                        dropdownMenuVagas.classList.remove('show');
                                    });
                                });
                            </script>
                            <li class="nav-item dropdown" style="vertical-align: middle;">
                                <a class="nav-link" href="{{ route('processos-seletivos.index') }}" id="navbarDropdownProcessos"
                                    role="button">
                                    <i class="fa-solid fa-graduation-cap fa-2x"></i><br>
                                    <small>Processos Seletivos</small>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownProcessos">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('processos-seletivos.create') }}">Novo
                                            Processo</a>
                                    </li>
                                </ul>
                            </li>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const dropdownProcessos = document.getElementById('navbarDropdownProcessos');
                                    const dropdownMenuProcessos = dropdownProcessos.nextElementSibling;

                                    dropdownProcessos.addEventListener('mouseenter', function () {
                                        if (dropdownMenuProcessos) {
                                            dropdownMenuProcessos.classList.add('show');
                                        }
                                    });

                                    dropdownProcessos.addEventListener('mouseleave', function () {
                                        if (dropdownMenuProcessos) {
                                            dropdownMenuProcessos.classList.remove('show');
                                        }
                                    });

                                    dropdownMenuProcessos.addEventListener('mouseenter', function () {
                                        dropdownMenuProcessos.classList.add('show');
                                    });

                                    dropdownMenuProcessos.addEventListener('mouseleave', function () {
                                        dropdownMenuProcessos.classList.remove('show');
                                    });
                                });
                            </script>
                            <li class="nav-item dropdown" style="vertical-align: middle;">
                                <a class="nav-link" href="{{ route('termos.index') }}" id="navbarDropdownTermos" role="button">
                                    <i class="fa-solid fa-file-contract fa-2x"></i><br>
                                    <small>Termos</small>
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownTermos">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('termos.create') }}">Novo TCE</a>
                                    </li>
                                </ul>
                            </li>
                            <script>
                                document.addEventListener('DOMContentLoaded', function () {
                                    const dropdownTermos = document.getElementById('navbarDropdownTermos');
                                    const dropdownMenuTermos = dropdownTermos.nextElementSibling;

                                    dropdownTermos.addEventListener('mouseenter', function () {
                                        if (dropdownMenuTermos) {
                                            dropdownMenuTermos.classList.add('show');
                                        }
                                    });

                                    dropdownTermos.addEventListener('mouseleave', function () {
                                        if (dropdownMenuTermos) {
                                            dropdownMenuTermos.classList.remove('show');
                                        }
                                    });

                                    dropdownMenuTermos.addEventListener('mouseenter', function () {
                                        dropdownMenuTermos.classList.add('show');
                                    });

                                    dropdownMenuTermos.addEventListener('mouseleave', function () {
                                        dropdownMenuTermos.classList.remove('show');
                                    });
                                });
                            </script>
                            <li class="nav-item dropdown" style="vertical-align: middle;">
                                <a class="nav-link" href="{{ route('chamados.painel') }}" id="navbarDropdownChamados"
                                    role="button">
                                    <span class="position-relative d-inline-block">
                                        <i class="fas fa-headset fa-2x"></i>
                                        @php
                                            $chamadosAbertos = \App\Models\Chamado::whereIn('status', ['pendente', 'em_analise', 'em_andamento'])->count();
                                        @endphp
                                        @if($chamadosAbertos > 0)
                                            <span
                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                                style="font-size:0.7em;">
                                                {{ $chamadosAbertos }}
                                            </span>
                                        @endif
                                    </span><br>
                                    <small>Chamados</small>
                                </a>
                            </li>
                        @endif

                        <!-- Botão de Avaliações (para admin e operador) -->
                        @if(Auth::check() && in_array(Auth::user()->nivel, ['admin', 'operador']))
                            <li class="nav-item dropdown" style="vertical-align: middle;">
                                <a class="nav-link" href="{{ route('avaliacoes.index') }}" id="navbarDropdownAvaliacoes"
                                    role="button">
                                    <span class="position-relative d-inline-block">
                                        <i class="fas fa-clipboard-list fa-2x"></i>
                                        @php
                                            $avaliacoesPendentes = \App\Models\Avaliacao::where('status', 'pendente')->count();
                                        @endphp
                                        @if($avaliacoesPendentes > 0)
                                            <span
                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning"
                                                style="font-size:0.7em;">
                                                {{ $avaliacoesPendentes }}
                                            </span>
                                        @endif
                                    </span><br>
                                    <small>Avaliações</small>
                                </a>
                            </li>
                        @endif

                        <li class="nav-item d-none d-lg-block" style="vertical-align: middle; height: 100%;">
                            <div class="vr mx-3 bg-white" style="height: 100%; width: 2px"></div>
                        </li>
                        <li class="nav-item dropdown">
                            @php
                                $termosVencidosCount = isset($termos)
                                    ? $termos->filter(function ($termo) {
                                        return \Carbon\Carbon::parse($termo->data_fim_estagio)->isPast() && !$termo->rescisao;
                                    })->count()
                                    : 0;
                            @endphp

                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown"
                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-sliders"></i> &nbsp; Opções
                                @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                                    @if($termosVencidosCount > 0)
                                        <span class="position-relative ms-2">
                                            <i class="fas fa-bell text-warning"></i>
                                            <span
                                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                                style="font-size:0.7em;">
                                                {{ $termosVencidosCount }}
                                            </span>
                                        </span>
                                    @endif
                                @endif
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li style="text-wrap: inherit;"><span style="text-wrap: inherit;"
                                        class="dropdown-header">{{ auth()->user()->name }}</span></li>
                                @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                                    <li><a class="dropdown-item" href="{{ route('folhas.index') }}">
                                            <i class="fa-solid fa-file-invoice-dollar" style="color: #102e6c"></i> Folhas de
                                            Pagamento</a>
                                    </li>
                                    @if($termosVencidosCount > 0)
                                        <li>
                                            <a class="dropdown-item text-danger fw-bold"
                                                href="{{ route('termos.index', ['status' => 'vencidos']) }}">
                                                <i class="fas fa-exclamation-triangle me-1"></i> Termos Vencidos
                                                <span class="badge bg-danger ms-1">{{ $termosVencidosCount }}</span>
                                            </a>
                                        </li>
                                    @endif
                                @endif
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                                    <li><a class="dropdown-item" href="{{ route('usuarios.index') }}">
                                            <i class="fa-solid fa-users" style="color: #102e6c"></i> Usuários</a>
                                    </li>
                                    <li><a class="dropdown-item" href="{{ route('configuracoes.index') }}">
                                            <i class="fa-solid fa-gear" style="color: #102e6c;"></i> Configurações</a>
                                    </li>
                                @endif
                                <li><a class="dropdown-item" href="{{ route('logout') }}">
                                        <i class="fa-solid fa-right-from-bracket" style="color: #102e6c;"></i>Sair</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </nav>

    <!-- Main content -->
    <main class="container my-5">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-white py-4">
        <style>
            @media (max-width: 767.98px) {
                footer .container {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    text-align: center;
                }

                footer .row {
                    width: 100% !important;
                    flex-direction: column !important;
                    align-items: center !important;
                    justify-content: center !important;
                    text-align: center !important;
                }

                footer .col-md-3,
                footer .col-md-6 {
                    width: 100% !important;
                    max-width: 100% !important;
                    text-align: center !important;
                    margin-bottom: 1.5rem !important;
                }

                footer .d-flex.flex-md-row {
                    flex-direction: column !important;
                    gap: 0.5rem !important;
                }

                footer .text-md-end,
                footer .text-md-start {
                    text-align: center !important;
                }
            }
        </style>
        <div class="container">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <a href="/" class="d-block mb-2">
                        <img src="{{ asset('images/logo_branca_sem_fundo.png') }}" alt="Logo" height="50"
                            class="d-inline-block align-center">
                    </a>
                    <h5 class="font-italic" style="font-size: 3rem; font-family: Asthoria, sans-serif;">SIGE</h5>
                    <p>Sistema de Integração e Gestão de Estágios - Facilitando a integração entre estagiários,
                        instituições de ensino e unidades concedentes.</p>
                </div>
                <div class="col-md-3 mb-3">
                    <h5 class="font-italic" style="font-size: 2rem;">Links Rápidos</h5>
                    <ul class="list-unstyled">
                        @auth
                            @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                                <li><a class="text-white" href="/">Página Inicial</a></li>
                                <li><a class="text-white" href="{{ route('empresas.index') }}">Unidades Concedentes</a></li>
                                <li><a class="text-white" href="{{ route('escolas.index') }}">Instituições de Ensino</a>
                                </li>
                                <li><a class="text-white" href="{{ route('estagiarios.index') }}">Estagiários</a></li>
                                <li><a class="text-white" href="{{ route('supervisores.index') }}">Supervisores</a></li>
                                <li><a class="text-white" href="{{ route('termos.index') }}">Termos de Contrato</a></li>
                            @else
                                <li><a class="text-white" target="_blank"
                                        href="https://ebcpconsultoria.com.br/quem-somos/">Sobre Nós</a></li>
                                <li><a class="text-white" target="_blank"
                                        href="https://ebcpconsultoria.com.br/solucoes-para-empresas/">Serviços</a></li>
                                <!-- Link Central de Ajuda -->
                                <div class="mt-3">
                                    <a href="{{ route('ajuda') }}" class="btn btn-sm btn-outline-light"
                                        style="border-radius: 20px; padding: 5px 20px; font-size: 0.85rem;" target="_blank">
                                        <i class="fas fa-question-circle"></i> Central de Ajuda
                                    </a>
                                </div>
                            @endif
                        @endauth
                        @guest
                            <li><a class="text-white" target="_blank"
                                    href="https://ebcpconsultoria.com.br/quem-somos/">Sobre Nós</a></li>
                            <li><a class="text-white" target="_blank"
                                    href="https://ebcpconsultoria.com.br/solucoes-para-empresas/">Serviços</a></li>
                            <!-- Link Central de Ajuda -->
                            <div class="mt-3">
                                <a href="{{ route('ajuda') }}" class="btn btn-sm btn-outline-light"
                                    style="border-radius: 20px; padding: 5px 20px; font-size: 0.85rem;" target="_blank">
                                    <i class="fas fa-question-circle"></i> Central de Ajuda
                                </a>
                            </div>
                        @endguest
                    </ul>
                </div>
                <div class="col-md-3 mb-3">
                    <h5 class="font-italic" style="font-size: 2rem;">Contato</h5>
                    <ul class="list-unstyled">
                        <li><a class="text-white" href="mailto:contato@ebcpconsultoria.com.br" target="_blank">Email:
                                contato@ebcpconsultoria.com.br</a></li>
                        <li><a class="text-white"
                                href="https://wa.me/5548991468761?text=Ol%C3%A1!%20Vim%20do%20site%20do%20SIGE!"
                                target="_blank">Telefone:
                                +55 (48) 9 9146-8761</a></li>
                        <li><a class="text-white" href="https://maps.app.goo.gl/fGphqEGNKxdSdS2K7" target="_blank">RUA
                                WENCESLAU BRAZ
                                332, VILA MOEMA - TUBARÃO - SC</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5 class="font-italic" style="font-size: 2rem;">Siga-nos</h5>
                    <ul class="list-unstyled">
                        <li><a class="text-white"
                                href="https://www.instagram.com/ebcp.oficial?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw=="
                                target="_blank">Instagram</a></li>
                        <li><a class="text-white" href="https://www.facebook.com/profile.php?id=61561939467054"
                                target="_blank">Facebook</a></li>
                        <li><a class="text-white" href="https://www.linkedin.com/company/ebcp-consultoria/"
                                target="_blank">Linkedin</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex flex-column flex-md-row justify-content-center align-items-center w-100 gap-5"
                        style="text-align:center;">
                        <div class="d-flex flex-column align-items-center justify-content-center text-center mb-3 mb-md-0"
                            style="width: 100%; max-width: 220px;">
                            <span class="fw-normal" style="font-size:1rem;">Desenvolvido por</span>
                            <a href="https://viniciusdev.com" target="_blank"
                                class="my-1 w-100 d-flex justify-content-center align-items-center">
                                <img src="{{ asset('images/Logo_VDev_Para_Fundo_Escuro.png') }}" alt="Vinicius Dev Logo"
                                    height="75"
                                    style="max-width:100px;width:100%;height:auto;object-fit:contain;display:block;">
                            </a>
                        </div>
                        <div class="d-flex flex-column align-items-center justify-content-center text-center"
                            style="margin-top: 0; width: 100%; max-width: 220px;">
                            <span class="fw-normal" style="font-size:1rem;">em parceria com</span>
                            <span class="fw-semibold d-flex flex-column align-items-center" style="font-size:0.8rem;">
                                <a href="https://4strum.github.io/portfolio/" target="_blank"
                                    class="text-white text-decoration-underline mb-1">João
                                    Pedro
                                    Developer</a>
                                <span class="mb-1">&amp;</span>
                                <a href="https://www.linkedin.com/in/davi-aguiar-b4b738280?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=ios_app"
                                    target="_blank" class="text-white text-decoration-underline">Davi Aguiar
                                    Developer</a>
                            </span>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <div class="fw-bold" style="font-size:1.15rem; letter-spacing:1px;">— © {{ date('Y') }}
                            <span class="">SIGE</span> —
                        </div>
                        <div class="fw-semibold" style="font-size:1.05rem;">— <span class="text-warning">Sistema de
                                Integração e Gestão de Estágios</span> —</div>
                        <div class="text-white-50" style="font-size:0.95rem;">— Todos os direitos reservados —</div>

                        <div class="mt-3" id="pwa-footer-link" style="display: none;">
                            <button id="install-app-footer" class="btn btn-sm btn-outline-light"
                                style="border-radius: 20px; padding: 5px 20px; font-size: 0.85rem;">
                                <i class="fas fa-download"></i> Instalar como Aplicativo
                            </button>
                        </div>

                        <!-- Link Central de Ajuda -->
                        <div class="mt-3">
                            <a href="{{ route('ajuda') }}" class="btn btn-sm btn-outline-light"
                                style="border-radius: 20px; padding: 5px 20px; font-size: 0.85rem;" target="_blank">
                                <i class="fas fa-question-circle"></i> Central de Ajuda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <style>
        @media (max-width: 767.98px) {
            footer .d-flex.flex-md-row {
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                text-align: center !important;
                gap: 0.5rem !important;
            }

            footer .d-flex.flex-md-row>div {
                width: 100% !important;
                max-width: 260px !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }

            footer .d-flex.flex-md-row>div:first-child {
                margin-bottom: 0.5rem !important;
            }

            footer .mt-3.text-center {
                margin-top: 2.5rem !important;
            }
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(function (registration) {
                        console.log('✅ Service Worker registrado com sucesso:', registration.scope);
                    })
                    .catch(function (error) {
                        console.log('❌ Falha ao registrar Service Worker:', error);
                    });
            });
        }
    </script>

    <!-- PWA Install Prompt -->
    <script>
        let deferredPrompt;
        const installButtons = {
            footer: document.getElementById('install-app-footer'),
            login: document.getElementById('install-app-login'),
            welcome: document.getElementById('install-app-welcome')
        };

        window.addEventListener('beforeinstallprompt', (e) => {
            // Previne o prompt automático do Chrome
            e.preventDefault();
            // Guarda o evento para usar depois
            deferredPrompt = e;

            // Mostra botões de instalação
            const footerLink = document.getElementById('pwa-footer-link');
            if (footerLink) footerLink.style.display = 'block';

            const loginCard = document.getElementById('pwa-login-card');
            if (loginCard) loginCard.style.display = 'block';

            // Mostra o bloco com o botão de instalação no card da página de login
            const loginInstallBlock = document.getElementById('pwa-install-block');
            if (loginInstallBlock) loginInstallBlock.style.display = 'block';

            const welcomeCard = document.getElementById('pwa-welcome-card');
            if (welcomeCard) welcomeCard.style.display = 'block';

            console.log('📱 PWA instalável detectado!');
        });

        // Função de instalação compartilhada
        function installPWA() {
            if (!deferredPrompt) {
                return;
            }

            // Mostra o prompt
            deferredPrompt.prompt();

            // Aguarda escolha do usuário
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('✅ Usuário aceitou instalar o PWA');

                    // Esconde todos os botões
                    const footerLink = document.getElementById('pwa-footer-link');
                    if (footerLink) footerLink.style.display = 'none';

                    const loginCard = document.getElementById('pwa-login-card');
                    if (loginCard) loginCard.style.display = 'none';

                    const loginInstallBlock = document.getElementById('pwa-install-block');
                    if (loginInstallBlock) loginInstallBlock.style.display = 'none';

                    const welcomeCard = document.getElementById('pwa-welcome-card');
                    if (welcomeCard) welcomeCard.style.display = 'none';

                    // Mostra mensagem de sucesso
                    alert('🎉 App instalado com sucesso! Confira na tela inicial do seu dispositivo.');
                } else {
                    console.log('❌ Usuário recusou instalar o PWA');
                }
                deferredPrompt = null;
            });
        }

        // Registra listeners nos botões
        Object.values(installButtons).forEach(button => {
            if (button) {
                button.addEventListener('click', installPWA);
            }
        });

        // Detecta quando o app já foi instalado
        window.addEventListener('appinstalled', () => {
            console.log('✅ PWA foi instalado!');
            deferredPrompt = null;

            // Esconde botões
            const footerLink = document.getElementById('pwa-footer-link');
            if (footerLink) footerLink.style.display = 'none';

            const loginCard = document.getElementById('pwa-login-card');
            if (loginCard) loginCard.style.display = 'none';

            const loginInstallBlock = document.getElementById('pwa-install-block');
            if (loginInstallBlock) loginInstallBlock.style.display = 'none';

            const welcomeCard = document.getElementById('pwa-welcome-card');
            if (welcomeCard) welcomeCard.style.display = 'none';
        });

        // Detecta se já está instalado (iOS/Android)
        window.addEventListener('load', () => {
            if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
                console.log('✅ App já está instalado');
                // Esconde todos os botões quando já está rodando como app
                const footerLink = document.getElementById('pwa-footer-link');
                if (footerLink) footerLink.style.display = 'none';

                const loginCard = document.getElementById('pwa-login-card');
                if (loginCard) loginCard.style.display = 'none';

                const loginInstallBlock = document.getElementById('pwa-install-block');
                if (loginInstallBlock) loginInstallBlock.style.display = 'none';

                const welcomeCard = document.getElementById('pwa-welcome-card');
                if (welcomeCard) welcomeCard.style.display = 'none';
            }
        });
    </script>

    @yield('scripts')
</body>

</html>