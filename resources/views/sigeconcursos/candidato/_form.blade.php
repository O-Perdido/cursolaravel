@php
    $candidato = $candidato ?? null;
    $cidades = $cidades ?? collect();
    $selectedEstado = old('fk_id_estado', $candidato?->cidade?->fk_id_estado);
    $selectedCidade = old('fk_id_cidade', $candidato?->fk_id_cidade);
    $showPasswordFields = $showPasswordFields ?? false;
    $formId = $formId ?? 'candidato-form';
    $orgaosExpedidores = $orgaosExpedidores ?? [];
    $ufs = $ufs ?? [];
    $dataNascimentoValor = old('data_nascimento', optional($candidato?->data_nascimento)->format('Y-m-d'));
    $dataNascimentoObj = null;

    if (!empty($dataNascimentoValor)) {
        try {
            $dataNascimentoObj = \Carbon\Carbon::parse($dataNascimentoValor);
        } catch (\Throwable $exception) {
            $dataNascimentoObj = null;
        }
    }

    $diaSelecionado = (int) old('data_nascimento_dia', $dataNascimentoObj?->day);
    $mesSelecionado = (int) old('data_nascimento_mes', $dataNascimentoObj?->month);
    $anoSelecionado = (int) old('data_nascimento_ano', $dataNascimentoObj?->year);
    $meses = [
        1 => 'Janeiro',
        2 => 'Fevereiro',
        3 => 'Março',
        4 => 'Abril',
        5 => 'Maio',
        6 => 'Junho',
        7 => 'Julho',
        8 => 'Agosto',
        9 => 'Setembro',
        10 => 'Outubro',
        11 => 'Novembro',
        12 => 'Dezembro',
    ];
@endphp

<style>
    .password-requirements {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-top: 12px;
    }

    .password-requirements h6 {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 12px;
        color: #495057;
    }

    .requirement-item {
        display: inline-block;
        padding: 6px 12px;
        margin: 4px;
        border-radius: 20px;
        font-size: 13px;
        transition: all 0.3s ease;
    }

    .requirement-item.invalid {
        background-color: #e9ecef;
        color: #6c757d;
    }

    .requirement-item.valid {
        background-color: #d4edda;
        color: #155724;
    }

    .requirement-item i {
        margin-right: 4px;
    }

    .password-strength-bar {
        height: 8px;
        border-radius: 4px;
        margin-top: 12px;
        margin-bottom: 8px;
        background-color: #e9ecef;
        overflow: hidden;
    }

    .password-strength-bar .progress-bar {
        transition: width 0.3s ease, background-color 0.3s ease;
    }

    .password-strength-text {
        font-size: 13px;
        font-weight: 500;
    }
</style>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Revise os campos abaixo.</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ $action }}" method="POST" id="{{ $formId }}">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <strong>Dados Pessoais</strong>
                </div>
                <div class="card-body row g-3">
                    <div class="col-12">
                        <label for="nome_completo" class="form-label">Nome completo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nome_completo') is-invalid @enderror"
                            id="nome_completo" name="nome_completo"
                            value="{{ old('nome_completo', $candidato?->nome_completo) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="numero_cpf" class="form-label">CPF @if($showPasswordFields)<span class="text-danger">*</span>@endif</label>
                        <input type="text" class="form-control @error('numero_cpf') is-invalid @enderror"
                            id="numero_cpf" name="numero_cpf"
                            value="{{ old('numero_cpf', $candidato?->numero_cpf) }}"
                            maxlength="14" {{ $showPasswordFields ? 'required' : 'readonly' }}>
                        @unless ($showPasswordFields)
                            <small class="text-muted">O CPF não pode ser alterado nesta etapa.</small>
                        @endunless
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Data de nascimento <span class="text-danger">*</span></label>
                        <div class="row g-2">
                            <div class="col-4">
                                <select class="form-select @error('data_nascimento_dia') is-invalid @enderror @error('data_nascimento') is-invalid @enderror"
                                    id="data_nascimento_dia" name="data_nascimento_dia" required>
                                    <option value="">Dia</option>
                                    @for ($dia = 1; $dia <= 31; $dia++)
                                        <option value="{{ $dia }}" {{ $diaSelecionado === $dia ? 'selected' : '' }}>
                                            {{ str_pad((string) $dia, 2, '0', STR_PAD_LEFT) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-4">
                                <select class="form-select @error('data_nascimento_mes') is-invalid @enderror @error('data_nascimento') is-invalid @enderror"
                                    id="data_nascimento_mes" name="data_nascimento_mes" required>
                                    <option value="">Mês</option>
                                    @foreach ($meses as $numeroMes => $nomeMes)
                                        <option value="{{ $numeroMes }}" {{ $mesSelecionado === $numeroMes ? 'selected' : '' }}>
                                            {{ $nomeMes }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4">
                                <select class="form-select @error('data_nascimento_ano') is-invalid @enderror @error('data_nascimento') is-invalid @enderror"
                                    id="data_nascimento_ano" name="data_nascimento_ano" required>
                                    <option value="">Ano</option>
                                    @for ($ano = now()->year; $ano >= 1900; $ano--)
                                        <option value="{{ $ano }}" {{ $anoSelecionado === $ano ? 'selected' : '' }}>
                                            {{ $ano }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="sexo" class="form-label">Sexo <span class="text-danger">*</span></label>
                        <select class="form-select @error('sexo') is-invalid @enderror" id="sexo" name="sexo" required>
                            <option value="">Selecione</option>
                            @foreach (['Masculino', 'Feminino', 'Não declarar'] as $sexo)
                                <option value="{{ $sexo }}" {{ old('sexo', $candidato?->sexo) === $sexo ? 'selected' : '' }}>
                                    {{ $sexo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="canhoto" class="form-label">Canhoto <span class="text-danger">*</span></label>
                        <select class="form-select @error('canhoto') is-invalid @enderror" id="canhoto" name="canhoto" required>
                            <option value="">Selecione</option>
                            <option value="sim" {{ old('canhoto', $candidato?->canhoto) === 'sim' ? 'selected' : '' }}>Sim</option>
                            <option value="nao" {{ old('canhoto', $candidato?->canhoto) === 'nao' ? 'selected' : '' }}>Não</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email', $candidato?->email) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="numero_rg" class="form-label">Número do RG <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('numero_rg') is-invalid @enderror"
                            id="numero_rg" name="numero_rg" value="{{ old('numero_rg', $candidato?->numero_rg) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label for="orgao_expedidor_rg" class="form-label">Órgão expedidor <span class="text-danger">*</span></label>
                        <select class="form-select @error('orgao_expedidor_rg') is-invalid @enderror"
                            id="orgao_expedidor_rg" name="orgao_expedidor_rg" required>
                            <option value="">Selecione</option>
                            @foreach ($orgaosExpedidores as $sigla => $descricao)
                                <option value="{{ $sigla }}" {{ old('orgao_expedidor_rg', $candidato?->orgao_expedidor_rg) === $sigla ? 'selected' : '' }}>
                                    {{ $sigla }} - {{ $descricao }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="uf_rg" class="form-label">UF do RG <span class="text-danger">*</span></label>
                        <select class="form-select @error('uf_rg') is-invalid @enderror"
                            id="uf_rg" name="uf_rg" required>
                            <option value="">Selecione</option>
                            @foreach ($ufs as $sigla => $nome)
                                <option value="{{ $sigla }}" {{ old('uf_rg', $candidato?->uf_rg) === $sigla ? 'selected' : '' }}>
                                    {{ $sigla }} - {{ $nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="nome_mae" class="form-label">Nome da mãe <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nome_mae') is-invalid @enderror"
                            id="nome_mae" name="nome_mae" value="{{ old('nome_mae', $candidato?->nome_mae) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="nacionalidade" class="form-label">Nacionalidade <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nacionalidade') is-invalid @enderror"
                            id="nacionalidade" name="nacionalidade"
                            value="{{ old('nacionalidade', $candidato?->nacionalidade) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label for="naturalidade_cidade" class="form-label">Naturalidade - cidade <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('naturalidade_cidade') is-invalid @enderror"
                            id="naturalidade_cidade" name="naturalidade_cidade"
                            value="{{ old('naturalidade_cidade', $candidato?->naturalidade_cidade) }}" required>
                    </div>

                    <div class="col-md-3">
                        <label for="naturalidade_estado" class="form-label">Naturalidade - estado <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('naturalidade_estado') is-invalid @enderror"
                            id="naturalidade_estado" name="naturalidade_estado"
                            value="{{ old('naturalidade_estado', $candidato?->naturalidade_estado) }}" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white">
                    <strong>Endereço e Contato</strong>
                </div>
                <div class="card-body row g-3">
                    <div class="col-md-6">
                        <label for="numero_cep" class="form-label">CEP <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('numero_cep') is-invalid @enderror"
                            id="numero_cep" name="numero_cep"
                            value="{{ old('numero_cep', $candidato?->numero_cep) }}" maxlength="9" required>
                    </div>

                    <div class="col-md-6">
                        <label for="bairro" class="form-label">Bairro <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('bairro') is-invalid @enderror"
                            id="bairro" name="bairro" value="{{ old('bairro', $candidato?->bairro) }}" required>
                    </div>

                    <div class="col-md-8">
                        <label for="endereco" class="form-label">Endereço <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('endereco') is-invalid @enderror"
                            id="endereco" name="endereco" value="{{ old('endereco', $candidato?->endereco) }}" required>
                    </div>

                    <div class="col-md-4">
                        <label for="numero_endereco" class="form-label">Número <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('numero_endereco') is-invalid @enderror"
                            id="numero_endereco" name="numero_endereco"
                            value="{{ old('numero_endereco', $candidato?->numero_endereco) }}" required>
                    </div>

                    <div class="col-12">
                        <label for="complemento_endereco" class="form-label">Complemento</label>
                        <input type="text" class="form-control @error('complemento_endereco') is-invalid @enderror"
                            id="complemento_endereco" name="complemento_endereco"
                            value="{{ old('complemento_endereco', $candidato?->complemento_endereco) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="fk_id_estado" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-select" id="fk_id_estado" name="fk_id_estado" required>
                            <option value="">Selecione um estado</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->id_estado }}" {{ (string) $selectedEstado === (string) $estado->id_estado ? 'selected' : '' }}>
                                    {{ $estado->nm_estado }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="fk_id_cidade" class="form-label">Cidade <span class="text-danger">*</span></label>
                        <select class="form-select @error('fk_id_cidade') is-invalid @enderror" id="fk_id_cidade" name="fk_id_cidade" required>
                            <option value="">Selecione uma cidade</option>
                            @foreach($cidades as $cidade)
                                <option value="{{ $cidade->id_cidade }}" {{ (string) $selectedCidade === (string) $cidade->id_cidade ? 'selected' : '' }}>
                                    {{ $cidade->nm_cidade }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="numero_telefone" class="form-label">Telefone de contato</label>
                        <input type="text" class="form-control @error('numero_telefone') is-invalid @enderror"
                            id="numero_telefone" name="numero_telefone"
                            value="{{ old('numero_telefone', $candidato?->numero_telefone) }}" maxlength="15">
                    </div>

                    <div class="col-md-6">
                        <label for="numero_celular" class="form-label">Celular WhatsApp <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('numero_celular') is-invalid @enderror"
                            id="numero_celular" name="numero_celular"
                            value="{{ old('numero_celular', $candidato?->numero_celular) }}" maxlength="15" required>
                    </div>
                </div>
            </div>

            @if ($showPasswordFields)
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <strong>Acesso ao Sistema</strong>
                    </div>
                    <div class="card-body row g-3">
                        <div class="col-12">
                            <label for="password" class="form-label">Senha <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="password_confirmation" class="form-label">Confirmar senha <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control"
                                    id="password_confirmation" name="password_confirmation" required>
                                <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password_confirmation">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="password-requirements">
                                <h6>Requisitos da senha:</h6>
                                <div>
                                    <span class="requirement-item invalid" id="rule-length">
                                        <i class="fas fa-times-circle"></i> 8+ caracteres
                                    </span>
                                    <span class="requirement-item invalid" id="rule-lower">
                                        <i class="fas fa-times-circle"></i> minúscula
                                    </span>
                                    <span class="requirement-item invalid" id="rule-upper">
                                        <i class="fas fa-times-circle"></i> MAIÚSCULA
                                    </span>
                                    <span class="requirement-item invalid" id="rule-number">
                                        <i class="fas fa-times-circle"></i> número
                                    </span>
                                    <span class="requirement-item invalid" id="rule-special">
                                        <i class="fas fa-times-circle"></i> especial
                                    </span>
                                    <span class="requirement-item invalid" id="rule-match">
                                        <i class="fas fa-times-circle"></i> coincidem
                                    </span>
                                </div>
                                <div class="password-strength-bar">
                                    <div id="password-strength-bar" class="progress-bar bg-danger" role="progressbar"
                                        style="width: 0%"></div>
                                </div>
                                <div id="password-strength-text" class="password-strength-text text-muted">
                                    Força: Muito fraca
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        @if (!empty($backUrl))
            <a href="{{ $backUrl }}" class="btn btn-outline-secondary">Voltar</a>
        @endif
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const estadoSelect = document.getElementById('fk_id_estado');
        const cidadeSelect = document.getElementById('fk_id_cidade');
        const selectedCidade = @json((string) old('fk_id_cidade', $candidato?->fk_id_cidade));
        const submitButton = document.querySelector('#{{ $formId }} button[type="submit"]');

        async function loadCidades(estadoId, cidadeSelecionada = '') {
            cidadeSelect.innerHTML = '<option value="">Carregando cidades...</option>';

            if (!estadoId) {
                cidadeSelect.innerHTML = '<option value="">Selecione uma cidade</option>';
                return;
            }

            try {
                const response = await fetch(`{{ url('estados') }}/${estadoId}/cidades`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                const cidades = await response.json();
                cidadeSelect.innerHTML = '<option value="">Selecione uma cidade</option>';

                cidades.forEach(function (cidade) {
                    const option = document.createElement('option');
                    option.value = cidade.id_cidade;
                    option.textContent = cidade.nm_cidade;

                    if (cidadeSelecionada && String(cidadeSelecionada) === String(cidade.id_cidade)) {
                        option.selected = true;
                    }

                    cidadeSelect.appendChild(option);
                });
            } catch (error) {
                cidadeSelect.innerHTML = '<option value="">Não foi possível carregar as cidades</option>';
            }
        }

        if (estadoSelect && cidadeSelect) {
            estadoSelect.addEventListener('change', async function () {
                await loadCidades(this.value);
            });

            if (estadoSelect.value && (!cidadeSelect.options.length || cidadeSelect.options.length === 1 || selectedCidade)) {
                loadCidades(estadoSelect.value, selectedCidade);
            }
        }

        document.querySelectorAll('.toggle-password').forEach(function (button) {
            button.addEventListener('click', function () {
                const target = document.getElementById(this.dataset.target);
                if (!target) {
                    return;
                }

                target.type = target.type === 'password' ? 'text' : 'password';
                this.innerHTML = target.type === 'password'
                    ? '<i class="fa-solid fa-eye"></i>'
                    : '<i class="fa-solid fa-eye-slash"></i>';
            });
        });

        const pwd = document.getElementById('password');
        const pwd2 = document.getElementById('password_confirmation');
        const ruleLength = document.getElementById('rule-length');
        const ruleLower = document.getElementById('rule-lower');
        const ruleUpper = document.getElementById('rule-upper');
        const ruleNumber = document.getElementById('rule-number');
        const ruleSpecial = document.getElementById('rule-special');
        const ruleMatch = document.getElementById('rule-match');
        const strengthBar = document.getElementById('password-strength-bar');
        const strengthText = document.getElementById('password-strength-text');

        function updateRequirement(element, isValid) {
            if (!element) {
                return;
            }

            if (isValid) {
                element.classList.remove('invalid');
                element.classList.add('valid');
                element.querySelector('i').className = 'fas fa-check-circle';
            } else {
                element.classList.remove('valid');
                element.classList.add('invalid');
                element.querySelector('i').className = 'fas fa-times-circle';
            }
        }

        function updateStrengthBar(score) {
            if (!strengthBar || !strengthText) {
                return;
            }

            const percentage = (score / 5) * 100;
            strengthBar.style.width = percentage + '%';

            let text = 'Muito fraca';
            let colorClass = 'bg-danger';

            if (score === 2) {
                text = 'Fraca';
            } else if (score === 3) {
                text = 'Média';
                colorClass = 'bg-warning';
            } else if (score === 4) {
                text = 'Forte';
                colorClass = 'bg-success';
            } else if (score === 5) {
                text = 'Muito forte';
                colorClass = 'bg-success';
            }

            strengthBar.className = 'progress-bar ' + colorClass;
            strengthText.textContent = 'Força: ' + text;
        }

        function validatePasswords() {
            if (!pwd || !pwd2) {
                return true;
            }

            const p = pwd.value;
            const p2 = pwd2.value;

            const checks = {
                length: p.length >= 8,
                lower: /[a-z]/.test(p),
                upper: /[A-Z]/.test(p),
                number: /[0-9]/.test(p),
                special: /[^a-zA-Z0-9]/.test(p),
                match: p.length > 0 && p === p2,
            };

            updateRequirement(ruleLength, checks.length);
            updateRequirement(ruleLower, checks.lower);
            updateRequirement(ruleUpper, checks.upper);
            updateRequirement(ruleNumber, checks.number);
            updateRequirement(ruleSpecial, checks.special);
            updateRequirement(ruleMatch, checks.match);

            const score = Object.values(checks).filter(Boolean).length - 1;
            updateStrengthBar(score);

            const allValid = Object.values(checks).every(Boolean);

            if (submitButton) {
                submitButton.disabled = !allValid;
            }

            return allValid;
        }

        if (pwd && pwd2) {
            pwd.addEventListener('input', validatePasswords);
            pwd2.addEventListener('input', validatePasswords);
            validatePasswords();

            document.getElementById('{{ $formId }}').addEventListener('submit', function (event) {
                if (!validatePasswords()) {
                    event.preventDefault();
                    pwd.focus();
                }
            });
        }
    });
</script>