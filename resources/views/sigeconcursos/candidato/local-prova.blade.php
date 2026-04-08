@extends('layouts.main')

@section('title', 'Meu Local de Prova')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Meu Local de Prova</h2>
            <p class="text-muted mb-0">{{ $inscricao->processo->titulo }}</p>
        </div>
        <div class="d-flex flex-wrap gap-2 justify-content-md-end">
            <a href="{{ route('sigeconcursos.candidato.comprovante-local-prova.pdf', $inscricao->id_inscricao) }}"
                class="btn btn-outline-success btn-sm">
                <i class="fa-solid fa-file-pdf me-1"></i> PDF local/sala
            </a>
            <a href="{{ route('sigeconcursos.candidato.minhas-inscricoes') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Minhas Inscrições
            </a>
        </div>
    </div>

    {{-- Dados da inscrição --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
            <strong>Dados da inscrição</strong>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="text-muted small">Número de inscrição</div>
                    <div class="fw-semibold">{{ $inscricao->numero_inscricao ?: '-' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Processo</div>
                    <div class="fw-semibold">{{ $inscricao->processo->titulo }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Edital</div>
                    <div class="fw-semibold">{{ $inscricao->processo->numero_edital }}</div>
                </div>
                @if($inscricao->processo->data_prova)
                    <div class="col-md-4">
                        <div class="text-muted small">Data da prova</div>
                        <div class="fw-semibold">{{ $inscricao->processo->data_prova->format('d/m/Y') }}</div>
                    </div>
                @endif
                <div class="col-md-4">
                    <div class="text-muted small">Modalidade</div>
                    <div class="fw-semibold">{{ $inscricao->modalidadeLabel() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Local de prova --}}
    @php
        $localAtribuido = $inscricao->localAtribuido?->processoLocal?->localProva;
        $salaAtribuida = $inscricao->salaAtribuida?->sala;
        $assento = $inscricao->salaAtribuida?->numero_assento;
    @endphp

    @if(!$localAtribuido && !$salaAtribuida)
        <div class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation me-1"></i>
            Seu local de prova ainda não foi atribuído. Verifique novamente mais tarde.
        </div>
    @else
        @if($localAtribuido)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <strong><i class="fa-solid fa-location-dot me-2"></i>Local de Prova</strong>
                </div>
                <div class="card-body">
                    <h5 class="mb-1">{{ $localAtribuido->nome_local }}</h5>
                    @if($localAtribuido->endereco)
                        <p class="mb-1 text-muted">
                            {{ $localAtribuido->endereco }}, {{ $localAtribuido->numero_endereco }}
                            @if($localAtribuido->complemento_endereco)
                                — {{ $localAtribuido->complemento_endereco }}
                            @endif
                        </p>
                        @if($localAtribuido->bairro)
                            <p class="mb-1 text-muted">{{ $localAtribuido->bairro }}</p>
                        @endif
                    @endif
                    @if($localAtribuido->observacoes)
                        <div class="mt-2 alert alert-info mb-0">
                            <i class="fa-solid fa-circle-info me-1"></i>
                            {{ $localAtribuido->observacoes }}
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if($salaAtribuida)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <strong><i class="fa-solid fa-door-open me-2"></i>Sala e Assento</strong>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-4">
                            <div class="text-muted small">Sala</div>
                            <div class="h5 mb-0">{{ $salaAtribuida->nome_sala }}</div>
                        </div>
                        @if($salaAtribuida->bloco)
                            <div class="col-6 col-md-4">
                                <div class="text-muted small">Bloco</div>
                                <div class="h5 mb-0">{{ $salaAtribuida->bloco }}</div>
                            </div>
                        @endif
                        @if($assento)
                            <div class="col-6 col-md-4">
                                <div class="text-muted small">Nº do Assento</div>
                                <div class="h5 mb-0">{{ $assento }}</div>
                            </div>
                        @endif
                    </div>
                    @if($salaAtribuida->observacoes)
                        <div class="mt-2 alert alert-light mb-0">{{ $salaAtribuida->observacoes }}</div>
                    @endif
                </div>
            </div>
        @endif
    @endif

    <div class="text-muted small">
        <i class="fa-solid fa-circle-info me-1"></i>
        Em caso de dúvidas, entre em contato com a instituição organizadora.
    </div>
@endsection