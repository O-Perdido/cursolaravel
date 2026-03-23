@extends('layouts.main')

@section('title', 'SIGE Concursos | Login do Candidato')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="text-center mb-4">
                <h2 class="mb-2">Área do Candidato</h2>
                <p class="text-muted mb-0">Localize seu cadastro pelo CPF e acesse com e-mail e senha.</p>
            </div>

            @if(session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-lg-5">
                    <div class="row g-4 align-items-start">
                        <div class="col-lg-5">
                            <div class="border rounded-3 p-4 h-100 bg-light-subtle">
                                <h5 class="mb-3">1. Informe seu CPF</h5>
                                <p class="text-muted small mb-3">
                                    Vamos conferir se seu cadastro já existe. Se existir, liberamos o formulário de acesso
                                    logo abaixo.
                                </p>

                                <form id="cpf-lookup-form">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="cpf_recuperacao" class="form-label">CPF</label>
                                        <input type="text" class="form-control" id="cpf_recuperacao" maxlength="14"
                                            placeholder="000.000.000-00" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100" id="cpf-lookup-submit">
                                        <i class="fa-solid fa-magnifying-glass"></i> Buscar Cadastro
                                    </button>
                                </form>

                                <div id="cpf-lookup-feedback" class="mt-3"></div>
                            </div>
                        </div>

                        <div class="col-lg-7">
                            <div class="border rounded-3 p-4 h-100">
                                <h5 class="mb-3">2. Faça seu login</h5>
                                <p class="text-muted small mb-3">
                                    O acesso do candidato é por e-mail e senha. O CPF é usado aqui apenas para localizar e
                                    validar o cadastro.
                                </p>

                                <form action="{{ route('sigeconcursos.candidato.auth') }}" method="POST"
                                    id="candidate-login-form" style="display: none;">
                                    @csrf
                                    <input type="hidden" name="cpf" id="login_cpf" value="{{ old('cpf') }}">

                                    <div class="mb-3">
                                        <label for="login_email" class="form-label">E-mail</label>
                                        <input type="email" class="form-control" id="login_email" name="email"
                                            value="{{ old('email') }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="login_password" class="form-label">Senha</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="login_password" name="password"
                                                required>
                                            <button type="button" class="btn btn-outline-secondary"
                                                id="toggle-login-password">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-column flex-md-row justify-content-between gap-2 mb-3">
                                        <a href="{{ route('password.request') }}" class="small">Esqueci minha senha</a>
                                        <a href="{{ route('sigeconcursos.candidato.cadastro') }}" class="small">Ainda não
                                            tenho cadastro</a>
                                    </div>

                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fa-solid fa-right-to-bracket"></i> Entrar na Área do Candidato
                                    </button>
                                </form>

                                <div id="login-placeholder" class="text-muted border rounded-3 p-4 bg-light-subtle">
                                    Localize primeiro seu CPF para liberar o formulário de acesso.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const lookupForm = document.getElementById('cpf-lookup-form');
            const feedback = document.getElementById('cpf-lookup-feedback');
            const submitButton = document.getElementById('cpf-lookup-submit');
            const loginForm = document.getElementById('candidate-login-form');
            const loginPlaceholder = document.getElementById('login-placeholder');
            const loginCpf = document.getElementById('login_cpf');
            const loginEmail = document.getElementById('login_email');
            const loginPassword = document.getElementById('login_password');
            const togglePassword = document.getElementById('toggle-login-password');
            const cpfInput = document.getElementById('cpf_recuperacao');

            togglePassword.addEventListener('click', function () {
                loginPassword.type = loginPassword.type === 'password' ? 'text' : 'password';
                this.innerHTML = loginPassword.type === 'password'
                    ? '<i class="fa-solid fa-eye"></i>'
                    : '<i class="fa-solid fa-eye-slash"></i>';
            });

            lookupForm.addEventListener('submit', async function (event) {
                event.preventDefault();

                submitButton.disabled = true;
                feedback.innerHTML = '';

                const formData = new FormData();
                formData.append('cpf', cpfInput.value);
                formData.append('_token', '{{ csrf_token() }}');

                try {
                    const response = await fetch('{{ route('sigeconcursos.candidato.buscar-cpf') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json'
                        },
                        body: formData,
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        feedback.innerHTML = `<div class="alert alert-danger mb-0">${data.message ?? 'Não foi possível validar o CPF informado.'}</div>`;
                        loginForm.style.display = 'none';
                        loginPlaceholder.style.display = 'block';
                        return;
                    }

                    if (data.status === 'login_ready') {
                        feedback.innerHTML = `<div class="alert alert-success mb-0">${data.message}</div>`;
                        loginCpf.value = cpfInput.value;
                        loginEmail.value = data.email ?? '';
                        loginForm.style.display = 'block';
                        loginPlaceholder.style.display = 'none';
                        loginPassword.focus();
                        return;
                    }

                    if (data.status === 'not_found') {
                        feedback.innerHTML = `
                                <div class="alert alert-warning mb-0">
                                    ${data.message}
                                    <div class="mt-2">
                                        <a href="${data.cadastro_url}" class="btn btn-sm btn-outline-primary">Cadastrar agora</a>
                                    </div>
                                </div>`;
                        loginForm.style.display = 'none';
                        loginPlaceholder.style.display = 'block';
                        return;
                    }

                    feedback.innerHTML = `<div class="alert alert-danger mb-0">${data.message ?? 'Não foi possível liberar o acesso.'}</div>`;
                    loginForm.style.display = 'none';
                    loginPlaceholder.style.display = 'block';
                } catch (error) {
                    feedback.innerHTML = '<div class="alert alert-danger mb-0">Falha ao consultar o cadastro. Tente novamente.</div>';
                    loginForm.style.display = 'none';
                    loginPlaceholder.style.display = 'block';
                } finally {
                    submitButton.disabled = false;
                }
            });

            @if(old('cpf') && old('email'))
                loginForm.style.display = 'block';
                loginPlaceholder.style.display = 'none';
                cpfInput.value = '{{ old('cpf') }}';
            @endif
            });
    </script>
@endsection