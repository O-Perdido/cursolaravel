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

            .sc-candidato-list-table-wrap {
                border: 1px solid var(--sc-line);
                border-radius: 18px;
                overflow: hidden;
                background: var(--sc-surface);
                box-shadow: 0 10px 22px rgba(17, 49, 58, 0.08);
            }

            .sc-candidato-list-table thead th {
                background: var(--sc-soft);
                color: var(--sc-muted);
                font-size: 0.78rem;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                border-bottom: 1px solid var(--sc-line);
                white-space: nowrap;
            }

            .sc-candidato-list-table td {
                vertical-align: middle;
                border-color: var(--sc-line);
            }

            .sc-candidato-list-table .titulo {
                color: var(--sc-ink);
                font-weight: 700;
                margin-bottom: 0.2rem;
            }

            .sc-candidato-list-table .subinfo {
                color: var(--sc-muted);
                font-size: 0.82rem;
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
                        <form method="GET" class="card sc-candidato-list-card">
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
                                <div class="d-flex justify-content-end mt-2">
                                    <div class="me-2" style="min-width: 210px;">
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
            <div class="sc-candidato-list-table-wrap mb-3">
                <div class="table-responsive">
                    <table class="table sc-candidato-list-table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Processo</th>
                                <th>Orgao</th>
                                <th>Status</th>
                                <th>Periodo de inscricao</th>
                                <th class="text-center">Acoes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($processos as $processo)
                                @php
                                    $inscricaoId = $inscricoesDoCandidato[$processo->id_processo] ?? null;
                                    $statusFluxo = $processo->statusApresentacaoDefinicao();
                                    $etapaAtual = $processo->etapaFluxoAtualDefinicao();
                                    $tipoLabel = $processo->tipo_processo === 'concurso_publico' ? 'Concurso Publico' : 'Processo Seletivo';
                                @endphp
                                <tr>
                                    <td style="min-width: 280px;">
                                        <div class="titulo">{{ $processo->titulo }}</div>
                                        <div class="subinfo">Edital {{ $processo->numero_edital ?: 'Nao informado' }}</div>
                                        <div class="d-flex flex-wrap gap-1 mt-2">
                                            <span class="badge text-dark"
                                                style="background: rgba(22, 48, 58, 0.1);">{{ $tipoLabel }}</span>
                                            @if($inscricaoId)
                                                <span
                                                    class="badge bg-success-subtle text-success-emphasis border border-success-subtle">Ja
                                                    inscrito</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="min-width: 200px;">
                                        {{ $processo->empresa?->nome_razao_social ?? 'Nao informado' }}
                                    </td>
                                    <td style="min-width: 230px;">
                                        <div class="mb-1">
                                            <span
                                                class="badge {{ $statusFluxo['badge_class'] }}">{{ $statusFluxo['titulo'] }}</span>
                                        </div>
                                        <div class="subinfo d-flex align-items-center gap-2">
                                            <i class="fa-solid {{ $etapaAtual['icone'] ?? 'fa-circle' }}"></i>
                                            <span>{{ $etapaAtual['titulo'] }}</span>
                                        </div>
                                    </td>
                                    <td style="min-width: 220px;">
                                        <div><strong>Inicio:</strong>
                                            {{ $processo->data_inicio_inscricoes?->format('d/m/Y H:i') ?: 'Nao definido' }}</div>
                                        <div><strong>Fim:</strong>
                                            {{ $processo->data_fim_inscricoes?->format('d/m/Y H:i') ?: 'Nao definido' }}</div>
                                    </td>
                                    <td class="text-center" style="min-width: 220px;">
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('sigeconcursos.candidato.processos.show', $processo->id_processo) }}"
                                                class="btn btn-sm btn-primary">
                                                <i class="fa-solid fa-circle-info me-1"></i> Ver detalhes
                                            </a>
                                            @if($inscricaoId)
                                                <a href="{{ route('sigeconcursos.candidato.minhas-inscricoes') }}"
                                                    class="btn btn-sm btn-outline-success">
                                                    <i class="fa-solid fa-check me-1"></i> Acompanhar inscricao
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
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