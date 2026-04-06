@extends('layouts.main')

@section('title', 'SIGE Concursos | Distribuição por Salas')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Distribuição por Salas</h2>
            <p class="text-muted mb-0">{{ $processo->titulo }} — Edital {{ $processo->numero_edital }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('sigeconcursos.processos.distribuicao-locais', $processo->id_processo) }}"
                class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-map-location-dot me-1"></i> ← Locais
            </a>
            <a href="{{ route('sigeconcursos.processos.show', $processo->id_processo) }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Voltar ao Processo
            </a>
        </div>
    </div>

    @include('sigeconcursos.processos._workflow-hub', ['processo' => $processo])

    {{-- Resumo --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Distribuídos em locais</div>
                    <div class="h4 mb-0 {{ $totalDistribuidosLocal > 0 ? 'text-success' : 'text-warning' }}">
                        {{ $totalDistribuidosLocal }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Distribuídos em salas</div>
                    <div class="h4 mb-0 {{ $totalDistribuidosSala > 0 ? 'text-success' : 'text-muted' }}">
                        {{ $totalDistribuidosSala }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Sem sala atribuída</div>
                    <div
                        class="h4 mb-0 {{ ($totalDistribuidosLocal - $totalDistribuidosSala) > 0 ? 'text-warning' : 'text-muted' }}">
                        {{ $totalDistribuidosLocal - $totalDistribuidosSala }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ações principais --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex flex-wrap gap-3 align-items-center">
            @if($totalDistribuidosLocal === 0)
                <div class="alert alert-warning mb-0 flex-grow-1">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                    Nenhum candidato foi distribuído por locais ainda.
                    <a href="{{ route('sigeconcursos.processos.distribuicao-locais', $processo->id_processo) }}"
                        class="alert-link">Ir para distribuição por locais</a>.
                </div>
            @else
                <form action="{{ route('sigeconcursos.processos.distribuicao-salas.distribuir', $processo->id_processo) }}"
                    method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary"
                        onclick="return confirm('Isso irá distribuir todos os candidatos nas salas disponíveis de cada local. A distribuição anterior por salas será substituída. Confirmar?')">
                        <i class="fa-solid fa-door-open me-1"></i>
                        Distribuir automaticamente por salas
                    </button>
                </form>

                @if($totalDistribuidosSala > 0)
                    <form action="{{ route('sigeconcursos.processos.distribuicao-salas.limpar', $processo->id_processo) }}"
                        method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm"
                            onclick="return confirm('Isso irá remover TODA a distribuição por salas. Confirmar?')">
                            <i class="fa-solid fa-trash me-1"></i> Limpar
                        </button>
                    </form>

                    @if(!$processo->localProvaPublicado())
                        <form action="{{ route('sigeconcursos.processos.local-prova.publicar', $processo->id_processo) }}"
                            method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm"
                                onclick="return confirm('Publicar local/sala de prova para os candidatos deferidos agora?')">
                                <i class="fa-solid fa-bullhorn me-1"></i> Publicar local de prova
                            </button>
                        </form>
                    @endif
                @endif

                <div class="text-muted small">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    Candidatos são distribuídos em ordem alfabética dentro de cada local, respeitando a capacidade de cada sala.
                </div>
            @endif
        </div>
    </div>

    {{-- Locais → Salas → Candidatos --}}
    @forelse($locais as $processoLocal)
        @php
            $local = $processoLocal->localProva;
            $salas = $local?->salas ?? collect();
        @endphp

        <div class="mb-5">
            <h5 class="mb-3">
                <i class="fa-solid fa-location-dot me-2 text-primary"></i>
                {{ $local?->nome_local ?? 'Local sem nome' }}
                @if($local?->endereco)
                    <small class="text-muted fw-normal fs-6"> — {{ $local->endereco }}, {{ $local->numero_endereco }}</small>
                @endif
            </h5>

            @if($salas->isEmpty())
                <div class="alert alert-warning">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                    Nenhuma sala ativa cadastrada neste local.
                    <a href="{{ route('sigeconcursos.locais-prova.show', $local?->id_local_prova) }}" class="alert-link">Gerenciar
                        salas</a>.
                </div>
            @else
                <div class="row g-3">
                    @foreach($salas as $sala)
                        @php
                            $atribuicoesNaSala = $sala->inscricoesAtribuidas;
                            $ocupados = $atribuicoesNaSala->count();
                            $capacidade = $sala->capacidade_maxima;
                            $percentual = $capacidade > 0 ? min(100, round($ocupados / $capacidade * 100)) : 0;
                            $corBarra = $percentual >= 100 ? 'bg-danger' : ($percentual >= 80 ? 'bg-warning' : 'bg-success');
                        @endphp
                        <div class="col-12 col-md-6 col-xl-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                    <div>
                                        <strong>{{ $sala->nome_sala }}</strong>
                                        @if($sala->bloco)
                                            <span class="text-muted small"> — Bloco {{ $sala->bloco }}</span>
                                        @endif
                                    </div>
                                    <span class="badge {{ $ocupados > 0 ? 'bg-primary' : 'bg-secondary' }}">
                                        {{ $ocupados }}{{ $capacidade > 0 ? "/{$capacidade}" : '' }}
                                    </span>
                                </div>
                                @if($capacidade > 0)
                                    <div class="px-3 pt-2">
                                        <div class="progress" style="height: 4px;">
                                            <div class="progress-bar {{ $corBarra }}" style="width: {{ $percentual }}%;"></div>
                                        </div>
                                    </div>
                                @endif
                                <div class="card-body p-0">
                                    @if($atribuicoesNaSala->isEmpty())
                                        <div class="text-muted small text-center py-3">Sem candidatos atribuídos.</div>
                                    @else
                                        <ul class="list-group list-group-flush">
                                            @foreach($atribuicoesNaSala->sortBy('numero_assento') as $atribuicao)
                                                @php $inscricao = $atribuicao->inscricao; @endphp
                                                <li class="list-group-item py-2 px-3">
                                                    <div class="d-flex align-items-center gap-2">
                                                        @if($atribuicao->numero_assento)
                                                            <span class="badge bg-light text-dark border fw-semibold"
                                                                style="min-width:32px;">{{ $atribuicao->numero_assento }}</span>
                                                        @endif
                                                        <div>
                                                            <div class="small fw-semibold">{{ $inscricao?->candidato?->nome_completo ?? '-' }}
                                                            </div>
                                                            <div class="small text-muted">{{ $inscricao?->numero_inscricao ?? '-' }}</div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @empty
        <div class="alert alert-info">Nenhum local de prova vinculado a este processo.</div>
    @endforelse

@endsection