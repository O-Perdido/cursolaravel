@extends('layouts.main')

@section('title', 'SIGE Concursos | Minhas Isencoes')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Minhas Isencoes</h2>
            <p class="text-muted mb-0">Acompanhe o resultado das solicitacoes de isencao de taxa.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sigeconcursos.candidato.minhas-inscricoes') }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-clipboard-list me-1"></i> Minhas Inscricoes
            </a>
            <a href="{{ route('sigeconcursos.candidato.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>

    <form method="GET" class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label for="status_isencao" class="form-label mb-1">Filtrar por status</label>
                    <select id="status_isencao" name="status_isencao" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="pendente" {{ request('status_isencao') === 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="deferida" {{ request('status_isencao') === 'deferida' ? 'selected' : '' }}>Deferida</option>
                        <option value="indeferida" {{ request('status_isencao') === 'indeferida' ? 'selected' : '' }}>Indeferida</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid d-md-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">Filtrar</button>
                    <a href="{{ route('sigeconcursos.candidato.minhas-isencoes') }}" class="btn btn-outline-secondary btn-sm flex-fill">Limpar</a>
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
                            <th>Inscricao</th>
                            <th>Processo</th>
                            <th>Caso de Isencao</th>
                            <th>Status</th>
                            <th>Parecer</th>
                            <th>Documentos</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($isencoes as $inscricao)
                            @php
                                $badgeIsencao = [
                                    'pendente' => 'bg-warning text-dark',
                                    'deferida' => 'bg-success',
                                    'indeferida' => 'bg-danger',
                                    'nao_solicitada' => 'bg-secondary',
                                ][$inscricao->status_isencao] ?? 'bg-secondary';
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $inscricao->numero_inscricao ?: '-' }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $inscricao->processo?->titulo }}</div>
                                    <div class="small text-muted">Edital {{ $inscricao->processo?->numero_edital }}</div>
                                </td>
                                <td>
                                    <div class="small fw-semibold">{{ $inscricao->isencao?->titulo ?: 'Nao informado' }}</div>
                                    <div class="small text-muted" style="white-space: pre-line;">
                                        {{ $inscricao->justificativa_isencao ?: 'Sem justificativa registrada.' }}
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $badgeIsencao }}">{{ ucfirst(str_replace('_', ' ', $inscricao->status_isencao)) }}</span>
                                    <div class="small text-muted mt-1">Pagamento:
                                        {{ ucfirst(str_replace('_', ' ', $inscricao->status_pagamento)) }}</div>
                                </td>
                                <td>
                                    <div class="small" style="white-space: pre-line;">
                                        {{ $inscricao->parecer_isencao ?: 'Aguardando analise.' }}</div>
                                </td>
                                <td>
                                    @if($inscricao->documentosIsencao->count() > 0)
                                        @foreach($inscricao->documentosIsencao as $documento)
                                            <div class="small mb-1">
                                                {{ $documento->nome_documento }}
                                                <a href="{{ asset('storage/' . $documento->caminho_arquivo) }}" target="_blank"
                                                    class="ms-1">Abrir</a>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-muted small">Sem anexos.</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Voce ainda nao possui solicitacoes de
                                    isencao.</td>
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