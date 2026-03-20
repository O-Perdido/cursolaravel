@extends('layouts.main')

@section('title', 'SIGE Concursos | Processos')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Processos</h2>
            <p class="text-muted mb-0">Base inicial da área de gerenciamento de processos do SIGE Concursos.</p>
        </div>
        <a href="{{ route('sigeconcursos.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i> Voltar ao dashboard
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title">Estrutura preparada</h5>
            <p class="card-text mb-3">Esta página já está separada no módulo sigeconcursos e pronta para receber listagem,
                cadastro, edição e exclusão dos processos.</p>
            <div class="alert alert-info mb-0">
                Próxima etapa sugerida: conectar esta área com o fluxo de cadastro e manutenção dos processos de concursos.
            </div>
        </div>
    </div>
@endsection