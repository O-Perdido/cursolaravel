@extends('layouts.main')

@section('title', 'Portal do Estagiário - SIGE')

@section('content')
<!-- Background decorativo da página inteira -->
<div class="page-ambient-bg"
    style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; pointer-events: none; overflow: hidden;">
    <!-- Padrão de pontos -->
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.07;">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="dots-bg" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                    <circle cx="20" cy="20" r="2" fill="#102E6C" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#dots-bg)" />
        </svg>
    </div>

    <!-- Camada de linhas diagonais suaves -->
    <div
        style="position: absolute; top: -20%; left: -20%; width: 140%; height: 140%; opacity: 0.07; background-image: repeating-linear-gradient(120deg, rgba(16, 46, 108, 0.16) 0px, rgba(16, 46, 108, 0.16) 1px, transparent 1px, transparent 120px); transform: rotate(-6deg);">
    </div>

    <!-- Glow radial central -->
    <div
        style="position: absolute; top: 20%; left: 50%; width: 900px; height: 900px; transform: translateX(-50%); background: radial-gradient(circle at center, rgba(16, 46, 108, 0.06), rgba(16, 46, 108, 0.02) 35%, transparent 70%); border-radius: 50%;">
    </div>

    <!-- Linhas horizontais -->
    <div
        style="position: absolute; top: 15%; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(16, 46, 108, 0.1), transparent);">
    </div>
    <div
        style="position: absolute; top: 30%; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(236, 208, 11, 0.08), transparent);">
    </div>
    <div
        style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(16, 46, 108, 0.08), transparent);">
    </div>
    <div
        style="position: absolute; top: 70%; left: 0; right: 0; height: 1px; background: linear-gradient(90deg, transparent, rgba(236, 208, 11, 0.08), transparent);">
    </div>

    <!-- Linhas verticais -->
    <div
        style="position: absolute; top: 0; left: 15%; bottom: 0; width: 1px; background: linear-gradient(180deg, transparent, rgba(16, 46, 108, 0.08), transparent);">
    </div>
    <div
        style="position: absolute; top: 0; left: 35%; bottom: 0; width: 1px; background: linear-gradient(180deg, transparent, rgba(236, 208, 11, 0.06), transparent);">
    </div>
    <div
        style="position: absolute; top: 0; left: 65%; bottom: 0; width: 1px; background: linear-gradient(180deg, transparent, rgba(236, 208, 11, 0.06), transparent);">
    </div>
    <div
        style="position: absolute; top: 0; left: 85%; bottom: 0; width: 1px; background: linear-gradient(180deg, transparent, rgba(16, 46, 108, 0.08), transparent);">
    </div>

    <!-- Formas geométricas em outline -->
    <div
        style="position: absolute; top: 18%; right: 12%; width: 130px; height: 130px; border: 2px solid rgba(16, 46, 108, 0.12); transform: rotate(18deg); border-radius: 16px;">
    </div>
    <div
        style="position: absolute; bottom: 18%; left: 12%; width: 110px; height: 110px; border: 2px solid rgba(236, 208, 11, 0.16); transform: rotate(-14deg); border-radius: 14px;">
    </div>
    <div style="position: absolute; top: 42%; left: 8%; width: 90px; height: 90px; opacity: 0.18;">
        <svg width="100%" height="100%" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
            <polygon points="50,5 93,28 93,72 50,95 7,72 7,28" fill="none" stroke="#102E6C" stroke-width="2" />
        </svg>
    </div>
    <div style="position: absolute; top: 62%; right: 10%; width: 100px; height: 100px; opacity: 0.2;">
        <svg width="100%" height="100%" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
            <polygon points="50,8 88,30 88,70 50,92 12,70 12,30" fill="none" stroke="#ECD00B" stroke-width="2" />
        </svg>
    </div>

    <!-- Formas circulares flutuantes -->
    <div
        style="position: absolute; top: 25%; left: 5%; width: 400px; height: 400px; background: radial-gradient(circle at 30% 30%, rgba(236, 208, 11, 0.05), transparent); border-radius: 50%; animation: float 8s ease-in-out infinite;">
    </div>
    <div
        style="position: absolute; top: 55%; right: 8%; width: 350px; height: 350px; background: radial-gradient(circle at 30% 30%, rgba(16, 46, 108, 0.04), transparent); border-radius: 50%; animation: float 10s ease-in-out infinite reverse;">
    </div>
    <div
        style="position: absolute; bottom: 10%; left: 25%; width: 300px; height: 300px; background: radial-gradient(circle at 30% 30%, rgba(236, 208, 11, 0.04), transparent); border-radius: 50%; animation: float 12s ease-in-out infinite;">
    </div>

    <!-- Pontos de destaque geométricos -->
    <div
        style="position: absolute; top: 12%; left: 52%; width: 18px; height: 18px; border-radius: 4px; background: rgba(236, 208, 11, 0.25); transform: rotate(45deg);">
    </div>
    <div
        style="position: absolute; bottom: 24%; right: 35%; width: 14px; height: 14px; border-radius: 3px; background: rgba(16, 46, 108, 0.28); transform: rotate(30deg);">
    </div>
    <div
        style="position: absolute; top: 46%; right: 48%; width: 10px; height: 10px; border-radius: 50%; background: rgba(236, 208, 11, 0.35);">
    </div>
</div>

<!-- Hero Section com Carrossel -->
<div class="hero-section"
    style="background: linear-gradient(135deg, #102E6C 0%, #0A1F4D 50%, #1a3a8a 100%); position: relative; overflow: hidden; min-height: 650px;">
    <!-- Padrão de fundo decorativo com pontos -->
    <div class="hero-pattern" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; opacity: 0.08;">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="dots" x="0" y="0" width="50" height="50" patternUnits="userSpaceOnUse">
                    <circle cx="25" cy="25" r="3" fill="white" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#dots)" />
        </svg>
    </div>

    <!-- Linhas decorativas onduladas -->
    <div style="position: absolute; top: -10%; left: 0; right: 0; height: 40%; opacity: 0.15; z-index: 1;">
        <svg width="100%" height="100%" viewBox="0 0 1200 600" preserveAspectRatio="none"
            xmlns="http://www.w3.org/2000/svg">
            <!-- Ondas superiores -->
            <path d="M0,100 Q300,50 600,100 T1200,100 L1200,200 Q900,250 600,200 T0,200 Z" fill="none" stroke="#E0E7FF"
                stroke-width="2" opacity="0.6" />
            <path d="M0,150 Q300,100 600,150 T1200,150 L1200,250 Q900,300 600,250 T0,250 Z" fill="none" stroke="#E0E7FF"
                stroke-width="2" opacity="0.4" />
            <path d="M0,200 Q300,150 600,200 T1200,200 L1200,300 Q900,350 600,300 T0,300 Z" fill="none" stroke="#E0E7FF"
                stroke-width="2" opacity="0.2" />
        </svg>
    </div>

    <!-- Formas geométricas flutuantes -->
    <div
        style="position: absolute; top: 10%; left: 5%; width: 300px; height: 300px; background: radial-gradient(circle at 30% 30%, rgba(236, 208, 11, 0.1), transparent); border-radius: 50%; z-index: 1;">
    </div>
    <div
        style="position: absolute; bottom: 5%; right: 3%; width: 250px; height: 250px; background: radial-gradient(circle at 30% 30%, rgba(236, 208, 11, 0.08), transparent); border-radius: 50%; z-index: 1;">
    </div>

    <!-- Linhas retas diagonais -->
    <div
        style="position: absolute; top: -5%; left: 10%; width: 2px; height: 120%; background: linear-gradient(180deg, transparent, rgba(224, 231, 255, 0.15), transparent); transform: rotate(-25deg); z-index: 1;">
    </div>
    <div
        style="position: absolute; top: -5%; left: 20%; width: 2px; height: 120%; background: linear-gradient(180deg, transparent, rgba(224, 231, 255, 0.1), transparent); transform: rotate(-25deg); z-index: 1;">
    </div>
    <div
        style="position: absolute; top: -5%; right: 10%; width: 2px; height: 120%; background: linear-gradient(180deg, transparent, rgba(224, 231, 255, 0.15), transparent); transform: rotate(25deg); z-index: 1;">
    </div>
    <div
        style="position: absolute; top: -5%; right: 20%; width: 2px; height: 120%; background: linear-gradient(180deg, transparent, rgba(224, 231, 255, 0.1), transparent); transform: rotate(25deg); z-index: 1;">
    </div>

    <!-- Conteúdo do Carrossel -->
    <div class="hero-carousel-wrapper" style="position: relative; z-index: 2; height: 650px;">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" style="height: 100%;">
            <!-- Slides do Carrossel -->
            <div class="carousel-inner" style="height: 100%;">
                <!-- Slide 1: Bem-vindo -->
                <!-- 📸 IMAGEM DE FUNDO (OPCIONAL): Adicione background-image abaixo -->
                <!-- Sugestão: Imagem de estudantes felizes, ambiente de trabalho moderno ou ilustração relacionada a estágio -->
                <!-- Tamanho recomendado: 1920x650px (landscape) | Formato: JPG/PNG -->
                <!-- Exemplo de uso: style="background: url('{{ asset('images/hero-slide1.jpg') }}') center/cover; height: 100%;" -->
                <div class="carousel-item active"
                    style="background: url('{{ asset('images/hero-slide1.jpg') }}') center/cover; height: 100%;">
                    <!-- Overlay escuro sobre a imagem de fundo (remova se não usar imagem) -->
                    <div class="carousel-overlay"
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(16, 47, 108, 0.685); z-index: 1;">
                    </div>
                    <div class="d-flex align-items-center justify-content-center"
                        style="height: 100%; position: relative; z-index: 2;">
                        <div class="container px-4">
                            <div class="row align-items-center">
                                <div class="col-lg-7 text-center text-lg-start mb-4 mb-lg-0">
                                    <h1 class="display-3 fw-bold mb-3 text-white">
                                        Bem-vindo ao SIGE
                                    </h1>
                                    <p class="lead text-white mb-4" style="font-size: 1.3rem; opacity: 0.95;">
                                        A plataforma de <strong>oportunidades de estágio</strong> mais completa do país
                                    </p>
                                    <div
                                        class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                                        <a href="{{ route('processos-seletivos.publicos') }}"
                                            class="btn btn-light btn-lg px-5 shadow-lg fw-bold">
                                            <i class="fas fa-briefcase me-2"></i>Explorar Oportunidades
                                        </a>
                                        @guest
                                            <a href="{{ route('novo-estagiario-ajax-create') }}"
                                                class="btn btn-outline-light btn-lg px-5 fw-bold"
                                                style="border-width: 2px;">
                                                <i class="fas fa-user-plus me-2"></i>Cadastre-se Grátis
                                            </a>
                                        @endguest
                                    </div>
                                </div>
                                <!--
                                <div class="col-lg-5 text-center hero-illustration">
                                    <div class="bg-white bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center mx-auto backdrop-blur"
                                        style="height: 320px; max-width: 420px; border: 2px solid rgba(255,255,255,0.1);">
                                        <i class="fas fa-graduation-cap text-white"
                                            style="font-size: 140px; opacity: 0.2;"></i>
                                    </div>
                                </div>
                                -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2: Sobre -->
                <!-- 📸 IMAGEM DE FUNDO (OPCIONAL): Adicione background-image abaixo -->
                <!-- Sugestão: Imagem de estudantes/profissionais trabalhando em equipe -->
                <!-- Tamanho recomendado: 1920x650px | Formato: JPG/PNG -->
                <div class="carousel-item"
                    style="background: url('{{ asset('images/hero-slide2.jpg') }}') center/cover; height: 100%;">
                    <!-- Overlay escuro sobre a imagem de fundo -->
                    <div class="carousel-overlay"
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(10, 31, 77, 0.685); z-index: 1;">
                    </div>
                    <div class="d-flex align-items-center justify-content-center"
                        style="height: 100%; position: relative; z-index: 2;">
                        <div class="container px-4">
                            <div class="row align-items-center g-4">
                                <!--
                                <div class="col-lg-6 order-lg-2 text-center mb-4 mb-lg-0 hero-illustration">
                                    <div class="bg-white bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center mx-auto p-4 backdrop-blur"
                                        style="border: 2px solid rgba(255,255,255,0.1); max-width: 350px; height: 300px;">
                                        <i class="fas fa-chart-line text-white"
                                            style="font-size: 100px; opacity: 0.2;"></i>
                                    </div>
                                </div>
                                -->
                                <div class="col-lg-6 order-lg-1 text-white px-3 px-lg-4">
                                    <h2 class="display-4 fw-bold mb-3">Conectando Talentos a Oportunidades</h2>
                                    <p class="lead mb-4" style="font-size: 1.15rem; opacity: 0.95; line-height: 1.7;">
                                        O SIGE reúne as melhores <strong>empresas</strong>, <strong>instituições de
                                            ensino</strong> e <strong>órgãos públicos</strong> em um único portal para
                                        conectar estagiários a oportunidades incríveis.
                                    </p>
                                    <ul class="list-unstyled mb-0" style="font-size: 1.05rem;">
                                        <li class="mb-2"><i class="fas fa-check-circle me-2"
                                                style="color: #ECD00B;"></i> Processos Seletivos Transparentes</li>
                                        <li class="mb-2"><i class="fas fa-check-circle me-2"
                                                style="color: #ECD00B;"></i> Acompanhamento em Tempo Real</li>
                                        <li class="mb-0"><i class="fas fa-check-circle me-2"
                                                style="color: #ECD00B;"></i> Segurança e Confiabilidade</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3: CTA Final -->
                <!-- 📸 IMAGEM DE FUNDO (OPCIONAL): Adicione background-image abaixo -->
                <!-- Sugestão: Imagem de estagiário feliz, conquista, ou workspace moderno -->
                <!-- Tamanho recomendado: 1920x650px | Formato: JPG/PNG -->
                <div class="carousel-item"
                    style="background: url('{{ asset('images/hero-slide3.jpg') }}') center/cover; height: 100%;">
                    <!-- Overlay escuro sobre a imagem de fundo -->
                    <div class="carousel-overlay"
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(26, 58, 138, 0.685); z-index: 1;">
                    </div>
                    <div class="d-flex align-items-center justify-content-center"
                        style="height: 100%; position: relative; z-index: 2;">
                        <div class="container text-center text-white px-4">
                            <div class="mb-4">
                                <i class="fas fa-rocket" style="font-size: 70px; opacity: 0;"></i>
                            </div>
                            <h2 class="display-3 fw-bold mb-3">Comece Agora Mesmo!</h2>
                            <p class="lead mb-4"
                                style="font-size: 1.25rem; opacity: 0.95; max-width: 650px; margin: 0 auto; line-height: 1.6;">
                                Junte-se a milhares de estagiários que já encontraram suas oportunidades através do SIGE
                            </p>
                            @guest
                                <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                                    <a href="{{ route('novo-estagiario-ajax-create') }}"
                                        class="btn btn-light btn-lg px-5 shadow-lg fw-bold">
                                        <i class="fas fa-user-plus me-2"></i>Criar Minha Conta
                                    </a>
                                    <a href="{{ route('processos-seletivos.publicos') }}"
                                        class="btn btn-outline-light btn-lg px-5 fw-bold" style="border-width: 2px;">
                                        <i class="fas fa-search me-2"></i>Procurar Vagas
                                    </a>
                                </div>
                            @else
                                <a href="{{ route('processos-seletivos.publicos') }}"
                                    class="btn btn-light btn-lg px-5 shadow-lg fw-bold">
                                    <i class="fas fa-briefcase me-2"></i>Ver Processos Disponíveis
                                </a>
                            @endguest
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controles do Carrossel -->
            <button class="carousel-control-prev carousel-control-custom" type="button" data-bs-target="#heroCarousel"
                data-bs-slide="prev">
                <span class="carousel-arrow-custom">
                    <i class="fas fa-chevron-left"></i>
                </span>
            </button>
            <button class="carousel-control-next carousel-control-custom" type="button" data-bs-target="#heroCarousel"
                data-bs-slide="next">
                <span class="carousel-arrow-custom">
                    <i class="fas fa-chevron-right"></i>
                </span>
            </button>

            <!-- Indicadores -->
            <div class="carousel-indicators" style="bottom: 30px; z-index: 10;">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"
                    aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid py-4">
    <div class="container mb-5">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Card 1: Processos Seletivos -->
            <div class="col">
                <div class="card h-100 shadow border-0 overflow-hidden hover-lift-card"
                    style="border-radius: 12px; transition: all 0.3s ease;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="rounded-circle p-3 me-3"
                                style="background: linear-gradient(135deg, #102E6C 0%, #1a3a8a 100%); width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-clipboard-list text-white" style="font-size: 28px;"></i>
                            </div>
                            <h3 class="mb-0 text-dark">Processos Seletivos</h3>
                        </div>
                        <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.6;">
                            Participe de processos seletivos de órgãos públicos e parceiros.
                            Acompanhe prazos, requisitos e inscreva-se.
                        </p>
                        <a href="{{ route('processos-seletivos.publicos') }}" class="btn fw-bold"
                            style="background-color: #102E6C; color: white; border: none; transition: all 0.3s;">
                            <i class="fas fa-arrow-right me-2"></i>Explorar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 2: Vagas de Estágio -->
            <div class="col">
                <div class="card h-100 shadow border-0 overflow-hidden hover-lift-card position-relative"
                    style="border-radius: 12px; transition: all 0.3s ease; opacity: 0.85;">
                    <span class="badge bg-warning position-absolute top-0 end-0 m-3 px-3 py-2"
                        style="border-radius: 20px;">
                        <i class="fas fa-clock me-1"></i>Em Breve
                    </span>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="rounded-circle p-3 me-3"
                                style="background: linear-gradient(135deg, #19B755 0%, #15a34a 100%); width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-briefcase text-white" style="font-size: 28px;"></i>
                            </div>
                            <h3 class="mb-0 text-dark">Vagas de Estágio</h3>
                        </div>
                        <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.6;">
                            Em breve você poderá buscar vagas de estágio direto no portal e se candidatar com apenas
                            alguns cliques.
                        </p>
                        <button class="btn fw-bold" disabled
                            style="background-color: #e0e0e0; color: #666; border: none;">
                            <i class="fas fa-hourglass-half me-2"></i>Aguarde
                        </button>
                    </div>
                </div>
            </div>

            <!-- Card 3: Meu Perfil -->
            <div class="col">
                <div class="card h-100 shadow border-0 overflow-hidden hover-lift-card"
                    style="border-radius: 12px; transition: all 0.3s ease;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="rounded-circle p-3 me-3"
                                style="background: linear-gradient(135deg, #ECD00B 0%, #f59e0b 100%); width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user text-dark" style="font-size: 28px;"></i>
                            </div>
                            <h3 class="mb-0 text-dark">Meu Perfil</h3>
                        </div>
                        <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.6;">
                            Gerencie suas informações, atualize seu perfil profissional, acompanhe seus contratos, seus
                            processos e mantenha-se atualizado.
                        </p>
                        @guest
                            <!-- rota para a tela inicial de estagiario -->
                            <a href="{{ route('login') }}" class="btn fw-bold"
                                style="background-color: #ECD00B; color: #000; border: none;">
                                <i class="fas fa-sign-in-alt me-2"></i>Entrar
                            </a>
                        @else
                            <a href="{{ route('estagiario.editar-perfil') }}" class="btn fw-bold"
                                style="background-color: #ECD00B; color: #000; border: none;">
                                <i class="fas fa-user me-2"></i>Acessar Perfil
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção: Como Funciona -->
    <!--
    <div class="row row-cols-1 row-cols-md-2 g-4 mb-5">        
        <div class="col">
            <div class="card h-100 shadow border-0 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle p-3 me-3" style="background-color: rgba(16, 46, 108, 0.1);">
                            <i class="fas fa-clipboard-list" style="font-size: 32px; color: #102E6C;"></i>
                        </div>
                        <h3 class="mb-0 text-dark">Processos Seletivos</h3>
                    </div>
                    <p class="text-muted mb-4">
                        Participe de processos seletivos de órgãos públicos e parceiros.
                        Acompanhe prazos, requisitos e inscreva-se diretamente pelo sistema.
                    </p>
                    <a href="{{ route('processos-seletivos.publicos') }}" class="btn btn-lg w-100"
                        style="background-color: #102E6C; color: white; border: none;">
                        <i class="fas fa-arrow-right me-2"></i>Explorar Processos
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 shadow border-0 position-relative" style="opacity: 0.85;">
                <span class="badge bg-warning position-absolute top-0 end-0 m-3 px-3 py-2">
                    <i class="fas fa-clock me-1"></i>Em Breve
                </span>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle p-3 me-3" style="background-color: rgba(25, 183, 85, 0.1);">
                            <i class="fas fa-briefcase" style="font-size: 32px; color: #19B755;"></i>
                        </div>
                        <h3 class="mb-0 text-dark">Vagas de Estágio</h3>
                    </div>
                    <p class="text-muted mb-4">
                        Em breve você poderá buscar vagas de estágio direto no portal, filtrar por área de interesse,
                        localização e se candidatar com apenas alguns cliques.
                    </p>
                    <button class="btn btn-lg w-100" disabled
                        style="background-color: #e0e0e0; color: #666; border: none;">
                        <i class="fas fa-hourglass-half me-2"></i>Aguarde Novidades
                    </button>
                </div>
            </div>
        </div>
    </div>
-->

    <!-- Seção: Como Funciona -->
    <div class="row mb-5">
        <div class="col-12 mb-4 text-center">
            <h2 class="fw-bold text-dark mb-2">Como Funciona?</h2>
            <p class="text-muted lead">Seu guia rápido para aproveitar ao máximo o portal</p>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
        <div class="col">
            <div class="card h-100 border-0 shadow-sm text-center p-4">
                <div class="mb-3">
                    <div class="rounded-circle d-inline-flex p-4 mx-auto"
                        style="background-color: rgba(16, 46, 108, 0.1);">
                        <i class="fas fa-user-plus" style="font-size: 40px; color: #102E6C;"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-3">1. Cadastre-se</h4>
                <p class="text-muted">
                    Crie sua conta e preencha seu perfil com suas informações acadêmicas e profissionais.
                </p>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 border-0 shadow-sm text-center p-4">
                <div class="mb-3">
                    <div class="rounded-circle d-inline-flex p-4 mx-auto"
                        style="background-color: rgba(25, 183, 85, 0.1);">
                        <i class="fas fa-search" style="font-size: 40px; color: #19B755;"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-3">2. Busque Oportunidades</h4>
                <p class="text-muted">
                    Explore processos seletivos e vagas compatíveis com seu perfil, curso e disponibilidade.
                </p>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 border-0 shadow-sm text-center p-4">
                <div class="mb-3">
                    <div class="rounded-circle d-inline-flex p-4 mx-auto"
                        style="background-color: rgba(16, 46, 108, 0.15);">
                        <i class="fas fa-rocket" style="font-size: 40px; color: #102E6C;"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-3">3. Candidate-se</h4>
                <p class="text-muted">
                    Inscreva-se nos processos de seu interesse e acompanhe o status de suas candidaturas pelo sistema.
                </p>
            </div>
        </div>
    </div>

    <!-- Banner Informativo com Imagem -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card border-0 shadow-lg overflow-hidden"
                style="background: linear-gradient(135deg, #102E6C 0%, #19B755 100%);">
                <div class="card-body p-4 p-md-5">
                    <div class="row align-items-center">
                        <div class="col-lg-8 text-white mb-4 mb-lg-0">
                            <h3 class="fw-bold mb-4">
                                <i class="fas fa-lightbulb me-2"></i>Dicas Importantes para Estagiários
                            </h3>
                            <ul class="list-unstyled mb-0" style="font-size: 1.1rem;">
                                <li class="mb-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Mantenha seu perfil sempre atualizado</strong> - Dados corretos aumentam
                                    suas chances
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Fique atento aos prazos de inscrição</strong> - Não perca oportunidades por
                                    atraso
                                </li>
                                <li class="mb-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Leia atentamente os requisitos</strong> - Verifique se você atende antes de
                                    se inscrever
                                </li>
                                <li class="mb-0">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Acompanhe suas inscrições regularmente</strong> - Fique por dentro do status
                                </li>
                            </ul>
                        </div>
                        <div class="col-lg-4 text-center">
                            <!-- ESPAÇO PARA IMAGEM: 300x300px -->
                            <!-- Sugestão Canva: Ícone de checklist, lâmpada ou pessoa estudando -->
                            <!-- Dimensão ideal: 300x300px (quadrado) -->
                            <div class="bg-white bg-opacity-20 rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 200px; height: 200px;">

                                <img src="{{ asset('images/tips-icon.png') }}" alt="Dicas"
                                    class="img-fluid rounded-circle" style="max-width: 200px;">
                                <!-- <i class="fas fa-lightbulb text-white" style="font-size: 80px;"></i> -->
                                <!-- Após criar imagem, substituir por: -->
                                <!-- <img src="{{ asset('images/tips-icon.png') }}" alt="Dicas" class="img-fluid rounded-circle" style="max-width: 200px;"> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Perguntas Frequentes -->
    <div class="row mb-5">
        <div class="col-12 mb-4 text-center">
            <h2 class="fw-bold text-dark mb-2">
                <i class="fas fa-question-circle me-2" style="color: #102E6C;"></i>Perguntas Frequentes
            </h2>
            <p class="text-muted">Tire suas dúvidas sobre o sistema</p>
        </div>
        <div class="col-lg-8 mx-auto">
            <div class="accordion shadow-sm" id="faqAccordion">
                <div class="accordion-item border-0 mb-3 shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold bg-light" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq1">
                            <i class="fas fa-user-plus me-2" style="color: #102E6C;"></i>
                            Como me cadastro no sistema?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted bg-white">
                            Clique no botão <strong>"Cadastre-se Aqui"</strong> no topo da página e preencha o
                            formulário com seus dados pessoais, acadêmicos e de contato. É rápido e gratuito!
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0 mb-3 shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold bg-light" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq2">
                            <i class="fas fa-clipboard-list me-2" style="color: #19B755;"></i>
                            Posso me inscrever em vários processos?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted bg-white">
                            Sim! Você pode se inscrever em <strong>quantos processos seletivos desejar</strong>, desde
                            que atenda aos requisitos específicos de cada um deles.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0 mb-3 shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold bg-light" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq3">
                            <i class="fas fa-chart-line me-2" style="color: #102E6C;"></i>
                            Como acompanho minhas inscrições?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted bg-white">
                            Após fazer login, acesse o menu <strong>"Minhas Inscrições"</strong> para visualizar todas
                            as suas candidaturas, status atualizados e próximas etapas.
                        </div>
                    </div>
                </div>

                <div class="accordion-item border-0 shadow-sm">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold bg-light" type="button"
                            data-bs-toggle="collapse" data-bs-target="#faq4">
                            <i class="fas fa-mobile-alt me-2" style="color: #19B755;"></i>
                            Posso acessar pelo celular?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted bg-white">
                            <strong>Sim!</strong> O portal é totalmente responsivo e otimizado para dispositivos móveis.
                            Acesse de qualquer lugar, a qualquer hora.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Final -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-lg border-0 text-center overflow-hidden"
                style="background: linear-gradient(135deg, #102E6C 0%, #1a3a8a 100%);">
                <div class="card-body p-5">
                    <h2 class="text-white fw-bold mb-3">
                        <i class="fas fa-star me-2"></i>Pronto para Começar sua Jornada?
                    </h2>
                    <p class="text-white lead mb-4">
                        Cadastre-se agora e tenha acesso às oportunidades de estágio disponíveis!
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center">
                        @guest
                            <a href="{{ route('novo-estagiario-ajax-create') }}"
                                class="btn btn-light btn-lg px-5 shadow-sm">
                                <i class="fas fa-user-plus me-2"></i>Criar Conta
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-5">
                                <i class="fas fa-sign-in-alt me-2"></i>Já Tenho Conta
                            </a>
                        @else
                        <a href="{{ route('processos-seletivos.publicos') }}"
                            class="btn btn-light btn-lg px-5 shadow-sm">
                            <i class="fas fa-briefcase me-2"></i>Ver Processos Disponíveis
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos customizados -->
<style>
    /* Animações de entrada */
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }
    }

    /* Hero Section */
    .hero-section {
        position: relative;
        overflow: hidden;
    }

    /* Transições suaves do carrossel - CORRIGIDO */
    .carousel-fade .carousel-item {
        opacity: 0;
        transition: opacity 0.8s ease-in-out;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: block !important;
    }

    .carousel-fade .carousel-item.active {
        opacity: 1;
        position: relative;
        z-index: 2;
    }

    /* Remove animações inline que causam conflito */
    .carousel-item * {
        animation: none !important;
    }

    /* Cards com Hover */
    .hover-lift-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .hover-lift-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 3rem rgba(16, 46, 108, 0.2) !important;
    }

    .hover-lift-card:hover .btn {
        background-color: #1a3a8a !important;
        transform: translateX(4px);
    }

    /* Backdrop blur effect */
    .backdrop-blur {
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    /* Controles do Carrossel Customizados */
    .carousel-control-custom {
        z-index: 10;
        width: auto;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }

    /* Mostrar setas quando hover sobre o carrossel */
    .hero-carousel-wrapper:hover .carousel-control-custom,
    #heroCarousel:hover .carousel-control-custom {
        opacity: 1;
        visibility: visible;
    }

    .carousel-arrow-custom {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        background: rgba(16, 46, 108, 0.85);
        border: 2px solid rgba(236, 208, 11, 0.6);
        border-radius: 50%;
        color: white;
        font-size: 20px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    }

    .carousel-control-custom:hover .carousel-arrow-custom {
        background: rgba(236, 208, 11, 0.95);
        border-color: rgba(236, 208, 11, 1);
        color: #102E6C;
        transform: scale(1.1);
        box-shadow: 0 6px 16px rgba(236, 208, 11, 0.4);
    }

    /* Posicionamento responsivo das setas */
    .carousel-control-prev {
        left: 20px;
    }

    .carousel-control-next {
        right: 20px;
    }

    @media (max-width: 768px) {
        .carousel-arrow-custom {
            width: 40px;
            height: 40px;
            font-size: 16px;
        }

        .carousel-control-prev {
            left: 10px;
        }

        .carousel-control-next {
            right: 10px;
        }
    }

    /* Efeito hover nos cards */
    .hover-lift {
        transition: all 0.3s ease;
    }

    .hover-lift:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, .175) !important;
    }

    /* Melhorar accordion */
    .accordion-button:not(.collapsed) {
        background-color: #102E6C;
        color: white;
    }

    .accordion-button:focus {
        box-shadow: none;
        border-color: rgba(0, 0, 0, .125);
    }

    /* Customizar indicators do carrossel */
    .carousel-indicators button {
        width: 12px !important;
        height: 12px !important;
        border-radius: 50% !important;
        background-color: white;
        opacity: 0.5;
        border: none;
        transition: all 0.3s ease;
        padding: 0;
        margin: 0 4px;
    }

    .carousel-indicators button.active {
        opacity: 1;
        background-color: white;
        width: 12px !important;
        height: 12px !important;
    }

    /* Responsividade extra para mobile */
    @media (max-width: 768px) {
        .hero-section {
            min-height: 0 !important;
            padding: 0;
        }

        .hero-pattern,
        .hero-section>div[style*="position: absolute"] {
            display: none !important;
        }

        .hero-carousel-wrapper {
            height: auto !important;
            padding: 0;
        }

        #heroCarousel,
        #heroCarousel .carousel-inner {
            height: auto !important;
        }

        .carousel-fade .carousel-item {
            position: relative;
            opacity: 1;
            height: auto !important;
            display: none !important;
        }

        .carousel-fade .carousel-item.active {
            position: relative;
            display: block !important;
        }

        .carousel-overlay {
            position: relative !important;
            height: auto !important;
        }

        .carousel-item .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .hero-illustration {
            display: none;
        }

        .display-3 {
            font-size: 2rem;
        }

        .display-4 {
            font-size: 1.5rem;
        }

        .lead {
            font-size: 1rem;
        }

        .carousel-item {
            padding: 24px 16px;
        }

        .carousel-item .d-flex {
            height: auto !important;
        }

        .hero-carousel-wrapper .btn-lg {
            width: 100%;
        }

        #heroCarousel .carousel-indicators {
            position: static;
            margin-top: 12px;
            margin-bottom: 0;
        }

        #heroCarousel .carousel-indicators button {
            width: 10px;
            height: 10px;
        }

        #heroCarousel .carousel-indicators button.active {
            width: 10px;
            height: 10px;
        }
    }

    @media (max-width: 576px) {
        .display-3 {
            font-size: 1.5rem;
        }

        .lead {
            font-size: 0.95rem;
        }

        .hero-carousel-wrapper {
            min-height: auto;
        }

        #heroCarousel {
            height: auto;
        }

        .carousel-control-custom {
            display: none;
        }

        #heroCarousel .carousel-indicators {
            margin-top: 12px;
        }

        .carousel-item .btn-lg {
            padding-left: 1.25rem;
            padding-right: 1.25rem;
            font-size: 0.95rem;
        }

        .carousel-control-prev,
        .carousel-control-next {
            top: 50%;
            transform: translateY(-50%);
        }
    }

    /* Suavizar transições */
    * {
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Botões com efeito */
    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    /* Gradientes nos ícones */
    .icon-gradient {
        background: linear-gradient(135deg, #102E6C, #1a3a8a);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Background da página */
    body {
        background-color: #ffffff;
        background-attachment: fixed;
    }

    .page-ambient-bg {
        opacity: 1;
    }

    @media (max-width: 768px) {
        .page-ambient-bg {
            opacity: 0.75;
        }
    }
</style>
@endsection