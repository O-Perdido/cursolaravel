@extends('layouts.main')

@section('title', 'Configurações do Sistema')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-cog"></i> Configurações do Sistema</h4>
                    </div>
                    <div class="card-body">
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

                        <ul class="nav nav-tabs" id="configTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="remessa-tab" data-bs-toggle="tab" data-bs-target="#remessa" type="button" role="tab" aria-controls="remessa" aria-selected="true">
                                    <i class="fas fa-money-bill-wave"></i> Remessa Bancária
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="chamados-tab" data-bs-toggle="tab" data-bs-target="#chamados" type="button" role="tab" aria-controls="chamados" aria-selected="false">
                                    <i class="fas fa-headset"></i> Chamados
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content mt-3" id="configTabsContent">
                            <div class="tab-pane fade show active" id="remessa" role="tabpanel" aria-labelledby="remessa-tab">
                                <form action="{{ route('configuracoes.update') }}" method="POST">
                                    @csrf

                                    <div class="mb-4">
                                        <h5 class="text-secondary border-bottom pb-2 mb-3">
                                            <i class="fas fa-money-bill-wave"></i> Configurações de Remessa Bancária
                                        </h5>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="limite_diario_remessa" class="form-label fw-bold">
                                                        Limite Diário para Remessas (R$) <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number" name="limite_diario_remessa" id="limite_diario_remessa"
                                                        class="form-control @error('limite_diario_remessa') is-invalid @enderror"
                                                        step="0.01" min="0"
                                                        value="{{ old('limite_diario_remessa', \App\Models\Configuracao::obterLimiteDiarioRemessa()) }}"
                                                        required>
                                                    @error('limite_diario_remessa')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">
                                                        <i class="fas fa-info-circle"></i> Este valor será usado para dividir
                                                        automaticamente as folhas de pagamento em múltiplos arquivos de remessa
                                                        quando o total ultrapassar este limite.
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('welcome') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Voltar
                                        </a>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Salvar Configurações
                                        </button>
                                    </div>
                                </form>

                                <hr class="my-4">

                                <div class="alert alert-info mb-0">
                                    <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Informações Importantes</h6>
                                    <ul class="mb-0 small">
                                        <li>Apenas administradores podem alterar estas configurações</li>
                                        <li>As mudanças entram em vigor imediatamente</li>
                                        <li>O sistema dividirá automaticamente folhas grandes em múltiplos arquivos</li>
                                        <li>Cada lote respeitará o limite configurado para garantir o processamento bancário</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="chamados" role="tabpanel" aria-labelledby="chamados-tab">
                                <form action="{{ route('configuracoes.update') }}" method="POST">
                                    @csrf
                                    <h5 class="text-secondary border-bottom pb-2 mb-3">
                                        <i class="fas fa-tools"></i> Configurações de Chamados
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="chamados_max_anexos" class="form-label fw-bold">Máx. Anexos por Chamado</label>
                                                <input type="number" name="chamados_max_anexos" id="chamados_max_anexos" min="0" class="form-control"
                                                    value="{{ old('chamados_max_anexos', \App\Models\Configuracao::obter('chamados_max_anexos', 5)) }}">
                                                <div class="form-text">Define quantos arquivos podem ser anexados em um chamado.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="chamados_max_tamanho_anexo_mb" class="form-label fw-bold">Tamanho Máx. por Anexo (MB)</label>
                                                <input type="number" step="0.01" min="0" name="chamados_max_tamanho_anexo_mb" id="chamados_max_tamanho_anexo_mb" class="form-control"
                                                    value="{{ old('chamados_max_tamanho_anexo_mb', \App\Models\Configuracao::obter('chamados_max_tamanho_anexo_mb', 10)) }}">
                                                <div class="form-text">Limite de tamanho para cada arquivo anexado.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check mt-4">
                                                <input class="form-check-input" type="checkbox" value="1" id="chamados_permitir_outros_empresa" name="chamados_permitir_outros_empresa"
                                                    {{ \App\Models\Configuracao::obter('chamados_permitir_outros_empresa', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="chamados_permitir_outros_empresa">
                                                    Permitir empresas abrirem chamados genéricos (Outros)
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Salvar Configurações de Chamados
                                        </button>
                                    </div>
                                </form>

                                <div class="card shadow-sm mt-4">
                                    <div class="card-body pb-2 pt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="mb-0">
                                                <i class="fas fa-tags me-2 text-primary"></i>
                                                Tipos de Chamados
                                            </h5>
                                            <a href="{{ route('admin.tipos-chamados.index') }}" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-external-link-alt"></i> Gerenciar na Página Completa
                                            </a>
                                        </div>
                                        <form method="POST" action="{{ route('admin.tipos-chamados.store') }}" class="row g-2">
                                            @csrf
                                            <div class="col-md-4">
                                                <input type="text" name="nome" class="form-control" placeholder="Novo tipo (ex.: Outros)" required>
                                            </div>
                                            <div class="col-md-5">
                                                <input type="text" name="descricao" class="form-control" placeholder="Descrição (opcional)">
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" name="ordem" class="form-control" placeholder="Ordem" min="0" value="99">
                                            </div>
                                            <div class="col-md-1 d-grid">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="card shadow-sm mt-3">
                                    <div class="card-body">
                                        @if(isset($tipos) && $tipos->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Nome</th>
                                                            <th>Descrição</th>
                                                            <th>Status</th>
                                                            <th>Tipo</th>
                                                            <th>Ordem</th>
                                                            <th class="text-center">Ações</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($tipos as $tipo)
                                                            <tr>
                                                                <td><strong>{{ $tipo->nome }}</strong></td>
                                                                <td>{{ Str::limit($tipo->descricao, 60) }}</td>
                                                                <td>
                                                                    @if($tipo->ativo)
                                                                        <span class="badge bg-success">Ativo</span>
                                                                    @else
                                                                        <span class="badge bg-secondary">Inativo</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if($tipo->sistema)
                                                                        <span class="badge bg-warning text-dark">Sistema</span>
                                                                    @else
                                                                        <span class="badge bg-info">Personalizado</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $tipo->ordem }}</td>
                                                                <td class="text-center">
                                                                    <a href="{{ route('admin.tipos-chamados.edit', $tipo->id_tipo_chamado) }}" class="btn btn-sm btn-primary" title="Editar">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>

                                                                    <!-- Toggle Ativo/Inativo (via update, preservando campos) -->
                                                                    <form action="{{ route('admin.tipos-chamados.update', $tipo->id_tipo_chamado) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <input type="hidden" name="nome" value="{{ $tipo->nome }}">
                                                                        <input type="hidden" name="descricao" value="{{ $tipo->descricao }}">
                                                                        <input type="hidden" name="ordem" value="{{ $tipo->ordem }}">
                                                                        <input type="hidden" name="ativo" value="{{ $tipo->ativo ? 0 : 1 }}">
                                                                        <button type="submit" class="btn btn-sm {{ $tipo->ativo ? 'btn-warning' : 'btn-success' }}" title="{{ $tipo->ativo ? 'Desativar' : 'Ativar' }}">
                                                                            <i class="fas {{ $tipo->ativo ? 'fa-ban' : 'fa-check' }}"></i>
                                                                        </button>
                                                                    </form>

                                                                    @if(!$tipo->sistema)
                                                                        <form action="{{ route('admin.tipos-chamados.destroy', $tipo->id_tipo_chamado) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja remover este tipo de chamado?');">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-sm btn-danger" title="Remover">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <div class="alert alert-info text-center">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Nenhum tipo de chamado cadastrado.
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection