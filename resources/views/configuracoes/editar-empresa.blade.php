@extends('layouts.main')

@section('title', 'Configurar ' . $empresa->nome_empresa)

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0">
                                <i class="fas fa-building"></i> {{ $empresa->nome_empresa }}
                            </h4>
                            <small class="text-light">Configurações de Processos Seletivos</small>
                        </div>
                        <a href="{{ route('configuracoes.empresas') }}" class="btn btn-sm btn-light">
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

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-lightbulb"></i>
                            <strong>Dica:</strong> Selecione "Usar Global" para aplicar a configuração padrão do sistema.
                            Escolha "Sim" ou "Não" para sobrescrever a configuração global apenas para esta unidade.
                        </div>

                        <form action="{{ route('configuracoes.atualizar-empresa', $empresa->id_empresa) }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="card border-0 bg-light mb-4">
                                        <div class="card-body">
                                            <h5 class="card-title mb-4">
                                                <i class="fas fa-user-tie"></i> Permissões de Processos Seletivos
                                            </h5>

                                            <!-- Config 1: Poder ver inscritos -->
                                            <div class="mb-4 pb-3 border-bottom">
                                                <label class="form-label fw-bold">
                                                    <i class="fas fa-eye"></i> Visualizar Inscritos
                                                </label>
                                                <p class="text-muted small mb-3">
                                                    Permite que a unidade concedente veja a lista de candidatos inscritos em seus processos seletivos.
                                                </p>
                                                <div class="btn-group w-100" role="group">
                                                    <input type="radio" class="btn-check" name="processos_empresa_pode_ver_inscritos"
                                                           id="poder_ver_global" value="global"
                                                           {{ $configsProcessos['processos_empresa_pode_ver_inscritos']['empresa'] === null ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-secondary" for="poder_ver_global">
                                                        <i class="fas fa-link"></i> Usar Global
                                                        <br><small>({{ $configsProcessos['processos_empresa_pode_ver_inscritos']['global'] ? '✓ Sim' : '✗ Não' }})</small>
                                                    </label>

                                                    <input type="radio" class="btn-check" name="processos_empresa_pode_ver_inscritos"
                                                           id="poder_ver_sim" value="sim"
                                                           {{ $configsProcessos['processos_empresa_pode_ver_inscritos']['empresa'] === true ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-success" for="poder_ver_sim">
                                                        <i class="fas fa-check"></i> Permitir
                                                    </label>

                                                    <input type="radio" class="btn-check" name="processos_empresa_pode_ver_inscritos"
                                                           id="poder_ver_nao" value="nao"
                                                           {{ $configsProcessos['processos_empresa_pode_ver_inscritos']['empresa'] === false ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-danger" for="poder_ver_nao">
                                                        <i class="fas fa-times"></i> Negar
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Config 2: Apenas deferidos -->
                                            <div class="mb-4 pb-3 border-bottom">
                                                <label class="form-label fw-bold">
                                                    <i class="fas fa-filter"></i> Restringir a Inscritos Deferidos
                                                </label>
                                                <p class="text-muted small mb-3">
                                                    Se ativado, a unidade concedente só poderá ver os candidatos que foram DEFERIDOS no processo.
                                                    Requer que a opção anterior (Visualizar Inscritos) esteja ativa.
                                                </p>
                                                <div class="btn-group w-100" role="group">
                                                    <input type="radio" class="btn-check" name="processos_empresa_apenas_deferidos"
                                                           id="deferidos_global" value="global"
                                                           {{ $configsProcessos['processos_empresa_apenas_deferidos']['empresa'] === null ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-secondary" for="deferidos_global">
                                                        <i class="fas fa-link"></i> Usar Global
                                                        <br><small>({{ $configsProcessos['processos_empresa_apenas_deferidos']['global'] ? '✓ Sim' : '✗ Não' }})</small>
                                                    </label>

                                                    <input type="radio" class="btn-check" name="processos_empresa_apenas_deferidos"
                                                           id="deferidos_sim" value="sim"
                                                           {{ $configsProcessos['processos_empresa_apenas_deferidos']['empresa'] === true ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-success" for="deferidos_sim">
                                                        <i class="fas fa-check"></i> Ativar
                                                    </label>

                                                    <input type="radio" class="btn-check" name="processos_empresa_apenas_deferidos"
                                                           id="deferidos_nao" value="nao"
                                                           {{ $configsProcessos['processos_empresa_apenas_deferidos']['empresa'] === false ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-danger" for="deferidos_nao">
                                                        <i class="fas fa-times"></i> Desativar
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Config 3: Poder exportar -->
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">
                                                    <i class="fas fa-download"></i> Exportar Relatórios
                                                </label>
                                                <p class="text-muted small mb-3">
                                                    Permite que a unidade concedente exporte relatórios de inscritos em formatos como PDF ou Excel.
                                                </p>
                                                <div class="btn-group w-100" role="group">
                                                    <input type="radio" class="btn-check" name="processos_empresa_pode_exportar"
                                                           id="exportar_global" value="global"
                                                           {{ $configsProcessos['processos_empresa_pode_exportar']['empresa'] === null ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-secondary" for="exportar_global">
                                                        <i class="fas fa-link"></i> Usar Global
                                                        <br><small>({{ $configsProcessos['processos_empresa_pode_exportar']['global'] ? '✓ Sim' : '✗ Não' }})</small>
                                                    </label>

                                                    <input type="radio" class="btn-check" name="processos_empresa_pode_exportar"
                                                           id="exportar_sim" value="sim"
                                                           {{ $configsProcessos['processos_empresa_pode_exportar']['empresa'] === true ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-success" for="exportar_sim">
                                                        <i class="fas fa-check"></i> Permitir
                                                    </label>

                                                    <input type="radio" class="btn-check" name="processos_empresa_pode_exportar"
                                                           id="exportar_nao" value="nao"
                                                           {{ $configsProcessos['processos_empresa_pode_exportar']['empresa'] === false ? 'checked' : '' }}>
                                                    <label class="btn btn-outline-danger" for="exportar_nao">
                                                        <i class="fas fa-times"></i> Negar
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sidebar com resumo -->
                                <div class="col-md-4">
                                    <div class="card border-info bg-light sticky-top" style="top: 20px;">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0"><i class="fas fa-info-circle"></i> Resumo da Unidade</h6>
                                        </div>
                                        <div class="card-body small">
                                            <p class="mb-2">
                                                <strong>Nome:</strong><br>
                                                {{ $empresa->nome_empresa }}
                                            </p>
                                            <p class="mb-2">
                                                <strong>CNPJ:</strong><br>
                                                {{ $empresa->numero_cnpj ?? '—' }}
                                            </p>
                                            <p class="mb-2">
                                                <strong>Email:</strong><br>
                                                {{ $empresa->email ?? '—' }}
                                            </p>
                                            <p class="mb-2">
                                                <strong>Telefone:</strong><br>
                                                {{ $empresa->numero_telefone ?? '—' }}
                                            </p>
                                            <hr>
                                            <p class="text-muted mb-2" style="font-size: 0.85rem;">
                                                <i class="fas fa-lightbulb"></i> Salve para aplicar as alterações.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-save"></i> Salvar
                                        </button>
                                        <a href="{{ route('configuracoes.empresas') }}" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-arrow-left"></i> Voltar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
