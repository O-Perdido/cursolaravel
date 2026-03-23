@extends('layouts.main')

@section('title', 'SIGE Concursos | Meus Dados')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Meus Dados</h2>
            <p class="text-muted mb-0">Consulte suas informações cadastrais do módulo SIGE Concursos.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sigeconcursos.candidato.dashboard') }}" class="btn btn-outline-secondary">Voltar</a>
            <a href="{{ route('sigeconcursos.candidato.perfil.editar') }}" class="btn btn-primary">Editar Dados</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white"><strong>Informações Pessoais</strong></div>
                <div class="card-body row g-3">
                    <div class="col-md-12"><strong>Nome:</strong> {{ $candidato->nome_completo }}</div>
                    <div class="col-md-6"><strong>CPF:</strong> {{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $candidato->numero_cpf) }}</div>
                    <div class="col-md-6"><strong>Data de nascimento:</strong> {{ optional($candidato->data_nascimento)->format('d/m/Y') }}</div>
                    <div class="col-md-6"><strong>Sexo:</strong> {{ $candidato->sexo }}</div>
                    <div class="col-md-6"><strong>Canhoto:</strong> {{ $candidato->canhoto === 'sim' ? 'Sim' : 'Não' }}</div>
                    <div class="col-md-12"><strong>E-mail:</strong> {{ $candidato->email }}</div>
                    <div class="col-md-6"><strong>RG:</strong> {{ $candidato->numero_rg }}</div>
                    <div class="col-md-3"><strong>Órgão expedidor:</strong> {{ $candidato->orgao_expedidor_rg }}</div>
                    <div class="col-md-3"><strong>UF do RG:</strong> {{ $candidato->uf_rg }}</div>
                    <div class="col-md-12"><strong>Nome da mãe:</strong> {{ $candidato->nome_mae }}</div>
                    <div class="col-md-6"><strong>Nacionalidade:</strong> {{ $candidato->nacionalidade }}</div>
                    <div class="col-md-3"><strong>Naturalidade - cidade:</strong> {{ $candidato->naturalidade_cidade }}</div>
                    <div class="col-md-3"><strong>Naturalidade - estado:</strong> {{ $candidato->naturalidade_estado }}</div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white"><strong>Endereço</strong></div>
                <div class="card-body row g-3">
                    <div class="col-md-4"><strong>CEP:</strong> {{ preg_replace('/(\d{5})(\d{3})/', '$1-$2', $candidato->numero_cep) }}</div>
                    <div class="col-md-8"><strong>Endereço:</strong> {{ $candidato->endereco }}</div>
                    <div class="col-md-4"><strong>Número:</strong> {{ $candidato->numero_endereco }}</div>
                    <div class="col-md-8"><strong>Complemento:</strong> {{ $candidato->complemento_endereco ?: 'Sem complemento' }}</div>
                    <div class="col-md-6"><strong>Bairro:</strong> {{ $candidato->bairro }}</div>
                    <div class="col-md-6"><strong>Cidade/UF:</strong> {{ $candidato->cidade?->nm_cidade }} / {{ $candidato->cidade?->estado?->uf_estado }}</div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><strong>Contatos</strong></div>
                <div class="card-body">
                    <p><strong>Telefone:</strong> {{ $candidato->numero_telefone ?: 'Não informado' }}</p>
                    <p class="mb-0"><strong>Celular WhatsApp:</strong> {{ $candidato->numero_celular }}</p>
                </div>
            </div>
        </div>
    </div>
@endsection