@extends('layouts.main')

@section('title', 'SIGE Concursos | Detalhes do Processo')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">{{ $processo->titulo }}</h2>
            <p class="text-muted mb-0">Edital {{ $processo->numero_edital }} - {{ $processo->empresa?->nome_razao_social }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sigeconcursos.candidato.processos.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Voltar
            </a>
            <a href="{{ route('sigeconcursos.candidato.minhas-inscricoes') }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-clipboard-list me-1"></i> Minhas Inscrições
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white"><strong>Resumo do Processo</strong></div>
                <div class="card-body">
                    <p class="mb-1"><strong>Tipo:</strong> {{ $processo->tipo_processo === 'concurso_publico' ? 'Concurso Público' : 'Processo Seletivo' }}</p>
                    <p class="mb-1"><strong>Publicação:</strong> {{ $processo->data_publicacao?->format('d/m/Y H:i') ?: 'Não informada' }}</p>
                    <p class="mb-1"><strong>Início das inscrições:</strong> {{ $processo->data_inicio_inscricoes?->format('d/m/Y H:i') ?: 'Não definido' }}</p>
                    <p class="mb-1"><strong>Fim das inscrições:</strong> {{ $processo->data_fim_inscricoes?->format('d/m/Y H:i') ?: 'Não definido' }}</p>
                    <p class="mb-3"><strong>Data da prova:</strong> {{ $processo->data_prova?->format('d/m/Y H:i') ?: 'Não definida' }}</p>

                    @if($processo->resumo)
                        <h6 class="text-muted">Resumo</h6>
                        <div class="border rounded p-3 bg-light mb-3" style="white-space: pre-line;">{{ $processo->resumo }}</div>
                    @endif

                    @if($processo->descricao)
                        <h6 class="text-muted">Descrição</h6>
                        <div class="border rounded p-3 bg-light" style="white-space: pre-line;">{{ $processo->descricao }}</div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white"><strong>Cargos Vinculados</strong></div>
                <div class="card-body">
                    @forelse($processo->processoCargos as $item)
                        <div class="border rounded p-3 mb-2 bg-light">
                            <div class="fw-semibold">{{ $item->cargo?->nome_cargo }}</div>
                            <div class="small text-muted">Vagas: {{ $item->quantidade_vagas ?? '0' }}</div>
                            <div class="small text-muted">Taxa: {{ $item->valor_taxa_inscricao !== null ? 'R$ ' . number_format((float) $item->valor_taxa_inscricao, 2, ',', '.') : 'Seguir regra geral do processo' }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Nenhum cargo cadastrado.</p>
                    @endforelse
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><strong>Documentos Exigidos na Inscrição</strong></div>
                <div class="card-body">
                    @forelse($processo->documentosExigidos as $documento)
                        <div class="border rounded p-3 mb-2 bg-light">
                            <div class="fw-semibold">{{ $documento->titulo }}</div>
                            <div class="small text-muted">{{ $documento->obrigatorio ? 'Obrigatório' : 'Opcional' }}</div>
                            <div class="small text-muted">{{ $documento->descricao ?: 'Sem orientação adicional.' }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Este processo não possui documentos adicionais obrigatórios.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            @if($inscricaoExistente)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-success text-white"><strong>Você já está inscrito(a)</strong></div>
                    <div class="card-body">
                        <p class="mb-1"><strong>Número da inscrição:</strong> {{ $inscricaoExistente->numero_inscricao }}</p>
                        <p class="mb-1"><strong>Modalidade:</strong> {{ $inscricaoExistente->modalidadeLabel() }}</p>
                        <p class="mb-1"><strong>Status:</strong> {{ ucfirst($inscricaoExistente->status_inscricao) }}</p>
                        <p class="mb-1"><strong>Isenção:</strong> {{ ucfirst(str_replace('_', ' ', $inscricaoExistente->status_isencao)) }}</p>
                        @if($inscricaoExistente->isencao)
                            <p class="mb-1"><strong>Caso solicitado:</strong> {{ $inscricaoExistente->isencao->titulo }}</p>
                        @endif
                        @if($inscricaoExistente->justificativa_isencao)
                            <p class="mb-1"><strong>Justificativa:</strong> {{ $inscricaoExistente->justificativa_isencao }}</p>
                        @endif
                        @if($inscricaoExistente->parecer_isencao)
                            <p class="mb-1"><strong>Parecer da análise:</strong> {{ $inscricaoExistente->parecer_isencao }}</p>
                        @endif
                        @if($inscricaoExistente->documentosIsencao->count() > 0)
                            <div class="mb-1">
                                <strong>Documentos de isenção:</strong>
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

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><strong>Formulário de Inscrição</strong></div>
                <div class="card-body">
                    @if(!$podeInscrever && !$inscricaoExistente)
                        <div class="alert alert-warning mb-0">
                            Este processo não está com inscrições abertas no momento.
                        </div>
                    @else
                        <form action="{{ route('sigeconcursos.candidato.processos.inscrever', $processo->id_processo) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="modalidade_concorrencia" class="form-label">Modalidade de Concorrência</label>
                                <select id="modalidade_concorrencia" name="modalidade_concorrencia" class="form-select @error('modalidade_concorrencia') is-invalid @enderror" {{ $inscricaoExistente ? 'disabled' : '' }} required>
                                    <option value="">Selecione</option>
                                    @if($processo->permite_ampla_concorrencia)
                                        <option value="ampla_concorrencia" {{ old('modalidade_concorrencia') === 'ampla_concorrencia' ? 'selected' : '' }}>Ampla Concorrência</option>
                                    @endif
                                    @if($processo->permite_pcd)
                                        <option value="pcd" {{ old('modalidade_concorrencia') === 'pcd' ? 'selected' : '' }}>PCD</option>
                                    @endif
                                </select>
                                @error('modalidade_concorrencia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="solicitou_isencao" name="solicitou_isencao" value="1" {{ old('solicitou_isencao') ? 'checked' : '' }} {{ $inscricaoExistente ? 'disabled' : '' }}>
                                <label class="form-check-label" for="solicitou_isencao">Desejo solicitar isenção de taxa</label>
                            </div>

                            @if($processo->possui_taxa_inscricao && $processo->isencoes->count() > 0)
                                <div class="mb-2">
                                    <label for="fk_id_isencao" class="form-label">Caso de isenção</label>
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
                                    <label for="justificativa_isencao" class="form-label">Justificativa da isenção</label>
                                    <textarea id="justificativa_isencao" name="justificativa_isencao" rows="3" class="form-control @error('justificativa_isencao') is-invalid @enderror" {{ $inscricaoExistente ? 'disabled' : '' }}>{{ old('justificativa_isencao') }}</textarea>
                                    @error('justificativa_isencao')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="mb-2">
                                    <label for="isencao_documentos" class="form-label">Documentos comprobatórios da isenção</label>
                                    <input type="file" id="isencao_documentos" name="isencao_documentos[]" class="form-control @error('isencao_documentos.*') is-invalid @enderror" multiple {{ $inscricaoExistente ? 'disabled' : '' }}>
                                    <div class="form-text">Envie os documentos que comprovam o enquadramento no caso de isenção selecionado.</div>
                                    @error('isencao_documentos.*')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            @endif

                            @if($processo->permite_condicao_especial)
                                <hr>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="solicitou_condicao_especial" name="solicitou_condicao_especial" value="1" {{ old('solicitou_condicao_especial') ? 'checked' : '' }} {{ $inscricaoExistente ? 'disabled' : '' }}>
                                    <label class="form-check-label" for="solicitou_condicao_especial">Solicitar condição especial de aplicação</label>
                                </div>
                                <div class="mb-2">
                                    <label for="descricao_condicao_especial" class="form-label">Descrição da condição especial</label>
                                    <textarea id="descricao_condicao_especial" name="descricao_condicao_especial" rows="3" class="form-control @error('descricao_condicao_especial') is-invalid @enderror" {{ $inscricaoExistente ? 'disabled' : '' }}>{{ old('descricao_condicao_especial') }}</textarea>
                                    @error('descricao_condicao_especial')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-2">
                                    <label for="documento_condicao_especial" class="form-label">Laudo/Documento da condição especial</label>
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
                                        Confirmo a leitura do edital de abertura e estou ciente das informações contidas.
                                    </label>
                                    @error('aceite_edital')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            @endif

                            @if(!$inscricaoExistente)
                                <button type="submit" class="btn btn-primary w-100 mt-3">
                                    <i class="fa-solid fa-file-signature me-1"></i> Confirmar Inscrição
                                </button>
                            @endif
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
