@extends('layouts.main')

@section('title', 'Meu Perfil - Estagiário')

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="color: #2d3748; font-weight: 700;">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-person-circle me-2" viewBox="0 0 16 16">
                    <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                    <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
                </svg>
                Meu Perfil
            </h2>
            <p class="text-muted mb-0">Visualize e gerencie suas informações pessoais</p>
        </div>
        <div>
            <a href="{{ route('welcome.estagiario') }}" style="margin-bottom: 10px;" class="btn btn-outline-secondary me-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-1" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                </svg>
                Voltar
            </a>
            <a href="{{ route('estagiario.perfil.editar') }}" style="margin-bottom: 10px;" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil me-1" viewBox="0 0 16 16">
                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325"/>
                </svg>
                Editar Dados
            </a>
        </div>
    </div>

    <hr>

    <div class="row g-4">
        <!-- Coluna Esquerda: Dados Pessoais -->
        <div class="col-lg-8">
            
            <!-- Card: Informações Pessoais -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header" style="background: linear-gradient(135deg, #102E6C 0%, #1e5bb8 100%); color: white; border-radius: 12px 12px 0 0; padding: 20px;">
                    <h5 class="mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-vcard me-2" viewBox="0 0 16 16">
                            <path d="M5 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4m4-2.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5M9 8a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 9 8m1 2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5"/>
                            <path d="M2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2zM1 4a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H8.96q.04-.245.04-.5C9 10.567 7.21 9 5 9c-2.086 0-3.8 1.398-3.984 3.181A1 1 0 0 1 1 12z"/>
                        </svg>
                        Informações Pessoais
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="text-muted small mb-1">Nome Completo</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->nome_estagiario }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">CPF</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->numero_cpf }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Data de Nascimento</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->data_nascimento }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">E-mail</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Nome da Mãe</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->nome_mae ?? 'Não informado' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Contatos -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="border-radius: 12px 12px 0 0; padding: 20px;">
                    <h5 class="mb-0" style="color: #2d3748;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-telephone me-2" viewBox="0 0 16 16">
                            <path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.6 17.6 0 0 0 4.168 6.608 17.6 17.6 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.68.68 0 0 0-.58-.122l-2.19.547a1.75 1.75 0 0 1-1.657-.459L5.482 8.062a1.75 1.75 0 0 1-.46-1.657l.548-2.19a.68.68 0 0 0-.122-.58zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
                        </svg>
                        Contatos
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Telefone</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->numero_telefone ?? 'Não informado' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Celular</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->numero_celular ?? 'Não informado' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Endereço -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="border-radius: 12px 12px 0 0; padding: 20px;">
                    <h5 class="mb-0" style="color: #2d3748;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-geo-alt me-2" viewBox="0 0 16 16">
                            <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10"/>
                            <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                        </svg>
                        Endereço
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="text-muted small mb-1">CEP</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->numero_cep ?? 'Não informado' }}</p>
                        </div>
                        <div class="col-md-8">
                            <label class="text-muted small mb-1">Endereço/Logradouro</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->endereco ?? 'Não informado' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small mb-1">Número</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->numero_endereco ?? 'S/N' }}</p>
                        </div>
                        <div class="col-md-8">
                            <label class="text-muted small mb-1">Bairro</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->bairro ?? 'Não informado' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small mb-1">Cidade/UF</label>
                            <p class="fw-semibold mb-0">
                                @if($estagiario->cidade)
                                    {{ $estagiario->cidade->nm_cidade }} / {{ $estagiario->cidade->estado->uf_estado }}
                                @else
                                    Não informado
                                @endif
                            </p>
                        </div>
                        <div class="col-md-8">
                            <label class="text-muted small mb-1">Complemento</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->complemento_endereco ?? 'Sem complemento' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Informações Acadêmicas -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="border-radius: 12px 12px 0 0; padding: 20px;">
                    <h5 class="mb-0" style="color: #2d3748;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-mortarboard me-2" viewBox="0 0 16 16">
                            <path d="M8.211 2.047a.5.5 0 0 0-.422 0l-7.5 3.5a.5.5 0 0 0 .025.917l7.5 3a.5.5 0 0 0 .372 0L14 7.14V13a1 1 0 0 0-1 1v2h3v-2a1 1 0 0 0-1-1V6.739l.686-.275a.5.5 0 0 0 .025-.917z"/>
                            <path d="M4.176 9.032a.5.5 0 0 0-.656.327l-.5 1.7a.5.5 0 0 0 .294.605l4.5 1.8a.5.5 0 0 0 .372 0l4.5-1.8a.5.5 0 0 0 .294-.605l-.5-1.7a.5.5 0 0 0-.656-.327L8 10.466z"/>
                        </svg>
                        Informações Acadêmicas
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="text-muted small mb-1">Instituição de Ensino</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->instituicao_ensino ?? 'Não informado' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Curso</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->curso ?? 'Não informado' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Nível do Curso</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->nivel_curso ?? 'Não informado' }}</p>
                        </div>
                        <div class="col-md-12">
                            <label class="text-muted small mb-1">Área de Estágio</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->area_de_estagio ?? 'Não informado' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Informações Bancárias -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header bg-white border-bottom" style="border-radius: 12px 12px 0 0; padding: 20px;">
                    <h5 class="mb-0" style="color: #2d3748;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-bank me-2" viewBox="0 0 16 16">
                            <path d="m8 0 6.61 3h.89a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v7a.5.5 0 0 1 .485.38l.5 2a.498.498 0 0 1-.485.62H.5a.498.498 0 0 1-.485-.62l.5-2A.5.5 0 0 1 1 13V6H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 3h.89zM3.777 3h8.447L8 1zM2 6v7h1V6zm2 0v7h2.5V6zm3.5 0v7h1V6zm2 0v7H12V6zM13 6v7h1V6zm2-1V4H1v1zm-.39 9H1.39l-.25 1h13.72z"/>
                        </svg>
                        Informações Bancárias
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Número PIS</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->numero_pis ?? 'Não informado' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">Tipo de Chave PIX</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->tipo_chave_pix ? ucfirst($estagiario->tipo_chave_pix) : 'Não informado' }}</p>
                        </div>
                        <div class="col-md-12">
                            <label class="text-muted small mb-1">Chave PIX</label>
                            <p class="fw-semibold mb-0">{{ $estagiario->chave_pix ?? 'Não informado' }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Coluna Direita: Documentos -->
        <div class="col-lg-4">
            
            <!-- Card: Documentos -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); color: white; border-radius: 12px 12px 0 0; padding: 20px;">
                    <h5 class="mb-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-file-earmark-text me-2" viewBox="0 0 16 16">
                            <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5"/>
                            <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/>
                        </svg>
                        Meus Documentos
                    </h5>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-3">Faça o download ou atualize seus documentos cadastrados.</p>
                    
                    <!-- Documento de Identidade -->
                    <div class="border rounded p-3 mb-3" style="background-color: #f8f9fa;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0" style="color: #2d3748;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-card-heading me-1" viewBox="0 0 16 16">
                                    <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2z"/>
                                    <path d="M3 8.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5m0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5m0-5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-9a.5.5 0 0 1-.5-.5z"/>
                                </svg>
                                Documento
                            </h6>
                            @if($estagiario->foto_documento)
                                <span class="badge bg-success">Enviado</span>
                            @else
                                <span class="badge bg-warning">Não enviado</span>
                            @endif
                        </div>
                        @if($estagiario->foto_documento)
                                     <a href="{{ route('estagiario.documento.download', ['campo' => 'foto_documento']) }}" 
                               class="btn btn-sm btn-outline-primary w-100 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-download me-1" viewBox="0 0 16 16">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
                                </svg>
                                Baixar
                            </a>
                        @endif
                        <button class="btn btn-sm btn-outline-secondary w-100" onclick="abrirModalDocumento('foto_documento')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-arrow-repeat me-1" viewBox="0 0 16 16">
                                <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41m-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9"/>
                                <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z"/>
                            </svg>
                            Atualizar
                        </button>
                    </div>

                    <!-- Comprovante de Residência -->
                    <div class="border rounded p-3 mb-3" style="background-color: #f8f9fa;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0" style="color: #2d3748;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-house-check me-1" viewBox="0 0 16 16">
                                    <path d="M7.293 1.5a1 1 0 0 1 1.414 0L11 3.793V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v3.293l2.354 2.353a.5.5 0 0 1-.708.708L8 2.207l-5 5V13.5a.5.5 0 0 0 .5.5h4a.5.5 0 0 1 0 1h-4A1.5 1.5 0 0 1 2 13.5V8.207l-.646.647a.5.5 0 1 1-.708-.708z"/>
                                    <path d="M12.5 16a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7m1.679-4.493-1.335 2.226a.75.75 0 0 1-1.174.144l-.774-.773a.5.5 0 0 1 .708-.708l.547.548 1.17-1.951a.5.5 0 1 1 .858.514"/>
                                </svg>
                                Comprovante de Residência
                            </h6>
                            @if($estagiario->comprovante_residencia)
                                <span class="badge bg-success">Enviado</span>
                            @else
                                <span class="badge bg-warning">Não enviado</span>
                            @endif
                        </div>
                        @if($estagiario->comprovante_residencia)
                                     <a href="{{ route('estagiario.documento.download', ['campo' => 'comprovante_residencia']) }}" 
                               class="btn btn-sm btn-outline-primary w-100 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-download me-1" viewBox="0 0 16 16">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
                                </svg>
                                Baixar
                            </a>
                        @endif
                        <button class="btn btn-sm btn-outline-secondary w-100" onclick="abrirModalDocumento('comprovante_residencia')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-arrow-repeat me-1" viewBox="0 0 16 16">
                                <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41m-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9"/>
                                <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z"/>
                            </svg>
                            Atualizar
                        </button>
                    </div>

                    <!-- Comprovante Escolar -->
                    <div class="border rounded p-3 mb-3" style="background-color: #f8f9fa;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0" style="color: #2d3748;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-check me-1" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M10.854 6.146a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708 0l-1.5-1.5a.5.5 0 1 1 .708-.708L7.5 8.793l2.646-2.647a.5.5 0 0 1 .708 0"/>
                                    <path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2"/>
                                    <path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1z"/>
                                </svg>
                                Comprovante Escolar
                            </h6>
                            @if($estagiario->comprovante_escolar)
                                <span class="badge bg-success">Enviado</span>
                            @else
                                <span class="badge bg-warning">Não enviado</span>
                            @endif
                        </div>
                        @if($estagiario->comprovante_escolar)
                                     <a href="{{ route('estagiario.documento.download', ['campo' => 'comprovante_escolar']) }}" 
                               class="btn btn-sm btn-outline-primary w-100 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-download me-1" viewBox="0 0 16 16">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                    <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"/>
                                </svg>
                                Baixar
                            </a>
                        @endif
                        <button class="btn btn-sm btn-outline-secondary w-100" onclick="abrirModalDocumento('comprovante_escolar')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-arrow-repeat me-1" viewBox="0 0 16 16">
                                <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41m-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9"/>
                                <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5 5 0 0 0 8 3M3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9z"/>
                            </svg>
                            Atualizar
                        </button>
                    </div>

                    <div class="alert alert-info mt-3" style="font-size: 0.85rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle me-1" viewBox="0 0 16 16">
                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                            <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533z"/>
                            <circle cx="8" cy="4.5" r="1"/>
                        </svg>
                        Ao atualizar um documento, o arquivo anterior será substituído automaticamente.
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Modal para Atualizar Documento -->
    <div class="modal fade" id="modalDocumento" tabindex="-1" aria-labelledby="modalDocumentoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('estagiario.documento.atualizar') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalDocumentoLabel">Atualizar Documento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="campo_documento" id="campoDocumento">
                        
                        <div class="alert alert-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-triangle me-2" viewBox="0 0 16 16">
                                <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                                <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                            </svg>
                            <strong>Atenção:</strong> O documento anterior será permanentemente substituído.
                        </div>
                        
                        <div class="mb-3">
                            <label for="novoDocumento" class="form-label">Selecione o novo arquivo</label>
                            <input type="file" class="form-control" id="novoDocumento" name="novo_documento" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">Formatos aceitos: PDF, JPG, JPEG, PNG (máx. 5MB)</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload me-1" viewBox="0 0 16 16">
                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/>
                                <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/>
                            </svg>
                            Atualizar Documento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function abrirModalDocumento(campo) {
            document.getElementById('campoDocumento').value = campo;
            const modal = new bootstrap.Modal(document.getElementById('modalDocumento'));
            modal.show();
        }
    </script>

@endsection
