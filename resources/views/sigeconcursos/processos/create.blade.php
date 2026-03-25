@extends('layouts.main')

@section('title', 'SIGE Concursos | Novo Processo')

@section('content')
    <h1 class="mb-3">Cadastrar Processo</h1>

    @include('sigeconcursos.processos._form', [
        'action' => route('sigeconcursos.processos.store'),
        'method' => 'POST',
        'submitLabel' => 'Salvar processo',
        'orgaos' => $orgaos,
        'cargos' => $cargos,
        'locais' => $locais,
    ])
@endsection