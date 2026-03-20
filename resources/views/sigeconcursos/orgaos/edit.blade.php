@extends('layouts.main')

@section('title', 'SIGE Concursos | Editar Órgão/Empresa')

@section('content')
    <h1 class="mb-3">Editar Órgão Público / Empresa</h1>

    @include('sigeconcursos.orgaos._form', [
        'action' => route('sigeconcursos.orgaos.update', $orgao->id_empresa),
        'method' => 'PUT',
        'submitLabel' => 'Salvar alterações',
        'orgao' => $orgao,
        'estados' => $estados,
        'cidades' => $cidades,
    ])
@endsection