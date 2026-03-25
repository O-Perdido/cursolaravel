@extends('layouts.main')

@section('title', 'SIGE Concursos | Editar Local de Prova')

@section('content')
    <h1 class="mb-3">Editar Local de Prova</h1>

    @include('sigeconcursos.locais-prova._form', [
        'action' => route('sigeconcursos.locais-prova.update', $local->id_local_prova),
        'method' => 'PUT',
        'submitLabel' => 'Salvar alterações',
        'local' => $local,
        'estados' => $estados,
        'cidades' => $cidades,
    ])
@endsection