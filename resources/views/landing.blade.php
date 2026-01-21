@extends('layouts.main')

@section('title', 'Portal do Estagiário - SIGE')

@section('content')
<div class="container-fluid py-4">
    <!-- Hero Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow-lg border-0 overflow-hidden"
                style="background: linear-gradient(135deg, #102E6C 0%, #0A1F4D 100%);">
                <div class="card-body text-white py-5 px-4">
                    <div class="row align-items-center">
                        <div class="col-lg-7 text-center text-lg-start mb-4 mb-lg-0">
                            <h1 class="display-4 fw-bold mb-3">Bem-vindo ao SIGE - EBCP</h1>
                            <p class="lead mb-4">Uma plataforma exclusiva para soluções de estágios.</p>
                            <div
                                class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                                <a href="{{ route('processos-seletivos.publicos') }}"
                                    class="btn btn-light btn-lg px-4 shadow-sm">
                                    <i class="fas fa-briefcase me-2"></i>Ver Processos Seletivos
                                </a>
                                @guest
                                    <a href="{{ route('novo-estagiario-ajax-create') }}"
                                        class="btn btn-outline-light btn-lg px-4">
                                        <i class="fas fa-user-plus me-2"></i>Cadastre-se Aqui
                                    </a>
                                @endguest
                            </div>
                        </div>
                        <div class="col-lg-5 text-center">
                            <!-- ESPAÇO PARA IMAGEM: 400x400px -->
                            <!-- Sugestão Canva: Ilustração de estagiário/estudante com notebook, cores vibrantes -->
                            <!-- Dimensão ideal: 400x400px (quadrado) ou 500x350px (retângulo) -->
                            <div class="bg-white bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center mx-auto"
                                style="height: 280px; max-width: 400px;">
                                <i class="fas fa-graduation-cap text-white" style="font-size: 120px; opacity: 0.3;"></i>
                                <!-- Após criar imagem, substituir por: -->
                                <!-- <img src="{{ asset('images/hero-estagiario.png') }}" alt="Estagiário" class="img-fluid rounded-3" style="max-height: 280px;"> -->
                            </div>
                            <p class="small text-white-50 mt-2 mb-0">💡 Adicione uma imagem 400x400px aqui</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Oportunidades -->
    <div class="row row-cols-1 row-cols-md-2 g-4 mb-5">
        <!-- Processos Seletivos -->
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

        <!-- Vagas de Estágio - EM BREVE -->
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
                                <i class="fas fa-lightbulb text-white" style="font-size: 80px;"></i>
                                <!-- Após criar imagem, substituir por: -->
                                <!-- <img src="{{ asset('images/tips-icon.png') }}" alt="Dicas" class="img-fluid rounded-circle" style="max-width: 200px;"> -->
                            </div>
                            <p class="small text-white-50 mt-2 mb-0">💡 Adicione uma imagem 300x300px aqui</p>
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

    /* Responsividade extra para mobile */
    @media (max-width: 576px) {
        .display-4 {
            font-size: 2rem;
        }

        .lead {
            font-size: 1rem;
        }
    }
</style>
@endsection