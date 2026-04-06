@extends('layouts.main')

@section('title', 'SIGE Concursos | Detalhes do Processo')

@section('content')
    @php
        $statusFluxo = $processo->statusApresentacaoDefinicao();
        $tipoLabel = $processo->tipo_processo === 'concurso_publico' ? 'Concurso Publico' : 'Processo Seletivo';

        if ($podeInscrever) {
            $inscricaoBadgeClass = 'bg-success';
            $inscricaoBadgeText = 'Inscricoes abertas';
            $inscricaoDescricao = 'Voce pode concluir sua inscricao nesta pagina.';
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
            .sc-candidato-shell {
                --sc-ink: #16303a;
                --sc-muted: #607580;
                --sc-line: rgba(22, 48, 58, 0.12);
                --sc-surface: #ffffff;
                --sc-soft: #f5f8f9;
            }

            .sc-candidato-pill {
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

            .sc-candidato-card {
                border: 1px solid var(--sc-line);
                border-radius: 18px;
                background: var(--sc-surface);
                box-shadow: 0 10px 22px rgba(17, 49, 58, 0.08);
            }

            .sc-candidato-meta {
                background: var(--sc-soft);
                border: 1px solid var(--sc-line);
                border-radius: 14px;
                padding: 0.85rem;
                min-height: 100%;
            }

            .sc-candidato-meta .label {
                color: var(--sc-muted);
                font-size: 0.76rem;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                margin-bottom: 0.35rem;
                font-weight: 700;
            }

            .sc-candidato-meta .value {
                color: var(--sc-ink);
                font-weight: 600;
            }

            .sc-candidato-section-title {
                color: var(--sc-ink);
                font-weight: 700;
                margin-bottom: 0.8rem;
            }

            .sc-candidato-cronograma {
                border: 1px solid var(--sc-line);
                border-radius: 12px;
                overflow: hidden;
                background: #fff;
            }

            .sc-candidato-cronograma table {
                margin-bottom: 0;
            }

            .sc-candidato-cronograma th {
                background: var(--sc-soft);
                color: var(--sc-muted);
                font-size: 0.78rem;
                text-transform: uppercase;
                letter-spacing: 0.04em;
                white-space: nowrap;
            }

            .sc-candidato-cronograma td,
            .sc-candidato-cronograma th {
                border-color: var(--sc-line);
                vertical-align: middle;
            }

            .sc-candidato-form-card {
                border: 2px solid #0f7670 !important;
                border-radius: 18px;
                background: var(--sc-surface);
                box-shadow: 0 16px 48px rgba(15, 118, 110, 0.18) !important;
                position: relative;
            }

            .sc-candidato-form-card::before {
                content: 'FORMULÁRIO DE INSCRIÇÃO';
                position: absolute;
                top: -12px;
                left: 16px;
                background: #0f7670;
                color: white;
                font-size: 0.72rem;
                font-weight: 700;
                padding: 0.35rem 0.8rem;
                border-radius: 12px;
                text-transform: uppercase;
                letter-spacing: 0.04em;
            }

            .sc-candidato-aside {
                position: sticky;
                top: 1rem;
            }

            @media (max-width: 991.98px) {
                .sc-candidato-aside {
                    position: static;
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

    <div class="sc-candidato-shell">
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
                    <span class="sc-candidato-pill"><i class="fa-solid fa-briefcase"></i>{{ $tipoLabel }}</span>
                </div>
                <div class="small text-muted mt-2">{{ $inscricaoDescricao }}</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('sigeconcursos.candidato.processos.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                </a>
                <a href="{{ route('sigeconcursos.candidato.minhas-inscricoes') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fa-solid fa-clipboard-list me-1"></i> Minhas inscricoes
                </a>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card sc-candidato-card mb-3">
                    <div class="card-body p-4">
                        <h5 class="sc-candidato-section-title">Resumo do edital</h5>

                        <div class="sc-candidato-cronograma table-responsive mb-3">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th>Evento</th>
                                        <th class="text-end">Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Publicacao do edital</td>
                                        <td class="text-end">{{ $processo->data_publicacao?->format('d/m/Y H:i') ?: 'Nao informada' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Inicio das inscricoes</td>
                                        <td class="text-end">{{ $processo->data_inicio_inscricoes?->format('d/m/Y H:i') ?: 'Nao definido' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Fim das inscricoes</td>
                                        <td class="text-end">{{ $processo->data_fim_inscricoes?->format('d/m/Y H:i') ?: 'Nao definido' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Data da prova</td>
                                        <td class="text-end">{{ $processo->data_prova?->format('d/m/Y H:i') ?: 'Nao definida' }}</td>
                                    </tr>
                                </tbody>
                            </table>
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

                <div class="card sc-candidato-card mb-3">
                    <div class="card-body p-4">
                        <h5 class="sc-candidato-section-title">Etapas do processo</h5>
                        @if(!empty($processo->fases))
                            <div class="sc-candidato-cronograma table-responsive">
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

                <div class="card sc-candidato-card mb-3">
                    <div class="card-body p-4">
                        <h5 class="sc-candidato-section-title">Cargos e vagas</h5>
                        <div class="row g-2">
                            @forelse($processo->processoCargos as $item)
                                <div class="col-md-6">
                                    <div class="sc-candidato-meta h-100">
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

                <div class="card sc-candidato-card mb-3">
                    <div class="card-body p-4">
                        <h5 class="sc-candidato-section-title">Documentos exigidos na inscricao</h5>
                        <div class="row g-2">
                            @forelse($processo->documentosExigidos as $documento)
                                <div class="col-md-6">
                                    <div class="sc-candidato-meta h-100">
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
            </div>

            <div class="col-lg-4">
                <div class="sc-candidato-aside">
                    @if($inscricaoExistente)
                        <div class="card sc-candidato-card mb-3">
                            <div class="card-body p-4">
                                <h5 class="sc-candidato-section-title mb-3">Sua inscricao</h5>
                                <div class="alert alert-success mb-3">Voce ja esta inscrito(a) neste processo.</div>
                                <p class="mb-1"><strong>Numero:</strong> {{ $inscricaoExistente->numero_inscricao }}</p>
                                <p class="mb-1"><strong>Modalidade:</strong> {{ $inscricaoExistente->modalidadeLabel() }}</p>
                                <p class="mb-1"><strong>Status:</strong> {{ ucfirst($inscricaoExistente->status_inscricao) }}</p>
                                <p class="mb-1"><strong>Isencao:</strong> {{ ucfirst(str_replace('_', ' ', $inscricaoExistente->status_isencao)) }}</p>
                                @if($inscricaoExistente->isencao)
                                    <p class="mb-1"><strong>Caso solicitado:</strong> {{ $inscricaoExistente->isencao->titulo }}</p>
                                @endif
                                @if($inscricaoExistente->justificativa_isencao)
                                    <p class="mb-1"><strong>Justificativa:</strong> {{ $inscricaoExistente->justificativa_isencao }}</p>
                                @endif
                                @if($inscricaoExistente->parecer_isencao)
                                    <p class="mb-1"><strong>Parecer da analise:</strong> {{ $inscricaoExistente->parecer_isencao }}</p>
                                @endif
                                @if($inscricaoExistente->documentosIsencao->count() > 0)
                                    <div class="mb-1">
                                        <strong>Documentos de isencao:</strong>
                                        <ul class="mb-0 mt-1 ps-3">
                                            @foreach($inscricaoExistente->documentosIsencao as $documentoIsencao)
                                                <li>
                                                    <a href="{{ asset('storage/' . $documentoIsencao->caminho_arquivo) }}" target="_blank">
                                                        {{ $documentoIsencao->nome_documento }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <p class="mb-0"><strong>Data:</strong> {{ $inscricaoExistente->created_at?->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="card sc-candidato-card sc-candidato-form-card">
                        <div class="card-body p-4" style="padding-top: 2.5rem !important;">
                            <h5 class="sc-candidato-section-title mb-3">Formulario de inscricao</h5>
                            @if(!$podeInscrever && !$inscricaoExistente)
                                <div class="alert alert-warning mb-0">
                                    Este processo nao esta com inscricoes abertas no momento.
                                </div>
                            @else
                                <form action="{{ route('sigeconcursos.candidato.processos.inscrever', $processo->id_processo) }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="modalidade_concorrencia" class="form-label">Modalidade de concorrencia</label>
                                        <select id="modalidade_concorrencia" name="modalidade_concorrencia" class="form-select @error('modalidade_concorrencia') is-invalid @enderror" {{ $inscricaoExistente ? 'disabled' : '' }} required>
                                            <option value="">Selecione</option>
                                            @if($processo->permite_ampla_concorrencia)
                                                <option value="ampla_concorrencia" {{ old('modalidade_concorrencia') === 'ampla_concorrencia' ? 'selected' : '' }}>Ampla concorrencia</option>
                                            @endif
                                            @if($processo->permite_pcd)
                                                <option value="pcd" {{ old('modalidade_concorrencia') === 'pcd' ? 'selected' : '' }}>PCD</option>
                                            @endif
                                        </select>
                                        @error('modalidade_concorrencia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="solicitou_isencao" name="solicitou_isencao" value="1" {{ old('solicitou_isencao') ? 'checked' : '' }} {{ $inscricaoExistente ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="solicitou_isencao">Desejo solicitar isencao de taxa</label>
                                    </div>

                                    @if($processo->possui_taxa_inscricao && $processo->isencoes->count() > 0)
                                        <div class="mb-2">
                                            <label for="fk_id_isencao" class="form-label">Caso de isencao</label>
                                            <select id="fk_id_isencao" name="fk_id_isencao" class="form-select @error('fk_id_isencao') is-invalid @enderror" {{ $inscricaoExistente ? 'disabled' : '' }}>
                                                <option value="">Selecione</option>
                                                @foreach($processo->isencoes as $isencao)
                                                    <option value="{{ $isencao->id_isencao }}" {{ (string) old('fk_id_isencao') === (string) $isencao->id_isencao ? 'selected' : '' }}>
                                                        {{ $isencao->titulo }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('fk_id_isencao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="mb-2">
                                            <label for="justificativa_isencao" class="form-label">Justificativa da isencao</label>
                                            <textarea id="justificativa_isencao" name="justificativa_isencao" rows="3" class="form-control @error('justificativa_isencao') is-invalid @enderror" {{ $inscricaoExistente ? 'disabled' : '' }}>{{ old('justificativa_isencao') }}</textarea>
                                            @error('justificativa_isencao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>

                                        <div class="mb-2">
                                            <label for="isencao_documentos" class="form-label">Documentos comprobatórios da isencao</label>
                                            <input type="file" id="isencao_documentos" name="isencao_documentos[]" class="form-control @error('isencao_documentos.*') is-invalid @enderror" multiple {{ $inscricaoExistente ? 'disabled' : '' }}>
                                            <div class="form-text">Envie os documentos que comprovam o enquadramento no caso de isencao selecionado.</div>
                                            @error('isencao_documentos.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    @endif

                                    @if($processo->permite_condicao_especial)
                                        <hr>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="solicitou_condicao_especial" name="solicitou_condicao_especial" value="1" {{ old('solicitou_condicao_especial') ? 'checked' : '' }} {{ $inscricaoExistente ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="solicitou_condicao_especial">Solicitar condicao especial de aplicacao</label>
                                        </div>
                                        <div class="mb-2">
                                            <label for="descricao_condicao_especial" class="form-label">Descricao da condicao especial</label>
                                            <textarea id="descricao_condicao_especial" name="descricao_condicao_especial" rows="3" class="form-control @error('descricao_condicao_especial') is-invalid @enderror" {{ $inscricaoExistente ? 'disabled' : '' }}>{{ old('descricao_condicao_especial') }}</textarea>
                                            @error('descricao_condicao_especial')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-2">
                                            <label for="documento_condicao_especial" class="form-label">Laudo/Documento da condicao especial</label>
                                            <input type="file" id="documento_condicao_especial" name="documento_condicao_especial" class="form-control @error('documento_condicao_especial') is-invalid @enderror" {{ $inscricaoExistente ? 'disabled' : '' }}>
                                            @error('documento_condicao_especial')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    @endif

                                    @if($processo->documentosExigidos->count() > 0)
                                        <hr>
                                        <h6 class="mb-3">Documentos do edital</h6>
                                        @foreach($processo->documentosExigidos as $documento)
                                            <div class="mb-3">
                                                <label for="documento_{{ $documento->id_documento_exigido }}" class="form-label">
                                                    {{ $documento->titulo }}
                                                    @if($documento->obrigatorio)
                                                        <span class="text-danger">*</span>
                                                    @endif
                                                </label>
                                                <input type="file" id="documento_{{ $documento->id_documento_exigido }}" name="documentos_exigidos[{{ $documento->id_documento_exigido }}]"
                                                    class="form-control @error('documentos_exigidos.' . $documento->id_documento_exigido) is-invalid @enderror" {{ $inscricaoExistente ? 'disabled' : '' }}>
                                                @if($documento->descricao)
                                                    <div class="form-text">{{ $documento->descricao }}</div>
                                                @endif
                                                @error('documentos_exigidos.' . $documento->id_documento_exigido)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            </div>
                                        @endforeach
                                    @endif

                                    @if($processo->exige_aceite_edital)
                                        <div class="form-check mt-3">
                                            <input class="form-check-input @error('aceite_edital') is-invalid @enderror" type="checkbox" id="aceite_edital" name="aceite_edital" value="1" {{ old('aceite_edital') ? 'checked' : '' }} {{ $inscricaoExistente ? 'disabled' : '' }}>
                                            <label class="form-check-label" for="aceite_edital">
                                                Confirmo a leitura do edital de abertura e estou ciente das informacoes contidas.
                                            </label>
                                            @error('aceite_edital')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                        </div>
                                    @endif

                                    @if(!$inscricaoExistente)
                                        <button type="submit" class="btn btn-primary w-100 mt-3">
                                            <i class="fa-solid fa-file-signature me-1"></i> Confirmar inscricao
                                        </button>
                                    @endif
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
