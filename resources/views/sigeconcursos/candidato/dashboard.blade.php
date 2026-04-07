@extends('layouts.main')

@section('title', 'SIGE Concursos | Área do Candidato')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="container mb-4"
        style="background: linear-gradient(135deg, #102E6C 0%, #1a4d9e 100%); border-radius: 15px; margin-top: -30px; padding: 30px; color: white; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        <div class="row align-items-center">
            <div class="col-md-12 text-center">
                <h2 class="mb-2" style="font-weight: 600;">Bem-vindo(a), {{ Auth::user()->name }}!</h2>
                <p class="mb-0" style="opacity: 0.9; font-size: 1.05rem;">Acompanhe seus dados cadastrais e prepare seu
                    acesso para os próximos processos do SIGE Concursos.</p>
            </div>
        </div>
    </div>

    <hr style="margin-top: -10px; background-color: #102e6c;">

    <div class="row g-4 mt-2">
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 60px; height: 60px; background: linear-gradient(135deg, #102E6C 0%, #1e5bb8 100%);">
                            <i class="fa-solid fa-id-card text-white fa-xl"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Meus Dados</h5>
                            <small class="text-muted">Consulta e atualização cadastral</small>
                        </div>
                    </div>

                    <p class="text-muted mb-4">
                        Consulte seus dados pessoais, confira as informações do cadastro e atualize o que for necessário.
                    </p>

                    <div class="d-grid gap-2">
                        <a href="{{ route('sigeconcursos.candidato.perfil') }}" class="btn btn-primary">Visualizar Meus
                            Dados</a>
                        <a href="{{ route('sigeconcursos.candidato.perfil.editar') }}"
                            class="btn btn-outline-primary">Editar Cadastro</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 60px; height: 60px; background: linear-gradient(135deg, #13502b 0%, #4ebb7c 100%);">
                            <i class="fa-solid fa-file-signature text-white fa-xl"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-0">Inscrições</h5>
                            <small class="text-muted">Próxima etapa do módulo</small>
                        </div>
                    </div>

                    <p class="text-muted mb-4">
                        Realize novas inscrições, acompanhe seu número de protocolo e verifique o status de cada processo.
                    </p>

                    <div class="alert alert-info mb-3">
                        <strong>Total de inscrições:</strong> {{ $totalInscricoes ?? 0 }}
                    </div>

                    @if(!empty($inscricoesRecentes) && $inscricoesRecentes->count() > 0)
                        <div class="mb-3">
                            @foreach($inscricoesRecentes as $inscricaoRecente)
                                <div class="small text-muted mb-1">
                                    {{ $inscricaoRecente->numero_inscricao }} - {{ $inscricaoRecente->processo?->titulo }}
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        <a href="{{ route('sigeconcursos.candidato.processos.index') }}" class="btn btn-success">Ver
                            Todos os Processos</a>
                        <a href="{{ route('sigeconcursos.candidato.minhas-inscricoes') }}"
                            class="btn btn-outline-success">Minhas Inscrições</a>
                        <a href="{{ route('sigeconcursos.candidato.minhas-isencoes') }}"
                            class="btn btn-outline-warning">Minhas Isenções</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection