@extends('layouts.main')

@section('title', 'SIGE Concursos | Editar Cargo')

@section('content')
    <h1 class="mb-3">Editar Cargo</h1>

    @include('sigeconcursos.cargos._form', [
        'action' => route('sigeconcursos.cargos.update', $cargo->id_cargo),
        'method' => 'PUT',
        'submitLabel' => 'Salvar alterações',
        'cargo' => $cargo,
    ])
@endsection