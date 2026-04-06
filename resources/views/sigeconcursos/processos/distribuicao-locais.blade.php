@extends('layouts.main')

@section('title', 'SIGE Concursos | Distribuição por Locais')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Distribuição por Locais</h2>
            <p class="text-muted mb-0">{{ $processo->titulo }} — Edital {{ $processo->numero_edital }}</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('sigeconcursos.processos.show', $processo->id_processo) }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Voltar ao Processo
            </a>
        </div>
    </div>

    @include('sigeconcursos.processos._workflow-hub', ['processo' => $processo])

    {{-- Resumo --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Candidatos deferidos</div>
                    <div class="h4 mb-0">{{ $totalDeferidos }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Distribuídos</div>
                    <div class="h4 mb-0 {{ $totalDistribuidos > 0 ? 'text-success' : 'text-muted' }}">
                        {{ $totalDistribuidos }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Sem local atribuído</div>
                    <div class="h4 mb-0 {{ ($totalDeferidos - $totalDistribuidos) > 0 ? 'text-warning' : 'text-muted' }}">
                        {{ $totalDeferidos - $totalDistribuidos }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">Locais disponíveis</div>
                    <div class="h4 mb-0">{{ $locais->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Ações principais --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body d-flex flex-wrap gap-3 align-items-center">
            @if($locais->isEmpty())
                <div class="alert alert-warning mb-0 flex-grow-1">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i>
                    Nenhum local de prova vinculado a este processo.
                    <a href="{{ route('sigeconcursos.processos.edit', $processo->id_processo) }}" class="alert-link">Editar
                        processo</a> para adicionar locais.
                </div>
            @elseif($totalDeferidos === 0)
                <div class="alert alert-info mb-0 flex-grow-1">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    Não há candidatos deferidos. Realize a homologação das inscrições antes de distribuir.
                </div>
            @else
                <form action="{{ route('sigeconcursos.processos.distribuicao-locais.distribuir', $processo->id_processo) }}"
                    method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary"
                        onclick="return confirm('Isso irá redistribuir TODOS os candidatos deferidos. A distribuição anterior será substituída. Confirmar?')">
                        <i class="fa-solid fa-shuffle me-1"></i>
                        Distribuir automaticamente ({{ $totalDeferidos }} candidatos, {{ $locais->count() }} local(is))
                    </button>
                </form>

                @if($totalDistribuidos > 0)
                    <form action="{{ route('sigeconcursos.processos.distribuicao-locais.limpar', $processo->id_processo) }}"
                        method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm"
                            onclick="return confirm('Isso irá remover TODA a distribuição por locais. Confirmar?')">
                            <i class="fa-solid fa-trash me-1"></i> Limpar distribuição
                        </button>
                    </form>
                @endif

                <div class="text-muted small">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    A distribuição é feita em ordem alfabética de nome dos candidatos, divididos igualmente entre os locais.
                    O último local recebe o restante.
                </div>
            @endif
        </div>
    </div>

    {{-- Locais com candidatos --}}
    @if($locais->isNotEmpty())
        <div class="row g-4">
            @foreach($locais as $processoLocal)
                @php
                    $local = $processoLocal->localProva;
                    $atribuidos = $processoLocal->inscricoesAtribuidas;
                    $quantidade = $atribuidos->count();
                @endphp
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center py-3">
                            <div>
                                <strong class="fs-6">{{ $local?->nome_local ?? 'Local sem nome' }}</strong>
                                @if($local?->endereco)
                                    <div class="small text-muted mt-1">
                                        {{ $local->endereco }}, {{ $local->numero_endereco }}
                                        @if($local->bairro) — {{ $local->bairro }}@endif
                                    </div>
                                @endif
                            </div>
                            <span class="badge bg-primary fs-6">{{ $quantidade }} candidato(s)</span>
                        </div>

                        @if($quantidade > 0)
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 40px;">#</th>
                                                <th>Candidato</th>
                                                <th>Inscrição</th>
                                                <th>Modalidade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($atribuidos as $i => $atribuicao)
                                                @php $inscricao = $atribuicao->inscricao; @endphp
                                                <tr>
                                                    <td class="text-muted">{{ $i + 1 }}</td>
                                                    <td>
                                                        <div class="fw-semibold">{{ $inscricao?->candidato?->nome_completo ?? '-' }}</div>
                                                        <div class="small text-muted">CPF: {{ $inscricao?->candidato?->numero_cpf ?? '-' }}
                                                        </div>
                                                    </td>
                                                    <td class="small text-muted">{{ $inscricao?->numero_inscricao ?? '-' }}</td>
                                                    <td class="small">{{ $inscricao?->modalidadeLabel() ?? '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="card-body text-muted text-center py-4 small">
                                Nenhum candidato atribuído a este local ainda.
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection