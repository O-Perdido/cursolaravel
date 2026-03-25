@extends('layouts.main')

@section('title', 'SIGE Concursos | Detalhes do Processo')

@section('content')
    <h1>Detalhes do Processo</h1>
    <button onclick="window.NavigationHistory?.goBack('{{ route('sigeconcursos.processos.index') }}')"
        class="btn btn-secondary mb-3" title="Voltar para a página anterior com filtros preservados">Voltar</button>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm mb-3">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0">{{ $processo->titulo }}</h5>
                <small class="text-muted">{{ $processo->numero_processo }} • Edital {{ $processo->numero_edital }}</small>
            </div>
            <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $processo->status)) }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Informações Gerais</h6>
                    <p class="mb-1"><strong>Tipo:</strong> {{ $processo->tipo_processo === 'concurso_publico' ? 'Concurso Público' : 'Processo Seletivo' }}</p>
                    <p class="mb-1"><strong>Órgão:</strong> {{ $processo->empresa?->nome_razao_social }}</p>
                    <p class="mb-1"><strong>Publicação:</strong> {{ $processo->data_publicacao?->format('d/m/Y H:i') ?: 'Não informada' }}</p>
                    <p class="mb-1"><strong>Início das Inscrições:</strong> {{ $processo->data_inicio_inscricoes?->format('d/m/Y H:i') ?: 'Não informado' }}</p>
                    <p class="mb-1"><strong>Fim das Inscrições:</strong> {{ $processo->data_fim_inscricoes?->format('d/m/Y H:i') ?: 'Não informado' }}</p>
                    <p class="mb-1"><strong>Data da Prova:</strong> {{ $processo->data_prova?->format('d/m/Y H:i') ?: 'Não informada' }}</p>
                    <p class="mb-1"><strong>Resultado Final:</strong> {{ $processo->data_resultado_final?->format('d/m/Y H:i') ?: 'Não informado' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Configurações</h6>
                    <p class="mb-1"><strong>Aceite do edital:</strong> {{ $processo->exige_aceite_edital ? 'Sim' : 'Não' }}</p>
                    <p class="mb-1"><strong>Escolha de local:</strong> {{ $processo->permite_escolha_local_prova ? 'Sim' : 'Não' }}</p>
                    <p class="mb-1"><strong>Taxa de inscrição:</strong> {{ $processo->possui_taxa_inscricao ? 'Sim' : 'Não' }}</p>
                    <p class="mb-1"><strong>Valor padrão da taxa:</strong> {{ $processo->valor_taxa_padrao !== null ? 'R$ ' . number_format((float) $processo->valor_taxa_padrao, 2, ',', '.') : 'Não informado' }}</p>
                    <p class="mb-1"><strong>Ampla concorrência:</strong> {{ $processo->permite_ampla_concorrencia ? 'Sim' : 'Não' }}</p>
                    <p class="mb-1"><strong>PCD:</strong> {{ $processo->permite_pcd ? 'Sim' : 'Não' }}</p>
                </div>
            </div>

            @if($processo->resumo)
                <hr class="my-3">
                <h6 class="text-muted mb-2">Resumo</h6>
                <div class="border rounded p-3 bg-light">{{ $processo->resumo }}</div>
            @endif

            @if($processo->descricao)
                <hr class="my-3">
                <h6 class="text-muted mb-2">Descrição</h6>
                <div class="border rounded p-3 bg-light" style="white-space: pre-line;">{{ $processo->descricao }}</div>
            @endif

            @if($processo->requisitos_gerais)
                <hr class="my-3">
                <h6 class="text-muted mb-2">Requisitos Gerais</h6>
                <div class="border rounded p-3 bg-light" style="white-space: pre-line;">{{ $processo->requisitos_gerais }}</div>
            @endif

            @if($processo->observacoes)
                <hr class="my-3">
                <h6 class="text-muted mb-2">Observações</h6>
                <div class="border rounded p-3 bg-light" style="white-space: pre-line;">{{ $processo->observacoes }}</div>
            @endif
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('sigeconcursos.processos.edit', $processo->id_processo) }}" class="btn btn-info">Editar</a>
            <form action="{{ route('sigeconcursos.processos.destroy', $processo->id_processo) }}" method="POST"
                style="display:inline;" onsubmit="return confirm('Confirma a exclusão deste processo?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Excluir</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header">Cargos Vinculados</div>
                <div class="card-body">
                    @forelse($processo->processoCargos as $item)
                        <div class="border rounded p-3 mb-2 bg-light">
                            <div class="fw-semibold">{{ $item->cargo?->nome_cargo }}</div>
                            <div class="small text-muted">Vagas: {{ $item->quantidade_vagas ?? '0' }} • CR: {{ $item->quantidade_cadastro_reserva ?? '0' }}</div>
                            <div class="small text-muted">Remuneração: {{ $item->valor_remuneracao !== null ? 'R$ ' . number_format((float) $item->valor_remuneracao, 2, ',', '.') : 'Não informada' }}</div>
                            <div class="small text-muted">Taxa: {{ $item->valor_taxa_inscricao !== null ? 'R$ ' . number_format((float) $item->valor_taxa_inscricao, 2, ',', '.') : 'Não informada' }}</div>
                            <div class="small text-muted">Carga horária: {{ $item->carga_horaria ?: 'Não informada' }}</div>
                            <div class="small text-muted">{{ $item->requisitos_especificos ?: 'Sem requisitos específicos.' }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Nenhum cargo vinculado.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header">Locais de Prova</div>
                <div class="card-body">
                    @forelse($processo->processoLocais as $item)
                        <div class="border rounded p-3 mb-2 bg-light">
                            <div class="fw-semibold">{{ $item->localProva?->nome_local }}</div>
                            <div class="small text-muted">{{ $item->localProva?->cidade?->nm_cidade }} / {{ $item->localProva?->cidade?->estado?->uf_estado }}</div>
                            <div class="small text-muted">Salas cadastradas: {{ $item->localProva?->salas?->count() }}</div>
                            <div class="small text-muted">{{ $item->observacoes ?: 'Sem observações.' }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Nenhum local vinculado.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header">Cronograma por Fases</div>
                <div class="card-body">
                    @forelse(($processo->fases ?? []) as $fase)
                        <div class="border rounded p-3 mb-2 bg-light">
                            <div class="fw-semibold">{{ $fase['descricao'] ?? 'Fase' }}</div>
                            <div class="small text-muted">{{ $fase['periodo'] ?? 'Período não informado' }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Nenhuma fase cadastrada.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header">Casos de Isenção</div>
                <div class="card-body">
                    @forelse($processo->isencoes as $isencao)
                        <div class="border rounded p-3 mb-2 bg-light">
                            <div class="fw-semibold">{{ $isencao->titulo }}</div>
                            <div class="small text-muted">{{ $isencao->descricao ?: 'Sem descrição.' }}</div>
                            <div class="small text-muted">Período: {{ $isencao->data_inicio?->format('d/m/Y H:i') ?: 'Não informado' }} até {{ $isencao->data_fim?->format('d/m/Y H:i') ?: 'Não informado' }}</div>
                            <div class="small text-muted">Comprovação: {{ $isencao->exige_comprovacao ? 'Sim' : 'Não' }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Nenhum caso de isenção cadastrado.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header">Arquivos do Processo</div>
        <div class="card-body">
            @forelse($processo->arquivos as $arquivo)
                <div class="border rounded p-3 mb-2 bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <div class="fw-semibold">{{ $arquivo->nome_exibicao }}</div>
                        <div class="small text-muted">{{ ucfirst($arquivo->tipo_arquivo) }}</div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ asset('storage/' . $arquivo->caminho_arquivo) }}" target="_blank" class="btn btn-sm btn-outline-primary">Abrir</a>
                        <form action="{{ route('sigeconcursos.processos.arquivos.destroy', $arquivo->id_arquivo) }}" method="POST"
                            onsubmit="return confirm('Remover este arquivo?');">
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
@endsection