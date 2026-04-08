@extends('layouts.main')

@section('title', 'SIGE Concursos | Minhas Inscrições')

@section('content')
    @once
        <style>
            .sc-insc-shell {
                --sc-ink: #16303a;
                --sc-muted: #607580;
                --sc-line: rgba(22, 48, 58, 0.12);
                --sc-surface: #ffffff;
                --sc-soft: #f5f8f9;
            }

            .sc-insc-hero {
                border: 0;
                border-radius: 20px;
                overflow: hidden;
                background:
                    radial-gradient(circle at top right, rgba(15, 118, 110, 0.16), transparent 40%),
                    linear-gradient(135deg, #fcfaf6 0%, #f4efe5 45%, #eef7f4 100%);
                box-shadow: 0 14px 32px rgba(17, 49, 58, 0.1);
            }

            .sc-insc-stat {
                border: 1px solid var(--sc-line);
                border-radius: 14px;
                background: var(--sc-surface);
                padding: 0.75rem 0.9rem;
            }

            .sc-insc-stat .label {
                color: var(--sc-muted);
                font-size: 0.74rem;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                font-weight: 700;
            }

            .sc-insc-stat .value {
                color: var(--sc-ink);
                font-size: 1.05rem;
                font-weight: 700;
            }

            .sc-insc-card,
            .sc-insc-table-wrap {
                border: 1px solid var(--sc-line);
                border-radius: 18px;
                background: var(--sc-surface);
                box-shadow: 0 10px 22px rgba(17, 49, 58, 0.08);
            }

            .sc-insc-table-wrap {
                overflow: hidden;
            }

            .sc-insc-table thead th {
                background: var(--sc-soft);
                color: var(--sc-muted);
                font-size: 0.78rem;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                white-space: nowrap;
                border-bottom: 1px solid var(--sc-line);
            }

            .sc-insc-table td {
                border-color: var(--sc-line);
                vertical-align: middle;
            }

            .sc-insc-mobile-item {
                border: 1px solid var(--sc-line);
                border-radius: 14px;
                background: var(--sc-surface);
                box-shadow: 0 8px 18px rgba(17, 49, 58, 0.07);
            }

            .sc-insc-mobile-row {
                display: flex;
                justify-content: space-between;
                gap: 0.8rem;
                border-top: 1px solid var(--sc-line);
                padding-top: 0.45rem;
                margin-top: 0.45rem;
                font-size: 0.9rem;
            }

            .sc-insc-mobile-row .label {
                color: var(--sc-muted);
                font-weight: 600;
            }

            @media (max-width: 991.98px) {
                .sc-insc-actions .btn {
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

    <div class="sc-insc-shell">
        @php
            $totalInscricoes = $inscricoes->count();
            $deferidas = $inscricoes->where('status_inscricao', 'deferido')->count();
            $pendentesIsencao = $inscricoes->where('status_isencao', 'pendente')->count();
        @endphp

        <div class="card sc-insc-hero mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-column flex-xl-row justify-content-between gap-4">
                    <div>
                        <h2 class="mb-1" style="color: #16303a;">Minhas Inscrições</h2>
                        <p class="text-muted mb-3">Acompanhe suas inscrições em concursos e processos seletivos.</p>
                        <div class="row g-2">
                            <div class="col-sm-4">
                                <div class="sc-insc-stat">
                                    <div class="label">Total</div>
                                    <div class="value">{{ $totalInscricoes }}</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="sc-insc-stat">
                                    <div class="label">Deferidas</div>
                                    <div class="value">{{ $deferidas }}</div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="sc-insc-stat">
                                    <div class="label">Isenção Pendente</div>
                                    <div class="value">{{ $pendentesIsencao }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sc-insc-actions d-grid gap-2" style="min-width: min(100%, 260px);">
                        <a href="{{ route('sigeconcursos.candidato.processos.index') }}" class="btn btn-primary">
                            <i class="fa-solid fa-file-circle-plus me-1"></i> Novo Processo
                        </a>
                        <a href="{{ route('sigeconcursos.candidato.minhas-isencoes') }}" class="btn btn-outline-warning">
                            <i class="fa-solid fa-percent me-1"></i> Minhas Isenções
                        </a>
                        <a href="{{ route('sigeconcursos.candidato.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="sc-insc-table-wrap d-none d-lg-block">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 sc-insc-table">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Processo</th>
                            <th>Modalidade</th>
                            <th>Status</th>
                            <th>Isenção</th>
                            <th>Pagamento</th>
                            <th>Data</th>
                            <th></th>
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

                                $badgeIsencao = [
                                    'nao_solicitada' => 'bg-secondary',
                                    'pendente' => 'bg-warning text-dark',
                                    'deferida' => 'bg-success',
                                    'indeferida' => 'bg-danger',
                                ][$inscricao->status_isencao] ?? 'bg-secondary';

                                $badgePagamento = [
                                    'nao_aplicavel' => 'bg-secondary',
                                    'pendente' => 'bg-warning text-dark',
                                    'aguardando_isencao' => 'bg-warning text-dark',
                                    'isento' => 'bg-success',
                                    'pago' => 'bg-success',
                                ][$inscricao->status_pagamento] ?? 'bg-secondary';
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $inscricao->numero_inscricao ?? '-' }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $inscricao->processo?->titulo }}</div>
                                    <div class="small text-muted">Edital {{ $inscricao->processo?->numero_edital }}</div>
                                </td>
                                <td>{{ $inscricao->modalidadeLabel() }}</td>
                                <td><span class="badge {{ $badgeStatus }}">{{ ucfirst($inscricao->status_inscricao) }}</span>
                                </td>
                                <td><span
                                        class="badge {{ $badgeIsencao }}">{{ ucfirst(str_replace('_', ' ', $inscricao->status_isencao)) }}</span>
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $badgePagamento }}">{{ ucfirst(str_replace('_', ' ', $inscricao->status_pagamento)) }}</span>
                                    <div class="small text-muted">
                                        {{ $inscricao->valor_taxa_aplicada !== null ? 'R$ ' . number_format((float) $inscricao->valor_taxa_aplicada, 2, ',', '.') : 'Sem taxa' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-nowrap">{{ $inscricao->created_at?->format('d/m/Y H:i') }}</div>
                                    @if($inscricao->isencao)
                                        <div class="small text-muted">Caso: {{ $inscricao->isencao->titulo }}</div>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('sigeconcursos.candidato.comprovante-inscricao.pdf', $inscricao->id_inscricao) }}"
                                        class="btn btn-sm btn-outline-primary mb-1">
                                        <i class="fa-solid fa-file-pdf me-1"></i> Comprovante
                                    </a>
                                    @if($inscricao->status_inscricao === 'deferido' && $inscricao->processo?->localProvaPublicado())
                                        <a href="{{ route('sigeconcursos.candidato.local-prova', $inscricao->id_inscricao) }}"
                                            class="btn btn-sm btn-success">
                                            <i class="fa-solid fa-location-dot me-1"></i> Ver local
                                        </a>
                                        <a href="{{ route('sigeconcursos.candidato.comprovante-local-prova.pdf', $inscricao->id_inscricao) }}"
                                            class="btn btn-sm btn-outline-success mt-1">
                                            <i class="fa-solid fa-file-pdf me-1"></i> PDF local/sala
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Você ainda não realizou inscrições neste
                                    módulo.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-lg-none">
            @forelse($inscricoes as $inscricao)
                @php
                    $badgeStatus = [
                        'inscrito' => 'bg-info',
                        'deferido' => 'bg-success',
                        'indeferido' => 'bg-danger',
                    ][$inscricao->status_inscricao] ?? 'bg-secondary';

                    $badgeIsencao = [
                        'nao_solicitada' => 'bg-secondary',
                        'pendente' => 'bg-warning text-dark',
                        'deferida' => 'bg-success',
                        'indeferida' => 'bg-danger',
                    ][$inscricao->status_isencao] ?? 'bg-secondary';

                    $badgePagamento = [
                        'nao_aplicavel' => 'bg-secondary',
                        'pendente' => 'bg-warning text-dark',
                        'aguardando_isencao' => 'bg-warning text-dark',
                        'isento' => 'bg-success',
                        'pago' => 'bg-success',
                    ][$inscricao->status_pagamento] ?? 'bg-secondary';
                @endphp
                <div class="sc-insc-mobile-item p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <div class="fw-semibold">{{ $inscricao->processo?->titulo }}</div>
                            <div class="small text-muted">Nº {{ $inscricao->numero_inscricao ?? '-' }}</div>
                            <div class="small text-muted">Edital {{ $inscricao->processo?->numero_edital }}</div>
                        </div>
                        <span class="badge {{ $badgeStatus }}">{{ ucfirst($inscricao->status_inscricao) }}</span>
                    </div>

                    <div class="sc-insc-mobile-row">
                        <span class="label">Modalidade</span>
                        <span>{{ $inscricao->modalidadeLabel() }}</span>
                    </div>
                    <div class="sc-insc-mobile-row">
                        <span class="label">Isenção</span>
                        <span
                            class="badge {{ $badgeIsencao }}">{{ ucfirst(str_replace('_', ' ', $inscricao->status_isencao)) }}</span>
                    </div>
                    <div class="sc-insc-mobile-row">
                        <span class="label">Pagamento</span>
                        <span
                            class="badge {{ $badgePagamento }}">{{ ucfirst(str_replace('_', ' ', $inscricao->status_pagamento)) }}</span>
                    </div>
                    <div class="sc-insc-mobile-row">
                        <span class="label">Valor</span>
                        <span>{{ $inscricao->valor_taxa_aplicada !== null ? 'R$ ' . number_format((float) $inscricao->valor_taxa_aplicada, 2, ',', '.') : 'Sem taxa' }}</span>
                    </div>
                    <div class="sc-insc-mobile-row">
                        <span class="label">Data</span>
                        <span>{{ $inscricao->created_at?->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($inscricao->isencao)
                        <div class="small text-muted mt-2">Caso: {{ $inscricao->isencao->titulo }}</div>
                    @endif
                    @if($inscricao->parecer_isencao)
                        <div class="small text-muted">Parecer: {{ $inscricao->parecer_isencao }}</div>
                    @endif

                    @if($inscricao->status_inscricao === 'deferido' && $inscricao->processo?->localProvaPublicado())
                        <a href="{{ route('sigeconcursos.candidato.local-prova', $inscricao->id_inscricao) }}"
                            class="btn btn-success btn-sm w-100 mt-3">
                            <i class="fa-solid fa-location-dot me-1"></i> Ver local de prova
                        </a>
                    @endif

                    <a href="{{ route('sigeconcursos.candidato.comprovante-inscricao.pdf', $inscricao->id_inscricao) }}"
                        class="btn btn-outline-primary btn-sm w-100 mt-2">
                        <i class="fa-solid fa-file-pdf me-1"></i> Baixar comprovante de inscricao
                    </a>

                    @if($inscricao->status_inscricao === 'deferido' && $inscricao->processo?->localProvaPublicado())
                        <a href="{{ route('sigeconcursos.candidato.comprovante-local-prova.pdf', $inscricao->id_inscricao) }}"
                            class="btn btn-outline-success btn-sm w-100 mt-2">
                            <i class="fa-solid fa-file-pdf me-1"></i> Baixar PDF local/sala
                        </a>
                    @endif
                </div>
            @empty
                <div class="sc-insc-card p-4 text-center text-muted">Você ainda não realizou inscrições neste módulo.</div>
            @endforelse
        </div>
    </div>

    @if($inscricoes->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $inscricoes->links() }}
        </div>
    @endif
@endsection