@extends('layouts.main')

@section('title', 'Página Inicial - Estagiário')

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header de Boas-vindas -->
    <div class="container mb-4"
        style="background: linear-gradient(135deg, #102E6C 0%, #1a4d9e 100%); border-radius: 15px; margin-top: -30px; padding: 30px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div class="row align-items-center">
            <div class="col-md-12 text-center">
                <h2 class="mb-2" style="font-weight: 600;">Bem-vindo(a), {{ Auth::user()->name }}! 👋</h2>
                <p class="mb-0" style="opacity: 0.9; font-size: 1.05rem;">Gerencie seus dados, contratos e acompanhe suas
                    informações de estágio.</p>
            </div>
        </div>
    </div>

    <hr style="margin-top: -10px; background-color: #102e6c;">

    <!-- Grid de Cards Principais -->
    <div class="row g-4 mt-2">

        <!-- Card: Dados Pessoais -->
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm"
                style="border-radius: 15px; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 60px; height: 60px; background: linear-gradient(135deg, #102E6C 0%, #1e5bb8 100%);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="white"
                                class="bi bi-person-badge" viewBox="0 0 16 16">
                                <path d="M6.5 2a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0" />
                                <path
                                    d="M4.5 0A2.5 2.5 0 0 0 2 2.5V14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2.5A2.5 2.5 0 0 0 11.5 0zM3 2.5A1.5 1.5 0 0 1 4.5 1h7A1.5 1.5 0 0 1 13 2.5v10.795a4.2 4.2 0 0 0-.776-.492C11.392 12.387 10.063 12 8 12s-3.392.387-4.224.803a4.2 4.2 0 0 0-.776.492z" />
                            </svg>
                        </div>
                        <div>
                            <h5 class="card-title mb-0" style="font-weight: 600; color: #2d3748;">Dados Pessoais</h5>
                            <small class="text-muted">Informações e documentos</small>
                        </div>
                    </div>

                    <p class="card-text text-muted mb-4" style="font-size: 0.95rem;">
                        Visualize e atualize seus dados cadastrais, faça download dos seus documentos ou envie versões
                        atualizadas.
                    </p>

                    <div class="d-grid gap-2">
                        <a href="{{ route('estagiario.perfil') }}" class="btn btn-primary"
                            style="background: linear-gradient(135deg, #102E6C 0%, #1e5bb8 100%); border: none; border-radius: 8px; padding: 10px; font-weight: 500;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-eye me-2" viewBox="0 0 16 16">
                                <path
                                    d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                <path
                                    d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                            </svg>
                            Ver Perfil Completo
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Contratos -->
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm"
                style="border-radius: 15px; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 60px; height: 60px; background: linear-gradient(135deg, #c42a19 0%, #d87469 100%);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="white"
                                class="bi bi-file-earmark-text" viewBox="0 0 16 16">
                                <path
                                    d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5" />
                                <path
                                    d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z" />
                            </svg>
                        </div>
                        <div>
                            <h5 class="card-title mb-0" style="font-weight: 600; color: #2d3748;">Contratos</h5>
                            <small class="text-muted">Termos de estágio</small>
                        </div>
                    </div>

                    <p class="card-text text-muted mb-4" style="font-size: 0.95rem;">
                        Acompanhe seus contratos de estágio, visualize documentos, recibos e informações sobre suas
                        atividades.
                    </p>

                    <div class="d-grid gap-2">
                        <a href="{{ route('estagiario.contratos') }}" class="btn"
                            style="background: linear-gradient(135deg, #c42a19 0%, #d87469 100%); border: none; border-radius: 8px; padding: 10px; font-weight: 500; color: white;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-folder2-open me-2" viewBox="0 0 16 16">
                                <path
                                    d="M1 3.5A1.5 1.5 0 0 1 2.5 2h2.764c.958 0 1.76.56 2.311 1.184C7.985 3.648 8.48 4 9 4h4.5A1.5 1.5 0 0 1 15 5.5v.64c.57.265.94.876.856 1.546l-.64 5.124A2.5 2.5 0 0 1 12.733 15H3.266a2.5 2.5 0 0 1-2.481-2.19l-.64-5.124A1.5 1.5 0 0 1 1 6.14zM2 6h12v-.5a.5.5 0 0 0-.5-.5H9c-.964 0-1.71-.629-2.174-1.154C6.374 3.334 5.82 3 5.264 3H2.5a.5.5 0 0 0-.5.5zm-.367 1a.5.5 0 0 0-.496.562l.64 5.124A1.5 1.5 0 0 0 3.266 14h9.468a1.5 1.5 0 0 0 1.489-1.314l.64-5.124A.5.5 0 0 0 14.367 7z" />
                            </svg>
                            Ver Meus Contratos
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm"
                style="border-radius: 15px; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 60px; height: 60px; background: linear-gradient(135deg, #0f4c81 0%, #58a6d9 100%);">
                            <i class="fas fa-briefcase" style="font-size: 1.5rem; color: white;"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0" style="font-weight: 600; color: #2d3748;">Vagas de Estágio</h5>
                            <small class="text-muted">Busca e candidatura</small>
                        </div>
                    </div>

                    <p class="card-text text-muted mb-4" style="font-size: 0.95rem;">
                        Consulte vagas divulgadas pelas unidades concedentes, envie seu currículo e acompanhe a situação das
                        suas candidaturas.
                    </p>

                    <div class="d-grid gap-2">
                        <a href="{{ route('vagas.publicas.index') }}" class="btn"
                            style="background: linear-gradient(135deg, #0f4c81 0%, #58a6d9 100%); border: none; border-radius: 8px; padding: 10px; font-weight: 500; color: white;">
                            <i class="fas fa-search me-2"></i>
                            Ver Vagas Abertas
                        </a>
                        <a href="{{ route('vagas.publicas.minhas-candidaturas') }}" class="btn btn-outline-primary"
                            style="border-radius: 8px; padding: 8px; font-weight: 500;">
                            <i class="fas fa-file-signature me-2"></i>
                            Minhas Candidaturas
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card: Processos Seletivos -->
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm"
                style="border-radius: 15px; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 60px; height: 60px; background: linear-gradient(135deg, #13502b 0%, #4ebb7c 100%);">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="white"
                                class="bi bi-mortarboard" viewBox="0 0 16 16">
                                <path
                                    d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.905 3.953a.5.5 0 0 0 .422.941L6.5 7.653V13a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5V7.653l6.294-3.159a.5.5 0 0 0 .422-.941L8.211 2.047Z" />
                                <path
                                    d="M13.25 8.885.75 5.568v7.052a1.5 1.5 0 0 0 1.5 1.5h11.5a1.5 1.5 0 0 0 1.5-1.5v-7.051Z" />
                            </svg>
                        </div>
                        <div>
                            <h5 class="card-title mb-0" style="font-weight: 600; color: #2d3748;">Processos Seletivos</h5>
                            <small class="text-muted">Oportunidades de inscrição</small>
                        </div>
                    </div>

                    <p class="card-text text-muted mb-4" style="font-size: 0.95rem;">
                        Confira os editais de processos seletivos abertos, realize suas inscrições e acompanhe o status de
                        suas candidaturas.
                    </p>

                    <div class="d-grid gap-2">
                        <div class="d-grid gap-2">
                            <a href="{{ route('processos-seletivos.abertos') }}" class="btn"
                                style="background: linear-gradient(135deg, #13502b 0%, #4ebb7c 100%); border: none; border-radius: 8px; padding: 10px; font-weight: 500; color: white;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-search me-2" viewBox="0 0 16 16">
                                    <path
                                        d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.02.062.038.093.055l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.09-.09zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                                </svg>
                                Ver Processos Abertos
                            </a>
                            <a href="{{ route('processos-seletivos.minhas-inscricoes') }}" class="btn btn-outline-success"
                                style="border: 2px solid #4ebb7c; color: #13502b; border-radius: 8px; padding: 8px; font-weight: 500;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-list-check me-2" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3.854 2.146a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 3.293l1.146-1.147a.5.5 0 0 1 .708 0zm0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 7.293l1.146-1.147a.5.5 0 0 1 .708 0zm0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0z" />
                                </svg>
                                Minhas Inscrições
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Informações Adicionais -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card border-0 shadow-sm"
                style="border-radius: 15px; background: linear-gradient(135deg, #d5e8f7 0%, #a8d0e6 100%);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#102E6C"
                            class="bi bi-info-circle me-3" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            <path
                                d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533z" />
                            <circle cx="8" cy="4.5" r="1" />
                        </svg>
                        <div>
                            <h6 class="mb-1" style="color: #102E6C; font-weight: 600;">Precisa de Ajuda?</h6>
                            <p class="mb-0 text-muted" style="font-size: 0.95rem;">
                                Em caso de dúvidas ou problemas, entre em contato com nossa equipe. Estamos à disposição
                                para sanar dúvidas em relação ao sistema e outros assuntos.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PWA Install Banner (enxugado) -->
    <div class="row mt-4" id="pwa-welcome-card" style="display:none;">
        <div class="col-12 col-lg-10 mx-auto">
            <div class="card shadow-sm border-0" style="border-radius:14px; animation: slideInUp .5s ease-out;">
                <div class="card-body d-flex flex-column flex-md-row align-items-center gap-4"
                    style="background:linear-gradient(135deg,#198754 0%,#20c997 100%); border-radius:14px; color:#fff;">
                    <div class="text-center" style="min-width:140px;">
                        <i class="fas fa-mobile-alt" style="font-size:3rem;"></i>
                        <h6 class="fw-bold mt-2 mb-0">Instale o SIGEBR</h6>
                        <small style="opacity:.85;">PC ou Celular</small>
                    </div>
                    <div class="flex-grow-1">
                        <p class="mb-2" style="font-size:.9rem; line-height:1.35;">
                            Use como aplicativo para abrir mais rápido e em tela cheia. Disponível para Android, iOS,
                            Windows e Mac.
                        </p>
                        <div class="d-flex flex-wrap gap-3 mb-3" style="font-size:.75rem;">
                            <span class="d-inline-flex align-items-center gap-1"><i class="fas fa-bolt"></i> Rápido</span>
                            <span class="d-inline-flex align-items-center gap-1"><i class="fas fa-sync-alt"></i>
                                Auto‑update</span>
                            <span class="d-inline-flex align-items-center gap-1"><i class="fas fa-expand"></i> Tela
                                cheia</span>
                            <span class="d-inline-flex align-items-center gap-1"><i class="fas fa-shield-alt"></i>
                                Seguro</span>
                        </div>
                        <button id="install-app-welcome" class="btn btn-light btn-sm fw-bold px-4 py-2"
                            style="border-radius:30px; box-shadow:0 4px 12px rgba(0,0,0,.25);">
                            <i class="fas fa-download me-1"></i> Instalar Agora
                        </button>
                        <small class="d-block mt-2" style="opacity:.85; font-size:.7rem;">Sem uso de espaço extra •
                            Instalação instantânea</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15) !important;
        }

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

        #install-app-welcome:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(25, 135, 84, 0.4) !important;
        }
    </style>

@endsection