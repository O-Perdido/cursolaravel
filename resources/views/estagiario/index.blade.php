@extends('layouts.main')

@section('title', 'Estagiários')

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('delete'))
        <div class="alert alert-danger">
            {{ session('delete') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <!-- Card de Filtro e Título -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body pb-2 pt-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    <i class="fas fa-file-alt me-2 text-primary"></i>
                    Lista de Estagiários
                </h4>
                <div>
                    <a href="{{ route('estagiarios.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Adicionar Estagiário
                    </a>
                    <!--<button id="copyLinkButton" class="btn btn-primary btn-sm">Compartilhar Link</button>-->
                    <button id="copyLinkButtonTeste" class="btn btn-primary btn-sm">Compartilhar link teste</button>
                    <span id="copyMessage" style="display:none; margin-left: 10px;">Link copiado</span>
                    <span id="copyMessageTeste" style="display:none; margin-left: 10px;">Link copiado</span>
                </div>
                <script>
                            /*document.getElementById('copyLinkButton').addEventListener('click', function () {
                                var link = "{{ route('novo-estagiario-create') }}";
                    navigator.clipboard.writeText(link).then(function () {
                        var message = document.getElementById('copyMessage');
                        message.style.display = 'inline';
                        setTimeout(function () {
                            message.style.display = 'none';
                        }, 5000);
                    });
                            });*/

                    // Botão para copiar o link da versão AJAX (teste)
                    document.getElementById('copyLinkButtonTeste').addEventListener('click', function () {
                        var linkTeste = "{{ route('novo-estagiario-ajax-create') }}";
                        navigator.clipboard.writeText(linkTeste).then(function () {
                            var messageTeste = document.getElementById('copyMessageTeste');
                            messageTeste.style.display = 'inline';
                            setTimeout(function () {
                                messageTeste.style.display = 'none';
                            }, 5000);
                        });
                    });
                </script>
            </div>
            <form method="GET" action="{{ route('estagiarios.index') }}">
                <div class="row align-items-end">
                    <div class="col-md-10">
                        <div class="row g-2">
                            <!-- Filtros -->
                            <!-- Filtro por Nome do Estagiário -->
                            <div class="col-md-3">
                                <label for="estagiario" class="form-label mb-1">Filtrar por Nome</label>
                                <input type="text" name="nome_estagiario" id="nome_estagiario"
                                    class="form-control form-control-sm" value="{{ request('nome_estagiario') }}"
                                    placeholder="Nome">
                            </div>
                            <!-- Filtro por CPF -->
                            <div class="col-md-3">
                                <label for="cpf" class="form-label mb-1">Filtrar por CPF</label>
                                <input type="text" name="cpf" id="cpf" class="form-control form-control-sm"
                                    value="{{ request('cpf') }}" placeholder="CPF">
                            </div>
                            <div class="col-md-2 d-flex flex-column align-items-end justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                            </div>
                            <div class="col-md-2 d-flex flex-column align-items-end justify-content-end gap-2">
                                <a href="{{ route('estagiarios.index') }}" class="btn btn-outline-secondary btn-sm w-100">
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

    <!-- Total de estagiários -->
    <div class="mb-2" style="font-weight: bold; font-size: 1.1em;">
        Total de estagiários: {{ method_exists($estagiarios, 'total') ? $estagiarios->total() : $estagiarios->count() }}
    </div>

    @if (method_exists($estagiarios, 'links'))
        <!-- Paginação (topo) -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($estagiarios->total() > 0)
                        Mostrando {{ $estagiarios->firstItem() }}–{{ $estagiarios->lastItem() }} de {{ $estagiarios->total() }}
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
                {{ $estagiarios->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <div style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Email</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($estagiarios as $estagiario)
                    <tr>
                        <td>{{ $estagiario->nome_estagiario }}</td>
                        <td>{{ $estagiario->numero_cpf }}</td>
                        <td>{{ $estagiario->email }}</td>
                        <td>
                            <a href="{{ route('estagiario.show', $estagiario->id_estagiario) }}"
                                class="btn btn-sm btn-info">Detalhes</a>
                            <a href="{{ route('estagiarios.edit', $estagiario->id_estagiario) }}"
                                class="btn btn-sm btn-primary">Editar</a>
                            <a href="{{ route('termos.create', ['id_estagiario' => $estagiario->id_estagiario]) }}"
                                class="btn btn-sm btn-warning">Novo Termo</a>
                            <form action="{{ route('estagiario.destroy', $estagiario->id_estagiario) }}" method="POST"
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

    @if (method_exists($estagiarios, 'links'))
        <!-- Paginação (rodapé) -->
        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($estagiarios->total() > 0)
                        Mostrando {{ $estagiarios->firstItem() }}–{{ $estagiarios->lastItem() }} de {{ $estagiarios->total() }}
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
                {{ $estagiarios->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
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