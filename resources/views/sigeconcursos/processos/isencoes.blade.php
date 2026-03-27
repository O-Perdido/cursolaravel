@extends('layouts.main')

@section('title', 'SIGE Concursos | Solicitações de Isenção')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Solicitações de Isenção</h2>
            <p class="text-muted mb-0">{{ $processo->titulo }} - Edital {{ $processo->numero_edital }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sigeconcursos.processos.inscricoes', $processo->id_processo) }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-clipboard-list me-1"></i> Ver Inscrições
            </a>
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
                    <div class="h4 mb-0 text-warning">{{ $resumo['pendentes'] }}</div>
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

    <form method="GET" class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label mb-1" for="nome">Nome</label>
                    <input type="text" name="nome" id="nome" class="form-control form-control-sm" value="{{ request('nome') }}"
                        placeholder="Nome do candidato">
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1" for="cpf">CPF</label>
                    <input type="text" name="cpf" id="cpf" class="form-control form-control-sm" value="{{ request('cpf') }}"
                        placeholder="Somente números">
                </div>
                <div class="col-md-3">
                    <label class="form-label mb-1" for="status_isencao">Status isenção</label>
                    <select name="status_isencao" id="status_isencao" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="pendente" {{ request('status_isencao') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="deferida" {{ request('status_isencao') === 'deferida' ? 'selected' : '' }}>Deferida</option>
                        <option value="indeferida" {{ request('status_isencao') === 'indeferida' ? 'selected' : '' }}>Indeferida</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid d-md-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill"><i class="fas fa-search"></i></button>
                    <a href="{{ route('sigeconcursos.processos.isencoes', $processo->id_processo) }}" class="btn btn-outline-secondary btn-sm flex-fill">Limpar</a>
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
                            <th>Inscrição</th>
                            <th>Candidato</th>
                            <th>Caso e Justificativa</th>
                            <th>Documentos</th>
                            <th>Status</th>
                            <th style="width: 300px;">Análise</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($isencoes as $inscricao)
                            @php
                                $badgeIsencao = [
                                    'pendente' => 'bg-warning text-dark',
                                    'deferida' => 'bg-success',
                                    'indeferida' => 'bg-danger',
                                ][$inscricao->status_isencao] ?? 'bg-secondary';
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $inscricao->numero_inscricao ?: '-' }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $inscricao->candidato?->nome_completo }}</div>
                                    <div class="small text-muted">CPF: {{ $inscricao->candidato?->numero_cpf }}</div>
                                    <div class="small text-muted">{{ $inscricao->created_at?->format('d/m/Y H:i') }}</div>
                                </td>
                                <td>
                                    <div class="small"><strong>Caso:</strong> {{ $inscricao->isencao?->titulo ?: 'Não informado' }}</div>
                                    <div class="small text-muted mt-1" style="white-space: pre-line;">
                                        {{ $inscricao->justificativa_isencao ?: 'Sem justificativa registrada.' }}
                                    </div>
                                </td>
                                <td>
                                    @if($inscricao->documentosIsencao->count() > 0)
                                        @foreach($inscricao->documentosIsencao as $documento)
                                            <div class="small mb-1">
                                                {{ $documento->nome_documento }}
                                                <a href="{{ asset('storage/' . $documento->caminho_arquivo) }}" target="_blank" class="ms-1">Abrir</a>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">Sem documentos anexados.</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $badgeIsencao }}">{{ ucfirst(str_replace('_', ' ', $inscricao->status_isencao)) }}</span>
                                    @if($inscricao->parecer_isencao)
                                        <div class="small text-muted mt-1" style="white-space: pre-line;">{{ $inscricao->parecer_isencao }}</div>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('sigeconcursos.processos.inscricoes.atualizar-isencao', $processo->id_processo) }}" method="POST" class="d-grid gap-2">
                                        @csrf
                                        <input type="hidden" name="inscricao_id" value="{{ $inscricao->id_inscricao }}">
                                        <select name="novo_status_isencao" class="form-select form-select-sm" required>
                                            <option value="pendente" {{ $inscricao->status_isencao === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                            <option value="deferida" {{ $inscricao->status_isencao === 'deferida' ? 'selected' : '' }}>Deferida</option>
                                            <option value="indeferida" {{ $inscricao->status_isencao === 'indeferida' ? 'selected' : '' }}>Indeferida</option>
                                        </select>
                                        <textarea name="parecer_isencao" rows="2" class="form-control form-control-sm" placeholder="Parecer da análise">{{ $inscricao->parecer_isencao }}</textarea>
                                        <button type="submit" class="btn btn-sm btn-primary">Salvar análise</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Nenhuma solicitação de isenção encontrada.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($isencoes->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $isencoes->links() }}
        </div>
    @endif
@endsection