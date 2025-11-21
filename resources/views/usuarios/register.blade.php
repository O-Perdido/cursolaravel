@extends('layouts.main')

@section('title', 'Cadastrar Usuário')

@section('content')
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 90vh;">
        <div class="col-12 col-md-10 col-lg-7">
            <div class="card shadow-lg border-0 rounded-4 p-4 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2 class="mb-0" style="font-weight: 600;">Cadastrar Novo Usuário</h2>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Voltar</a>
                </div>
                <form action="{{ route('usuarios.store') }}" method="POST" autocomplete="off">
                    @csrf
                    @method('POST')
                    <div class="form-group mb-3">
                        <label for="nivel">Selecione o tipo de Usuário</label>
                        <select class="form-control" name="nivel" id="nivel" onchange="toggleNomeUnidade()">
                            <option value="" disabled selected>Selecione...</option>
                            <option value="admin">Administrador</option>
                            <option value="operador">Operador</option>
                            <option value="empresa">Unidade Concedente</option>
                            <option value="estagiario">Estagiário</option>
                        </select>
                    </div>

                    <!-- Campo de seleção de Estagiário (fora do select, exibido condicionalmente) -->
                    <div class="form-group mb-3" id="estagiario-group" style="display: none;">
                        <label for="fk_id_estagiario" class="form-label">Selecione o Estagiário</label>
                        <div style="position: relative;">
                            <input type="text" class="form-control" id="estagiario_search"
                                placeholder="Digite para buscar..." autocomplete="off">
                            <select class="form-control mt-2" id="fk_id_estagiario" name="fk_id_estagiario" size="5"
                                style="display:none; position: absolute; top: 60px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                                @if(isset($estagiarios))
                                    @foreach($estagiarios->sortBy('nome') as $estagiario)
                                        <option value="{{ $estagiario->id }}" data-nome="{{ $estagiario->nome }}"
                                            data-email="{{ $estagiario->email }}" data-cpf="{{ $estagiario->cpf }}"
                                            title="{{ $estagiario->nome }}">
                                            {{ $estagiario->nome }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-3" id="nome-group">
                        <label for="nome">Nome do Usuário</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" readonly>
                        @error('name')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-3" id="empresa-group" style="display: none;">
                        <label for="fk_id_empresa" class="form-label">Selecione a Unidade Concedente</label>
                        <div style="position: relative;">
                            <input type="text" class="form-control" id="empresa_search" placeholder="Digite para buscar..."
                                autocomplete="off">
                            <select class="form-control mt-2" id="fk_id_empresa" name="fk_id_empresa" size="5"
                                style="display:none; position: absolute; top: 60px; left: 0; width: 400px; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                                @foreach($empresas->sortBy('nome_empresa') as $empresa)
                                    <option value="{{ $empresa->id_empresa }}" data-nome="{{ $empresa->nome_empresa }}"
                                        title="{{ $empresa->nome_empresa }}">
                                        {{ $empresa->nome_empresa }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}">
                        @error('email')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label for="password">Senha</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password"
                                value="{{ old('password') }}">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" style="border: none;"
                                    onclick="togglePasswordVisibility()">
                                    <img style="width: 25px; height: 25px;" src="{{ asset('images/eye_visible.png') }}"
                                        id="password-icon" alt="Mostrar Senha">
                                </button>
                                <button type="button" class="btn btn-outline-primary ml-2"
                                    style="border: none; display: none;" id="btn-gerar-senha" onclick="gerarSenhaPadrao()">
                                    Gerar Senha Padrão
                                </button>
                            </div>
                        </div>
                        @error('password')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
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
                    <div class="form-group d-flex justify-content-end mt-4 mb-0">
                        <button type="submit" class="btn btn-primary px-4 py-2 shadow-sm" id="save_button"
                            style="font-weight: 500; font-size: 1.1rem;">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleNomeUnidade() {
            var nivel = document.getElementById('nivel').value;
            var nomeGroup = document.getElementById('nome-group');
            var unidadeGroup = document.getElementById('empresa-group');
            var estagiarioGroup = document.getElementById('estagiario-group');

            if (nivel === 'empresa') {
                nomeGroup.style.display = 'none';
                unidadeGroup.style.display = 'block';
                estagiarioGroup.style.display = 'none';
            } else if (nivel === 'estagiario') {
                nomeGroup.style.display = 'none';
                unidadeGroup.style.display = 'none';
                estagiarioGroup.style.display = 'block';
            } else {
                nomeGroup.style.display = 'block';
                unidadeGroup.style.display = 'none';
                estagiarioGroup.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Empresa
            const searchInput = document.getElementById('empresa_search');
            const select = document.getElementById('fk_id_empresa');
            const options = select ? Array.from(select.options) : [];

            if (searchInput && select) {
                searchInput.addEventListener('focus', function () {
                    select.style.display = 'block';
                    setTimeout(() => {
                        searchInput.select();
                    }, 0);
                });

                searchInput.addEventListener('input', function () {
                    const value = this.value.toLowerCase();
                    select.innerHTML = '';
                    options.forEach(option => {
                        if (option.text.toLowerCase().includes(value) || option.value === "") {
                            select.appendChild(option.cloneNode(true));
                        }
                    });
                    select.style.display = 'block';
                });

                select.addEventListener('change', function () {
                    const selected = select.options[select.selectedIndex];
                    searchInput.value = selected.text;
                    select.style.display = 'none';
                    // Atualiza o campo nome do usuário com o nome da empresa
                    document.getElementById('name').value = selected.getAttribute('data-nome');
                });

                document.addEventListener('click', function (e) {
                    if (!searchInput.contains(e.target) && !select.contains(e.target)) {
                        select.style.display = 'none';
                    }
                });
            }

            // Estagiário
            const estagiarioSearch = document.getElementById('estagiario_search');
            const estagiarioSelect = document.getElementById('fk_id_estagiario');
            const estagiarioOptions = estagiarioSelect ? Array.from(estagiarioSelect.options) : [];

            if (estagiarioSearch && estagiarioSelect) {
                estagiarioSearch.addEventListener('focus', function () {
                    estagiarioSelect.style.display = 'block';
                    setTimeout(() => {
                        estagiarioSearch.select();
                    }, 0);
                });

                estagiarioSearch.addEventListener('input', function () {
                    const value = this.value.toLowerCase();
                    estagiarioSelect.innerHTML = '';
                    estagiarioOptions.forEach(option => {
                        if (option.text.toLowerCase().includes(value) || option.value === "") {
                            estagiarioSelect.appendChild(option.cloneNode(true));
                        }
                    });
                    estagiarioSelect.style.display = 'block';
                });

                estagiarioSelect.addEventListener('change', function () {
                    const selected = estagiarioSelect.options[estagiarioSelect.selectedIndex];
                    estagiarioSearch.value = selected.text;
                    estagiarioSelect.style.display = 'none';
                    // Preenche o campo de email com o email do estagiário
                    document.getElementById('email').value = selected.getAttribute('data-email');
                    // Preenche o campo de nome com o nome do estagiário
                    document.getElementById('name').value = selected.getAttribute('data-nome');
                    // Salva o CPF do estagiário em um atributo do campo de senha para uso na geração da senha
                    document.getElementById('password').setAttribute('data-cpf', selected.getAttribute('data-cpf'));
                    document.getElementById('password').setAttribute('data-nome', selected.getAttribute('data-nome'));
                    // Exibe o botão de gerar senha padrão
                    document.getElementById('btn-gerar-senha').style.display = 'inline-block';
                });

                document.addEventListener('click', function (e) {
                    if (!estagiarioSearch.contains(e.target) && !estagiarioSelect.contains(e.target)) {
                        estagiarioSelect.style.display = 'none';
                    }
                });
            }
            // Esconde o botão de gerar senha padrão ao trocar o tipo de usuário
            document.getElementById('nivel').addEventListener('change', function () {
                if (this.value !== 'estagiario') {
                    document.getElementById('btn-gerar-senha').style.display = 'none';
                    document.getElementById('name').value = '';
                }
            });
        });
        // Função para gerar senha padrão para estagiário
        function gerarSenhaPadrao() {
            var passwordField = document.getElementById('password');
            var nome = passwordField.getAttribute('data-nome') || '';
            var cpf = passwordField.getAttribute('data-cpf') || '';
            if (!nome || !cpf) {
                alert('Selecione um estagiário para gerar a senha padrão.');
                return;
            }
            // Extrai o primeiro nome e as iniciais dos sobrenomes
            var nomes = nome.trim().split(/\s+/);
            var primeiroNome = nomes[0].charAt(0).toUpperCase() + nomes[0].slice(1).toLowerCase();
            var iniciaisSobrenomes = nomes.slice(1).map(n => n.charAt(0).toLowerCase()).join('');
            // Extrai os 3 últimos dígitos do CPF antes dos dígitos verificadores
            var cpfNumeros = cpf.replace(/\D/g, '');
            var tresUltimos = cpfNumeros.length >= 5 ? cpfNumeros.substr(cpfNumeros.length - 5, 3) : '';
            var senha = primeiroNome + '.' + iniciaisSobrenomes + '@' + tresUltimos;
            passwordField.value = senha;
        }
    </script>
@endsection

<style>
    #fk_id_empresa option,
    #fk_id_estagiario option {
        white-space: normal !important;
        word-break: break-word;
        padding-top: 6px;
        padding-bottom: 6px;
    }

    .card {
        background: #f8fafc;
        border-radius: 1.5rem;
        box-shadow: 0 4px 24px 0 rgba(0, 0, 0, 0.08);
    }

    .form-group label {
        font-weight: 500;
        margin-bottom: 0.4rem;
    }

    .form-control,
    .btn {
        border-radius: 0.7rem;
    }

    .input-group-append .btn {
        border-radius: 0.7rem !important;
    }

    .form-control:focus {
        box-shadow: 0 0 0 2px #0d6efd33;
        border-color: #0d6efd;
    }

    @media (max-width: 767px) {
        .card {
            padding: 1.2rem !important;
        }

        .col-md-10,
        .col-lg-7 {
            padding: 0;
        }
    }
</style>