@extends('layouts.main')

@section('title', 'SIGE Concursos | Homologação de Inscrições')

@section('content')
    @once
        <style>
            .sc-insc-list-card {
                border: 1px solid rgba(17, 49, 58, 0.12);
                border-radius: 16px;
                box-shadow: 0 8px 20px rgba(17, 49, 58, 0.08);
            }

            .sc-insc-list-card.apta {
                border-color: rgba(255, 193, 7, 0.45);
                background: linear-gradient(180deg, rgba(255, 243, 205, 0.32), #fff);
            }

            .sc-insc-list-title {
                color: #11313a;
                font-weight: 700;
                margin-bottom: 0;
            }

            .sc-insc-section-title {
                font-size: 0.76rem;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                color: #607580;
                font-weight: 700;
                margin-bottom: 0.4rem;
            }

            .sc-doc-list {
                max-height: 180px;
                overflow: auto;
                border: 1px solid rgba(17, 49, 58, 0.1);
                border-radius: 10px;
                padding: 0.55rem;
                background: #fff;
            }

            .sc-doc-list .item {
                font-size: 0.86rem;
                margin-bottom: 0.25rem;
            }

            .sc-doc-list .item:last-child {
                margin-bottom: 0;
            }

            .sc-insc-list-card.has-pending-changes {
                border-color: rgba(255, 193, 7, 0.6);
                box-shadow: 0 0 0 2px rgba(255, 193, 7, 0.14), 0 8px 20px rgba(17, 49, 58, 0.08);
            }

            .sc-save-indicator {
                font-size: 0.78rem;
                min-height: 1rem;
            }

            .sc-bulk-bar {
                position: sticky;
                bottom: 12px;
                z-index: 50;
            }
        </style>
    @endonce

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Homologação de Inscrições</h2>
            <p class="text-muted mb-0">{{ $processo->titulo }} — Edital {{ $processo->numero_edital }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sigeconcursos.processos.show', $processo->id_processo) }}"
                class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Voltar ao Processo
            </a>
        </div>
    </div>

    @include('sigeconcursos.processos._workflow-hub', ['processo' => $processo])

    {{-- Painel de execução: blocos clicáveis por fila --}}
    @php
        $aguardandoPagamento = max(0, $resumo['pendentes'] - $resumo['aptos']);
        $filtroAtivo = '';
        if (request('status_inscricao') === 'deferido') {
            $filtroAtivo = 'deferida';
        } elseif (request('status_inscricao') === 'indeferido') {
            $filtroAtivo = 'indeferida';
        } elseif (request('status_inscricao') === 'inscrito' && request('status_pagamento') === 'pendente') {
            $filtroAtivo = 'aguardando';
        } elseif (request('status_inscricao') === 'inscrito') {
            $filtroAtivo = 'analise';
        }
        $baseUrl = route('sigeconcursos.processos.inscricoes', $processo->id_processo);
        $temFiltroAtivo = request()->hasAny(['nome', 'cpf', 'modalidade_concorrencia', 'status_inscricao', 'status_pagamento', 'status_isencao']);
    @endphp

    <div id="painel-homologacao" class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <a href="{{ $baseUrl }}?status_inscricao=inscrito" class="text-decoration-none">
                <div
                    class="card border-0 shadow-sm h-100 {{ $filtroAtivo === 'analise' ? 'border-warning border-2' : '' }}">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-hourglass-half text-warning"></i>
                            <span class="text-muted small fw-semibold">Fila de análise</span>
                        </div>
                        <div class="h3 mb-1 text-warning">{{ $resumo['aptos'] }}</div>
                        <div class="small text-muted">Aptos para decidir</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ $baseUrl }}?status_inscricao=inscrito&status_pagamento=pendente" class="text-decoration-none">
                <div
                    class="card border-0 shadow-sm h-100 {{ $filtroAtivo === 'aguardando' ? 'border-info border-2' : '' }}">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-clock text-info"></i>
                            <span class="text-muted small fw-semibold">Aguardando pagamento</span>
                        </div>
                        <div class="h3 mb-1 text-info">{{ $aguardandoPagamento }}</div>
                        <div class="small text-muted">Inscritos bloqueados</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ $baseUrl }}?status_inscricao=deferido" class="text-decoration-none">
                <div
                    class="card border-0 shadow-sm h-100 {{ $filtroAtivo === 'deferida' ? 'border-success border-2' : '' }}">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-circle-check text-success"></i>
                            <span class="text-muted small fw-semibold">Deferidas</span>
                        </div>
                        <div class="h3 mb-1 text-success">{{ $resumo['deferidas'] }}</div>
                        <div class="small text-muted">Homologadas</div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ $baseUrl }}?status_inscricao=indeferido" class="text-decoration-none">
                <div
                    class="card border-0 shadow-sm h-100 {{ $filtroAtivo === 'indeferida' ? 'border-danger border-2' : '' }}">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="fa-solid fa-circle-xmark text-danger"></i>
                            <span class="text-muted small fw-semibold">Indeferidas</span>
                        </div>
                        <div class="h3 mb-1 text-danger">{{ $resumo['indeferidas'] }}</div>
                        <div class="small text-muted">Reprovadas na análise</div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Atalhos por tipo de fila --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-2 d-flex flex-wrap align-items-center gap-2">
            <span class="text-muted small fw-semibold me-1">Acesso rápido:</span>
            <a href="{{ $baseUrl }}?status_inscricao=inscrito&status_pagamento=pago" class="btn btn-sm btn-outline-primary">
                <i class="fa-solid fa-money-bill-wave me-1"></i> Pagos pendentes
            </a>
            <a href="{{ $baseUrl }}?status_inscricao=inscrito&status_pagamento=isento"
                class="btn btn-sm btn-outline-success">
                <i class="fa-solid fa-hand-holding me-1"></i> Isentos pendentes
            </a>
            <a href="{{ $baseUrl }}?status_inscricao=inscrito&status_pagamento=nao_aplicavel"
                class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-tags me-1"></i> Gratuitos pendentes
            </a>
            <a href="{{ $baseUrl }}?status_isencao=pendente" class="btn btn-sm btn-outline-warning">
                <i class="fa-solid fa-file-lines me-1"></i> Isenções pendentes
            </a>
            <a href="{{ $baseUrl }}" class="btn btn-sm btn-outline-dark">
                <i class="fa-solid fa-list me-1"></i> Todas
            </a>
        </div>
    </div>

    {{-- Filtro avançado colapsível --}}
    <div class="mb-4">
        <button class="btn btn-sm btn-outline-secondary mb-2" type="button" data-bs-toggle="collapse"
            data-bs-target="#filtroAvancado" aria-expanded="{{ $temFiltroAtivo ? 'true' : 'false' }}">
            <i class="fa-solid fa-sliders me-1"></i> Filtro avançado
            @if($temFiltroAtivo)
                <span class="badge bg-primary ms-1">ativo</span>
            @endif
        </button>
        <div class="collapse {{ $temFiltroAtivo ? 'show' : '' }}" id="filtroAvancado">
            <form method="GET" class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label mb-1" for="nome">Nome</label>
                            <input type="text" name="nome" id="nome" class="form-control form-control-sm"
                                value="{{ request('nome') }}" placeholder="Nome do candidato">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1" for="cpf">CPF</label>
                            <input type="text" name="cpf" id="cpf" class="form-control form-control-sm"
                                value="{{ request('cpf') }}" placeholder="Somente números">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1" for="modalidade_concorrencia">Modalidade</label>
                            <select name="modalidade_concorrencia" id="modalidade_concorrencia"
                                class="form-select form-select-sm">
                                <option value="">Todas</option>
                                <option value="ampla_concorrencia" {{ request('modalidade_concorrencia') === 'ampla_concorrencia' ? 'selected' : '' }}>Ampla
                                </option>
                                <option value="pcd" {{ request('modalidade_concorrencia') === 'pcd' ? 'selected' : '' }}>PCD
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1" for="status_inscricao">Status inscrição</label>
                            <select name="status_inscricao" id="status_inscricao" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option value="inscrito" {{ request('status_inscricao') === 'inscrito' ? 'selected' : '' }}>
                                    Inscrito</option>
                                <option value="deferido" {{ request('status_inscricao') === 'deferido' ? 'selected' : '' }}>
                                    Deferido</option>
                                <option value="indeferido" {{ request('status_inscricao') === 'indeferido' ? 'selected' : '' }}>Indeferido</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label mb-1" for="status_isencao">Status isenção</label>
                            <select name="status_isencao" id="status_isencao" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option value="nao_solicitada" {{ request('status_isencao') === 'nao_solicitada' ? 'selected' : '' }}>Não solicitada</option>
                                <option value="pendente" {{ request('status_isencao') === 'pendente' ? 'selected' : '' }}>
                                    Pendente</option>
                                <option value="deferida" {{ request('status_isencao') === 'deferida' ? 'selected' : '' }}>
                                    Deferida</option>
                                <option value="indeferida" {{ request('status_isencao') === 'indeferida' ? 'selected' : '' }}>
                                    Indeferida</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-grid">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="row g-2 align-items-end mt-1">
                        <div class="col-md-2">
                            <label class="form-label mb-1" for="status_pagamento">Pagamento</label>
                            <select name="status_pagamento" id="status_pagamento" class="form-select form-select-sm">
                                <option value="">Todos</option>
                                <option value="nao_aplicavel" {{ request('status_pagamento') === 'nao_aplicavel' ? 'selected' : '' }}>Não aplicável</option>
                                <option value="pendente" {{ request('status_pagamento') === 'pendente' ? 'selected' : '' }}>
                                    Pendente</option>
                                <option value="aguardando_isencao" {{ request('status_pagamento') === 'aguardando_isencao' ? 'selected' : '' }}>Aguardando isenção</option>
                                <option value="isento" {{ request('status_pagamento') === 'isento' ? 'selected' : '' }}>Isento
                                </option>
                                <option value="pago" {{ request('status_pagamento') === 'pago' ? 'selected' : '' }}>Pago
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ $baseUrl }}" class="btn btn-outline-secondary btn-sm d-block">Limpar filtros</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Contexto do resultado atual --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted small">
            Exibindo <strong>{{ $inscricoes->firstItem() ?? 0 }}–{{ $inscricoes->lastItem() ?? 0 }}</strong>
            de <strong>{{ $resumo['total'] }}</strong>
            inscri{{ $resumo['total'] === 1 ? 'ção' : 'ções' }}{{ $temFiltroAtivo ? ' (com filtros ativos)' : '' }}
        </span>
    </div>

    <div id="sc-bulk-bar" class="sc-bulk-bar d-none">
        <div class="alert alert-warning border-0 shadow-sm d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-3 py-2">
            <div class="small mb-0">
                <strong id="sc-bulk-count">0</strong> inscrição(ões) com alteração pendente.
            </div>
            <div class="d-flex gap-2">
                <button type="button" id="btn-salvar-tudo" class="btn btn-sm btn-warning">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Salvar alterações
                </button>
                <button type="button" id="btn-limpar-pendencias" class="btn btn-sm btn-outline-secondary">
                    Descartar alterações locais
                </button>
            </div>
        </div>
    </div>

    {{-- Lista operacional de homologação --}}
    <div class="d-grid gap-3">
        @forelse($inscricoes as $inscricao)
            @php
                $badgeInscricao = [
                    'inscrito' => 'bg-info',
                    'deferido' => 'bg-success',
                    'indeferido' => 'bg-danger',
                ][$inscricao->status_inscricao] ?? 'bg-secondary';

                $badgePagamento = match ($inscricao->status_pagamento) {
                    'pago' => 'bg-success',
                    'isento' => 'bg-success',
                    'nao_aplicavel' => 'bg-secondary',
                    'pendente' => 'bg-warning text-dark',
                    'aguardando_isencao' => 'bg-warning text-dark',
                    default => 'bg-secondary',
                };

                $aptaParaDecisao = $inscricao->status_inscricao === 'inscrito'
                    && in_array($inscricao->status_pagamento, ['pago', 'isento', 'nao_aplicavel'], true);

                $isencaoPendente = $inscricao->status_isencao === 'pendente';
                $temIsencao = $inscricao->status_isencao !== 'nao_solicitada';

                $badgeIsencao = match ($inscricao->status_isencao) {
                    'pendente' => 'bg-warning text-dark',
                    'deferida' => 'bg-success',
                    'indeferida' => 'bg-danger',
                    default => 'bg-secondary',
                };
            @endphp

            <div class="card sc-insc-list-card {{ $aptaParaDecisao ? 'apta' : '' }}" data-card-inscricao="{{ $inscricao->id_inscricao }}">
                <div class="card-body">
                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-2 mb-3">
                        <div>
                            <h5 class="sc-insc-list-title">{{ $inscricao->candidato?->nome_completo }}</h5>
                            <div class="small text-muted">
                                Nº {{ $inscricao->numero_inscricao ?: '—' }} • {{ $inscricao->candidato?->numero_cpf }} • {{ $inscricao->candidato?->email }}
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-1">
                            <span class="badge {{ $badgeInscricao }}">{{ ucfirst($inscricao->status_inscricao) }}</span>
                            <span class="badge {{ $badgePagamento }}">{{ ucfirst(str_replace('_', ' ', $inscricao->status_pagamento)) }}</span>
                            @if($temIsencao)
                                <span class="badge {{ $badgeIsencao }}">Isenção: {{ ucfirst($inscricao->status_isencao) }}</span>
                            @endif
                            @if($aptaParaDecisao)
                                <span class="badge text-dark" style="background: #ffe69c;">Apto para decidir</span>
                            @endif
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12 col-lg-3">
                            <div class="sc-insc-section-title">Dados da inscrição</div>
                            <div class="small"><strong>Modalidade:</strong> {{ $inscricao->modalidadeLabel() }}</div>
                            @if($inscricao->solicitou_nome_social && $inscricao->nome_social)
                                <div class="small"><strong>Nome social:</strong> {{ $inscricao->nome_social }}</div>
                            @endif
                            <div class="small"><strong>Data:</strong> {{ $inscricao->created_at?->format('d/m/Y H:i') }}</div>
                            @if($inscricao->valor_taxa_aplicada !== null)
                                <div class="small"><strong>Taxa:</strong> R$ {{ number_format((float) $inscricao->valor_taxa_aplicada, 2, ',', '.') }}</div>
                            @endif
                            @if($inscricao->observacoes)
                                <div class="small text-muted mt-2"><strong>Obs atual:</strong> {{ Str::limit($inscricao->observacoes, 90) }}</div>
                            @endif
                        </div>

                        <div class="col-12 col-lg-4">
                            <div class="sc-insc-section-title">Documentos</div>
                            <div class="sc-doc-list">
                                @if($inscricao->solicitou_condicao_especial)
                                    <div class="item">
                                        <strong>Cond. especial:</strong>
                                        {{ Str::limit($inscricao->descricao_condicao_especial ?: 'Solicitada', 60) }}
                                    </div>
                                    @if($inscricao->caminho_documento_condicao_especial)
                                        <div class="item">
                                            <a href="{{ asset('storage/' . $inscricao->caminho_documento_condicao_especial) }}"
                                                target="_blank">
                                                <i class="fa-solid fa-file-medical me-1"></i>Laudo condição especial
                                            </a>
                                        </div>
                                    @endif
                                @endif

                                @foreach($inscricao->documentos as $documento)
                                    <div class="item">
                                        <a href="{{ asset('storage/' . $documento->caminho_arquivo) }}" target="_blank">
                                            <i class="fa-solid fa-paperclip me-1"></i>{{ $documento->titulo_documento }}
                                        </a>
                                    </div>
                                @endforeach

                                @if($inscricao->documentosIsencao->count() > 0)
                                    <div class="item text-muted fw-semibold mt-2">Docs isenção</div>
                                    @foreach($inscricao->documentosIsencao as $documentoIsencao)
                                        <div class="item">
                                            <a href="{{ asset('storage/' . $documentoIsencao->caminho_arquivo) }}" target="_blank">
                                                <i class="fa-solid fa-paperclip me-1"></i>{{ $documentoIsencao->nome_documento }}
                                            </a>
                                        </div>
                                    @endforeach
                                @endif

                                @if(!$inscricao->solicitou_condicao_especial && $inscricao->documentos->count() === 0 && $inscricao->documentosIsencao->count() === 0)
                                    <span class="text-muted small">Sem documentos enviados.</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 col-lg-5">
                            <div class="row g-2">
                                <div class="col-12 col-xl-6">
                                    <div class="sc-insc-section-title">Análise da inscrição</div>
                                    <form action="{{ route('sigeconcursos.processos.inscricoes.atualizar-status', $processo->id_processo) }}"
                                        method="POST" class="js-inscricao-form" data-inscricao-id="{{ $inscricao->id_inscricao }}"
                                        data-original-status="{{ $inscricao->status_inscricao }}"
                                        data-original-observacoes="{{ e((string) ($inscricao->observacoes ?? '')) }}">
                                        @csrf
                                        <input type="hidden" name="inscricao_id" value="{{ $inscricao->id_inscricao }}">
                                        <select name="novo_status" class="form-select form-select-sm mb-2 js-inscricao-status" required>
                                            <option value="inscrito" {{ $inscricao->status_inscricao === 'inscrito' ? 'selected' : '' }}>Inscrito</option>
                                            <option value="deferido" {{ $inscricao->status_inscricao === 'deferido' ? 'selected' : '' }}>Deferido</option>
                                            <option value="indeferido" {{ $inscricao->status_inscricao === 'indeferido' ? 'selected' : '' }}>Indeferido</option>
                                        </select>
                                        <textarea name="observacoes" rows="2" class="form-control form-control-sm mb-2 js-inscricao-observacoes"
                                            placeholder="Observações">{{ $inscricao->observacoes }}</textarea>
                                        <button type="submit" class="btn btn-sm btn-primary w-100 js-btn-salvar-inscricao">Salvar inscrição</button>
                                        <div class="sc-save-indicator text-muted mt-1 js-save-feedback">Sem alterações pendentes.</div>
                                    </form>
                                </div>

                                <div class="col-12 col-xl-6">
                                    @if($temIsencao)
                                        <div class="sc-insc-section-title {{ $isencaoPendente ? 'text-warning' : '' }}">
                                            @if($isencaoPendente)
                                                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                            @endif
                                            Análise da isenção
                                        </div>
                                        <form action="{{ route('sigeconcursos.processos.inscricoes.atualizar-isencao', $processo->id_processo) }}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="inscricao_id" value="{{ $inscricao->id_inscricao }}">

                                            @if($inscricao->isencao)
                                                <div class="small text-muted mb-1">Caso: {{ $inscricao->isencao->titulo }}</div>
                                            @endif
                                            @if($inscricao->justificativa_isencao)
                                                <div class="small text-muted mb-2"><em>{{ Str::limit($inscricao->justificativa_isencao, 90) }}</em></div>
                                            @endif

                                            <select name="novo_status_isencao" class="form-select form-select-sm mb-2" required>
                                                <option value="nao_solicitada" {{ $inscricao->status_isencao === 'nao_solicitada' ? 'selected' : '' }}>Não solicitada</option>
                                                <option value="pendente" {{ $inscricao->status_isencao === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                                <option value="deferida" {{ $inscricao->status_isencao === 'deferida' ? 'selected' : '' }}>Deferida</option>
                                                <option value="indeferida" {{ $inscricao->status_isencao === 'indeferida' ? 'selected' : '' }}>Indeferida</option>
                                            </select>
                                            <textarea name="parecer_isencao" rows="2" class="form-control form-control-sm mb-2"
                                                placeholder="Parecer">{{ $inscricao->parecer_isencao }}</textarea>
                                            <button type="submit" class="btn btn-sm btn-outline-primary w-100">Salvar isenção</button>
                                        </form>
                                    @else
                                        <div class="sc-insc-section-title">Análise da isenção</div>
                                        <div class="text-muted small">Sem solicitação de isenção para esta inscrição.</div>
                                    @endif
                                </div>

                                <div class="col-12">
                                    <div class="pt-1 border-top">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa-solid fa-shield-halved me-1"></i> Ações avançadas
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <button type="button"
                                                        class="dropdown-item text-danger btn-excluir-inscricao"
                                                        data-url="{{ route('sigeconcursos.processos.inscricoes.destroy', [$processo->id_processo, $inscricao->id_inscricao]) }}"
                                                        data-candidato="{{ $inscricao->candidato?->nome_completo }}"
                                                        data-numero="{{ $inscricao->numero_inscricao ?: 'sem número' }}">
                                                        <i class="fa-solid fa-trash me-1"></i> Excluir inscrição permanentemente
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center text-muted py-5">
                    <i class="fa-solid fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                    Nenhuma inscrição encontrada com os filtros informados.
                </div>
            </div>
        @endforelse
    </div>

    @if($inscricoes->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $inscricoes->links() }}
        </div>
    @endif

    <div class="modal fade" id="modal-excluir-inscricao" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar exclusão da inscrição</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form method="POST" id="form-excluir-inscricao">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p id="texto-excluir-inscricao" class="mb-3"></p>
                        <label for="password_confirm_inscricao" class="form-label">Senha do usuário logado</label>
                        <input type="password" class="form-control" id="password_confirm_inscricao" name="password_confirm" required>
                        <small class="text-muted">Informe a senha do operador/admin atual para confirmar a exclusão.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir inscrição</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalElement = document.getElementById('modal-excluir-inscricao');
            const deleteForm = document.getElementById('form-excluir-inscricao');
            const deleteText = document.getElementById('texto-excluir-inscricao');
            const passwordField = document.getElementById('password_confirm_inscricao');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const bulkBar = document.getElementById('sc-bulk-bar');
            const bulkCount = document.getElementById('sc-bulk-count');
            const btnSalvarTudo = document.getElementById('btn-salvar-tudo');
            const btnLimparPendencias = document.getElementById('btn-limpar-pendencias');
            const pendingChanges = new Map();
            const routeSalvarLote = @json(route('sigeconcursos.processos.inscricoes.atualizar-status-lote', $processo->id_processo));

            function normalizarTexto(valor) {
                return (valor || '').trim();
            }

            function setFeedback(form, texto, tipo = 'muted') {
                const feedback = form.querySelector('.js-save-feedback');
                if (!feedback) return;

                feedback.className = 'sc-save-indicator mt-1 js-save-feedback';
                feedback.classList.add(`text-${tipo}`);
                feedback.textContent = texto;
            }

            function getCard(form) {
                const id = form.dataset.inscricaoId;
                return document.querySelector(`[data-card-inscricao="${id}"]`);
            }

            function formState(form) {
                const status = form.querySelector('.js-inscricao-status')?.value || '';
                const observacoes = normalizarTexto(form.querySelector('.js-inscricao-observacoes')?.value || '');
                const originalStatus = form.dataset.originalStatus || '';
                const originalObservacoes = normalizarTexto(form.dataset.originalObservacoes || '');

                return {
                    changed: status !== originalStatus || observacoes !== originalObservacoes,
                    status,
                    observacoes,
                    originalStatus,
                    originalObservacoes,
                };
            }

            function updateBulkBar() {
                const total = pendingChanges.size;
                if (bulkCount) {
                    bulkCount.textContent = total;
                }
                if (bulkBar) {
                    bulkBar.classList.toggle('d-none', total === 0);
                }
            }

            function markFormChange(form) {
                const state = formState(form);
                const card = getCard(form);
                const id = Number(form.dataset.inscricaoId);

                if (state.changed) {
                    pendingChanges.set(id, {
                        inscricao_id: id,
                        novo_status: state.status,
                        observacoes: state.observacoes,
                    });
                    setFeedback(form, 'Alteração pendente. Clique em Salvar inscrição ou Salvar alterações.', 'warning');
                    card?.classList.add('has-pending-changes');
                } else {
                    pendingChanges.delete(id);
                    setFeedback(form, 'Sem alterações pendentes.', 'muted');
                    card?.classList.remove('has-pending-changes');
                }

                updateBulkBar();
            }

            async function salvarFormAjax(form) {
                const button = form.querySelector('.js-btn-salvar-inscricao');
                const formData = new FormData(form);

                try {
                    if (button) {
                        button.disabled = true;
                        button.textContent = 'Salvando...';
                    }
                    setFeedback(form, 'Salvando alteração...', 'info');

                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: formData,
                    });

                    if (!response.ok) {
                        throw new Error('Falha ao salvar a inscrição.');
                    }

                    const data = await response.json();
                    form.dataset.originalStatus = data.inscricao?.status_inscricao || form.querySelector('.js-inscricao-status')?.value || '';
                    form.dataset.originalObservacoes = data.inscricao?.observacoes || '';
                    markFormChange(form);
                    setFeedback(form, 'Inscrição salva sem recarregar a página.', 'success');
                } catch (error) {
                    setFeedback(form, 'Não foi possível salvar agora. Tente novamente.', 'danger');
                } finally {
                    if (button) {
                        button.disabled = false;
                        button.textContent = 'Salvar inscrição';
                    }
                }
            }

            if (!modalElement || !deleteForm || !deleteText || !passwordField) {
                // Continua mesmo sem o modal para manter o salvamento assíncrono.
            }

            const modal = modalElement ? new bootstrap.Modal(modalElement) : null;

            document.querySelectorAll('.js-inscricao-form').forEach(function (form) {
                const statusField = form.querySelector('.js-inscricao-status');
                const obsField = form.querySelector('.js-inscricao-observacoes');

                if (statusField) {
                    statusField.addEventListener('change', function () {
                        markFormChange(form);
                    });
                }

                if (obsField) {
                    obsField.addEventListener('input', function () {
                        markFormChange(form);
                    });
                }

                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    salvarFormAjax(form);
                });
            });

            btnSalvarTudo?.addEventListener('click', async function () {
                if (pendingChanges.size === 0) {
                    return;
                }

                const payload = { updates: Array.from(pendingChanges.values()) };
                const originalText = this.innerHTML;
                this.disabled = true;
                this.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Salvando...';

                try {
                    const response = await fetch(routeSalvarLote, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify(payload),
                    });

                    if (!response.ok) {
                        throw new Error('Falha ao salvar as alterações em lote.');
                    }

                    const data = await response.json();

                    (data.inscricoes || []).forEach(function (item) {
                        const form = document.querySelector(`.js-inscricao-form[data-inscricao-id="${item.id_inscricao}"]`);
                        if (!form) return;
                        form.dataset.originalStatus = item.status_inscricao || '';
                        form.dataset.originalObservacoes = item.observacoes || '';
                        markFormChange(form);
                        setFeedback(form, 'Alteração incluída no salvamento em lote.', 'success');
                    });

                    pendingChanges.clear();
                    updateBulkBar();
                } catch (error) {
                    alert('Não foi possível salvar todas as alterações. Tente novamente.');
                } finally {
                    this.disabled = false;
                    this.innerHTML = originalText;
                }
            });

            btnLimparPendencias?.addEventListener('click', function () {
                document.querySelectorAll('.js-inscricao-form').forEach(function (form) {
                    const statusField = form.querySelector('.js-inscricao-status');
                    const obsField = form.querySelector('.js-inscricao-observacoes');
                    if (statusField) statusField.value = form.dataset.originalStatus || 'inscrito';
                    if (obsField) obsField.value = form.dataset.originalObservacoes || '';
                    markFormChange(form);
                });
                pendingChanges.clear();
                updateBulkBar();
            });

            document.querySelectorAll('.btn-excluir-inscricao').forEach(function (button) {
                button.addEventListener('click', function () {
                    if (!modal) {
                        return;
                    }
                    const url = this.dataset.url;
                    const candidato = this.dataset.candidato;
                    const numero = this.dataset.numero;

                    deleteForm.action = url;
                    deleteText.textContent = `Você está prestes a excluir a inscrição ${numero} de ${candidato}. Esta ação remove também os documentos e vínculos de distribuição relacionados a ela.`;
                    passwordField.value = '';
                    modal.show();
                });
            });
        });
    </script>
@endsection