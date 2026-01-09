@extends('layouts.main')

@section('title', 'Editar Unidade Concedente')

@section('content')
    <h1>Editar Unidade Concedente</h1>
    <button onclick="window.NavigationHistory?.goBack('{{ route('empresas.index') }}')" class="btn btn-secondary mb-3"
        title="Voltar para a página anterior com filtros preservados">Voltar</button>
    <form action="{{ route('empresas.update', $empresa->id_empresa) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <!-- Informações da Unidade Concedente -->
            <div class="col-md-6">
                <div class="form-group mb-2">
                    <label for="nome_empresa">Nome</label>
                    <input type="text" class="form-control" id="nome_empresa" name="nome_empresa"
                        value="{{ $empresa->nome_empresa }}" required>
                </div>
                <div class="form-group mb-2">
                    <label for="numero_cnpj">CNPJ</label>
                    <input type="text" class="form-control" id="numero_cnpj" name="numero_cnpj"
                        value="{{ $empresa->numero_cnpj }}" required>
                </div>
                <div class="form-group row mb-2">
                    <div class="col-md-6">
                        <label for="numero_telefone">Telefone</label>
                        <input type="text" class="form-control" id="numero_telefone" name="numero_telefone"
                            value="{{ $empresa->numero_telefone }}">
                    </div>
                    <div class="col-md-6">
                        <label for="numero_celular">Celular</label>
                        <input type="text" class="form-control" id="numero_celular" name="numero_celular"
                            value="{{ $empresa->numero_celular }}">
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $empresa->email }}">
                </div>
                <div class="form-group row mb-2">
                    <div class="col-md-6">
                        <label for="tipo_taxa">Tipo de Taxa</label>
                        <select name="tipo_taxa" id="tipo_taxa" class="form-control" required>
                            <option value="">Selecione o tipo de taxa</option>
                            <option value="fixa" {{ old('tipo_taxa', $empresa->tipo_taxa) == 'fixa' ? 'selected' : '' }}>Taxa
                                Fixa (R$)</option>
                            <option value="percentual" {{ old('tipo_taxa', $empresa->tipo_taxa) == 'percentual' ? 'selected' : '' }}>Taxa Percentual (%)</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="campo_valor_taxa" style="display: none;">
                        <label for="valor_taxa" id="label_valor_taxa">Valor da Taxa</label>
                        <input type="number" step="0.01" name="valor_taxa" id="valor_taxa" class="form-control"
                            value="{{ old('valor_taxa', $empresa->tipo_taxa == 'fixa' ? $empresa->taxa_fixa : $empresa->taxa_percentual) }}"
                            placeholder="Informe o valor">
                    </div>
                </div>
            </div>

            <!-- Endereço -->
            <div class="col-md-6">
                <div class="form-group row mb-2">
                    <div class="col-md-6">
                        <label for="numero_cep">CEP</label>
                        <input type="text" class="form-control" id="numero_cep" name="numero_cep"
                            value="{{ $empresa->numero_cep }}">
                    </div>
                    <div class="col-md-6">
                        <label for="bairro">Bairro</label>
                        <input type="text" class="form-control" id="bairro" name="bairro" value="{{ $empresa->bairro }}">
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <div class="col-md-8">
                        <label for="endereco">Endereço</label>
                        <input type="text" class="form-control" id="endereco" name="endereco"
                            value="{{ $empresa->endereco }}">
                    </div>
                    <div class="col-md-4">
                        <label for="numero_endereco">Número</label>
                        <input type="text" class="form-control" id="numero_endereco" name="numero_endereco"
                            value="{{ $empresa->numero_endereco }}">
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label for="complemento_endereco">Complemento</label>
                    <input type="text" class="form-control" id="complemento_endereco" name="complemento_endereco"
                        value="{{ $empresa->complemento_endereco }}">
                </div>
                <div class="form-group row mb-2">
                    <div class="col-md-6">
                        <label for="fk_id_estado">Estado</label>
                        <select class="form-control" id="fk_id_estado" name="fk_id_estado" required>
                            <option value="">Selecione um estado</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->id_estado }}" {{ $estado->id_estado == $empresa->cidade->fk_id_estado ? 'selected' : '' }}>
                                    {{ $estado->nm_estado }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="fk_id_cidade">Cidade</label>
                        <select class="form-control" id="fk_id_cidade" name="fk_id_cidade" required>
                            <option value="">Selecione uma cidade</option>
                            @foreach($cidades as $cidade)
                                <option value="{{ $cidade->id_cidade }}" {{ $cidade->id_cidade == $empresa->fk_id_cidade ? 'selected' : '' }}>
                                    {{ $cidade->nm_cidade }}
                                </option>
                            @endforeach
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
                    <input type="text" class="form-control" id="nome_representante" name="nome_representante"
                        value="{{ $empresa->nome_representante }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-2">
                    <label for="cargo_representante">Cargo</label>
                    <input type="text" class="form-control" id="cargo_representante" name="cargo_representante"
                        value="{{ $empresa->cargo_representante }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-2">
                    <label for="cpf_representante">CPF</label>
                    <input type="text" class="form-control" id="cpf_representante" name="cpf_representante"
                        value="{{ $empresa->cpf_representante }}">
                </div>
            </div>
        </div>

        <div class="form-group mt-3 text-end">
            <button type="submit" class="btn btn-primary">Salvar</button>
        </div>
    </form>

    <script>
        // Aguarda o carregamento completo da página
        document.addEventListener('DOMContentLoaded', function () {
            const estadoSelect = document.getElementById('fk_id_estado');
            const cidadesSelect = document.getElementById('fk_id_cidade');
            const cidadeOriginal = cidadesSelect.value; // Salva a cidade original

            estadoSelect.addEventListener('change', function () {
                const estadoId = this.value;
                const cidadeAtualId = cidadesSelect.value; // Salva a cidade atualmente selecionada
                cidadesSelect.innerHTML = '<option value="">Selecione uma cidade</option>';

                if (estadoId) {
                    fetch(`/estados/${estadoId}/cidades`)
                        .then(response => response.json())
                        .then(cidades => {
                            cidades.forEach(cidade => {
                                const option = document.createElement('option');
                                option.value = cidade.id_cidade;
                                option.text = cidade.nm_cidade;
                                // Reseleciona a cidade que estava selecionada
                                if (cidade.id_cidade == cidadeAtualId) {
                                    option.selected = true;
                                }
                                cidadesSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Erro ao carregar cidades:', error);
                        });
                }
            });

            // Trigger the change event if an estado is pre-selected
            if (estadoSelect.value) {
                estadoSelect.dispatchEvent(new Event('change'));
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
        atualizarCampoTaxa();
    </script>
@endsection