@extends('layouts.main')

@section('title', 'SIGE Concursos | Detalhes do Candidato')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Detalhes do Candidato</h2>
            <p class="text-muted mb-0">Consulta administrativa dos dados do candidato.</p>
        </div>
        <a href="{{ route('sigeconcursos.candidatos.index') }}" class="btn btn-outline-secondary">Voltar para a listagem</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white"><strong>Informações Pessoais</strong></div>
                <div class="card-body row g-3">
                    <div class="col-12"><strong>Nome completo:</strong> {{ $candidato->nome_completo }}</div>
                    <div class="col-md-6"><strong>CPF:</strong>
                        {{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $candidato->numero_cpf) }}</div>
                    <div class="col-md-6"><strong>Data de nascimento:</strong>
                        {{ optional($candidato->data_nascimento)->format('d/m/Y') }}</div>
                    <div class="col-md-4"><strong>Sexo:</strong> {{ $candidato->sexo }}</div>
                    <div class="col-md-4"><strong>Canhoto:</strong> {{ $candidato->canhoto === 'sim' ? 'Sim' : 'Não' }}
                    </div>
                    <div class="col-md-4"><strong>E-mail:</strong> {{ $candidato->email }}</div>
                    <div class="col-md-4"><strong>RG:</strong> {{ $candidato->numero_rg }}</div>
                    <div class="col-md-4"><strong>Órgão expedidor:</strong> {{ $candidato->orgao_expedidor_rg }}</div>
                    <div class="col-md-4"><strong>UF do RG:</strong> {{ $candidato->uf_rg }}</div>
                    <div class="col-12"><strong>Nome da mãe:</strong> {{ $candidato->nome_mae }}</div>
                    <div class="col-md-6"><strong>Nacionalidade:</strong> {{ $candidato->nacionalidade }}</div>
                    <div class="col-md-3"><strong>Naturalidade - cidade:</strong> {{ $candidato->naturalidade_cidade }}
                    </div>
                    <div class="col-md-3"><strong>Naturalidade - estado:</strong> {{ $candidato->naturalidade_estado }}
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white"><strong>Endereço e Contato</strong></div>
                <div class="card-body row g-3">
                    <div class="col-md-4"><strong>CEP:</strong>
                        {{ preg_replace('/(\d{5})(\d{3})/', '$1-$2', $candidato->numero_cep) }}</div>
                    <div class="col-md-8"><strong>Endereço:</strong> {{ $candidato->endereco }}</div>
                    <div class="col-md-4"><strong>Número:</strong> {{ $candidato->numero_endereco }}</div>
                    <div class="col-md-8"><strong>Complemento:</strong>
                        {{ $candidato->complemento_endereco ?: 'Sem complemento' }}</div>
                    <div class="col-md-6"><strong>Bairro:</strong> {{ $candidato->bairro }}</div>
                    <div class="col-md-6"><strong>Cidade/UF:</strong> {{ $candidato->cidade?->nm_cidade }} /
                        {{ $candidato->cidade?->estado?->uf_estado }}</div>
                    <div class="col-md-6"><strong>Telefone:</strong> {{ $candidato->numero_telefone ?: 'Não informado' }}
                    </div>
                    <div class="col-md-6"><strong>Celular WhatsApp:</strong> {{ $candidato->numero_celular }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white"><strong>Acesso</strong></div>
                <div class="card-body">
                    <p><strong>Status do usuário:</strong> {{ $candidato->user ? 'Criado' : 'Não vinculado' }}</p>
                    <p><strong>E-mail de login:</strong> {{ $candidato->user?->email ?? 'Não disponível' }}</p>
                    <p class="mb-0"><strong>E-mail verificado:</strong>
                        {{ $candidato->user?->email_verified_at ? 'Sim' : 'Não' }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection