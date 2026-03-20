@extends('layouts.main')

@section('title', 'SIGE Concursos | Novo Órgão/Empresa')

@section('content')
    <h1 class="mb-3">Cadastrar Órgão Público / Empresa</h1>

    @include('sigeconcursos.orgaos._form', [
        'action' => route('sigeconcursos.orgaos.store'),
        'method' => 'POST',
        'submitLabel' => 'Salvar cadastro',
        'estados' => $estados,
    ])
@endsection