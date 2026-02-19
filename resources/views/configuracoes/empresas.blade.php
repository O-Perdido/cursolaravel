@extends('layouts.main')

@section('title', 'Configurações por Unidade Concedente')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-building"></i> Configurações por Unidade Concedente
                        </h4>
                        <button type="button" class="btn btn-sm btn-light" onclick="window.close()"
                            title="Fechar esta guia">
                            <i class="fas fa-times"></i> Fechar
                        </button>
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

                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle"></i>
                            <strong>Como funciona:</strong> Aqui você pode configurar permissões individualizadas para cada
                            unidade concedente.
                            Deixe em branco para usar as configurações globais, ou escolha um valor específico para
                            sobrescrever.
                        </div>

                        <!-- Filtro e Busca -->
                        <div class="card mb-3 bg-light">
                            <div class="card-body pb-2 pt-3">
                                <form method="GET" action="{{ route('configuracoes.empresas') }}">
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <label for="nome" class="form-label mb-1 fw-semibold">
                                                <i class="fas fa-building"></i> Nome da Unidade
                                            </label>
                                            <input type="text" class="form-control form-control-sm" id="nome" name="nome"
                                                placeholder="Digite para buscar..." value="{{ request('nome') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="email" class="form-label mb-1 fw-semibold">
                                                <i class="fas fa-envelope"></i> Email
                                            </label>
                                            <input type="email" class="form-control form-control-sm" id="email" name="email"
                                                placeholder="exemplo@email.com" value="{{ request('email') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="cnpj" class="form-label mb-1 fw-semibold">
                                                <i class="fas fa-id-card"></i> CNPJ
                                            </label>
                                            <input type="text" class="form-control form-control-sm" id="cnpj" name="cnpj"
                                                placeholder="00.000.000/0000-00" value="{{ request('cnpj') }}">
                                        </div>
                                    </div>
                                    <div class="row g-2 mt-1">
                                        <div class="col-md-12 d-flex gap-2">
                                            <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                                <i class="fas fa-search me-1"></i> Buscar
                                            </button>
                                            <a href="{{ route('configuracoes.empresas') }}"
                                                class="btn btn-outline-secondary btn-sm flex-fill">
                                                <i class="fas fa-broom me-1"></i> Limpar
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th><i class="fas fa-building"></i> Unidade Concedente</th>
                                        <th>Email</th>
                                        <th>Telefone</th>
                                        <th>Status Configuração</th>
                                        <th width="100">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($empresas as $empresa)
                                        @php
                                            $configsEmpresa = \App\Models\EmpresaConfiguracao::obterTodasPorEmpresa($empresa->id_empresa);
                                            $temConfigPersonalizada = $configsEmpresa->whereNotNull('valor')->count() > 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <strong>{{ $empresa->nome_empresa }}</strong>
                                            </td>
                                            <td>{{ $empresa->email ?? '—' }}</td>
                                            <td>{{ $empresa->numero_telefone ?? '—' }}</td>
                                            <td>
                                                @if($temConfigPersonalizada)
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-star"></i> Personalizado
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-link"></i> Global
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('configuracoes.editar-empresa', $empresa->id_empresa) }}"
                                                    class="btn btn-sm btn-primary" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox"></i> Nenhuma unidade concedente encontrada
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        @if($empresas->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $empresas->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection