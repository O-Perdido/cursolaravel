@extends('layouts.main')

@section('title', 'Instituições de Ensino')

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
    @if(session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif
    <!-- Card de Filtro e Título -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body pb-2 pt-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    <i class="fas fa-file-alt me-2 text-primary"></i>
                    Lista de Instituições de Ensino
                </h4>
                <div>
                    <a href="{{ route('escolas.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Adicionar Instituição de Ensino
                    </a>
                </div>
            </div>
            <form method="GET" action="{{ route('escolas.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-10">
                        <div class="row g-2">
                            <!-- Filtros -->
                            <!-- Filtro por Nome da Instituição de Ensino -->
                            <div class="col-md-3">
                                <label for="nome_empresa" class="form-label mb-1">Filtrar por Nome</label>
                                <input type="text" name="nome_escola" id="nome_escola" class="form-control form-control-sm"
                                    value="{{ request('nome_escola') }}" placeholder="Nome">
                            </div>
                            <!-- Filtro por CNPJ -->
                            <div class="col-md-3">
                                <label for="cnpj" class="form-label mb-1">Filtrar por CNPJ</label>
                                <input type="text" name="cnpj" id="cnpj" class="form-control form-control-sm"
                                    value="{{ request('cnpj') }}" placeholder="CNPJ">
                            </div>
                            <div class="col-md-2 d-flex flex-column align-items-end justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                            </div>
                            <div class="col-md-2 d-flex flex-column align-items-end justify-content-end gap-2">
                                <a href="{{ route('escolas.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="fas fa-eraser"></i> Limpar
                                </a>
                            </div>
                            <!-- Checkbox para ordenar pelo estagiarios mais recentes -->
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ordem_cadastro"
                                        id="ordem_cadastro" value="1" {{ request('ordem_cadastro') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ordem_cadastro">
                                        Ordenar por Mais Recentes
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- FIM DO CARD DE FILTRO E TÍTULO -->

    <!-- Total de escolas -->
    <div class="mb-2" style="font-weight: bold; font-size: 1.1em;">
        Total de Instituições de Ensino: {{ method_exists($escolas, 'total') ? $escolas->total() : $escolas->count() }}
    </div>

    @if (method_exists($escolas, 'links'))
        <!-- Paginação (topo) -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($escolas->total() > 0)
                        Mostrando {{ $escolas->firstItem() }}–{{ $escolas->lastItem() }} de {{ $escolas->total() }}
                    @else
                        Nenhum registro encontrado
                    @endif
                </span>
                @php $pp = request('per_page', '25'); @endphp
                <select id="perPageSelector" class="form-select form-select-sm per-page-selector"
                    onchange="changePerPage(this.value)"
                    style="display: inline-block; width: auto; margin-left: 10px; font-size: 0.875rem;">
                    <option value="25" {{ $pp == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $pp == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $pp == '100' ? 'selected' : '' }}>100</option>
                    <option value="200" {{ $pp == '200' ? 'selected' : '' }}>200</option>
                    <option value="all" {{ $pp == 'all' ? 'selected' : '' }}>Tudo</option>
                </select>
            </div>
            <div>
                {{ $escolas->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <div style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CNPJ</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody style="vertical-align: middle;">
                @foreach ($escolas as $escola)
                    <tr>
                        <td style="width: 350px;">{{ $escola->nome_escola }}</td>
                        <td style="max-width: 200px;">
                            {{ preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $escola->numero_cnpj) }}
                        </td>
                        <td>
                            {{ preg_replace('/^(\d{2})(\d{4,5})(\d{4})$/', '($1) $2-$3', preg_replace('/\D/', '', $escola->numero_telefone)) }}
                        </td>
                        <td style="max-width: 200px;">{{ $escola->email }}</td>
                        <td style="width: 250px;">
                            <a href="{{ route('escolas.show', $escola->id_escola) }}" class="btn btn-sm btn-info">Detalhes</a>
                            <a href="{{ route('escolas.edit', $escola->id_escola) }}" class="btn btn-sm btn-primary">Editar</a>
                            <form action="{{ route('escolas.destroy', $escola->id_escola) }}" method="POST"
                                style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if (method_exists($escolas, 'links'))
        <!-- Paginação (rodapé) -->
        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($escolas->total() > 0)
                        Mostrando {{ $escolas->firstItem() }}–{{ $escolas->lastItem() }} de {{ $escolas->total() }}
                    @else
                        Nenhum registro encontrado
                    @endif
                </span>
                @php $pp = request('per_page', '25'); @endphp
                <select id="perPageSelectorBottom" class="form-select form-select-sm per-page-selector"
                    onchange="changePerPage(this.value)"
                    style="display: inline-block; width: auto; margin-left: 10px; font-size: 0.875rem;">
                    <option value="25" {{ $pp == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $pp == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $pp == '100' ? 'selected' : '' }}>100</option>
                    <option value="200" {{ $pp == '200' ? 'selected' : '' }}>200</option>
                    <option value="all" {{ $pp == 'all' ? 'selected' : '' }}>Tudo</option>
                </select>
            </div>
            <div>
                {{ $escolas->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <script>
        // Função para alterar itens por página
        function changePerPage(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.delete('page'); // Reset para página 1
            window.location.href = url.toString();
        }
    </script>

@endsection