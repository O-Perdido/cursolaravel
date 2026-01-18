@extends('layouts.main')

@section('title', 'Processos Seletivos Disponíveis')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('welcome.estagiario') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
            <h4 class="mb-0">Processos Seletivos Disponíveis</h4>
        </div>
        <p class="text-muted small mb-0">Visualize e inscreva-se com facilidade no seu celular ou desktop.</p>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-9 col-lg-10">
                    <input type="text" name="search" placeholder="Buscar por empresa..." class="form-control" value="{{ request('search') }}">
                </div>
                <div class="col-12 col-md-3 col-lg-2 d-grid">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($processos->count() > 0)
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
            @foreach($processos as $processo)
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column gap-3">
                            <div class="d-flex align-items-center">
                                @if($processo->empresa->logo_empresa)
                                    <img src="{{ Storage::url($processo->empresa->logo_empresa) }}" alt="{{ $processo->empresa->nome_empresa }}" class="rounded-circle me-2" style="width: 44px; height: 44px; object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 44px; height: 44px;">
                                        <i class="fas fa-building text-muted"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <p class="mb-0 text-muted small">{{ $processo->empresa->nome_empresa }}</p>
                                    <span class="badge bg-light text-dark">{{ $processo->numero_processo }}</span>
                                </div>
                            </div>

                            <div class="flex-grow-1">
                                <h6 class="mb-2">{{ Str::limit($processo->titulo, 70) }}</h6>
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <span class="badge @switch($processo->status) @case('aberto') bg-success @break @case('inscricoes') bg-info @break @case('encerrado') bg-warning @break @case('finalizado') bg-dark @break @default bg-secondary @endswitch">{{ ucfirst($processo->status) }}</span>
                                </div>
                                @if($processo->data_fechamento_inscricoes)
                                    <p class="text-muted small mb-1"><i class="fas fa-calendar me-1"></i>Inscrições até {{ $processo->data_fechamento_inscricoes->format('d/m/Y') }}</p>
                                @endif
                                @if($processo->cursos_destino && count($processo->cursos_destino) > 0)
                                    <p class="text-muted small mb-0"><i class="fas fa-graduation-cap me-1"></i>{{ count($processo->cursos_destino) }} curso(s)</p>
                                @endif
                            </div>

                            <div class="d-grid">
                                <a href="{{ route('processos-seletivos.detalhes', $processo->id_processo) }}" class="btn btn-primary">
                                    <i class="fas fa-eye me-1"></i> Ver Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info text-center py-4" role="alert">
            <i class="fas fa-info-circle me-2"></i> Nenhum processo seletivo disponível no momento.
        </div>
    @endif
</div>
@endsection
