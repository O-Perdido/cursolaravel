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
                    <!-- Atividades -->
                    <div class="mb-3">
                        <label for="atividades" class="form-label">Atividades <span class="text-danger">*</span></label>
                        <textarea name="atividades" id="atividades" class="form-control form-control-sm" rows="4"
                            required>{{ old('atividades') }}</textarea>
                        <small class="form-text text-muted">Descreva as principais atividades do estágio</small>
                    </div>

                    <!-- Nome do Orientador -->
                    <div class="mb-3">
                        <label for="nome_orientador" class="form-label">Nome do Orientador <span
                                class="text-danger">*</span></label>
                        <input type="text" name="nome_orientador" id="nome_orientador"
                            class="form-control form-control-sm" required value="{{ old('nome_orientador') }}">
                    </div>

                    <!-- Cargo do Orientador -->
                    <div class="mb-3">
                        <label for="cargo_orientador" class="form-label">Cargo do Orientador <span
                                class="text-danger">*</span></label>
                        <input type="text" name="cargo_orientador" id="cargo_orientador"
                            class="form-control form-control-sm" required value="{{ old('cargo_orientador') }}">
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
                </div>

                <!-- Coluna 2 -->
                <div class="col-md-6">
                    <!-- Local -->
                    <div class="mb-3" style="position: relative;">
                        <label for="fk_id_local" class="form-label">Local <small
                                class="text-muted">(opcional)</small></label>
                        <input type="text" class="form-control form-control-sm" id="local_search"
                            placeholder="Digite para buscar..." autocomplete="off" {{ (isset($empresaSelecionada) && $empresaSelecionada) ? '' : ((auth()->user()->nivel !== 'empresa') ? 'disabled' : '') }}>
                        <select class="form-control form-control-sm mt-2" id="fk_id_local" name="fk_id_local" size="5"
                            style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                            <option value="">Nenhum local selecionado</option>
                            @foreach($locais as $local)
                                <option value="{{ $local->id_local }}" {{ old('fk_id_local') == $local->id_local ? 'selected' : '' }}>
                                    {{ $local->descricao }}
                                </option>
                            @endforeach
                        </select>
                        <small id="local_help" class="form-text text-muted">Se não houver locais, deixe em
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
                            <label for="valor_auxilio_transporte_mask" class="form-label">Auxílio Transporte (R$) <span
                                    class="text-danger">*</span></label>
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
                <a href="{{ route('vagas.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times me-1"></i> Cancelar
                </a>
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
        // Helpers
        const qs = (s) => document.querySelector(s);
        const nivel = @json(auth()->user()->nivel ?? null);

        // Elementos
        const empresaSelect = qs('#fk_id_empresa');
        const empresaSearch = qs('#empresa_search');
        const localSearch = qs('#local_search');
        const localSelect = qs('#fk_id_local');
        const localHelp = qs('#local_help');

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
                optPadrao.textContent = 'Nenhum local selecionado';
                localSelect.appendChild(optPadrao);
                if (dados.length === 0) {
                    localHelp.textContent = 'Nenhum local encontrado para esta empresa. Você pode salvar sem local.';
                } else {
                    localHelp.textContent = 'Selecione um local ou deixe em branco.';
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
        @endif

                // Validação de datas
                const dataInicio = document.getElementById('data_inicio');
        const dataTermino = document.getElementById('data_termino');
        dataInicio.addEventListener('change', function () { dataTermino.min = this.value; });
        dataTermino.addEventListener('change', function () {
            if (dataInicio.value && this.value < dataInicio.value) {
                alert('A data de término não pode ser anterior à data de início!');
                this.value = '';
            }
        });
    </script>
@endsection