@extends('layouts.main')

@section('title', 'SIGE Concursos | Processos e Concursos')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="mb-1 fw-bold">Processos e Concursos</h2>
            <p class="text-muted mb-0">Confira os editais disponíveis. Para se inscrever, acesse a área do candidato.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('landing') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Voltar
            </a>
            <a href="{{ route('sigeconcursos.candidato.login') }}" class="btn btn-primary">
                <i class="fa-solid fa-right-to-bracket me-1"></i> Área do Candidato
            </a>
        </div>
    </div>

    <form method="GET" class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="busca" class="form-label">Buscar por título, edital ou órgão</label>
                    <input type="text" class="form-control" id="busca" name="busca" value="{{ request('busca') }}"
                        placeholder="Ex: Edital 01/2026, Prefeitura, Analista...">
                </div>
                <div class="col-md-2">
                    <label for="tipo_processo" class="form-label">Tipo</label>
                    <select id="tipo_processo" name="tipo_processo" class="form-select">
                        <option value="">Todos</option>
                        <option value="processo_seletivo" {{ request('tipo_processo') === 'processo_seletivo' ? 'selected' : '' }}>Processo Seletivo</option>
                        <option value="concurso_publico" {{ request('tipo_processo') === 'concurso_publico' ? 'selected' : '' }}>Concurso Público</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="publicado" {{ request('status') === 'publicado' ? 'selected' : '' }}>Publicado</option>
                        <option value="inscricoes_abertas" {{ request('status') === 'inscricoes_abertas' ? 'selected' : '' }}>
                            Inscrições abertas</option>
                        <option value="inscricoes_encerradas" {{ request('status') === 'inscricoes_encerradas' ? 'selected' : '' }}>Inscrições encerradas</option>
                        <option value="em_andamento" {{ request('status') === 'em_andamento' ? 'selected' : '' }}>Em andamento
                        </option>
                        <option value="finalizado" {{ request('status') === 'finalizado' ? 'selected' : '' }}>Finalizado
                        </option>
                        <option value="suspenso" {{ request('status') === 'suspenso' ? 'selected' : '' }}>Suspenso</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid d-md-flex gap-2">
                    <button class="btn btn-primary flex-fill" type="submit">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('sigeconcursos.publico.processos.index') }}"
                        class="btn btn-outline-secondary flex-fill">Limpar</a>
                </div>
            </div>
        </div>
    </form>

    @if($processos->total() > 0)
        <p class="text-muted small mb-3">{{ $processos->total() }}
            {{ $processos->total() === 1 ? 'processo encontrado' : 'processos encontrados' }}</p>
    @endif

    <div class="d-flex flex-column gap-3">
        @forelse($processos as $processo)
            @php
                $statusFluxo = $processo->statusApresentacaoDefinicao();
                $cor = $statusFluxo['color'] ?? '#6c757d';
                $badgeClass = $statusFluxo['badge_class'] ?? 'bg-secondary';
                $label = $statusFluxo['titulo'] ?? 'Status';
                $tipoLabel = $processo->tipo_processo === 'concurso_publico' ? 'Concurso Público' : 'Processo Seletivo';
                $primeiraIsencao = $processo->isencoes->first();
            @endphp
            <div class="card border-0 shadow-sm" style="border-left: 5px solid {{ $cor }} !important;">
                <div class="card-body py-3 px-4">
                    <div class="d-flex justify-content-between align-items-start gap-3">

                        {{-- Lado esquerdo: conteúdo principal --}}
                        <div class="flex-grow-1 min-width-0">

                            {{-- Linha superior: tipo + status (mobile) --}}
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                <span class="badge rounded-pill fw-normal border"
                                    style="background: transparent; color: {{ $cor }}; border-color: {{ $cor }} !important; font-size: 0.78rem;">
                                    {{ $tipoLabel }}
                                </span>
                                <span class="badge {{ $badgeClass }} d-md-none">{{ $label }}</span>
                            </div>

                            {{-- Ícone + nome do órgão + título --}}
                            <div class="d-flex align-items-center gap-3 mb-2">
                                @if($processo->icone_processo)
                                    <img src="{{ asset('storage/' . $processo->icone_processo) }}" alt="Ícone"
                                        style="height: 48px; width: 48px; object-fit: contain; flex-shrink: 0;">
                                @endif
                                <div>
                                    <div class="text-muted small mb-0">
                                        {{ $processo->empresa?->nome_razao_social ?? 'Órgão não informado' }}</div>
                                    <h5 class="fw-bold mb-0" style="line-height: 1.3;">{{ $processo->titulo }}</h5>
                                </div>
                            </div>

                            {{-- Edital --}}
                            <div class="text-muted small mb-2">
                                <i class="fa-solid fa-file-lines me-1"></i>
                                Edital nº <strong>{{ $processo->numero_edital }}</strong>
                            </div>

                            {{-- Datas de inscrição --}}
                            @if($processo->data_inicio_inscricoes || $processo->data_fim_inscricoes)
                                <div class="d-flex align-items-center flex-wrap gap-2 small mb-1">
                                    <i class="fa-solid fa-calendar-days text-muted"></i>
                                    <span>
                                        Inscrições de
                                        <strong>{{ $processo->data_inicio_inscricoes?->format('d/m/Y') ?? '?' }}</strong>
                                        a
                                        <strong>{{ $processo->data_fim_inscricoes?->format('d/m/Y') ?? '?' }}</strong>
                                    </span>
                                    @if($processo->inscricoesAbertasAgora())
                                        <span class="badge bg-success">Inscrições Abertas!</span>
                                    @elseif($processo->inscricoesEncerradas())
                                        <span class="badge bg-secondary">Encerradas</span>
                                    @endif
                                </div>
                            @endif

                            {{-- Período de isenção (primeira isenção cadastrada) --}}
                            @if($primeiraIsencao && ($primeiraIsencao->data_inicio || $primeiraIsencao->data_fim))
                                <div class="d-flex align-items-center flex-wrap gap-2 small text-muted">
                                    <i class="fa-solid fa-calendar-check"></i>
                                    <span>
                                        Pedidos de Isenção de
                                        <strong>{{ $primeiraIsencao->data_inicio?->format('d/m/Y') ?? '?' }}</strong>
                                        a
                                        <strong>{{ $primeiraIsencao->data_fim?->format('d/m/Y') ?? '?' }}</strong>
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Lado direito: status + botão --}}
                        <div class="flex-shrink-0 d-none d-md-flex flex-column align-items-end gap-2">
                            <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                            <a href="{{ route('sigeconcursos.publico.processos.show', $processo->id_processo) }}"
                                class="btn btn-outline-primary btn-sm px-3">
                                Mais Informações
                            </a>
                        </div>
                    </div>

                    {{-- Botão visível só no mobile --}}
                    <div class="d-md-none mt-3">
                        <a href="{{ route('sigeconcursos.publico.processos.show', $processo->id_processo) }}"
                            class="btn btn-outline-primary btn-sm w-100">
                            Mais Informações
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-light border text-center mb-0">
                Nenhum processo foi encontrado com os filtros selecionados.
            </div>
        @endforelse
    </div>

    @if($processos->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $processos->links() }}
        </div>
    @endif
@endsection