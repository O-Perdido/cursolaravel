@extends('layouts.main')

@section('title', 'Cadastrar Nova Vaga')

@section('content')

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-briefcase me-2"></i>
            Cadastrar Nova Vaga de Estágio
        </h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('vagas.store') }}">
            @csrf

            <div class="row g-3">
                <!-- Coluna 1 -->
                <div class="col-md-6">
                    @php($nivel = auth()->user()->nivel ?? null)
                    @if($nivel !== 'empresa')
                        <!-- Seleção de Empresa (somente admin/operador) -->
                        <div class="mb-3" style="position: relative;">
                            <label for="fk_id_empresa" class="form-label">Unidade Concedente (Empresa) <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="empresa_search"
                                placeholder="Digite para buscar..." autocomplete="off">
                            <select class="form-control form-control-sm mt-2" id="fk_id_empresa" name="fk_id_empresa"
                                size="5" required
                                style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                                <option value="">Escolha uma empresa</option>
                                @if(isset($empresas) && $empresas->count())
                                    @foreach($empresas as $emp)
                                        <option value="{{ $emp->id_empresa }}">{{ $emp->nome_empresa }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <small class="form-text text-muted">Selecione a empresa para carregar os locais.</small>
                        </div>
                    @else
                        <input type="hidden" name="fk_id_empresa" value="{{ $empresaSelecionada }}">
                    @endif

                    <!-- Título da Vaga -->
                    <div class="mb-3">
                        <label for="titulo_vaga" class="form-label">Título da Vaga <span
                                class="text-danger">*</span></label>
                        <input type="text" name="titulo_vaga" id="titulo_vaga" class="form-control form-control-sm"
                            value="{{ old('titulo_vaga') }}" required
                            placeholder="Ex: Estágio em Contabilidade, Estágio em Dev Web, Estágio em Marketing, etc.">
                        <small class="form-text text-muted">Título ou nome resumido da vaga</small>
                    </div>

                    <!-- Atividades -->
                    <div class="mb-3">
                        <label for="atividades" class="form-label">Atividades <span class="text-danger">*</span></label>
                        <textarea name="atividades" id="atividades" class="form-control form-control-sm" rows="4"
                            required>{{ old('atividades') }}</textarea>
                        <small class="form-text text-muted">Descreva as principais atividades do estágio</small>
                    </div>

                    <!-- Supervisor -->
                    <div class="mb-3" style="position: relative;">
                        <label for="fk_id_supervisor" class="form-label">Supervisor <span
                                class="text-danger">*</span></label>
                        <div class="d-flex gap-2">
                            <div class="flex-grow-1" style="position: relative;">
                                <input type="text" class="form-control form-control-sm" id="supervisor_search"
                                    placeholder="Digite para buscar..." autocomplete="off" {{ (isset($empresaSelecionada) && $empresaSelecionada) ? '' : ((auth()->user()->nivel !== 'empresa') ? 'disabled' : '') }}>
                                <select class="form-control form-control-sm mt-2" id="fk_id_supervisor"
                                    name="fk_id_supervisor" size="5" required
                                    style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                                    <option value="">Escolha um supervisor</option>
                                </select>
                            </div>
                            <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal"
                                data-bs-target="#modalNovoSupervisor" title="Novo Supervisor">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <small id="supervisor_help" class="form-text text-muted">Selecione a empresa para carregar os
                            supervisores.</small>
                    </div>

                    <!-- Datas -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="data_inicio" class="form-label">Data de Início <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="data_inicio" id="data_inicio" class="form-control form-control-sm"
                                required value="{{ old('data_inicio') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="data_termino" class="form-label">Data de Término <span
                                    class="text-danger">*</span></label>
                            <input type="date" name="data_termino" id="data_termino"
                                class="form-control form-control-sm" required value="{{ old('data_termino') }}">
                        </div>
                    </div>

                    <!-- Horário -->
                    <div class="mb-3">
                        <label for="horario" class="form-label">Horário <span class="text-danger">*</span></label>
                        <input type="text" name="horario" id="horario" class="form-control form-control-sm" required
                            value="{{ old('horario') }}" placeholder="Ex: Segunda a Sexta, 08:00 às 12:00">
                    </div>

                    @if($nivel === 'empresa')
                        <!-- Estagiário definido (opcional, só empresa) -->
                        <div class="mb-3">
                            <label class="form-label d-block">Esta vaga já tem estagiário definido?</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tem_estagiario_definido"
                                    id="vaga_com_estagiario" value="sim">
                                <label class="form-check-label" for="vaga_com_estagiario">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tem_estagiario_definido"
                                    id="vaga_sem_estagiario" value="nao" checked>
                                <label class="form-check-label" for="vaga_sem_estagiario">Não</label>
                            </div>
                            <small class="form-text text-muted d-block">Opcional: preencha apenas se já houver um estagiário
                                definido para esta vaga.</small>
                        </div>

                        <div id="campos_estagiario" style="display:none;" class="border rounded p-3 mb-3">
                            <div class="mb-2">
                                <label for="nome_estagiario" class="form-label">Nome do Estagiário</label>
                                <input type="text" name="nome_estagiario" id="nome_estagiario"
                                    class="form-control form-control-sm" value="{{ old('nome_estagiario') }}"
                                    placeholder="Nome completo">
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label for="contato_whatsapp" class="form-label">WhatsApp</label>
                                    <input type="text" name="contato_whatsapp" id="contato_whatsapp"
                                        class="form-control form-control-sm" value="{{ old('contato_whatsapp') }}"
                                        placeholder="(00) 00000-0000">
                                </div>
                                <div class="col-md-6">
                                    <label for="contato_email" class="form-label">Email</label>
                                    <input type="email" name="contato_email" id="contato_email"
                                        class="form-control form-control-sm" value="{{ old('contato_email') }}"
                                        placeholder="email@exemplo.com">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Coluna 2 -->
                <div class="col-md-6">
                    <!-- Local -->
                    <div class="mb-3" style="position: relative;">
                        <label for="fk_id_local" class="form-label">Departamento <small
                                class="text-muted">(opcional)</small></label>
                        <input type="text" class="form-control form-control-sm" id="local_search"
                            placeholder="Digite para buscar..." autocomplete="off" {{ (isset($empresaSelecionada) && $empresaSelecionada) ? '' : ((auth()->user()->nivel !== 'empresa') ? 'disabled' : '') }}>
                        <select class="form-control form-control-sm mt-2" id="fk_id_local" name="fk_id_local" size="5"
                            style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                            <option value="">Nenhum departamento selecionado</option>
                            @foreach($locais as $local)
                                <option value="{{ $local->id_local }}" {{ old('fk_id_local') == $local->id_local ? 'selected' : '' }}>
                                    {{ $local->descricao }}
                                </option>
                            @endforeach
                        </select>
                        <small id="local_help" class="form-text text-muted">Se não houver departamentos, deixe em
                            branco.</small>
                    </div>

                    <!-- Lotação -->
                    <div class="mb-3">
                        <label for="lotacao" class="form-label">Lotação <span class="text-danger">*</span></label>
                        <input type="text" name="lotacao" id="lotacao" class="form-control form-control-sm" required
                            value="{{ old('lotacao') }}" placeholder="Ex: Departamento de TI">
                    </div>

                    <!-- Valores -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="valor_bolsa_mask" class="form-label">Valor da Bolsa (R$) <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="valor_bolsa_mask" id="valor_bolsa_mask"
                                class="form-control form-control-sm" inputmode="numeric" placeholder="0,00"
                                value="{{ old('valor_bolsa') ? number_format(old('valor_bolsa'), 2, ',', '.') : '' }}">
                            <input type="hidden" name="valor_bolsa" id="valor_bolsa" value="{{ old('valor_bolsa') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="valor_auxilio_transporte_mask" class="form-label">Auxílio Transporte
                                (R$)</label>
                            <input type="text" name="valor_auxilio_transporte_mask" id="valor_auxilio_transporte_mask"
                                class="form-control form-control-sm" inputmode="numeric" placeholder="0,00"
                                value="{{ old('valor_auxilio_transporte') ? number_format(old('valor_auxilio_transporte'), 2, ',', '.') : '' }}">
                            <input type="hidden" name="valor_auxilio_transporte" id="valor_auxilio_transporte"
                                value="{{ old('valor_auxilio_transporte') }}">
                        </div>
                    </div>

                    <!-- Info adicional -->
                    <div class="alert alert-info alert-sm mt-3">
                        <i class="fas fa-info-circle me-1"></i>
                        <strong>Dica:</strong> O número da vaga será gerado automaticamente ao salvar.
                    </div>
                </div>
            </div>

            <!-- Botões de ação -->
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <button onclick="window.NavigationHistory?.goBack('{{ route('vagas.index') }}')"
                    class="btn btn-secondary btn-sm" title="Voltar para a página anterior com filtros preservados">
                    <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fas fa-save me-1"></i> Salvar Vaga
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
    <script>
        // Máscara simples de moeda pt-BR (sem libs)
        function formatToBRL(value) {
            const digits = String(value || '').replace(/\D/g, '');
            const num = (parseInt(digits, 10) || 0) / 100;
            return num.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        function unmaskBRLToNumber(value) {
            if (!value) return '';
            const cleaned = String(value).replace(/\./g, '').replace(',', '.');
            const num = parseFloat(cleaned);
            return isNaN(num) ? '' : num.toFixed(2);
        }

        // Máscara de telefone/WhatsApp (XX) XXXXX-XXXX
        function applyPhoneMask(value) {
            if (!value) return '';
            value = value.replace(/\D/g, ''); // Remove tudo que não é dígito
            value = value.substring(0, 11); // Limita a 11 dígitos
            if (value.length <= 10) {
                // Formato (XX) XXXX-XXXX
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            } else {
                // Formato (XX) XXXXX-XXXX
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
            return value;
        }

        function bindPhoneMask(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;
            input.addEventListener('input', function () {
                this.value = applyPhoneMask(this.value);
            });
        }

        function bindMoneyMask(maskId, hiddenId) {
            const mask = document.getElementById(maskId);
            const hidden = document.getElementById(hiddenId);
            if (!mask || !hidden) return;
            // Inicializar exibição
            if (hidden.value) mask.value = formatToBRL(Math.round(parseFloat(hidden.value) * 100));
            mask.addEventListener('input', function () {
                this.value = formatToBRL(this.value);
                hidden.value = unmaskBRLToNumber(this.value);
            });
            // Ao sair do campo, garante formato
            mask.addEventListener('blur', function () {
                this.value = formatToBRL(this.value);
                hidden.value = unmaskBRLToNumber(this.value);
            });
        }
        bindMoneyMask('valor_bolsa_mask', 'valor_bolsa');
        bindMoneyMask('valor_auxilio_transporte_mask', 'valor_auxilio_transporte');
        bindPhoneMask('contato_whatsapp');
        // Helpers
        const qs = (s) => document.querySelector(s);
        const nivel = @json(auth()->user()->nivel ?? null);

        // Elementos
        const empresaSelect = qs('#fk_id_empresa');
        const empresaSearch = qs('#empresa_search');
        const localSearch = qs('#local_search');
        const localSelect = qs('#fk_id_local');
        const localHelp = qs('#local_help');
        const supervisorSearch = qs('#supervisor_search');
        const supervisorSelect = qs('#fk_id_supervisor');
        const supervisorHelp = qs('#supervisor_help');
        const camposEstagiario = document.getElementById('campos_estagiario');
        const radioComEstagiario = document.getElementById('vaga_com_estagiario');
        const radioSemEstagiario = document.getElementById('vaga_sem_estagiario');
        const nomeEstagiarioInput = document.getElementById('nome_estagiario');
        const contatoWhatsappInput = document.getElementById('contato_whatsapp');
        const contatoEmailInput = document.getElementById('contato_email');

        // Carregar locais da empresa
        async function carregarLocais(empresaId) {
            if (!empresaId) { return; }
            try {
                const resp = await fetch(`{{ route('api.locais.por-empresa') }}?empresa_id=${empresaId}`);
                const dados = await resp.json();
                // Limpar e preencher
                localSelect.innerHTML = '';
                const optPadrao = document.createElement('option');
                optPadrao.value = '';
                optPadrao.textContent = 'Nenhum departamento selecionado';
                localSelect.appendChild(optPadrao);
                if (dados.length === 0) {
                    localHelp.textContent = 'Nenhum departamento encontrado para esta empresa. Você pode salvar sem departamento.';
                } else {
                    localHelp.textContent = 'Selecione um departamento ou deixe em branco.';
                    dados.forEach(l => {
                        const opt = document.createElement('option');
                        opt.value = l.id;
                        opt.textContent = l.descricao;
                        localSelect.appendChild(opt);
                    });
                }
                localSearch.disabled = false;
            } catch (e) {
                console.error(e);
            }
        }

        async function carregarSupervisores(empresaId) {
            if (!empresaId) { return; }
            try {
                const resp = await fetch(`{{ route('api.supervisores.por-empresa') }}?empresa_id=${empresaId}`);
                const dados = await resp.json();
                supervisorSelect.innerHTML = '';
                const optPadrao = document.createElement('option');
                optPadrao.value = '';
                optPadrao.textContent = 'Escolha um supervisor';
                supervisorSelect.appendChild(optPadrao);
                if (dados.length === 0) {
                    supervisorHelp.textContent = 'Nenhum supervisor encontrado para esta empresa.';
                } else {
                    supervisorHelp.textContent = 'Selecione um supervisor.';
                    dados.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.id;
                        opt.textContent = s.nome_supervisor;
                        supervisorSelect.appendChild(opt);
                    });
                }
                supervisorSearch.disabled = false;
            } catch (e) {
                console.error(e);
            }
        }

        // Filtro do dropdown de Local
        if (localSearch) {
            localSearch.addEventListener('focus', function () {
                localSelect.style.display = 'block';
            });
            localSearch.addEventListener('input', function () {
                const filter = this.value.toUpperCase();
                const options = localSelect.getElementsByTagName('option');
                for (let i = 0; i < options.length; i++) {
                    const txtValue = options[i].textContent || options[i].innerText;
                    options[i].style.display = (txtValue.toUpperCase().indexOf(filter) > -1 || options[i].value === '') ? '' : 'none';
                }
                localSelect.style.display = 'block';
            });
            localSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                localSearch.value = selectedOption.text;
                localSelect.style.display = 'none';
            });
            document.addEventListener('click', function (event) {
                if (!localSearch.contains(event.target) && !localSelect.contains(event.target)) {
                    localSelect.style.display = 'none';
                }
            });
        }

        // Filtro do dropdown de Supervisor
        if (supervisorSearch) {
            supervisorSearch.addEventListener('focus', function () {
                supervisorSelect.style.display = 'block';
            });
            supervisorSearch.addEventListener('input', function () {
                const filter = this.value.toUpperCase();
                const options = supervisorSelect.getElementsByTagName('option');
                for (let i = 0; i < options.length; i++) {
                    const txtValue = options[i].textContent || options[i].innerText;
                    options[i].style.display = (txtValue.toUpperCase().indexOf(filter) > -1 || options[i].value === '') ? '' : 'none';
                }
                supervisorSelect.style.display = 'block';
            });
            supervisorSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                supervisorSearch.value = selectedOption.text;
                supervisorSelect.style.display = 'none';
            });
            document.addEventListener('click', function (event) {
                if (!supervisorSearch.contains(event.target) && !supervisorSelect.contains(event.target)) {
                    supervisorSelect.style.display = 'none';
                }
            });
        }

        // Seleção de Empresa (somente admin/operador)
        if (empresaSelect && empresaSearch) {
            empresaSearch.addEventListener('focus', function () { empresaSelect.style.display = 'block'; });
            empresaSearch.addEventListener('input', function () {
                const filter = this.value.toUpperCase();
                const options = empresaSelect.getElementsByTagName('option');
                for (let i = 0; i < options.length; i++) {
                    const txtValue = options[i].textContent || options[i].innerText;
                    options[i].style.display = (txtValue.toUpperCase().indexOf(filter) > -1 || options[i].value === '') ? '' : 'none';
                }
                empresaSelect.style.display = 'block';
            });
            empresaSelect.addEventListener('change', function () {
                const selected = this.options[this.selectedIndex];
                empresaSearch.value = selected.text;
                empresaSelect.style.display = 'none';
                carregarLocais(this.value);
                carregarSupervisores(this.value);
            });
            document.addEventListener('click', function (event) {
                if (!empresaSearch.contains(event.target) && !empresaSelect.contains(event.target)) {
                    empresaSelect.style.display = 'none';
                }
            });
        }

        // Empresa já selecionada (usuário empresa)
        @if(isset($empresaSelecionada) && $empresaSelecionada)
            carregarLocais(@json($empresaSelecionada));
            carregarSupervisores(@json($empresaSelecionada));
        @endif

                                                                                                                        // Filtro do dropdown de Supervisor
                                                                                                                if (supervisorSearch) {
            supervisorSearch.addEventListener('focus', function () {
                supervisorSelect.style.display = 'block';
            });
            supervisorSearch.addEventListener('input', function () {
                const filter = this.value.toUpperCase();
                const options = supervisorSelect.getElementsByTagName('option');
                for (let i = 0; i < options.length; i++) {
                    const txtValue = options[i].textContent || options[i].innerText;
                    options[i].style.display = (txtValue.toUpperCase().indexOf(filter) > -1 || options[i].value === '') ? '' : 'none';
                }
                supervisorSelect.style.display = 'block';
            });
            supervisorSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                supervisorSearch.value = selectedOption.text;
                supervisorSelect.style.display = 'none';
            });
            document.addEventListener('click', function (event) {
                if (!supervisorSearch.contains(event.target) && !supervisorSelect.contains(event.target)) {
                    supervisorSelect.style.display = 'none';
                }
            });
        }

        // Validação de datas
        const dataInicio = document.getElementById('data_inicio');
        const dataTermino = document.getElementById('data_termino');
        dataInicio.addEventListener('focusout', function () { dataTermino.min = this.value; });
        dataTermino.addEventListener('focusout', function () {
            if (dataInicio.value && this.value < dataInicio.value) {
                mostrarToast('A data de término não pode ser anterior à data de início!', 'danger');
                this.value = '';
            }
        });

        // Mostrar/ocultar campos do estagiário (somente empresa)
        if (radioComEstagiario && radioSemEstagiario && camposEstagiario) {
            const camposEstagiarioInputs = [nomeEstagiarioInput, contatoWhatsappInput, contatoEmailInput];
            const possuiCamposPreenchidos = () => camposEstagiarioInputs.some((input) => input && input.value.trim() !== '');

            function toggleCamposEstagiario() {
                camposEstagiario.style.display = radioComEstagiario.checked ? 'block' : 'none';
            }

            function marcarComoTemEstagiario() {
                radioComEstagiario.checked = true;
                radioSemEstagiario.checked = false;
                toggleCamposEstagiario();
            }

            camposEstagiarioInputs.forEach((input) => {
                if (!input) return;
                input.addEventListener('input', function () {
                    if (this.value.trim() !== '') {
                        marcarComoTemEstagiario();
                    }
                });
            });

            radioComEstagiario.addEventListener('change', toggleCamposEstagiario);
            radioSemEstagiario.addEventListener('change', function () {
                if (this.checked && possuiCamposPreenchidos()) {
                    mostrarToast('Limpe os dados do estagiário para marcar que a vaga não tem estagiário definido.', 'danger');
                    marcarComoTemEstagiario();
                    return;
                }
                toggleCamposEstagiario();
            });

            if (possuiCamposPreenchidos()) {
                marcarComoTemEstagiario();
            } else {
                toggleCamposEstagiario();
            }
        }
    </script>

    <!-- Modal Novo Supervisor -->
    <div class="modal fade" id="modalNovoSupervisor" tabindex="-1" aria-labelledby="modalNovoSupervisorLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovoSupervisorLabel">Novo Supervisor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalErrors" class="alert alert-danger" style="display:none;"></div>
                    <form id="formNovoSupervisor" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="modal_nome_supervisor" class="form-label">Nome <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modal_nome_supervisor" name="nome_supervisor"
                                required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="modal_cpf_supervisor" class="form-label">CPF <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modal_cpf_supervisor" name="cpf_supervisor"
                                placeholder="000.000.000-00" maxlength="14" required>
                            <small class="form-text text-muted" id="modalCpfStatus"></small>
                            <div class="invalid-feedback" id="modalCpfError" style="display:none;">CPF inválido ou
                                duplicado.</div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="modal_celular_supervisor" class="form-label">Número de Celular</label>
                            <input type="tel" class="form-control" id="modal_celular_supervisor" maxlength="15"
                                name="celular_supervisor" placeholder="(DD) 90000-0000">
                        </div>
                        <div class="form-group mb-3">
                            <label for="modal_email_supervisor" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="modal_email_supervisor" name="email_supervisor"
                                placeholder="email@exemplo.com">
                        </div>
                        <div class="form-group mb-3">
                            <label for="modal_area_formacao" class="form-label">Área de Formação</label>
                            <input type="text" class="form-control" id="modal_area_formacao" name="area_formacao">
                        </div>
                        <div class="form-group mb-3">
                            <label for="modal_tempo_experiencia" class="form-label">Tempo de Experiência</label>
                            <input type="text" class="form-control" id="modal_tempo_experiencia" name="tempo_experiencia">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnSalvarSupervisor">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer"
        style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999; pointer-events: none;">
    </div>

    <script>
        // Formatar CPF em tempo real
        const cpfInput = document.getElementById('modal_cpf_supervisor');
        const cpfStatus = document.getElementById('modalCpfStatus');
        const cpfError = document.getElementById('modalCpfError');
        const btnSalvar = document.getElementById('btnSalvarSupervisor');
        const celularInput = document.getElementById('modal_celular_supervisor');

        function formatarCPF(valor) {
            valor = valor.replace(/\D/g, '');
            if (valor.length > 11) valor = valor.slice(0, 11);
            if (valor.length <= 3) return valor;
            if (valor.length <= 6) return valor.slice(0, 3) + '.' + valor.slice(3);
            if (valor.length <= 9) return valor.slice(0, 3) + '.' + valor.slice(3, 6) + '.' + valor.slice(6);
            return valor.slice(0, 3) + '.' + valor.slice(3, 6) + '.' + valor.slice(6, 9) + '-' + valor.slice(9);
        }

        function mascaraCelular(value) {
            value = value.replace(/\D/g, '');
            if (value.length <= 10) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            } else {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
            return value;
        }

        celularInput.addEventListener('input', function () {
            this.value = mascaraCelular(this.value);
        });

        function validarCPF(cpf) {
            cpf = cpf.replace(/\D/g, '');
            if (cpf.length !== 11) return false;
            if (/^(\d)\1+$/.test(cpf)) return false;

            let soma = 0;
            for (let i = 0; i < 9; i++) soma += parseInt(cpf.charAt(i)) * (10 - i);
            let resto = 11 - (soma % 11);
            if (resto === 10 || resto === 11) resto = 0;
            if (resto !== parseInt(cpf.charAt(9))) return false;

            soma = 0;
            for (let i = 0; i < 10; i++) soma += parseInt(cpf.charAt(i)) * (11 - i);
            resto = 11 - (soma % 11);
            if (resto === 10 || resto === 11) resto = 0;
            if (resto !== parseInt(cpf.charAt(10))) return false;

            return true;
        }

        cpfInput.addEventListener('input', function () {
            this.value = formatarCPF(this.value);

            const cpfLimpo = this.value.replace(/\D/g, '');
            if (cpfLimpo.length === 11) {
                if (validarCPF(this.value)) {
                    cpfStatus.textContent = '✓ CPF válido';
                    cpfStatus.style.color = '#28a745';
                    cpfError.style.display = 'none';
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                    btnSalvar.disabled = false;
                } else {
                    cpfStatus.textContent = '✗ CPF inválido';
                    cpfStatus.style.color = '#dc3545';
                    cpfError.style.display = 'block';
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                    btnSalvar.disabled = true;
                }
            } else {
                cpfStatus.textContent = '';
                cpfError.style.display = 'none';
                this.classList.remove('is-invalid', 'is-valid');
                btnSalvar.disabled = true;
            }
        });

        // Toast de sucesso
        function mostrarToast(mensagem, tipo = 'success') {
            const container = document.getElementById('toastContainer');
            const toastId = 'toast-' + Date.now();
            const bgColor = tipo === 'success' ? 'bg-success' : 'bg-danger';
            const icon = tipo === 'success' ? '✓' : '✕';

            const toastHTML = `
                                                                                    <div id="${toastId}" class="toast" role="alert" aria-live="assertive" aria-atomic="true" style="pointer-events: auto;">
                                                                                        <div class="toast-header ${bgColor} text-white">
                                                                                            <strong class="me-auto">${icon} ${tipo === 'success' ? 'Sucesso' : 'Erro'}</strong>
                                                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                                                                                        </div>
                                                                                        <div class="toast-body">${mensagem}</div>
                                                                                    </div>
                                                                                `;

            container.insertAdjacentHTML('beforeend', toastHTML);
            const toastEl = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastEl, {
                delay: 5000
            });
            toast.show();

            // Remover elemento após desaparecer
            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        }

        document.getElementById('btnSalvarSupervisor').addEventListener('click', async function () {
            const form = document.getElementById('formNovoSupervisor');
            const modalErrors = document.getElementById('modalErrors');
            const nivel = @json(auth()->user()->nivel ?? '');
            const empresaId = @json(isset($empresaSelecionada) ? $empresaSelecionada : null);

            // Obter empresa selecionada no formulário (para admin/operador)
            let empresaSelecionadaAtual = empresaId;
            if (!empresaSelecionadaAtual && nivel !== 'empresa') {
                const empresaSelect = document.getElementById('fk_id_empresa');
                empresaSelecionadaAtual = empresaSelect ? empresaSelect.value : null;
            }

            if (!empresaSelecionadaAtual) {
                modalErrors.textContent = 'Selecione uma empresa antes de adicionar um supervisor.';
                modalErrors.style.display = 'block';
                return;
            }

            const formData = new FormData(form);
            formData.append('fk_id_empresa', empresaSelecionadaAtual);

            const rota = nivel === 'empresa' ? '{{ route("empresa.supervisores.store") }}' : '{{ route("supervisor.store") }}';

            try {
                const response = await fetch(rota, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    // Sucesso: fechar modal, limpar form, recarregar supervisores
                    form.reset();
                    modalErrors.style.display = 'none';
                    cpfStatus.textContent = '';
                    cpfInput.classList.remove('is-valid', 'is-invalid');

                    // Recarregar lista de supervisores
                    await carregarSupervisores(empresaSelecionadaAtual);

                    // Fechar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalNovoSupervisor'));
                    modal.hide();

                    // Mostrar toast de sucesso
                    mostrarToast('Supervisor cadastrado com sucesso!', 'success');
                } else {
                    // Erro de validação
                    let erros = '';
                    if (data.errors) {
                        erros = Object.values(data.errors).flat().join('<br>');
                    } else {
                        erros = data.message || 'Erro ao salvar supervisor.';
                    }
                    modalErrors.innerHTML = erros;
                    modalErrors.style.display = 'block';
                }
            } catch (error) {
                console.error('Erro:', error);
                modalErrors.textContent = 'Erro ao salvar supervisor. Tente novamente.';
                modalErrors.style.display = 'block';
            }
        });

        // Limpar erros ao abrir modal
        document.getElementById('modalNovoSupervisor').addEventListener('show.bs.modal', function () {
            document.getElementById('formNovoSupervisor').reset();
            document.getElementById('modalErrors').style.display = 'none';
            cpfStatus.textContent = '';
            cpfInput.classList.remove('is-valid', 'is-invalid');
            btnSalvar.disabled = true;
        });
    </script>
@endsection