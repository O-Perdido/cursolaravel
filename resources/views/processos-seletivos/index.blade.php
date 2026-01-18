@extends('layouts.main')

@section('title', 'Processos Seletivos')

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
                    <i class="fas fa-graduation-cap me-2 text-primary"></i>
                    Processos Seletivos
                </h4>
                @if(Auth::user()->nivel !== 'empresa' || auth()->user()->fk_id_empresa)
                    <a href="{{ route('processos-seletivos.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Novo Processo
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card shadow-sm mt-3">
        <div class="card-body">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <label for="status" class="form-label small">Status</label>
                    <select name="status" id="status" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="rascunho" {{ request('status') === 'rascunho' ? 'selected' : '' }}>Rascunho</option>
                        <option value="aberto" {{ request('status') === 'aberto' ? 'selected' : '' }}>Aberto</option>
                        <option value="inscricoes" {{ request('status') === 'inscricoes' ? 'selected' : '' }}>Inscrições</option>
                        <option value="encerrado" {{ request('status') === 'encerrado' ? 'selected' : '' }}>Encerrado</option>
                        <option value="finalizado" {{ request('status') === 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                    </select>
                </div>

                @if(Auth::user()->nivel !== 'empresa')
                    <div class="col-md-4">
                        <label for="empresa" class="form-label small">Empresa</label>
                        <select name="empresa" id="empresa" class="form-select form-select-sm">
                            <option value="">Todas</option>
                            @foreach($empresas as $empresa)
                                <option value="{{ $empresa->id_empresa }}" {{ request('empresa') == $empresa->id_empresa ? 'selected' : '' }}>
                                    {{ $empresa->nome_empresa }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="col-md-@if(Auth::user()->nivel !== 'empresa') 4 @else 6 @endif d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search me-1"></i> Filtrar
                    </button>
                    <a href="{{ route('processos-seletivos.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-redo me-1"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Mensagens de sucesso/erro --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Erro!</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Tabela de processos --}}
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Número</th>
                        <th>Título</th>
                        <th>Empresa</th>
                        <th>Status</th>
                        <th>Inscrições</th>
                        <th>Data de Abertura</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($processos as $processo)
                        <tr>
                            <td>
                                <strong>{{ $processo->numero_processo }}</strong>
                            </td>
                            <td>{{ Str::limit($processo->titulo, 50) }}</td>
                            <td>{{ $processo->empresa->nome_empresa ?? 'N/A' }}</td>
                            <td>
                                <span class="badge 
                                    @switch($processo->status)
                                        @case('rascunho') bg-secondary @break
                                        @case('aberto') bg-success @break
                                        @case('inscricoes') bg-info @break
                                        @case('encerrado') bg-warning @break
                                        @case('finalizado') bg-dark @break
                                    @endswitch
                                ">
                                    {{ ucfirst($processo->status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $processo->inscricoesCount() }}</span>
                            </td>
                            <td>
                                @if($processo->data_abertura)
                                    {{ $processo->data_abertura->format('d/m/Y H:i') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('processos-seletivos.edit', $processo->id_processo) }}" class="btn btn-outline-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('processos-seletivos.inscricoes', $processo->id_processo) }}" class="btn btn-outline-info" title="Ver Inscrições">
                                        <i class="fas fa-users"></i>
                                    </a>
                                    <a href="{{ route('processos-seletivos.resultados', $processo->id_processo) }}" class="btn btn-outline-success" title="Resultados">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                    <form action="{{ route('processos-seletivos.destroy', $processo->id_processo) }}" method="POST" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja deletar?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Deletar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Nenhum processo seletivo encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginação --}}
    <div class="mt-4">
        {{ $processos->links() }}
    </div>
</div>
@endsection
