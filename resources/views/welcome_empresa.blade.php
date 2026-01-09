@extends('layouts.main')

@section('title', 'Página Inicial - Empresa')

@section('content')

    @if(session('error'))
        <div class="alert alert-danger mt-2">{{ session('error') }}</div>
    @endif

    <div class="container"
        style="background-color:rgb(242, 242, 242); border-radius: 15px; margin-top: -30px; padding-top: 5px; padding-left: 15px; margin-bottom: 20px;">
        <div class="row">
            <div class="col">
                <h3>Bem-vindo</h3>
                <h3>{{ Auth::user()->name }}!</h3>
            </div>
            <div class="col-md-7">
                <p class="lead">Este é o painel principal do sistema de gestão. Aqui você pode acessar as principais
                    funcionalidades de acordo com o seu nível de acesso.</p>
            </div>
        </div>
    </div>
    <hr style="margin-top: -10px; background-color: #102e6c;">
    <div class="row" style="margin-top: -5px;">
        <p>Utilize os botões abaixo ou o menu de navegação para explorar as opções disponíveis.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col col-md-4">
            <div class="card text-center" style="margin-bottom: 10px;">
                <div class="card-body">
                    <h5 class="card-title">Vagas de Estágio</h5>
                    <p class="card-text">Gerencie aqui a abertura de Vagas.</p>
                    <a href="{{ route('vagas.index') }}" class="btn btn-primary">Ver Vagas</a>
                </div>
            </div>
        </div>
        <div class="col col-md-4">
            <div class="card text-center" style="margin-bottom: 10px;">
                <div class="card-body">
                    <h5 class="card-title">Chamados</h5>
                    <p class="card-text">Abra e acompanhe chamados de suporte.</p>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNovoChamado">
                        <i class="fas fa-plus me-1"></i> Abrir Chamado
                    </button>
                    <a href="{{ route('chamados.index') }}" class="btn btn-primary ms-2">
                        <i class="fas fa-list me-1"></i> Ver Chamados
                    </a>
                </div>
            </div>
        </div>
        <div class="col col-md-4">
            <div class="card text-center" style="margin-bottom: 10px;">
                <div class="card-body">
                    <h5 class="card-title">Supervisores</h5>
                    <p class="card-text">Gerencie os supervisores da sua empresa.</p>
                    <a href="{{ route('empresa.supervisores.index') }}" class="btn btn-primary">Ver Supervisores</a>
                </div>
            </div>
        </div>
        <div class="col col-md-4">
            <div class="card text-center" style="margin-bottom: 10px;">
                <div class="card-body">
                    <h5 class="card-title">Termos de Contrato</h5>
                    <p class="card-text">Visualize os contratos da sua empresa.</p>
                    <a href="{{ route('termos.index') }}" class="btn btn-primary">Ver Termos</a>
                </div>
            </div>
        </div>
        <div class="col col-md-4">
            <div class="card text-center" style="margin-bottom: 10px;">
                <div class="card-body">
                    <h5 class="card-title">Folhas de Pagamento</h5>
                    <p class="card-text">Visualize a lista de folhas de pagamento.</p>
                    <a href="{{ route('folhas.index') }}" class="btn btn-primary">Ver Folhas</a>
                </div>
            </div>
        </div>
        <div class="col col-md-4">
            <div class="card text-center" style="margin-bottom: 10px;">
                <div class="card-body">
                    <h5 class="card-title">Meus Departamentos</h5>
                    <p class="card-text">Gerencie os seus departamentos.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#meusLocaisModal">Gerenciar Departamentos</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Meus Locais (somente visualizar e editar) -->
    <div class="modal fade" id="meusLocaisModal" tabindex="-1" aria-labelledby="meusLocaisModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="meusLocaisModalLabel">Meus Departamentos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-end mb-2">
                        <button type="button" id="btnNovoMeuLocal" class="btn btn-success btn-sm">Adicionar
                            Departamento</button>
                    </div>
                    <div id="meusLocaisLoading" class="text-center text-muted py-2" style="display:none;">Carregando...
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th class="w-75">Descrição</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="meusLocaisBody"></tbody>
                        </table>
                    </div>
                    <div id="meusLocaisVazio" class="alert alert-info py-2" style="display:none;">Nenhum departamento
                        cadastrado.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Editar Departamento (empresa) -->
    <div class="modal fade" id="editarMeuLocalModal" tabindex="-1" aria-labelledby="editarMeuLocalModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarMeuLocalModalLabel">Editar Departamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="editarMeuLocalAlert" class="alert alert-danger py-2" style="display:none;"></div>
                    <div class="mb-3">
                        <label for="editarMeuLocalDescricao" class="form-label">Descrição</label>
                        <input type="text" class="form-control" id="editarMeuLocalDescricao" maxlength="255" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="salvarMeuLocalBtn">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Config para JS (URLs e CSRF) -->
    <div id="meusLocaisConfig" data-index-url="{{ url('/meus-locais') }}" data-update-url-prefix="{{ url('/meus-locais') }}"
        style="display:none;"></div>

    <script>
        (function () {
            const configEl = document.getElementById('meusLocaisConfig');
            const indexUrl = configEl?.dataset.indexUrl || '/meus-locais';
            const updatePrefix = configEl?.dataset.updateUrlPrefix || '/meus-locais';
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            const modalListaEl = document.getElementById('meusLocaisModal');
            const modalEditarEl = document.getElementById('editarMeuLocalModal');

            function showModal(el) { const m = window.bootstrap?.Modal.getOrCreateInstance(el); m && m.show(); }
            function hideModal(el) { const m = window.bootstrap?.Modal.getOrCreateInstance(el); m && m.hide(); }

            const body = document.getElementById('meusLocaisBody');
            const loading = document.getElementById('meusLocaisLoading');
            const vazio = document.getElementById('meusLocaisVazio');
            const editarDesc = document.getElementById('editarMeuLocalDescricao');
            const editarAlert = document.getElementById('editarMeuLocalAlert');
            const btnSalvar = document.getElementById('salvarMeuLocalBtn');
            const btnNovo = document.getElementById('btnNovoMeuLocal');
            const modalEditarTitulo = document.getElementById('editarMeuLocalModalLabel');

            let cache = [];
            let editId = null;

            function setLoading(on) { if (loading) loading.style.display = on ? 'block' : 'none'; }
            function render() {
                body.innerHTML = '';
                if (!cache.length) { vazio.style.display = 'block'; return; }
                vazio.style.display = 'none';
                cache.forEach(l => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                                                                                    <td>${l.descricao ?? ''}</td>
                                                                                    <td class="text-end">
                                                                                        <button class="btn btn-sm btn-outline-primary" data-action="editar" data-id="${l.id_local}">Editar</button>
                                                                                    </td>`;
                    body.appendChild(tr);
                });
            }

            async function carregar() {
                try {
                    setLoading(true);
                    const resp = await fetch(indexUrl, { headers: { 'Accept': 'application/json' } });
                    if (!resp.ok) throw new Error('Falha ao carregar locais');
                    cache = await resp.json();
                    render();
                } catch (e) {
                    body.innerHTML = '<tr><td colspan="2" class="text-danger">Erro ao carregar locais.</td></tr>';
                } finally { setLoading(false); }
            }

            function abrirEdicao(local) {
                editId = local.id_local;
                editarDesc.value = local.descricao || '';
                if (modalEditarTitulo) modalEditarTitulo.textContent = 'Editar Departamento';
                editarAlert.style.display = 'none';
                showModal(modalEditarEl);
            }

            function abrirCriacao() {
                editId = null;
                editarDesc.value = '';
                if (modalEditarTitulo) modalEditarTitulo.textContent = 'Novo Departamento';
                editarAlert.style.display = 'none';
                showModal(modalEditarEl);
            }

            async function salvar() {
                if (!editarDesc.value.trim()) {
                    editarAlert.textContent = 'Descrição é obrigatória.';
                    editarAlert.style.display = 'block';
                    return;
                }
                try {
                    const isCreate = !editId;
                    const url = isCreate ? indexUrl : `${updatePrefix}/${editId}`;
                    const method = isCreate ? 'POST' : 'PUT';
                    const resp = await fetch(url, {
                        method,
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ descricao: editarDesc.value.trim() })
                    });
                    if (!resp.ok) {
                        const data = await resp.json().catch(() => ({}));
                        const msg = data?.message || (data?.errors ? Object.values(data.errors).flat().join(' ') : 'Erro ao salvar.');
                        throw new Error(msg);
                    }
                    await carregar();
                    hideModal(modalEditarEl);
                } catch (e) {
                    editarAlert.textContent = e.message || 'Erro ao salvar.';
                    editarAlert.style.display = 'block';
                }
            }

            if (modalListaEl) {
                modalListaEl.addEventListener('shown.bs.modal', carregar);
            }
            if (body) {
                body.addEventListener('click', (e) => {
                    const t = e.target;
                    if (!(t instanceof HTMLElement)) return;
                    const action = t.getAttribute('data-action');
                    const id = t.getAttribute('data-id');
                    if (action === 'editar' && id) {
                        const local = cache.find(x => String(x.id_local) === String(id));
                        if (local) abrirEdicao(local);
                    }
                });
            }
            btnSalvar && btnSalvar.addEventListener('click', salvar);
            btnNovo && btnNovo.addEventListener('click', abrirCriacao);
        })();
    </script>

    <!-- Incluir modal de novo chamado -->
    @include('chamados.partials.modal-novo-chamado')
@endsection