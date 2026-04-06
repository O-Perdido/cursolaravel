@extends('layouts.main')

@section('title', 'SIGE Concursos | Minhas Isencoes')

@section('content')
    @once
        <style>
            .sc-isen-shell {
                --sc-ink: #16303a;
                --sc-muted: #607580;
                --sc-line: rgba(22, 48, 58, 0.12);
                --sc-surface: #ffffff;
                --sc-soft: #f5f8f9;
            }

            .sc-isen-hero {
                border: 0;
                border-radius: 20px;
                overflow: hidden;
                background:
                    radial-gradient(circle at top right, rgba(15, 118, 110, 0.16), transparent 40%),
                    linear-gradient(135deg, #fcfaf6 0%, #f4efe5 45%, #eef7f4 100%);
                box-shadow: 0 14px 32px rgba(17, 49, 58, 0.1);
            }

            .sc-isen-stat {
                border: 1px solid var(--sc-line);
                border-radius: 14px;
                background: var(--sc-surface);
                padding: 0.75rem 0.9rem;
            }

            .sc-isen-stat .label {
                color: var(--sc-muted);
                font-size: 0.74rem;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                font-weight: 700;
            }

            .sc-isen-stat .value {
                color: var(--sc-ink);
                font-size: 1.05rem;
                font-weight: 700;
            }

            .sc-isen-card,
            .sc-isen-table-wrap {
                border: 1px solid var(--sc-line);
                border-radius: 18px;
                background: var(--sc-surface);
                box-shadow: 0 10px 22px rgba(17, 49, 58, 0.08);
            }

            .sc-isen-table-wrap {
                overflow: hidden;
            }

            .sc-isen-filter {
                border: 1px solid var(--sc-line);
                border-radius: 14px;
                background: var(--sc-surface);
            }

            .sc-isen-table thead th {
                background: var(--sc-soft);
                color: var(--sc-muted);
                font-size: 0.78rem;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                white-space: nowrap;
                border-bottom: 1px solid var(--sc-line);
            }

            .sc-isen-table td {
                border-color: var(--sc-line);
                vertical-align: middle;
            }

            .sc-isen-mobile-item {
                border: 1px solid var(--sc-line);
                border-radius: 14px;
                background: var(--sc-surface);
                box-shadow: 0 8px 18px rgba(17, 49, 58, 0.07);
            }

            .sc-isen-mobile-row {
                display: flex;
                justify-content: space-between;
                gap: 0.8rem;
                border-top: 1px solid var(--sc-line);
                padding-top: 0.45rem;
                margin-top: 0.45rem;
                font-size: 0.9rem;
            }

            .sc-isen-mobile-row .label {
                color: var(--sc-muted);
                font-weight: 600;
            }

            @media (max-width: 991.98px) {

                .sc-isen-actions .btn,
                .sc-isen-filter-actions .btn {
                    width: 100%;
                }
            }
        </style>
    @endonce

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="sc-isen-shell">
        @php
            $totalIsencoes = $isencoes->count();
            $deferidas = $isencoes->where('status_isencao', 'deferida')->count();
            $pendentes = $isencoes->where('status_isencao', 'pendente')->count();
        @endphp

        <div class="card sc-isen-hero mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-column flex-xl-row justify-content-between gap-4">
                    <div>
                        <h2 class="mb-1" style="color: #16303a;">Minhas Isencoes</h2>
                        <p class="text-muted mb-3">Acompanhe o resultado das solicitacoes de isencao de taxa.</p>
                        <div class="row g-2">
                            <div class="col-sm-4">
                                <div class="sc-isen-stat">
                                    <div class="label">Total</div>
                                    <div class="value">{{ $totalIsencoes }}</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="sc-isen-stat">
                                    <div class="label">Deferidas</div>
                                    <div class="value">{{ $deferidas }}</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="sc-isen-stat">
                                    <div class="label">Pendentes</div>
                                    <div class="value">{{ $pendentes }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sc-isen-actions d-grid gap-2" style="min-width: min(100%, 260px);">
                        <a href="{{ route('sigeconcursos.candidato.minhas-inscricoes') }}" class="btn btn-outline-primary">
                            <i class="fa-solid fa-clipboard-list me-1"></i> Minhas Inscricoes
                        </a>
                        <a href="{{ route('sigeconcursos.candidato.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" class="sc-isen-filter p-3 p-lg-4 mb-4">
            <div class="row g-2 align-items-end">
                <div class="col-lg-5">
                    <label for="status_isencao" class="form-label mb-1">Filtrar por status</label>
                    <select id="status_isencao" name="status_isencao" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendente" {{ request('status_isencao') === 'pendente' ? 'selected' : '' }}>Pendente
                        </option>
                        <option value="deferida" {{ request('status_isencao') === 'deferida' ? 'selected' : '' }}>Deferida
                        </option>
                        <option value="indeferida" {{ request('status_isencao') === 'indeferida' ? 'selected' : '' }}>
                            Indeferida</option>
                    </select>
                </div>
                <div class="col-lg-3 d-grid d-lg-flex gap-2 sc-isen-filter-actions">
                    <button type="submit" class="btn btn-primary flex-fill">Filtrar</button>
                    <a href="{{ route('sigeconcursos.candidato.minhas-isencoes') }}"
                        class="btn btn-outline-secondary flex-fill">Limpar</a>
                </div>
            </div>
        </form>

        <div class="sc-isen-table-wrap d-none d-lg-block">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 sc-isen-table">
                    <thead>
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

        <div class="d-lg-none">
            @forelse($isencoes as $inscricao)
                @php
                    $badgeIsencao = [
                        'pendente' => 'bg-warning text-dark',
                        'deferida' => 'bg-success',
                        'indeferida' => 'bg-danger',
                        'nao_solicitada' => 'bg-secondary',
                    ][$inscricao->status_isencao] ?? 'bg-secondary';
                @endphp

                <div class="sc-isen-mobile-item p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <div class="fw-semibold">{{ $inscricao->processo?->titulo }}</div>
                            <div class="small text-muted">Nº {{ $inscricao->numero_inscricao ?: '-' }}</div>
                            <div class="small text-muted">Edital {{ $inscricao->processo?->numero_edital }}</div>
                        </div>
                        <span
                            class="badge {{ $badgeIsencao }}">{{ ucfirst(str_replace('_', ' ', $inscricao->status_isencao)) }}</span>
                    </div>

                    <div class="sc-isen-mobile-row">
                        <span class="label">Caso</span>
                        <span>{{ $inscricao->isencao?->titulo ?: 'Nao informado' }}</span>
                    </div>
                    <div class="sc-isen-mobile-row">
                        <span class="label">Pagamento</span>
                        <span>{{ ucfirst(str_replace('_', ' ', $inscricao->status_pagamento)) }}</span>
                    </div>
                    <div class="sc-isen-mobile-row">
                        <span class="label">Parecer</span>
                        <span>{{ $inscricao->parecer_isencao ?: 'Aguardando analise.' }}</span>
                    </div>

                    <div class="mt-2">
                        <div class="small text-muted mb-1">Justificativa</div>
                        <div class="small" style="white-space: pre-line;">
                            {{ $inscricao->justificativa_isencao ?: 'Sem justificativa registrada.' }}</div>
                    </div>

                    <div class="mt-2">
                        <div class="small text-muted mb-1">Documentos</div>
                        @if($inscricao->documentosIsencao->count() > 0)
                            @foreach($inscricao->documentosIsencao as $documento)
                                <div class="small mb-1">
                                    {{ $documento->nome_documento }}
                                    <a href="{{ asset('storage/' . $documento->caminho_arquivo) }}" target="_blank"
                                        class="ms-1">Abrir</a>
                                </div>
                            @endforeach
                        @else
                            <span class="small text-muted">Sem anexos.</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="sc-isen-card p-4 text-center text-muted">Voce ainda nao possui solicitacoes de isencao.</div>
            @endforelse
        </div>
    </div>

    @if($isencoes->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $isencoes->links() }}
        </div>
    @endif
@endsection