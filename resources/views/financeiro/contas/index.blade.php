@extends('layouts.main')

@section('title', 'Contas Financeiras')

@section('content')
<div class="container mt-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-sack-dollar"></i> Contas Financeiras</h4>
            <a href="{{ route('financeiro.index') }}" class="btn btn-sm btn-light">
                <i class="fas fa-arrow-left"></i> Financeiro
            </a>
        </div>
        <div class="card-body">
            <p class="text-muted mb-0">
                As contas cadastradas aqui serao reutilizadas nos lancamentos mensais e no consolidado anual.
            </p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <strong><i class="fas fa-arrow-trend-up me-2"></i>Receitas</strong>
                    <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#modalNovaConta" onclick="definirTipo('receita')">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="card-body">
                    @if($contasReceita->isEmpty())
                        <div class="alert alert-info mb-0">Nenhuma conta de receita cadastrada.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Conta</th>
                                        <th>Status</th>
                                        <th class="text-center">Acoes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contasReceita as $conta)
                                        <tr>
                                            <td>{{ $conta->nome_conta }}</td>
                                            <td>
                                                <span class="badge {{ $conta->ativo ? 'bg-primary' : 'bg-secondary' }}">
                                                    {{ $conta->ativo ? 'Ativa' : 'Inativa' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('financeiro.contas.edit', $conta->id_financeiro_conta) }}" class="btn btn-sm btn-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('financeiro.contas.destroy', $conta->id_financeiro_conta) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta conta?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <strong><i class="fas fa-arrow-trend-down me-2"></i>Despesas</strong>
                    <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#modalNovaConta" onclick="definirTipo('despesa')">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="card-body">
                    @if($contasDespesa->isEmpty())
                        <div class="alert alert-info mb-0">Nenhuma conta de despesa cadastrada.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Conta</th>
                                        <th>Status</th>
                                        <th class="text-center">Acoes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contasDespesa as $conta)
                                        <tr>
                                            <td>{{ $conta->nome_conta }}</td>
                                            <td>
                                                <span class="badge {{ $conta->ativo ? 'bg-primary' : 'bg-secondary' }}">
                                                    {{ $conta->ativo ? 'Ativa' : 'Inativa' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('financeiro.contas.edit', $conta->id_financeiro_conta) }}" class="btn btn-sm btn-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('financeiro.contas.destroy', $conta->id_financeiro_conta) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta conta?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNovaConta" tabindex="-1" aria-labelledby="modalNovaContaLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('financeiro.contas.store') }}">
                @csrf
                <div class="modal-header" id="modalNovaContaHeader">
                    <h5 class="modal-title" id="modalNovaContaLabel">Nova Conta</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="tipo_conta" id="modal_tipo_conta">
                    <div class="mb-3">
                        <label for="modal_nome_conta" class="form-label fw-bold">Nome da Conta</label>
                        <input type="text" name="nome_conta" id="modal_nome_conta" class="form-control" maxlength="150" required autofocus placeholder="Ex.: CONTRATO PREF JOICABA">
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="modal_ativo" name="ativo" value="1" checked>
                        <label class="form-check-label" for="modal_ativo">Conta ativa</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const cores = { receita: '#198754', despesa: '#dc3545' };
    const labels = { receita: 'Nova Receita', despesa: 'Nova Despesa' };
    function definirTipo(tipo) {
        document.getElementById('modal_tipo_conta').value = tipo;
        document.getElementById('modalNovaContaLabel').textContent = labels[tipo];
        document.getElementById('modalNovaContaHeader').style.backgroundColor = cores[tipo];
        document.getElementById('modalNovaContaHeader').style.color = '#fff';
        document.getElementById('modal_nome_conta').value = '';
        document.getElementById('modal_ativo').checked = true;
    }
</script>
@endsection
