@extends('layouts.main')

@section('title', 'Cadastrar Unidade Concedente')

@section('content')
    <h1>Adicionar Unidade Concedente</h1>
    <button onclick="window.NavigationHistory?.goBack('{{ route('empresas.index') }}')" class="btn btn-secondary mb-3"
        title="Voltar para a página anterior com filtros preservados">Voltar</button>
    <form action="{{ route('empresas.store') }}" method="POST">
        @csrf
        @method('POST')
        <div class="row">
            <!-- Informações da Unidade Concedente -->
            <div class="col-md-6">
                <div class="form-group mb-2">
                    <label for="nome_empresa">Nome</label>
                    <input type="text" class="form-control" id="nome_empresa" name="nome_empresa" required>
                </div>
                <div class="form-group mb-2">
                    <label for="numero_cnpj">CNPJ</label>
                    <input type="text" class="form-control" id="numero_cnpj" name="numero_cnpj" required>
                    <div class="invalid-feedback" id="cnpjError" style="display: none;">CNPJ inválido.</div>
                </div>
                <div class="form-group row mb-2">
                    <div class="col-md-6">
                        <label for="numero_telefone">Telefone</label>
                        <input type="text" class="form-control" id="numero_telefone" name="numero_telefone" required>
                    </div>
                    <div class="col-md-6">
                        <label for="numero_celular">Celular</label>
                        <input type="text" class="form-control" id="numero_celular" name="numero_celular" required>
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email">
                    <div class="invalid-feedback" id="emailError" style="display: none;">Por favor, insira um e-mail válido.
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <div class="col-md-6">
                        <label for="tipo_taxa">Tipo de Taxa</label>
                        <select name="tipo_taxa" id="tipo_taxa" class="form-control" required>
                            <option value="">Selecione o tipo de taxa</option>
                            <option value="fixa" {{ old('tipo_taxa') == 'fixa' ? 'selected' : '' }}>Taxa Fixa (R$)</option>
                            <option value="percentual" {{ old('tipo_taxa') == 'percentual' ? 'selected' : '' }}>Taxa
                                Percentual (%)</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="campo_valor_taxa" style="display: none;">
                        <label for="valor_taxa" id="label_valor_taxa">Valor da Taxa</label>
                        <input type="number" step="0.01" name="valor_taxa" id="valor_taxa" class="form-control"
                            value="{{ old('valor_taxa') }}" placeholder="Informe o valor">
                    </div>
                </div>
            </div>

            <!-- Endereço -->
            <div class="col-md-6">
                <div class="form-group row mb-2">
                    <div class="col-md-6">
                        <label for="numero_cep">CEP</label>
                        <input type="text" class="form-control" id="numero_cep" name="numero_cep" required>
                    </div>
                    <div class="col-md-6">
                        <label for="bairro">Bairro</label>
                        <input type="text" class="form-control" id="bairro" name="bairro">
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <div class="col-md-8">
                        <label for="endereco">Endereço</label>
                        <input type="text" class="form-control" id="endereco" name="endereco">
                    </div>
                    <div class="col-md-4">
                        <label for="numero_endereco">Número</label>
                        <input type="text" class="form-control" id="numero_endereco" name="numero_endereco" required>
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label for="complemento_endereco">Complemento</label>
                    <input type="text" class="form-control" id="complemento_endereco" name="complemento_endereco">
                </div>
                <div class="form-group row mb-2">
                    <div class="col-md-6">
                        <label for="fk_id_estado">Estado</label>
                        <select class="form-control" id="fk_id_estado" name="fk_id_estado" required>
                            <option value="">Selecione um estado</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->id_estado }}">{{ $estado->nm_estado }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="fk_id_cidade">Cidade</label>
                        <select class="form-control" id="fk_id_cidade" name="fk_id_cidade" required>
                            <option value="">Selecione uma cidade</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Linha sutil separadora -->
        <hr class="my-4" style="opacity: 0.3;">

        <!-- Representante -->
        <div class="row mt-3">
            <div class="col-md-6">
                <div class="form-group mb-2">
                    <label for="nome_representante">Nome do Representante</label>
                    <input type="text" class="form-control" id="nome_representante" name="nome_representante">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-2">
                    <label for="cargo_representante">Cargo</label>
                    <input type="text" class="form-control" id="cargo_representante" name="cargo_representante">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-2">
                    <label for="cpf_representante">CPF</label>
                    <input type="text" class="form-control" id="cpf_representante" name="cpf_representante" required>
                    <div class="invalid-feedback" id="cpfError" style="display: none;">CPF inválido.</div>
                </div>
            </div>
        </div>

        <div class="form-group mt-3 text-end">
            <button type="submit" class="btn btn-primary" id="submitButton">Salvar</button>
        </div>

        <script>
            document.getElementById('aceitacao_termos')?.addEventListener('change', function () {
                const submitButton = document.getElementById('submitButton');
                submitButton.disabled = !this.checked;
            });

            document.getElementById('fk_id_estado').addEventListener('change', function () {
                const estadoId = this.value;
                const cidadesSelect = document.getElementById('fk_id_cidade');

                cidadesSelect.innerHTML = '<option value="">Selecione uma cidade</option>';

                if (estadoId) {
                    fetch(`/estados/${estadoId}/cidades`)
                        .then(response => response.json())
                        .then(cidades => {
                            cidades.forEach(cidade => {
                                const option = document.createElement('option');
                                option.value = cidade.id_cidade;
                                option.text = cidade.nm_cidade;
                                cidadesSelect.appendChild(option);
                            });
                        });
                }
            });

            const tipoTaxaSelect = document.getElementById('tipo_taxa');
            const campoValor = document.getElementById('campo_valor_taxa');
            const labelValor = document.getElementById('label_valor_taxa');

            function atualizarCampoTaxa() {
                const tipoSelecionado = tipoTaxaSelect.value;

                if (tipoSelecionado === 'fixa') {
                    campoValor.style.display = 'block';
                    labelValor.textContent = 'Taxa Fixa (R$)';
                } else if (tipoSelecionado === 'percentual') {
                    campoValor.style.display = 'block';
                    labelValor.textContent = 'Taxa Percentual (%)';
                } else {
                    campoValor.style.display = 'none';
                }
            }

            tipoTaxaSelect.addEventListener('change', atualizarCampoTaxa);
            window.addEventListener('DOMContentLoaded', atualizarCampoTaxa);
            atualizarCampoTaxa();

            function validarCNPJ(cnpj) {
                cnpj = cnpj.replace(/[^\d]+/g, '');
                if (cnpj.length !== 14) return false;
                if (/^(\d)\1+$/.test(cnpj)) return false;

                let tamanho = cnpj.length - 2;
                let numeros = cnpj.substring(0, tamanho);
                let digitos = cnpj.substring(tamanho);
                let soma = 0;
                let pos = tamanho - 7;
                for (let i = tamanho; i >= 1; i--) {
                    soma += numeros.charAt(tamanho - i) * pos--;
                    if (pos < 2) pos = 9;
                }
                let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
                if (resultado != digitos.charAt(0)) return false;

                tamanho = tamanho + 1;
                numeros = cnpj.substring(0, tamanho);
                soma = 0;
                pos = tamanho - 7;
                for (let i = tamanho; i >= 1; i--) {
                    soma += numeros.charAt(tamanho - i) * pos--;
                    if (pos < 2) pos = 9;
                }
                resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
                if (resultado != digitos.charAt(1)) return false;

                return true;
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
                const cnpjValido = validarCNPJ(document.getElementById('numero_cnpj').value);
                const cpfValido = validarCPF(document.getElementById('cpf_representante').value);
                document.getElementById('submitButton').disabled = !(cnpjValido && cpfValido);
            }

            document.getElementById('numero_cnpj').addEventListener('input', function () {
                const cnpjValido = validarCNPJ(this.value);
                const cnpjError = document.getElementById('cnpjError');
                if (!cnpjValido && this.value.length > 0) {
                    this.classList.add('is-invalid');
                    cnpjError.style.display = 'block';
                } else {
                    this.classList.remove('is-invalid');
                    cnpjError.style.display = 'none';
                }
                checarCamposValidos();
            });

            document.getElementById('cpf_representante').addEventListener('input', function () {
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

            // Desabilita o botão ao carregar a página
            window.addEventListener('DOMContentLoaded', function () {
                checarCamposValidos();
            });
        </script>
    </form>
@endsection