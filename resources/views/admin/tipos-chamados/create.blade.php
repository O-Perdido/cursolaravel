@extends('layouts.main')

@section('title', 'Adicionar Tipo de Chamado')

@section('content')

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-plus me-2"></i>Adicionar Tipo de Chamado
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.tipos-chamados.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="nome" class="form-label">
                        Nome <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome"
                        value="{{ old('nome') }}" required>
                    @error('nome')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control @error('descricao') is-invalid @enderror" id="descricao" name="descricao"
                        rows="3">{{ old('descricao') }}</textarea>
                    <small class="form-text text-muted">
                        Descrição opcional para ajudar os usuários a entender quando usar este tipo de chamado.
                    </small>
                    @error('descricao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="ordem" class="form-label">Ordem de Exibição</label>
                    <input type="number" class="form-control @error('ordem') is-invalid @enderror" id="ordem" name="ordem"
                        value="{{ old('ordem', 99) }}" min="0">
                    <small class="form-text text-muted">
                        Define a ordem em que este tipo aparecerá na lista (menor = primeiro).
                    </small>
                    @error('ordem')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Importante:</strong> Este tipo de chamado usará o formulário genérico com campos: Título,
                    Detalhes e Anexos.
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button onclick="window.NavigationHistory?.goBack('{{ route('admin.tipos-chamados.index') }}')"
                        class="btn btn-secondary" title="Voltar para a página anterior com filtros preservados">
                        <i class="fas fa-arrow-left me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection