@extends('layouts.main')

@section('title', 'Cadastrar Termo de Estágio')

@section('content')

    <h1>Adicionar Termo de Estágio</h1>
    <button onclick="window.NavigationHistory?.goBack('{{ route('termos.index') }}')" class="btn btn-secondary mb-3"
        title="Voltar para a página anterior com filtros preservados">Voltar</button>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Erro ao cadastrar termo:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $prefillEmpresaId = old('fk_id_empresa') ?? request('empresa_id');
        $prefillVagaId = old('fk_id_vaga') ?? request('vaga_id');
        $prefillEmpresaNome = $prefillEmpresaId ? optional($empresas->firstWhere('id_empresa', $prefillEmpresaId))->nome_empresa : '';
    @endphp

    <form action="{{ route('termos.store') }}" method="POST">
        @csrf
        @method('POST')
        <div class="row">
            <!-- Coluna 1 -->
            <div class="col-md-6">
                <div class="mb-3" style="position: relative;">
                    <label for="fk_id_empresa" class="form-label">Selecione a Unidade Concedente</label>
                    <input type="text" class="form-control" id="empresa_search" placeholder="Digite para buscar..."
                        autocomplete="off" value="{{ $prefillEmpresaNome }}">
                    <select class="form-control mt-2" id="fk_id_empresa" name="fk_id_empresa" size="5" required
                        data-prefill-empresa="{{ $prefillEmpresaId }}" data-prefill-vaga="{{ $prefillVagaId }}"
                        style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                        @foreach($empresas as $empresa)
                            <option value="{{ $empresa->id_empresa }}" @if((old('fk_id_empresa') ?? request('empresa_id')) == $empresa->id_empresa) selected @endif>
                                {{ $empresa->nome_empresa }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Novo campo: Seleção de vaga (aparece após escolher empresa) -->
                <div class="mb-3" id="vaga-container" style="position: relative; display: none;">
                    <label for="fk_id_vaga" class="form-label">Vincular à Vaga (Opcional)</label>
                    <input type="text" class="form-control" id="vaga_search" placeholder="Digite para buscar..."
                        autocomplete="off" disabled>
                    <div class="mt-2">
                        <select class="form-control" id="fk_id_vaga" name="fk_id_vaga" size="5"
                            style="display:none; width: 100%; background: #fff; border: 1px solid #ced4da;">
                            <option value="">Não vincular (preencher manualmente)</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <button type="button" class="btn btn-outline-info btn-sm" id="btnInfoVaga"
                            title="Informações da vaga" disabled>
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </div>
                    <small class="form-text text-muted">Selecione uma vaga para preencher automaticamente os campos, ou
                        deixe em branco para preencher manualmente.</small>
                </div>

                <div class="mb-3" style="position: relative;">
                    <label for="fk_id_escola" class="form-label">Selecione a Instituição de Ensino</label>
                    <input type="text" class="form-control" id="escola_search" placeholder="Digite para buscar..."
                        autocomplete="off">
                    <select class="form-control mt-2" id="fk_id_escola" name="fk_id_escola" size="5" required
                        style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                        <option value="">Escolha uma instituição de ensino</option>
                        @foreach($escolas as $escola)
                            <option value="{{ $escola->id_escola }}">{{ $escola->nome_escola }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="position: relative;">
                    <label for="fk_id_estagiario">Selecione o Estagiário</label>
                    <input type="text" class="form-control @error('fk_id_estagiario') is-invalid @enderror"
                        id="estagiario_search" placeholder="Digite para buscar..." autocomplete="off"
                        value="@if(isset($id_estagiario) && $id_estagiario){{ optional($estagiarios->firstWhere('id_estagiario', $id_estagiario))->nome_estagiario }}@endif">

                    <select class="form-control mt-2 @error('fk_id_estagiario') is-invalid @enderror" id="fk_id_estagiario"
                        name="fk_id_estagiario" size="5" required
                        style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                        <option value="">Escolha um estagiário</option>
                        @foreach($estagiarios as $estagiario)
                            <option value="{{ $estagiario->id_estagiario }}" @if(isset($id_estagiario) && $id_estagiario == $estagiario->id_estagiario) selected
                            @elseif(old('fk_id_estagiario') == $estagiario->id_estagiario) selected @endif>
                                {{ $estagiario->nome_estagiario }}
                            </option>
                        @endforeach
                    </select>
                    @error('fk_id_estagiario')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group" style="position: relative;">
                    <label for="fk_id_supervisor_fixo">Selecione o Supervisor</label>
                    <input type="text" class="form-control" id="supervisor_search" placeholder="Digite para buscar..."
                        autocomplete="off">
                    <select class="form-control mt-2" id="fk_id_supervisor_fixo" name="fk_id_supervisor_fixo" size="5"
                        required
                        style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                        <option value="">Escolha um supervisor</option>
                        @foreach($supervisores as $supervisor)
                            <option value="{{ $supervisor->id_supervisor }}">{{ $supervisor->nome_supervisor }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="desc_atividades_fixo">Atividades</label>
                    <textarea class="form-control" id="desc_atividades_fixo" name="desc_atividades_fixo"
                        rows="4"></textarea>
                </div>
            </div>



            <!-- Coluna 2 -->
            <div class="col-md-6">

                <div class="form-group">
                    <label for="nome_orientador">Nome do Orientador</label>
                    <input type="text" class="form-control" id="nome_orientador_fixo" name="nome_orientador_fixo" required>
                </div>

                <div class="form-group">
                    <label for="cargo_orientador">Cargo do Orientador</label>
                    <input type="text" class="form-control" id="cargo_orientador_fixo" name="cargo_orientador_fixo"
                        required>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="data_inicio_estagio">Data de Início do Estágio</label>
                        <input type="date" class="form-control" id="data_inicio_estagio" name="data_inicio_estagio"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label for="data_fim_estagio">Data de Término do Estágio</label>
                        <input type="date" class="form-control" id="data_fim_estagio_fixo" name="data_fim_estagio_fixo"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="horario">Horário</label>
                    <textarea class="form-control" id="horario_fixo" name="horario_fixo" rows="2" required></textarea>
                </div>

                <!-- Seleção de Local (aparece após escolher a Unidade Concedente) -->
                <div class="mb-3" style="position: relative;">
                    <label for="fk_id_local" class="form-label">Selecione o Local</label>
                    <input type="text" class="form-control" id="local_search" placeholder="Digite para buscar..."
                        autocomplete="off" disabled>
                    <select class="form-control mt-2" id="fk_id_local" name="fk_id_local" size="5"
                        style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                        <option value="">Escolha um local</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="lotacao">Lotação</label>
                    <textarea class="form-control" id="lotacao" name="lotacao" rows="2" required></textarea>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="valor_bolsa_fixo">Valor da Bolsa</label>
                        <input type="text" class="form-control" id="valor_bolsa_fixo" name="valor_bolsa_fixo" required
                            title="" min="0">
                    </div>
                    <div class="col-md-6">
                        <label for="auxilio_transporte_fixo">Valor do Auxílio Transporte</label>
                        <input type="text" class="form-control" id="auxilio_transporte_fixo" name="auxilio_transporte_fixo"
                            title="" min="0">
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary"
                        style="margin-top: 10px; width: 50%; margin-left: 25%; margin-right: 25%">Salvar</button>
                </div>

            </div>
        </div>

        <input type="hidden" id="valor_bolsa" name="valor_bolsa">
        <input type="hidden" id="fk_id_supervisor" name="fk_id_supervisor">
        <input type="hidden" id="auxilio_transporte" name="auxilio_transporte">
        <input type="hidden" id="nome_orientador" name="nome_orientador">
        <input type="hidden" id="cargo_orientador" name="cargo_orientador">
        <input type="hidden" id="data_fim_estagio" name="data_fim_estagio">
        <input type="hidden" id="horario" name="horario">
        <input type="hidden" id="desc_atividades" name="desc_atividades">
        <input type="hidden" id="fk_id_local_fixo" name="fk_id_local_fixo">
        <input type="hidden" id="lotacao_fixo" name="lotacao_fixo">

    </form>

    <script>

        document.getElementById('valor_bolsa_fixo').addEventListener('input', function () {
            document.getElementById('valor_bolsa').value = this.value;
        });

        document.getElementById('fk_id_supervisor_fixo').addEventListener('input', function () {
            document.getElementById('fk_id_supervisor').value = this.value;
        });

        document.getElementById('auxilio_transporte_fixo').addEventListener('input', function () {
            document.getElementById('auxilio_transporte').value = this.value;
        });

        document.getElementById('nome_orientador_fixo').addEventListener('input', function () {
            document.getElementById('nome_orientador').value = this.value;
        });

        document.getElementById('cargo_orientador_fixo').addEventListener('input', function () {
            document.getElementById('cargo_orientador').value = this.value;
        });

        document.getElementById('data_fim_estagio_fixo').addEventListener('input', function () {
            document.getElementById('data_fim_estagio').value = this.value;
        });

        document.getElementById('horario_fixo').addEventListener('input', function () {
            document.getElementById('horario').value = this.value;
        });

        document.getElementById('desc_atividades_fixo').addEventListener('input', function () {
            document.getElementById('desc_atividades').value = this.value;
        });

        // Sincronizar campos de local e lotação
        const localSelect = document.getElementById('fk_id_local');
        const lotacaoInput = document.getElementById('lotacao');

        if (localSelect) {
            localSelect.addEventListener('change', function () {
                document.getElementById('fk_id_local_fixo').value = this.value;
            });
        }

        if (lotacaoInput) {
            lotacaoInput.addEventListener('input', function () {
                document.getElementById('lotacao_fixo').value = this.value;
            });
        }
    </script>

    <script>
        function setupFilter(searchId, selectId) {
            const searchInput = document.getElementById(searchId);
            const select = document.getElementById(selectId);
            const originalOptions = Array.from(select.options);

            searchInput.addEventListener('focus', function () {
                select.style.display = 'block';
                setTimeout(() => {
                    searchInput.select();
                }, 0);
            });

            searchInput.addEventListener('input', function () {
                const value = this.value.toLowerCase();
                select.innerHTML = '';
                // Sempre mantém o primeiro option (placeholder)
                if (originalOptions.length > 0 && originalOptions[0].value === '') {
                    select.appendChild(originalOptions[0].cloneNode(true));
                }
                originalOptions.slice(1).forEach(option => {
                    if (option.text.toLowerCase().includes(value)) {
                        select.appendChild(option.cloneNode(true));
                    }
                });
                select.style.display = 'block';
            });

            select.addEventListener('change', function () {
                const selected = select.options[select.selectedIndex];
                searchInput.value = selected.text;
                select.style.display = 'none';
            });

            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !select.contains(e.target)) {
                    select.style.display = 'none';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            setupFilter('estagiario_search', 'fk_id_estagiario');
            setupFilter('empresa_search', 'fk_id_empresa');
            setupFilter('escola_search', 'fk_id_escola');
            setupFilter('supervisor_search', 'fk_id_supervisor_fixo');

            // Lógica de Locais dependente da Unidade Concedente
            const empresaSelect = document.getElementById('fk_id_empresa');
            const localSearch = document.getElementById('local_search');
            const localSelect = document.getElementById('fk_id_local');
            let locaisCache = [];

            // Lógica de Vagas
            const vagaContainer = document.getElementById('vaga-container');
            const vagaSearch = document.getElementById('vaga_search');
            const vagaSelect = document.getElementById('fk_id_vaga');
            const btnInfoVaga = document.getElementById('btnInfoVaga');
            let vagasCache = [];
            let camposOriginais = {}; // Para armazenar valores originais antes de preencher pela vaga

            function showVagaSelect() {
                vagaSelect.style.display = 'block';
            }
            function hideVagaSelect() {
                vagaSelect.style.display = 'none';
            }
            function populateVagaOptions(filter = '') {
                const value = (filter || '').toLowerCase();
                vagaSelect.innerHTML = '';
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = 'Não vincular (preencher manualmente)';
                vagaSelect.appendChild(placeholder);
                vagasCache
                    .filter(v => !value || (String(v.numero_vaga || '') + ' ' + String(v.atividades || '')).toLowerCase().includes(value))
                    .forEach(v => {
                        const opt = document.createElement('option');
                        opt.value = v.id_vaga;
                        opt.textContent = `${v.numero_vaga} - ${v.atividades.substring(0, 50)}...`;
                        opt.dataset.vaga = JSON.stringify(v);
                        vagaSelect.appendChild(opt);
                    });
            }
            function resetVagaField() {
                vagasCache = [];
                vagaSearch.value = '';
                vagaSearch.disabled = true;
                vagaSelect.innerHTML = '<option value="">Não vincular (preencher manualmente)</option>';
                hideVagaSelect();
                vagaContainer.style.display = 'none';
            }
            async function loadVagasByEmpresa(idEmpresa, preselectVagaId = null) {
                resetVagaField();
                if (!idEmpresa) return;
                try {
                    const resp = await fetch(`/api/vagas-por-empresa?empresa_id=${encodeURIComponent(idEmpresa)}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (!resp.ok) throw new Error('Falha ao carregar vagas');
                    const data = await resp.json();
                    vagasCache = Array.isArray(data) ? data : [];
                    if (vagasCache.length > 0) {
                        vagaContainer.style.display = 'block';
                        vagaSearch.disabled = false;
                        populateVagaOptions('');
                        if (preselectVagaId) {
                            const opt = vagaSelect.querySelector(`option[value="${preselectVagaId}"]`);
                            if (opt) {
                                vagaSelect.value = preselectVagaId;
                                vagaSelect.dispatchEvent(new Event('change'));
                            }
                        }
                    }
                } catch (e) {
                    console.error('Erro ao carregar vagas:', e);
                }
            }

            function preencherCamposComVaga(vaga) {
                // Verificar se vaga está expirada
                if (vaga.expirada) {
                    alert('ATENÇÃO: Esta vaga está expirada (data de término passou). Não é recomendado vinculá-la.');
                }

                // Armazenar valores originais
                camposOriginais = {
                    desc_atividades_fixo: document.getElementById('desc_atividades_fixo').value,
                    nome_orientador_fixo: document.getElementById('nome_orientador_fixo').value,
                    cargo_orientador_fixo: document.getElementById('cargo_orientador_fixo').value,
                    data_inicio_estagio: document.getElementById('data_inicio_estagio').value,
                    data_fim_estagio_fixo: document.getElementById('data_fim_estagio_fixo').value,
                    horario_fixo: document.getElementById('horario_fixo').value,
                    lotacao: document.getElementById('lotacao').value,
                    valor_bolsa_fixo: document.getElementById('valor_bolsa_fixo').value,
                    auxilio_transporte_fixo: document.getElementById('auxilio_transporte_fixo').value,
                    fk_id_local: document.getElementById('fk_id_local').value,
                };

                // Preencher campos com dados da vaga
                document.getElementById('desc_atividades_fixo').value = vaga.atividades;
                // Campos de orientador permanecem livres (não são preenchidos pela vaga)
                document.getElementById('data_inicio_estagio').value = vaga.data_inicio;
                document.getElementById('data_fim_estagio_fixo').value = vaga.data_termino;
                document.getElementById('horario_fixo').value = vaga.horario;
                document.getElementById('lotacao').value = vaga.lotacao;
                function formatBRL(value) {
                    if (value === null || value === undefined || value === '') return '';
                    const num = typeof value === 'number' ? value : parseFloat(String(value).replace(',', '.'));
                    if (isNaN(num)) return '';
                    return num.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                }
                document.getElementById('valor_bolsa_fixo').value = formatBRL(vaga.valor_bolsa);
                document.getElementById('auxilio_transporte_fixo').value = formatBRL(vaga.valor_auxilio_transporte);

                // Selecionar local automaticamente
                if (vaga.fk_id_local) {
                    localSelect.value = vaga.fk_id_local;
                    const selectedLocal = localSelect.options[localSelect.selectedIndex];
                    if (selectedLocal) {
                        localSearch.value = selectedLocal.text;
                    }
                }

                // Selecionar supervisor automaticamente (se existir na vaga)
                const supervisorSelect = document.getElementById('fk_id_supervisor_fixo');
                const supervisorSearch = document.getElementById('supervisor_search');
                if (vaga.supervisor && vaga.supervisor.id_supervisor) {
                    if (supervisorSelect) {
                        supervisorSelect.value = vaga.supervisor.id_supervisor;
                        supervisorSelect.dispatchEvent(new Event('input'));
                    }
                    if (supervisorSearch) {
                        supervisorSearch.value = vaga.supervisor.nome_supervisor || '';
                    }
                }

                // Disparar eventos para atualizar campos hidden
                document.getElementById('desc_atividades_fixo').dispatchEvent(new Event('input'));
                document.getElementById('nome_orientador_fixo').dispatchEvent(new Event('input'));
                document.getElementById('cargo_orientador_fixo').dispatchEvent(new Event('input'));
                document.getElementById('data_fim_estagio_fixo').dispatchEvent(new Event('input'));
                document.getElementById('horario_fixo').dispatchEvent(new Event('input'));
                document.getElementById('valor_bolsa_fixo').dispatchEvent(new Event('input'));
                document.getElementById('auxilio_transporte_fixo').dispatchEvent(new Event('input'));
                document.getElementById('lotacao').dispatchEvent(new Event('input'));
                document.getElementById('fk_id_local').dispatchEvent(new Event('change'));

                // Tornar campos readonly (exceto estagiário, escola e supervisor)
                desabilitarCamposVaga(true);
            }

            function limparCamposVaga() {
                // Restaurar valores originais ou limpar
                Object.keys(camposOriginais).forEach(key => {
                    const elem = document.getElementById(key);
                    if (elem) elem.value = camposOriginais[key] || '';
                });

                // Habilitar campos novamente
                desabilitarCamposVaga(false);
            }

            function desabilitarCamposVaga(desabilitar) {
                const campos = [
                    'desc_atividades_fixo',
                    // 'nome_orientador_fixo' e 'cargo_orientador_fixo' permanecem sempre editáveis
                    'data_inicio_estagio', 'data_fim_estagio_fixo', 'horario_fixo',
                    'lotacao', 'valor_bolsa_fixo', 'auxilio_transporte_fixo', 'local_search'
                ];
                campos.forEach(id => {
                    const elem = document.getElementById(id);
                    if (elem) elem.readOnly = desabilitar;
                });
                localSearch.disabled = desabilitar;
            }

            function showLocalSelect() {
                localSelect.style.display = 'block';
            }
            function hideLocalSelect() {
                localSelect.style.display = 'none';
            }
            function populateLocalOptions(filter = '') {
                const value = (filter || '').toLowerCase();
                localSelect.innerHTML = '';
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = 'Escolha um local';
                localSelect.appendChild(placeholder);
                locaisCache
                    .filter(l => !value || (String(l.descricao || '')).toLowerCase().includes(value))
                    .forEach(l => {
                        const opt = document.createElement('option');
                        opt.value = l.id_local;
                        opt.textContent = l.descricao || '';
                        localSelect.appendChild(opt);
                    });
            }
            function resetLocalField() {
                locaisCache = [];
                localSearch.value = '';
                localSearch.disabled = true;
                localSelect.innerHTML = '<option value="">Escolha um local</option>';
                hideLocalSelect();
            }
            async function loadLocaisByEmpresa(idEmpresa) {
                resetLocalField();
                if (!idEmpresa) return;
                try {
                    const resp = await fetch(`/locais?empresa=${encodeURIComponent(idEmpresa)}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (!resp.ok) throw new Error('Falha ao carregar locais');
                    const data = await resp.json();
                    locaisCache = Array.isArray(data) ? data : [];
                    localSearch.disabled = false;
                    populateLocalOptions('');
                } catch (e) {
                    // mantém desabilitado em caso de erro
                }
            }

            if (empresaSelect) {
                empresaSelect.addEventListener('change', function () {
                    const id = this.value;
                    loadLocaisByEmpresa(id);
                    loadVagasByEmpresa(id);
                    localSelect.value = '';
                    vagaSelect.value = '';
                    limparCamposVaga();
                });
                // Caso já venha selecionado (ex.: edição ou fluxo pré-preenchido)
                const prefillEmpresaId = empresaSelect.dataset.prefillEmpresa;
                const prefillVagaId = empresaSelect.dataset.prefillVaga;
                if (prefillEmpresaId) {
                    empresaSelect.value = prefillEmpresaId;
                    const selected = empresaSelect.options[empresaSelect.selectedIndex];
                    if (selected) {
                        const empresaSearch = document.getElementById('empresa_search');
                        if (empresaSearch) empresaSearch.value = selected.text;
                    }
                    loadLocaisByEmpresa(prefillEmpresaId);
                    loadVagasByEmpresa(prefillEmpresaId, prefillVagaId);
                } else if (empresaSelect.value) {
                    loadLocaisByEmpresa(empresaSelect.value);
                    loadVagasByEmpresa(empresaSelect.value);
                } else {
                    resetLocalField();
                    resetVagaField();
                }
            }

            if (localSearch) {
                localSearch.addEventListener('focus', function () {
                    showLocalSelect();
                    setTimeout(() => localSearch.select(), 0);
                });
                localSearch.addEventListener('input', function () {
                    populateLocalOptions(this.value);
                    showLocalSelect();
                });
            }
            if (localSelect) {
                localSelect.addEventListener('change', function () {
                    const selected = localSelect.options[localSelect.selectedIndex];
                    if (selected) {
                        localSearch.value = selected.text;
                    }
                    hideLocalSelect();
                });
            }
            document.addEventListener('click', function (e) {
                if (!localSearch.contains(e.target) && !localSelect.contains(e.target)) {
                    hideLocalSelect();
                }
            });

            // Eventos para campo de vaga
            if (vagaSearch) {
                vagaSearch.addEventListener('focus', function () {
                    showVagaSelect();
                    setTimeout(() => vagaSearch.select(), 0);
                });
                vagaSearch.addEventListener('input', function () {
                    populateVagaOptions(this.value);
                    showVagaSelect();
                });
            }
            if (vagaSelect) {
                vagaSelect.addEventListener('change', async function () {
                    const selected = vagaSelect.options[vagaSelect.selectedIndex];
                    if (selected && selected.value) {
                        vagaSearch.value = selected.text;
                        const vagaData = JSON.parse(selected.dataset.vaga || '{}');
                        preencherCamposComVaga(vagaData);
                        // Verificar dados do estagiário antes de mostrar botão
                        try {
                            const url = '{{ route('api.vagas.info', ['id' => ':id']) }}'.replace(':id', selected.value);
                            const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
                            if (resp.ok) {
                                const data = await resp.json();
                                const hasContacts = !!(data.tem_estagiario_definido && (data.nome_estagiario || data.contato_whatsapp || data.contato_email));
                                if (btnInfoVaga) btnInfoVaga.disabled = !hasContacts;
                            } else {
                                if (btnInfoVaga) btnInfoVaga.disabled = true;
                            }
                        } catch (e) {
                            if (btnInfoVaga) btnInfoVaga.disabled = true;
                        }
                    } else {
                        vagaSearch.value = 'Não vincular (preencher manualmente)';
                        limparCamposVaga();
                        if (btnInfoVaga) btnInfoVaga.disabled = true;
                    }
                    hideVagaSelect();
                });
            }
            document.addEventListener('click', function (e) {
                if (vagaSearch && vagaSelect && !vagaSearch.contains(e.target) && !vagaSelect.contains(e.target)) {
                    hideVagaSelect();
                }
            });

            // Abrir modal com informações da vaga/estagiário
            if (btnInfoVaga) {
                btnInfoVaga.addEventListener('click', async function () {
                    const selected = vagaSelect.options[vagaSelect.selectedIndex];
                    if (!selected || !selected.value) return;
                    try {
                        const vagaId = selected.value;
                        const url = '{{ route('api.vagas.info', ['id' => ':id']) }}'.replace(':id', vagaId);
                        const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
                        if (!resp.ok) throw new Error('Falha ao obter informações da vaga');
                        const data = await resp.json();

                        document.getElementById('infoNomeEstagiario').textContent = data.nome_estagiario || '-';
                        document.getElementById('infoWhatsapp').textContent = data.contato_whatsapp || '-';
                        document.getElementById('infoEmail').textContent = data.contato_email || '-';

                        // Habilita/desabilita botões baseado nos dados
                        const btnWhatsapp = document.getElementById('btnWhatsapp');
                        const btnCopiarEmail = document.getElementById('btnCopiarEmail');

                        if (data.contato_whatsapp && data.contato_whatsapp !== '-') {
                            btnWhatsapp.disabled = false;
                            btnWhatsapp.onclick = function () {
                                const numero = data.contato_whatsapp.replace(/\D/g, '');
                                const mensagem = encodeURIComponent('Olá! Identificamos que você foi selecionado para uma vaga de estágio. Para prosseguir, pedimos que se cadastre no SIGE através do link: https://sigeb.br/novo-estagiario-ajax.');
                                window.open(`https://wa.me/${numero}?text=${mensagem}`, '_blank');
                            };
                        } else {
                            btnWhatsapp.disabled = true;
                        }

                        if (data.contato_email && data.contato_email !== '-') {
                            btnCopiarEmail.disabled = false;
                            btnCopiarEmail.onclick = function () {
                                navigator.clipboard.writeText(data.contato_email).then(() => {
                                    const originalHtml = btnCopiarEmail.innerHTML;
                                    btnCopiarEmail.innerHTML = '<i class="fas fa-check"></i>';
                                    btnCopiarEmail.classList.remove('btn-primary');
                                    btnCopiarEmail.classList.add('btn-success');
                                    setTimeout(() => {
                                        btnCopiarEmail.innerHTML = originalHtml;
                                        btnCopiarEmail.classList.remove('btn-success');
                                        btnCopiarEmail.classList.add('btn-primary');
                                    }, 2000);
                                }).catch(err => {
                                    alert('Erro ao copiar email: ' + err);
                                });
                            };
                        } else {
                            btnCopiarEmail.disabled = true;
                        }

                        const modal = new bootstrap.Modal(document.getElementById('modalInfoVaga'));
                        modal.show();
                    } catch (e) {
                        console.error(e);
                    }
                });
            }
        });
    </script>

    <!-- Modal Info Vaga / Estagiário -->
    <div class="modal fade" id="modalInfoVaga" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Informações da Vaga</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="infoVagaBody">
                        <p><strong>Estagiário:</strong> <span id="infoNomeEstagiario">-</span></p>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <p class="mb-0"><strong>WhatsApp:</strong> <span id="infoWhatsapp">-</span></p>
                            <button type="button" class="btn btn-sm btn-success" id="btnWhatsapp" disabled
                                title="Abrir WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <p class="mb-0"><strong>Email:</strong> <span id="infoEmail">-</span></p>
                            <button type="button" class="btn btn-sm btn-primary" id="btnCopiarEmail" disabled
                                title="Copiar email">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>

                        <hr>
                        <p class="text-muted mb-0">Use estes contatos para solicitar que o estagiário se cadastre no SIGE e
                            liberar a geração do termo.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

@endsection