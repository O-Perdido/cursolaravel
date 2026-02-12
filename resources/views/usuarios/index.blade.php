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
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#searchUsuariosModal">
                        <i class="fas fa-magnifying-glass me-1"></i> Pesquisar usuários
                    </button>
                    <a href="{{ route('usuarios.register') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Cadastrar Usuário
                    </a>
                </div>
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
                <!--
                        <th style="padding-left: 75px;">Senha</th>
                        -->
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->name }}</td>
                    <td>{{ $usuario->email }}</td>
                    <td>{{ $usuario->nivel }}</td>
                    <!--
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
                                    -->
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

    <!-- Modal: Pesquisa de usuários -->
    <div class="modal fade" id="searchUsuariosModal" tabindex="-1" aria-labelledby="searchUsuariosModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchUsuariosModalLabel">Pesquisar usuários</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="usuariosSearchAlert" class="alert d-none" role="alert"></div>
                    <form id="usuariosSearchForm">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label for="usuariosSearchNivel" class="form-label mb-1">Nível</label>
                                <select id="usuariosSearchNivel" class="form-select form-select-sm" required>
                                    <option value="estagiario" selected>Estagiário</option>
                                    <option value="empresa">Unidade Concedente</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="usuariosSearchTermo" class="form-label mb-1">CPF, nome ou email</label>
                                <input type="text" id="usuariosSearchTermo" class="form-control form-control-sm"
                                    placeholder="CPF, nome do estagiário ou email do usuário" required>
                            </div>
                            <div class="col-md-2 d-grid">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search me-1"></i> Pesquisar
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive mt-3">
                        <table class="table table-sm align-middle mb-1">
                            <thead>
                                <tr>
                                    <th>Usuário</th>
                                    <th>Email</th>
                                    <th id="usuariosSearchDocumentoHeader">CPF/CNPJ</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="usuariosSearchResultados"></tbody>
                        </table>
                        <div id="usuariosSearchEmpty" class="text-muted small d-none">
                            Nenhum resultado encontrado.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Detalhes do usuário -->
    <div class="modal fade" id="usuarioDetalhesModal" tabindex="-1" aria-labelledby="usuarioDetalhesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="usuarioDetalhesModalLabel">Detalhes do usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="usuarioDetalheAlert" class="alert d-none" role="alert"></div>
                    <input type="hidden" id="usuarioDetalheId">
                    <dl class="row mb-3">
                        <dt class="col-5">Nome do usuário</dt>
                        <dd class="col-7" id="usuarioDetalheNome">-</dd>
                        <dt class="col-5">Nível</dt>
                        <dd class="col-7" id="usuarioDetalheNivel">-</dd>
                        <dt class="col-5" id="usuarioDetalheEntidadeLabel">Entidade</dt>
                        <dd class="col-7" id="usuarioDetalheEntidadeNome">-</dd>
                        <dt class="col-5" id="usuarioDetalheDocumentoLabel">Documento</dt>
                        <dd class="col-7" id="usuarioDetalheDocumento">-</dd>
                        <dt class="col-5">Email da entidade</dt>
                        <dd class="col-7" id="usuarioDetalheEntidadeEmail">-</dd>
                    </dl>

                    <div>
                        <label for="usuarioEmailInput" class="form-label mb-1">Email do usuário</label>
                        <div class="input-group input-group-sm">
                            <input type="email" id="usuarioEmailInput" class="form-control" readonly>
                            <button class="btn btn-outline-secondary" type="button" id="btnEditarEmailUsuario">
                                Editar
                            </button>
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <button class="btn btn-success btn-sm d-none" type="button" id="btnSalvarEmailUsuario">
                                Salvar
                            </button>
                            <button class="btn btn-outline-secondary btn-sm d-none" type="button"
                                id="btnCancelarEmailUsuario">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var searchModalEl = document.getElementById('searchUsuariosModal');
            if (!searchModalEl) {
                return;
            }

            var searchForm = document.getElementById('usuariosSearchForm');
            var nivelSelect = document.getElementById('usuariosSearchNivel');
            var termoInput = document.getElementById('usuariosSearchTermo');
            var resultadosBody = document.getElementById('usuariosSearchResultados');
            var emptyMsg = document.getElementById('usuariosSearchEmpty');
            var alertBox = document.getElementById('usuariosSearchAlert');
            var documentoHeader = document.getElementById('usuariosSearchDocumentoHeader');

            var detalheModalEl = document.getElementById('usuarioDetalhesModal');
            var detalheModal = new bootstrap.Modal(detalheModalEl);
            var detalheAlert = document.getElementById('usuarioDetalheAlert');
            var detalheId = document.getElementById('usuarioDetalheId');
            var detalheNome = document.getElementById('usuarioDetalheNome');
            var detalheNivel = document.getElementById('usuarioDetalheNivel');
            var detalheEntidadeLabel = document.getElementById('usuarioDetalheEntidadeLabel');
            var detalheEntidadeNome = document.getElementById('usuarioDetalheEntidadeNome');
            var detalheDocumentoLabel = document.getElementById('usuarioDetalheDocumentoLabel');
            var detalheDocumento = document.getElementById('usuarioDetalheDocumento');
            var detalheEntidadeEmail = document.getElementById('usuarioDetalheEntidadeEmail');
            var emailInput = document.getElementById('usuarioEmailInput');
            var editarEmailBtn = document.getElementById('btnEditarEmailUsuario');
            var salvarEmailBtn = document.getElementById('btnSalvarEmailUsuario');
            var cancelarEmailBtn = document.getElementById('btnCancelarEmailUsuario');

            var searchUrl = @json(route('usuarios.search'));
            var usuariosBaseUrl = @json(url('/usuarios'));
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            function setAlert(el, type, message) {
                el.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
                el.classList.add('alert-' + type);
                el.textContent = message;
            }

            function clearAlert(el) {
                el.className = 'alert d-none';
                el.textContent = '';
            }

            function getErrorMessage(payload, fallback) {
                if (payload && payload.message) {
                    return payload.message;
                }
                if (payload && payload.errors) {
                    var firstKey = Object.keys(payload.errors)[0];
                    if (firstKey && payload.errors[firstKey] && payload.errors[firstKey][0]) {
                        return payload.errors[firstKey][0];
                    }
                }
                return fallback;
            }

            function updatePlaceholder() {
                var nivel = nivelSelect.value;
                if (nivel === 'empresa') {
                    termoInput.placeholder = 'CNPJ, nome da empresa ou email do usuário';
                    documentoHeader.textContent = 'CNPJ';
                } else {
                    termoInput.placeholder = 'CPF, nome do estagiário ou email do usuário';
                    documentoHeader.textContent = 'CPF';
                }
            }

            function resetResultados() {
                resultadosBody.innerHTML = '';
                emptyMsg.classList.add('d-none');
            }

            function lockEmailEdit(lock) {
                emailInput.readOnly = lock;
                editarEmailBtn.classList.toggle('d-none', !lock);
                salvarEmailBtn.classList.toggle('d-none', lock);
                cancelarEmailBtn.classList.toggle('d-none', lock);
            }

            nivelSelect.addEventListener('change', function () {
                updatePlaceholder();
                resetResultados();
            });

            updatePlaceholder();

            searchForm.addEventListener('submit', async function (event) {
                event.preventDefault();
                clearAlert(alertBox);
                resetResultados();

                var termo = termoInput.value.trim();
                if (termo.length < 2) {
                    setAlert(alertBox, 'warning', 'Digite pelo menos 2 caracteres para pesquisar.');
                    return;
                }

                try {
                    var response = await fetch(searchUrl + '?nivel=' + encodeURIComponent(nivelSelect.value)
                        + '&termo=' + encodeURIComponent(termo), {
                        headers: { 'Accept': 'application/json' }
                    });
                    var payload = await response.json();

                    if (!response.ok) {
                        setAlert(alertBox, 'danger', getErrorMessage(payload, 'Não foi possível realizar a pesquisa.'));
                        return;
                    }

                    if (!payload.data || payload.data.length === 0) {
                        emptyMsg.classList.remove('d-none');
                        return;
                    }

                    payload.data.forEach(function (usuario) {
                        var documento = usuario.documento || '-';
                        var nomeBase = usuario.entidade_nome || usuario.nome_usuario || '-';
                        var row = document.createElement('tr');
                        var tdNome = document.createElement('td');
                        tdNome.textContent = nomeBase;
                        if (usuario.nome_usuario && usuario.entidade_nome) {
                            var nomeExtra = document.createElement('div');
                            nomeExtra.className = 'text-muted small';
                            nomeExtra.textContent = 'Usuário: ' + usuario.nome_usuario;
                            tdNome.appendChild(nomeExtra);
                        }

                        var tdEmail = document.createElement('td');
                        tdEmail.textContent = usuario.email_usuario || '-';

                        var tdDocumento = document.createElement('td');
                        tdDocumento.textContent = documento;

                        var tdAcoes = document.createElement('td');
                        tdAcoes.className = 'text-end';
                        var abrirBtn = document.createElement('button');
                        abrirBtn.type = 'button';
                        abrirBtn.className = 'btn btn-sm btn-outline-primary';
                        abrirBtn.textContent = 'Abrir';
                        abrirBtn.setAttribute('data-userid', usuario.id);
                        tdAcoes.appendChild(abrirBtn);

                        row.appendChild(tdNome);
                        row.appendChild(tdEmail);
                        row.appendChild(tdDocumento);
                        row.appendChild(tdAcoes);
                        resultadosBody.appendChild(row);
                    });
                } catch (error) {
                    setAlert(alertBox, 'danger', 'Não foi possível realizar a pesquisa.');
                }
            });

            resultadosBody.addEventListener('click', async function (event) {
                var button = event.target.closest('button[data-userid]');
                if (!button) {
                    return;
                }

                var userId = button.getAttribute('data-userid');
                clearAlert(detalheAlert);
                lockEmailEdit(true);

                try {
                    var response = await fetch(usuariosBaseUrl + '/' + userId + '/detalhes', {
                        headers: { 'Accept': 'application/json' }
                    });
                    var payload = await response.json();

                    if (!response.ok) {
                        setAlert(detalheAlert, 'danger', getErrorMessage(payload, 'Não foi possível carregar o usuário.'));
                        return;
                    }

                    detalheId.value = payload.id;
                    detalheNome.textContent = payload.nome_usuario || '-';
                    detalheNivel.textContent = payload.nivel || '-';

                    if (payload.nivel === 'empresa') {
                        detalheEntidadeLabel.textContent = 'Empresa';
                        detalheDocumentoLabel.textContent = 'CNPJ';
                        detalheEntidadeNome.textContent = payload.empresa ? payload.empresa.nome : '-';
                        detalheDocumento.textContent = payload.empresa ? payload.empresa.cnpj : '-';
                        detalheEntidadeEmail.textContent = payload.empresa ? payload.empresa.email : '-';
                    } else {
                        detalheEntidadeLabel.textContent = 'Estagiário';
                        detalheDocumentoLabel.textContent = 'CPF';
                        detalheEntidadeNome.textContent = payload.estagiario ? payload.estagiario.nome : '-';
                        detalheDocumento.textContent = payload.estagiario ? payload.estagiario.cpf : '-';
                        detalheEntidadeEmail.textContent = payload.estagiario ? payload.estagiario.email : '-';
                    }

                    emailInput.value = payload.email_usuario || '';
                    emailInput.dataset.original = emailInput.value;
                    lockEmailEdit(true);
                    detalheModal.show();
                } catch (error) {
                    setAlert(detalheAlert, 'danger', 'Não foi possível carregar o usuário.');
                }
            });

            editarEmailBtn.addEventListener('click', function () {
                clearAlert(detalheAlert);
                lockEmailEdit(false);
                emailInput.focus();
            });

            cancelarEmailBtn.addEventListener('click', function () {
                emailInput.value = emailInput.dataset.original || '';
                lockEmailEdit(true);
            });

            salvarEmailBtn.addEventListener('click', async function () {
                clearAlert(detalheAlert);
                var novoEmail = emailInput.value.trim();
                if (novoEmail.length === 0) {
                    setAlert(detalheAlert, 'warning', 'Informe um email válido.');
                    return;
                }

                try {
                    var response = await fetch(usuariosBaseUrl + '/' + detalheId.value + '/email', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ email: novoEmail })
                    });

                    var payload = await response.json();

                    if (!response.ok) {
                        setAlert(detalheAlert, 'danger', getErrorMessage(payload, 'Não foi possível atualizar o email.'));
                        return;
                    }

                    emailInput.dataset.original = novoEmail;
                    lockEmailEdit(true);
                    setAlert(detalheAlert, 'success', payload.message || 'Email atualizado com sucesso.');
                } catch (error) {
                    setAlert(detalheAlert, 'danger', 'Não foi possível atualizar o email.');
                }
            });
        });
    </script>
@endsection