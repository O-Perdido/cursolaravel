@php
    $statusFluxo = $processo->statusApresentacaoDefinicao();
    $etapaAtual = $processo->etapaFluxoAtualDefinicao();
    $proximaAcao = $processo->proximaAcaoOperacional();
    $indicadores = $processo->indicadoresOperacionais();
    $fluxo = $processo->fluxoOperacional();
@endphp

@once
    <style>
        .sc-workflow-shell {
            --sc-ink: #11313a;
            --sc-ocean: #0f766e;
            --sc-sand: #f6efe4;
            --sc-paper: #fcfaf6;
            --sc-line: rgba(17, 49, 58, 0.12);
            border: 0;
            border-radius: 24px;
            overflow: hidden;
            background:
                radial-gradient(circle at top right, rgba(15, 118, 110, 0.16), transparent 34%),
                linear-gradient(135deg, #fdfbf7 0%, #f5eee3 52%, #eef7f4 100%);
            box-shadow: 0 18px 44px rgba(17, 49, 58, 0.12);
        }

        .sc-workflow-shell .card-body {
            padding: 1.5rem;
        }

        .sc-pill-soft {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(17, 49, 58, 0.08);
            color: var(--sc-ink);
            font-size: 0.86rem;
            font-weight: 600;
        }

        .sc-kpi-card {
            border-radius: 18px;
            border: 1px solid var(--sc-line);
            background: rgba(255, 255, 255, 0.8);
            padding: 1rem;
            min-height: 118px;
        }

        .sc-kpi-card .metric {
            font-size: 1.8rem;
            line-height: 1;
            color: var(--sc-ink);
            font-weight: 700;
        }

        .sc-flow-track {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 0.9rem;
        }

        .sc-flow-card {
            position: relative;
            border-radius: 20px;
            border: 1px solid var(--sc-line);
            padding: 1rem;
            background: rgba(255, 255, 255, 0.74);
            min-height: 210px;
        }

        .sc-flow-card.atual {
            background: linear-gradient(180deg, rgba(15, 118, 110, 0.12), rgba(255, 255, 255, 0.92));
            border-color: rgba(15, 118, 110, 0.4);
            box-shadow: inset 0 0 0 1px rgba(15, 118, 110, 0.08);
        }

        .sc-flow-card.concluida {
            background: linear-gradient(180deg, rgba(25, 135, 84, 0.12), rgba(255, 255, 255, 0.92));
            border-color: rgba(25, 135, 84, 0.28);
        }

        .sc-flow-card.proxima {
            border-style: dashed;
            border-color: rgba(13, 110, 253, 0.28);
        }

        .sc-flow-card .step-index {
            width: 2rem;
            height: 2rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 700;
            background: rgba(17, 49, 58, 0.08);
            color: var(--sc-ink);
        }

        .sc-flow-card .step-summary {
            color: #4f666d;
            min-height: 40px;
        }

        .sc-flow-card .step-meta {
            color: #6b7f86;
            font-size: 0.82rem;
        }

        .sc-progress {
            height: 0.55rem;
            border-radius: 999px;
            background: rgba(17, 49, 58, 0.08);
            overflow: hidden;
        }

        .sc-progress>span {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #0f766e 0%, #3eb489 100%);
        }
    </style>
@endonce

<div class="card sc-workflow-shell mb-4">
    <div class="card-body">
        <div class="d-flex flex-column flex-xl-row justify-content-between gap-4 mb-4">
            <div class="flex-grow-1">
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="sc-pill-soft">
                        <i class="fa-solid fa-diagram-project"></i>
                        {{ $processo->numero_processo ?: 'Numero pendente' }}
                    </span>
                    <span class="badge {{ $statusFluxo['badge_class'] }} px-3 py-2">{{ $statusFluxo['titulo'] }}</span>
                    <span class="sc-pill-soft">
                        <i class="fa-solid {{ $etapaAtual['icone'] ?? 'fa-circle' }}"></i>
                        {{ $etapaAtual['titulo'] }}
                    </span>
                </div>

                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-3">
                    <div>
                        <div class="text-uppercase small fw-semibold text-muted mb-2">
                            {{ $processo->tipo_processo === 'concurso_publico' ? 'Concurso Publico' : 'Processo Seletivo' }}
                        </div>
                        <h2 class="mb-2" style="color: #11313a;">{{ $processo->titulo }}</h2>
                        <p class="mb-0 text-muted">
                            Edital {{ $processo->numero_edital ?: 'Nao informado' }}
                            @if($processo->empresa?->nome_razao_social)
                                • {{ $processo->empresa->nome_razao_social }}
                            @endif
                        </p>
                    </div>

                    @if($proximaAcao)
                    <div class="rounded-4 p-3"
                        style="min-width: 290px; background: rgba(255, 255, 255, 0.78); border: 1px solid rgba(17, 49, 58, 0.08);">
                        <div class="small text-uppercase fw-semibold text-muted mb-2">Foco operacional</div>
                        <div class="fw-semibold mb-1">{{ $proximaAcao['titulo'] }}</div>
                        <div class="small text-muted mb-3">{{ $proximaAcao['resumo'] }}</div>
                        @if(!empty($proximaAcao['route_name']))
                        @php($proximaAcaoEhEtapaAtual = ($proximaAcao['situacao'] ?? null) === 'atual')
                        @php($destinoProximaAcao = route($proximaAcao['route_name'], $processo->id_processo))
                        @if(($proximaAcao['chave'] ?? null) === 'inscricoes')
                            <form method="POST"
                                action="{{ route('sigeconcursos.processos.iniciar-inscricoes', $processo->id_processo) }}"
                                class="d-inline">
                                @csrf
                                <input type="hidden" name="redirect_to"
                                    value="{{ route('sigeconcursos.processos.inscricoes', $processo->id_processo, false) }}#painel-homologacao">
                                <button type="submit" class="btn btn-dark btn-sm">
                                    {{ $proximaAcao['cta'] ?: 'Iniciar inscricoes' }}
                                </button>
                            </form>
                        @elseif(($proximaAcao['chave'] ?? null) === 'homologacao_inscricoes')
                            <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modalHomologarInscricoes">
                                {{ $proximaAcao['cta'] ?: 'Homologar candidaturas' }}
                            </button>
                        @elseif(($proximaAcao['chave'] ?? null) === 'etapas_finais')
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-dark btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalEtapasFinaisArquivo">
                                    <i class="fa-solid fa-upload me-1"></i> Adicionar arquivo
                                </button>
                                <form method="POST"
                                    action="{{ route('sigeconcursos.processos.encerrar', $processo->id_processo) }}"
                                    onsubmit="return confirm('Confirma encerrar o processo e marcar como finalizado?');">
                                    @csrf
                                    <input type="hidden" name="redirect_to" value="{{ request()->getRequestUri() }}">
                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                        <i class="fa-solid fa-flag-checkered me-1"></i> Encerrar processo
                                    </button>
                                </form>
                            </div>
                        @elseif($proximaAcaoEhEtapaAtual)
                            <span class="btn btn-sm btn-outline-dark disabled" aria-disabled="true">
                                Etapa atual
                            </span>
                        @else
                            <a href="{{ $destinoProximaAcao }}" class="btn btn-dark btn-sm">
                                {{ $proximaAcao['cta'] ?: 'Abrir etapa' }}
                            </a>
                        @endif
                        @endif
                    </div>
                    @endif
                </div>

                <div class="mt-4">
                    <div class="d-flex justify-content-between align-items-center small text-muted mb-2">
                        <span>Progresso da jornada operacional</span>
                        <span>{{ $processo->progressoOperacionalPercentual() }}%</span>
                    </div>
                    <div class="sc-progress">
                        <span style="width: {{ $processo->progressoOperacionalPercentual() }}%;"></span>
                    </div>
                </div>
            </div>

            <div class="row g-3" style="min-width: min(100%, 450px);">
                <div class="col-6">
                    <div class="sc-kpi-card">
                        <div class="small text-muted mb-2">Inscricoes recebidas</div>
                        <div class="metric">{{ $indicadores['inscricoes_total'] }}</div>
                        <div class="small text-muted mt-2">{{ $indicadores['inscricoes_deferidas'] }} deferidas /
                            {{ $indicadores['inscricoes_pendentes'] }} pendentes
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="sc-kpi-card">
                        <div class="small text-muted mb-2">Solicitacoes de isencao</div>
                        <div class="metric">{{ $indicadores['isencoes_pendentes'] }}</div>
                        <div class="small text-muted mt-2">Pendentes de analise</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="sc-kpi-card">
                        <div class="small text-muted mb-2">Distribuicao por locais</div>
                        <div class="metric">{{ $indicadores['distribuidos_local'] }}</div>
                        <div class="small text-muted mt-2">De {{ $indicadores['inscricoes_deferidas'] }} candidatos
                            aptos</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="sc-kpi-card">
                        <div class="small text-muted mb-2">Distribuicao por salas</div>
                        <div class="metric">{{ $indicadores['distribuidos_sala'] }}</div>
                        <div class="small text-muted mt-2">{{ $indicadores['salas_ativas'] }} sala(s) ativa(s)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 mb-4">
            @if($processo->status === 'rascunho')
                <form method="POST" action="{{ route('sigeconcursos.processos.publicar-edital', $processo->id_processo) }}"
                    class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fa-solid fa-bullhorn me-1"></i> Publicar edital
                    </button>
                </form>
            @endif
            <a href="{{ route('sigeconcursos.processos.edit', $processo->id_processo) }}"
                class="btn btn-outline-dark btn-sm">
                <i class="fa-solid fa-pen me-1"></i> Estrutura do processo
            </a>
            <a href="{{ route('sigeconcursos.processos.inscricoes', $processo->id_processo) }}"
                class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-clipboard-list me-1"></i> Inscricoes
            </a>
            <a href="{{ route('sigeconcursos.processos.isencoes', $processo->id_processo) }}"
                class="btn btn-outline-warning btn-sm">
                <i class="fa-solid fa-file-circle-check me-1"></i> Isencoes
            </a>
            <a href="{{ route('sigeconcursos.processos.distribuicao-locais', $processo->id_processo) }}"
                class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-map-location-dot me-1"></i> Locais
            </a>
            <a href="{{ route('sigeconcursos.processos.distribuicao-salas', $processo->id_processo) }}"
                class="btn btn-outline-info btn-sm">
                <i class="fa-solid fa-door-open me-1"></i> Salas
            </a>
        </div>

        <div class="sc-flow-track">
            @foreach($fluxo as $index => $etapa)
            <div class="sc-flow-card {{ $etapa['situacao'] }}">
                <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                    <span class="step-index">{{ $index + 1 }}</span>
                    <span
                        class="badge {{ $etapa['situacao'] === 'concluida' ? 'bg-success' : ($etapa['situacao'] === 'atual' ? 'bg-dark' : ($etapa['situacao'] === 'proxima' ? 'bg-primary' : 'bg-light text-dark')) }}">
                        {{ $etapa['situacao'] === 'concluida' ? 'Concluida' : ($etapa['situacao'] === 'atual' ? 'Atual' : ($etapa['situacao'] === 'proxima' ? 'Proxima' : 'Planejada')) }}
                    </span>
                </div>
                <div class="fw-semibold mb-2">
                    <i class="fa-solid {{ $etapa['icone'] }} me-2"></i>{{ $etapa['titulo'] }}
                </div>
                <div class="small text-muted mb-2">{{ $etapa['descricao'] }}</div>
                <div class="small step-summary mb-3">{{ $etapa['resumo'] }}</div>
                @if(!empty($etapa['route_name']))
                @php($etapaEhAtual = $etapa['situacao'] === 'atual')
                @php($destinoEtapa = route($etapa['route_name'], $processo->id_processo))
                @if(($etapa['chave'] ?? null) === 'inscricoes')
                    <form method="POST"
                        action="{{ route('sigeconcursos.processos.iniciar-inscricoes', $processo->id_processo) }}"
                        class="d-inline">
                        @csrf
                        <input type="hidden" name="redirect_to"
                            value="{{ route('sigeconcursos.processos.inscricoes', $processo->id_processo, false) }}#painel-homologacao">
                        <button type="submit" class="btn btn-outline-dark btn-sm">
                            {{ $etapa['cta'] ?: 'Iniciar inscricoes' }}
                        </button>
                    </form>
                @elseif(($etapa['chave'] ?? null) === 'homologacao_inscricoes')
                    <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                        data-bs-target="#modalHomologarInscricoes">
                        {{ $etapa['cta'] ?: 'Homologar candidaturas' }}
                    </button>
                @elseif(($etapa['chave'] ?? null) === 'etapas_finais')
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalEtapasFinaisArquivo">
                            <i class="fa-solid fa-upload me-1"></i> Adicionar arquivo
                        </button>
                        <form method="POST" action="{{ route('sigeconcursos.processos.encerrar', $processo->id_processo) }}"
                            onsubmit="return confirm('Confirma encerrar o processo e marcar como finalizado?');">
                            @csrf
                            <input type="hidden" name="redirect_to" value="{{ request()->getRequestUri() }}">
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="fa-solid fa-flag-checkered me-1"></i> Encerrar processo
                            </button>
                        </form>
                    </div>
                @elseif($etapaEhAtual)
                    <a href="{{ $destinoEtapa }}" class="btn btn-sm btn-outline-dark">
                        Abrir etapa atual
                    </a>
                @else
                    <a href="{{ $destinoEtapa }}" class="btn btn-outline-dark btn-sm">
                        {{ $etapa['cta'] ?: 'Abrir' }}
                    </a>
                @endif
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="modal fade" id="modalHomologarInscricoes" tabindex="-1" aria-labelledby="modalHomologarInscricoesLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST"
                action="{{ route('sigeconcursos.processos.homologar-inscricoes', $processo->id_processo) }}"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="redirect_to"
                    value="{{ route('sigeconcursos.processos.inscricoes', $processo->id_processo, false) }}#painel-homologacao">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalHomologarInscricoesLabel">Homologar candidaturas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        Envie o arquivo da homologacao para registrar a etapa automaticamente, sem precisar editar o
                        processo.
                    </p>

                    <div class="mb-3">
                        <label for="arquivo_homologacao" class="form-label">Arquivo da homologacao <span
                                class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="arquivo_homologacao" name="arquivo_homologacao"
                            required>
                        <div class="form-text">Tamanho maximo: 10MB.</div>
                    </div>

                    <div>
                        <label for="nome_exibicao_homologacao" class="form-label">Nome para exibicao (opcional)</label>
                        <input type="text" class="form-control" id="nome_exibicao_homologacao"
                            name="nome_exibicao_homologacao" maxlength="255"
                            placeholder="Ex: Homologacao das inscricoes - 2026">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-upload me-1"></i> Enviar e homologar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEtapasFinaisArquivo" tabindex="-1" aria-labelledby="modalEtapasFinaisArquivoLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST"
                action="{{ route('sigeconcursos.processos.etapas-finais.arquivo', $processo->id_processo) }}"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="redirect_to" value="{{ request()->getRequestUri() }}">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalEtapasFinaisArquivoLabel">Adicionar arquivo da etapa final</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        Envie arquivos como resultado preliminar, resultado final, recursos ou publicacoes finais.
                    </p>

                    <div class="mb-3">
                        <label for="arquivo_etapa_final" class="form-label">Arquivo <span
                                class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="arquivo_etapa_final" name="arquivo_etapa_final"
                            required>
                        <div class="form-text">Tamanho maximo: 10MB.</div>
                    </div>

                    <div class="mb-3">
                        <label for="tipo_arquivo_etapa_final" class="form-label">Tipo do arquivo</label>
                        <select id="tipo_arquivo_etapa_final" name="tipo_arquivo_etapa_final" class="form-select">
                            <option value="resultado_preliminar">Resultado preliminar</option>
                            <option value="resultado_final">Resultado final</option>
                            <option value="recurso">Recurso</option>
                            <option value="publicacao_final">Publicacao final</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>

                    <div>
                        <label for="nome_exibicao_etapa_final" class="form-label">Nome para exibicao (opcional)</label>
                        <input type="text" class="form-control" id="nome_exibicao_etapa_final"
                            name="nome_exibicao_etapa_final" maxlength="255"
                            placeholder="Ex: Resultado preliminar - Lista geral">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-upload me-1"></i> Enviar arquivo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>