@extends('layouts.main')

@section('title', 'SIGE Concursos | Candidatos')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body pb-2 pt-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">
                    <i class="fa-solid fa-users-viewfinder me-2 text-primary"></i>
                    Lista de Candidatos
                </h4>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-primary btn-sm" id="copiar-link-cadastro">
                        <i class="fa-solid fa-link"></i> Copiar link para cadastro
                    </button>
                    <a href="{{ route('sigeconcursos.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left"></i> Voltar ao dashboard
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('sigeconcursos.candidatos.index') }}">
                <div class="row align-items-end g-2">
                    <div class="col-md-3">
                        <label for="nome" class="form-label mb-1">Filtrar por Nome</label>
                        <input type="text" class="form-control form-control-sm" id="nome" name="nome"
                            value="{{ request('nome') }}" placeholder="Nome completo">
                    </div>

                    <div class="col-md-3">
                        <label for="cpf" class="form-label mb-1">Filtrar por CPF</label>
                        <input type="text" class="form-control form-control-sm" id="cpf" name="cpf"
                            value="{{ request('cpf') }}" maxlength="14" placeholder="000.000.000-00">
                    </div>

                    <div class="col-md-3">
                        <label for="email" class="form-label mb-1">Filtrar por E-mail</label>
                        <input type="text" class="form-control form-control-sm" id="email" name="email"
                            value="{{ request('email') }}" placeholder="E-mail">
                    </div>

                    <div class="col-md-1">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="ordem_cadastro" name="ordem_cadastro"
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
                        <a href="{{ route('sigeconcursos.candidatos.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-eraser"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="mb-2" style="font-weight: bold; font-size: 1.1em;">
        Total de candidatos: {{ method_exists($candidatos, 'total') ? $candidatos->total() : $candidatos->count() }}
    </div>

    @if (method_exists($candidatos, 'links'))
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($candidatos->total() > 0)
                        Mostrando {{ $candidatos->firstItem() }}–{{ $candidatos->lastItem() }} de {{ $candidatos->total() }}
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
                {{ $candidatos->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <div style="max-height: 520px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Nome Completo</th>
                    <th>CPF</th>
                    <th>E-mail</th>
                    <th style="width: 220px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($candidatos as $candidato)
                    <tr>
                        <td>{{ $candidato->nome_completo }}</td>
                        <td>{{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $candidato->numero_cpf) }}</td>
                        <td>{{ $candidato->email }}</td>
                        <td>
                            <a href="{{ route('sigeconcursos.candidatos.show', $candidato->id_candidato) }}"
                                class="btn btn-sm btn-info">
                                Detalhes
                            </a>
                            <button type="button" class="btn btn-sm btn-danger btn-excluir-candidato"
                                data-id="{{ $candidato->id_candidato }}" data-nome="{{ $candidato->nome_completo }}">
                                Excluir
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">Nenhum candidato encontrado com os filtros
                            informados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (method_exists($candidatos, 'links'))
        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($candidatos->total() > 0)
                        Mostrando {{ $candidatos->firstItem() }}–{{ $candidatos->lastItem() }} de {{ $candidatos->total() }}
                    @else
                        Nenhum registro encontrado
                    @endif
                </span>
            </div>
            <div>
                {{ $candidatos->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <div class="modal fade" id="modal-excluir-candidato" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form method="POST" id="form-excluir-candidato">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p id="texto-excluir-candidato" class="mb-3"></p>
                        <label for="password_confirm" class="form-label">Senha do usuário logado</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        <small class="text-muted">Informe a senha do operador/admin atual para confirmar a exclusão.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir candidato</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const copyButton = document.getElementById('copiar-link-cadastro');
            const modalElement = document.getElementById('modal-excluir-candidato');
            const deleteForm = document.getElementById('form-excluir-candidato');
            const deleteText = document.getElementById('texto-excluir-candidato');
            const passwordField = document.getElementById('password_confirm');
            const modal = new bootstrap.Modal(modalElement);

            copyButton.addEventListener('click', async function () {
                const link = '{{ route('sigeconcursos.candidato.cadastro') }}';

                try {
                    await navigator.clipboard.writeText(link);
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('btn-success');
                    this.innerHTML = '<i class="fa-solid fa-check"></i> Link copiado';
                    setTimeout(() => {
                        this.classList.add('btn-outline-primary');
                        this.classList.remove('btn-success');
                        this.innerHTML = '<i class="fa-solid fa-link"></i> Copiar Link de Cadastro';
                    }, 2000);
                } catch (error) {
                    window.prompt('Copie o link de cadastro do candidato:', link);
                }
            });

            document.querySelectorAll('.btn-excluir-candidato').forEach(function (button) {
                button.addEventListener('click', function () {
                    const id = this.dataset.id;
                    const nome = this.dataset.nome;
                    deleteForm.action = `{{ url('sigeconcursos/candidatos') }}/${id}`;
                    deleteText.textContent = `Você está prestes a excluir o candidato ${nome}. Esta ação remove também o usuário de acesso vinculado.`;
                    passwordField.value = '';
                    modal.show();
                });
            });
        });

        function changePerPage(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
    </script>
@endsection