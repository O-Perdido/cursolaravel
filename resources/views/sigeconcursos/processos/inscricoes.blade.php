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

            <div class="card sc-insc-list-card {{ $aptaParaDecisao ? 'apta' : '' }}">
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
                                        method="POST">
                                        @csrf
                                        <input type="hidden" name="inscricao_id" value="{{ $inscricao->id_inscricao }}">
                                        <select name="novo_status" class="form-select form-select-sm mb-2" required>
                                            <option value="inscrito" {{ $inscricao->status_inscricao === 'inscrito' ? 'selected' : '' }}>Inscrito</option>
                                            <option value="deferido" {{ $inscricao->status_inscricao === 'deferido' ? 'selected' : '' }}>Deferido</option>
                                            <option value="indeferido" {{ $inscricao->status_inscricao === 'indeferido' ? 'selected' : '' }}>Indeferido</option>
                                        </select>
                                        <textarea name="observacoes" rows="2" class="form-control form-control-sm mb-2"
                                            placeholder="Observações">{{ $inscricao->observacoes }}</textarea>
                                        <button type="submit" class="btn btn-sm btn-primary w-100">Salvar inscrição</button>
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
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger btn-excluir-inscricao"
                                            data-url="{{ route('sigeconcursos.processos.inscricoes.destroy', [$processo->id_processo, $inscricao->id_inscricao]) }}"
                                            data-candidato="{{ $inscricao->candidato?->nome_completo }}"
                                            data-numero="{{ $inscricao->numero_inscricao ?: 'sem número' }}">
                                            <i class="fa-solid fa-trash me-1"></i> Excluir inscrição
                                        </button>
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

            if (!modalElement || !deleteForm || !deleteText || !passwordField) {
                return;
            }

            const modal = new bootstrap.Modal(modalElement);

            document.querySelectorAll('.btn-excluir-inscricao').forEach(function (button) {
                button.addEventListener('click', function () {
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