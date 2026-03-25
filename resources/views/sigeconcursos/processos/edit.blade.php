@extends('layouts.main')

@section('title', 'SIGE Concursos | Editar Processo')

@section('content')
    <h1 class="mb-3">Editar Processo</h1>

    @include('sigeconcursos.processos._form', [
        'action' => route('sigeconcursos.processos.update', $processo->id_processo),
        'method' => 'PUT',
        'submitLabel' => 'Salvar alterações',
        'processo' => $processo,
        'orgaos' => $orgaos,
        'cargos' => $cargos,
        'locais' => $locais,
    ])
@endsection