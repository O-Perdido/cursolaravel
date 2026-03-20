@extends('layouts.main')

@section('title', 'SIGE Concursos | Órgãos e Empresas')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">Órgãos Públicos e Empresas</h2>
            <p class="text-muted mb-0">Área inicial para o gerenciamento das entidades vinculadas ao módulo SIGE Concursos.
            </p>
        </div>
        <a href="{{ route('sigeconcursos.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left"></i> Voltar ao dashboard
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title">Estrutura preparada</h5>
            <p class="card-text mb-3">A separação visual e de rota já está criada para esta área, mantendo o módulo
                independente do fluxo de estágios.</p>
            <div class="alert alert-info mb-0">
                Próxima etapa sugerida: definir os campos e o CRUD de órgãos públicos e empresas do SIGE Concursos.
            </div>
        </div>
    </div>
@endsection