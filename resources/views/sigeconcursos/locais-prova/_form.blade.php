@php
    $local = $local ?? null;
    $cidades = $cidades ?? collect();
    $selectedEstado = old('fk_id_estado', $local?->cidade?->fk_id_estado);
    $selectedCidade = old('fk_id_cidade', $local?->fk_id_cidade);
    $salasData = old('salas', $local?->salas?->map(function ($sala) {
        return [
            'nome_sala' => $sala->nome_sala,
            'bloco' => $sala->bloco,
            'capacidade_maxima' => $sala->capacidade_maxima,
            'observacoes' => $sala->observacoes,
        ];
    })->all() ?? [['nome_sala' => '', 'bloco' => '', 'capacidade_maxima' => '', 'observacoes' => '']]);
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
                <div class="card-header">Dados do Local</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nome_local" class="form-label">Nome do Local</label>
                        <input type="text" class="form-control @error('nome_local') is-invalid @enderror" id="nome_local"
                            name="nome_local" value="{{ old('nome_local', $local?->nome_local) }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="numero_cep" class="form-label">CEP</label>
                            <input type="text" class="form-control @error('numero_cep') is-invalid @enderror" id="numero_cep"
                                name="numero_cep" value="{{ old('numero_cep', $local?->numero_cep) }}" maxlength="9" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input type="text" class="form-control @error('bairro') is-invalid @enderror" id="bairro"
                                name="bairro" value="{{ old('bairro', $local?->bairro) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="endereco" class="form-label">Endereço</label>
                            <input type="text" class="form-control @error('endereco') is-invalid @enderror" id="endereco"
                                name="endereco" value="{{ old('endereco', $local?->endereco) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="numero_endereco" class="form-label">Número</label>
                            <input type="text" class="form-control @error('numero_endereco') is-invalid @enderror"
                                id="numero_endereco" name="numero_endereco"
                                value="{{ old('numero_endereco', $local?->numero_endereco) }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="complemento_endereco" class="form-label">Complemento</label>
                        <input type="text" class="form-control @error('complemento_endereco') is-invalid @enderror"
                            id="complemento_endereco" name="complemento_endereco"
                            value="{{ old('complemento_endereco', $local?->complemento_endereco) }}">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="fk_id_estado" class="form-label">Estado</label>
                            <select class="form-control" id="fk_id_estado" name="fk_id_estado" required>
                                <option value="">Selecione um estado</option>
                                @foreach($estados as $estado)
                                    <option value="{{ $estado->id_estado }}" {{ (string) $selectedEstado === (string) $estado->id_estado ? 'selected' : '' }}>
                                        {{ $estado->nm_estado }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fk_id_cidade" class="form-label">Cidade</label>
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

                    <div class="mb-0">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control @error('observacoes') is-invalid @enderror" id="observacoes"
                            name="observacoes" rows="4">{{ old('observacoes', $local?->observacoes) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header">Situação</div>
                <div class="card-body">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="1"
                            {{ old('ativo', $local?->ativo ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="ativo">Local ativo</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Salas</span>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="adicionar-sala">
                        <i class="fas fa-plus me-1"></i> Adicionar Sala
                    </button>
                </div>
                <div class="card-body">
                    <div id="salas-container"></div>
                    <small class="text-muted">Cadastre as salas já vinculadas a este local de prova.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('sigeconcursos.locais-prova.index') }}" class="btn btn-outline-secondary">Voltar</a>
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const estadoSelect = document.getElementById('fk_id_estado');
        const cidadesSelect = document.getElementById('fk_id_cidade');
        const cidadeSelecionada = @json((string) $selectedCidade);
        const salasContainer = document.getElementById('salas-container');
        const adicionarSala = document.getElementById('adicionar-sala');
        const salasData = @json($salasData);
        const cepInput = document.getElementById('numero_cep');

        function onlyDigits(value) {
            return (value || '').replace(/\D/g, '');
        }

        function applyCepMask(value) {
            value = onlyDigits(value).slice(0, 8);
            value = value.replace(/^(\d{5})(\d)/, '$1-$2');
            return value;
        }

        if (cepInput) {
            cepInput.value = applyCepMask(cepInput.value || '');
            cepInput.addEventListener('input', function () {
                this.value = applyCepMask(this.value);
            });
        }

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

        function renderSalaRow(data = { nome_sala: '', bloco: '', capacidade_maxima: '', observacoes: '' }) {
            const index = salasContainer.querySelectorAll('.sala-item').length;
            const wrapper = document.createElement('div');
            wrapper.className = 'sala-item border rounded p-3 mb-3 bg-light';
            wrapper.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Sala</h6>
                    <button type="button" class="btn btn-outline-danger btn-sm remover-sala">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label small">Nome da Sala</label>
                        <input type="text" class="form-control form-control-sm" name="salas[${index}][nome_sala]" value="${data.nome_sala ?? ''}">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label small">Bloco</label>
                        <input type="text" class="form-control form-control-sm" name="salas[${index}][bloco]" value="${data.bloco ?? ''}">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label small">Capacidade Máxima</label>
                        <input type="number" min="1" class="form-control form-control-sm" name="salas[${index}][capacidade_maxima]" value="${data.capacidade_maxima ?? ''}">
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label small">Observações</label>
                        <input type="text" class="form-control form-control-sm" name="salas[${index}][observacoes]" value="${data.observacoes ?? ''}">
                    </div>
                </div>
            `;

            wrapper.querySelector('.remover-sala').addEventListener('click', function () {
                wrapper.remove();
                reindexSalas();
            });

            salasContainer.appendChild(wrapper);
        }

        function reindexSalas() {
            salasContainer.querySelectorAll('.sala-item').forEach((item, index) => {
                item.querySelectorAll('input').forEach(input => {
                    input.name = input.name.replace(/salas\[\d+\]/, `salas[${index}]`);
                });
            });
        }

        (salasData && salasData.length ? salasData : [{ nome_sala: '', bloco: '', capacidade_maxima: '', observacoes: '' }])
            .forEach(renderSalaRow);

        adicionarSala.addEventListener('click', function () {
            renderSalaRow();
        });

        estadoSelect.addEventListener('change', function () {
            carregarCidades(this.value, '');
        });

        if (estadoSelect.value) {
            carregarCidades(estadoSelect.value, cidadeSelecionada);
        }
    });
</script>