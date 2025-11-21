@extends('layouts.main')

@section('title', 'Cadastro de Estagiário')

@section('content')
    <style>
        :root {
            --primary-color: #102E6C;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: #0d2555;
            border-color: #0d2555;
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .spinner-border.text-primary {
            color: var(--primary-color) !important;
        }

        .password-requirements {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
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

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(255, 255, 255, 0.95);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        .terms-checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .terms-checkbox-wrapper input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .terms-text {
            font-size: 14px;
            color: #495057;
        }

        .terms-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .terms-link:hover {
            text-decoration: underline;
        }

        .btn-toggle-password {
            border-left: 1px solid #ced4da;
        }

        .file-upload-wrapper {
            text-align: center;
        }

        .file-upload-wrapper .btn-file-upload {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 38px;
            cursor: pointer;
        }

        .file-upload-label-text {
            display: inline-block;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
            text-align: center;
        }

        .file-upload-wrapper .btn-file-upload i {
            flex-shrink: 0;
        }
    </style>

    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-user-plus mr-2"></i> Cadastro de Estagiário</h4>
            </div>
            <div class="card-body">
                <div id="alerts"></div>

                <form id="form-estagiario" action="{{ route('novo-estagiario-ajax-store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Coluna Esquerda -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary"><i class="fas fa-user mr-2"></i> Dados Pessoais</h5>

                            <div class="form-group">
                                <label for="nome_estagiario">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nome_estagiario" name="nome_estagiario" required
                                    oninput="this.value = this.value.toUpperCase()">
                            </div>

                            <div class="form-group">
                                <label for="nome_mae">Nome da Mãe</label>
                                <input type="text" class="form-control" id="nome_mae" name="nome_mae"
                                    oninput="this.value = this.value.toUpperCase()">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="data_nascimento">Data de Nascimento <span
                                                class="text-danger">*</span></label>
                                        <input type="date" class="form-control" id="data_nascimento" name="data_nascimento"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="numero_cpf">CPF <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="numero_cpf" name="numero_cpf" required
                                            maxlength="14">
                                        <div class="invalid-feedback" id="cpfError">CPF inválido.</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="numero_telefone">Telefone</label>
                                        <input type="text" class="form-control" id="numero_telefone" name="numero_telefone"
                                            maxlength="15">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="numero_celular">Celular</label>
                                        <input type="text" class="form-control" id="numero_celular" name="numero_celular"
                                            maxlength="15">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">E-mail <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3 text-primary"><i class="fas fa-lock mr-2"></i> Dados de Acesso</h5>

                            <div class="form-group">
                                <label for="password_usuario">Senha <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" id="password_usuario" class="form-control" name="password"
                                        required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary btn-toggle-password"
                                            id="togglePasswordUsuario">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="password_confirmacao">Confirmar Senha <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" id="password_confirmacao" class="form-control"
                                        name="password_confirmation" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary btn-toggle-password"
                                            id="togglePasswordConfirmacao">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

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

                            <hr class="my-4">

                            <h5 class="mb-3 text-primary"><i class="fas fa-file-upload mr-2"></i> Documentos</h5>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group file-upload-wrapper">
                                        <label class="font-weight-bold"><i class="fas fa-id-card"></i> Documento de
                                            Identidade</label>
                                        <input type="file" class="d-none" id="foto_documento" name="foto_documento"
                                            accept="image/*,.pdf">
                                        <label class="btn btn-outline-primary btn-block btn-file-upload mt-2"
                                            for="foto_documento">
                                            <i class="fas fa-upload"></i>
                                            <span class="file-upload-label-text" id="label_foto_documento">Escolher
                                                arquivo</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group file-upload-wrapper">
                                        <label class="font-weight-bold"><i class="fas fa-home"></i> Comprovante de
                                            Residência</label>
                                        <input type="file" class="d-none" id="comprovante_residencia"
                                            name="comprovante_residencia" accept="image/*,.pdf">
                                        <label class="btn btn-outline-primary btn-block btn-file-upload mt-2"
                                            for="comprovante_residencia">
                                            <i class="fas fa-upload"></i>
                                            <span class="file-upload-label-text" id="label_comprovante_residencia">Escolher
                                                arquivo</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group file-upload-wrapper">
                                        <label class="font-weight-bold"><i class="fas fa-graduation-cap"></i> Comprovante
                                            de Matrícula</label>
                                        <input type="file" class="d-none" id="comprovante_escolar"
                                            name="comprovante_escolar" accept="image/*,.pdf">
                                        <label class="btn btn-outline-primary btn-block btn-file-upload mt-2"
                                            for="comprovante_escolar">
                                            <i class="fas fa-upload"></i>
                                            <span class="file-upload-label-text" id="label_comprovante_escolar">Escolher
                                                arquivo</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3 text-primary"><i class="fas fa-money-check mr-2"></i> Dados Bancários</h5>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="numero_pis">Número PIS</label>
                                        <input type="text" class="form-control" id="numero_pis" name="numero_pis">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tipo_chave_pix">Tipo da Chave PIX</label>
                                        <select class="form-control" id="tipo_chave_pix" name="tipo_chave_pix">
                                            <option value="">Selecione o tipo</option>
                                            <option value="CPF">CPF</option>
                                            <option value="EMAIL">E-mail</option>
                                            <option value="TELEFONE">Telefone</option>
                                            <option value="ALEATORIA">Aleatória</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="chave_pix">Chave PIX</label>
                                <input type="text" class="form-control" id="chave_pix" name="chave_pix">
                            </div>
                        </div>

                        <!-- Coluna Direita -->
                        <div class="col-md-6">
                            <h5 class="mb-3 text-primary"><i class="fas fa-map-marker-alt mr-2"></i> Endereço</h5>

                            <div class="form-group">
                                <label for="numero_cep">CEP <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="numero_cep" name="numero_cep" required
                                    maxlength="9">
                            </div>

                            <div class="form-group">
                                <label for="endereco">Endereço</label>
                                <input type="text" class="form-control" id="endereco" name="endereco">
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="numero_endereco">Número</label>
                                        <input type="text" class="form-control" id="numero_endereco" name="numero_endereco">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="complemento_endereco">Complemento</label>
                                        <input type="text" class="form-control" id="complemento_endereco"
                                            name="complemento_endereco">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="bairro">Bairro</label>
                                        <input type="text" class="form-control" id="bairro" name="bairro">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fk_id_estado">Estado <span class="text-danger">*</span></label>
                                        <select class="form-control" id="fk_id_estado" name="fk_id_estado" required>
                                            <option value="">Escolha um estado</option>
                                            @foreach ($estados as $estado)
                                                <option value="{{ $estado->id_estado }}">{{ $estado->nm_estado }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fk_id_cidade">Cidade <span class="text-danger">*</span></label>
                                        <select class="form-control" id="fk_id_cidade" name="fk_id_cidade" required>
                                            <option value="">Escolha uma cidade</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <h5 class="mb-3 text-primary"><i class="fas fa-graduation-cap mr-2"></i> Dados Acadêmicos</h5>

                            <div class="form-group">
                                <label for="instituicao_ensino">Instituição de Ensino <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="instituicao_ensino" name="instituicao_ensino"
                                    required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="curso">Curso</label>
                                        <input type="text" class="form-control" id="curso" name="curso">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="nivel_curso">Nível do Curso</label>
                                        <input type="text" class="form-control" id="nivel_curso" name="nivel_curso">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="area_de_estagio">Área de Estágio</label>
                                <input type="text" class="form-control" id="area_de_estagio" name="area_de_estagio">
                            </div>

                            <hr class="my-4">

                            <div class="form-group">
                                <div class="terms-checkbox-wrapper">
                                    <input type="checkbox" id="aceitacao_termos" name="aceitacao_termos">
                                    <label for="aceitacao_termos" class="mb-0 terms-text">
                                        Aceito os Termos e Condições
                                        <a href="#" class="terms-link" data-toggle="modal" data-target="#termsModal">(Ler
                                            termos)</a>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary btn-lg btn-block" id="cadastrarBtn" disabled>
                                    <i class="fas fa-save mr-2"></i> Realizar Cadastro
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="form-loading" class="loading-overlay">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Carregando...</span>
        </div>
        <div class="mt-3 text-center">
            <strong>Processando...</strong><br>
            <span class="text-muted">Não feche ou recarregue a página</span>
        </div>
    </div>

    <!-- Modal de Termos -->
    <div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel"><i class="fas fa-file-contract mr-2"></i> Termos e
                        Condições</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p style="text-align:justify;">
                        Ao submeter este formulário, você autoriza o uso dos seus dados pessoais no
                        processo de cadastro para vagas de estágio e emissão de documentos correlatos.
                        Seus dados serão tratados de acordo com a Lei Geral de Proteção de Dados (LGPD).
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Validação de CPF
        function validarCPF(cpf) {
            cpf = cpf.replace(/[^\d]+/g, '');
            if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;

            let soma = 0;
            for (let i = 1; i <= 9; i++) {
                soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
            }
            let resto = (soma * 10) % 11;
            if (resto === 10 || resto === 11) resto = 0;
            if (resto !== parseInt(cpf.substring(9, 10))) return false;

            soma = 0;
            for (let i = 1; i <= 10; i++) {
                soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
            }
            resto = (soma * 10) % 11;
            if (resto === 10 || resto === 11) resto = 0;
            if (resto !== parseInt(cpf.substring(10, 11))) return false;

            return true;
        }

        // Mostra alertas
        function showAlert(containerId, type, messages) {
            const container = document.getElementById(containerId);
            if (!container) return;

            const list = Array.isArray(messages) ? messages : [messages];
            const alertClass = type === 'danger' ? 'alert-danger' :
                type === 'success' ? 'alert-success' :
                    type === 'warning' ? 'alert-warning' : 'alert-info';

            container.innerHTML = `
                                                                        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                                                                            <ul class="mb-0">
                                                                                ${list.map(m => `<li>${m}</li>`).join('')}
                                                                            </ul>
                                                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                    `;

            // Scroll para o alerta
            container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // Inicialização após o DOM carregar
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('form-estagiario');
            const loading = document.getElementById('form-loading');
            const cadastrarBtn = document.getElementById('cadastrarBtn');

            // Elementos de senha
            const pwd = document.getElementById('password_usuario');
            const pwd2 = document.getElementById('password_confirmacao');
            const togglePwd = document.getElementById('togglePasswordUsuario');
            const togglePwd2 = document.getElementById('togglePasswordConfirmacao');

            // Elementos de validação de senha
            const ruleLength = document.getElementById('rule-length');
            const ruleLower = document.getElementById('rule-lower');
            const ruleUpper = document.getElementById('rule-upper');
            const ruleNumber = document.getElementById('rule-number');
            const ruleSpecial = document.getElementById('rule-special');
            const ruleMatch = document.getElementById('rule-match');
            const strengthBar = document.getElementById('password-strength-bar');
            const strengthText = document.getElementById('password-strength-text');

            // Toggle mostrar/ocultar senha
            togglePwd.addEventListener('click', function () {
                const type = pwd.type === 'password' ? 'text' : 'password';
                pwd.type = type;
                this.innerHTML = type === 'password' ?
                    '<i class="fas fa-eye"></i>' :
                    '<i class="fas fa-eye-slash"></i>';
            });

            togglePwd2.addEventListener('click', function () {
                const type = pwd2.type === 'password' ? 'text' : 'password';
                pwd2.type = type;
                this.innerHTML = type === 'password' ?
                    '<i class="fas fa-eye"></i>' :
                    '<i class="fas fa-eye-slash"></i>';
            });

            // Validação de senha
            function validatePasswords() {
                const p = pwd.value;
                const p2 = pwd2.value;

                const checks = {
                    length: p.length >= 8,
                    lower: /[a-z]/.test(p),
                    upper: /[A-Z]/.test(p),
                    number: /[0-9]/.test(p),
                    special: /[^a-zA-Z0-9]/.test(p),
                    match: p.length > 0 && p === p2
                };

                // Atualizar requisitos visuais
                updateRequirement(ruleLength, checks.length);
                updateRequirement(ruleLower, checks.lower);
                updateRequirement(ruleUpper, checks.upper);
                updateRequirement(ruleNumber, checks.number);
                updateRequirement(ruleSpecial, checks.special);
                updateRequirement(ruleMatch, checks.match);

                // Calcular força da senha
                const score = Object.values(checks).filter(Boolean).length - 1; // Excluir match do score
                updateStrengthBar(score);

                // Retornar se todas as condições foram atendidas
                return Object.values(checks).every(Boolean);
            }

            function updateRequirement(element, isValid) {
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
                const percentage = (score / 5) * 100;
                strengthBar.style.width = percentage + '%';

                let text = 'Muito fraca';
                let colorClass = 'bg-danger';

                if (score === 2) {
                    text = 'Fraca';
                    colorClass = 'bg-danger';
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

            // Eventos de input nas senhas
            pwd.addEventListener('input', function () {
                validatePasswords();
                checkFormValid();
            });

            pwd2.addEventListener('input', function () {
                validatePasswords();
                checkFormValid();
            });

            // Validação de CPF
            const cpfInput = document.getElementById('numero_cpf');
            const cpfError = document.getElementById('cpfError');

            cpfInput.addEventListener('input', function () {
                const cpfValido = validarCPF(this.value);

                if (this.value.length > 0 && !cpfValido) {
                    this.classList.add('is-invalid');
                    cpfError.style.display = 'block';
                } else {
                    this.classList.remove('is-invalid');
                    cpfError.style.display = 'none';
                }

                checkFormValid();
            });

            // Checkbox de termos
            const termosCheckbox = document.getElementById('aceitacao_termos');
            termosCheckbox.addEventListener('change', checkFormValid);

            // Verifica se o formulário está válido
            function checkFormValid() {
                const cpfValido = validarCPF(cpfInput.value);
                const termosAceitos = termosCheckbox.checked;
                const senhaValida = validatePasswords();

                cadastrarBtn.disabled = !(cpfValido && termosAceitos && senhaValida);
            }

            // Carregar cidades ao selecionar estado
            const estadoSelect = document.getElementById('fk_id_estado');
            const cidadeSelect = document.getElementById('fk_id_cidade');

            estadoSelect.addEventListener('change', function () {
                const estadoId = this.value;
                cidadeSelect.innerHTML = '<option value="">Carregando...</option>';

                if (!estadoId) {
                    cidadeSelect.innerHTML = '<option value="">Escolha uma cidade</option>';
                    return;
                }

                fetch(`/estados/${estadoId}/cidades`)
                    .then(response => response.json())
                    .then(cidades => {
                        cidadeSelect.innerHTML = '<option value="">Escolha uma cidade</option>';
                        cidades.forEach(cidade => {
                            const option = document.createElement('option');
                            option.value = cidade.id_cidade;
                            option.textContent = cidade.nm_cidade;
                            cidadeSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Erro ao carregar cidades:', error);
                        cidadeSelect.innerHTML = '<option value="">Erro ao carregar cidades</option>';
                    });
            });

            // Atualizar labels dos arquivos
            document.getElementById('foto_documento').addEventListener('change', function (e) {
                const fileName = e.target.files[0] ? e.target.files[0].name : 'Escolher arquivo';
                document.getElementById('label_foto_documento').textContent = fileName;
            });

            document.getElementById('comprovante_residencia').addEventListener('change', function (e) {
                const fileName = e.target.files[0] ? e.target.files[0].name : 'Escolher arquivo';
                document.getElementById('label_comprovante_residencia').textContent = fileName;
            });

            document.getElementById('comprovante_escolar').addEventListener('change', function (e) {
                const fileName = e.target.files[0] ? e.target.files[0].name : 'Escolher arquivo';
                document.getElementById('label_comprovante_escolar').textContent = fileName;
            });

            // Submissão do formulário
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                loading.style.display = 'flex';
                showAlert('alerts', 'info', 'Enviando dados...');

                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                    .then(async response => {
                        loading.style.display = 'none';

                        if (!response.ok) {
                            const data = await response.json().catch(() => ({}));
                            const errors = data.errors ?
                                Object.values(data.errors).flat() : ['Erro ao enviar o formulário.'];
                            showAlert('alerts', 'danger', errors);
                            return;
                        }

                        const data = await response.json();
                        // Redireciona para a página de verificação de e-mail
                        if (data.redirect) {
                            window.location.href = data.redirect;
                            return;
                        }
                        // Fallback: mensagem
                        showAlert('alerts', 'success', 'Cadastro realizado com sucesso. Verifique seu e-mail.');
                    })
                    .catch(error => {
                        loading.style.display = 'none';
                        console.error('Erro:', error);
                        showAlert('alerts', 'danger', 'Falha de rede. Tente novamente.');
                    });
            });

            // Inicializar validações
            validatePasswords();
            checkFormValid();
        });
    </script>
@endsection