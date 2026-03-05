@extends('layouts.main')

@section('title', 'Meus Chamados')

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body pb-2 pt-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    <i class="fas fa-headset me-2 text-primary"></i>
                    {{ Auth::user()->nivel === 'empresa' ? 'Meus Chamados' : 'Chamados' }}
                </h4>
                @if(Auth::user()->nivel === 'empresa')
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                        data-bs-target="#modalNovoChamado">
                        <i class="fas fa-plus me-1"></i> Abrir Chamado
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-3">
        <div class="card-body">
            @if($chamados->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Protocolo</th>
                                <th>Tipo</th>
                                @if(Auth::user()->nivel !== 'empresa')
                                    <th>Empresa</th>
                                @endif
                                <th>Assunto</th>
                                <th>Status</th>
                                <th>Data</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($chamados as $chamado)
                                <tr>
                                    <td>
                                        <strong class="text-primary">{{ $chamado->protocolo }}</strong>
                                        @if(($chamado->mensagens_nao_lidas_count ?? 0) > 0)
                                            <span class="badge bg-danger ms-1" title="Mensagens não lidas">
                                                <i class="fas fa-comment-dots me-1"></i>{{ $chamado->mensagens_nao_lidas_count }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $chamado->tipoChamado->nome }}</span>
                                    </td>
                                    @if(Auth::user()->nivel !== 'empresa')
                                        <td>{{ $chamado->empresa->nome_fantasia }}</td>
                                    @endif
                                    <td>
                                        @if($chamado->isRescisao() && $chamado->termo)
                                            <small class="text-muted">Termo:</small> {{ $chamado->termo->numero_termo }}<br>
                                            <small class="text-muted">Estagiário:</small> {{ $chamado->termo->estagiario->nome }}
                                        @elseif($chamado->isAlteracao() && $chamado->termo)
                                            <small class="text-muted">Termo:</small> {{ $chamado->termo->numero_termo }}<br>
                                            <small class="text-muted">Estagiário:</small> {{ $chamado->termo->estagiario->nome }}
                                        @else
                                            {{ Str::limit($chamado->titulo, 50) }}
                                        @endif
                                    </td>
                                    <td>{!! $chamado->getStatusBadge() !!}</td>
                                    <td>
                                        <small>{{ $chamado->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('chamados.show', $chamado->id_chamado) }}" class="btn btn-sm btn-primary"
                                            title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(Auth::user()->nivel === 'empresa' && !in_array($chamado->status, ['concluido', 'cancelado']))
                                            <form action="{{ route('chamados.cancelar', $chamado->id_chamado) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Tem certeza que deseja cancelar este chamado?');">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Cancelar Chamado">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $chamados->links() }}
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    Nenhum chamado encontrado.
                    @if(Auth::user()->nivel === 'empresa')
                        Clique no botão "Abrir Chamado" para criar seu primeiro chamado.
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if(Auth::user()->nivel === 'empresa')
        @include('chamados.partials.modal-novo-chamado')
    @endif

@endsection