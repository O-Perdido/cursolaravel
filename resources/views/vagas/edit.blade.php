@extends('layouts.main')

@section('title', 'Editar Vaga')

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

    @if($vaga->fk_id_termo)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-1"></i>
            <strong>Atenção:</strong> Esta vaga está vinculada a um termo de estágio. Edições limitadas.
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0">
                <i class="fas fa-edit me-2"></i>
                Editar Vaga de Estágio #{{ $vaga->numero_vaga }}
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('vagas.update', $vaga->id_vaga) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <!-- Coluna 1 -->
                    <div class="col-md-6">
                        <!-- Número da vaga (readonly) -->
                        <div class="mb-3">
                            <label for="numero_vaga" class="form-label">Número da Vaga</label>
                            <input type="text" id="numero_vaga" class="form-control form-control-sm"
                                value="{{ $vaga->numero_vaga }}" readonly>
                        </div>

                        <!-- Atividades -->
                        <div class="mb-3">
                            <label for="atividades" class="form-label">Atividades <span class="text-danger">*</span></label>
                            <textarea name="atividades" id="atividades" class="form-control form-control-sm" rows="4"
                                required {{ $vaga->fk_id_termo ? 'readonly' : '' }}>{{ old('atividades', $vaga->atividades) }}</textarea>
                        </div>

                        <!-- Supervisor -->
                        <div class="mb-3" style="position: relative;">
                            <label for="fk_id_supervisor" class="form-label">Supervisor <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <div class="flex-grow-1" style="position: relative;">
                                    <input type="text" class="form-control form-control-sm" id="supervisor_search"
                                        placeholder="Digite para buscar..." autocomplete="off" 
                                        value="{{ $vaga->supervisor->nome_supervisor ?? '' }}" 
                                        {{ $vaga->fk_id_termo ? 'readonly' : '' }}>
                                    <select class="form-control form-control-sm mt-2" id="fk_id_supervisor" name="fk_id_supervisor" size="5" required
                                        {{ $vaga->fk_id_termo ? 'disabled' : '' }}
                                        style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                                        <option value="">Escolha um supervisor</option>
                                    </select>
                                </div>
                                @if(!$vaga->fk_id_termo)
                                    <a href="{{ route('empresa.supervisores.create') }}" class="btn btn-outline-success btn-sm" title="Novo Supervisor" target="_blank">
                                        <i class="fas fa-plus"></i>
                                    </a>
                                @endif
                            </div>
                            @if($vaga->fk_id_termo)
                                <input type="hidden" name="fk_id_supervisor" value="{{ $vaga->fk_id_supervisor }}">
                            @endif
                        </div>

                        <!-- Datas -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="data_inicio" class="form-label">Data de Início <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="data_inicio" id="data_inicio" class="form-control form-control-sm"
                                    required value="{{ old('data_inicio', $vaga->data_inicio) }}" {{ $vaga->fk_id_termo ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="data_termino" class="form-label">Data de Término <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="data_termino" id="data_termino"
                                    class="form-control form-control-sm" required
                                    value="{{ old('data_termino', $vaga->data_termino) }}" {{ $vaga->fk_id_termo ? 'readonly' : '' }}>
                            </div>
                        </div>

                        <!-- Horário -->
                        <div class="mb-3">
                            <label for="horario" class="form-label">Horário <span class="text-danger">*</span></label>
                            <input type="text" name="horario" id="horario" class="form-control form-control-sm" required
                                value="{{ old('horario', $vaga->horario) }}" {{ $vaga->fk_id_termo ? 'readonly' : '' }}>
                        </div>

                        @php($nivel = auth()->user()->nivel ?? null)
                        @if($nivel === 'empresa')
                        <!-- Estagiário definido (opcional, só empresa) -->
                        <div class="mb-3">
                            <label class="form-label d-block">Esta vaga já tem estagiário definido?</label>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tem_estagiario_definido" id="vaga_com_estagiario" value="sim" {{ old('tem_estagiario_definido', ($vaga->tem_estagiario_definido ? 'sim' : 'nao')) === 'sim' ? 'checked' : '' }} {{ $vaga->fk_id_termo ? 'disabled' : '' }}>
                                <label class="form-check-label" for="vaga_com_estagiario">Sim</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tem_estagiario_definido" id="vaga_sem_estagiario" value="nao" {{ old('tem_estagiario_definido', ($vaga->tem_estagiario_definido ? 'sim' : 'nao')) === 'nao' ? 'checked' : '' }} {{ $vaga->fk_id_termo ? 'disabled' : '' }}>
                                <label class="form-check-label" for="vaga_sem_estagiario">Não</label>
                            </div>
                            <small class="form-text text-muted d-block">Opcional: preencha apenas se já houver um estagiário definido para esta vaga.</small>
                        </div>

                        <div id="campos_estagiario" class="border rounded p-3 mb-3" style="display: {{ ($vaga->tem_estagiario_definido ? 'block' : 'none') }};">
                            <div class="mb-2">
                                <label for="nome_estagiario" class="form-label">Nome do Estagiário</label>
                                <input type="text" name="nome_estagiario" id="nome_estagiario" class="form-control form-control-sm" value="{{ old('nome_estagiario', $vaga->nome_estagiario) }}" placeholder="Nome completo" {{ $vaga->fk_id_termo ? 'readonly' : '' }}>
                            </div>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label for="contato_whatsapp" class="form-label">WhatsApp</label>
                                    <input type="text" name="contato_whatsapp" id="contato_whatsapp" class="form-control form-control-sm" value="{{ old('contato_whatsapp', $vaga->contato_whatsapp) }}" placeholder="(00) 00000-0000" {{ $vaga->fk_id_termo ? 'readonly' : '' }}>
                                </div>
                                <div class="col-md-6">
                                    <label for="contato_email" class="form-label">Email</label>
                                    <input type="email" name="contato_email" id="contato_email" class="form-control form-control-sm" value="{{ old('contato_email', $vaga->contato_email) }}" placeholder="email@exemplo.com" {{ $vaga->fk_id_termo ? 'readonly' : '' }}>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Coluna 2 -->
                    <div class="col-md-6">
                        <!-- Local -->
                        <div class="mb-3" style="position: relative;">
                            <label for="fk_id_local" class="form-label">Local <small
                                    class="text-muted">(opcional)</small></label>
                            <input type="text" class="form-control form-control-sm" id="local_search"
                                placeholder="Digite para buscar..." autocomplete="off" {{ $vaga->fk_id_termo ? 'readonly' : '' }} value="{{ $vaga->local->descricao ?? '' }}">
                            <select class="form-control form-control-sm mt-2" id="fk_id_local" name="fk_id_local" size="5"
                                {{ $vaga->fk_id_termo ? 'disabled' : '' }}
                                style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                                <option value="">Escolha um local</option>
                                @foreach($locais as $local)
                                    <option value="{{ $local->id_local }}" {{ old('fk_id_local', $vaga->fk_id_local) == $local->id_local ? 'selected' : '' }}>
                                        {{ $local->descricao }}
                                    </option>
                                @endforeach
                            </select>
                            <!-- Campo hidden para garantir envio quando disabled -->
                            @if($vaga->fk_id_termo)
                                <input type="hidden" name="fk_id_local" value="{{ $vaga->fk_id_local }}">
                            @endif
                        </div>

                        <!-- Lotação -->
                        <div class="mb-3">
                            <label for="lotacao" class="form-label">Lotação <span class="text-danger">*</span></label>
                            <input type="text" name="lotacao" id="lotacao" class="form-control form-control-sm" required
                                value="{{ old('lotacao', $vaga->lotacao) }}" {{ $vaga->fk_id_termo ? 'readonly' : '' }}>
                        </div>

                        <!-- Valores -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="valor_bolsa_mask" class="form-label">Valor da Bolsa (R$) <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="valor_bolsa_mask" id="valor_bolsa_mask"
                                    class="form-control form-control-sm" inputmode="numeric" placeholder="0,00"
                                    value="{{ number_format(old('valor_bolsa', $vaga->valor_bolsa), 2, ',', '.') }}" {{ $vaga->fk_id_termo ? 'readonly' : '' }}>
                                <input type="hidden" name="valor_bolsa" id="valor_bolsa"
                                    value="{{ old('valor_bolsa', number_format($vaga->valor_bolsa, 2, '.', '')) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="valor_auxilio_transporte_mask" class="form-label">Auxílio Transporte (R$) <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="valor_auxilio_transporte_mask" id="valor_auxilio_transporte_mask"
                                    class="form-control form-control-sm" inputmode="numeric" placeholder="0,00"
                                    value="{{ number_format(old('valor_auxilio_transporte', $vaga->valor_auxilio_transporte), 2, ',', '.') }}"
                                    {{ $vaga->fk_id_termo ? 'readonly' : '' }}>
                                <input type="hidden" name="valor_auxilio_transporte" id="valor_auxilio_transporte"
                                    value="{{ old('valor_auxilio_transporte', number_format($vaga->valor_auxilio_transporte, 2, '.', '')) }}">
                            </div>
                        </div>

                        <!-- Status (readonly) -->
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <input type="text" id="status" class="form-control form-control-sm"
                                value="{{ ucfirst($vaga->status) }}" readonly>
                        </div>

                        @if($vaga->fk_id_termo)
                            <div class="alert alert-info alert-sm">
                                <i class="fas fa-link me-1"></i>
                                Vaga vinculada ao Termo #{{ $vaga->termo->num_termo ?? '-' }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('vagas.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                    @if(!$vaga->fk_id_termo)
                        <button type="submit" class="btn btn-warning btn-sm">
                            <i class="fas fa-save me-1"></i> Atualizar Vaga
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        // Máscara moeda pt-BR
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
        function bindMoneyMask(maskId, hiddenId) {
            const mask = document.getElementById(maskId);
            const hidden = document.getElementById(hiddenId);
            if (!mask || !hidden) return;
            if (hidden.value) mask.value = formatToBRL(Math.round(parseFloat(hidden.value) * 100));
            mask.addEventListener('input', function () {
                this.value = formatToBRL(this.value);
                hidden.value = unmaskBRLToNumber(this.value);
            });
            mask.addEventListener('blur', function () {
                this.value = formatToBRL(this.value);
                hidden.value = unmaskBRLToNumber(this.value);
            });
        }
        @if(!$vaga->fk_id_termo)
            bindMoneyMask('valor_bolsa_mask', 'valor_bolsa');
            bindMoneyMask('valor_auxilio_transporte_mask', 'valor_auxilio_transporte');

            // Carregar supervisores da empresa
            async function carregarSupervisores() {
                try {
                    const resp = await fetch(`{{ route('api.supervisores.por-empresa') }}?empresa_id={{ $vaga->fk_id_empresa }}`);
                    const dados = await resp.json();
                    const supervisorSelect = document.getElementById('fk_id_supervisor');
                    supervisorSelect.innerHTML = '';
                    const optPadrao = document.createElement('option');
                    optPadrao.value = '';
                    optPadrao.textContent = 'Escolha um supervisor';
                    supervisorSelect.appendChild(optPadrao);
                    dados.forEach(s => {
                        const opt = document.createElement('option');
                        opt.value = s.id;
                        opt.textContent = s.nome_supervisor;
                        if (s.id == {{ $vaga->fk_id_supervisor ?? 'null' }}) opt.selected = true;
                        supervisorSelect.appendChild(opt);
                    });
                } catch (e) { console.error(e); }
            }
            carregarSupervisores();

            // Filtro do dropdown de Supervisor
            const supervisorSearch = document.getElementById('supervisor_search');
            const supervisorSelect = document.getElementById('fk_id_supervisor');
            supervisorSearch.addEventListener('focus', function () { supervisorSelect.style.display = 'block'; });
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
        @endif
            @if(!$vaga->fk_id_termo)
                // Alternar campos do estagiário (empresa)
                const camposEstagiario = document.getElementById('campos_estagiario');
                const radioComEstagiario = document.getElementById('vaga_com_estagiario');
                const radioSemEstagiario = document.getElementById('vaga_sem_estagiario');
                if (radioComEstagiario && radioSemEstagiario && camposEstagiario) {
                    function toggleCamposEstagiario() {
                        camposEstagiario.style.display = radioComEstagiario.checked ? 'block' : 'none';
                    }
                    radioComEstagiario.addEventListener('change', toggleCamposEstagiario);
                    radioSemEstagiario.addEventListener('change', toggleCamposEstagiario);
                    toggleCamposEstagiario();
                }
                // Busca de Local (só ativa se vaga não está vinculada)
                const localSearch = document.getElementById('local_search');
                const localSelect = document.getElementById('fk_id_local');

                localSearch.addEventListener('focus', function () {
                    localSelect.style.display = 'block';
                });

                localSearch.addEventListener('input', function () {
                    const filter = this.value.toUpperCase();
                    const options = localSelect.getElementsByTagName('option');

                    for (let i = 0; i < options.length; i++) {
                        const txtValue = options[i].textContent || options[i].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            options[i].style.display = '';
                        } else {
                            options[i].style.display = 'none';
                        }
                    }
                    localSelect.style.display = 'block';
                });

                localSelect.addEventListener('change', function () {
                    const selectedOption = this.options[this.selectedIndex];
                    localSearch.value = selectedOption.text;
                    localSelect.style.display = 'none';
                });

                // Fechar dropdown ao clicar fora
                document.addEventListener('click', function (event) {
                    if (!localSearch.contains(event.target) && !localSelect.contains(event.target)) {
                        localSelect.style.display = 'none';
                    }
                });

                // Validação de datas
                const dataInicio = document.getElementById('data_inicio');
                const dataTermino = document.getElementById('data_termino');

                dataInicio.addEventListener('change', function () {
                    dataTermino.min = this.value;
                });

                dataTermino.addEventListener('change', function () {
                    if (dataInicio.value && this.value < dataInicio.value) {
                        alert('A data de término não pode ser anterior à data de início!');
                        this.value = '{{ $vaga->data_termino }}';
                    }
                });
            @endif
    </script>
@endsection