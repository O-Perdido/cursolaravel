@extends('layouts.main')

@section('title', 'SIGE Concursos | Homologação de Inscrições')

@section('content')
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

    {{-- Tabela de homologação --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nº</th>
                            <th>Candidato</th>
                            <th>Modalidade</th>
                            <th>Situação</th>
                            <th>Documentos</th>
                            <th style="width: 260px;">Análise</th>
                        </tr>
                    </thead>
                    <tbody>
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
                            <tr class="{{ $aptaParaDecisao ? 'table-warning' : '' }}">
                                <td class="fw-semibold small">{{ $inscricao->numero_inscricao ?: '—' }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $inscricao->candidato?->nome_completo }}</div>
                                    <div class="small text-muted">{{ $inscricao->candidato?->numero_cpf }}</div>
                                    <div class="small text-muted">{{ $inscricao->candidato?->email }}</div>
                                    <div class="small text-muted">{{ $inscricao->created_at?->format('d/m/Y H:i') }}</div>
                                </td>
                                <td class="small">{{ $inscricao->modalidadeLabel() }}</td>
                                <td>
                                    <span class="badge {{ $badgeInscricao }}">{{ ucfirst($inscricao->status_inscricao) }}</span>
                                    <div class="mt-1">
                                        <span class="badge {{ $badgePagamento }}">
                                            {{ ucfirst(str_replace('_', ' ', $inscricao->status_pagamento)) }}
                                        </span>
                                    </div>
                                    @if($temIsencao)
                                        <div class="mt-1">
                                            <span class="badge {{ $badgeIsencao }}">
                                                Isenção: {{ ucfirst($inscricao->status_isencao) }}
                                            </span>
                                        </div>
                                    @endif
                                    @if($inscricao->observacoes)
                                        <div class="small text-muted mt-1">
                                            {{ Str::limit($inscricao->observacoes, 60) }}
                                        </div>
                                    @endif
                                    @if($inscricao->valor_taxa_aplicada !== null)
                                        <div class="small text-muted mt-1">
                                            Taxa: R$ {{ number_format((float) $inscricao->valor_taxa_aplicada, 2, ',', '.') }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($inscricao->solicitou_condicao_especial)
                                        <div class="small mb-1">
                                            <strong>Cond. especial:</strong>
                                            {{ Str::limit($inscricao->descricao_condicao_especial ?: 'Solicitada', 50) }}
                                        </div>
                                        @if($inscricao->caminho_documento_condicao_especial)
                                            <a href="{{ asset('storage/' . $inscricao->caminho_documento_condicao_especial) }}"
                                                target="_blank" class="btn btn-sm btn-outline-primary mb-2">
                                                <i class="fa-solid fa-file me-1"></i> Laudo
                                            </a>
                                        @endif
                                    @endif

                                    @foreach($inscricao->documentos as $documento)
                                        <div class="small mb-1">
                                            <a href="{{ asset('storage/' . $documento->caminho_arquivo) }}" target="_blank">
                                                <i class="fa-solid fa-paperclip me-1"></i>{{ $documento->titulo_documento }}
                                            </a>
                                        </div>
                                    @endforeach

                                    @if($inscricao->documentosIsencao->count() > 0)
                                        <div class="small fw-semibold text-muted mt-2 mb-1">Docs isenção</div>
                                        @foreach($inscricao->documentosIsencao as $documentoIsencao)
                                            <div class="small mb-1">
                                                <a href="{{ asset('storage/' . $documentoIsencao->caminho_arquivo) }}" target="_blank">
                                                    <i class="fa-solid fa-paperclip me-1"></i>{{ $documentoIsencao->nome_documento }}
                                                </a>
                                            </div>
                                        @endforeach
                                    @endif

                                    @if(!$inscricao->solicitou_condicao_especial && $inscricao->documentos->count() === 0 && $inscricao->documentosIsencao->count() === 0)
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Formulário: status da inscrição --}}
                                    <form
                                        action="{{ route('sigeconcursos.processos.inscricoes.atualizar-status', $processo->id_processo) }}"
                                        method="POST">
                                        @csrf
                                        <input type="hidden" name="inscricao_id" value="{{ $inscricao->id_inscricao }}">
                                        <p class="small text-muted fw-semibold mb-1">Inscrição</p>
                                        <select name="novo_status" class="form-select form-select-sm mb-1" required>
                                            <option value="inscrito" {{ $inscricao->status_inscricao === 'inscrito' ? 'selected' : '' }}>Inscrito</option>
                                            <option value="deferido" {{ $inscricao->status_inscricao === 'deferido' ? 'selected' : '' }}>Deferido</option>
                                            <option value="indeferido" {{ $inscricao->status_inscricao === 'indeferido' ? 'selected' : '' }}>Indeferido</option>
                                        </select>
                                        <textarea name="observacoes" rows="1" class="form-control form-control-sm mb-1"
                                            placeholder="Observações">{{ $inscricao->observacoes }}</textarea>
                                        <button type="submit" class="btn btn-sm btn-primary w-100">Salvar</button>
                                    </form>

                                    {{-- Formulário: análise de isenção (apenas quando solicitada) --}}
                                    @if($temIsencao)
                                        <hr class="my-2">
                                        <form
                                            action="{{ route('sigeconcursos.processos.inscricoes.atualizar-isencao', $processo->id_processo) }}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="inscricao_id" value="{{ $inscricao->id_inscricao }}">
                                            <p
                                                class="small fw-semibold mb-1 {{ $isencaoPendente ? 'text-warning' : 'text-muted' }}">
                                                @if($isencaoPendente)
                                                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                                @endif
                                                Isenção
                                                @if($inscricao->isencao)
                                                    <span class="fw-normal text-muted">— {{ $inscricao->isencao->titulo }}</span>
                                                @endif
                                            </p>
                                            @if($inscricao->justificativa_isencao)
                                                <div class="small text-muted mb-1">
                                                    <em>{{ Str::limit($inscricao->justificativa_isencao, 80) }}</em>
                                                </div>
                                            @endif
                                            <select name="novo_status_isencao" class="form-select form-select-sm mb-1" required>
                                                <option value="nao_solicitada" {{ $inscricao->status_isencao === 'nao_solicitada' ? 'selected' : '' }}>Não solicitada</option>
                                                <option value="pendente" {{ $inscricao->status_isencao === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                                <option value="deferida" {{ $inscricao->status_isencao === 'deferida' ? 'selected' : '' }}>Deferida</option>
                                                <option value="indeferida" {{ $inscricao->status_isencao === 'indeferida' ? 'selected' : '' }}>Indeferida</option>
                                            </select>
                                            <textarea name="parecer_isencao" rows="1" class="form-control form-control-sm mb-1"
                                                placeholder="Parecer">{{ $inscricao->parecer_isencao }}</textarea>
                                            <button type="submit" class="btn btn-sm btn-outline-primary w-100">Salvar
                                                isenção</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fa-solid fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                    Nenhuma inscrição encontrada com os filtros informados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($inscricoes->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $inscricoes->links() }}
        </div>
    @endif
@endsection