@extends('layouts.main')

@section('title', 'SIGE Concursos | Detalhes do Processo')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-3">
        <div>
            <h1 class="mb-1">Hub do Processo</h1>
            <p class="text-muted mb-0">Visao executiva, jornada operacional e acessos rapidos em um unico lugar.</p>
        </div>
        <button onclick="window.NavigationHistory?.goBack('{{ route('sigeconcursos.processos.index') }}')"
            class="btn btn-outline-secondary" title="Voltar para a página anterior com filtros preservados">Voltar</button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @include('sigeconcursos.processos._workflow-hub', ['processo' => $processo])

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white">Visao do Edital</div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="small text-muted">Tipo</div>
                            <div class="fw-semibold">{{ $processo->tipo_processo === 'concurso_publico' ? 'Concurso Publico' : 'Processo Seletivo' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted">Orgao responsavel</div>
                            <div class="fw-semibold">{{ $processo->empresa?->nome_razao_social ?: 'Nao informado' }}</div>
                        </div>
                    </div>

                    @if($processo->resumo)
                        <div class="rounded-4 p-3 mb-3" style="background: #f8f5ee; border: 1px solid rgba(17, 49, 58, 0.08);">
                            <div class="small text-uppercase text-muted fw-semibold mb-2">Resumo executivo</div>
                            <div style="white-space: pre-line;">{{ $processo->resumo }}</div>
                        </div>
                    @endif

                    @if($processo->descricao)
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Descricao completa</h6>
                            <div class="border rounded-4 p-3 bg-light" style="white-space: pre-line;">{{ $processo->descricao }}</div>
                        </div>
                    @endif

                    @if($processo->requisitos_gerais)
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Requisitos gerais</h6>
                            <div class="border rounded-4 p-3 bg-light" style="white-space: pre-line;">{{ $processo->requisitos_gerais }}</div>
                        </div>
                    @endif

                    @if($processo->observacoes)
                        <div>
                            <h6 class="text-muted mb-2">Observacoes internas</h6>
                            <div class="border rounded-4 p-3 bg-light" style="white-space: pre-line;">{{ $processo->observacoes }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white">Painel de configuracao</div>
                <div class="card-body">
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Publicacao</span>
                        <span class="fw-semibold">{{ $processo->data_publicacao?->format('d/m/Y H:i') ?: 'Nao informada' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Inicio das inscricoes</span>
                        <span class="fw-semibold">{{ $processo->data_inicio_inscricoes?->format('d/m/Y H:i') ?: 'Nao informado' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Fim das inscricoes</span>
                        <span class="fw-semibold">{{ $processo->data_fim_inscricoes?->format('d/m/Y H:i') ?: 'Nao informado' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Data da prova</span>
                        <span class="fw-semibold">{{ $processo->data_prova?->format('d/m/Y H:i') ?: 'Nao informada' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Resultado final</span>
                        <span class="fw-semibold">{{ $processo->data_resultado_final?->format('d/m/Y H:i') ?: 'Nao informado' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Aceite do edital</span>
                        <span class="fw-semibold">{{ $processo->exige_aceite_edital ? 'Sim' : 'Nao' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Condicao especial</span>
                        <span class="fw-semibold">{{ $processo->permite_condicao_especial ? 'Sim' : 'Nao' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Laudo obrigatorio</span>
                        <span class="fw-semibold">{{ $processo->exige_documento_condicao_especial ? 'Sim' : 'Nao' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Taxa de inscricao</span>
                        <span class="fw-semibold">{{ $processo->possui_taxa_inscricao ? 'Sim' : 'Nao' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Valor da taxa</span>
                        <span class="fw-semibold">{{ $processo->valor_taxa_padrao !== null ? 'R$ ' . number_format((float) $processo->valor_taxa_padrao, 2, ',', '.') : 'Nao informado' }}</span>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom">
                        <span class="text-muted">Ampla concorrencia</span>
                        <span class="fw-semibold">{{ $processo->permite_ampla_concorrencia ? 'Sim' : 'Nao' }}</span>
                    </div>
                    <div class="d-flex justify-content-between pt-2">
                        <span class="text-muted">PCD</span>
                        <span class="fw-semibold">{{ $processo->permite_pcd ? 'Sim' : 'Nao' }}</span>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white">Arquivos do processo</div>
                <div class="card-body">
                    @forelse($processo->arquivos as $arquivo)
                        <div class="border rounded-4 p-3 mb-2 bg-light d-flex justify-content-between align-items-center gap-2">
                            <div>
                                <div class="fw-semibold">{{ $arquivo->nome_exibicao }}</div>
                                <div class="small text-muted">{{ ucfirst($arquivo->tipo_arquivo) }}</div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ asset('storage/' . $arquivo->caminho_arquivo) }}" target="_blank" class="btn btn-sm btn-outline-primary">Abrir</a>
                                <form action="{{ route('sigeconcursos.processos.arquivos.destroy', $arquivo->id_arquivo) }}" method="POST" onsubmit="return confirm('Remover este arquivo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Nenhum arquivo anexado ao processo.</p>
                    @endforelse
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">Acoes administrativas</div>
                <div class="card-body d-grid gap-2">
                    @if($processo->status === 'rascunho')
                        <form action="{{ route('sigeconcursos.processos.publicar-edital', $processo->id_processo) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">Publicar edital</button>
                        </form>
                    @endif
                    @if(!in_array($processo->status, ['suspenso', 'finalizado'], true))
                        <form action="{{ route('sigeconcursos.processos.iniciar-inscricoes', $processo->id_processo) }}" method="POST">
                            @csrf
                            <input type="hidden" name="redirect_to"
                                value="{{ route('sigeconcursos.processos.inscricoes', $processo->id_processo, false) }}#painel-homologacao">
                            <button type="submit" class="btn btn-primary w-100">Acompanhar inscricoes</button>
                        </form>
                    @endif
                    <a href="{{ route('sigeconcursos.processos.edit', $processo->id_processo) }}" class="btn btn-dark">Editar processo</a>
                    <form action="{{ route('sigeconcursos.processos.destroy', $processo->id_processo) }}" method="POST"
                        onsubmit="return confirm('Confirma a exclusão deste processo?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">Excluir processo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white">Cargos vinculados</div>
                <div class="card-body">
                    @forelse($processo->processoCargos as $item)
                        <div class="border rounded-4 p-3 mb-2 bg-light">
                            <div class="fw-semibold">{{ $item->cargo?->nome_cargo }}</div>
                            <div class="small text-muted">Vagas: {{ $item->descricaoVagas() }}</div>
                            <div class="small text-muted">Remuneracao: {{ $item->valor_remuneracao !== null ? 'R$ ' . number_format((float) $item->valor_remuneracao, 2, ',', '.') : 'Nao informada' }}</div>
                            <div class="small text-muted">Taxa: {{ $item->valor_taxa_inscricao !== null ? 'R$ ' . number_format((float) $item->valor_taxa_inscricao, 2, ',', '.') : 'Nao informada' }}</div>
                            <div class="small text-muted">Carga horaria: {{ $item->carga_horaria ?: 'Nao informada' }}</div>
                            <div class="small text-muted">{{ $item->requisitos_especificos ?: 'Sem requisitos especificos.' }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Nenhum cargo vinculado.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white">Locais de prova</div>
                <div class="card-body">
                    @forelse($processo->processoLocais as $item)
                        <div class="border rounded-4 p-3 mb-2 bg-light">
                            <div class="fw-semibold">{{ $item->localProva?->nome_local }}</div>
                            <div class="small text-muted">{{ $item->localProva?->cidade?->nm_cidade }} / {{ $item->localProva?->cidade?->estado?->uf_estado }}</div>
                            <div class="small text-muted">Salas cadastradas: {{ $item->localProva?->salas?->count() }}</div>
                            <div class="small text-muted">{{ $item->observacoes ?: 'Sem observacoes.' }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Nenhum local vinculado.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white">Cronograma por fases</div>
                <div class="card-body">
                    @forelse(($processo->fases ?? []) as $fase)
                        <div class="border rounded-4 p-3 mb-2 bg-light">
                            <div class="fw-semibold">{{ $fase['descricao'] ?? 'Fase' }}</div>
                            <div class="small text-muted">{{ $fase['periodo'] ?? 'Periodo nao informado' }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Nenhuma fase cadastrada.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white">Casos de isencao</div>
                <div class="card-body">
                    @forelse($processo->isencoes as $isencao)
                        <div class="border rounded-4 p-3 mb-2 bg-light">
                            <div class="fw-semibold">{{ $isencao->titulo }}</div>
                            <div class="small text-muted">{{ $isencao->descricao ?: 'Sem descricao.' }}</div>
                            <div class="small text-muted">Periodo: {{ $isencao->data_inicio?->format('d/m/Y H:i') ?: 'Nao informado' }} ate {{ $isencao->data_fim?->format('d/m/Y H:i') ?: 'Nao informado' }}</div>
                            <div class="small text-muted">Comprovacao: {{ $isencao->exige_comprovacao ? 'Sim' : 'Nao' }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Nenhum caso de isencao cadastrado.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-3">
        <div class="card-header bg-white">Documentos exigidos na inscricao</div>
        <div class="card-body">
            @forelse($processo->documentosExigidos as $documento)
                <div class="border rounded-4 p-3 mb-2 bg-light d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <div class="fw-semibold">{{ $documento->titulo }}</div>
                        <div class="small text-muted">{{ $documento->obrigatorio ? 'Obrigatorio' : 'Opcional' }}</div>
                        <div class="small text-muted">{{ $documento->descricao ?: 'Sem orientacao adicional.' }}</div>
                    </div>
                    <form action="{{ route('sigeconcursos.processos.documentos-exigidos.destroy', $documento->id_documento_exigido) }}"
                        method="POST" onsubmit="return confirm('Remover este documento exigido?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                    </form>
                </div>
            @empty
                <p class="text-muted mb-0">Nenhum documento exigido configurado para a inscricao.</p>
            @endforelse
        </div>
    </div>
@endsection