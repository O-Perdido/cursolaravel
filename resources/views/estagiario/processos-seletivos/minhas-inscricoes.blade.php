@extends('layouts.main')

@section('title', 'Minhas Inscrições')

@section('content')
<div class="container-fluid py-3">
    <div class="mb-3">
        <h4 class="mb-1">Minhas Inscrições</h4>
        <p class="text-muted small mb-0">Acompanhe o status das participações em processos seletivos.</p>
    </div>

    @if($inscricoes->count() > 0)
        <div class="row row-cols-1 row-cols-md-2 g-3">
            @foreach($inscricoes as $inscricao)
                <div class="col">
                    <div class="card shadow-sm h-100">
                        <div class="card-body d-flex flex-column gap-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $inscricao->processo->titulo }}</h6>
                                    <p class="text-muted small mb-0">{{ $inscricao->processo->empresa->nome_empresa }}</p>
                                </div>
                                <span class="badge @switch($inscricao->status_inscricao) @case('inscrito') bg-info @break @case('deferido') bg-success @break @case('indeferido') bg-danger @break @default bg-secondary @endswitch">{{ ucfirst($inscricao->status_inscricao) }}</span>
                            </div>

                            <div class="small text-muted">
                                <div class="mb-1"><i class="fas fa-hashtag me-1"></i>Processo {{ $inscricao->processo->numero_processo }}</div>
                                <div class="mb-1"><i class="fas fa-calendar me-1"></i>Inscrito em {{ $inscricao->created_at->format('d/m/Y H:i') }}</div>
                                @if($inscricao->processo->data_fechamento_inscricoes)
                                    <div><i class="fas fa-hourglass-end me-1"></i>Até {{ $inscricao->processo->data_fechamento_inscricoes->format('d/m/Y H:i') }}</div>
                                @endif
                            </div>

                            <div class="alert alert-info mb-0 py-2 px-3" role="alert">
                                <small>
                                    @switch($inscricao->status_inscricao)
                                        @case('inscrito')
                                            <i class="fas fa-info-circle me-1"></i>Inscrição registrada. Aguarde a divulgação dos resultados.
                                            @break
                                        @case('deferido')
                                            <i class="fas fa-check-circle text-success me-1"></i>Parabéns! Sua inscrição foi deferida.
                                            @break
                                        @case('indeferido')
                                            <i class="fas fa-times-circle text-danger me-1"></i>Sua inscrição foi indeferida.
                                            @break
                                    @endswitch
                                </small>
                            </div>

                            <div class="d-flex gap-2 mt-auto">
                                <a href="{{ route('processos-seletivos.detalhes', $inscricao->processo->id_processo) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                    <i class="fas fa-eye me-1"></i> Detalhes
                                </a>
                                @if($inscricao->processo->resultados->count() > 0)
                                    <a href="#" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#resultadoModal{{ $inscricao->id_inscricao }}">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($inscricao->processo->resultados->count() > 0)
                    <div class="modal fade" id="resultadoModal{{ $inscricao->id_inscricao }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Resultados - {{ $inscricao->processo->titulo }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="list-group list-group-flush">
                                        @foreach($inscricao->processo->resultados as $resultado)
                                            <a href="{{ Storage::url($resultado->arquivo_resultado) }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-0">{{ $resultado->numero_resultado }}</h6>
                                                    <small class="text-muted">Publicado em {{ $resultado->created_at->format('d/m/Y') }}</small>
                                                </div>
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center py-4" role="alert">
            <i class="fas fa-inbox fa-2x mb-2"></i>
            <p class="mb-2">Você não possui inscrições em nenhum processo seletivo.</p>
            <a href="{{ route('processos-seletivos.abertos') }}" class="btn btn-primary">
                <i class="fas fa-search me-1"></i> Procurar Processos
            </a>
        </div>
    @endif
</div>
@endsection
