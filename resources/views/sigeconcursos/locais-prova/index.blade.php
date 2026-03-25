@extends('layouts.main')

@section('title', 'SIGE Concursos | Locais de Prova')

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
                    <i class="fa-solid fa-school me-2 text-primary"></i>
                    Locais de Prova
                </h4>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('sigeconcursos.locais-prova.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Novo Local
                    </a>
                    <a href="{{ route('sigeconcursos.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left"></i> Voltar ao dashboard
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('sigeconcursos.locais-prova.index') }}">
                <div class="row align-items-end g-2">
                    <div class="col-md-4">
                        <label for="nome_local" class="form-label mb-1">Filtrar por Local</label>
                        <input type="text" name="nome_local" id="nome_local" class="form-control form-control-sm"
                            value="{{ request('nome_local') }}" placeholder="Nome do local">
                    </div>

                    <div class="col-md-3">
                        <label for="cidade" class="form-label mb-1">Filtrar por Cidade</label>
                        <input type="text" name="cidade" id="cidade" class="form-control form-control-sm"
                            value="{{ request('cidade') }}" placeholder="Cidade">
                    </div>

                    <div class="col-md-2">
                        <label for="ativo" class="form-label mb-1">Situação</label>
                        <select name="ativo" id="ativo" class="form-select form-select-sm">
                            <option value="">Todas</option>
                            <option value="1" {{ request('ativo') === '1' ? 'selected' : '' }}>Ativos</option>
                            <option value="0" {{ request('ativo') === '0' ? 'selected' : '' }}>Inativos</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="ordem_cadastro" id="ordem_cadastro"
                                value="1" {{ request('ordem_cadastro') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="ordem_cadastro">Recentes</label>
                        </div>
                    </div>

                    <div class="col-md-1 d-grid">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="mb-2" style="font-weight: bold; font-size: 1.1em;">
        Total de locais: {{ method_exists($locais, 'total') ? $locais->total() : $locais->count() }}
    </div>

    @if (method_exists($locais, 'links'))
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($locais->total() > 0)
                        Mostrando {{ $locais->firstItem() }}–{{ $locais->lastItem() }} de {{ $locais->total() }}
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
                {{ $locais->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <div style="max-height: 520px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Local</th>
                    <th>Cidade/UF</th>
                    <th>Salas</th>
                    <th>Situação</th>
                    <th style="width: 250px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($locais as $local)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $local->nome_local }}</div>
                            <div class="text-muted small">{{ $local->endereco }}, {{ $local->numero_endereco }} -
                                {{ $local->bairro }}</div>
                        </td>
                        <td>{{ $local->cidade?->nm_cidade }} / {{ $local->cidade?->estado?->uf_estado }}</td>
                        <td>{{ $local->salas->count() }}</td>
                        <td>
                            <span class="badge {{ $local->ativo ? 'bg-success' : 'bg-secondary' }}">
                                {{ $local->ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('sigeconcursos.locais-prova.show', $local->id_local_prova) }}"
                                class="btn btn-sm btn-info">Detalhes</a>
                            <a href="{{ route('sigeconcursos.locais-prova.edit', $local->id_local_prova) }}"
                                class="btn btn-sm btn-primary">Editar</a>
                            <form action="{{ route('sigeconcursos.locais-prova.destroy', $local->id_local_prova) }}"
                                method="POST" style="display:inline-block"
                                onsubmit="return confirm('Confirma a exclusão deste local de prova?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Nenhum local de prova encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (method_exists($locais, 'links'))
        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="text-muted small">
                @if($locais->total() > 0)
                    Mostrando {{ $locais->firstItem() }}–{{ $locais->lastItem() }} de {{ $locais->total() }}
                @else
                    Nenhum registro encontrado
                @endif
            </div>
            <div>
                {{ $locais->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
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