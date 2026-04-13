@extends('layouts.main')

@section('title', 'Nova Conta Financeira')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-plus-circle"></i> Nova Conta Financeira</h4>
                        <a href="{{ route('financeiro.contas.index') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('financeiro.contas.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="tipo_conta" class="form-label fw-bold">Tipo da Conta</label>
                                <select name="tipo_conta" id="tipo_conta"
                                    class="form-select @error('tipo_conta') is-invalid @enderror" required>
                                    <option value="">Selecione</option>
                                    <option value="receita" {{ old('tipo_conta') === 'receita' ? 'selected' : '' }}>Receita
                                    </option>
                                    <option value="despesa" {{ old('tipo_conta') === 'despesa' ? 'selected' : '' }}>Despesa
                                    </option>
                                </select>
                                @error('tipo_conta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nome_conta" class="form-label fw-bold">Nome da Conta</label>
                                <input type="text" name="nome_conta" id="nome_conta"
                                    class="form-control @error('nome_conta') is-invalid @enderror"
                                    value="{{ old('nome_conta') }}" maxlength="150" required>
                                @error('nome_conta')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" role="switch" id="ativo" name="ativo"
                                    value="1" {{ old('ativo', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="ativo">Conta ativa</label>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('financeiro.contas.index') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Salvar Conta
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection