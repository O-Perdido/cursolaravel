@extends('layouts.main')

@section('title', 'SIGE Concursos | Candidatos')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Candidatos</h2>
            <p class="text-muted mb-0">Área inicial para visualização e futura manutenção dos candidatos do módulo SIGE
                Concursos.</p>
        </div>
        <a href="{{ route('sigeconcursos.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i> Voltar ao dashboard
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title">Estrutura preparada</h5>
            <p class="card-text mb-3">Esta página já está pronta para receber listagem, visualização, edição e exclusão dos
                candidatos cadastrados.</p>
            <div class="alert alert-info mb-0">
                Próxima etapa sugerida: definir o modelo de dados e a tela de consulta dos candidatos do SIGE Concursos.
            </div>
        </div>
    </div>
@endsection