@extends('layouts.main')

@section('title', 'Tipos de Chamados')

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
                    <i class="fas fa-tags me-2 text-primary"></i>
                    Tipos de Chamados
                </h4>
                <a href="{{ route('admin.tipos-chamados.create') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus me-1"></i> Adicionar Tipo
                </a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-3">
        <div class="card-body">
            @if($tipos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th>Status</th>
                                <th>Tipo</th>
                                <th>Ordem</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tipos as $tipo)
                                <tr>
                                    <td><strong>{{ $tipo->nome }}</strong></td>
                                    <td>{{ Str::limit($tipo->descricao, 50) }}</td>
                                    <td>
                                        @if($tipo->ativo)
                                            <span class="badge bg-success">Ativo</span>
                                        @else
                                            <span class="badge bg-secondary">Inativo</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tipo->sistema)
                                            <span class="badge bg-warning text-dark">Sistema</span>
                                        @else
                                            <span class="badge bg-info">Personalizado</span>
                                        @endif
                                    </td>
                                    <td>{{ $tipo->ordem }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.tipos-chamados.edit', $tipo->id_tipo_chamado) }}"
                                            class="btn btn-sm btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!$tipo->sistema)
                                            <form action="{{ route('admin.tipos-chamados.destroy', $tipo->id_tipo_chamado) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Tem certeza que deseja remover este tipo de chamado?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Remover">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i>
                    Nenhum tipo de chamado cadastrado.
                </div>
            @endif
        </div>
    </div>

@endsection