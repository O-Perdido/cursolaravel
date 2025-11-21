@extends('layouts.main')

@section('title', 'Detalhes da Unidade Concedente')

@section('content')
    <h1>Detalhes da Unidade Concedente</h1>
    <a href="{{ route('empresas.index') }}" class="btn btn-secondary mb-3">Voltar</a>
    <div class="card shadow-sm">
        <div class="card-header text-black">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $empresa->nome_empresa }}</h5>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#representantesModal">
                        <i class="fas fa-users me-1"></i> Representantes
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#locaisModal">
                        Gerenciar Locais
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Coluna 1 -->
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Informações Gerais</h6>
                    <p class="mb-1"><strong>Nome:</strong> {{ $empresa->nome_empresa }}</p>
                    <p class="mb-1"><strong>CNPJ:</strong>
                        {{ $empresa->numero_cnpj ? preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $empresa->numero_cnpj) : '' }}
                    </p>
                    <p class="mb-1"><strong>Telefone:</strong>
                        {{ $empresa->numero_telefone ? preg_replace('/(\d{2})(\d{4,5})(\d{4})/', '($1) $2-$3', $empresa->numero_telefone) : '' }}
                    </p>
                    <p class="mb-1"><strong>Celular:</strong>
                        {{ $empresa->numero_celular ? preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $empresa->numero_celular) : '' }}
                    </p>
                    <p class="mb-1"><strong>Email:</strong> {{ $empresa->email }}</p>
                    <hr class="my-2">
                    <h6 class="text-muted mb-3">Endereço</h6>
                    <p class="mb-1"><strong>CEP:</strong>
                        {{ $empresa->numero_cep ? preg_replace('/(\d{5})(\d{3})/', '$1-$2', $empresa->numero_cep) : '' }}
                    </p>
                    <p class="mb-1"><strong>Endereço:</strong> {{ $empresa->endereco }}</p>
                    <p class="mb-1"><strong>Número:</strong> {{ $empresa->numero_endereco }}</p>
                    <p class="mb-1"><strong>Complemento:</strong> {{ $empresa->complemento_endereco }}</p>
                    <p class="mb-1"><strong>Bairro:</strong> {{ $empresa->bairro }}</p>
                    <p class="mb-1"><strong>Cidade:</strong> {{ $empresa->cidade->nm_cidade }}</p>
                    <p class="mb-1"><strong>Estado:</strong> {{ $empresa->cidade->estado->nm_estado }}</p>
                </div>
                <!-- Coluna 2 -->
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Representante Principal (Legado)</h6>
                    <p class="mb-1"><strong>Nome:</strong> {{ $empresa->nome_representante }}</p>
                    <p class="mb-1"><strong>Cargo:</strong> {{ $empresa->cargo_representante }}</p>
                    <p class="mb-1"><strong>CPF:</strong>
                        {{ $empresa->cpf_representante ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $empresa->cpf_representante) : '' }}
                    </p>
                    <p class="text-muted small">
                        <i class="fas fa-info-circle"></i> Use o botão "Representantes" para cadastrar múltiplos
                        representantes com emails para assinatura digital.
                    </p>
                    <hr class="my-2">
                    <h6 class="text-muted mb-3">Taxa</h6>
                    <p class="mb-1"><strong>Tipo de Taxa:</strong>
                        {{ $empresa->tipo_taxa ? ucfirst($empresa->tipo_taxa) : 'Não informado' }}
                    </p>
                    <p class="mb-1"><strong>Valor da Taxa:</strong>
                        @if ($empresa->tipo_taxa === 'fixa')
                            R$ {{ number_format($empresa->taxa_fixa, 2, ',', '.') }}
                        @elseif ($empresa->tipo_taxa === 'percentual')
                            {{ number_format($empresa->taxa_percentual, 2, ',', '.') }}%
                        @else
                            Não informado
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('empresas.edit', $empresa->id_empresa) }}" class="btn btn-info">Editar</a>
            <form action="{{ route('empresas.destroy', $empresa->id_empresa) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Excluir</button>
            </form>
        </div>
    </div>

    <!-- Modal: Gerenciar Representantes -->
    <div class="modal fade" id="representantesModal" tabindex="-1" aria-labelledby="representantesModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="representantesModalLabel">
                        <i class="fas fa-users me-2"></i>Representantes - {{ $empresa->nome_empresa }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle me-1"></i>
                        Cadastre os representantes que poderão assinar digitalmente os documentos via ZapSign.
                    </div>

                    <!-- Botão Adicionar -->
                    <div class="mb-3">
                        <button type="button" class="btn btn-success btn-sm" onclick="mostrarFormRepresentanteEmpresa()">
                            <i class="fas fa-plus me-1"></i> Adicionar Representante
                        </button>
                    </div>

                    <!-- Formulário de Cadastro (oculto inicialmente) -->
                    <div id="formRepresentanteEmpresa" style="display: none;" class="card mb-3">
                        <div class="card-body">
                            <form action="{{ route('representantes.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="representavel_type" value="App\Models\Empresa">
                                <input type="hidden" name="representavel_id" value="{{ $empresa->id_empresa }}">

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Nome <span class="text-danger">*</span></label>
                                        <input type="text" name="nome" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Cargo <span class="text-danger">*</span></label>
                                        <input type="text" name="cargo" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">E-mail <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">CPF (opcional)</label>
                                        <input type="text" name="cpf" class="form-control form-control-sm" maxlength="14"
                                            placeholder="000.000.000-00">
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-3">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-save me-1"></i> Salvar
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm"
                                        onclick="ocultarFormRepresentanteEmpresa()">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Lista de Representantes -->
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nome</th>
                                    <th>Cargo</th>
                                    <th>E-mail</th>
                                    <th style="width: 100px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($empresa->representantes as $rep)
                                    <tr id="row-rep-{{ $rep->id_representante }}">
                                        <td class="rep-nome">{{ $rep->nome }}</td>
                                        <td class="rep-cargo">{{ $rep->cargo }}</td>
                                        <td class="rep-email">{{ $rep->email }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-info btn-sm me-1"
                                                onclick="editarRepresentanteEmpresa({{ $rep->id_representante }}, '{{ $rep->nome }}', '{{ $rep->cargo }}', '{{ $rep->email }}', '{{ $rep->cpf ?? '' }}')"
                                                title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('representantes.destroy', $rep->id_representante) }}"
                                                method="POST" style="display: inline;"
                                                onsubmit="return confirm('Confirma exclusão?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            Nenhum representante cadastrado ainda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Lista de Locais -->
    <div class="modal fade" id="locaisModal" tabindex="-1" aria-labelledby="locaisModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="locaisModalLabel">Locais da Empresa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex gap-2 mb-3">
                        <button id="btnNovoLocal" class="btn btn-success">Adicionar Local</button>
                    </div>
                    <div id="listaLocaisContainer">
                        <div class="text-center py-3 text-muted" id="locaisLoading" style="display:none;">Carregando...
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead>
                                    <tr>
                                        <th class="w-75">Descrição</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="listaLocaisBody">
                                    <!-- Preenchido via JS -->
                                </tbody>
                            </table>
                        </div>
                        <div class="alert alert-info py-2" id="listaLocaisVazia" style="display:none;">Nenhum local
                            cadastrado.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Formulário de Local (Criar/Editar) -->
    <div class="modal fade" id="localFormModal" tabindex="-1" aria-labelledby="localFormModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="localFormModalLabel">Novo Local</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="localFormAlert" class="alert alert-danger py-2" style="display:none;"></div>
                    <form id="localForm">
                        <div class="mb-3">
                            <label for="descricaoLocal" class="form-label">Descrição</label>
                            <input type="text" class="form-control" id="descricaoLocal" maxlength="255" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="salvarLocalBtn">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Confirmar Exclusão -->
    <div class="modal fade" id="confirmDeleteLocalModal" tabindex="-1" aria-labelledby="confirmDeleteLocalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteLocalLabel">Excluir Local</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Tem certeza que deseja excluir o local: <strong id="deleteLocalDescricao"></strong>?</p>
                    <div id="deleteLocalAlert" class="alert alert-danger py-2 mt-2" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteLocalBtn">Excluir</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Config para JS (evita Blade dentro do script) -->
    <div id="locaisConfig" data-empresa-id="{{ $empresa->id_empresa }}" data-locais-base-url="{{ url('/locais') }}"
        style="display:none;"></div>

    @verbatim
        <script>
            (function () {
                const configEl = document.getElementById('locaisConfig');
                const empresaId = Number(configEl?.dataset.empresaId || 0);
                const locaisBaseUrl = configEl?.dataset.locaisBaseUrl || '/locais';
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                // Elementos principais
                const locaisModalEl = document.getElementById('locaisModal');
                const localFormModalEl = document.getElementById('localFormModal');
                const confirmDeleteLocalModalEl = document.getElementById('confirmDeleteLocalModal');

                function showModal(el) {
                    if (!el) return;
                    const modal = window.bootstrap ? window.bootstrap.Modal.getOrCreateInstance(el) : null;
                    modal && modal.show();
                }

                function hideModal(el) {
                    if (!el) return;
                    const modal = window.bootstrap ? window.bootstrap.Modal.getOrCreateInstance(el) : null;
                    modal && modal.hide();
                }

                // Campos e botões
                const listaBody = document.getElementById('listaLocaisBody');
                const loadingEl = document.getElementById('locaisLoading');
                const vazioEl = document.getElementById('listaLocaisVazia');
                const btnNovo = document.getElementById('btnNovoLocal');
                const formAlert = document.getElementById('localFormAlert');
                const formDescricao = document.getElementById('descricaoLocal');
                const btnSalvar = document.getElementById('salvarLocalBtn');
                const deleteDescricao = document.getElementById('deleteLocalDescricao');
                const deleteAlert = document.getElementById('deleteLocalAlert');
                const btnConfirmDelete = document.getElementById('confirmDeleteLocalBtn');

                let editandoId = null;
                let locaisCache = [];

                function setLoading(loading) {
                    if (!loadingEl || !listaBody) return;
                    loadingEl.style.display = loading ? 'block' : 'none';
                    if (loading) {
                        listaBody.innerHTML = '';
                        vazioEl && (vazioEl.style.display = 'none');
                    }
                }

                function renderLista(locais) {
                    if (!listaBody) return;
                    listaBody.innerHTML = '';
                    if (!locais || locais.length === 0) {
                        if (vazioEl) vazioEl.style.display = 'block';
                        return;
                    }
                    if (vazioEl) vazioEl.style.display = 'none';
                    locais.forEach(local => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                                        <td>${local.descricao ?? ''}</td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-outline-primary me-1" data-action="editar" data-id="${local.id_local}">Editar</button>
                                            <button class="btn btn-sm btn-outline-danger" data-action="excluir" data-id="${local.id_local}" data-desc="${local.descricao ?? ''}">Excluir</button>
                                        </td>
                                    `;
                        listaBody.appendChild(tr);
                    });
                }

                async function carregarLocais() {
                    try {
                        setLoading(true);
                        const params = new URLSearchParams();
                        params.set('empresa', String(empresaId));
                        const resp = await fetch(`${locaisBaseUrl}?${params.toString()}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (!resp.ok) throw new Error('Falha ao carregar locais');
                        const data = await resp.json();
                        locaisCache = Array.isArray(data) ? data : [];
                        renderLista(locaisCache);
                    } catch (e) {
                        if (listaBody) {
                            listaBody.innerHTML = '<tr><td colspan="2" class="text-danger">Erro ao carregar locais.</td></tr>';
                        }
                    } finally {
                        setLoading(false);
                    }
                }

                function limparFormulario() {
                    editandoId = null;
                    formDescricao && (formDescricao.value = '');
                    formAlert && (formAlert.style.display = 'none', formAlert.textContent = '');
                    const titulo = document.getElementById('localFormModalLabel');
                    if (titulo) titulo.textContent = 'Novo Local';
                }

                function abrirEdicao(local) {
                    editandoId = local.id_local;
                    if (formDescricao) formDescricao.value = local.descricao ?? '';
                    const titulo = document.getElementById('localFormModalLabel');
                    if (titulo) titulo.textContent = 'Editar Local';
                    formAlert && (formAlert.style.display = 'none', formAlert.textContent = '');
                    showModal(localFormModalEl);
                }

                async function salvarLocal() {
                    if (!formDescricao || !formDescricao.value.trim()) {
                        if (formAlert) {
                            formAlert.textContent = 'Descrição é obrigatória.';
                            formAlert.style.display = 'block';
                        }
                        return;
                    }
                    const payload = {
                        descricao: formDescricao.value.trim(),
                        fk_id_empresa: empresaId
                    };
                    const url = editandoId ? `${locaisBaseUrl}/${editandoId}` : `${locaisBaseUrl}`;
                    const method = editandoId ? 'PUT' : 'POST';
                    try {
                        const resp = await fetch(url, {
                            method,
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify(payload)
                        });
                        if (!resp.ok) {
                            const data = await resp.json().catch(() => ({}));
                            let msg = 'Erro ao salvar o local.';
                            if (data && data.errors) {
                                msg = Object.values(data.errors).flat().join(' ');
                            } else if (data && data.message) {
                                msg = data.message;
                            }
                            throw new Error(msg);
                        }
                        await carregarLocais();
                        hideModal(localFormModalEl);
                    } catch (e) {
                        if (formAlert) {
                            formAlert.textContent = e.message || 'Erro ao salvar o local.';
                            formAlert.style.display = 'block';
                        }
                    }
                }

                let deletandoId = null;
                function abrirExcluir(id, descricao) {
                    deletandoId = id;
                    deleteDescricao && (deleteDescricao.textContent = descricao || '');
                    deleteAlert && (deleteAlert.style.display = 'none', deleteAlert.textContent = '');
                    showModal(confirmDeleteLocalModalEl);
                }

                async function confirmarExclusao() {
                    if (!deletandoId) return;
                    try {
                        const resp = await fetch(`${locaisBaseUrl}/${deletandoId}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            }
                        });
                        if (!resp.ok) {
                            const data = await resp.json().catch(() => ({}));
                            const msg = (data && data.message) ? data.message : 'Não foi possível excluir o local.';
                            throw new Error(msg);
                        }
                        await carregarLocais();
                        hideModal(confirmDeleteLocalModalEl);
                    } catch (e) {
                        if (deleteAlert) {
                            deleteAlert.textContent = e.message || 'Erro ao excluir.';
                            deleteAlert.style.display = 'block';
                        }
                    } finally {
                        deletandoId = null;
                    }
                }

                // Eventos
                if (locaisModalEl) {
                    locaisModalEl.addEventListener('shown.bs.modal', () => {
                        carregarLocais();
                    });
                }
                btnNovo && btnNovo.addEventListener('click', () => { limparFormulario(); showModal(localFormModalEl); });
                btnSalvar && btnSalvar.addEventListener('click', salvarLocal);

                if (listaBody) {
                    listaBody.addEventListener('click', (e) => {
                        const target = e.target;
                        if (!(target instanceof HTMLElement)) return;
                        const action = target.getAttribute('data-action');
                        const id = target.getAttribute('data-id');
                        if (!action || !id) return;
                        const local = locaisCache.find(l => String(l.id_local) === String(id));
                        if (!local) return;
                        if (action === 'editar') {
                            abrirEdicao(local);
                        } else if (action === 'excluir') {
                            abrirExcluir(local.id_local, local.descricao);
                        }
                    });
                }

                btnConfirmDelete && btnConfirmDelete.addEventListener('click', confirmarExclusao);
            })();
        </script>
    @endverbatim

    <script>
        let modoEdicaoEmpresa = false;
        let idEdicaoEmpresa = null;

        function mostrarFormRepresentanteEmpresa() {
            modoEdicaoEmpresa = false;
            idEdicaoEmpresa = null;
            document.getElementById('formRepresentanteEmpresa').style.display = 'block';
            document.querySelector('#formRepresentanteEmpresa form').reset();
            document.querySelector('#formRepresentanteEmpresa form').action = '{{ route("representantes.store") }}';
            document.querySelector('#formRepresentanteEmpresa form input[name="_method"]')?.remove();
        }

        function ocultarFormRepresentanteEmpresa() {
            document.getElementById('formRepresentanteEmpresa').style.display = 'none';
        }

        function editarRepresentanteEmpresa(id, nome, cargo, email, cpf) {
            modoEdicaoEmpresa = true;
            idEdicaoEmpresa = id;

            const form = document.querySelector('#formRepresentanteEmpresa form');
            form.action = `/representantes/${id}`;

            // Adicionar método PUT se não existir
            if (!form.querySelector('input[name="_method"]')) {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);
            }

            // Preencher campos
            form.querySelector('input[name="nome"]').value = nome;
            form.querySelector('input[name="cargo"]').value = cargo;
            form.querySelector('input[name="email"]').value = email;
            form.querySelector('input[name="cpf"]').value = cpf || '';

            document.getElementById('formRepresentanteEmpresa').style.display = 'block';
            form.querySelector('input[name="nome"]').focus();
        }
    </script>
@endsection