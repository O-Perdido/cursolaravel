@php
    $cargo = $cargo ?? null;
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Revise os campos abaixo.</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="card shadow-sm mb-3">
        <div class="card-header">Dados do Cargo</div>
        <div class="card-body">
            <div class="mb-3">
                <label for="nome_cargo" class="form-label">Nome do Cargo</label>
                <input type="text" class="form-control @error('nome_cargo') is-invalid @enderror" id="nome_cargo"
                    name="nome_cargo" value="{{ old('nome_cargo', $cargo?->nome_cargo) }}" required>
            </div>

            <div class="mb-3">
                <label for="escolaridade_minima" class="form-label">Escolaridade Mínima</label>
                <input type="text" class="form-control @error('escolaridade_minima') is-invalid @enderror"
                    id="escolaridade_minima" name="escolaridade_minima"
                    value="{{ old('escolaridade_minima', $cargo?->escolaridade_minima) }}"
                    placeholder="Ex: Ensino médio completo">
            </div>

            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea class="form-control @error('descricao') is-invalid @enderror" id="descricao" name="descricao"
                    rows="4" placeholder="Descrição resumida do cargo">{{ old('descricao', $cargo?->descricao) }}</textarea>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="ativo" name="ativo" value="1"
                    {{ old('ativo', $cargo?->ativo ?? true) ? 'checked' : '' }}>
                <label class="form-check-label" for="ativo">Cargo ativo</label>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('sigeconcursos.cargos.index') }}" class="btn btn-outline-secondary">Voltar</a>
        <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
    </div>
</form>