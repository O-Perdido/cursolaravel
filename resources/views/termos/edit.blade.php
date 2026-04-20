@extends('layouts.main')

@section('title', 'Editar Termo de Estagio')

@section('content')

    <h1>Editar Termo de Estagio</h1>
    <a href="{{ route('termos.show', $termo->id_termo) }}" class="btn btn-secondary mb-3"
        title="Voltar para os detalhes do termo">Voltar</a>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Erro ao editar termo:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $empresaSelecionadaId = old('fk_id_empresa', $termo->fk_id_empresa);
        $empresaSelecionadaNome = $empresaSelecionadaId ? optional($empresas->firstWhere('id_empresa', $empresaSelecionadaId))->nome_empresa : '';
        $escolaSelecionadaId = old('fk_id_escola', $termo->fk_id_escola);
        $estagiarioSelecionadoId = old('fk_id_estagiario', $termo->fk_id_estagiario);
        $supervisorSelecionadoId = old('fk_id_supervisor_fixo', $termo->fk_id_supervisor_fixo);
        $vagaSelecionadaId = old('fk_id_vaga', $termo->fk_id_vaga);
        $localSelecionadoId = old('fk_id_local', $termo->fk_id_local);
        $localSelecionadoNome = $termo->local ? $termo->local->descricao : '';
        if (old('fk_id_local') && (int) old('fk_id_local') !== (int) $termo->fk_id_local) {
            $localSelecionadoNome = '';
        }

        $valorBolsaFixo = old('valor_bolsa_fixo');
        if ($valorBolsaFixo === null || $valorBolsaFixo === '') {
            $valorBolsaFixo = $termo->valor_bolsa_fixo !== null ? number_format($termo->valor_bolsa_fixo, 2, ',', '.') : '';
        }
        $auxilioTransporteFixo = old('auxilio_transporte_fixo');
        if ($auxilioTransporteFixo === null || $auxilioTransporteFixo === '') {
            $auxilioTransporteFixo = $termo->auxilio_transporte_fixo !== null ? number_format($termo->auxilio_transporte_fixo, 2, ',', '.') : '';
        }
        $valorBolsa = old('valor_bolsa', $valorBolsaFixo);
        $auxilioTransporte = old('auxilio_transporte', $auxilioTransporteFixo);

        $dataInicio = $termo->data_inicio_estagio ? $termo->data_inicio_estagio->format('Y-m-d') : '';
        $dataFimFixo = $termo->data_fim_estagio_fixo ? $termo->data_fim_estagio_fixo->format('Y-m-d') : '';
        $dataFim = $termo->data_fim_estagio ? $termo->data_fim_estagio->format('Y-m-d') : '';
    @endphp

    <form id="termo-edit-form" action="{{ route('termos.update', $termo->id_termo) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Coluna 1 -->
            <div class="col-md-6">
                <div class="mb-3" style="position: relative;">
                    <label for="fk_id_empresa" class="form-label">Selecione a Unidade Concedente</label>
                    <input type="text" class="form-control" id="empresa_search" placeholder="Digite para buscar..."
                        autocomplete="off" value="{{ $empresaSelecionadaNome }}">
                    <select class="form-control mt-2" id="fk_id_empresa" name="fk_id_empresa" size="5" required
                        data-prefill-empresa="{{ $empresaSelecionadaId }}" data-prefill-vaga="{{ $vagaSelecionadaId }}"
                        style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                        @foreach($empresas as $empresa)
                            <option value="{{ $empresa->id_empresa }}" @if($empresaSelecionadaId == $empresa->id_empresa) selected
                            @endif>
                                {{ $empresa->nome_empresa }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Novo campo: Seleção de vaga (aparece após escolher empresa) -->
                {{-- Campo de vaga removido: não é mais possível editar ou alterar a vaga vinculada neste formulário. --}}

                <div class="mb-3" style="position: relative;">
                    <label for="fk_id_escola" class="form-label">Selecione a Instituicao de Ensino</label>
                    <input type="text" class="form-control" id="escola_search" placeholder="Digite para buscar..."
                        autocomplete="off"
                        value="{{ optional($escolas->firstWhere('id_escola', $escolaSelecionadaId))->nome_escola }}">
                    <select class="form-control mt-2" id="fk_id_escola" name="fk_id_escola" size="5" required
                        style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                        <option value="">Escolha uma instituicao de ensino</option>
                        @foreach($escolas as $escola)
                            <option value="{{ $escola->id_escola }}" @if($escolaSelecionadaId == $escola->id_escola) selected
                            @endif>
                                {{ $escola->nome_escola }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="position: relative;">
                    <label for="fk_id_estagiario">Selecione o Estagiario</label>
                    <input type="text" class="form-control @error('fk_id_estagiario') is-invalid @enderror"
                        id="estagiario_search" placeholder="Digite para buscar..." autocomplete="off"
                        value="{{ optional($estagiarios->firstWhere('id_estagiario', $estagiarioSelecionadoId))->nome_estagiario }}">

                    <select class="form-control mt-2 @error('fk_id_estagiario') is-invalid @enderror" id="fk_id_estagiario"
                        name="fk_id_estagiario" size="5" required
                        style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                        <option value="">Escolha um estagiario</option>
                        @foreach($estagiarios as $estagiario)
                            <option value="{{ $estagiario->id_estagiario }}"
                                @if($estagiarioSelecionadoId == $estagiario->id_estagiario) selected @endif>
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
                        autocomplete="off"
                        value="{{ optional($supervisores->firstWhere('id_supervisor', $supervisorSelecionadoId))->nome_supervisor }}">
                    <select class="form-control mt-2" id="fk_id_supervisor_fixo" name="fk_id_supervisor_fixo" size="5"
                        required
                        style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                        <option value="">Escolha um supervisor</option>
                        @foreach($supervisores as $supervisor)
                            <option value="{{ $supervisor->id_supervisor }}"
                                @if($supervisorSelecionadoId == $supervisor->id_supervisor) selected @endif>
                                {{ $supervisor->nome_supervisor }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="desc_atividades_fixo">Atividades</label>
                    <textarea class="form-control" id="desc_atividades_fixo" name="desc_atividades_fixo"
                        rows="4">{{ old('desc_atividades_fixo', $termo->desc_atividades_fixo) }}</textarea>
                </div>
            </div>

            <!-- Coluna 2 -->
            <div class="col-md-6">

                <div class="form-group">
                    <label for="nome_orientador">Nome do Orientador</label>
                    <input type="text" class="form-control" id="nome_orientador_fixo" name="nome_orientador_fixo" required
                        value="{{ old('nome_orientador_fixo', $termo->nome_orientador_fixo) }}">
                </div>

                <div class="form-group">
                    <label for="cargo_orientador">Cargo do Orientador</label>
                    <input type="text" class="form-control" id="cargo_orientador_fixo" name="cargo_orientador_fixo" required
                        value="{{ old('cargo_orientador_fixo', $termo->cargo_orientador_fixo) }}">
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="data_inicio_estagio">Data de Inicio do Estagio</label>
                        <input type="date" class="form-control" id="data_inicio_estagio" name="data_inicio_estagio" required
                            value="{{ old('data_inicio_estagio', $dataInicio) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="data_fim_estagio">Data de Termino do Estagio</label>
                        <input type="date" class="form-control" id="data_fim_estagio_fixo" name="data_fim_estagio_fixo"
                            required value="{{ old('data_fim_estagio_fixo', $dataFimFixo) }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="horario">Horario</label>
                    <textarea class="form-control" id="horario_fixo" name="horario_fixo" rows="2"
                        required>{{ old('horario_fixo', $termo->horario_fixo) }}</textarea>
                </div>

                <!-- Seleção de Local (aparece após escolher a Unidade Concedente) -->
                <div class="mb-3" style="position: relative;">
                    <label for="fk_id_local" class="form-label">Selecione o Local</label>
                    <input type="text" class="form-control" id="local_search" placeholder="Digite para buscar..."
                        autocomplete="off" disabled data-prefill-local="{{ $localSelecionadoId }}"
                        data-prefill-local-text="{{ $localSelecionadoNome }}" value="{{ $localSelecionadoNome }}">
                    <select class="form-control mt-2" id="fk_id_local" name="fk_id_local" size="5"
                        data-prefill-local="{{ $localSelecionadoId }}" data-prefill-local-text="{{ $localSelecionadoNome }}"
                        style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                        <option value="">Escolha um local</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="lotacao">Lotacao</label>
                    <textarea class="form-control" id="lotacao" name="lotacao" rows="2"
                        required>{{ old('lotacao', $termo->lotacao) }}</textarea>
                </div>

                <div class="form-group row">
                    <div class="col-md-6">
                        <label for="valor_bolsa_fixo">Valor da Bolsa</label>
                        <input type="text" class="form-control" id="valor_bolsa_fixo" name="valor_bolsa_fixo" required
                            title="" min="0" value="{{ $valorBolsaFixo }}">
                    </div>
                    <div class="col-md-6">
                        <label for="auxilio_transporte_fixo">Valor do Auxilio Transporte</label>
                        <input type="text" class="form-control" id="auxilio_transporte_fixo" name="auxilio_transporte_fixo"
                            title="" min="0" value="{{ $auxilioTransporteFixo }}">
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary"
                        style="margin-top: 10px; width: 50%; margin-left: 25%; margin-right: 25%">Salvar</button>
                </div>

            </div>
        </div>

        <input type="hidden" id="valor_bolsa" name="valor_bolsa" value="{{ $valorBolsa }}">
        <input type="hidden" id="fk_id_supervisor" name="fk_id_supervisor"
            value="{{ old('fk_id_supervisor', $termo->fk_id_supervisor) }}">
        <input type="hidden" id="auxilio_transporte" name="auxilio_transporte" value="{{ $auxilioTransporte }}">
        <input type="hidden" id="nome_orientador" name="nome_orientador"
            value="{{ old('nome_orientador', $termo->nome_orientador) }}">
        <input type="hidden" id="cargo_orientador" name="cargo_orientador"
            value="{{ old('cargo_orientador', $termo->cargo_orientador) }}">
        <input type="hidden" id="data_fim_estagio" name="data_fim_estagio" value="{{ old('data_fim_estagio', $dataFim) }}">
        <input type="hidden" id="horario" name="horario" value="{{ old('horario', $termo->horario) }}">
        <input type="hidden" id="desc_atividades" name="desc_atividades"
            value="{{ old('desc_atividades', $termo->desc_atividades) }}">
        <input type="hidden" id="fk_id_local_fixo" name="fk_id_local_fixo"
            value="{{ old('fk_id_local_fixo', $termo->fk_id_local_fixo) }}">
        <input type="hidden" id="lotacao_fixo" name="lotacao_fixo" value="{{ old('lotacao_fixo', $termo->lotacao_fixo) }}">
        <input type="hidden" id="password_confirm" name="password_confirm" value="">

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

        // Sincronizar campos de local e lotacao
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

            // Campo de vaga removido: nenhuma lógica JS relacionada a vaga vinculada é mais necessária.

            // Lógica de Locais dependente da Unidade Concedente
            const empresaSelect = document.getElementById('fk_id_empresa');
            const localSearch = document.getElementById('local_search');
            const localSelect = document.getElementById('fk_id_local');
            let locaisCache = [];
            let localPrefillApplied = false;

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
                placeholder.textContent = 'Nao vincular (preencher manualmente)';
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
                vagaSelect.innerHTML = '<option value="">Nao vincular (preencher manualmente)</option>';
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
                            setTimeout(() => {
                                const opt = vagaSelect.querySelector(`option[value="${preselectVagaId}"]`);
                                if (opt) {
                                    vagaSelect.value = preselectVagaId;
                                    const selected = vagaSelect.options[vagaSelect.selectedIndex];
                                    if (selected && selected.text) {
                                        vagaSearch.value = selected.text;
                                    }
                                    const vagaData = JSON.parse(selected.dataset.vaga || '{}');
                                    if (Object.keys(vagaData).length > 0) {
                                        preencherCamposComVaga(vagaData);
                                    }
                                }
                            }, 100);
                        }
                    }
                } catch (e) {
                    console.error('Erro ao carregar vagas:', e);
                }
            }

            function preencherCamposComVaga(vaga) {
                // Verificar se vaga esta expirada
                if (vaga.expirada) {
                    alert('ATENCAO: Esta vaga esta expirada (data de termino passou). Nao e recomendado vincula-la.');
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
                // Campos de orientador permanecem livres (nao sao preenchidos pela vaga)
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

                // Tornar campos readonly (exceto estagiario, escola e supervisor)
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
                    // 'nome_orientador_fixo' e 'cargo_orientador_fixo' permanecem sempre editaveis
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

                    const prefillLocalId = localSelect.dataset.prefillLocal;
                    const prefillLocalText = localSelect.dataset.prefillLocalText;
                    if (!localPrefillApplied && prefillLocalId) {
                        localSelect.value = prefillLocalId;
                        if (prefillLocalText) {
                            localSearch.value = prefillLocalText;
                        } else {
                            const selectedLocal = localSelect.options[localSelect.selectedIndex];
                            if (selectedLocal) {
                                localSearch.value = selectedLocal.text;
                            }
                        }
                        localSelect.dispatchEvent(new Event('change'));
                        localPrefillApplied = true;
                    }
                } catch (e) {
                    // mantem desabilitado em caso de erro
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
                // Caso ja venha selecionado (ex.: edicao ou fluxo pre-preenchido)
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
                    if (prefillVagaId && prefillVagaId !== '') {
                        loadVagasByEmpresa(prefillEmpresaId, prefillVagaId);
                    } else {
                        loadVagasByEmpresa(prefillEmpresaId);
                    }
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
                        // Verificar dados do estagiario antes de mostrar botao
                        try {
                            const url = '{{ route('api.vagas.info', ['id' => ':id']) }}'.replace(':id', selected.value);
                            const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
                            if (resp.ok) {
                                const data = await resp.json();
                                const hasContacts = !!(data.tem_estagiario_definido && (data.nome_estagiario || data.nome_social_estagiario || data.contato_whatsapp || data.contato_email));
                                if (btnInfoVaga) btnInfoVaga.disabled = !hasContacts;
                            } else {
                                if (btnInfoVaga) btnInfoVaga.disabled = true;
                            }
                        } catch (e) {
                            if (btnInfoVaga) btnInfoVaga.disabled = true;
                        }
                    } else {
                        vagaSearch.value = 'Nao vincular (preencher manualmente)';
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

            // Abrir modal com informacoes da vaga/estagiario
            if (btnInfoVaga) {
                btnInfoVaga.addEventListener('click', async function () {
                    const selected = vagaSelect.options[vagaSelect.selectedIndex];
                    if (!selected || !selected.value) return;
                    try {
                        const vagaId = selected.value;
                        const url = '{{ route('api.vagas.info', ['id' => ':id']) }}'.replace(':id', vagaId);
                        const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
                        if (!resp.ok) throw new Error('Falha ao obter informacoes da vaga');
                        const data = await resp.json();

                        document.getElementById('infoNomeEstagiario').textContent = data.nome_estagiario || '-';
                        document.getElementById('infoNomeSocialEstagiario').textContent = data.nome_social_estagiario || '-';
                        document.getElementById('infoWhatsapp').textContent = data.contato_whatsapp || '-';
                        document.getElementById('infoEmail').textContent = data.contato_email || '-';

                        // Habilita/desabilita botoes baseado nos dados
                        const btnWhatsapp = document.getElementById('btnWhatsapp');
                        const btnCopiarEmail = document.getElementById('btnCopiarEmail');

                        if (data.contato_whatsapp && data.contato_whatsapp !== '-') {
                            btnWhatsapp.disabled = false;
                            btnWhatsapp.onclick = function () {
                                const numero = data.contato_whatsapp.replace(/\D/g, '');
                                const mensagem = encodeURIComponent('Ola! Identificamos que voce foi selecionado para uma vaga de estagio. Para prosseguir, pedimos que se cadastre no SIGE atraves do link: https://sigeb.br/novo-estagiario-ajax.');
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

            // Confirmacao de senha ao salvar
            const form = document.getElementById('termo-edit-form');
            const modalEl = document.getElementById('confirmPasswordModal');
            const passwordInput = document.getElementById('password_confirm_input');
            const hiddenPassword = document.getElementById('password_confirm');
            const confirmBtn = document.getElementById('confirmPasswordBtn');

            if (form && modalEl && passwordInput && hiddenPassword && confirmBtn) {
                const modal = new bootstrap.Modal(modalEl);

                form.addEventListener('submit', function (event) {
                    if (!hiddenPassword.value) {
                        event.preventDefault();
                        passwordInput.value = '';
                        passwordInput.classList.remove('is-invalid');
                        modal.show();
                        setTimeout(() => passwordInput.focus(), 200);
                    }
                });

                confirmBtn.addEventListener('click', function () {
                    if (!passwordInput.value) {
                        passwordInput.classList.add('is-invalid');
                        return;
                    }
                    passwordInput.classList.remove('is-invalid');
                    hiddenPassword.value = passwordInput.value;
                    modal.hide();
                    form.submit();
                });

                modalEl.addEventListener('hidden.bs.modal', function () {
                    if (!hiddenPassword.value) {
                        passwordInput.value = '';
                        passwordInput.classList.remove('is-invalid');
                    }
                });
            }
        });
    </script>

    <!-- Modal Info Vaga / Estagiario -->
    <div class="modal fade" id="modalInfoVaga" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Informacoes da Vaga</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="infoVagaBody">
                        <p><strong>Estagiario:</strong> <span id="infoNomeEstagiario">-</span></p>
                        <p><strong>Nome social:</strong> <span id="infoNomeSocialEstagiario">-</span></p>

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
                        <p class="text-muted mb-0">Use estes contatos para solicitar que o estagiario se cadastre no SIGE e
                            liberar a geracao do termo.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmar Senha -->
    <div class="modal fade" id="confirmPasswordModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Senha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="password_confirm_input" class="form-label">Digite sua senha para salvar as
                        alteracoes</label>
                    <input type="password" class="form-control" id="password_confirm_input" autocomplete="current-password">
                    <div class="invalid-feedback">Informe sua senha para continuar.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="confirmPasswordBtn">Confirmar e salvar</button>
                </div>
            </div>
        </div>
    </div>

@endsection