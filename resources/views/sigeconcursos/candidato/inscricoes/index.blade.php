@extends('layouts.main')

@section('title', 'SIGE Concursos | Minhas Inscrições')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Minhas Inscrições</h2>
            <p class="text-muted mb-0">Acompanhe suas inscrições em concursos e processos seletivos.</p>
        </div>
        <div class="d-flex gap-2">
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

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
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
                            <tr>
                                <td class="fw-semibold">{{ $inscricao->numero_inscricao ?? '-' }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $inscricao->processo?->titulo }}</div>
                                    <div class="small text-muted">Edital {{ $inscricao->processo?->numero_edital }}</div>
                                </td>
                                <td>{{ $inscricao->modalidadeLabel() }}</td>
                                <td>
                                    @php
                                        $badgeStatus = [
                                            'inscrito' => 'bg-info',
                                            'deferido' => 'bg-success',
                                            'indeferido' => 'bg-danger',
                                        ][$inscricao->status_inscricao] ?? 'bg-secondary';
                                    @endphp
                                    <span class="badge {{ $badgeStatus }}">{{ ucfirst($inscricao->status_inscricao) }}</span>
                                </td>
                                <td>
                                    @php
                                        $badgeIsencao = [
                                            'nao_solicitada' => 'bg-secondary',
                                            'pendente' => 'bg-warning text-dark',
                                            'deferida' => 'bg-success',
                                            'indeferida' => 'bg-danger',
                                        ][$inscricao->status_isencao] ?? 'bg-secondary';
                                    @endphp
                                    <span
                                        class="badge {{ $badgeIsencao }}">{{ ucfirst(str_replace('_', ' ', $inscricao->status_isencao)) }}</span>
                                </td>
                                <td>
                                    @php
                                        $badgePagamento = [
                                            'nao_aplicavel' => 'bg-secondary',
                                            'pendente' => 'bg-warning text-dark',
                                            'aguardando_isencao' => 'bg-warning text-dark',
                                            'isento' => 'bg-success',
                                            'pago' => 'bg-success',
                                        ][$inscricao->status_pagamento] ?? 'bg-secondary';
                                    @endphp
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
                                    @if($inscricao->parecer_isencao)
                                        <div class="small text-muted">Parecer: {{ $inscricao->parecer_isencao }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($inscricao->status_inscricao === 'deferido' && $inscricao->processo?->localProvaPublicado())
                                        <a href="{{ route('sigeconcursos.candidato.local-prova', $inscricao->id_inscricao) }}"
                                            class="btn btn-sm btn-success">
                                            <i class="fa-solid fa-location-dot me-1"></i> Ver local
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
    </div>

    @if($inscricoes->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $inscricoes->links() }}
        </div>
    @endif
@endsection