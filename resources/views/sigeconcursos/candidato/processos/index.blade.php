@extends('layouts.main')

@section('title', 'SIGE Concursos | Processos com Inscrições Abertas')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Processos com Inscrições Abertas</h2>
            <p class="text-muted mb-0">Selecione um processo para conferir os detalhes e realizar sua inscrição.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sigeconcursos.candidato.minhas-inscricoes') }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-clipboard-list me-1"></i> Minhas Inscrições
            </a>
            <a href="{{ route('sigeconcursos.candidato.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>

    <form method="GET" class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-9">
                    <label for="busca" class="form-label">Buscar por título, edital ou órgão</label>
                    <input type="text" class="form-control" id="busca" name="busca" value="{{ request('busca') }}"
                        placeholder="Ex: Edital 01/2026, Prefeitura, Analista...">
                </div>
                <div class="col-md-3 d-grid d-md-flex gap-2">
                    <button class="btn btn-primary flex-fill" type="submit">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Buscar
                    </button>
                    <a href="{{ route('sigeconcursos.candidato.processos.index') }}"
                        class="btn btn-outline-secondary flex-fill">Limpar</a>
                </div>
            </div>
        </div>
    </form>

    <div class="row g-4">
        @forelse($processos as $processo)
            @php
                $inscricaoId = $inscricoesDoCandidato[$processo->id_processo] ?? null;
            @endphp
            <div class="col-lg-6">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <strong>{{ $processo->numero_edital }}</strong>
                        <span class="badge bg-success">Inscrições abertas</span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        @if($processo->icone_processo)
                            <img src="{{ asset('storage/' . $processo->icone_processo) }}" alt="Ícone"
                                style="max-height: 60px; max-width: 200px; object-fit: contain; margin-bottom: 0.75rem;">
                        @endif
                        <h5 class="card-title">{{ $processo->titulo }}</h5>
                        <p class="text-muted small mb-2">Órgão: {{ $processo->empresa?->nome_razao_social ?? 'Não informado' }}
                        </p>

                        <div class="small text-muted mb-3">
                            <div><strong>Início:</strong>
                                {{ $processo->data_inicio_inscricoes?->format('d/m/Y H:i') ?: 'Não definido' }}</div>
                            <div><strong>Fim:</strong>
                                {{ $processo->data_fim_inscricoes?->format('d/m/Y H:i') ?: 'Não definido' }}</div>
                        </div>

                        @if($processo->resumo)
                            <p class="text-muted flex-grow-1" style="white-space: pre-line;">
                                {{ \Illuminate\Support\Str::limit($processo->resumo, 220) }}</p>
                        @else
                            <p class="text-muted flex-grow-1">Sem resumo cadastrado.</p>
                        @endif

                        <div class="d-flex gap-2 mt-2">
                            <a href="{{ route('sigeconcursos.candidato.processos.show', $processo->id_processo) }}"
                                class="btn btn-primary w-100">
                                <i class="fa-solid fa-circle-info me-1"></i> Ver Detalhes
                            </a>
                            @if($inscricaoId)
                                <a href="{{ route('sigeconcursos.candidato.minhas-inscricoes') }}"
                                    class="btn btn-outline-success w-100">
                                    <i class="fa-solid fa-check me-1"></i> Já Inscrito
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-light border text-center mb-0">
                    Nenhum processo com inscrições abertas foi encontrado no momento.
                </div>
            </div>
        @endforelse
    </div>

    @if($processos->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $processos->links() }}
        </div>
    @endif
@endsection