@php
    $orgao = $orgao ?? null;
    $cidades = $cidades ?? collect();
    $selectedEstado = old('fk_id_estado', $orgao?->cidade?->fk_id_estado);
    $selectedCidade = old('fk_id_cidade', $orgao?->fk_id_cidade);
@endphp

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

<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header">Dados do Órgão/Empresa</div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="nome_razao_social">Nome/Razão Social</label>
                        <input type="text" class="form-control @error('nome_razao_social') is-invalid @enderror"
                            id="nome_razao_social" name="nome_razao_social"
                            value="{{ old('nome_razao_social', $orgao?->nome_razao_social) }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="numero_cnpj">CNPJ</label>
                        <input type="text" class="form-control @error('numero_cnpj') is-invalid @enderror"
                            id="numero_cnpj" name="numero_cnpj"
                            value="{{ old('numero_cnpj', $orgao?->numero_cnpj) }}" maxlength="18" required>
                        <div class="invalid-feedback">Informe um CNPJ válido.</div>
                    </div>

                    <div class="form-group row mb-3">
                        <div class="col-md-6">
                            <label for="numero_telefone">Telefone</label>
                            <input type="text" class="form-control @error('numero_telefone') is-invalid @enderror"
                                id="numero_telefone" name="numero_telefone"
                                value="{{ old('numero_telefone', $orgao?->numero_telefone) }}" maxlength="15">
                        </div>
                        <div class="col-md-6">
                            <label for="numero_celular">Celular</label>
                            <input type="text" class="form-control @error('numero_celular') is-invalid @enderror"
                                id="numero_celular" name="numero_celular"
                                value="{{ old('numero_celular', $orgao?->numero_celular) }}" maxlength="15">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="email">E-mail</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email', $orgao?->email) }}" required>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header">Representante</div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="nome_representante">Nome do Representante</label>
                        <input type="text" class="form-control @error('nome_representante') is-invalid @enderror"
                            id="nome_representante" name="nome_representante"
                            value="{{ old('nome_representante', $orgao?->nome_representante) }}" required>
                    </div>

                    <div class="form-group row mb-3">
                        <div class="col-md-6">
                            <label for="cargo_representante">Cargo do Representante</label>
                            <input type="text" class="form-control @error('cargo_representante') is-invalid @enderror"
                                id="cargo_representante" name="cargo_representante"
                                value="{{ old('cargo_representante', $orgao?->cargo_representante) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="cpf_representante">CPF do Representante</label>
                            <input type="text" class="form-control @error('cpf_representante') is-invalid @enderror"
                                id="cpf_representante" name="cpf_representante"
                                value="{{ old('cpf_representante', $orgao?->cpf_representante) }}" maxlength="14" required>
                            <div class="invalid-feedback">Informe um CPF válido.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header">Endereço</div>
                <div class="card-body">
                    <div class="form-group row mb-3">
                        <div class="col-md-6">
                            <label for="numero_cep">CEP</label>
                            <input type="text" class="form-control @error('numero_cep') is-invalid @enderror"
                                id="numero_cep" name="numero_cep"
                                value="{{ old('numero_cep', $orgao?->numero_cep) }}" maxlength="9" required>
                        </div>
                        <div class="col-md-6">
                            <label for="bairro">Bairro</label>
                            <input type="text" class="form-control @error('bairro') is-invalid @enderror"
                                id="bairro" name="bairro" value="{{ old('bairro', $orgao?->bairro) }}" required>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <div class="col-md-8">
                            <label for="endereco">Endereço</label>
                            <input type="text" class="form-control @error('endereco') is-invalid @enderror"
                                id="endereco" name="endereco" value="{{ old('endereco', $orgao?->endereco) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label for="numero_endereco">Número</label>
                            <input type="text" class="form-control @error('numero_endereco') is-invalid @enderror"
                                id="numero_endereco" name="numero_endereco"
                                value="{{ old('numero_endereco', $orgao?->numero_endereco) }}" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="complemento_endereco">Complemento</label>
                        <input type="text" class="form-control @error('complemento_endereco') is-invalid @enderror"
                            id="complemento_endereco" name="complemento_endereco"
                            value="{{ old('complemento_endereco', $orgao?->complemento_endereco) }}">
                    </div>

                    <div class="form-group row mb-3">
                        <div class="col-md-6">
                            <label for="fk_id_estado">Estado</label>
                            <select class="form-control" id="fk_id_estado" name="fk_id_estado" required>
                                <option value="">Selecione um estado</option>
                                @foreach($estados as $estado)
                                    <option value="{{ $estado->id_estado }}" {{ (string) $selectedEstado === (string) $estado->id_estado ? 'selected' : '' }}>
                                        {{ $estado->nm_estado }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="fk_id_cidade">Cidade</label>
                            <select class="form-control @error('fk_id_cidade') is-invalid @enderror" id="fk_id_cidade" name="fk_id_cidade" required>
                                <option value="">Selecione uma cidade</option>
                                @foreach($cidades as $cidade)
                                    <option value="{{ $cidade->id_cidade }}" {{ (string) $selectedCidade === (string) $cidade->id_cidade ? 'selected' : '' }}>
                                        {{ $cidade->nm_cidade }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header">Dados Bancários</div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <label for="dados_bancarios">Dados bancários</label>
                        <textarea class="form-control @error('dados_bancarios') is-invalid @enderror"
                            id="dados_bancarios" name="dados_bancarios" rows="6"
                            placeholder="Nome e número do banco, agência, conta e tipo de conta.">{{ old('dados_bancarios', $orgao?->dados_bancarios) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group mt-3 text-end d-flex justify-content-end gap-2">
        <button type="button" onclick="window.NavigationHistory?.goBack('{{ route('sigeconcursos.orgaos.index') }}')"
            class="btn btn-secondary">
            Voltar
        </button>
        <button type="submit" class="btn btn-primary" id="submitButton">{{ $submitLabel }}</button>
    </div>
</form>

<script>
    function onlyDigits(value) {
        return value.replace(/\D/g, '');
    }

    function applyCnpjMask(value) {
        value = onlyDigits(value).slice(0, 14);
        value = value.replace(/^(\d{2})(\d)/, '$1.$2');
        value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
        value = value.replace(/(\d{4})(\d)/, '$1-$2');
        return value;
    }

    function applyCpfMask(value) {
        value = onlyDigits(value).slice(0, 11);
        value = value.replace(/^(\d{3})(\d)/, '$1.$2');
        value = value.replace(/^(\d{3})\.(\d{3})(\d)/, '$1.$2.$3');
        value = value.replace(/\.(\d{3})(\d)/, '.$1-$2');
        return value;
    }

    function applyCepMask(value) {
        value = onlyDigits(value).slice(0, 8);
        value = value.replace(/^(\d{5})(\d)/, '$1-$2');
        return value;
    }

    function applyPhoneMask(value) {
        value = onlyDigits(value).slice(0, 11);

        if (value.length > 10) {
            value = value.replace(/^(\d{2})(\d{5})(\d)/, '($1) $2-$3');
        } else {
            value = value.replace(/^(\d{2})(\d{4})(\d)/, '($1) $2-$3');
        }

        return value;
    }

    function validarCPF(cpf) {
        cpf = onlyDigits(cpf);

        if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) {
            return false;
        }

        let soma = 0;
        for (let i = 0; i < 9; i++) {
            soma += parseInt(cpf.charAt(i), 10) * (10 - i);
        }

        let resto = 11 - (soma % 11);
        resto = resto >= 10 ? 0 : resto;
        if (resto !== parseInt(cpf.charAt(9), 10)) {
            return false;
        }

        soma = 0;
        for (let i = 0; i < 10; i++) {
            soma += parseInt(cpf.charAt(i), 10) * (11 - i);
        }

        resto = 11 - (soma % 11);
        resto = resto >= 10 ? 0 : resto;
        return resto === parseInt(cpf.charAt(10), 10);
    }

    function validarCNPJ(cnpj) {
        cnpj = onlyDigits(cnpj);

        if (cnpj.length !== 14 || /^(\d)\1+$/.test(cnpj)) {
            return false;
        }

        let tamanho = cnpj.length - 2;
        let numeros = cnpj.substring(0, tamanho);
        const digitos = cnpj.substring(tamanho);
        let soma = 0;
        let pos = tamanho - 7;

        for (let i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) {
                pos = 9;
            }
        }

        let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        if (resultado !== parseInt(digitos.charAt(0), 10)) {
            return false;
        }

        tamanho += 1;
        numeros = cnpj.substring(0, tamanho);
        soma = 0;
        pos = tamanho - 7;

        for (let i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2) {
                pos = 9;
            }
        }

        resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
        return resultado === parseInt(digitos.charAt(1), 10);
    }

    function bindMask(inputId, formatter) {
        const input = document.getElementById(inputId);
        if (!input) {
            return;
        }

        input.value = formatter(input.value || '');
        input.addEventListener('input', function () {
            this.value = formatter(this.value);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindMask('numero_cnpj', applyCnpjMask);
        bindMask('cpf_representante', applyCpfMask);
        bindMask('numero_cep', applyCepMask);
        bindMask('numero_telefone', applyPhoneMask);
        bindMask('numero_celular', applyPhoneMask);

        const cnpjInput = document.getElementById('numero_cnpj');
        const cpfInput = document.getElementById('cpf_representante');

        if (cnpjInput) {
            cnpjInput.addEventListener('blur', function () {
                this.classList.toggle('is-invalid', this.value.length > 0 && !validarCNPJ(this.value));
            });
        }

        if (cpfInput) {
            cpfInput.addEventListener('blur', function () {
                this.classList.toggle('is-invalid', this.value.length > 0 && !validarCPF(this.value));
            });
        }

        const estadoSelect = document.getElementById('fk_id_estado');
        const cidadesSelect = document.getElementById('fk_id_cidade');
        const cidadeSelecionada = '{{ $selectedCidade }}';

        function carregarCidades(estadoId, cidadeId = '') {
            cidadesSelect.innerHTML = '<option value="">Selecione uma cidade</option>';

            if (!estadoId) {
                return;
            }

            fetch(`/estados/${estadoId}/cidades`)
                .then(response => response.json())
                .then(cidades => {
                    cidades.forEach(cidade => {
                        const option = document.createElement('option');
                        option.value = cidade.id_cidade;
                        option.text = cidade.nm_cidade;
                        option.selected = String(cidade.id_cidade) === String(cidadeId);
                        cidadesSelect.appendChild(option);
                    });
                });
        }

        estadoSelect.addEventListener('change', function () {
            carregarCidades(this.value, '');
        });

        if (estadoSelect.value) {
            carregarCidades(estadoSelect.value, cidadeSelecionada);
        }
    });
</script>