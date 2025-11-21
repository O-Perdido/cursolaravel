@extends('layouts.main')

@section('title', 'Cadastrar Supervisor')

@section('content')
    <h1>Adicionar Supervisor</h1>
    <a href="{{ route('supervisores.index') }}" class="btn btn-secondary mb-3">Voltar</a>
    <form action="{{ route('supervisor.store') }}" method="POST">
        @csrf
        <div class="row">
            <!-- Dados do Supervisor -->
            <div class="col-md-6">
                <div class="form-group mb-2">
                    <label for="nome_supervisor">Nome</label>
                    <input type="text" class="form-control" id="nome_supervisor" name="nome_supervisor" required>
                </div>
                <div class="form-group mb-2">
                    <label for="fk_id_empresa">Empresa</label>
                    <div style="position: relative;">
                        <input type="text" class="form-control" id="empresa_search" placeholder="Digite para buscar..."
                            autocomplete="off">
                        <select class="form-control mt-2" id="fk_id_empresa" name="fk_id_empresa" size="5" required
                            style="display:none; position: absolute; top: 42px; left: 0; width: 100%; z-index: 1050; background: #fff; border: 1px solid #ced4da;">
                            <option value="">Selecione a empresa</option>
                            @foreach($empresas->sortBy('nome_empresa') as $empresa)
                                <option value="{{ $empresa->id_empresa }}">{{ $empresa->nome_empresa }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label for="cpf_supervisor">CPF</label>
                    <input type="text" class="form-control" id="cpf_supervisor" name="cpf_supervisor" required>
                    <div class="invalid-feedback" id="cpfError" style="display: none;">CPF inválido.</div>
                </div>
            </div>
            <!-- Formação e Experiência -->
            <div class="col-md-6">
                <div class="form-group mb-2">
                    <label for="area_formacao">Área de Formação</label>
                    <input type="text" class="form-control" id="area_formacao" name="area_formacao">
                </div>
                <div class="form-group mb-2">
                    <label for="tempo_experiencia">Tempo de Experiência</label>
                    <input type="text" class="form-control" id="tempo_experiencia" name="tempo_experiencia">
                </div>
            </div>
        </div>

        <div class="form-group mt-3 text-end">
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

                function validarCPF(cpf) {
                    cpf = cpf.replace(/[^\d]+/g, '');
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

                function checarCamposValidos() {
                    const cpfValido = validarCPF(document.getElementById('cpf_supervisor').value);
                    document.getElementById('save_button').disabled = !cpfValido;
                }

                document.getElementById('cpf_supervisor').addEventListener('input', function () {
                    const cpfValido = validarCPF(this.value);
                    const cpfError = document.getElementById('cpfError');
                    if (!cpfValido && this.value.length > 0) {
                        this.classList.add('is-invalid');
                        cpfError.style.display = 'block';
                    } else {
                        this.classList.remove('is-invalid');
                        cpfError.style.display = 'none';
                    }
                    checarCamposValidos();
                });

                document.addEventListener('DOMContentLoaded', function () {
                    setupFilter('empresa_search', 'fk_id_empresa');
                    checarCamposValidos();
                });
            </script>
            <button type="submit" class="btn btn-primary" id="save_button">Salvar</button>
        </div>
    </form>
@endsection