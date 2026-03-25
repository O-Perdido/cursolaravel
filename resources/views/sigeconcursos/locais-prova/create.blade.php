@extends('layouts.main')

@section('title', 'SIGE Concursos | Novo Local de Prova')

@section('content')
    <h1 class="mb-3">Cadastrar Local de Prova</h1>

    @include('sigeconcursos.locais-prova._form', [
        'action' => route('sigeconcursos.locais-prova.store'),
        'method' => 'POST',
        'submitLabel' => 'Salvar local',
        'estados' => $estados,
    ])
@endsection