@extends('layouts.main')

@section('title', 'Detalhes do Contrato')

@section('content')

    @include('components.modal-sistema')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="color: #2d3748; font-weight: 700;">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                    class="bi bi-file-earmark-text me-2" viewBox="0 0 16 16">
                    <path
                        d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5" />
                    <path
                        d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z" />
                </svg>
                Detalhes do Contrato
            </h2>
            <p class="text-muted mb-0">Termo nº {{ $termo->numero_termo }}/{{ $termo->ano_termo }}</p>
        </div>
        <a href="{{ route('estagiario.contratos') }}" class="btn btn-outline-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-1"
                viewBox="0 0 16 16">
                <path fill-rule="evenodd"
                    d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
            </svg>
            Voltar
        </a>
    </div>

    <hr>

    <!-- Status do Contrato -->
    @php
        $status = $termo->rescisao ? 'Rescindido' : 'Ativo';
        $statusColor = $status === 'Ativo' ? '#27ae60' : '#e74c3c';
        $statusBg = $status === 'Ativo' ? '#e8f5e9' : '#ffebee';
        $statusIcon = $status === 'Ativo' ? 'bi-check-circle-fill' : 'bi-x-circle-fill';
    @endphp

    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-header text-white"
            style="background: linear-gradient(135deg, #102E6C 0%, #1a4d8f 100%); border-radius: 15px 15px 0 0 !important; padding: 20px;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1" style="font-weight: 600;">
                        {{ $termo->empresa ? $termo->empresa->nome_empresa : 'Empresa não especificada' }}
                    </h5>
                    <small style="opacity: 0.9;">Termo nº {{ $termo->numero_termo }}/{{ $termo->ano_termo }}</small>
                </div>
                <span class="badge" style="background: rgba(255,255,255,0.25); font-size: 0.95rem; padding: 8px 16px;">
                    {{ $status }}
                </span>
            </div>
        </div>
        <div class="card-body p-4">
            <!-- Alert de Status -->
            <div class="alert d-flex align-items-center mb-4" role="alert"
                style="background: {{ $statusBg }}; border-left: 4px solid {{ $statusColor }}; border-radius: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="{{ $statusColor }}"
                    class="bi {{ $statusIcon }} me-3" viewBox="0 0 16 16">
                    @if($status === 'Ativo')
                        <path
                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                    @else
                        <path
                            d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293z" />
                    @endif
                </svg>
                <div>
                    <h6 class="mb-1" style="color: {{ $statusColor }}; font-weight: 600;">
                        Status: {{ $status }}
                    </h6>
                    @if($termo->rescisao)
                        <p class="mb-0" style="color: #555; font-size: 0.9rem;">
                            <strong>Data da Rescisão:</strong>
                            {{ \Carbon\Carbon::parse($termo->rescisao->data_rescisao)->format('d/m/Y') }}
                            <br>
                            <strong>Motivo:</strong> {{ $termo->rescisao->motivo }}
                        </p>
                    @else
                        <p class="mb-0" style="color: #555; font-size: 0.9rem;">
                            Seu contrato está ativo e em vigência.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Informações Principais em Grid Compacto -->
            <div class="row g-2 mb-3">
                <!-- Data de Início -->
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-2" style="background: #e8f5e9; border-radius: 8px;">
                        <div class="d-flex align-items-center mb-1">
                            <div class="me-1"
                                style="width: 28px; height: 28px; background: #27ae60; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="white"
                                    class="bi bi-calendar-check" viewBox="0 0 16 16">
                                    <path
                                        d="M10.854 7.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 9.793l2.646-2.647a.5.5 0 0 1 .708 0" />
                                    <path
                                        d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                                </svg>
                            </div>
                            <small class="text-muted" style="font-weight: 600; font-size: 0.65rem;">INÍCIO</small>
                        </div>
                        <p class="mb-0" style="color: #2d3748; font-weight: 700; font-size: 0.85rem;">
                            {{ $termo->data_inicio_estagio ? \Carbon\Carbon::parse($termo->data_inicio_estagio)->format('d/m/Y') : '-' }}
                        </p>
                    </div>
                </div>

                <!-- Data de Término -->
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-2" style="background: #ffebee; border-radius: 8px;">
                        <div class="d-flex align-items-center mb-1">
                            <div class="me-1"
                                style="width: 28px; height: 28px; background: #e74c3c; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="white"
                                    class="bi bi-calendar-x" viewBox="0 0 16 16">
                                    <path
                                        d="M6.146 7.146a.5.5 0 0 1 .708 0L8 8.293l1.146-1.147a.5.5 0 1 1 .708.708L8.707 9l1.147 1.146a.5.5 0 0 1-.708.708L8 9.707l-1.146 1.147a.5.5 0 0 1-.708-.708L7.293 9 6.146 7.854a.5.5 0 0 1 0-.708" />
                                    <path
                                        d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                                </svg>
                            </div>
                            <small class="text-muted" style="font-weight: 600; font-size: 0.65rem;">TÉRMINO</small>
                        </div>
                        <p class="mb-0" style="color: #2d3748; font-weight: 700; font-size: 0.85rem;">
                            {{ $termo->data_fim_estagio ? \Carbon\Carbon::parse($termo->data_fim_estagio)->format('d/m/Y') : '-' }}
                        </p>
                    </div>
                </div>

                <!-- Valor da Bolsa -->
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="p-2" style="background: #e3f2fd; border-radius: 8px;">
                        <div class="d-flex align-items-center mb-1">
                            <div class="me-1"
                                style="width: 28px; height: 28px; background: #102E6C; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="white"
                                    class="bi bi-currency-dollar" viewBox="0 0 16 16">
                                    <path
                                        d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73z" />
                                </svg>
                            </div>
                            <small class="text-muted" style="font-weight: 600; font-size: 0.65rem;">BOLSA</small>
                        </div>
                        <p class="mb-0" style="color: #2d3748; font-weight: 700; font-size: 0.85rem;">
                            R$ {{ number_format($termo->valor_bolsa ?? 0, 2, ',', '.') }}
                        </p>
                    </div>
                </div>

                <!-- Auxílio Transporte -->
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="p-2" style="background: #f3e5f5; border-radius: 8px;">
                        <div class="d-flex align-items-center mb-1">
                            <div class="me-1"
                                style="width: 28px; height: 28px; background: #9c27b0; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="white"
                                    class="bi bi-bus-front" viewBox="0 0 16 16">
                                    <path
                                        d="M5 11a1 1 0 1 1-2 0 1 1 0 0 1 2 0m8 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0m-6-1a1 1 0 1 0 0 2h2a1 1 0 1 0 0-2zm1-6c-1.876 0-3.426.109-4.552.226A.5.5 0 0 0 3 4.723v3.554a.5.5 0 0 0 .448.497C4.574 8.891 6.124 9 8 9s3.426-.109 4.552-.226A.5.5 0 0 0 13 8.277V4.723a.5.5 0 0 0-.448-.497A44 44 0 0 0 8 4m0-1c-1.837 0-3.353.107-4.448.22a.5.5 0 1 1-.104-.994A44 44 0 0 1 8 2c1.876 0 3.426.109 4.552.226a.5.5 0 1 1-.104.994A43 43 0 0 0 8 3" />
                                    <path
                                        d="M15 8a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1V2.64c0-1.188-.845-2.232-2.064-2.372A44 44 0 0 0 8 0C5.9 0 4.208.136 3.064.268 1.845.408 1 1.452 1 2.64V4a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1v3.5c0 .818.393 1.544 1 2v2a.5.5 0 0 0 .5.5h2a.5.5 0 0 0 .5-.5V14h6v1.5a.5.5 0 0 0 .5.5h2a.5.5 0 0 0 .5-.5v-2c.607-.456 1-1.182 1-2zM8 1c2.056 0 3.71.134 4.822.261.676.078 1.178.66 1.178 1.379v8.86a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 11.5V2.64c0-.72.502-1.301 1.178-1.379A43 43 0 0 1 8 1" />
                                </svg>
                            </div>
                            <small class="text-muted" style="font-weight: 600; font-size: 0.65rem;">TRANSPORTE</small>
                        </div>
                        <p class="mb-0" style="color: #2d3748; font-weight: 700; font-size: 0.85rem;">
                            R$ {{ number_format($termo->auxilio_transporte ?? 0, 2, ',', '.') }}
                        </p>
                    </div>
                </div>

                <!-- Recesso Disponível -->
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="p-2" style="background: #fff3e0; border-radius: 8px;">
                        <div class="d-flex align-items-center mb-1">
                            <div class="me-1"
                                style="width: 28px; height: 28px; background: #f39c12; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="white"
                                    class="bi bi-calendar-heart" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd"
                                        d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4zM1 14V4h14v10a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1m7-6.507c1.664-1.711 5.825 1.283 0 5.132-5.825-3.85-1.664-6.843 0-5.132" />
                                </svg>
                            </div>
                            <small class="text-muted" style="font-weight: 600; font-size: 0.65rem;">RECESSO</small>
                        </div>
                        @php
                            $hoje = \Carbon\Carbon::today();
                            $inicioContrato = \Carbon\Carbon::parse($termo->data_inicio_estagio);
                            $diasTrabalhados = max(0, $inicioContrato->diffInDays($hoje));
                            $recessoAcumulado = (30 * $diasTrabalhados) / 360;
                            $saldoAtual = (int) ($termo->saldo_recesso ?? 30);
                            $jaUsado = 30 - $saldoAtual;
                            $recessoDisponivel = max(0, $recessoAcumulado - $jaUsado);
                            $recessoDisponivelInt = (int) round($recessoDisponivel);
                        @endphp
                        <p class="mb-0" style="color: #2d3748; font-weight: 700; font-size: 0.85rem;">
                            {{ $recessoDisponivelInt }} {{ $recessoDisponivelInt === 1 ? 'dia' : 'dias' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Detalhes do Estágio -->
            <div class="card border-0 mb-3" style="background: #f8f9fa; border-radius: 10px;">
                <div class="card-body p-3">
                    <h6 class="mb-2" style="color: #102E6C; font-weight: 700; font-size: 0.95rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                            class="bi bi-info-circle me-1" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                            <path
                                d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0" />
                        </svg>
                        Informações do Estágio
                    </h6>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1" style="font-weight: 600; font-size: 0.9rem;">Unidade
                                Concedente</small>
                            <p class="mb-2" style="color: #2d3748; font-size: 0.9rem;">
                                {{ $termo->empresa ? $termo->empresa->nome_empresa : 'Não especificado' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1" style="font-weight: 600; font-size: 0.9rem;">Instituição
                                de Ensino</small>
                            <p class="mb-2" style="color: #2d3748; font-size: 0.9rem;">
                                {{ $termo->escola ? $termo->escola->nome_escola : 'Não especificado' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1"
                                style="font-weight: 600; font-size: 0.9rem;">Supervisor</small>
                            <p class="mb-2" style="color: #2d3748; font-size: 0.9rem;">
                                {{ $termo->supervisor ? $termo->supervisor->nome_supervisor : 'Não especificado' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1"
                                style="font-weight: 600; font-size: 0.9rem;">Orientador</small>
                            <p class="mb-2" style="color: #2d3748; font-size: 0.9rem;">
                                {{ $termo->nome_orientador ?? 'Não especificado' }}
                            </p>
                        </div>
                        @if($termo->local)
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1"
                                    style="font-weight: 600; font-size: 0.9rem;">Local</small>
                                <p class="mb-2" style="color: #2d3748; font-size: 0.9rem;">{{ $termo->local }}</p>
                            </div>
                        @endif
                        @if($termo->lotacao)
                            <div class="col-md-6">
                                <small class="text-muted d-block mb-1"
                                    style="font-weight: 600; font-size: 0.9rem;">Lotação</small>
                                <p class="mb-2" style="color: #2d3748; font-size: 0.9rem;">{{ $termo->lotacao }}</p>
                            </div>
                        @endif
                        <div class="col-md-6">
                            <small class="text-muted d-block mb-1"
                                style="font-weight: 600; font-size: 0.90rem;">Horário</small>
                            <p class="mb-2" style="color: #2d3748; font-size: 0.9rem;">
                                {{ $termo->horario ?? 'Não especificado' }} horas
                            </p>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block mb-1" style="font-weight: 600; font-size: 0.9rem;">Descrição
                                das Atividades</small>
                            <p class="mb-0" style="color: #2d3748;  font-size: 0.9rem;">
                                {{ $termo->desc_atividades ?? 'Não especificado' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Histórico de Recessos -->
            @if($termo->concessoesRecesso && $termo->concessoesRecesso->where('status', 'ativo')->count() > 0)
                <div class="card border-0 mb-3" style="background: #e8f5e9; border-radius: 10px;">
                    <div class="card-body p-3">
                        <h6 class="mb-2" style="color: #27ae60; font-weight: 700; font-size: 0.95rem;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                class="bi bi-calendar-heart me-1" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4zM1 14V4h14v10a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1m7-6.507c1.664-1.711 5.825 1.283 0 5.132-5.825-3.85-1.664-6.843 0-5.132" />
                            </svg>
                            Histórico de Recessos
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0"
                                style="background: white; border-radius: 6px; overflow: hidden; font-size: 0.85rem;">
                                <thead style="background: #27ae60; color: white;">
                                    <tr>
                                        <th style="border: none; padding: 8px;">Concessão</th>
                                        <th style="border: none; padding: 8px;">Início</th>
                                        <th style="border: none; padding: 8px;">Fim</th>
                                        <th style="border: none; padding: 8px;">Dias</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($termo->concessoesRecesso->where('status', 'ativo')->sortByDesc('data_concessao') as $concessao)
                                        <tr>
                                            <td style="padding: 8px;">
                                                {{ \Carbon\Carbon::parse($concessao->data_concessao)->format('d/m/Y') }}
                                            </td>
                                            <td style="padding: 8px;">
                                                {{ \Carbon\Carbon::parse($concessao->data_inicio_recesso)->format('d/m/Y') }}
                                            </td>
                                            <td style="padding: 8px;">
                                                {{ \Carbon\Carbon::parse($concessao->data_fim_recesso)->format('d/m/Y') }}
                                            </td>
                                            <td style="padding: 8px;"><strong>{{ $concessao->dias_concedidos }}
                                                    {{ $concessao->dias_concedidos === 1 ? 'dia' : 'dias' }}</strong></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Avaliações do Estágio -->
            <div class="card border-0 mb-3"
                style="background: linear-gradient(135deg, #fffaf0 0%, #fff 100%); border-radius: 10px; border: 1px solid #f6e3b4;">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <h6 class="mb-1" style="color: #8a5b00; font-weight: 700; font-size: 0.95rem;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                    class="bi bi-star-fill me-1" viewBox="0 0 16 16">
                                    <path
                                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792a.513.513 0 0 1 .924 0l2.184 4.327 4.898.696c.441.062.612.636.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187z" />
                                </svg>
                                Avaliações do Estágio
                            </h6>
                            <p class="text-muted mb-0" style="font-size: 0.85rem;">
                                Gere a avaliação do seu contrato, copie o link para o supervisor e acompanhe o status da resposta.
                            </p>
                        </div>

                        <button type="button" class="btn btn-sm text-white" data-bs-toggle="modal"
                            data-bs-target="#modalGerarAvaliacaoEstagiario"
                            style="background: linear-gradient(135deg, #f39c12 0%, #d68910 100%); font-weight: 600;"
                            {{ $possuiAvaliacaoPendente ? 'disabled' : '' }}>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                                class="bi bi-plus-circle me-1" viewBox="0 0 16 16">
                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                            </svg>
                            Gerar Avaliação
                        </button>
                    </div>

                    @if($possuiAvaliacaoPendente)
                        <div class="alert alert-warning py-2 px-3 mb-3" role="alert"
                            style="border-left: 4px solid #f39c12; border-radius: 8px; font-size: 0.85rem;">
                            Existe uma avaliação pendente para este contrato. Uma nova avaliação só poderá ser criada depois que a anterior for respondida.
                        </div>
                    @endif

                    @if($avaliacoes->isEmpty())
                        <div class="alert alert-light border mb-0" role="alert" style="font-size: 0.85rem;">
                            Nenhuma avaliação foi criada para este contrato até o momento.
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($avaliacoes as $avaliacao)
                                <div class="col-lg-6">
                                    <div class="h-100 p-3"
                                        style="background: white; border: 1px solid #f1e2bf; border-radius: 10px; box-shadow: 0 2px 10px rgba(16, 46, 108, 0.05);">
                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                            <div>
                                                <span class="badge"
                                                    style="background: {{ $avaliacao->tipo_avaliacao === 'seis_meses' ? '#e8f1ff' : '#fff1df' }}; color: {{ $avaliacao->tipo_avaliacao === 'seis_meses' ? '#102E6C' : '#b26a00' }}; font-size: 0.78rem;">
                                                    {{ $avaliacao->tipo_avaliacao === 'seis_meses' ? 'Avaliação de 6 meses' : 'Avaliação de finalização' }}
                                                </span>
                                            </div>
                                            <span class="badge"
                                                style="background: {{ $avaliacao->status === 'respondida' ? '#dff3e4' : '#fff3cd' }}; color: {{ $avaliacao->status === 'respondida' ? '#1e7e34' : '#856404' }}; font-size: 0.78rem;">
                                                {{ $avaliacao->status === 'respondida' ? 'Respondida' : 'Pendente' }}
                                            </span>
                                        </div>

                                        <div class="small text-muted mb-2">
                                            <div><strong>Criada em:</strong> {{ $avaliacao->created_at?->format('d/m/Y H:i') ?? '-' }}</div>
                                            <div><strong>Supervisor:</strong> {{ optional($avaliacao->supervisor)->nome_supervisor ?? optional($termo->supervisor)->nome_supervisor ?? 'Não informado' }}</div>
                                            @if($avaliacao->respondida_em)
                                                <div><strong>Respondida em:</strong> {{ $avaliacao->respondida_em->format('d/m/Y H:i') }}</div>
                                                <div><strong>Respondida por:</strong> {{ $avaliacao->respondida_por ?? 'Não informado' }}</div>
                                            @endif
                                        </div>

                                        @if($avaliacao->status === 'pendente')
                                            <p class="mb-3" style="font-size: 0.84rem; color: #6c757d;">
                                                Compartilhe o link com o supervisor. Se o link anterior falhar, gere um novo para invalidar o anterior.
                                            </p>
                                            <div class="d-flex flex-wrap gap-2">
                                                <button type="button" class="btn btn-sm btn-success btn-link-avaliacao"
                                                    data-url="{{ route('estagiario.avaliacoes.gerar-link', $avaliacao) }}"
                                                    data-acao="copiar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                                                        class="bi bi-share me-1" viewBox="0 0 16 16">
                                                        <path d="M13.5 1a1.5 1.5 0 1 0 .848 2.736L8.41 7.174a1.5 1.5 0 0 0 0 1.652l5.937 3.438a1.5 1.5 0 1 0 .503-.864L8.913 7.962a1.5 1.5 0 0 0 0-.924l5.937-3.438A1.5 1.5 0 0 0 13.5 1" />
                                                        <path d="M3.5 5a1.5 1.5 0 1 0 .85 2.736l5.936 3.438a1.5 1.5 0 1 0 .503-.864L4.852 6.872A1.5 1.5 0 0 0 3.5 5m0 6a1.5 1.5 0 1 0 .85 2.736l5.936-3.438a1.5 1.5 0 1 0-.503-.864L3.847 12.87A1.5 1.5 0 0 0 3.5 11" />
                                                    </svg>
                                                    Copiar link
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-warning btn-link-avaliacao"
                                                    data-url="{{ route('estagiario.avaliacoes.regenerar-link', $avaliacao) }}"
                                                    data-acao="regenerar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                                                        class="bi bi-arrow-repeat me-1" viewBox="0 0 16 16">
                                                        <path d="M2 2.5A.5.5 0 0 1 2.5 2H6a.5.5 0 0 1 0 1H3.707l1.147 1.146a.5.5 0 0 1-.708.708l-2-2A.5.5 0 0 1 2 2.5" />
                                                        <path d="M2.5 8a.5.5 0 0 1 .5.5A5.5 5.5 0 0 0 8.5 14a5.5 5.5 0 0 0 4.473-2.293.5.5 0 1 1 .81.586A6.5 6.5 0 0 1 2 8.5.5.5 0 0 1 2.5 8" />
                                                        <path d="M14 13.5a.5.5 0 0 1-.5.5H10a.5.5 0 0 1 0-1h2.293l-1.147-1.146a.5.5 0 0 1 .708-.708l2 2a.5.5 0 0 1 .146.354" />
                                                        <path d="M13.5 8a.5.5 0 0 1-.5-.5A5.5 5.5 0 0 0 7.5 2a5.5 5.5 0 0 0-4.473 2.293.5.5 0 1 1-.81-.586A6.5 6.5 0 0 1 14 7.5a.5.5 0 0 1-.5.5" />
                                                    </svg>
                                                    Gerar novo link
                                                </button>
                                            </div>
                                        @else
                                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                                <div class="alert alert-success py-2 px-3 mb-0 flex-grow-1" role="alert"
                                                    style="font-size: 0.84rem; border-radius: 8px; min-width: 220px;">
                                                    Esta avaliação já foi respondida pelo supervisor.
                                                </div>
                                                <a href="{{ route('estagiario.avaliacoes.pdf', $avaliacao) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                                                        class="bi bi-file-earmark-pdf me-1" viewBox="0 0 16 16">
                                                        <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zM9.5 3A1.5 1.5 0 0 0 11 4.5h2" />
                                                        <path d="M4.603 12.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787.376-.221.83-.42 1.482-.645a20 20 0 0 0 1.062-2.227 7.3 7.3 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.187-.012.395-.047.614-.084.51-.27 1.134-.52 1.794a11 11 0 0 0 .98 1.686 5.8 5.8 0 0 1 1.334.05c.364.065.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.86.86 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.7 5.7 0 0 1-.911-.95 11.6 11.6 0 0 0-1.997.406 11.3 11.3 0 0 1-1.021 1.51c-.29.35-.608.655-.926.787a.8.8 0 0 1-.58.029" />
                                                    </svg>
                                                    Baixar PDF
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Geração de Recibo -->
            <div class="card border-0 mb-3"
                style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-radius: 10px;">
                <div class="card-body p-3">
                    <h6 class="mb-2" style="color: #102E6C; font-weight: 700; font-size: 0.95rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                            class="bi bi-receipt me-1" viewBox="0 0 16 16">
                            <path
                                d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27m.217 1.338L2 2.118v11.764l.137.274.51-.51a.5.5 0 0 1 .707 0l.646.647.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.646.646.646-.646a.5.5 0 0 1 .708 0l.509.509.137-.274V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0z" />
                            <path
                                d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5m8-6a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5" />
                        </svg>
                        Gerar Recibo de Pagamento
                    </h6>

                    <form id="formGerarRecibo" action="{{ route('estagiario.gerar.recibo', $termo->id_termo) }}"
                        method="GET" target="_blank">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-5">
                                <label for="mes_referencia" class="form-label mb-1"
                                    style="font-weight: 600; color: #2d3748; font-size: 0.85rem;">
                                    Mês
                                </label>
                                <select class="form-select form-select-sm" id="mes_referencia" name="mes_referencia"
                                    required>
                                    <option value="">Selecione</option>
                                    <option value="1">Janeiro</option>
                                    <option value="2">Fevereiro</option>
                                    <option value="3">Março</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Maio</option>
                                    <option value="6">Junho</option>
                                    <option value="7">Julho</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Setembro</option>
                                    <option value="10">Outubro</option>
                                    <option value="11">Novembro</option>
                                    <option value="12">Dezembro</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="ano_referencia" class="form-label mb-1"
                                    style="font-weight: 600; color: #2d3748; font-size: 0.85rem;">
                                    Ano
                                </label>
                                <select class="form-select form-select-sm" id="ano_referencia" name="ano_referencia"
                                    required>
                                    <option value="">Selecione</option>
                                    @php
                                        $anoAtual = date('Y');
                                        $anoInicio = 2020;
                                    @endphp
                                    @for ($ano = $anoAtual; $ano >= $anoInicio; $ano--)
                                        <option value="{{ $ano }}">{{ $ano }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-sm text-white w-100"
                                    style="background: linear-gradient(135deg, #27ae60 0%, #1f7234 100%); font-weight: 600;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                                        class="bi bi-file-earmark-pdf me-1" viewBox="0 0 16 16">
                                        <path
                                            d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z" />
                                        <path
                                            d="M4.603 14.087a.8.8 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.7 7.7 0 0 1 1.482-.645 20 20 0 0 0 1.062-2.227 7.3 7.3 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a11 11 0 0 0 .98 1.686 5.8 5.8 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.86.86 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.7 5.7 0 0 1-.911-.95 11.7 11.7 0 0 0-1.997.406 11.3 11.3 0 0 1-1.021 1.51c-.29.35-.608.655-.926.787a.8.8 0 0 1-.58.029m1.379-1.901q-.25.115-.459.238c-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361q.016.032.026.044l.035-.012c.137-.056.355-.235.635-.572a8 8 0 0 0 .45-.606m1.64-1.33a13 13 0 0 1 1.01-.193 12 12 0 0 1-.51-.858 21 21 0 0 1-.5 1.05zm2.446.45q.226.244.435.41c.24.19.407.253.498.256a.1.1 0 0 0 .07-.015.3.3 0 0 0 .094-.125.44.44 0 0 0 .059-.2.1.1 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a4 4 0 0 0-.612-.053zM8.078 7.8a7 7 0 0 0 .2-.828q.046-.282.038-.465a.6.6 0 0 0-.032-.198.5.5 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822q.036.167.09.346z" />
                                    </svg>
                                    Gerar PDF
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="alert alert-info mt-2 mb-0 py-2 px-3 d-flex align-items-start" role="alert"
                        style="border-left: 3px solid #17a2b8; background: #d1ecf1; border-radius: 6px; font-size: 0.8rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#17a2b8"
                            class="bi bi-info-circle-fill me-2 mt-1" viewBox="0 0 16 16" style="flex-shrink: 0;">
                            <path
                                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2" />
                        </svg>
                        <small style="color: #0c5460;">
                            O recibo só estará disponível se a folha de pagamento do mês/ano já tiver sido gerada.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Botão para baixar PDF -->
            <a href="{{ route('termos.gerarPdf', $termo->id_termo) }}"
                class="btn btn-outline-primary d-inline-flex align-items-center" target="_blank"
                style="border-radius: 8px; font-weight: 600; font-size: 0.9rem;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-file-pdf me-2" viewBox="0 0 16 16">
                    <path
                        d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1" />
                    <path
                        d="M4.603 12.087a.8.8 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.7 7.7 0 0 1 1.482-.645 20 20 0 0 0 1.062-2.227 7.3 7.3 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.187-.012.395-.047.614-.084.51-.27 1.134-.52 1.794a11 11 0 0 0 .98 1.686 5.8 5.8 0 0 1 1.334.05c.364.065.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.86.86 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.7 5.7 0 0 1-.911-.95 11.6 11.6 0 0 0-1.997.406 11.3 11.3 0 0 1-1.021 1.51c-.29.35-.608.655-.926.787a.8.8 0 0 1-.58.029m1.379-1.901q-.25.115-.459.238c-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361q.016.032.026.044l.035-.012c.137-.056.355-.235.635-.572a8 8 0 0 0 .45-.606m1.64-1.33a13 13 0 0 1 1.01-.193 12 12 0 0 1-.51-.858 21 21 0 0 1-.5 1.05zm2.446.45q.226.244.435.41c.24.19.407.253.498.256a.1.1 0 0 0 .07-.015.3.3 0 0 0 .094-.125.44.44 0 0 0 .059-.2.1.1 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a4 4 0 0 0-.612-.053zM8.078 5.8a7 7 0 0 0 .2-.828q.046-.282.038-.465a.6.6 0 0 0-.032-.198.5.5 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822q.036.167.09.346z" />
                </svg>
                Baixar Contrato (PDF)
            </a>
        </div>
    </div>

    <div class="modal fade" id="modalLinkAvaliacao" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header" style="background: #102E6C; color: white;">
                    <h5 class="modal-title">Link da Avaliação</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted" style="font-size: 0.9rem;">
                        Encaminhe este link ao supervisor responsável pelo contrato.
                    </p>
                    <div class="input-group">
                        <input type="text" class="form-control" id="linkAvaliacaoInput" readonly>
                        <button class="btn btn-primary" type="button" id="copiarLinkAvaliacaoBtn">Copiar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalGerarAvaliacaoEstagiario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header" style="background: #f39c12; color: white;">
                    <h5 class="modal-title">Gerar Avaliação</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('estagiario.avaliacoes.gerar-manual') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="fk_id_termo" value="{{ $termo->id_termo }}">

                        <div class="mb-3">
                            <label for="tipo_avaliacao_estagiario" class="form-label">Tipo de avaliação</label>
                            <select name="tipo_avaliacao" id="tipo_avaliacao_estagiario" class="form-select" required>
                                <option value="">Selecione</option>
                                <option value="seis_meses">6 meses</option>
                                <option value="finalizacao">Finalização</option>
                            </select>
                        </div>

                        <div class="alert alert-light border mb-0" role="alert" style="font-size: 0.85rem;">
                            Depois de criada, a avaliação ficará disponível nesta tela com o link para compartilhamento.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning text-white">Gerar avaliação</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const botoesLink = document.querySelectorAll('.btn-link-avaliacao');
            const inputLink = document.getElementById('linkAvaliacaoInput');
            const botaoCopiar = document.getElementById('copiarLinkAvaliacaoBtn');
            const modalLink = document.getElementById('modalLinkAvaliacao');

            async function enviarAcaoLink(url, acao) {
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || 'Não foi possível processar a solicitação.');
                    }

                    inputLink.value = data.link;
                    bootstrap.Modal.getOrCreateInstance(modalLink).show();
                    mostrarSucesso(
                        acao === 'regenerar' ? 'Novo link gerado' : 'Link pronto para compartilhar',
                        data.message || 'O link está disponível para cópia.'
                    );
                } catch (error) {
                    mostrarErro('Falha ao processar avaliação', error.message || 'Não foi possível gerar o link.');
                }
            }

            botoesLink.forEach(function (botao) {
                botao.addEventListener('click', function () {
                    enviarAcaoLink(this.dataset.url, this.dataset.acao);
                });
            });

            botaoCopiar.addEventListener('click', async function () {
                if (!inputLink.value) {
                    return;
                }

                try {
                    if (navigator.clipboard && window.isSecureContext) {
                        await navigator.clipboard.writeText(inputLink.value);
                    } else {
                        inputLink.select();
                        document.execCommand('copy');
                    }

                    mostrarSucesso('Link copiado', 'O link foi copiado para a área de transferência.');
                } catch (error) {
                    mostrarErro('Falha ao copiar link', 'Copie o link manualmente e tente novamente.');
                }
            });
        });
    </script>

@endsection