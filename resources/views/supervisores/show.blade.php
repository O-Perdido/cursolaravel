@extends('layouts.main')

@section('title', 'Mostrar Supervisor')

@section('content')
    <h1>Detalhes do Supervisor</h1>
    <a href="{{ route('supervisores.index') }}" class="btn btn-secondary mb-3">Voltar</a> <!-- Botão de Voltar -->
    <div class="card">
        <div class="card-header">
            {{ $supervisor->nome_supervisor }}
        </div>
        <div class="card-body">
            <p><strong>ID:</strong> {{ $supervisor->id_supervisor }}</p>
            <p><strong>Empresa:</strong> {{ $supervisor->fk_id_empresa }}</p>
            <p><strong>Área de Formação:</strong> {{ $supervisor->area_formacao }}</p>
            <p><strong>Tempo de Experiência:</strong> {{ $supervisor->tempo_experiencia }} anos</p>
            <p><strong>Celular:</strong>
                {{ $supervisor->celular_supervisor
        ? preg_replace('/(\d{2})(\d{4,5})(\d{4})/', '($1) $2-$3', $supervisor->celular_supervisor)
        : '—' }}
            </p>
            <p><strong>E-mail:</strong> {{ $supervisor->email_supervisor ?: '—' }}</p>
        </div>
        <div class="card-footer">
            <a href="{{ route('supervisores.edit', $supervisor->id_supervisor) }}" class="btn btn-info">Editar</a>
            <form action="{{ route('supervisores.destroy', $supervisor->id_supervisor) }}" method="POST"
                style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Excluir</button>
            </form>
        </div>
    </div>
@endsection