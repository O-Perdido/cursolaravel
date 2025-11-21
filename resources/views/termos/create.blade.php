@extends('layouts.main')

@section('title', 'Cadastrar Termo de Estágio')

@section('content')

    <h1>Adicionar Termo de Estágio</h1>
    <a href="{{ route('termos.index') }}" class="btn btn-secondary mb-3">Voltar</a>
    <form action="{{ route('termos.store') }}" method="POST">
        @csrf
        @method('POST')
        <div class="row">
            <!-- Coluna 1 -->
            <div class="col-md-6">
                <div class="form-group" style="position: relative;">
                    <label for="fk_id_estagiario">Selecione o Estagiário</label>
                    <input type="text" class="form-control" id="estagiario_search" placeholder="Digite para buscar..."
                        autocomplete="off"
                        value="@if(isset($id_estagiario) && $id_estagiario){{ optional($estagiarios->firstWhere('id_estagiario', $id_estagiario))->nome_estagiario }}@endif">

                    <select class="form-control mt-2" id="fk_id_estagiario" name="fk_id_estagiario" size="5" required
                        style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                        <option value="">Escolha um estagiário</option>
                        @foreach($estagiarios as $estagiario)
                            <option value="{{ $estagiario->id_estagiario }}" @if(isset($id_estagiario) && $id_estagiario == $estagiario->id_estagiario) selected @endif>
                                {{ $estagiario->nome_estagiario }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3" style="position: relative;">
                    <label for="fk_id_empresa" class="form-label">Selecione a Unidade Concedente</label>
                    <input type="text" class="form-control" id="empresa_search" placeholder="Digite para buscar..."
                        autocomplete="off">
                    <select class="form-control mt-2" id="fk_id_empresa" name="fk_id_empresa" size="5" required
                        style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                        @foreach($empresas as $empresa)
                            <option value="{{ $empresa->id_empresa }}">{{ $empresa->nome_empresa }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Novo campo: Seleção de vaga (aparece após escolher empresa) -->
                <div class="mb-3" id="vaga-container" style="position: relative; display: none;">
                    <label for="fk_id_vaga" class="form-label">Vincular à Vaga (Opcional)</label>
                    <input type="text" class="form-control" id="vaga_search" placeholder="Digite para buscar..."
                        autocomplete="off" disabled>
                    <select class="form-control mt-2" id="fk_id_vaga" name="fk_id_vaga" size="5"
                        style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                        <option value="">Não vincular (preencher manualmente)</option>
                    </select>
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
            async function loadVagasByEmpresa(idEmpresa) {
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
                };

                // Preencher campos com dados da vaga
                document.getElementById('desc_atividades_fixo').value = vaga.atividades;
                document.getElementById('nome_orientador_fixo').value = vaga.nome_orientador;
                document.getElementById('cargo_orientador_fixo').value = vaga.cargo_orientador;
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

                // Disparar eventos para atualizar campos hidden
                document.getElementById('desc_atividades_fixo').dispatchEvent(new Event('input'));
                document.getElementById('nome_orientador_fixo').dispatchEvent(new Event('input'));
                document.getElementById('cargo_orientador_fixo').dispatchEvent(new Event('input'));
                document.getElementById('data_fim_estagio_fixo').dispatchEvent(new Event('input'));
                document.getElementById('horario_fixo').dispatchEvent(new Event('input'));
                document.getElementById('valor_bolsa_fixo').dispatchEvent(new Event('input'));
                document.getElementById('auxilio_transporte_fixo').dispatchEvent(new Event('input'));

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
                    'desc_atividades_fixo', 'nome_orientador_fixo', 'cargo_orientador_fixo',
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
                if (empresaSelect.value) {
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
                vagaSelect.addEventListener('change', function () {
                    const selected = vagaSelect.options[vagaSelect.selectedIndex];
                    if (selected && selected.value) {
                        vagaSearch.value = selected.text;
                        const vagaData = JSON.parse(selected.dataset.vaga || '{}');
                        preencherCamposComVaga(vagaData);
                    } else {
                        vagaSearch.value = 'Não vincular (preencher manualmente)';
                        limparCamposVaga();
                    }
                    hideVagaSelect();
                });
            }
            document.addEventListener('click', function (e) {
                if (vagaSearch && vagaSelect && !vagaSearch.contains(e.target) && !vagaSelect.contains(e.target)) {
                    hideVagaSelect();
                }
            });
        });
    </script>

@endsection