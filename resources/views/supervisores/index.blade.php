@extends('layouts.main')

@section('title', 'Supervisores')

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

    @php
        $isEmpresa = (auth()->user()->nivel ?? '') === 'empresa';
        $indexRoute = $isEmpresa ? 'empresa.supervisores.index' : 'supervisores.index';
        $createRoute = $isEmpresa ? 'empresa.supervisores.create' : 'supervisor.create';
        $pp = request('per_page', '25');
        $backRoute = $isEmpresa ? 'welcome.empresa' : 'welcome.admin';
    @endphp

    <!-- Card de Filtro e Título -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body pb-2 pt-3">
            <div class="d-flex justify-content-end mb-2">
                <a href="{{ route($backRoute) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    <i class="fas fa-file-alt me-2 text-primary"></i>
                    Lista de Supervisores
                </h4>
                <div>
                    <a href="{{ route($createRoute) }}" class="btn btn-success btn-sm" target="_blank">
                        <i class="fas fa-plus me-1"></i> Adicionar Supervisor
                    </a>
                </div>
            </div>
            <form method="GET" action="{{ route($indexRoute) }}">
                <div class="row align-items-end">
                    <div class="col-md-10">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label for="supervisor" class="form-label mb-1">Filtrar por Nome</label>
                                <input type="text" name="nome_supervisor" id="nome_supervisor"
                                    class="form-control form-control-sm" value="{{ request('nome_supervisor') }}"
                                    placeholder="Nome">
                            </div>
                            @if(!$isEmpresa)
                                <div class="col-md-3">
                                    <label for="empresa_search" class="form-label mb-1">Filtrar por Unidade Concedente</label>
                                    <input type="text" class="form-control form-control-sm" id="empresa_search"
                                        placeholder="Digite para buscar..." autocomplete="off"
                                        value="{{ $empresas->firstWhere('id_empresa', request('empresa'))?->nome_empresa }}">
                                    <div id="empresa_select_wrapper"
                                        style="display:none; position:absolute; z-index:1050; resize:horizontal; overflow:auto; border:1px solid #ced4da; min-width:400px;">
                                        <select name="empresa" id="empresa" class="form-select form-select-sm" size="5"
                                            style="width:100%; border:none; margin:0; padding:0;">
                                            <option value="">Todas</option>
                                            @foreach ($empresas as $empresa)
                                                <option value="{{ $empresa->id_empresa }}" {{ request('empresa') == $empresa->id_empresa ? 'selected' : '' }}>
                                                    {{ $empresa->nome_empresa }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-3">
                                <label for="cpf" class="form-label mb-1">Filtrar por CPF</label>
                                <input type="text" name="cpf" id="cpf" class="form-control form-control-sm"
                                    value="{{ request('cpf') }}" placeholder="CPF">
                            </div>
                        </div>
                        <div class="row g-2 mt-2">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ordem_cadastro"
                                        id="ordem_cadastro" value="1" {{ request('ordem_cadastro') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="ordem_cadastro">
                                        Ordenar por Mais Recentes
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex flex-column align-items-end justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                            </div>
                            <div class="col-md-2 d-flex flex-column align-items-end justify-content-end gap-2">
                                <a href="{{ route($indexRoute) }}" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="fas fa-eraser"></i> Limpar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="mb-2" style="font-weight: bold; font-size: 1.1em;">
        Total de supervisores: {{ method_exists($supervisores, 'total') ? $supervisores->total() : $supervisores->count() }}
    </div>

    @if (method_exists($supervisores, 'links'))
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($supervisores->total() > 0)
                        Mostrando {{ $supervisores->firstItem() }}–{{ $supervisores->lastItem() }} de {{ $supervisores->total() }}
                    @else
                        Nenhum registro encontrado
                    @endif
                </span>
                <select id="perPageSelectorSupervisores" class="form-select form-select-sm per-page-selector"
                    onchange="changePerPage(this.value)">
                    <option value="25" {{ $pp == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $pp == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $pp == '100' ? 'selected' : '' }}>100</option>
                    <option value="200" {{ $pp == '200' ? 'selected' : '' }}>200</option>
                    <option value="all" {{ $pp == 'all' ? 'selected' : '' }}>Tudo</option>
                </select>
            </div>
            <div>
                {{ $supervisores->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <div style="max-height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
        <table class="table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Empresa</th>
                    <th>Área de Formação</th>
                    <th>Tempo de Experiência</th>
                    <th>CPF</th>
                    <th>Celular</th>
                    <th>E-mail</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($supervisores as $supervisor)
                        <tr>
                            <td style="max-width: 300px;">{{ $supervisor->nome_supervisor }}</td>
                            <td style="max-width: 250px;">{{ $supervisor->empresa->nome_empresa ?? 'N/A' }}</td>
                            <td style="max-width: 200px;">{{ $supervisor->area_formacao }}</td>
                            <td style="max-width: 100px;">{{ $supervisor->tempo_experiencia }}</td>
                            <td>{{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $supervisor->cpf_supervisor) }}
                            </td>
                            <td>
                                {{ $supervisor->celular_supervisor
                    ? preg_replace('/(\d{2})(\d{4,5})(\d{4})/', '($1) $2-$3', $supervisor->celular_supervisor)
                    : '—' }}
                            </td>
                            <td style="max-width: 220px;">
                                {{ $supervisor->email_supervisor ?: '—' }}
                            </td>
                            <td style="width: 150px;">
                                <a href="{{ route($isEmpresa ? 'empresa.supervisores.edit' : 'supervisores.edit', $supervisor->id_supervisor) }}"
                                    class="btn btn-sm btn-primary">Editar</a>
                                <form
                                    action="{{ route($isEmpresa ? 'empresa.supervisores.destroy' : 'supervisores.destroy', $supervisor->id_supervisor) }}"
                                    method="POST" style="display:inline-block;">
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

    @if (method_exists($supervisores, 'links'))
        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($supervisores->total() > 0)
                        Mostrando {{ $supervisores->firstItem() }}–{{ $supervisores->lastItem() }} de {{ $supervisores->total() }}
                    @else
                        Nenhum registro encontrado
                    @endif
                </span>
                <select id="perPageSelectorSupervisoresBottom" class="form-select form-select-sm per-page-selector"
                    onchange="changePerPage(this.value)">
                    <option value="25" {{ $pp == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $pp == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $pp == '100' ? 'selected' : '' }}>100</option>
                    <option value="200" {{ $pp == '200' ? 'selected' : '' }}>200</option>
                    <option value="all" {{ $pp == 'all' ? 'selected' : '' }}>Tudo</option>
                </select>
            </div>
            <div>
                {{ $supervisores->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <script>
        function changePerPage(perPage) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', perPage);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('empresa_search');
            const select = document.getElementById('empresa');
            const wrapper = document.getElementById('empresa_select_wrapper');
            if (!searchInput || !select) return;
            const options = Array.from(select.options);

            searchInput.addEventListener('focus', function () {
                if (wrapper) {
                    wrapper.style.display = 'block';
                } else {
                    select.style.display = 'block';
                }
                setTimeout(() => searchInput.select(), 0);
            });

            searchInput.addEventListener('input', function () {
                const value = this.value.toLowerCase();
                select.innerHTML = '';
                options.forEach(option => {
                    if (option.text.toLowerCase().includes(value)) {
                        select.appendChild(option.cloneNode(true));
                    }
                });
                if (wrapper) {
                    wrapper.style.display = 'block';
                } else {
                    select.style.display = 'block';
                }
            });

            select.addEventListener('change', function () {
                const selected = select.options[select.selectedIndex];
                searchInput.value = selected.text;
                if (wrapper) {
                    wrapper.style.display = 'none';
                } else {
                    select.style.display = 'none';
                }
            });

            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !select.contains(e.target) && (!wrapper || !wrapper.contains(e.target))) {
                    if (wrapper) {
                        wrapper.style.display = 'none';
                    } else {
                        select.style.display = 'none';
                    }
                }
            });
        });
    </script>
@endsection