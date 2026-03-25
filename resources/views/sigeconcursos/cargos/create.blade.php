@extends('layouts.main')

@section('title', 'SIGE Concursos | Novo Cargo')

@section('content')
    <h1 class="mb-3">Cadastrar Cargo</h1>

    @include('sigeconcursos.cargos._form', [
        'action' => route('sigeconcursos.cargos.store'),
        'method' => 'POST',
        'submitLabel' => 'Salvar cargo',
    ])
@endsection