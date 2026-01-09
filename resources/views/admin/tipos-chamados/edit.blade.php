@extends('layouts.main')

@section('title', 'Editar Tipo de Chamado')

@section('content')

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-edit me-2"></i>Editar Tipo de Chamado
        </h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.tipos-chamados.update', $tipo->id_tipo_chamado) }}">
            @csrf
            @method('PUT')

            @if($tipo->sistema)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Atenção:</strong> Este é um tipo de chamado do sistema. 
                    Você pode editar alguns campos, mas não pode removê-lo.
                </div>
            @endif

            <div class="mb-3">
                <label for="nome" class="form-label">
                    Nome <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control @error('nome') is-invalid @enderror" 
                       id="nome" name="nome" value="{{ old('nome', $tipo->nome) }}" required>
                @error('nome')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea class="form-control @error('descricao') is-invalid @enderror" 
                          id="descricao" name="descricao" rows="3">{{ old('descricao', $tipo->descricao) }}</textarea>
                @error('descricao')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="ordem" class="form-label">Ordem de Exibição</label>
                <input type="number" class="form-control @error('ordem') is-invalid @enderror" 
                       id="ordem" name="ordem" value="{{ old('ordem', $tipo->ordem) }}" min="0">
                @error('ordem')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="ativo" id="ativo1" 
                           value="1" {{ old('ativo', $tipo->ativo) == 1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="ativo1">
                        Ativo
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="ativo" id="ativo0" 
                           value="0" {{ old('ativo', $tipo->ativo) == 0 ? 'checked' : '' }}>
                    <label class="form-check-label" for="ativo0">
                        Inativo
                    </label>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button onclick="window.NavigationHistory?.goBack('{{ route('admin.tipos-chamados.index') }}')" class="btn btn-secondary" title="Voltar para a página anterior com filtros preservados">
                    <i class="fas fa-arrow-left me-2"></i>Cancelar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Atualizar
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
