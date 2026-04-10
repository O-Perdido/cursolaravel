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
                <div class="row align-items-end g-2">
                    <!-- Filtro por Unidade Concedente -->
                    @if (Auth::user()->nivel != 'empresa')
                        <div class="col-md-2">
                            <label for="empresa_search" class="form-label mb-1">Unidade Concedente</label>
                            <input type="text" class="form-control form-control-sm" id="empresa_search"
                                placeholder="Digite para buscar..." autocomplete="off"
                                value="{{ $empresas->firstWhere('id_empresa', request('empresa'))?->nome_empresa }}">
                            <div id="empresa_select_wrapper"
                                style="display:none; position:absolute; z-index:1050; resize:horizontal; overflow:auto; border:1px solid #ced4da; min-width:300px;">
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
                    <!-- Filtro por Status -->
                    <div class="col-md-2">
                        <label for="status" class="form-label mb-1">Status</label>
                        <select name="status" id="status" class="form-select form-select-sm">
                            <option value="">Todas</option>
                            <option value="disponivel" {{ request('status') == 'disponivel' ? 'selected' : '' }}>
                                Disponível</option>
                            <option value="preenchida" {{ request('status') == 'preenchida' ? 'selected' : '' }}>
                                Preenchida</option>
                            <option value="suspensa" {{ request('status') == 'suspensa' ? 'selected' : '' }}>
                                Suspensa</option>
                        </select>
                    </div>
                    <!-- Filtro por Data de Início -->
                    <div class="col-md-2">
                        <label for="data_inicio" class="form-label mb-1">Data Início do Termo</label>
                        <input type="date" class="form-control form-control-sm" id="data_inicio" name="data_inicio"
                            value="{{ request('data_inicio') }}">
                    </div>
                    <!-- Filtro por Data de Término -->
                    <div class="col-md-2">
                        <label for="data_termino" class="form-label mb-1">Data Término do Termo</label>
                        <input type="date" class="form-control form-control-sm" id="data_termino" name="data_termino"
                            value="{{ request('data_termino') }}">
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="com_candidaturas" name="com_candidaturas" {{ request('com_candidaturas') ? 'checked' : '' }}>
                            <label class="form-check-label" for="com_candidaturas">Somente vagas com candidaturas</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="com_estagiario_definido" name="com_estagiario_definido" {{ request('com_estagiario_definido') ? 'checked' : '' }}>
                            <label class="form-check-label" for="com_estagiario_definido">Somente com estagiário definido</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="termo_pendente" name="termo_pendente" {{ request('termo_pendente') ? 'checked' : '' }}>
                            <label class="form-check-label" for="termo_pendente">Somente termo pendente</label>
                        </div>
                    </div>
                    <!-- Botões -->
                    <div class="col-md-1 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="{{ route('vagas.index') }}" class="btn btn-outline-secondary btn-sm flex-grow-1">
                            <i class="fas fa-eraser"></i> Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- FIM DO CARD DE FILTRO E TÍTULO -->

    <!-- Total de vagas -->
    <div class="mb-2" style="font-weight: bold; font-size: 1.1em;">
        Total de vagas: {{ method_exists($vagas, 'total') ? $vagas->total() : $vagas->count() }}
    </div>

    @if (method_exists($vagas, 'links'))
        <!-- Paginação (topo) -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($vagas->total() > 0)
                        Mostrando {{ $vagas->firstItem() }}–{{ $vagas->lastItem() }} de {{ $vagas->total() }}
                    @else
                        Nenhum registro encontrado
                    @endif
                </span>
                @php $pp = request('per_page', '25'); @endphp
                <select id="perPageSelectorVagas" class="form-select form-select-sm per-page-selector"
                    onchange="changePerPage(this.value)" style="width: auto; margin-left: 10px;">
                    <option value="25" {{ $pp == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $pp == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $pp == '100' ? 'selected' : '' }}>100</option>
                    <option value="200" {{ $pp == '200' ? 'selected' : '' }}>200</option>
                    <option value="all" {{ $pp == 'all' ? 'selected' : '' }}>Tudo</option>
                </select>
            </div>
            <div>
                {{ $vagas->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <!-- Tabela de Vagas -->
    <div class="table-responsive">
        <table class="table table-hover table-striped align-middle">
            <thead class="table-light">
                <tr>
                    <th>Número</th>
                    <th>Título</th>
                    @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                        <th>Empresa</th>
                    @endif
                    @if (Auth::user()->nivel == 'empresa')
                        <th>Departamento</th>
                    @endif
                    <th>Estagiário</th>
                    <th>Período</th>
                    <th>Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vagas as $vaga)
                    <tr>
                        <td><strong>{{ $vaga->numero_vaga }}</strong></td>
                        <td>{{ $vaga->titulo_vaga ?? '-' }}</td>
                        @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                            <td>{{ $vaga->empresa->nome_empresa ?? '-' }}</td>
                        @endif
                        @if (Auth::user()->nivel == 'empresa')
                            <td>{{ $vaga->local->descricao ?? '-' }}</td>
                        @endif
                        <td>
                            <div>{{ $vaga->termo->estagiario->nome_estagiario ?? $vaga->estagiarioDefinido->nome_estagiario ?? $vaga->nome_estagiario ?? 'Não vinculado' }}</div>
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                @if($vaga->candidaturas_count > 0)
                                    <span class="badge bg-light text-dark">{{ $vaga->candidaturas_count }} candidatura(s)</span>
                                @endif
                                @if($vaga->divulgada_publicamente)
                                    <span class="badge bg-info text-dark">Divulgada</span>
                                @endif
                                @if($vaga->fk_id_estagiario_definido || $vaga->tem_estagiario_definido)
                                    <span class="badge bg-warning text-dark">Estagiário definido</span>
                                @endif
                                @if($vaga->tem_termo_pendente)
                                    <span class="badge bg-danger">Termo pendente</span>
                                @endif
                            </div>
                            @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                                @if($vaga->fk_id_termo)
                                    <a href="{{ route('estagiario.show', $vaga->termo->estagiario->id_estagiario) }}" target="_blank"
                                        class="ml-1" title="Ver detalhes do estagiário">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                @endif
                            @endif
                        </td>
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
                            @elseif($vaga->status == 'suspensa')
                                <span class="badge bg-secondary">Suspensa</span>
                            @else
                                <span class="badge bg-secondary">Suspensa</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center align-items-center">
                                <a href="{{ route('vagas.edit', $vaga->id_vaga) }}" class="btn btn-warning btn-sm"
                                    title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <a href="{{ route('vagas.candidaturas.index', $vaga->id_vaga) }}" class="btn btn-outline-primary btn-sm"
                                    title="Ver candidaturas">
                                    <i class="fas fa-users"></i>
                                </a>

                                <!-- Botão atalho para o termo vinculado -->
                                @if($vaga->fk_id_termo)
                                    <a href="{{ route('termos.show', $vaga->fk_id_termo) }}" class="btn btn-info btn-sm"
                                        title="Ver Termo Vinculado">
                                        <i class="fas fa-file-contract"></i>
                                    </a>
                                @endif

                                <!-- Botão para preencher a vaga (gerar termo) -->
                                @if((Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador') && $vaga->status == 'disponivel' && !$vaga->fk_id_termo)
                                    <a href="{{ route('termos.create', ['empresa_id' => $vaga->fk_id_empresa, 'vaga_id' => $vaga->id_vaga, 'return_to' => 'vagas.index']) }}"
                                        class="btn btn-info btn-sm" title="Preencher Vaga (gerar termo)">
                                        <i class="fas fa-file-pen"></i>
                                    </a>
                                @endif

                                @if(!$vaga->fk_id_termo)
                                    <form action="{{ route('vagas.destroy', $vaga->id_vaga) }}" method="POST" class="m-0"
                                        onsubmit="return confirm('Confirma a exclusão desta vaga?')">
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

    @if (method_exists($vagas, 'links'))
        <!-- Paginação (rodapé) -->
        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($vagas->total() > 0)
                        Mostrando {{ $vagas->firstItem() }}–{{ $vagas->lastItem() }} de {{ $vagas->total() }}
                    @else
                        Nenhum registro encontrado
                    @endif
                </span>
                @php $pp = request('per_page', '25'); @endphp
                <select id="perPageSelectorVagasBottom" class="form-select form-select-sm per-page-selector"
                    onchange="changePerPage(this.value)" style="width: auto; margin-left: 10px;">
                    <option value="25" {{ $pp == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $pp == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $pp == '100' ? 'selected' : '' }}>100</option>
                    <option value="200" {{ $pp == '200' ? 'selected' : '' }}>200</option>
                    <option value="all" {{ $pp == 'all' ? 'selected' : '' }}>Tudo</option>
                </select>
            </div>
            <div>
                {{ $vagas->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <script>
        function changePerPage(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.delete('page'); // Reset para página 1
            window.location.href = url.toString();
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Setup filtro de busca de empresa
            if (document.getElementById('empresa_search')) {
                const searchInput = document.getElementById('empresa_search');
                const select = document.getElementById('empresa');
                const wrapper = document.getElementById('empresa_select_wrapper');
                const options = Array.from(select.options);

                searchInput.addEventListener('focus', function () {
                    wrapper.style.display = 'block';
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
                    wrapper.style.display = 'block';
                });

                select.addEventListener('change', function () {
                    const selected = select.options[select.selectedIndex];
                    searchInput.value = selected.text;
                    wrapper.style.display = 'none';
                });

                document.addEventListener('click', function (e) {
                    if (!searchInput.contains(e.target) && !wrapper.contains(e.target)) {
                        wrapper.style.display = 'none';
                    }
                });
            }
        });
    </script>

@endsection