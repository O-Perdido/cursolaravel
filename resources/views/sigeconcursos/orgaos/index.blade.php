@extends('layouts.main')

@section('title', 'SIGE Concursos | Órgãos e Empresas')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-3 shadow-sm">
        <div class="card-body pb-2 pt-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    <i class="fa-solid fa-building-columns me-2 text-primary"></i>
                    Órgãos Públicos e Empresas
                </h4>
                <div>
                    <a href="{{ route('sigeconcursos.orgaos.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Novo Cadastro
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('sigeconcursos.orgaos.index') }}">
                <div class="row align-items-end g-2">
                    <div class="col-md-3">
                        <label for="nome_razao_social" class="form-label mb-1">Filtrar por Nome/Razão Social</label>
                        <input type="text" name="nome_razao_social" id="nome_razao_social"
                            class="form-control form-control-sm" value="{{ request('nome_razao_social') }}"
                            placeholder="Nome ou razão social">
                    </div>

                    <div class="col-md-3">
                        <label for="cnpj" class="form-label mb-1">Filtrar por CNPJ</label>
                        <input type="text" name="cnpj" id="cnpj" class="form-control form-control-sm"
                            value="{{ request('cnpj') }}" placeholder="00.000.000/0000-00">
                    </div>

                    <div class="col-md-3">
                        <label for="email" class="form-label mb-1">Filtrar por E-mail</label>
                        <input type="text" name="email" id="email" class="form-control form-control-sm"
                            value="{{ request('email') }}" placeholder="E-mail">
                    </div>

                    <div class="col-md-1">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="ordem_cadastro" id="ordem_cadastro"
                                value="1" {{ request('ordem_cadastro') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="ordem_cadastro">Recentes</label>
                        </div>
                    </div>

                    <div class="col-md-1 d-grid">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>

                    <div class="col-md-1 d-grid">
                        <a href="{{ route('sigeconcursos.orgaos.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-eraser"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="mb-2" style="font-weight: bold; font-size: 1.1em;">
        Total de cadastros: {{ method_exists($orgaos, 'total') ? $orgaos->total() : $orgaos->count() }}
    </div>

    @if (method_exists($orgaos, 'links'))
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($orgaos->total() > 0)
                        Mostrando {{ $orgaos->firstItem() }}–{{ $orgaos->lastItem() }} de {{ $orgaos->total() }}
                    @else
                        Nenhum registro encontrado
                    @endif
                </span>
                @php $pp = request('per_page', '25'); @endphp
                <select class="form-select form-select-sm" onchange="changePerPage(this.value)"
                    style="display: inline-block; width: auto; margin-left: 10px; font-size: 0.875rem;">
                    <option value="25" {{ $pp == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $pp == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $pp == '100' ? 'selected' : '' }}>100</option>
                    <option value="200" {{ $pp == '200' ? 'selected' : '' }}>200</option>
                    <option value="all" {{ $pp == 'all' ? 'selected' : '' }}>Tudo</option>
                </select>
            </div>
            <div>
                {{ $orgaos->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <div style="max-height: 520px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Nome/Razão Social</th>
                    <th>CNPJ</th>
                    <th>E-mail</th>
                    <th style="width: 250px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orgaos as $orgao)
                    <tr>
                        <td>{{ $orgao->nome_razao_social }}</td>
                        <td>
                            {{ preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', '$1.$2.$3/$4-$5', preg_replace('/\D/', '', $orgao->numero_cnpj)) }}
                        </td>
                        <td>{{ $orgao->email }}</td>
                        <td>
                            <a href="{{ route('sigeconcursos.orgaos.show', $orgao->id_empresa) }}"
                                class="btn btn-sm btn-info">Detalhes</a>
                            <a href="{{ route('sigeconcursos.orgaos.edit', $orgao->id_empresa) }}"
                                class="btn btn-sm btn-primary">Editar</a>
                            <form action="{{ route('sigeconcursos.orgaos.destroy', $orgao->id_empresa) }}" method="POST"
                                style="display:inline-block" onsubmit="return confirm('Confirma a exclusão deste cadastro?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            Nenhum órgão público/empresa cadastrado até o momento.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (method_exists($orgaos, 'links'))
        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($orgaos->total() > 0)
                        Mostrando {{ $orgaos->firstItem() }}–{{ $orgaos->lastItem() }} de {{ $orgaos->total() }}
                    @else
                        Nenhum registro encontrado
                    @endif
                </span>
            </div>
            <div>
                {{ $orgaos->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <script>
        function changePerPage(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }

        const cnpjInput = document.getElementById('cnpj');
        if (cnpjInput) {
            cnpjInput.addEventListener('input', function () {
                let value = this.value.replace(/\D/g, '').slice(0, 14);
                value = value.replace(/^(\d{2})(\d)/, '$1.$2');
                value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
                this.value = value;
            });
        }
    </script>
@endsection