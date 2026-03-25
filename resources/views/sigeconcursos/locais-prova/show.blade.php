@extends('layouts.main')

@section('title', 'SIGE Concursos | Detalhes do Local de Prova')

@section('content')
    <h1>Detalhes do Local de Prova</h1>
    <button onclick="window.NavigationHistory?.goBack('{{ route('sigeconcursos.locais-prova.index') }}')"
        class="btn btn-secondary mb-3" title="Voltar para a página anterior com filtros preservados">Voltar</button>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $local->nome_local }}</h5>
            <span
                class="badge {{ $local->ativo ? 'bg-success' : 'bg-secondary' }}">{{ $local->ativo ? 'Ativo' : 'Inativo' }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Endereço</h6>
                    <p class="mb-1"><strong>CEP:</strong>
                        {{ $local->numero_cep ? preg_replace('/(\d{5})(\d{3})/', '$1-$2', $local->numero_cep) : '' }}</p>
                    <p class="mb-1"><strong>Endereço:</strong> {{ $local->endereco }}, {{ $local->numero_endereco }}</p>
                    <p class="mb-1"><strong>Complemento:</strong> {{ $local->complemento_endereco ?: 'Não informado' }}</p>
                    <p class="mb-1"><strong>Bairro:</strong> {{ $local->bairro }}</p>
                    <p class="mb-1"><strong>Cidade/UF:</strong> {{ $local->cidade?->nm_cidade }} /
                        {{ $local->cidade?->estado?->uf_estado }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Observações</h6>
                    <div class="border rounded p-3 bg-light" style="white-space: pre-line; min-height: 120px;">
                        {{ $local->observacoes ?: 'Nenhuma observação cadastrada.' }}
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('sigeconcursos.locais-prova.edit', $local->id_local_prova) }}" class="btn btn-info">Editar</a>
            <form action="{{ route('sigeconcursos.locais-prova.destroy', $local->id_local_prova) }}" method="POST"
                style="display:inline;" onsubmit="return confirm('Confirma a exclusão deste local de prova?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Excluir</button>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header">Salas Cadastradas</div>
                <div class="card-body">
                    @forelse($local->salas as $sala)
                        <div class="border rounded p-3 mb-2 bg-light">
                            <div class="fw-semibold">{{ $sala->nome_sala }}</div>
                            <div class="small text-muted">Bloco: {{ $sala->bloco ?: 'Não informado' }}</div>
                            <div class="small text-muted">Capacidade: {{ $sala->capacidade_maxima }}</div>
                            <div class="small text-muted">{{ $sala->observacoes ?: 'Sem observações.' }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Nenhuma sala cadastrada para este local.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm mb-3">
                <div class="card-header">Processos Vinculados</div>
                <div class="card-body">
                    @forelse($local->processos as $vinculo)
                        <div class="border rounded p-3 mb-2 bg-light">
                            <div class="fw-semibold">{{ $vinculo->processo?->titulo }}</div>
                            <div class="small text-muted">Edital: {{ $vinculo->processo?->numero_edital }}</div>
                            <div class="small text-muted">Status: {{ str_replace('_', ' ', $vinculo->processo?->status) }}</div>
                            @if($vinculo->processo)
                                <a href="{{ route('sigeconcursos.processos.show', $vinculo->processo->id_processo) }}"
                                    class="btn btn-sm btn-outline-primary mt-2">
                                    Ver processo
                                </a>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-0">Este local ainda não está vinculado a nenhum processo.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection