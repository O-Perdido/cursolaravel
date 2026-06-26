@extends('layouts.main')

@section('title', 'Editar Estagiário')

@section('content')
    @php
        $possuiNomeSocial = old('possui_nome_social') !== null
            ? (bool) old('possui_nome_social')
            : !empty($estagiario->nome_secundario);

        $dataNascimentoValor = old('data_nascimento');
        if (empty($dataNascimentoValor) && isset($estagiario) && !empty($estagiario->data_nascimento)) {
            if (strpos($estagiario->data_nascimento, '/') !== false) {
                try {
                    $dataNascimentoValor = \Carbon\Carbon::createFromFormat('d/m/Y', $estagiario->data_nascimento)->format('Y-m-d');
                } catch (\Throwable $e) {
                    $dataNascimentoValor = null;
                }
            } else {
                try {
                    $dataNascimentoValor = \Carbon\Carbon::parse($estagiario->data_nascimento)->format('Y-m-d');
                } catch (\Throwable $e) {
                    $dataNascimentoValor = null;
                }
            }
        }
        $dataNascimentoObj = null;

        if (!empty($dataNascimentoValor)) {
            try {
                $dataNascimentoObj = \Carbon\Carbon::parse($dataNascimentoValor);
            } catch (\Throwable $exception) {
                $dataNascimentoObj = null;
            }
        }

        $diaSelecionado = $dataNascimentoObj ? (int) $dataNascimentoObj->day : null;
        $mesSelecionado = $dataNascimentoObj ? (int) $dataNascimentoObj->month : null;
        $anoSelecionado = $dataNascimentoObj ? (int) $dataNascimentoObj->year : null;
        
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

    <h1>Editar Estagiário</h1>

    @if(session('success'))
        <div style="color: green;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="color: red;">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('estagiario.update', $estagiario->id_estagiario) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div style="color: red;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <!-- Dados Pessoais -->
            <div class="col-md-6">
                <div class="form-group mb-2">
                    <label for="nome_estagiario" id="label_nome_estagiario">{{ $possuiNomeSocial ? 'Nome Social' : 'Nome do Estagiário' }}</label>
                    <input type="text" id="nome_estagiario" name="nome_estagiario" class="form-control"
                        value="{{ old('nome_estagiario', $estagiario->nome_estagiario) }}">
                </div>
                <div class="form-check mb-2">
                    <input type="checkbox" class="form-check-input" id="possui_nome_social" name="possui_nome_social" value="1"
                        {{ $possuiNomeSocial ? 'checked' : '' }}>
                    <label class="form-check-label" for="possui_nome_social">Possui nome social?</label>
                </div>
                <div class="form-group mb-2" id="grupo_nome_secundario" style="display: {{ $possuiNomeSocial ? 'block' : 'none' }};">
                    <label for="nome_secundario">Nome Civil</label>
                    <input type="text" id="nome_secundario" name="nome_secundario" class="form-control"
                        value="{{ old('nome_secundario', $estagiario->nome_secundario) }}">
                </div>
                <div class="form-group mb-2">
                    <label for="numero_cpf">CPF</label>
                    <input type="text" id="numero_cpf" name="numero_cpf"
                        class="form-control @error('numero_cpf') is-invalid @enderror"
                        value="{{ old('numero_cpf', $estagiario->numero_cpf) }}" required>
                    @error('numero_cpf')
                        <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group mb-2">
                    <label class="form-label">Data de Nascimento <span class="text-danger">*</span></label>
                    <input type="hidden" id="data_nascimento" name="data_nascimento" value="{{ $dataNascimentoValor }}" required>
                    <div class="row g-1">
                        <div class="col-4" style="padding-right: 2px;">
                            <select class="form-control" id="data_nascimento_dia" required>
                                <option value="">Dia</option>
                                @for ($dia = 1; $dia <= 31; $dia++)
                                    <option value="{{ $dia }}" {{ $diaSelecionado === $dia ? 'selected' : '' }}>
                                        {{ str_pad((string) $dia, 2, '0', STR_PAD_LEFT) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-4" style="padding-left: 2px; padding-right: 2px;">
                            <select class="form-control" id="data_nascimento_mes" required>
                                <option value="">Mês</option>
                                @foreach ($meses as $numeroMes => $nomeMes)
                                    <option value="{{ $numeroMes }}" {{ $mesSelecionado === $numeroMes ? 'selected' : '' }}>
                                        {{ $nomeMes }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-4" style="padding-left: 2px;">
                            <select class="form-control" id="data_nascimento_ano" required>
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
                <script>
                    (function() {
                        const diaSel = document.getElementById('data_nascimento_dia');
                        const mesSel = document.getElementById('data_nascimento_mes');
                        const anoSel = document.getElementById('data_nascimento_ano');
                        const hiddenInput = document.getElementById('data_nascimento');

                        function updateHiddenInput() {
                            const dia = diaSel.value;
                            const mes = mesSel.value;
                            const ano = anoSel.value;

                            if (dia && mes && ano) {
                                const padDia = dia.toString().padStart(2, '0');
                                const padMes = mes.toString().padStart(2, '0');
                                hiddenInput.value = `${ano}-${padMes}-${padDia}`;
                            } else {
                                hiddenInput.value = '';
                            }
                            hiddenInput.dispatchEvent(new Event('change'));
                        }

                        diaSel.addEventListener('change', updateHiddenInput);
                        mesSel.addEventListener('change', updateHiddenInput);
                        anoSel.addEventListener('change', updateHiddenInput);
                    })();
                </script>
                <div class="form-group row mb-2">
                    <div class="col-md-6">
                        <label for="numero_telefone">Telefone</label>
                        <input type="text" id="numero_telefone" name="numero_telefone" class="form-control"
                            value="{{ old('numero_telefone', $estagiario->numero_telefone) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="numero_celular">Celular</label>
                        <input type="text" id="numero_celular" name="numero_celular" class="form-control"
                            value="{{ old('numero_celular', $estagiario->numero_celular) }}">
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                        value="{{ old('email', $estagiario->email) }}">
                </div>
                <div class="form-group row mb-2">
                    <div class="col-md-6">
                        <label for="numero_cep">CEP</label>
                        <input type="text" id="numero_cep" name="numero_cep" class="form-control"
                            value="{{ old('numero_cep', $estagiario->numero_cep) }}">
                    </div>
                    <div class="col-md-6">
                        <label for="bairro">Bairro</label>
                        <input type="text" id="bairro" name="bairro" class="form-control"
                            value="{{ old('bairro', $estagiario->bairro) }}">
                    </div>
                </div>
                <div class="form-group row mb-2">
                    <div class="col-md-8">
                        <label for="endereco">Endereço</label>
                        <input type="text" id="endereco" name="endereco" class="form-control"
                            value="{{ old('endereco', $estagiario->endereco) }}">
                    </div>
                    <div class="col-md-4">
                        <label for="numero_endereco">Número</label>
                        <input type="text" id="numero_endereco" name="numero_endereco" class="form-control"
                            value="{{ old('numero_endereco', $estagiario->numero_endereco) }}">
                    </div>
                </div>
                <div class="form-group mb-2">
                    <label for="complemento_endereco">Complemento</label>
                    <input type="text" id="complemento_endereco" name="complemento_endereco" class="form-control"
                        value="{{ old('complemento_endereco', $estagiario->complemento_endereco) }}">
                </div>
                <div class="form-group row mb-2">
                    <div class="col-md-6">
                        <label for="fk_id_estado">Estado</label>
                        <select id="fk_id_estado" name="fk_id_estado" class="form-control" required>
                            <option value="">Selecione um estado</option>
                            @foreach($estados as $estado)
                                <option value="{{ $estado->id_estado }}" {{ old('fk_id_estado', $estagiario->cidade->fk_id_estado) == $estado->id_estado ? 'selected' : '' }}>
                                    {{ $estado->nm_estado }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="fk_id_cidade">Cidade</label>
                        <select id="fk_id_cidade" name="fk_id_cidade" class="form-control" required>
                            <option value="">Selecione uma cidade</option>
                            @foreach($cidades as $cidade)
                                <option value="{{ $cidade->id_cidade }}" {{ old('fk_id_cidade', $estagiario->fk_id_cidade) == $cidade->id_cidade ? 'selected' : '' }}>
                                    {{ $cidade->nm_cidade }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Dados Acadêmicos -->
            <div class="col-md-6">
                <div class="form-group mb-2">
                    <label for="instituicao_ensino">Instituição de Ensino</label>
                    <input type="text" class="form-control" id="instituicao_ensino" name="instituicao_ensino"
                        value="{{ old('instituicao_ensino', $estagiario->instituicao_ensino ?? '') }}">
                </div>
                <div class="form-group mb-2">
                    <label for="nivel_curso">Nível do Curso</label>
                    <select class="form-control" id="nivel_curso" name="nivel_curso" required>
                        <option value="">Escolha um nível</option>
                        <option value="Ensino Médio" {{ (old('nivel_curso', $estagiario->nivel_curso ?? '') == 'Ensino Médio' || old('nivel_curso', $estagiario->nivel_curso ?? '') == 'Ensino Medio') ? 'selected' : '' }}>Ensino Médio</option>
                        <option value="Técnico" {{ (old('nivel_curso', $estagiario->nivel_curso ?? '') == 'Técnico' || old('nivel_curso', $estagiario->nivel_curso ?? '') == 'Tecnico') ? 'selected' : '' }}>Técnico</option>
                        <option value="Graduação" {{ (old('nivel_curso', $estagiario->nivel_curso ?? '') == 'Graduação' || old('nivel_curso', $estagiario->nivel_curso ?? '') == 'Graduacao') ? 'selected' : '' }}>Graduação</option>
                        <option value="Pós Graduação" {{ (old('nivel_curso', $estagiario->nivel_curso ?? '') == 'Pós Graduação' || old('nivel_curso', $estagiario->nivel_curso ?? '') == 'Pós' || old('nivel_curso', $estagiario->nivel_curso ?? '') == 'Pos') ? 'selected' : '' }}>Pós Graduação</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label for="curso">Curso</label>
                    <input type="text" id="curso" name="curso" class="form-control" required
                        value="{{ old('curso', $estagiario->curso) }}">
                </div>
                <div class="form-group mb-2">
                    <label for="nome_mae">Nome da Mãe</label>
                    <input type="text" id="nome_mae" name="nome_mae" class="form-control"
                        value="{{ old('nome_mae', $estagiario->nome_mae) }}">
                </div>
                <div class="form-group mb-2">
                    <label for="numero_pis">Número PIS</label>
                    <input type="text" id="numero_pis" name="numero_pis" class="form-control"
                        value="{{ old('numero_pis', $estagiario->numero_pis) }}">
                </div>
                <div class="form-group mb-2">
                    <label for="chave_pix">Chave PIX</label>
                    <input type="text" id="chave_pix" name="chave_pix" class="form-control"
                        value="{{ old('chave_pix', $estagiario->chave_pix) }}">
                </div>
                <div class="form-group mb-2">
                    <label for="tipo_chave_pix">Tipo da Chave PIX</label>
                    <select id="tipo_chave_pix" name="tipo_chave_pix" class="form-control">
                        <option value="">Selecione o tipo</option>
                        <option value="CPF" {{ old('tipo_chave_pix', $estagiario->tipo_chave_pix) == 'CPF' ? 'selected' : '' }}>CPF</option>
                        <option value="EMAIL" {{ old('tipo_chave_pix', $estagiario->tipo_chave_pix) == 'EMAIL' ? 'selected' : '' }}>EMAIL</option>
                        <option value="TELEFONE" {{ old('tipo_chave_pix', $estagiario->tipo_chave_pix) == 'TELEFONE' ? 'selected' : '' }}>TELEFONE</option>
                        <option value="ALEATORIA" {{ old('tipo_chave_pix', $estagiario->tipo_chave_pix) == 'ALEATORIA' ? 'selected' : '' }}>ALEATÓRIA</option>
                    </select>
                </div>
                <div class="form-group mb-2">
                    <label for="foto_documento">Documento de Identidade</label>
                    <input type="file" id="foto_documento" name="foto_documento" class="form-control">
                </div>
                <div class="form-group mb-2">
                    <label for="comprovante_residencia">Comprovante de Residência</label>
                    <input type="file" id="comprovante_residencia" name="comprovante_residencia" class="form-control">
                </div>
                <div class="form-group mb-2">
                    <label for="comprovante_escolar">Comprovante Escolar</label>
                    <input type="file" id="comprovante_escolar" name="comprovante_escolar" class="form-control">
                </div>
            </div>
        </div>

        <div class="form-group mt-3 text-end">
            <button type="submit" class="btn btn-primary">Atualizar Estagiário</button>
        </div>
    </form>

    <script>
        // Aguarda o carregamento completo da página
        document.addEventListener('DOMContentLoaded', function () {
            const estadoSelect = document.getElementById('fk_id_estado');
            const cidadesSelect = document.getElementById('fk_id_cidade');
            const cidadeOriginal = cidadesSelect.value; // Salva a cidade original
            const possuiNomeSocialCheckbox = document.getElementById('possui_nome_social');
            const grupoNomeSecundario = document.getElementById('grupo_nome_secundario');
            const nomeSecundarioInput = document.getElementById('nome_secundario');
            const labelNomeEstagiario = document.getElementById('label_nome_estagiario');

            function atualizarCamposNomeSocial() {
                const ativo = possuiNomeSocialCheckbox.checked;
                grupoNomeSecundario.style.display = ativo ? 'block' : 'none';
                nomeSecundarioInput.required = ativo;
                labelNomeEstagiario.textContent = ativo ? 'Nome Social' : 'Nome do Estagiário';

                if (!ativo) {
                    nomeSecundarioInput.value = '';
                }
            }

            possuiNomeSocialCheckbox.addEventListener('change', atualizarCamposNomeSocial);
            atualizarCamposNomeSocial();

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
    </script>

@endsection