@extends('layouts.main')

@section('title', 'Editar Meu Perfil')

@section('content')

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Erro ao atualizar dados:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="color: #2d3748; font-weight: 700;">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                    class="bi bi-pencil-square me-2" viewBox="0 0 16 16">
                    <path
                        d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                    <path fill-rule="evenodd"
                        d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                </svg>
                Editar Meu Perfil
            </h2>
            <p class="text-muted mb-0">Atualize suas informações pessoais</p>
        </div>
        <a href="{{ route('estagiario.perfil') }}" class="btn btn-outline-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg me-1"
                viewBox="0 0 16 16">
                <path
                    d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
            </svg>
            Cancelar
        </a>
    </div>

    <hr>

    <form action="{{ route('estagiario.perfil.atualizar') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <!-- Coluna Esquerda -->
            <div class="col-lg-6">

                <!-- Informações Pessoais -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-header text-white" style="background: linear-gradient(135deg, #102E6C 0%, #1e5bb8 100%); border-radius: 12px 12px 0 0;">
                        <h5 class="mb-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-person me-2" viewBox="0 0 16 16">
                                <path
                                    d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z" />
                            </svg>
                            Informações Pessoais
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="nome_estagiario" class="form-label">Nome Completo *</label>
                            <input type="text" class="form-control" id="nome_estagiario" name="nome_estagiario"
                                value="{{ old('nome_estagiario', $estagiario->nome_estagiario) }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="data_nascimento" class="form-label">Data de Nascimento *</label>
                                <input type="date" class="form-control" id="data_nascimento" name="data_nascimento"
                                    value="{{ old('data_nascimento', \Carbon\Carbon::createFromFormat('d/m/Y', $estagiario->data_nascimento)->format('Y-m-d')) }}"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nome_mae" class="form-label">Nome da Mãe *</label>
                                <input type="text" class="form-control" id="nome_mae" name="nome_mae"
                                    value="{{ old('nome_mae', $estagiario->nome_mae) }}" required>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <small>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                                    class="bi bi-info-circle me-1" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                    <path
                                        d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533z" />
                                    <circle cx="8" cy="4.5" r="1" />
                                </svg>
                                <strong>CPF não pode ser alterado.</strong> Em caso de erro, entre em contato com a
                                administração.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Contatos -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom" style="border-radius: 12px 12px 0 0;">
                        <h5 class="mb-0" style="color: #2d3748;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-telephone me-2" viewBox="0 0 16 16">
                                <path
                                    d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z" />
                            </svg>
                            Contatos
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail *</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ old('email', $estagiario->email) }}" required>
                            <div class="mt-2" style="font-size: 13px; color: #6c757d;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                                    class="bi bi-info-circle me-1" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                    <path
                                        d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533z" />
                                    <circle cx="8" cy="4.5" r="1" />
                                </svg>
                                Caso precise alterar o e-mail de login, entre em contato com nossa equipe.
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="numero_telefone" class="form-label">Telefone</label>
                                <input type="text" class="form-control" id="numero_telefone" name="numero_telefone"
                                    value="{{ old('numero_telefone', $estagiario->numero_telefone) }}"
                                    placeholder="(00) 0000-0000">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="numero_celular" class="form-label">Celular *</label>
                                <input type="text" class="form-control" id="numero_celular" name="numero_celular"
                                    value="{{ old('numero_celular', $estagiario->numero_celular) }}"
                                    placeholder="(00) 00000-0000" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Endereço -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom" style="border-radius: 12px 12px 0 0;">
                        <h5 class="mb-0" style="color: #2d3748;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-geo-alt me-2" viewBox="0 0 16 16">
                                <path
                                    d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10" />
                                <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                            </svg>
                            Endereço
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="numero_cep" class="form-label">CEP *</label>
                                <input type="text" class="form-control" id="numero_cep" name="numero_cep"
                                    value="{{ old('numero_cep', $estagiario->numero_cep) }}" placeholder="00000-000"
                                    required>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label for="endereco" class="form-label">Endereço *</label>
                                <input type="text" class="form-control" id="endereco" name="endereco"
                                    value="{{ old('endereco', $estagiario->endereco) }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="numero_endereco" class="form-label">Número *</label>
                                <input type="text" class="form-control" id="numero_endereco" name="numero_endereco"
                                    value="{{ old('numero_endereco', $estagiario->numero_endereco) }}" required>
                            </div>
                            <div class="col-md-9 mb-3">
                                <label for="complemento_endereco" class="form-label">Complemento</label>
                                <input type="text" class="form-control" id="complemento_endereco" name="complemento_endereco"
                                    value="{{ old('complemento_endereco', $estagiario->complemento_endereco) }}" placeholder="Apt, Bloco, etc.">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="bairro" class="form-label">Bairro *</label>
                            <input type="text" class="form-control" id="bairro" name="bairro"
                                value="{{ old('bairro', $estagiario->bairro) }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fk_id_estado" class="form-label">Estado *</label>
                                <select class="form-select" id="fk_id_estado" name="fk_id_estado" required>
                                    <option value="">Selecione um estado</option>
                                    @foreach($estados as $estado)
                                        <option value="{{ $estado->id_estado }}" {{ old('fk_id_estado', $estagiario->cidade->fk_id_estado ?? '') == $estado->id_estado ? 'selected' : '' }}>
                                            {{ $estado->nm_estado }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fk_id_cidade" class="form-label">Cidade *</label>
                                <select class="form-select" id="fk_id_cidade" name="fk_id_cidade" required>
                                    <option value="">Selecione uma cidade</option>
                                    @foreach($estados as $estado)
                                        @foreach($estado->cidades as $cidade)
                                            <option value="{{ $cidade->id_cidade }}" 
                                                data-estado="{{ $estado->id_estado }}"
                                                {{ old('fk_id_cidade', $estagiario->fk_id_cidade) == $cidade->id_cidade ? 'selected' : '' }}>
                                                {{ $cidade->nm_cidade }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Coluna Direita -->
            <div class="col-lg-6">

                <!-- Informações Acadêmicas -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom" style="border-radius: 12px 12px 0 0;">
                        <h5 class="mb-0" style="color: #2d3748;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-mortarboard me-2" viewBox="0 0 16 16">
                                <path
                                    d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z" />
                                <path
                                    d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466z" />
                            </svg>
                            Informações Acadêmicas
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="instituicao_ensino" class="form-label">Instituição de Ensino *</label>
                            <input type="text" class="form-control" id="instituicao_ensino" name="instituicao_ensino"
                                value="{{ old('instituicao_ensino', $estagiario->instituicao_ensino) }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="curso" class="form-label">Curso *</label>
                            <input type="text" class="form-control" id="curso" name="curso"
                                value="{{ old('curso', $estagiario->curso) }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nivel_curso" class="form-label">Nível do Curso *</label>
                                <input type="text" class="form-control" id="nivel_curso" name="nivel_curso"
                                    value="{{ old('nivel_curso', $estagiario->nivel_curso) }}" 
                                    placeholder="Ex: Graduação, Ensino Médio" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="area_de_estagio" class="form-label">Área de Estágio *</label>
                                <input type="text" class="form-control" id="area_de_estagio" name="area_de_estagio"
                                    value="{{ old('area_de_estagio', $estagiario->area_de_estagio) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informações Bancárias -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom" style="border-radius: 12px 12px 0 0;">
                        <h5 class="mb-0" style="color: #2d3748;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-bank me-2" viewBox="0 0 16 16">
                                <path
                                    d="m8 0 6.61 3h.89a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v7a.5.5 0 0 1 .485.38l.5 2a.498.498 0 0 1-.485.62H.5a.498.498 0 0 1-.485-.62l.5-2A.5.5 0 0 1 1 13V6H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 3h.89zM3.777 3h8.447L8 1zM2 6v7h1V6zm2 0v7h2.5V6zm3.5 0v7h1V6zm2 0v7H12V6zM13 6v7h1V6zm2-1V4H1v1zm-.39 9H1.39l-.25 1h13.72z" />
                            </svg>
                            Informações Bancárias
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label for="numero_pis" class="form-label">Número PIS</label>
                            <input type="text" class="form-control" id="numero_pis" name="numero_pis"
                                value="{{ old('numero_pis', $estagiario->numero_pis) }}" placeholder="000.00000.00-0">
                        </div>
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label for="tipo_chave_pix" class="form-label">Tipo Chave PIX</label>
                                <select class="form-select" id="tipo_chave_pix" name="tipo_chave_pix">
                                    <option value="">Selecione</option>
                                    <option value="CPF" {{ old('tipo_chave_pix', $estagiario->tipo_chave_pix) == 'CPF' ? 'selected' : '' }}>CPF</option>
                                    <option value="EMAIL" {{ old('tipo_chave_pix', $estagiario->tipo_chave_pix) == 'EMAIL' ? 'selected' : '' }}>E-mail</option>
                                    <option value="TELEFONE" {{ old('tipo_chave_pix', $estagiario->tipo_chave_pix) == 'TELEFONE' ? 'selected' : '' }}>Telefone</option>
                                    <option value="ALEATORIA" {{ old('tipo_chave_pix', $estagiario->tipo_chave_pix) == 'ALEATORIA' ? 'selected' : '' }}>Aleatória</option>
                                </select>
                            </div>
                            <div class="col-md-7 mb-3">
                                <label for="chave_pix" class="form-label">Chave PIX</label>
                                <input type="text" class="form-control" id="chave_pix" name="chave_pix"
                                    value="{{ old('chave_pix', $estagiario->chave_pix) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="card border-0 shadow-sm"
                    style="border-radius: 12px; background: linear-gradient(135deg, #102E6C 0%, #1e5bb8 100%);">
                    <div class="card-body p-4">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-light btn-lg" style="font-weight: 600; color: #102E6C;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                    class="bi bi-check-circle me-2" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                    <path
                                        d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05" />
                                </svg>
                                Salvar Alterações
                            </button>
                            <a href="{{ route('estagiario.perfil') }}" class="btn btn-outline-secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-x-circle me-2" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                    <path
                                        d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                                </svg>
                                Cancelar
                            </a>
                        </div>

                        <div class="alert alert-warning mt-3 mb-0">
                            <small>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor"
                                    class="bi bi-exclamation-triangle me-1" viewBox="0 0 16 16">
                                    <path
                                        d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z" />
                                    <path
                                        d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z" />
                                </svg>
                                <strong>Campos marcados com * são obrigatórios.</strong> Para atualizar documentos, acesse a
                                página de visualização do perfil.
                            </small>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const estadoSelect = document.getElementById('fk_id_estado');
            const cidadeSelect = document.getElementById('fk_id_cidade');
            const todasCidades = Array.from(cidadeSelect.querySelectorAll('option[data-estado]'));

            function filtrarCidades() {
                const estadoId = estadoSelect.value;
                
                // Limpar e adicionar opção padrão
                cidadeSelect.innerHTML = '<option value="">Selecione uma cidade</option>';
                
                if (estadoId) {
                    // Filtrar e adicionar apenas cidades do estado selecionado
                    todasCidades.forEach(option => {
                        if (option.dataset.estado === estadoId) {
                            cidadeSelect.appendChild(option.cloneNode(true));
                        }
                    });
                } else {
                    // Se nenhum estado selecionado, mostrar todas
                    todasCidades.forEach(option => {
                        cidadeSelect.appendChild(option.cloneNode(true));
                    });
                }
            }

            // Filtrar ao mudar estado
            estadoSelect.addEventListener('change', filtrarCidades);

            // Filtrar no carregamento se houver estado selecionado
            if (estadoSelect.value) {
                filtrarCidades();
            }
        });
    </script>

@endsection