@extends('layouts.main')

@section('title', 'SIGE Concursos | Cadastro de Candidato')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Cadastro de Candidato</h2>
            <p class="text-muted mb-0">Preencha seus dados pessoais e crie o acesso para entrar na área do candidato.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sigeconcursos.candidato.login') }}" class="btn btn-outline-primary">
                <i class="fa-solid fa-right-to-bracket"></i> Já tenho cadastro
            </a>
            <a href="{{ route('landing') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="alert alert-info">
        O e-mail informado será usado também como login. Após concluir o cadastro, você receberá um código para validar o
        acesso.
    </div>

    <div class="alert alert-light border">
        <strong>Campos obrigatórios:</strong> os campos marcados com <span class="text-danger">*</span> precisam ser
        preenchidos.
    </div>

    @if(session('candidate_email_platform_conflict'))
        <div class="alert alert-warning">
            {{ session('candidate_email_platform_conflict.message') }}<br>
            Informe um e-mail diferente. Se não for possível, <a href="{{ session('candidate_email_platform_conflict.url') }}"
                target="_blank" rel="noopener">entre em contato com o suporte</a>.
        </div>
    @endif

    @include('sigeconcursos.candidato._form', [
        'action' => route('sigeconcursos.candidato.store'),
        'method' => 'POST',
        'estados' => $estados,
        'orgaosExpedidores' => $orgaosExpedidores,
        'ufs' => $ufs,
        'showPasswordFields' => true,
        'submitLabel' => 'Concluir Cadastro',
        'backUrl' => route('sigeconcursos.candidato.login'),
        'formId' => 'candidato-cadastro-form',
    ])
@endsection