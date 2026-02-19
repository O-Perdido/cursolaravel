@extends('layouts.main')

@section('title', 'Configurações do Sistema')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-cog"></i> Configurações do Sistema</h4>
                        <a href="{{ route('landing') }}" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
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
                                <button class="nav-link active" id="remessa-tab" data-bs-toggle="tab"
                                    data-bs-target="#remessa" type="button" role="tab" aria-controls="remessa"
                                    aria-selected="true">
                                    <i class="fas fa-money-bill-wave"></i> Folhas de Pagamento
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="chamados-tab" data-bs-toggle="tab" data-bs-target="#chamados"
                                    type="button" role="tab" aria-controls="chamados" aria-selected="false">
                                    <i class="fas fa-headset"></i> Chamados
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="processos-tab" data-bs-toggle="tab" data-bs-target="#processos"
                                    type="button" role="tab" aria-controls="processos" aria-selected="false">
                                    <i class="fas fa-user-tie"></i> Processos Seletivos
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="{{ route('configuracoes.empresas') }}" target="_blank">
                                    <i class="fas fa-building"></i> Por Unidade Concedente
                                    <i class="fas fa-external-link-alt" style="font-size: 0.75em;"></i>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" href="{{ route('admin.tipos-chamados.index') }}" target="_blank">
                                    <i class="fas fa-tags"></i> Tipos de Chamados
                                    <i class="fas fa-external-link-alt" style="font-size: 0.75em;"></i>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content mt-3" id="configTabsContent">
                            <div class="tab-pane fade show active" id="remessa" role="tabpanel"
                                aria-labelledby="remessa-tab">
                                <form action="{{ route('configuracoes.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="aba" value="remessa">

                                    <div class="mb-4">
                                        <h5 class="text-secondary border-bottom pb-2 mb-3">
                                            <i class="fas fa-money-bill-wave"></i> Configurações de Folhas de Pagamento
                                        </h5>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="limite_diario_remessa" class="form-label fw-bold">
                                                        Limite para Remessas (R$) <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number" name="limite_diario_remessa"
                                                        id="limite_diario_remessa"
                                                        class="form-control @error('limite_diario_remessa') is-invalid @enderror"
                                                        step="0.01" min="0"
                                                        value="{{ old('limite_diario_remessa', \App\Models\Configuracao::obterLimiteDiarioRemessa()) }}"
                                                        required>
                                                    @error('limite_diario_remessa')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">
                                                        <i class="fas fa-info-circle"></i> Este valor será usado para
                                                        dividir
                                                        automaticamente as folhas de pagamento em múltiplos arquivos de
                                                        remessa
                                                        quando o total ultrapassar este limite.
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="dias_padrao_calculo_folha" class="form-label fw-bold">
                                                        <i class="fas fa-calculator"></i> Dias Padrão para Cálculo de Folha
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="number" name="dias_padrao_calculo_folha"
                                                        id="dias_padrao_calculo_folha"
                                                        class="form-control @error('dias_padrao_calculo_folha') is-invalid @enderror"
                                                        value="{{ old('dias_padrao_calculo_folha', \App\Models\Configuracao::obter('dias_padrao_calculo_folha', 30)) }}"
                                                        min="1" max="31" required>
                                                    @error('dias_padrao_calculo_folha')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <div class="form-text">
                                                        <i class="fas fa-info-circle"></i>
                                                        Número de dias usado como base para cálculo proporcional de bolsa e
                                                        auxílio transporte.
                                                        Padrão: 30 dias. Ajuste conforme a política da empresa.
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
                                    <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Informações Importantes
                                    </h6>
                                    <ul class="mb-0 small">
                                        <li>Apenas administradores podem alterar estas configurações</li>
                                        <li>As mudanças entram em vigor imediatamente</li>
                                        <li>O sistema dividirá automaticamente folhas grandes em múltiplos arquivos</li>
                                        <li>Cada lote respeitará o limite configurado para garantir o processamento bancário
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="chamados" role="tabpanel" aria-labelledby="chamados-tab">
                                <form action="{{ route('configuracoes.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="aba" value="chamados">
                                    <h5 class="text-secondary border-bottom pb-2 mb-3">
                                        <i class="fas fa-tools"></i> Configurações de Chamados
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="chamados_max_anexos" class="form-label fw-bold">Máx. Anexos por
                                                    Chamado</label>
                                                <input type="number" name="chamados_max_anexos" id="chamados_max_anexos"
                                                    min="0" class="form-control"
                                                    value="{{ old('chamados_max_anexos', \App\Models\Configuracao::obter('chamados_max_anexos', 5)) }}">
                                                <div class="form-text">Define quantos arquivos podem ser anexados em um
                                                    chamado.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="chamados_max_tamanho_anexo_mb"
                                                    class="form-label fw-bold">Tamanho Máx. por Anexo (MB)</label>
                                                <input type="number" step="0.01" min="0"
                                                    name="chamados_max_tamanho_anexo_mb" id="chamados_max_tamanho_anexo_mb"
                                                    class="form-control"
                                                    value="{{ old('chamados_max_tamanho_anexo_mb', \App\Models\Configuracao::obter('chamados_max_tamanho_anexo_mb', 10)) }}">
                                                <div class="form-text">Limite de tamanho para cada arquivo anexado.</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check mt-4">
                                                <input class="form-check-input" type="checkbox" value="1"
                                                    id="chamados_permitir_outros_empresa"
                                                    name="chamados_permitir_outros_empresa" {{ \App\Models\Configuracao::obter('chamados_permitir_outros_empresa', true) ? 'checked' : '' }}>
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


                            </div>

                            <div class="tab-pane fade" id="processos" role="tabpanel" aria-labelledby="processos-tab">
                                <form action="{{ route('configuracoes.update') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="aba" value="processos">
                                    <h5 class="text-secondary border-bottom pb-2 mb-3">
                                        <i class="fas fa-building"></i> Permissões para Unidades Concedentes
                                    </h5>
                                    <p class="text-muted mb-4">
                                        <i class="fas fa-info-circle"></i> Configure o que as unidades concedentes
                                        (empresas) podem acessar nos processos seletivos.
                                    </p>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="fw-bold mb-3">
                                                        <i class="fas fa-users text-primary"></i> Visualização de Inscritos
                                                    </h6>

                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" value="1"
                                                            id="processos_empresa_pode_ver_inscritos"
                                                            name="processos_empresa_pode_ver_inscritos" {{ \App\Models\Configuracao::obter('processos_empresa_pode_ver_inscritos', true) ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="processos_empresa_pode_ver_inscritos">
                                                            <strong>Permitir que unidades concedentes visualizem os
                                                                inscritos</strong>
                                                        </label>
                                                        <div class="form-text">
                                                            Se ativo, as empresas poderão acessar a lista de inscritos nos
                                                            processos seletivos vinculados a ela.
                                                        </div>
                                                    </div>

                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" value="1"
                                                            id="processos_empresa_apenas_deferidos"
                                                            name="processos_empresa_apenas_deferidos" {{ \App\Models\Configuracao::obter('processos_empresa_apenas_deferidos', false) ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="processos_empresa_apenas_deferidos">
                                                            <strong>Restringir visualização apenas para inscritos
                                                                DEFERIDOS</strong>
                                                        </label>
                                                        <div class="form-text">
                                                            Se ativo, as empresas verão apenas candidatos com status
                                                            "Deferido".
                                                            Se inativo, verão todos os inscritos independente do status.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="card mb-3">
                                                <div class="card-body">
                                                    <h6 class="fw-bold mb-3">
                                                        <i class="fas fa-file-export text-success"></i> Exportação de
                                                        Relatórios
                                                    </h6>

                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" value="1"
                                                            id="processos_empresa_pode_exportar"
                                                            name="processos_empresa_pode_exportar" {{ \App\Models\Configuracao::obter('processos_empresa_pode_exportar', true) ? 'checked' : '' }}>
                                                        <label class="form-check-label"
                                                            for="processos_empresa_pode_exportar">
                                                            <strong>Permitir que empresas exportem relatórios
                                                                (PDF/Excel)</strong>
                                                        </label>
                                                        <div class="form-text">
                                                            Se ativo, as unidades concedentes poderão exportar a lista de
                                                            inscritos em formato PDF ou Excel.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="alert alert-warning">
                                                <h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i>
                                                    Importante</h6>
                                                <ul class="mb-0 small">
                                                    <li>Unidades concedentes <strong>nunca</strong> poderão alterar o status
                                                        dos inscritos (apenas Admin/Operador)</li>
                                                    <li>As empresas visualizarão apenas os processos vinculados a elas</li>
                                                    <li>Botões de ação (Deferir/Indeferir) serão substituídos por badges de
                                                        status para empresas</li>
                                                    <li>Alterações nessas configurações entram em vigor imediatamente</li>
                                                </ul>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection