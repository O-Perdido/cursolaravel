@extends('layouts.main')

@section('title', 'Login')

@section('content')

    <!-- Nome do sistema -->
    <div class="text-center">
        <h1 class="">Sistema de Integração e Gestão de Estágios</h1>
    </div>
    <hr class="my-4">
    <div class="container mt-5">
        <div class="row justify-content-center g-4">
            <!-- Coluna: Login -->
            <div class="col-12 col-md-8 col-lg-5">
                <h3>Realizar Login</h3>
                <div class="card card-body bg-light">
                    @if(session('status'))
                        <div class="alert alert-success">{{ session('status') }}</div>
                    @endif
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        @method('POST')
                        <div class="form-group mb-3 mt-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="password">Senha</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" style="border: none;"
                                        onclick="togglePasswordVisibility()">
                                        <img style="width: 25px; height: 25px;" src="{{ asset('images/eye_visible.png') }}"
                                            id="password-icon" alt="Mostrar Senha">
                                    </button>
                                </div>
                            </div>
                        </div>
                        <script>
                            function togglePasswordVisibility() {
                                var passwordField = document.getElementById('password');
                                var passwordIcon = document.getElementById('password-icon');
                                if (passwordField.type === 'password') {
                                    passwordField.type = 'text';
                                    passwordIcon.src = '{{ asset('images/eye_not_visible.png') }}';
                                } else {
                                    passwordField.type = 'password';
                                    passwordIcon.src = '{{ asset('images/eye_visible.png') }}';
                                }
                            }
                        </script>
                        <div class="form-group form-check mb-3 mt-3 d-flex justify-content-between align-items-center">
                            <a href="{{ route('password.request') }}" class="small">Redefinir senha</a>
                            <span class="float-right" style="font-size: 0.8em"><a
                                    href="https://api.whatsapp.com/send?phone=5548991468761&text=Ol%C3%A1%21%20Tentei%20realizar%20o%20login%20e%20esqueci%20minha%20senha%2C%20tem%20como%20me%20ajudar%3F"
                                    target="_blank">Precisa de ajuda?</a></span>
                        </div>
                        @if(session('error'))
                            <div class="alert alert-danger mt-2">{{ session('error') }}</div>
                        @endif
                        <button type="submit" class="btn btn-primary w-100 mb-3" id="save_button">
                            <i class="fas fa-sign-in-alt me-2"></i>Entrar
                        </button>
                    </form>

                    <!-- Divisor -->
                    <div class="text-center my-3">
                        <hr class="my-2">
                        <small class="text-muted">Não tem conta?</small>
                    </div>

                    <!-- Botão de Cadastro para Estagiários -->
                    <a href="{{ route('novo-estagiario-ajax-create') }}" class="btn btn-outline-success w-100 mb-3">
                        <i class="fas fa-user-plus me-2"></i>Cadastrar como Estagiário
                    </a>

                    <button type="button" class="btn btn-outline-secondary w-100 mb-3" data-bs-toggle="modal"
                        data-bs-target="#modal-recuperar-acesso">
                        <i class="fas fa-user-check me-2"></i>Já tenho cadastro, mas não consigo acessar
                    </button>

                    <!-- Alerta informativo -->
                    <div class="alert alert-info mb-0 py-2" style="font-size: 0.85rem;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Atenção:</strong> O cadastro online é exclusivo para <strong>estagiários</strong>.<br>
                        <small>
                            <i class="fas fa-building me-1"></i><strong>Empresas</strong> ou
                            <i class="fas fa-school me-1"></i><strong>Instituições de Ensino</strong>:
                            <a href="https://api.whatsapp.com/send?phone=5548991468761&text=Ol%C3%A1%21%20Gostaria%20de%20cadastrar%20minha%20empresa%2Finstituição%20no%20SIGE"
                                target="_blank" class="alert-link">
                                <i class="fab fa-whatsapp"></i> Entre em contato
                            </a>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Coluna: PWA Install (aparece ao lado no desktop e abaixo no mobile) -->
            <div class="col-12 col-md-8 col-lg-5">
                <h3>Instalar o SIGEBR</h3>
                <!-- PWA Install Card (mostrado quando PWA for instalável) -->
                <div class="card border-success shadow-sm" id="pwa-login-card" style="display: none; animation: slideInUp 0.5s ease-out;
                                                                                               background: linear-gradient(135deg, #198754 0%, #20c997 100%);
                                                                                               color: #fff;
                                                                                               border-radius: 10px;
                                                                                               overflow: hidden;">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-download" style="font-size: 2rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fw-bold mb-2">Instale o SIGEBR no seu celular ou computador</h5>

                                <!-- Bloco: botão padrão (Android/desktop) -->
                                <div id="pwa-install-block" style="display:none">
                                    <p class="mb-2" style="font-size: 0.9rem; line-height: 1.3;">
                                        Abre em tela cheia e carrega mais rápido.
                                    </p>
                                    <button id="install-app-login" class="btn btn-light fw-bold"
                                        style="border-radius: 20px; box-shadow: 0 3px 10px rgba(0,0,0,0.25); padding:6px 16px;">
                                        <i class="fas fa-plus-square"></i> Instalar Agora
                                    </button>
                                </div>

                                <!-- Bloco: instruções iOS (Safari) -->
                                <div id="pwa-ios-instructions" style="display:none">
                                    <p class="mb-2" style="font-size: 0.9rem; line-height: 1.3;">
                                        No iPhone/iPad, instale pelo Safari:
                                    </p>
                                    <ol class="mb-2" style="font-size: 0.85rem; padding-left: 1.1rem; line-height:1.3;">
                                        <li>Toque no botão Compartilhar (quadrado com seta para cima).</li>
                                        <li>Escolha “Adicionar à Tela de Início”.</li>
                                        <li>Confirme em “Adicionar”.</li>
                                    </ol>
                                    <div id="ios-browser-tip" class="small" style="display:none; opacity:0.9;">
                                        Dica: se estiver usando Chrome/Edge no iOS, abra este site no Safari para instalar.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal: Recuperar Acesso do Estagiário -->
    <div class="modal fade" id="modal-recuperar-acesso" tabindex="-1" aria-labelledby="modal-recuperar-acesso-label"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-recuperar-acesso-label">Recuperar acesso do estagiário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Informe seu CPF para localizar seu cadastro. Se encontrarmos, vamos orientar o próximo passo.
                    </p>

                    <form id="cpf-recovery-form" class="mb-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-8">
                                <label for="cpf_recuperacao" class="form-label">CPF</label>
                                <input type="text" class="form-control" id="cpf_recuperacao" placeholder="000.000.000-00"
                                    maxlength="14" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <button type="submit" class="btn btn-primary w-100" id="cpf-recovery-submit">
                                    <i class="fas fa-search me-2"></i>Buscar
                                </button>
                            </div>
                        </div>
                    </form>

                    <div id="cpf-recovery-result"></div>

                    <form id="create-user-form" style="display:none;" class="mt-3">
                        <input type="hidden" id="create-user-estagiario-id">

                        <div class="mb-3">
                            <label for="create-user-email" class="form-label">Email para acesso</label>
                            <input type="email" class="form-control" id="create-user-email" required>
                        </div>

                        <div class="mb-3">
                            <label for="create-user-password" class="form-label">Senha</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="create-user-password" required>
                                <button type="button" class="btn btn-outline-secondary btn-toggle-password"
                                    id="toggle-create-password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="create-user-password-confirm" class="form-label">Confirmar senha</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="create-user-password-confirm" required>
                                <button type="button" class="btn btn-outline-secondary btn-toggle-password"
                                    id="toggle-create-password-confirm">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="password-requirements">
                            <h6>Requisitos da senha:</h6>
                            <div>
                                <span class="requirement-item invalid" id="rule-length-rec">
                                    <i class="fas fa-times-circle"></i> 8+ caracteres
                                </span>
                                <span class="requirement-item invalid" id="rule-lower-rec">
                                    <i class="fas fa-times-circle"></i> minúscula
                                </span>
                                <span class="requirement-item invalid" id="rule-upper-rec">
                                    <i class="fas fa-times-circle"></i> MAIÚSCULA
                                </span>
                                <span class="requirement-item invalid" id="rule-number-rec">
                                    <i class="fas fa-times-circle"></i> número
                                </span>
                                <span class="requirement-item invalid" id="rule-special-rec">
                                    <i class="fas fa-times-circle"></i> especial
                                </span>
                                <span class="requirement-item invalid" id="rule-match-rec">
                                    <i class="fas fa-times-circle"></i> coincidem
                                </span>
                            </div>
                            <div class="password-strength-bar">
                                <div id="password-strength-bar-rec" class="progress-bar bg-danger" role="progressbar"
                                    style="width: 0%"></div>
                            </div>
                            <div id="password-strength-text-rec" class="password-strength-text text-muted">
                                Força: Muito fraca
                            </div>
                        </div>

                        <div id="create-user-feedback" class="mt-2"></div>

                        <button type="submit" class="btn btn-primary w-100" id="create-user-submit">
                            <i class="fas fa-user-lock me-2"></i>Criar meu acesso
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .password-requirements {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 16px;
        }

        .password-requirements h6 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
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

    <script>
        (function () {
            const pwaCard = document.getElementById('pwa-login-card');
            const installBlock = document.getElementById('pwa-install-block');
            const iosBlock = document.getElementById('pwa-ios-instructions');
            const iosTip = document.getElementById('ios-browser-tip');

            const ua = navigator.userAgent || navigator.vendor || window.opera;
            const isIOS = /iphone|ipad|ipod/i.test(ua);
            const isSafari = /^((?!chrome|android).)*safari/i.test(ua);
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;

            // Se já está instalado, não mostra o card
            if (isStandalone) return;

            // iOS: exibe instruções (instalação via Safari), oculta o botão padrão
            // Em outras plataformas, o layout principal controla a exibição via beforeinstallprompt
            if (isIOS) {
                if (pwaCard) pwaCard.style.display = '';
                if (iosBlock) iosBlock.style.display = '';
                if (installBlock) installBlock.style.display = 'none';
                if (!isSafari && iosTip) iosTip.style.display = '';
            }
        })();
    </script>

    <script>
        (function () {
            const buscarForm = document.getElementById('cpf-recovery-form');
            if (!buscarForm) return;

            const cpfInput = document.getElementById('cpf_recuperacao');
            const resultBox = document.getElementById('cpf-recovery-result');
            const buscarBtn = document.getElementById('cpf-recovery-submit');

            const createForm = document.getElementById('create-user-form');
            const createFeedback = document.getElementById('create-user-feedback');
            const createSubmit = document.getElementById('create-user-submit');
            const createEmail = document.getElementById('create-user-email');
            const createId = document.getElementById('create-user-estagiario-id');

            const pwd = document.getElementById('create-user-password');
            const pwd2 = document.getElementById('create-user-password-confirm');
            const togglePwd = document.getElementById('toggle-create-password');
            const togglePwd2 = document.getElementById('toggle-create-password-confirm');

            const ruleLength = document.getElementById('rule-length-rec');
            const ruleLower = document.getElementById('rule-lower-rec');
            const ruleUpper = document.getElementById('rule-upper-rec');
            const ruleNumber = document.getElementById('rule-number-rec');
            const ruleSpecial = document.getElementById('rule-special-rec');
            const ruleMatch = document.getElementById('rule-match-rec');
            const strengthBar = document.getElementById('password-strength-bar-rec');
            const strengthText = document.getElementById('password-strength-text-rec');

            const buscarUrl = "{{ route('estagiarios.buscar-por-cpf') }}";
            const criarUsuarioBaseUrl = "{{ url('/estagiarios') }}";
            const cadastroUrl = "{{ route('novo-estagiario-ajax-create') }}";
            const whatsappUrl = "https://api.whatsapp.com/send?phone=5548991468761&text=Ol%C3%A1%21%20Tenho%20dificuldade%20para%20acessar%20meu%20cadastro%20de%20estagi%C3%A1rio%20no%20SIGE";
            const csrfToken = "{{ csrf_token() }}";

            function setResult(html) {
                resultBox.innerHTML = html;
            }

            function resetCreateForm() {
                createForm.style.display = 'none';
                createFeedback.innerHTML = '';
                createEmail.value = '';
                createId.value = '';
                pwd.value = '';
                pwd2.value = '';
                updateRequirement(ruleLength, false);
                updateRequirement(ruleLower, false);
                updateRequirement(ruleUpper, false);
                updateRequirement(ruleNumber, false);
                updateRequirement(ruleSpecial, false);
                updateRequirement(ruleMatch, false);
                updateStrengthBar(0);
            }

            function showFeedback(type, messages) {
                const list = Array.isArray(messages) ? messages : [messages];
                createFeedback.innerHTML = `
                            <div class="alert alert-${type} mt-2" role="alert">
                                <ul class="mb-0">${list.map(m => `<li>${m}</li>`).join('')}</ul>
                            </div>
                        `;
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

                updateRequirement(ruleLength, checks.length);
                updateRequirement(ruleLower, checks.lower);
                updateRequirement(ruleUpper, checks.upper);
                updateRequirement(ruleNumber, checks.number);
                updateRequirement(ruleSpecial, checks.special);
                updateRequirement(ruleMatch, checks.match);

                const score = Object.values(checks).filter(Boolean).length - 1;
                updateStrengthBar(score);

                return Object.values(checks).every(Boolean);
            }

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

            pwd.addEventListener('input', validatePasswords);
            pwd2.addEventListener('input', validatePasswords);

            buscarForm.addEventListener('submit', async function (event) {
                event.preventDefault();
                resetCreateForm();
                setResult('');

                const cpf = (cpfInput.value || '').trim();
                if (!cpf) return;

                buscarBtn.disabled = true;
                buscarBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Buscando...';

                try {
                    const response = await fetch(buscarUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ cpf })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        const message = data?.message || 'Não foi possível validar o CPF.';
                        setResult(`<div class="alert alert-danger">${message}</div>`);
                        return;
                    }

                    if (data.status === 'not_found') {
                        setResult(`
                                    <div class="alert alert-warning">${data.message}</div>
                                    <a href="${cadastroUrl}" class="btn btn-outline-success w-100">
                                        <i class="fas fa-user-plus me-2"></i>Fazer cadastro de estagiário
                                    </a>
                                `);
                        return;
                    }

                    if (data.status === 'multiple') {
                        setResult(`
                                    <div class="alert alert-warning">${data.message}</div>
                                    <a href="${whatsappUrl}" target="_blank" class="btn btn-outline-primary w-100">
                                        <i class="fab fa-whatsapp me-2"></i>Entrar em contato
                                    </a>
                                `);
                        return;
                    }

                    if (data.status === 'has_user') {
                        setResult(`
                                    <div class="alert alert-info">
                                        Você já tem um usuário de acesso cadastrado com o e-mail <strong>${data.user_email}</strong>
                                        Se esse e-mail estiver incorreto, entre em contato.
                                    </div>
                                    <a href="${whatsappUrl}" target="_blank" class="btn btn-outline-primary w-100">
                                        <i class="fab fa-whatsapp me-2"></i>Entrar em contato
                                    </a>
                                `);
                        return;
                    }

                    if (data.status === 'can_create_user') {
                        setResult(`
                                    <div class="alert alert-success">
                                        Encontramos seu cadastro. Crie seu acesso para entrar no sistema.
                                    </div>
                                `);

                        createId.value = data.estagiario.id;
                        createEmail.value = data.estagiario.email || '';
                        createForm.style.display = '';
                        return;
                    }

                    setResult('<div class="alert alert-warning">Não foi possível concluir a busca.</div>');
                } catch (error) {
                    setResult('<div class="alert alert-danger">Erro ao buscar o CPF. Tente novamente.</div>');
                } finally {
                    buscarBtn.disabled = false;
                    buscarBtn.innerHTML = '<i class="fas fa-search me-2"></i>Buscar';
                }
            });

            createForm.addEventListener('submit', async function (event) {
                event.preventDefault();
                createFeedback.innerHTML = '';

                if (!validatePasswords()) {
                    showFeedback('warning', 'Confira os requisitos de senha antes de continuar.');
                    return;
                }

                const estagiarioId = createId.value;
                if (!estagiarioId) return;

                createSubmit.disabled = true;
                createSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Salvando...';

                try {
                    const response = await fetch(`${criarUsuarioBaseUrl}/${estagiarioId}/criar-usuario`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            email: createEmail.value.trim(),
                            password: pwd.value,
                            password_confirmation: pwd2.value
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (response.status === 409) {
                            setResult(`
                                        <div class="alert alert-info">${data.message}</div>
                                        <a href="${whatsappUrl}" target="_blank" class="btn btn-outline-primary w-100">
                                            <i class="fab fa-whatsapp me-2"></i>Entrar em contato
                                        </a>
                                    `);
                            resetCreateForm();
                            return;
                        }

                        const errors = data?.errors ? Object.values(data.errors).flat() : [data?.message || 'Erro ao salvar.'];
                        showFeedback('danger', errors);
                        return;
                    }

                    setResult(`
                                <div class="alert alert-success">
                                    Usuário criado com sucesso. Agora você já pode entrar com o email ${data.user.email}.
                                </div>
                            `);
                    resetCreateForm();
                } catch (error) {
                    showFeedback('danger', 'Erro ao criar o usuário. Tente novamente.');
                } finally {
                    createSubmit.disabled = false;
                    createSubmit.innerHTML = '<i class="fas fa-user-lock me-2"></i>Criar meu acesso';
                }
            });

            const modal = document.getElementById('modal-recuperar-acesso');
            if (modal) {
                modal.addEventListener('hidden.bs.modal', function () {
                    buscarForm.reset();
                    setResult('');
                    resetCreateForm();
                });
            }
        })();
    </script>

@endsection