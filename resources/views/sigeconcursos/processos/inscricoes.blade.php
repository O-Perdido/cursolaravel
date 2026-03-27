@extends('layouts.main')

@section('title', 'SIGE Concursos | Inscrições do Processo')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Inscrições do Processo</h2>
            <p class="text-muted mb-0">{{ $processo->titulo }} - Edital {{ $processo->numero_edital }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sigeconcursos.processos.show', $processo->id_processo) }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Voltar ao Processo
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Total</div>
                    <div class="h4 mb-0">{{ $resumo['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Pendentes</div>
                    <div class="h4 mb-0 text-info">{{ $resumo['pendentes'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Deferidas</div>
                    <div class="h4 mb-0 text-success">{{ $resumo['deferidas'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Indeferidas</div>
                    <div class="h4 mb-0 text-danger">{{ $resumo['indeferidas'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ffc107 !important;">
                <div class="card-body">
                    <div class="text-muted small">Aptos para homologar</div>
                    <div class="h4 mb-0 text-warning">{{ $resumo['aptos'] }}</div>
                    <div class="small text-muted mt-1">Inscrito + pagamento regularizado</div>
                </div>
            </div>
        </div>
        <div class="col-md-8 d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('sigeconcursos.processos.inscricoes', $processo->id_processo) }}?status_inscricao=inscrito&status_pagamento=pago"
                class="btn btn-sm btn-outline-primary">
                <i class="fa-solid fa-filter me-1"></i> Ver pagos pendentes
            </a>
            <a href="{{ route('sigeconcursos.processos.inscricoes', $processo->id_processo) }}?status_inscricao=inscrito&status_pagamento=isento"
                class="btn btn-sm btn-outline-success">
                <i class="fa-solid fa-filter me-1"></i> Ver isentos pendentes
            </a>
            <a href="{{ route('sigeconcursos.processos.inscricoes', $processo->id_processo) }}?status_inscricao=inscrito&status_pagamento=nao_aplicavel"
                class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-filter me-1"></i> Gratuitos pendentes
            </a>
        </div>
    </div>

    <form method="GET" class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label mb-1" for="nome">Nome</label>
                    <input type="text" name="nome" id="nome" class="form-control form-control-sm"
                        value="{{ request('nome') }}" placeholder="Nome do candidato">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1" for="cpf">CPF</label>
                    <input type="text" name="cpf" id="cpf" class="form-control form-control-sm" value="{{ request('cpf') }}"
                        placeholder="Somente números">
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1" for="modalidade_concorrencia">Modalidade</label>
                    <select name="modalidade_concorrencia" id="modalidade_concorrencia" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <option value="ampla_concorrencia" {{ request('modalidade_concorrencia') === 'ampla_concorrencia' ? 'selected' : '' }}>Ampla</option>
                        <option value="pcd" {{ request('modalidade_concorrencia') === 'pcd' ? 'selected' : '' }}>PCD</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1" for="status_inscricao">Status inscrição</label>
                    <select name="status_inscricao" id="status_inscricao" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="inscrito" {{ request('status_inscricao') === 'inscrito' ? 'selected' : '' }}>Inscrito
                        </option>
                        <option value="deferido" {{ request('status_inscricao') === 'deferido' ? 'selected' : '' }}>Deferido
                        </option>
                        <option value="indeferido" {{ request('status_inscricao') === 'indeferido' ? 'selected' : '' }}>
                            Indeferido</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-1" for="status_isencao">Status isenção</label>
                    <select name="status_isencao" id="status_isencao" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="nao_solicitada" {{ request('status_isencao') === 'nao_solicitada' ? 'selected' : '' }}>
                            Não solicitada</option>
                        <option value="pendente" {{ request('status_isencao') === 'pendente' ? 'selected' : '' }}>Pendente
                        </option>
                        <option value="deferida" {{ request('status_isencao') === 'deferida' ? 'selected' : '' }}>Deferida
                        </option>
                        <option value="indeferida" {{ request('status_isencao') === 'indeferida' ? 'selected' : '' }}>
                            Indeferida</option>
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
                </div>
            </div>
            <div class="row g-2 align-items-end mt-1">
                <div class="col-md-2">
                    <label class="form-label mb-1" for="status_pagamento">Pagamento</label>
                    <select name="status_pagamento" id="status_pagamento" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="nao_aplicavel" {{ request('status_pagamento') === 'nao_aplicavel' ? 'selected' : '' }}>
                            Não aplicável</option>
                        <option value="pendente" {{ request('status_pagamento') === 'pendente' ? 'selected' : '' }}>Pendente
                        </option>
                        <option value="aguardando_isencao" {{ request('status_pagamento') === 'aguardando_isencao' ? 'selected' : '' }}>Aguardando isenção</option>
                        <option value="isento" {{ request('status_pagamento') === 'isento' ? 'selected' : '' }}>Isento
                        </option>
                        <option value="pago" {{ request('status_pagamento') === 'pago' ? 'selected' : '' }}>Pago</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <a href="{{ route('sigeconcursos.processos.inscricoes', $processo->id_processo) }}"
                        class="btn btn-outline-secondary btn-sm">Limpar</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Número</th>
                            <th>Candidato</th>
                            <th>Modalidade</th>
                            <th>Status</th>
                            <th>Isenção/Pagamento</th>
                            <th>Documentos</th>
                            <th style="width: 420px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inscricoes as $inscricao)
                            @php
                                $badgeStatus = [
                                    'inscrito' => 'bg-info',
                                    'deferido' => 'bg-success',
                                    'indeferido' => 'bg-danger',
                                ][$inscricao->status_inscricao] ?? 'bg-secondary';
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $inscricao->numero_inscricao ?: '-' }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $inscricao->candidato?->nome_completo }}</div>
                                    <div class="small text-muted">CPF: {{ $inscricao->candidato?->numero_cpf }}</div>
                                    <div class="small text-muted">{{ $inscricao->candidato?->email }}</div>
                                    <div class="small text-muted">{{ $inscricao->created_at?->format('d/m/Y H:i') }}</div>
                                </td>
                                <td>{{ $inscricao->modalidadeLabel() }}</td>
                                <td>
                                    <span class="badge {{ $badgeStatus }}">{{ ucfirst($inscricao->status_inscricao) }}</span>
                                    @if($inscricao->observacoes)
                                        <div class="small text-muted mt-1" style="white-space: pre-line;">
                                            {{ $inscricao->observacoes }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="small"><strong>Isenção:</strong>
                                        {{ ucfirst(str_replace('_', ' ', $inscricao->status_isencao)) }}</div>
                                    @if($inscricao->isencao)
                                        <div class="small text-muted"><strong>Caso:</strong> {{ $inscricao->isencao->titulo }}</div>
                                    @endif
                                    @if($inscricao->justificativa_isencao)
                                        <div class="small text-muted mt-1" style="white-space: pre-line;">
                                            <strong>Justificativa:</strong> {{ $inscricao->justificativa_isencao }}</div>
                                    @endif
                                    @if($inscricao->parecer_isencao)
                                        <div class="small text-muted mt-1" style="white-space: pre-line;"><strong>Parecer:</strong>
                                            {{ $inscricao->parecer_isencao }}</div>
                                    @endif
                                    <div class="small"><strong>Pagamento:</strong>
                                        {{ ucfirst(str_replace('_', ' ', $inscricao->status_pagamento)) }}</div>
                                    <div class="small text-muted">
                                        {{ $inscricao->valor_taxa_aplicada !== null ? 'R$ ' . number_format((float) $inscricao->valor_taxa_aplicada, 2, ',', '.') : 'Sem taxa' }}
                                    </div>
                                </td>
                                <td>
                                    @if($inscricao->solicitou_condicao_especial)
                                        <div class="small mb-1">
                                            <strong>Condição especial:</strong>
                                            {{ $inscricao->descricao_condicao_especial ?: 'Solicitada sem descrição.' }}
                                        </div>
                                        @if($inscricao->caminho_documento_condicao_especial)
                                            <a href="{{ asset('storage/' . $inscricao->caminho_documento_condicao_especial) }}"
                                                target="_blank" class="btn btn-sm btn-outline-primary mb-2">Laudo</a>
                                        @endif
                                    @endif

                                    @if($inscricao->documentos->count() > 0)
                                        @foreach($inscricao->documentos as $documento)
                                            <div class="small mb-1">
                                                {{ $documento->titulo_documento }}
                                                <a href="{{ asset('storage/' . $documento->caminho_arquivo) }}" target="_blank"
                                                    class="ms-1">Abrir</a>
                                            </div>
                                        @endforeach
                                    @endif

                                    @if($inscricao->documentosIsencao->count() > 0)
                                        <div class="small fw-semibold mt-2 mb-1">Documentos de isenção</div>
                                        @foreach($inscricao->documentosIsencao as $documentoIsencao)
                                            <div class="small mb-1">
                                                {{ $documentoIsencao->nome_documento }}
                                                <a href="{{ asset('storage/' . $documentoIsencao->caminho_arquivo) }}" target="_blank"
                                                    class="ms-1">Abrir</a>
                                            </div>
                                        @endforeach
                                    @endif

                                    @if($inscricao->documentos->count() === 0 && $inscricao->documentosIsencao->count() === 0)
                                        <span class="text-muted small">Sem documentos adicionais.</span>
                                    @endif
                                </td>
                                <td>
                                    <form
                                        action="{{ route('sigeconcursos.processos.inscricoes.atualizar-status', $processo->id_processo) }}"
                                        method="POST" class="d-grid gap-2">
                                        @csrf
                                        <input type="hidden" name="inscricao_id" value="{{ $inscricao->id_inscricao }}">
                                        <select name="novo_status" class="form-select form-select-sm" required>
                                            <option value="inscrito" {{ $inscricao->status_inscricao === 'inscrito' ? 'selected' : '' }}>Inscrito</option>
                                            <option value="deferido" {{ $inscricao->status_inscricao === 'deferido' ? 'selected' : '' }}>Deferido</option>
                                            <option value="indeferido" {{ $inscricao->status_inscricao === 'indeferido' ? 'selected' : '' }}>Indeferido</option>
                                        </select>
                                        <textarea name="observacoes" rows="2" class="form-control form-control-sm"
                                            placeholder="Observações da análise">{{ $inscricao->observacoes }}</textarea>
                                        <button type="submit" class="btn btn-sm btn-primary">Salvar Status</button>
                                    </form>

                                    <form
                                        action="{{ route('sigeconcursos.processos.inscricoes.atualizar-isencao', $processo->id_processo) }}"
                                        method="POST" class="d-grid gap-2 mt-2">
                                        @csrf
                                        <input type="hidden" name="inscricao_id" value="{{ $inscricao->id_inscricao }}">
                                        <select name="novo_status_isencao" class="form-select form-select-sm" required>
                                            <option value="nao_solicitada" {{ $inscricao->status_isencao === 'nao_solicitada' ? 'selected' : '' }}>Não solicitada</option>
                                            <option value="pendente" {{ $inscricao->status_isencao === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                            <option value="deferida" {{ $inscricao->status_isencao === 'deferida' ? 'selected' : '' }}>Deferida</option>
                                            <option value="indeferida" {{ $inscricao->status_isencao === 'indeferida' ? 'selected' : '' }}>Indeferida</option>
                                        </select>
                                        <textarea name="parecer_isencao" rows="2" class="form-control form-control-sm"
                                            placeholder="Parecer da análise de isenção">{{ $inscricao->parecer_isencao }}</textarea>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Salvar Isenção</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Nenhuma inscrição encontrada com os filtros
                                    informados.</td>
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