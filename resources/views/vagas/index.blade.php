@extends('layouts.main')

@section('title', 'Vagas de Estágio')

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Card de Filtro e Título -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body pb-2 pt-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    <i class="fas fa-briefcase me-2 text-primary"></i>
                    Vagas de Estágio
                </h4>
                <a href="{{ route('vagas.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus me-1"></i> Adicionar Vaga
                </a>
            </div>
            <form method="GET" action="{{ route('vagas.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-10">
                        <div class="row g-2">
                            <!-- Filtro por Status -->
                            <div class="col-md-3">
                                <label for="status" class="form-label mb-1">Filtrar por Status</label>
                                <select name="status" id="status" class="form-select form-select-sm">
                                    <option value="">Todas</option>
                                    <option value="disponivel" {{ request('status') == 'disponivel' ? 'selected' : '' }}>
                                        Disponível</option>
                                    <option value="preenchida" {{ request('status') == 'preenchida' ? 'selected' : '' }}>
                                        Preenchida</option>
                                    <option value="expirada" {{ request('status') == 'expirada' ? 'selected' : '' }}>Expirada
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex flex-column align-items-end justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                            </div>
                            <div class="col-md-2 d-flex flex-column align-items-end justify-content-end gap-2">
                                <a href="{{ route('vagas.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="fas fa-eraser"></i> Limpar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- FIM DO CARD DE FILTRO E TÍTULO -->

    <!-- Total de vagas -->
    <div class="mb-2">
        <span class="text-muted">Total de vagas: <strong>{{ $vagas->total() }}</strong></span>
    </div>

    <!-- Tabela de Vagas -->
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Número</th>
                    <th>Empresa</th>
                    <th>Local</th>
                    <th>Atividades</th>
                    <th>Orientador</th>
                    <th>Período</th>
                    <th>Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vagas as $vaga)
                    <tr>
                        <td><strong>{{ $vaga->numero_vaga }}</strong></td>
                        <td>{{ $vaga->empresa->nome_empresa ?? '-' }}</td>
                        <td>{{ $vaga->local->descricao ?? '-' }}</td>
                        <td>{{ Str::limit($vaga->atividades, 50) }}</td>
                        <td>{{ $vaga->nome_orientador }}</td>
                        <td>
                            <small>
                                {{ \Carbon\Carbon::parse($vaga->data_inicio)->format('d/m/Y') }} -
                                {{ \Carbon\Carbon::parse($vaga->data_termino)->format('d/m/Y') }}
                            </small>
                        </td>
                        <td>
                            @if($vaga->status == 'disponivel')
                                <span class="badge bg-success">Disponível</span>
                            @elseif($vaga->status == 'preenchida')
                                <span class="badge bg-primary">Preenchida</span>
                            @else
                                <span class="badge bg-danger">Expirada</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('vagas.edit', $vaga->id_vaga) }}" class="btn btn-warning btn-sm"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if((Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador') && $vaga->status == 'disponivel' && !$vaga->fk_id_termo)
                                    <a href="{{ route('termos.create', ['empresa_id' => $vaga->fk_id_empresa, 'vaga_id' => $vaga->id_vaga]) }}"
                                        class="btn btn-info btn-sm" title="Preencher Vaga (gerar termo)">
                                        <i class="fas fa-file-pen"></i>
                                    </a>
                                @endif
                                @if(!$vaga->fk_id_termo)
                                    <form action="{{ route('vagas.destroy', $vaga->id_vaga) }}" method="POST"
                                        style="display:inline-block" onsubmit="return confirm('Confirma a exclusão desta vaga?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-secondary btn-sm"
                                        title="Vaga vinculada - não pode ser excluída" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            Nenhuma vaga encontrada
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div class="d-flex justify-content-center mt-3">
        {{ $vagas->links() }}
    </div>

@endsection