@extends('layouts.main')

@section('title', 'Termos')

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


    <!-- Card de Filtro e Título -->
    <div class="card mb-3 shadow-sm">
        <div class="card-body pb-2 pt-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    <i class="fas fa-users me-2 text-primary"></i>
                    Lista de Usuários
                </h4>
                <a href="{{ route('usuarios.register') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-plus me-1"></i> Cadastrar Usuário
                </a>
            </div>
            <form method="GET" action="{{ route('usuarios.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="filtro_nome" class="form-label mb-1">Nome</label>
                        <input type="text" name="filtro_nome" id="filtro_nome" class="form-control form-control-sm"
                            value="{{ request('filtro_nome') }}" placeholder="Nome do usuário">
                    </div>
                    <div class="col-md-3">
                        <label for="filtro_email" class="form-label mb-1">Email</label>
                        <input type="text" name="filtro_email" id="filtro_email" class="form-control form-control-sm"
                            value="{{ request('filtro_email') }}" placeholder="Email">
                    </div>
                    <div class="col-md-3">
                        <label for="filtro_nivel" class="form-label mb-1">Nível de Acesso</label>
                        <select name="filtro_nivel" id="filtro_nivel" class="form-select form-select-sm">
                            <option value="">Todos</option>
                            <option value="admin" {{ request('filtro_nivel') == 'admin' ? 'selected' : '' }}>Administrador
                            </option>
                            <option value="operador" {{ request('filtro_nivel') == 'operador' ? 'selected' : '' }}>Operador
                            </option>
                            <option value="empresa" {{ request('filtro_nivel') == 'empresa' ? 'selected' : '' }}>Unidade
                                Concedente</option>
                            <option value="estagiario" {{ request('filtro_nivel') == 'estagiario' ? 'selected' : '' }}>
                                Estagiário</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="ordem_cadastro" id="ordem_cadastro"
                                value="1" {{ request('ordem_cadastro') ? 'checked' : '' }}>
                            <label class="form-check-label" for="ordem_cadastro">
                                Ordenar por Mais Recentes
                            </label>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex flex-column align-items-end justify-content-end gap-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100 mb-1">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                            <i class="fas fa-eraser"></i> Limpar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="mb-2" style="font-weight: bold; font-size: 1.1em;">
        Total de usuários: {{ method_exists($usuarios, 'total') ? $usuarios->total() : $usuarios->count() }}
    </div>

    @if (method_exists($usuarios, 'links'))
        <!-- Paginação (topo) -->
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($usuarios->total() > 0)
                        Mostrando {{ $usuarios->firstItem() }}–{{ $usuarios->lastItem() }} de {{ $usuarios->total() }}
                    @else
                        Nenhum registro encontrado
                    @endif
                </span>
                @php $pp = request('per_page', '25'); @endphp
                <select id="perPageSelectorUsuarios" class="form-select form-select-sm per-page-selector"
                    onchange="changePerPage(this.value)">
                    <option value="25" {{ $pp == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $pp == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $pp == '100' ? 'selected' : '' }}>100</option>
                    <option value="200" {{ $pp == '200' ? 'selected' : '' }}>200</option>
                    <option value="all" {{ $pp == 'all' ? 'selected' : '' }}>Tudo</option>
                </select>
            </div>
            <div>
                {{ $usuarios->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Nome do Usuário</th>
                <th>Email</th>
                <th>Nível de Acesso</th>
                <th style="padding-left: 75px;">Senha</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->name }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->nivel }}</td>
                    <td>
                        <button type="button" class="btn btn-outline-secondary" style="border: none;"
                            onclick="togglePasswordVisibility({{ $usuario->id }})">
                            <img style="width: 25px; height: 25px;" src="{{ asset('images/eye_visible.png') }}"
                                id="password-icon{{ $usuario->id }}" alt="Mostrar Senha">
                        </button>
                        <input style="border: none; outline: none;" type="password" id="password{{ $usuario->id }}"
                            value="{{ $usuario->senha }}" readonly>
                    </td>
                    <script>
                        function togglePasswordVisibility(userId) {
                            var passwordField = document.getElementById('password' + userId);
                            var passwordIcon = document.getElementById('password-icon' + userId);
                            if (passwordField.type === 'password') {
                                passwordField.type = 'text';
                                passwordIcon.src = '{{ asset('images/eye_not_visible.png') }}';
                            } else {
                                passwordField.type = 'password';
                                passwordIcon.src = '{{ asset('images/eye_visible.png') }}';
                            }
                        }
                    </script>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                            data-bs-target="#confirmDeleteModal" data-userid="{{ $usuario->id }}">
                            Excluir
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if (method_exists($usuarios, 'links'))
        <!-- Paginação (rodapé) -->
        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($usuarios->total() > 0)
                        Mostrando {{ $usuarios->firstItem() }}–{{ $usuarios->lastItem() }} de {{ $usuarios->total() }}
                    @else
                        Nenhum registro encontrado
                    @endif
                </span>
                <select id="perPageSelectorUsuariosBottom" class="form-select form-select-sm per-page-selector"
                    onchange="changePerPage(this.value)">
                    <option value="25" {{ $pp == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $pp == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $pp == '100' ? 'selected' : '' }}>100</option>
                    <option value="200" {{ $pp == '200' ? 'selected' : '' }}>200</option>
                    <option value="all" {{ $pp == 'all' ? 'selected' : '' }}>Tudo</option>
                </select>
            </div>
            <div>
                {{ $usuarios->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <script>
        function changePerPage(perPage) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', perPage);
            url.searchParams.set('page', 1); // Sempre volta para a primeira página
            window.location.href = url.toString();
        }
    </script>

    <!-- Modal -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Você tem certeza que deseja excluir este usuário?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteForm" action="" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        var confirmDeleteModal = document.getElementById('confirmDeleteModal');
        confirmDeleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var userId = button.getAttribute('data-userid');
            var form = document.getElementById('deleteForm');
            form.action = '/usuarios/' + userId;
        });
    </script>
@endsection