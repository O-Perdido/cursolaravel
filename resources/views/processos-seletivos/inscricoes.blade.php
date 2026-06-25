@extends('layouts.main')

@section('title', 'Inscrições do Processo Seletivo')

@section('content')

<style>
    /* Custom Style System */
    :root {
        --color-primary: #102e6c;
        --color-success: #19b755;
        --color-warning: #ecd00b;
        --color-danger: #dc3545;
        --color-primary-light: #e8eefc;
        --color-success-light: #e8f8ef;
        --color-warning-light: #fefbe7;
        --color-danger-light: #fdebee;
    }

    .custom-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04);
        transition: all 0.3s ease;
        background: #fff;
    }

    .header-card {
        background: linear-gradient(135deg, #102e6c 0%, #0a1f4d 100%);
        border: none;
        border-radius: 16px;
    }

    /* Metric Stats Cards */
    .metric-card {
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.03);
        box-shadow: 0 4px 15px rgba(0,0,0,0.015);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        overflow: hidden;
        position: relative;
        background: #fff;
    }

    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.05);
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }

    .metric-card.primary::before { background-color: var(--color-primary); }
    .metric-card.success::before { background-color: var(--color-success); }
    .metric-card.warning::before { background-color: var(--color-warning); }
    .metric-card.danger::before { background-color: var(--color-danger); }

    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .bg-primary-light { background-color: var(--color-primary-light); color: var(--color-primary); }
    .bg-success-light { background-color: var(--color-success-light); color: var(--color-success); }
    .bg-warning-light { background-color: var(--color-warning-light); color: #bfa00a; }
    .bg-danger-light { background-color: var(--color-danger-light); color: var(--color-danger); }

    /* Filters Box */
    .filters-box {
        background-color: #f8fafc;
        border-radius: 12px;
        border: 1px solid #eef2f6;
    }

    .form-control-custom {
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        padding: 0.6rem 1rem;
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }

    .form-control-custom:focus {
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(16, 46, 108, 0.15);
        outline: none;
    }

    /* Modern Table */
    .modern-table-wrapper {
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid #edf2f7;
    }

    .modern-table {
        margin-bottom: 0;
    }

    .modern-table thead th {
        background-color: #f8fafc;
        border-bottom: 2px solid #edf2f7;
        color: #4a5568;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        padding: 0.85rem 0.6rem;
    }

    .modern-table tbody tr {
        transition: all 0.2s ease;
    }

    .modern-table tbody tr:hover {
        background-color: #fcfdfe;
    }

    .modern-table tbody td {
        padding: 0.85rem 0.6rem;
        border-bottom: 1px solid #edf2f7;
        vertical-align: middle;
        font-size: 0.88rem;
    }

    .candidate-name-container {
        max-width: 210px;
        word-break: break-word;
    }

    /* Pill Badges */
    .status-badge {
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .status-badge.deferido {
        background-color: var(--color-success-light);
        color: var(--color-success);
    }

    .status-badge.indeferido {
        background-color: var(--color-danger-light);
        color: var(--color-danger);
    }

    .status-badge.inscrito {
        background-color: var(--color-warning-light);
        color: #bfa00a;
    }

    /* Monospace inscription code */
    .inscricao-code {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 700;
        color: var(--color-primary);
        background-color: #f1f5f9;
        padding: 5px 10px;
        border-radius: 6px;
        font-size: 0.82rem;
        border: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    /* Action Buttons */
    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        padding: 0;
        border: none;
        font-size: 0.85rem;
    }

    .action-btn.btn-approve {
        background-color: var(--color-success-light);
        color: var(--color-success);
    }

    .action-btn.btn-approve:hover {
        background-color: var(--color-success);
        color: #fff;
        transform: scale(1.1);
    }

    .action-btn.btn-reject {
        background-color: var(--color-danger-light);
        color: var(--color-danger);
    }

    .action-btn.btn-reject:hover {
        background-color: var(--color-danger);
        color: #fff;
        transform: scale(1.1);
    }

    .action-btn.btn-revert {
        background-color: var(--color-primary-light);
        color: var(--color-primary);
    }

    .action-btn.btn-revert:hover {
        background-color: var(--color-primary);
        color: #fff;
        transform: scale(1.1);
    }

    .action-btn.btn-delete {
        background-color: #f1f5f9;
        color: #64748b;
    }

    .action-btn.btn-delete:hover {
        background-color: #ef4444;
        color: #fff;
        transform: scale(1.1);
    }

    .hover-opacity:hover {
        opacity: 0.8;
    }
</style>

<div class="container-fluid py-3">
    <!-- Header Processo Card -->
    <div class="card custom-card header-card text-white mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div>
                    <span class="text-white-50 text-uppercase tracking-wider fw-bold small" style="font-size: 0.75rem; letter-spacing: 1px;">Processo Seletivo</span>
                    <h3 class="mb-1 fw-bold mt-1 text-white">{{ $processo->titulo }}</h3>
                    <div class="d-flex flex-wrap align-items-center gap-3 mt-2 text-white-50 small">
                        <span><i class="fas fa-hashtag me-1"></i> <strong>Número:</strong> {{ $processo->numero_processo }}</span>
                        <span><i class="fas fa-building me-1"></i> <strong>Empresa:</strong> {{ $processo->empresa->nome_empresa }}</span>
                        <span class="badge @switch($processo->status) @case('rascunho') bg-secondary @break @case('aberto') bg-success @break @case('inscricoes') bg-info @break @case('encerrado') bg-warning @break @case('finalizado') bg-dark @break @default bg-light text-dark @endswitch px-2.5 py-1.5 align-middle text-uppercase" style="font-size: 0.7rem; font-weight: 700; letter-spacing: 0.5px;">{{ $processo->status }}</span>
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    @if($config['pode_exportar'])
                        <button class="btn btn-light btn-sm px-3 py-2 fw-bold d-flex align-items-center gap-2 shadow-sm text-primary" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="fas fa-download"></i> Exportar Lista
                        </button>
                    @endif
                    <a href="{{ route('processos-seletivos.index') }}" class="btn btn-outline-light btn-sm px-3 py-2 fw-bold d-flex align-items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(!$config['pode_alterar_status'])
        <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-4" role="alert" style="border-radius: 12px; background-color: #f0f7ff; color: #1e3a8a;">
            <i class="fas fa-info-circle fa-lg text-primary"></i>
            <div>
                <strong>Modo de Visualização Empresa:</strong> Você está visualizando os inscritos deste processo seletivo. 
                Alterações de status são realizadas apenas pela administração do sistema.
                @if(!\App\Models\Configuracao::obter('processos_empresa_pode_exportar', true))
                    A exportação de relatórios está desabilitada.
                @endif
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Mini Dashboard Statistics -->
    <div class="row g-3 mb-4">
        <!-- Card Total -->
        <div class="col-6 col-md-3">
            <div class="card metric-card primary p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold d-block">Total Inscritos</span>
                        <h3 class="mb-0 fw-bold mt-1" style="color: var(--color-primary)">{{ $stats['total'] ?? 0 }}</h3>
                    </div>
                    <div class="metric-icon bg-primary-light">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card Deferidos -->
        <div class="col-6 col-md-3">
            <div class="card metric-card success p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold d-block">Deferidos</span>
                        <h3 class="mb-0 fw-bold mt-1 text-success">{{ $stats['deferidos'] ?? 0 }}</h3>
                    </div>
                    <div class="metric-icon bg-success-light">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card Indeferidos -->
        <div class="col-6 col-md-3">
            <div class="card metric-card danger p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold d-block">Indeferidos</span>
                        <h3 class="mb-0 fw-bold mt-1 text-danger">{{ $stats['indeferidos'] ?? 0 }}</h3>
                    </div>
                    <div class="metric-icon bg-danger-light">
                        <i class="fas fa-times-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card Pendentes (Inscritos) -->
        <div class="col-6 col-md-3">
            <div class="card metric-card warning p-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small fw-semibold d-block">Pendentes</span>
                        <h3 class="mb-0 fw-bold mt-1" style="color: #bfa00a;">{{ $stats['inscritos'] ?? 0 }}</h3>
                    </div>
                    <div class="metric-icon bg-warning-light">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="card custom-card mb-4">
        <div class="card-body p-4 filters-box">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="card-title mb-0 fs-6 fw-bold text-dark">
                    <i class="fas fa-sliders-h me-2 text-primary"></i>Buscar e Filtrar Inscritos
                </h5>
                @if(request()->anyFilled(['search', 'status', 'curso']))
                    <span class="badge bg-primary px-2 py-1 rounded-pill" style="font-size: 0.75rem;">Filtros Ativos</span>
                @endif
            </div>
            
            <form action="{{ route('processos-seletivos.inscricoes', $processo->id_processo) }}" method="GET">
                <div class="row g-3 align-items-end">
                    <!-- Busca Geral -->
                    <div class="col-12 col-md-4 col-lg-5">
                        <label class="form-label text-muted small fw-bold mb-1">Pesquisar por dados do candidato</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted px-3" style="border-radius: 8px 0 0 8px; border: 1.5px solid #e2e8f0;">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" name="search" class="form-control form-control-custom border-start-0 ps-1" 
                                   value="{{ request('search') }}" placeholder="Nome, CPF, e-mail ou Nº Inscrição..." 
                                   style="border-radius: 0 8px 8px 0;">
                        </div>
                    </div>
                    
                    <!-- Filtro de Status -->
                    <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                        <label class="form-label text-muted small fw-bold mb-1">Status da Inscrição</label>
                        @if($config['apenas_deferidos'])
                            <select class="form-select form-control-custom" disabled>
                                <option selected>✅ Apenas Deferidos</option>
                            </select>
                            <input type="hidden" name="status" value="deferido">
                        @else
                            <select name="status" class="form-select form-control-custom">
                                <option value="">Todos os Status</option>
                                <option value="inscrito" {{ request('status') === 'inscrito' ? 'selected' : '' }}>⏳ Pendente / Inscrito</option>
                                <option value="deferido" {{ request('status') === 'deferido' ? 'selected' : '' }}>✅ Deferido</option>
                                <option value="indeferido" {{ request('status') === 'indeferido' ? 'selected' : '' }}>❌ Indeferido</option>
                            </select>
                        @endif
                    </div>
                    
                    <!-- Filtro de Curso -->
                    <div class="col-12 col-sm-6 col-md-3 col-lg-3">
                        <label class="form-label text-muted small fw-bold mb-1">Curso Acadêmico</label>
                        <select name="curso" class="form-select form-control-custom">
                            <option value="">Todos os Cursos</option>
                            @foreach($cursos as $c)
                                <option value="{{ $c }}" {{ request('curso') === $c ? 'selected' : '' }}>🎓 {{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Botões de Ação -->
                    <div class="col-12 col-md-2 col-lg-1 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center" style="border-radius: 8px; min-height: 41px;" title="Aplicar filtros">
                            <i class="fas fa-filter me-1"></i> Filtrar
                        </button>
                    </div>
                </div>
                
                @if(request()->anyFilled(['search', 'status', 'curso']))
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-3 border-top" style="border-top-style: dashed !important;">
                        <span class="text-muted small">Filtros aplicados:</span>
                        @if(request()->filled('search'))
                            <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill small">Busca: "{{ request('search') }}"</span>
                        @endif
                        @if(request()->filled('status'))
                            <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill small">Status: {{ ucfirst(request('status')) }}</span>
                        @endif
                        @if(request()->filled('curso'))
                            <span class="badge bg-light text-dark border px-2.5 py-1.5 rounded-pill small">Curso: {{ request('curso') }}</span>
                        @endif
                        <a href="{{ route('processos-seletivos.inscricoes', $processo->id_processo) }}" class="text-decoration-none text-danger fw-bold small ms-auto hover-opacity">
                            <i class="fas fa-trash-alt me-1"></i> Limpar Filtros
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Candidate List Table -->
    <div class="card custom-card">
        <div class="card-header bg-white py-3 px-4 d-flex align-items-center justify-content-between border-0">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-list text-primary"></i>
                <h6 class="mb-0 fw-bold text-dark">Listagem de Candidatos</h6>
                <span class="badge bg-light text-muted border ms-2">{{ $inscricoes->total() }} registro(s)</span>
            </div>
            <span class="text-muted small"><i class="far fa-clock me-1"></i>Atualizado em {{ now()->format('d/m/Y H:i') }}</span>
        </div>
        
        <div class="modern-table-wrapper">
            <div class="table-responsive">
                <table class="table modern-table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 130px;">Nº Inscrição</th>
                            <th>Candidato</th>
                            <th>Contato</th>
                            <th>Curso</th>
                            <th>Status</th>
                            @if (Auth::user()->nivel === 'admin' || Auth::user()->nivel === 'operador')
                                <th style="width: 80px;">Anexo</th>
                            @endif
                            <th style="width: 110px;">Data Inscrição</th>
                            @if($config['pode_alterar_status'])
                                <th style="width: 120px;" class="text-center">Ações</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inscricoes as $inscricao)
                            <tr>
                                <!-- Inscription code -->
                                <td>
                                    <span class="inscricao-code">
                                        {{ $inscricao->numero_inscricao ?? '—' }}
                                    </span>
                                </td>
                                
                                <!-- Candidate details (Name & CPF) -->
                                <td>
                                    <div>
                                        <div class="fw-bold text-dark d-flex align-items-center gap-1 flex-wrap candidate-name-container">
                                            {{ $inscricao->estagiario->nome_estagiario }}
                                            @if (Auth::user()->nivel === 'admin' || Auth::user()->nivel === 'operador')
                                                <a href="{{ route('estagiario.show', $inscricao->fk_id_estagiario) }}" target="_blank" class="text-primary hover-opacity" title="Abrir perfil completo" style="font-size: 0.75rem; padding: 2px;">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            @endif
                                        </div>
                                        <div class="text-muted small" style="font-size: 0.75rem;">
                                            <i class="far fa-id-card me-1"></i>CPF: {{ $inscricao->estagiario->numero_cpf }}
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Contact info -->
                                <td>
                                    <div class="d-flex flex-column gap-1" style="font-size: 0.85rem;">
                                        <div class="text-dark d-inline-flex align-items-center gap-1" title="{{ $inscricao->estagiario->email }}">
                                            <i class="far fa-envelope text-muted" style="width: 14px;"></i>
                                            <span class="text-truncate" style="max-width: 160px;">{{ $inscricao->estagiario->email }}</span>
                                        </div>
                                        @php
                                            $phone = $inscricao->estagiario->numero_celular ?? $inscricao->estagiario->numero_telefone;
                                        @endphp
                                        @if($phone)
                                            <div class="text-muted d-inline-flex align-items-center gap-1">
                                                <i class="fas fa-phone-alt text-muted" style="width: 14px; font-size: 0.75rem;"></i>
                                                <span>{{ $phone }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                
                                <!-- Course -->
                                <td>
                                    <div class="fw-semibold text-secondary" style="font-size: 0.85rem;" title="{{ $inscricao->estagiario->curso ?? 'Não especificado' }}">
                                        {{ $inscricao->estagiario->curso ?? '—' }}
                                    </div>
                                </td>
                                
                                <!-- Status badge -->
                                <td>
                                    <span class="status-badge {{ $inscricao->status_inscricao }}">
                                        @switch($inscricao->status_inscricao)
                                            @case('deferido')
                                                <i class="fas fa-check-circle"></i> Deferido
                                                @break
                                            @case('indeferido')
                                                <i class="fas fa-times-circle"></i> Indeferido
                                                @break
                                            @case('inscrito')
                                                <i class="fas fa-clock"></i> Pendente
                                                @break
                                            @default
                                                <i class="fas fa-question-circle"></i> {{ ucfirst($inscricao->status_inscricao) }}
                                        @endswitch
                                    </span>
                                </td>
                                
                                <!-- Attachment Link (Admin/Operador) -->
                                @if (Auth::user()->nivel === 'admin' || Auth::user()->nivel === 'operador')
                                    <td>
                                        @if($inscricao->arquivo_inscricao)
                                            <a href="{{ Storage::url($inscricao->arquivo_inscricao) }}" target="_blank" class="text-decoration-none text-primary fw-bold p-0 d-inline-flex align-items-center gap-1 hover-opacity" style="font-size: 0.85rem;" title="Abrir anexo em nova aba">
                                                <i class="fas fa-paperclip text-muted"></i>
                                                <span>Visualizar</span>
                                            </a>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                @endif
                                
                                <!-- Subscription Date -->
                                <td class="text-muted" style="font-size: 0.85rem;">
                                    <i class="far fa-calendar-alt me-1"></i> {{ $inscricao->created_at->format('d/m/Y') }}
                                    <div class="small mt-0.5 text-black-50" style="font-size: 0.75rem;"><i class="far fa-clock me-1"></i> {{ $inscricao->created_at->format('H:i') }}</div>
                                </td>
                                
                                <!-- Actions -->
                                @if($config['pode_alterar_status'])
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center gap-1">
                                            @if($inscricao->status_inscricao !== 'deferido')
                                                <form action="{{ route('processos-seletivos.inscricoes.atualizar-status', $processo->id_processo) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="inscricao_id" value="{{ $inscricao->id_inscricao }}">
                                                    <input type="hidden" name="novo_status" value="deferido">
                                                    <button type="submit" class="action-btn btn-approve" title="Marcar como Deferido">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($inscricao->status_inscricao !== 'indeferido')
                                                <form action="{{ route('processos-seletivos.inscricoes.atualizar-status', $processo->id_processo) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="inscricao_id" value="{{ $inscricao->id_inscricao }}">
                                                    <input type="hidden" name="novo_status" value="indeferido">
                                                    <button type="submit" class="action-btn btn-reject" title="Marcar como Indeferido">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            @if($inscricao->status_inscricao !== 'inscrito')
                                                <form action="{{ route('processos-seletivos.inscricoes.atualizar-status', $processo->id_processo) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="inscricao_id" value="{{ $inscricao->id_inscricao }}">
                                                    <input type="hidden" name="novo_status" value="inscrito">
                                                    <button type="submit" class="action-btn btn-revert" title="Reverter para Pendente">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('processos-seletivos.inscricoes.excluir', [$processo->id_processo, $inscricao->id_inscricao]) }}" method="POST" class="d-inline"
                                                onsubmit="return confirm('Tem certeza que deseja excluir esta inscrição? Esta ação não pode ser desfeita.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn btn-delete" title="Excluir Inscrição">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <!-- Empty Search Results visualizer -->
                            <tr>
                                <td colspan="100" class="text-center py-5 text-muted">
                                    <div class="d-flex flex-column align-items-center gap-3">
                                        <div class="bg-light rounded-circle p-3 d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                                            <i class="fas fa-users-slash fa-2x text-secondary"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-1">Nenhum inscrito encontrado</h6>
                                            <p class="text-muted small mb-0">Tente ajustar seus termos de pesquisa ou filtros aplicados.</p>
                                        </div>
                                        @if(request()->anyFilled(['search', 'status', 'curso']))
                                            <a href="{{ route('processos-seletivos.inscricoes', $processo->id_processo) }}" class="btn btn-outline-primary btn-sm rounded-pill px-4 mt-2 fw-semibold">
                                                <i class="fas fa-undo me-1"></i>Limpar Filtros
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination links (with search parameters appended) -->
    <div class="pt-4 d-flex justify-content-center">
        {{ $inscricoes->appends(request()->query())->links() }}
    </div>
</div>

<!-- Modern Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3 px-4">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2 text-white">
                    <i class="fas fa-download"></i>Exportar Dados de Inscrição
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('processos-seletivos.exportar-inscricoes', $processo->id_processo) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <!-- Format Options -->
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold mb-2">Selecione o Formato de Exportação</label>
                        <div class="row g-3">
                            <div class="col-6">
                                <input class="form-check-input d-none" type="radio" name="format" id="formatPdf" value="pdf" checked>
                                <label class="w-100" for="formatPdf" style="cursor: pointer;">
                                    <div class="border rounded-4 p-4 text-center option-card-format" id="formatCardPdf" style="transition: all 0.2s ease;">
                                        <i class="fas fa-file-pdf fa-3x text-danger mb-3"></i>
                                        <div class="fw-bold text-dark">Documento PDF</div>
                                        <span class="text-muted small">Ideal para impressão/leitura</span>
                                    </div>
                                </label>
                            </div>
                            <div class="col-6">
                                <input class="form-check-input d-none" type="radio" name="format" id="formatExcel" value="excel">
                                <label class="w-100" for="formatExcel" style="cursor: pointer;">
                                    <div class="border rounded-4 p-4 text-center option-card-format" id="formatCardExcel" style="transition: all 0.2s ease;">
                                        <i class="fas fa-file-excel fa-3x text-success mb-3"></i>
                                        <div class="fw-bold text-dark">Planilha Excel</div>
                                        <span class="text-muted small">Ideal para manipulação de dados</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- CSS support for Format Options Cards -->
                    <style>
                        .option-card-format {
                            background-color: #fafbfc;
                            border: 2px solid #eef2f6 !important;
                        }
                        input[name="format"]:checked + label .option-card-format {
                            border-color: var(--color-primary) !important;
                            background-color: var(--color-primary-light);
                        }
                    </style>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const pdfRadio = document.getElementById('formatPdf');
                            const excelRadio = document.getElementById('formatExcel');
                            
                            pdfRadio.addEventListener('change', updateFormatStyle);
                            excelRadio.addEventListener('change', updateFormatStyle);
                            
                            function updateFormatStyle() {
                                // Handled automatically by CSS selector :checked + label
                            }
                        });
                    </script>

                    <!-- Filter Options in Export -->
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold mb-2">Qual status deseja exportar?</label>
                        @if($config['apenas_deferidos'])
                            <select class="form-select form-control-custom" name="status_filter" required>
                                <option value="deferido" selected>✅ Apenas Deferidos</option>
                            </select>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle me-1 text-primary"></i>
                                A exportação está restrita apenas para inscritos deferidos conforme configurações de empresa.
                            </small>
                        @else
                            <select class="form-select form-control-custom" name="status_filter" required>
                                <option value="todos" {{ request('status') === '' ? 'selected' : '' }}>📋 Todos os Inscritos</option>
                                <option value="deferido" {{ request('status') === 'deferido' ? 'selected' : '' }}>✅ Apenas Deferidos</option>
                                <option value="indeferido" {{ request('status') === 'indeferido' ? 'selected' : '' }}>❌ Apenas Indeferidos</option>
                                <option value="inscrito" {{ request('status') === 'inscrito' ? 'selected' : '' }}>⏳ Apenas Pendentes (Inscritos)</option>
                            </select>
                        @endif
                    </div>

                    <!-- Column selection -->
                    <div class="mb-2">
                        <label class="form-label text-muted small fw-bold mb-2">Selecione as colunas para o relatório</label>
                        <div class="border rounded-3 p-3 bg-light">
                            <div class="row g-2">
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="numero_inscricao" id="colNumero" checked>
                                        <label class="form-check-label text-dark small" for="colNumero">Nº Inscrição</label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="nome" id="colNome" checked>
                                        <label class="form-check-label text-dark small" for="colNome">Nome Completo</label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="email" id="colEmail" checked>
                                        <label class="form-check-label text-dark small" for="colEmail">E-mail</label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="telefone" id="colTelefone" checked>
                                        <label class="form-check-label text-dark small" for="colTelefone">Telefone / Celular</label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="cpf" id="colCpf">
                                        <label class="form-check-label text-dark small" for="colCpf">CPF</label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="curso" id="colCurso" checked>
                                        <label class="form-check-label text-dark small" for="colCurso">Curso</label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="instituicao" id="colInstituicao">
                                        <label class="form-check-label text-dark small" for="colInstituicao">Instituição</label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="status" id="colStatus" checked>
                                        <label class="form-check-label text-dark small" for="colStatus">Status</label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="data_inscricao" id="colData" checked>
                                        <label class="form-check-label text-dark small" for="colData">Data Inscrição</label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="data_nascimento" id="colDataNascimento">
                                        <label class="form-check-label text-dark small" for="colDataNascimento">Data Nascimento</label>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="idade" id="colIdade">
                                        <label class="form-check-label text-dark small" for="colIdade">Idade</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 border-top pt-2 d-flex gap-2">
                                <button type="button" class="btn btn-xs btn-outline-primary py-1 px-2 text-xs" onclick="selecionarTodas()" style="font-size: 0.75rem;">
                                    <i class="fas fa-check-double me-1"></i> Selecionar Todas
                                </button>
                                <button type="button" class="btn btn-xs btn-outline-secondary py-1 px-2 text-xs" onclick="desselecionarTodas()" style="font-size: 0.75rem;">
                                    <i class="fas fa-times me-1"></i> Limpar Seleção
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-3 px-4">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 8px;">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 8px;">
                        <i class="fas fa-file-export me-1"></i> Exportar Relatório
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function selecionarTodas() {
    document.querySelectorAll('input[name="colunas[]"]').forEach(cb => cb.checked = true);
}
function desselecionarTodas() {
    document.querySelectorAll('input[name="colunas[]"]').forEach(cb => cb.checked = false);
}
</script>
@endsection
