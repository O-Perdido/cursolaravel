@extends('layouts.main')

@section('title', 'Editar Supervisor')

@section('content')
@php($isEmpresa = (Auth::user()->nivel ?? '') === 'empresa')
<h1>Editar Supervisor</h1>
<div class="d-flex justify-content-between mb-3">
    <a href="{{ route($isEmpresa ? 'empresa.supervisores.index' : 'supervisores.index') }}"
        class="btn btn-secondary">Voltar</a>
</div>
<form
    action="{{ route($isEmpresa ? 'empresa.supervisores.update' : 'supervisores.update', $supervisor->id_supervisor) }}"
    method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <!-- Dados do Supervisor -->
        <div class="col-md-6">
            <div class="form-group mb-2">
                <label for="nome_supervisor">Nome</label>
                <input type="text" class="form-control" id="nome_supervisor" name="nome_supervisor"
                    value="{{ $supervisor->nome_supervisor }}" required>
            </div>
            @php($nivel = Auth::user()->nivel ?? '')
            @if($nivel !== 'empresa')
                <div class="form-group mb-2">
                    <label for="fk_id_empresa">Empresa</label>
                    <select class="form-control" id="fk_id_empresa" name="fk_id_empresa" required>
                        <option value="">Selecione a empresa</option>
                        @foreach($empresas as $empresa)
                            <option value="{{ $empresa->id_empresa }}" {{ $empresa->id_empresa == $supervisor->fk_id_empresa ? 'selected' : '' }}>
                                {{ $empresa->nome_empresa }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="form-group mb-2">
                <label for="cpf_supervisor">CPF</label>
                <input type="text" class="form-control @error('cpf_supervisor') is-invalid @enderror"
                    id="cpf_supervisor" name="cpf_supervisor"
                    value="{{ old('cpf_supervisor', $supervisor->cpf_supervisor) }}" required>
                @error('cpf_supervisor')
                    <div class="invalid-feedback" style="display:block;">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group mb-2">
                <label for="celular_supervisor">Número de Celular</label>
                <input type="tel" class="form-control" id="celular_supervisor" maxlength="15" name="celular_supervisor"
                    value="{{ old('celular_supervisor', $supervisor->celular_supervisor) }}"
                    placeholder="(DD) 90000-0000">
            </div>
            <div class="form-group mb-2">
                <label for="email_supervisor">E-mail</label>
                <input type="email" class="form-control" id="email_supervisor" name="email_supervisor"
                    value="{{ old('email_supervisor', $supervisor->email_supervisor) }}"
                    placeholder="email@exemplo.com">
            </div>
        </div>
        <!-- Formação e Experiência -->
        <div class="col-md-6">
            <div class="form-group mb-2">
                <label for="area_formacao">Área de Formação</label>
                <input type="text" class="form-control" id="area_formacao" name="area_formacao"
                    value="{{ $supervisor->area_formacao }}">
            </div>
            <div class="form-group mb-2">
                <label for="tempo_experiencia">Tempo de Experiência</label>
                <input type="text" class="form-control" id="tempo_experiencia" name="tempo_experiencia"
                    value="{{ $supervisor->tempo_experiencia }}">
            </div>
        </div>
    </div>
    <div class="form-group mt-3 text-end">
        <button type="submit" class="btn btn-primary" id="save_button">Salvar</button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const cpfInput = document.getElementById('cpf_supervisor');
        const cpfError = document.getElementById('cpf_error');
        const saveButton = document.getElementById('save_button');
        const celularInput = document.getElementById('celular_supervisor');

        function mascaraCelular(value) {
            value = value.replace(/\D/g, '');
            if (value.length <= 10) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            } else {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
            }
            return value;
        }

        // Aplicar máscara inicial caso já tenha valor
        if (celularInput.value) {
            celularInput.value = mascaraCelular(celularInput.value);
        }

        celularInput.addEventListener('input', function () {
            this.value = mascaraCelular(this.value);
        });

        cpfInput.addEventListener('input', function () {
            if (isValidCPF(cpfInput.value)) {
                cpfInput.classList.remove('is-invalid');
                cpfInput.classList.add('is-valid');
                cpfError.classList.add('d-none');
                saveButton.disabled = false;
            } else {
                cpfInput.classList.remove('is-valid');
                cpfInput.classList.add('is-invalid');
                cpfError.classList.remove('d-none');
                saveButton.disabled = true;
            }
        });

        function isValidCPF(cpf) {
            cpf = cpf.replace(/[^\d]+/g, '');
            if (cpf === '') return false;
            if (cpf.length !== 11) return false;
            if (/^(\d)\1{10}$/.test(cpf)) return false;

            let sum;
            let remainder;
            sum = 0;
            for (let i = 1; i <= 9; i++) sum += parseInt(cpf.substring(i - 1, i)) * (11 - i);
            remainder = (sum * 10) % 11;
            if ((remainder === 10) || (remainder === 11)) remainder = 0;
            if (remainder !== parseInt(cpf.substring(9, 10))) return false;

            sum = 0;
            for (let i = 1; i <= 10; i++) sum += parseInt(cpf.substring(i - 1, i)) * (12 - i);
            remainder = (sum * 10) % 11;
            if ((remainder === 10) || (remainder === 11)) remainder = 0;
            if (remainder !== parseInt(cpf.substring(10, 11))) return false;
            return true;
        }
    });
</script>
@endsection