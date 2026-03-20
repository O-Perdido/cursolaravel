@extends('layouts.main')

@section('title', 'SIGE Concursos | Dashboard')

@section('content')
    <div class="container"
        style="background-color: rgb(242, 242, 242); border-radius: 15px; margin-top: -30px; padding: 18px 20px; margin-bottom: 20px;">
        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <h3 class="mb-1">SIGE Concursos</h3>
                <h4 class="mb-0">{{ Auth::user()->name }}</h4>
            </div>
            <div class="col-md-6">
                <p class="lead mb-0">
                    Este painel concentra a entrada do novo módulo de concursos, mantendo a navegação separada do contexto
                    de estágios.
                </p>
            </div>
        </div>
    </div>

    <hr style="margin-top: -10px; background-color: #102e6c;">

    <div class="row mb-3">
        <div class="col">
            <p class="mb-0">Use os cards abaixo para acessar as áreas iniciais do módulo SIGE Concursos.</p>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <div class="col">
            <div class="card text-center h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column">
                    <div class="mb-3">
                        <i class="fa-solid fa-folder-tree fa-3x" style="color: #102e6c;"></i>
                    </div>
                    <h5 class="card-title">Processos</h5>
                    <p class="card-text flex-grow-1">Acesse a área que concentrará listagem, cadastro, edição e exclusão dos
                        processos do módulo.</p>
                    <a href="{{ route('sigeconcursos.processos.index') }}" class="btn btn-primary">Gerenciar Processos</a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card text-center h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column">
                    <div class="mb-3">
                        <i class="fa-solid fa-building-columns fa-3x" style="color: #102e6c;"></i>
                    </div>
                    <h5 class="card-title">Órgãos Públicos e Empresas</h5>
                    <p class="card-text flex-grow-1">Centralize aqui o gerenciamento das entidades responsáveis pelos
                        processos seletivos do novo módulo.</p>
                    <a href="{{ route('sigeconcursos.orgaos.index') }}" class="btn btn-primary">Gerenciar
                        Órgãos/Empresas</a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card text-center h-100 shadow-sm border-0">
                <div class="card-body d-flex flex-column">
                    <div class="mb-3">
                        <i class="fa-solid fa-users-viewfinder fa-3x" style="color: #102e6c;"></i>
                    </div>
                    <h5 class="card-title">Candidatos</h5>
                    <p class="card-text flex-grow-1">Visualize a base de candidatos cadastrados e prepare o espaço para
                        futuras ações de consulta e manutenção.</p>
                    <a href="{{ route('sigeconcursos.candidatos.index') }}" class="btn btn-primary">Gerenciar Candidatos</a>
                </div>
            </div>
        </div>
    </div>
@endsection