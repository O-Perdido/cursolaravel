@extends('layouts.main')

@section('title', 'SIGE Concursos | Processos e Concursos')

@section('content')
    @once
        <style>
            .sc-candidato-list-shell {
                --sc-ink: #16303a;
                --sc-muted: #607580;
                --sc-line: rgba(22, 48, 58, 0.12);
                --sc-surface: #ffffff;
                --sc-soft: #f5f8f9;
                --sc-accent: #0f766e;
            }

            .sc-candidato-list-hero {
                border: 0;
                border-radius: 24px;
                overflow: hidden;
                background:
                    radial-gradient(circle at top right, rgba(15, 118, 110, 0.18), transparent 38%),
                    linear-gradient(135deg, #fcfaf6 0%, #f4efe5 45%, #eef7f4 100%);
                box-shadow: 0 18px 38px rgba(17, 49, 58, 0.12);
            }

            .sc-candidato-list-card {
                border: 1px solid var(--sc-line);
                border-radius: 18px;
                background: var(--sc-surface);
                box-shadow: 0 10px 22px rgba(17, 49, 58, 0.08);
            }

            .sc-candidato-list-filter-card {
                border: 1px solid rgba(22, 48, 58, 0.08);
                border-radius: 18px;
                background: rgba(255, 255, 255, 0.88);
                box-shadow: 0 12px 28px rgba(17, 49, 58, 0.08);
            }

            .sc-candidato-list-filter-actions {
                display: grid;
                grid-template-columns: minmax(0, 1fr) auto;
                gap: 0.75rem;
                align-items: end;
            }

            .sc-candidato-list-card-stack {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .sc-candidato-process-card {
                border: 1px solid var(--sc-line);
                border-radius: 22px;
                background: var(--sc-surface);
                box-shadow: 0 12px 28px rgba(17, 49, 58, 0.09);
                overflow: hidden;
            }

            .sc-candidato-process-card .card-body {
                padding: 1.1rem 1.1rem 1rem;
            }

            .sc-candidato-process-card .topline {
                display: flex;
                flex-wrap: wrap;
                gap: 0.45rem;
                margin-bottom: 0.9rem;
            }

            .sc-candidato-process-card .tipo-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.32rem 0.7rem;
                border-radius: 999px;
                border: 1px solid rgba(22, 48, 58, 0.14);
                background: rgba(22, 48, 58, 0.06);
                color: var(--sc-ink);
                font-size: 0.77rem;
                font-weight: 700;
            }

            .sc-candidato-process-card .inscricao-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.32rem 0.7rem;
                border-radius: 999px;
                font-size: 0.77rem;
                font-weight: 700;
                background: rgba(25, 135, 84, 0.12);
                color: #146c43;
                border: 1px solid rgba(25, 135, 84, 0.16);
            }

            .sc-candidato-process-card .orgao {
                color: var(--sc-muted);
                font-size: 0.83rem;
                margin-bottom: 0.25rem;
            }

            .sc-candidato-process-card .titulo {
                color: var(--sc-ink);
                font-size: 1.12rem;
                font-weight: 800;
                line-height: 1.3;
                margin-bottom: 0.45rem;
            }

            .sc-candidato-process-card .edital {
                color: var(--sc-muted);
                font-size: 0.9rem;
                margin-bottom: 0.9rem;
            }

            .sc-candidato-process-card .content-grid {
                display: grid;
                grid-template-columns: minmax(0, 1.5fr) minmax(260px, 1fr);
                gap: 1rem;
                align-items: start;
            }

            .sc-candidato-process-card .summary {
                display: flex;
                flex-direction: column;
                gap: 0.8rem;
            }

            .sc-candidato-process-card .etapa {
                display: flex;
                align-items: flex-start;
                gap: 0.6rem;
                color: var(--sc-muted);
                font-size: 0.9rem;
            }

            .sc-candidato-process-card .meta-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.75rem;
            }

            .sc-candidato-list-meta {
                background: var(--sc-soft);
                border: 1px solid var(--sc-line);
                border-radius: 12px;
                padding: 0.65rem 0.75rem;
            }

            .sc-candidato-list-meta .label {
                color: var(--sc-muted);
                font-size: 0.72rem;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                margin-bottom: 0.25rem;
                font-weight: 700;
            }

            .sc-candidato-list-meta .value {
                color: var(--sc-ink);
                font-weight: 600;
                font-size: 0.88rem;
            }

            .sc-candidato-list-section-title {
                color: var(--sc-ink);
                font-weight: 700;
                margin-bottom: 0.8rem;
            }

            .sc-candidato-process-card .actions {
                display: flex;
                flex-direction: column;
                gap: 0.65rem;
            }

            .sc-candidato-process-card .actions .btn {
                width: 100%;
            }

            .sc-candidato-result-info {
                display: flex;
                justify-content: space-between;
                gap: 1rem;
                align-items: center;
                margin-bottom: 1rem;
                color: var(--sc-muted);
                font-size: 0.9rem;
            }

            @media (max-width: 991.98px) {
                .sc-candidato-list-hero .card-body {
                    padding: 1.2rem;
                }

                .sc-candidato-list-filter-actions {
                    grid-template-columns: 1fr;
                }

                .sc-candidato-process-card .content-grid {
                    grid-template-columns: 1fr;
                }

                .sc-candidato-process-card .meta-grid {
                    grid-template-columns: 1fr;
                }

                .sc-candidato-result-info {
                    flex-direction: column;
                    align-items: flex-start;
                }
            }

            @media (max-width: 575.98px) {
                .sc-candidato-list-shell .btn-sm {
                    width: 100%;
                }

                .sc-candidato-process-card .card-body {
                    padding: 1rem 0.9rem 0.95rem;
                }

                .sc-candidato-process-card .titulo {
                    font-size: 1rem;
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

    <div class="sc-candidato-list-shell">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                <h2 class="mb-1" style="color: #16303a;">Processos e concursos</h2>
                <p class="text-muted mb-0">Visualize todos os editais e aplique filtros quando quiser.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('sigeconcursos.candidato.minhas-inscricoes') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fa-solid fa-clipboard-list me-1"></i> Minhas inscricoes
                </a>
                <a href="{{ route('sigeconcursos.candidato.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
        </div>

        <div class="card sc-candidato-list-hero mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-7">
                        <h5 class="mb-2" style="color: #16303a;">Encontre o edital ideal</h5>
                        <p class="text-muted mb-0">Filtre por titulo, numero do edital ou orgao para localizar rapidamente o
                            processo desejado.</p>
                    </div>
                    <div class="col-lg-5">
                        <form method="GET" class="card sc-candidato-list-filter-card">
                            <div class="card-body">
                                <label for="busca" class="form-label small text-muted mb-2">Buscar edital</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="busca" name="busca"
                                        value="{{ request('busca') }}"
                                        placeholder="Ex: Edital 01/2026, Prefeitura, Analista...">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </button>
                                </div>
                                <div class="sc-candidato-list-filter-actions mt-3">
                                    <div>
                                        <label for="filtro_inscricao"
                                            class="form-label small text-muted mb-2">Exibição</label>
                                        <select id="filtro_inscricao" name="filtro_inscricao"
                                            class="form-select form-select-sm">
                                            <option value="todos" {{ ($filtroInscricao ?? 'todos') === 'todos' ? 'selected' : '' }}>Todos os processos</option>
                                            <option value="abertas" {{ ($filtroInscricao ?? 'todos') === 'abertas' ? 'selected' : '' }}>Somente inscricoes abertas</option>
                                        </select>
                                    </div>
                                    <a href="{{ route('sigeconcursos.candidato.processos.index') }}"
                                        class="btn btn-sm btn-outline-secondary">Limpar filtro</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if($processos->count() > 0)
            <div class="sc-candidato-result-info">
                <span>
                    {{ $processos->total() }} {{ $processos->total() === 1 ? 'processo encontrado' : 'processos encontrados' }}
                </span>
                @if(($filtroInscricao ?? 'todos') === 'abertas')
                    <span>Mostrando apenas processos com inscricoes abertas.</span>
                @endif
            </div>

            <div class="sc-candidato-list-card-stack mb-3">
                @foreach($processos as $processo)
                    @php
                        $inscricaoId = $inscricoesDoCandidato[$processo->id_processo] ?? null;
                        $statusFluxo = $processo->statusApresentacaoDefinicao();
                        $tipoLabel = $processo->tipo_processo === 'concurso_publico' ? 'Concurso Público' : 'Processo Seletivo';
                        $primeiraIsencao = $processo->isencoes->first();
                        $cor = $statusFluxo['color'] ?? '#6c757d';
                        $badgeClass = $statusFluxo['badge_class'] ?? 'bg-secondary';
                    @endphp

                    <div class="sc-candidato-process-card" style="border-left: 5px solid {{ $cor }};">
                        <div class="card-body">
                            <div class="topline">
                                <span class="tipo-badge">{{ $tipoLabel }}</span>
                                <span class="badge {{ $badgeClass }}">{{ $statusFluxo['titulo'] }}</span>
                                @if($inscricaoId)
                                    <span class="inscricao-badge">
                                        <i class="fa-solid fa-circle-check me-1"></i> Já inscrito
                                    </span>
                                @endif
                            </div>

                            <div class="orgao">{{ $processo->empresa?->nome_razao_social ?? 'Órgão não informado' }}</div>
                            <div class="titulo">{{ $processo->titulo }}</div>
                            <div class="edital">
                                <i class="fa-solid fa-file-lines me-1"></i>
                                Edital {{ $processo->numero_edital ?: 'Não informado' }}
                            </div>

                            <div class="content-grid">
                                <div class="summary">
                                    <div class="meta-grid">
                                        <div class="sc-candidato-list-meta">
                                            <div class="label">Início das inscrições</div>
                                            <div class="value">
                                                {{ $processo->data_inicio_inscricoes?->format('d/m/Y H:i') ?: 'Não definido' }}
                                            </div>
                                        </div>
                                        <div class="sc-candidato-list-meta">
                                            <div class="label">Fim das inscrições</div>
                                            <div class="value">
                                                {{ $processo->data_fim_inscricoes?->format('d/m/Y H:i') ?: 'Não definido' }}</div>
                                        </div>
                                        <div class="sc-candidato-list-meta">
                                            <div class="label">Data da prova</div>
                                            <div class="value">{{ $processo->data_prova?->format('d/m/Y H:i') ?: 'Não definida' }}
                                            </div>
                                        </div>
                                        <div class="sc-candidato-list-meta">
                                            <div class="label">Isenção</div>
                                            <div class="value">
                                                @if($primeiraIsencao && ($primeiraIsencao->data_inicio || $primeiraIsencao->data_fim))
                                                    {{ $primeiraIsencao->data_inicio?->format('d/m/Y') ?? '?' }} até
                                                    {{ $primeiraIsencao->data_fim?->format('d/m/Y') ?? '?' }}
                                                @else
                                                    Não configurada
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="actions">
                                    <a href="{{ route('sigeconcursos.candidato.processos.show', $processo->id_processo) }}"
                                        class="btn btn-primary">
                                        <i class="fa-solid fa-circle-info me-1"></i> Ver detalhes
                                    </a>
                                    @if($inscricaoId)
                                        <a href="{{ route('sigeconcursos.candidato.minhas-inscricoes') }}"
                                            class="btn btn-outline-success">
                                            <i class="fa-solid fa-check me-1"></i> Acompanhar inscrição
                                        </a>
                                    @else
                                        <a href="{{ route('sigeconcursos.candidato.processos.show', $processo->id_processo) }}"
                                            class="btn btn-outline-secondary">
                                            <i class="fa-solid fa-file-signature me-1"></i> Ver regras e cronograma
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card sc-candidato-list-card">
                <div class="card-body text-center py-5">
                    <i class="fa-solid fa-folder-open fa-2x text-muted mb-2"></i>
                    <div class="text-muted">
                        @if(($filtroInscricao ?? 'todos') === 'abertas')
                            Nenhum processo com inscricoes abertas foi encontrado no momento.
                        @else
                            Nenhum processo foi encontrado com os filtros informados.
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if($processos->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $processos->links() }}
            </div>
        @endif
    </div>
@endsection