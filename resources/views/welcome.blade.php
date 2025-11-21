@extends('layouts.main')

@section('title', 'Página Inicial')

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
    </div>

    @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
        <div class="row">
            <div class="col-8">
                <div class="row row-cols-2">
                    <div class="col">
                        <div class="card text-center" style="margin-bottom: 10px;">
                            <div class="card-body">
                                <h5 class="card-title">Instituições de Ensino</h5>
                                <p class="card-text">Visualize a lista de instituições de ensino.</p>
                                <a href="{{ route('escolas.index') }}" class="btn btn-primary">Ver Instituições de Ensino</a>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card text-center" style="margin-bottom: 10px;">
                            <div class="card-body">
                                <h5 class="card-title">Unidades Concedentes</h5>
                                <p class="card-text">Visualize a lista de empresas concedentes.</p>
                                <a href="{{ route('empresas.index') }}" class="btn btn-primary">Ver Unidades Concedentes</a>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card text-center" style="margin-bottom: 10px;">
                            <div class="card-body">
                                <h5 class="card-title">Estagiários</h5>
                                <p class="card-text">Visualize a lista de estagiários.</p>
                                <a href="{{ route('estagiarios.index') }}" class="btn btn-primary">Ver Estagiários</a>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card text-center" style="margin-bottom: 10px;">
                            <div class="card-body">
                                <h5 class="card-title">Supervisores</h5>
                                <p class="card-text">Visualize a lista de supervisores.</p>
                                <a href="{{ route('supervisores.index') }}" class="btn btn-primary">Ver Supervisores</a>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card text-center" style="margin-bottom: 10px;">
                            <div class="card-body">
                                <h5 class="card-title">Termos de Contrato</h5>
                                <p class="card-text">Visualize a lista de termos de contrato.</p>
                                <a href="{{ route('termos.index') }}" class="btn btn-primary">Ver Termos</a>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card text-center" style="margin-bottom: 10px;">
                            <div class="card-body">
                                <h5 class="card-title">Folhas de Pagamento</h5>
                                <p class="card-text">Visualize a lista de folhas de pagamento.</p>
                                <a href="{{ route('folhas.index') }}" class="btn btn-primary">Ver Folhas</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card" style="max-height: 480px;">
                    <div class="card-header">
                        <div class="row row-cols-2 align-items-center">
                            <div class="col">
                                Termos A Vencer
                            </div>
                            <div class="row align-items-center">
                                <select id="diasVencimento" class="form-select mb-3" style="font-size: 0.8em;"
                                    title="Dias até o vencimento">
                                    <option value="15" selected>Próximos 15 dias</option>
                                    <option value="10">Próximos 10 dias</option>
                                    <option value="5">Próximos 5 dias</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Selecione o período para visualizar os termos que estão próximos do vencimento.</p>
                        <ul id="termosList" class="list-group" style="max-height: 320px; overflow-y: auto;">
                            <!-- A lista será preenchida via JS -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (Auth::user()->nivel == 'empresa')
        <div class="row justify-content-center">
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
                        <h5 class="card-title">Meus Locais</h5>
                        <p class="card-text">Gerencie os locais da sua unidade concedente.</p>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#meusLocaisModal">Gerenciar Locais</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Meus Locais (somente visualizar e editar) -->
        <div class="modal fade" id="meusLocaisModal" tabindex="-1" aria-labelledby="meusLocaisModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="meusLocaisModalLabel">Meus Locais</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="d-flex justify-content-end mb-2">
                            <button type="button" id="btnNovoMeuLocal" class="btn btn-success btn-sm">Adicionar Local</button>
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
                        <div id="meusLocaisVazio" class="alert alert-info py-2" style="display:none;">Nenhum local cadastrado.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal: Editar Local (empresa) -->
        <div class="modal fade" id="editarMeuLocalModal" tabindex="-1" aria-labelledby="editarMeuLocalModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarMeuLocalModalLabel">Editar Local</h5>
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
                    if (modalEditarTitulo) modalEditarTitulo.textContent = 'Editar Local';
                    editarAlert.style.display = 'none';
                    showModal(modalEditarEl);
                }

                function abrirCriacao() {
                    editId = null;
                    editarDesc.value = '';
                    if (modalEditarTitulo) modalEditarTitulo.textContent = 'Novo Local';
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
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const termosList = document.getElementById('termosList');
            const diasVencimento = document.getElementById('diasVencimento');

            // Array de termos vindo do backend
            const termos = [
                @foreach ($termos as $termo)
                                                                                                                                                                                                                                    {
                        id: {{ $termo->id_termo }},
                        numero: '{{ $termo->numero_termo }}',
                        ano: '{{ $termo->ano_termo }}',
                        estagiario: '{{ isset($termo->estagiario) ? e($termo->estagiario->nome_estagiario) : "N/A" }}',
                        data_fim: '{{ \Carbon\Carbon::parse($termo->data_fim_estagio)->format('Y-m-d') }}',
                        data_fim_formatada: '{{ \Carbon\Carbon::parse($termo->data_fim_estagio)->format('d/m/Y') }}',
                        url: '{{ route('termos.show', $termo->id_termo) }}'
                    },
                @endforeach
                                                                                                                    ];

            function filtrarTermos() {
                const dias = parseInt(diasVencimento.value);
                const hoje = new Date();
                const dataLimite = new Date();
                dataLimite.setDate(hoje.getDate() + dias);

                termosList.innerHTML = '';

                const filtrados = termos.filter(termo => {
                    const dataFim = new Date(termo.data_fim + 'T23:59:59');
                    return dataFim >= hoje && dataFim <= dataLimite;
                }).sort((a, b) => {
                    // Ordena do mais próximo para o mais distante
                    return new Date(a.data_fim) - new Date(b.data_fim);
                });

                if (filtrados.length === 0) {
                    termosList.innerHTML = '<li class="list-group-item text-center text-muted"><i class="bi bi-info-circle"></i> Nenhum termo perto de vencer neste período.</li>';
                } else {
                    filtrados.forEach(termo => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center flex-wrap';
                        li.innerHTML = `
                                                                                                                                    <div class="d-flex align-items-center">
                                                                                                                                        <div>
                                                                                                                                            <a href="${termo.url}" class="fw-bold text-decoration-none">${termo.numero}/${termo.ano}</a>
                                                                                                                                            <div class="small text-secondary mb-1">Vencimento: <span class="badge bg-danger">${termo.data_fim_formatada}</span></div>
                                                                                                                                            <div class="small text-muted" style="font-size: 0.85em;"><i class="bi bi-person"></i> ${termo.estagiario}</div>
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                    <a href="${termo.url}" class="btn btn-sm btn-outline-primary mt-2 mt-md-0">Detalhes</a>
                                                                                                                                `;
                        termosList.appendChild(li);
                    });
                }
            }

            diasVencimento.addEventListener('change', filtrarTermos);

            // Filtra ao carregar a página
            filtrarTermos();
        });
    </script>
    <!-- Adicione o Bootstrap Icons no seu layout principal, se ainda não tiver: -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
@endsection