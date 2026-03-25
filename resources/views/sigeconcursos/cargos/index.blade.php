@extends('layouts.main')

@section('title', 'SIGE Concursos | Cargos')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-3 shadow-sm">
        <div class="card-body pb-2 pt-3">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h4 class="mb-0">
                    <i class="fa-solid fa-briefcase me-2 text-primary"></i>
                    Cargos
                </h4>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('sigeconcursos.cargos.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Novo Cargo
                    </a>
                    <a href="{{ route('sigeconcursos.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left"></i> Voltar ao dashboard
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('sigeconcursos.cargos.index') }}">
                <div class="row align-items-end g-2">
                    <div class="col-md-4">
                        <label for="nome_cargo" class="form-label mb-1">Filtrar por Cargo</label>
                        <input type="text" name="nome_cargo" id="nome_cargo" class="form-control form-control-sm"
                            value="{{ request('nome_cargo') }}" placeholder="Nome do cargo">
                    </div>

                    <div class="col-md-4">
                        <label for="escolaridade_minima" class="form-label mb-1">Filtrar por Escolaridade</label>
                        <input type="text" name="escolaridade_minima" id="escolaridade_minima"
                            class="form-control form-control-sm" value="{{ request('escolaridade_minima') }}"
                            placeholder="Escolaridade mínima">
                    </div>

                    <div class="col-md-2">
                        <label for="ativo" class="form-label mb-1">Situação</label>
                        <select name="ativo" id="ativo" class="form-select form-select-sm">
                            <option value="">Todas</option>
                            <option value="1" {{ request('ativo') === '1' ? 'selected' : '' }}>Ativos</option>
                            <option value="0" {{ request('ativo') === '0' ? 'selected' : '' }}>Inativos</option>
                        </select>
                    </div>

                    <div class="col-md-1 d-grid">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>

                    <div class="col-md-1 d-grid">
                        <a href="{{ route('sigeconcursos.cargos.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-eraser"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="mb-2" style="font-weight: bold; font-size: 1.1em;">
        Total de cargos: {{ method_exists($cargos, 'total') ? $cargos->total() : $cargos->count() }}
    </div>

    @if (method_exists($cargos, 'links'))
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($cargos->total() > 0)
                        Mostrando {{ $cargos->firstItem() }}–{{ $cargos->lastItem() }} de {{ $cargos->total() }}
                    @else
                        Nenhum registro encontrado
                    @endif
                </span>
                @php $pp = request('per_page', '25'); @endphp
                <select class="form-select form-select-sm" onchange="changePerPage(this.value)"
                    style="display: inline-block; width: auto; margin-left: 10px; font-size: 0.875rem;">
                    <option value="25" {{ $pp == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $pp == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $pp == '100' ? 'selected' : '' }}>100</option>
                    <option value="200" {{ $pp == '200' ? 'selected' : '' }}>200</option>
                    <option value="all" {{ $pp == 'all' ? 'selected' : '' }}>Tudo</option>
                </select>
            </div>
            <div>
                {{ $cargos->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <div style="max-height: 520px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Cargo</th>
                    <th>Escolaridade Mínima</th>
                    <th>Situação</th>
                    <th style="width: 220px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cargos as $cargo)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $cargo->nome_cargo }}</div>
                            <div class="text-muted small">{{ $cargo->descricao ?: 'Sem descrição cadastrada.' }}</div>
                        </td>
                        <td>{{ $cargo->escolaridade_minima ?: 'Não informada' }}</td>
                        <td>
                            <span class="badge {{ $cargo->ativo ? 'bg-success' : 'bg-secondary' }}">
                                {{ $cargo->ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('sigeconcursos.cargos.edit', $cargo->id_cargo) }}" class="btn btn-sm btn-primary">
                                Editar
                            </a>
                            <form action="{{ route('sigeconcursos.cargos.destroy', $cargo->id_cargo) }}" method="POST"
                                style="display:inline-block" onsubmit="return confirm('Confirma a exclusão deste cargo?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Nenhum cargo encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (method_exists($cargos, 'links'))
        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="text-muted small">
                @if($cargos->total() > 0)
                    Mostrando {{ $cargos->firstItem() }}–{{ $cargos->lastItem() }} de {{ $cargos->total() }}
                @else
                    Nenhum registro encontrado
                @endif
            </div>
            <div>
                {{ $cargos->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <script>
        function changePerPage(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
    </script>
@endsection