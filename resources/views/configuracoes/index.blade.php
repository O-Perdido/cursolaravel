@extends('layouts.main')

@section('title', 'Configurações do Sistema')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
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
                </div>
            </div>
        </div>
    </div>
@endsection