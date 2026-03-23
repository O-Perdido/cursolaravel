@extends('layouts.main')

@section('title', 'SIGE Concursos | Editar Meus Dados')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Editar Meus Dados</h2>
            <p class="text-muted mb-0">Atualize suas informações cadastrais. A senha é gerenciada pela opção de redefinição
                de acesso.</p>
        </div>
        <a href="{{ route('sigeconcursos.candidato.perfil') }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>

    <div class="alert alert-warning">
        Se você alterar o e-mail cadastrado, o e-mail usado no login também será atualizado.
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
        'action' => route('sigeconcursos.candidato.perfil.atualizar'),
        'method' => 'PUT',
        'candidato' => $candidato,
        'estados' => $estados,
        'cidades' => $cidades,
        'orgaosExpedidores' => $orgaosExpedidores,
        'ufs' => $ufs,
        'showPasswordFields' => false,
        'submitLabel' => 'Salvar Alterações',
        'backUrl' => route('sigeconcursos.candidato.perfil'),
        'formId' => 'candidato-editar-form',
    ])
    <script>
    document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('candidato-editar-form');
        const emailInput = document.getElementById('email');
        const originalEmail = '{{ old('email', $candidato->email) }}';
        form.addEventListener('submit', function (event) {
                    if (emailInput.value.trim().toLowerCase() === originalEmail.trim().toLowerCase()) {
                        return;
                        }

                        const confirmed = window.confirm('Ao alterar o e-mail cadastrado, o e-mail de login também será atualizado automaticamente. Deseja continuar?');

                        if (!confirmed) {
                            event.preventDefault();
                        }
                    });
                });
            </script>
@endsection