@extends('layouts.main')

@section('title', 'SIGE Concursos | Detalhes do Processo')

@section('content')
    @php
        $statusFluxo = $processo->statusApresentacaoDefinicao();
        $tipoLabel = $processo->tipo_processo === 'concurso_publico' ? 'Concurso Publico' : 'Processo Seletivo';

        if ($podeInscrever) {
            $inscricaoBadgeClass = 'bg-success';
            $inscricaoBadgeText = 'Inscricoes abertas';
            $inscricaoDescricao = 'Voce pode consultar os detalhes e seguir para a inscricao.';
        } elseif ($processo->data_inicio_inscricoes && now()->lt($processo->data_inicio_inscricoes)) {
            $inscricaoBadgeClass = 'bg-warning text-dark';
            $inscricaoBadgeText = 'Inscricoes ainda nao iniciadas';
            $inscricaoDescricao = 'Acompanhe as datas e retorne no periodo de inscricoes.';
        } elseif ($processo->data_fim_inscricoes && now()->gt($processo->data_fim_inscricoes)) {
            $inscricaoBadgeClass = 'bg-secondary';
            $inscricaoBadgeText = 'Inscricoes encerradas';
            $inscricaoDescricao = 'O prazo de inscricao foi encerrado para este edital.';
        } else {
            $inscricaoBadgeClass = 'bg-secondary';
            $inscricaoBadgeText = 'Inscricoes indisponiveis';
            $inscricaoDescricao = 'No momento, este processo nao esta recebendo inscricoes.';
        }

        $mostrarBadgeInscricao = strtolower((string) $inscricaoBadgeText) !== strtolower((string) $statusFluxo['titulo']);
    @endphp

    @once
        <style>
            .sc-public-shell {
                --sc-ink: #16303a;
                --sc-muted: #607580;
                --sc-line: rgba(22, 48, 58, 0.12);
                --sc-surface: #ffffff;
                --sc-soft: #f5f8f9;
            }

            .sc-public-pill {
                display: inline-flex;
                align-items: center;
                gap: 0.45rem;
                padding: 0.42rem 0.75rem;
                border-radius: 999px;
                border: 1px solid var(--sc-line);
                background: rgba(255, 255, 255, 0.72);
                color: var(--sc-ink);
                font-size: 0.82rem;
                font-weight: 600;
            }

            .sc-public-card {
                border: 1px solid var(--sc-line);
                border-radius: 18px;
                background: var(--sc-surface);
                box-shadow: 0 10px 22px rgba(17, 49, 58, 0.08);
            }

            .sc-public-meta {
                background: var(--sc-soft);
                border: 1px solid var(--sc-line);
                border-radius: 14px;
                padding: 0.85rem;
                min-height: 100%;
            }

            .sc-public-meta .label {
                color: var(--sc-muted);
                font-size: 0.76rem;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                margin-bottom: 0.35rem;
                font-weight: 700;
            }

            .sc-public-meta .value {
                color: var(--sc-ink);
                font-weight: 600;
            }

            .sc-public-section-title {
                color: var(--sc-ink);
                font-weight: 700;
                margin-bottom: 0.8rem;
            }

            .sc-public-cronograma {
                border: 1px solid var(--sc-line);
                border-radius: 12px;
                overflow: hidden;
                background: #fff;
            }

            .sc-public-cronograma table {
                margin-bottom: 0;
            }

            .sc-public-cronograma th {
                background: var(--sc-soft);
                color: var(--sc-muted);
                font-size: 0.78rem;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                white-space: nowrap;
            }

            .sc-public-cronograma td,
            .sc-public-cronograma th {
                border-color: var(--sc-line);
                vertical-align: middle;
            }

            .sc-public-aside {
                position: sticky;
                top: 1rem;
            }

            @media (max-width: 991.98px) {
                .sc-public-aside {
                    position: static;
                }
            }
        </style>
    @endonce

    <div class="sc-public-shell">
        <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
            <div>
                @if($processo->icone_processo)
                    <img src="{{ asset('storage/' . $processo->icone_processo) }}" alt="Imagem do processo"
                        style="max-height: 68px; max-width: 220px; object-fit: contain; margin-bottom: 0.6rem;">
                @endif
                <h2 class="mb-1" style="color: #16303a;">{{ $processo->titulo }}</h2>
                <p class="text-muted mb-0">
                    Edital {{ $processo->numero_edital ?: ($processo->numero_processo ?: 'Sem numero') }} -
                    {{ $processo->empresa?->nome_razao_social }}
                </p>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <span class="badge {{ $statusFluxo['badge_class'] }} px-3 py-2">{{ $statusFluxo['titulo'] }}</span>
                    @if($mostrarBadgeInscricao)
                        <span class="badge {{ $inscricaoBadgeClass }} px-3 py-2">{{ $inscricaoBadgeText }}</span>
                    @endif
                    <span class="sc-public-pill"><i class="fa-solid fa-briefcase"></i>{{ $tipoLabel }}</span>
                </div>
                <div class="small text-muted mt-2">{{ $inscricaoDescricao }}</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('sigeconcursos.publico.processos.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                </a>
                <a href="{{ route('sigeconcursos.candidato.login') }}" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-right-to-bracket me-1"></i> Login do Candidato
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card sc-public-card mb-3">
                    <div class="card-body p-4">
                        <h5 class="sc-public-section-title">Resumo do edital</h5>

                        <div class="row g-2 mb-3">
                            <div class="col-sm-6 col-lg-3">
                                <div class="sc-public-meta">
                                    <div class="label">Publicacao</div>
                                    <div class="value">{{ $processo->data_publicacao?->format('d/m/Y H:i') ?: 'Nao informada' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="sc-public-meta">
                                    <div class="label">Inicio inscricoes</div>
                                    <div class="value">{{ $processo->data_inicio_inscricoes?->format('d/m/Y H:i') ?: 'Nao definido' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="sc-public-meta">
                                    <div class="label">Fim inscricoes</div>
                                    <div class="value">{{ $processo->data_fim_inscricoes?->format('d/m/Y H:i') ?: 'Nao definido' }}</div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-lg-3">
                                <div class="sc-public-meta">
                                    <div class="label">Data da prova</div>
                                    <div class="value">{{ $processo->data_prova?->format('d/m/Y H:i') ?: 'Nao definida' }}</div>
                                </div>
                            </div>
                        </div>

                        @if($processo->resumo)
                            <h6 class="text-muted mb-2">Resumo</h6>
                            <div class="border rounded-3 p-3 bg-light mb-3" style="white-space: pre-line;">{{ $processo->resumo }}</div>
                        @endif

                        @if($processo->descricao)
                            <h6 class="text-muted mb-2">Descricao</h6>
                            <div class="border rounded-3 p-3 bg-light" style="white-space: pre-line;">{{ $processo->descricao }}</div>
                        @endif
                    </div>
                </div>

                <div class="card sc-public-card mb-3">
                    <div class="card-body p-4">
                        <h5 class="sc-public-section-title">Etapas do processo</h5>
                        @if(!empty($processo->fases))
                            <div class="sc-public-cronograma table-responsive">
                                <table class="table table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>Etapa</th>
                                            <th class="text-end">Periodo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($processo->fases as $indice => $fase)
                                            <tr>
                                                <td>
                                                    <strong>Etapa {{ $indice + 1 }}</strong><br>
                                                    <span class="text-muted">{{ $fase['descricao'] ?? 'Etapa' }}</span>
                                                </td>
                                                <td class="text-end">{{ $fase['periodo'] ?? 'Periodo nao informado' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">Nenhuma etapa foi cadastrada para este processo.</p>
                        @endif
                    </div>
                </div>

                <div class="card sc-public-card mb-3">
                    <div class="card-body p-4">
                        <h5 class="sc-public-section-title">Cargos e vagas</h5>
                        <div class="row g-2">
                            @forelse($processo->processoCargos as $item)
                                <div class="col-md-6">
                                    <div class="sc-public-meta h-100">
                                        <div class="value mb-2">{{ $item->cargo?->nome_cargo }}</div>
                                        <div class="small text-muted mb-1">Vagas: {{ $item->quantidade_vagas ?? '0' }}</div>
                                        <div class="small text-muted">Taxa:
                                            {{ $item->valor_taxa_inscricao !== null ? 'R$ ' . number_format((float) $item->valor_taxa_inscricao, 2, ',', '.') : 'Seguir regra geral do processo' }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted mb-0">Nenhum cargo cadastrado.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="card sc-public-card mb-3">
                    <div class="card-body p-4">
                        <h5 class="sc-public-section-title">Documentos exigidos na inscricao</h5>
                        <div class="row g-2">
                            @forelse($processo->documentosExigidos as $documento)
                                <div class="col-md-6">
                                    <div class="sc-public-meta h-100">
                                        <div class="value mb-1">{{ $documento->titulo }}</div>
                                        <div class="small text-muted mb-1">{{ $documento->obrigatorio ? 'Obrigatorio' : 'Opcional' }}</div>
                                        <div class="small text-muted">{{ $documento->descricao ?: 'Sem orientacao adicional.' }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted mb-0">Este processo nao possui documentos adicionais obrigatorios.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                @if($processo->arquivos->count() > 0)
                    <div class="card sc-public-card">
                        <div class="card-body p-4">
                            <h5 class="sc-public-section-title">Arquivos do processo</h5>
                            <div class="list-group">
                                @foreach($processo->arquivos as $arquivo)
                                    <a href="{{ asset('storage/' . $arquivo->caminho_arquivo) }}" target="_blank"
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center rounded-3 mb-2 border">
                                        <span>{{ $arquivo->nome_exibicao ?: 'Arquivo do processo' }}</span>
                                        <i class="fa-solid fa-arrow-up-right-from-square text-muted"></i>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="sc-public-aside">
                    <div class="card sc-public-card mb-3">
                        <div class="card-body p-4">
                            <h5 class="sc-public-section-title mb-3">Inscricao</h5>
                            @if($inscricaoExistente)
                                <div class="alert alert-success mb-3">
                                    Voce ja possui inscricao neste processo.
                                </div>
                                <p class="mb-1"><strong>Numero:</strong> {{ $inscricaoExistente->numero_inscricao }}</p>
                                <p class="mb-3"><strong>Status:</strong>
                                    {{ ucfirst(str_replace('_', ' ', (string) $inscricaoExistente->status_inscricao)) }}</p>
                                <a href="{{ route('sigeconcursos.candidato.minhas-inscricoes') }}" class="btn btn-outline-success w-100">
                                    <i class="fa-solid fa-clipboard-list me-1"></i> Ir para minhas inscricoes
                                </a>
                            @elseif(!$podeInscrever)
                                <div class="alert alert-warning mb-0">
                                    Este processo nao esta com inscricoes abertas no momento.
                                </div>
                            @else
                                @auth
                                    @if(auth()->user()->nivel === 'candidato')
                                        <a href="{{ route('sigeconcursos.candidato.processos.show', $processo->id_processo) }}" class="btn btn-primary w-100">
                                            <i class="fa-solid fa-file-signature me-1"></i> Ir para inscricao
                                        </a>
                                    @else
                                        <div class="alert alert-info mb-3">
                                            A inscricao em concursos e exclusiva para usuarios com perfil de candidato.
                                        </div>
                                        <a href="{{ route('sigeconcursos.candidato.login') }}" class="btn btn-primary w-100">
                                            <i class="fa-solid fa-right-to-bracket me-1"></i> Entrar como candidato
                                        </a>
                                    @endif
                                @else
                                    <div class="alert alert-info mb-3">
                                        Para se inscrever, voce precisa ter cadastro e estar logado na area do candidato.
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('sigeconcursos.candidato.login') }}" class="btn btn-primary">
                                            <i class="fa-solid fa-right-to-bracket me-1"></i> Ja tenho cadastro
                                        </a>
                                        <a href="{{ route('sigeconcursos.candidato.cadastro') }}" class="btn btn-outline-primary">
                                            <i class="fa-solid fa-user-plus me-1"></i> Quero me cadastrar
                                        </a>
                                    </div>
                                @endauth
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
