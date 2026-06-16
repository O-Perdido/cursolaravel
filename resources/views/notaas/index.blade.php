@extends('layouts.main')

@section('title', 'Gerenciador de Notas Fiscais (NFS-e)')

@section('content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Painel de Notas Fiscais (NFS-e)</h1>
            <p class="text-muted small mb-0">Visualize, sincronize e gerencie todas as notas fiscais emitidas via API Notaas.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#emitirNotaAvulsaModal">
                <i class="fas fa-plus-circle me-1"></i> Emitir Nota Avulsa (100% Personalizada)
            </button>
        </div>
    </div>

    <!-- Barra de Filtros -->
    <div class="card border shadow-sm mb-4">
        <div class="card-header bg-light py-2">
            <h6 class="m-0 fw-semibold text-secondary"><i class="fas fa-filter me-1"></i> Filtrar Notas</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('notaas.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="filter_tomador" class="form-label small fw-semibold text-muted">Razão Social (Tomador)</label>
                    <input type="text" class="form-control form-control-sm" id="filter_tomador" name="tomador" 
                        value="{{ request('tomador') }}" placeholder="Nome do cliente...">
                </div>
                <div class="col-md-2">
                    <label for="filter_cnpj" class="form-label small fw-semibold text-muted">CNPJ do Tomador</label>
                    <input type="text" class="form-control form-control-sm" id="filter_cnpj" name="cnpj" 
                        value="{{ request('cnpj') }}" placeholder="Apenas números...">
                </div>
                <div class="col-md-2">
                    <label for="filter_status" class="form-label small fw-semibold text-muted">Status</label>
                    <select class="form-select form-select-sm" id="filter_status" name="status">
                        <option value="">Todos os status</option>
                        @foreach ($statuses as $val => $lbl)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                                {{ $lbl }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filter_data_inicio" class="form-label small fw-semibold text-muted">Data Inicial</label>
                    <input type="date" class="form-control form-control-sm" id="filter_data_inicio" name="data_inicio" 
                        value="{{ request('data_inicio') }}">
                </div>
                <div class="col-md-2">
                    <label for="filter_data_fim" class="form-label small fw-semibold text-muted">Data Final</label>
                    <input type="date" class="form-control form-control-sm" id="filter_data_fim" name="data_fim" 
                        value="{{ request('data_fim') }}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <div class="w-100 d-flex gap-2">
                        <button type="submit" class="btn btn-sm btn-primary flex-fill" title="Filtrar">
                            <i class="fas fa-search"></i>
                        </button>
                        <a href="{{ route('notaas.index') }}" class="btn btn-sm btn-outline-secondary" title="Limpar Filtros">
                            <i class="fas fa-eraser"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de Notas Fiscais -->
    <div class="card border shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">ID Notaas / Referência</th>
                            <th>Tomador (Cliente)</th>
                            <th>Origem / Vínculo</th>
                            <th>Valor</th>
                            <th>Competência</th>
                            <th>Status</th>
                            <th class="text-center pe-3">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($invoices as $invoice)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-semibold">{{ $invoice->notaas_invoice_id ?: 'Aguardando Fila' }}</div>
                                    <span class="text-xs text-muted">Ref: {{ $invoice->referencia }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-secondary">{{ $invoice->tomador_nome }}</div>
                                    <span class="text-xs text-muted">CNPJ: {{ preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $invoice->tomador_cnpj) }}</span>
                                </td>
                                <td>
                                    @if ($invoice->fk_id_folha)
                                        <a href="{{ route('folhas.show', $invoice->fk_id_folha) }}" class="badge bg-light text-primary border text-decoration-none">
                                            <i class="fas fa-file-invoice-dollar me-1"></i> Folha #{{ $invoice->folhaPagamento->numero_folha ?? $invoice->fk_id_folha }}
                                        </a>
                                    @else
                                        <span class="badge bg-light text-secondary border">
                                            <i class="fas fa-user-edit me-1"></i> Nota Avulsa
                                        </span>
                                    @endif
                                </td>
                                <td class="fw-bold text-dark">
                                    R$ {{ number_format($invoice->valor, 2, ',', '.') }}
                                </td>
                                <td>
                                    {{ $invoice->competencia }}
                                </td>
                                <td>
                                    @switch($invoice->notaas_status)
                                        @case('queued')
                                            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Na Fila</span>
                                            @break
                                        @case('processing')
                                            <span class="badge bg-info"><i class="fas fa-spinner fa-spin me-1"></i> Processando</span>
                                            @break
                                        @case('issued')
                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Emitida</span>
                                            @break
                                        @case('error')
                                            <span class="badge bg-danger" data-bs-toggle="tooltip" title="{{ $invoice->notaas_error_message }}">
                                                <i class="fas fa-exclamation-triangle me-1"></i> Erro
                                            </span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-secondary"><i class="fas fa-ban me-1"></i> Cancelada</span>
                                            @break
                                        @default
                                            <span class="badge bg-light text-dark border">{{ $invoice->notaas_status }}</span>
                                    @endswitch
                                </td>
                                <td class="text-center pe-3">
                                    <div class="d-inline-flex gap-1">
                                        <!-- Form para Sincronizar -->
                                        <form action="{{ route('notaas.sincronizar', $invoice->id) }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-primary" title="Sincronizar Status">
                                                <i class="fas fa-sync-alt"></i>
                                            </button>
                                        </form>

                                        @if ($invoice->notaas_status === 'issued')
                                            @if ($invoice->notaas_pdf_url)
                                                <a href="{{ $invoice->notaas_pdf_url }}" target="_blank" class="btn btn-sm btn-outline-success" title="Visualizar PDF">
                                                    <i class="fas fa-file-pdf"></i>
                                                </a>
                                            @endif
                                            @if ($invoice->notaas_xml_url)
                                                <a href="{{ $invoice->notaas_xml_url }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Baixar XML">
                                                    <i class="fas fa-file-code"></i>
                                                </a>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Cancelar Nota"
                                                data-bs-toggle="modal" data-bs-target="#cancelarNotaModal{{ $invoice->id }}">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @endif

                                        @if ($invoice->notaas_status === 'error' && $invoice->fk_id_folha)
                                            <a href="{{ route('folhas.show', $invoice->fk_id_folha) }}" class="btn btn-sm btn-warning" title="Tentar Reemitir via Folha">
                                                <i class="fas fa-redo"></i>
                                            </a>
                                        @endif
                                    </div>

                                    <!-- Modal Cancelar NFS-e para esta nota -->
                                    @if ($invoice->notaas_status === 'issued')
                                        <div class="modal fade" id="cancelarNotaModal{{ $invoice->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content text-start">
                                                    <form action="{{ route('notaas.cancelar', $invoice->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title text-danger fw-bold"><i class="fas fa-ban"></i> Cancelar Nota Fiscal</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p class="text-danger fw-semibold">
                                                                <i class="fas fa-exclamation-triangle"></i> Atenção: Esta ação enviará uma solicitação de cancelamento da nota diretamente à prefeitura/SEFAZ.
                                                            </p>
                                                            <div class="mb-3">
                                                                <label for="motivo_cancelamento_{{ $invoice->id }}" class="form-label fw-bold small text-secondary">Motivo do Cancelamento</label>
                                                                <textarea class="form-control" id="motivo_cancelamento_{{ $invoice->id }}" name="motivo_cancelamento" rows="3" 
                                                                    placeholder="Descreva o motivo (mínimo de 5 caracteres)..." required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
                                                            <button type="submit" class="btn btn-danger btn-sm">Confirmar Cancelamento</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle fa-2x mb-2 text-black-50"></i>
                                    <p class="mb-0">Nenhuma nota fiscal encontrada.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($invoices->hasPages())
            <div class="card-footer bg-light py-2">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Emitir Nota Avulsa -->
    <div class="modal fade" id="emitirNotaAvulsaModal" tabindex="-1" aria-labelledby="emitirNotaAvulsaLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('notaas.storeCustom') }}" method="POST" id="notaAvulsaForm">
                    @csrf
                    <input type="hidden" name="fk_id_empresa" id="fk_id_empresa" value="">
                    
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold text-secondary" id="emitirNotaAvulsaLabel">
                            <i class="fas fa-file-invoice me-1 text-primary"></i> Emitir Nota Fiscal Avulsa
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    
                    <div class="modal-body py-3">
                        <!-- Dropdown de Autopreenchimento -->
                        <div class="mb-4 p-3 bg-light border rounded-3">
                            <label for="empresa_select" class="form-label fw-bold text-secondary small">
                                <i class="fas fa-magic text-warning me-1"></i> Preenchimento Automático por Concedente Cadastrada
                            </label>
                            <select class="form-select" id="empresa_select">
                                <option value="">-- Preencher manualmente ou selecionar Concedente --</option>
                                @foreach ($empresas as $emp)
                                    <option value="{{ $emp->id_empresa }}"
                                        data-nome="{{ $emp->nome_empresa }}"
                                        data-cnpj="{{ preg_replace('/\D/', '', $emp->numero_cnpj) }}"
                                        data-email="{{ $emp->email }}"
                                        data-telefone="{{ preg_replace('/\D/', '', $emp->numero_telefone) }}"
                                        data-endereco="{{ $emp->endereco }}"
                                        data-numero="{{ $emp->numero_endereco }}"
                                        data-bairro="{{ $emp->bairro }}"
                                        data-cep="{{ preg_replace('/\D/', '', $emp->numero_cep) }}"
                                        data-cidade="{{ $emp->cidade->nm_cidade ?? '' }}"
                                        data-uf="{{ $emp->cidade->estado->uf_estado ?? '' }}">
                                        {{ $emp->nome_empresa }} (CNPJ: {{ $emp->numero_cnpj }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Grid de Campos do Tomador -->
                        <h6 class="fw-bold mb-3 text-primary border-bottom pb-1"><i class="fas fa-user-tie me-1"></i> Dados do Tomador (Cliente)</h6>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label for="tomador_nome" class="form-label small fw-semibold text-muted">Razão Social / Nome completo</label>
                                <input type="text" class="form-control form-control-sm" id="tomador_nome" name="tomador_nome" required value="{{ old('tomador_nome') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="tomador_cnpj" class="form-label small fw-semibold text-muted">CNPJ</label>
                                <input type="text" class="form-control form-control-sm" id="tomador_cnpj" name="tomador_cnpj" required value="{{ old('tomador_cnpj') }}" placeholder="Apenas números">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <label for="tomador_email" class="form-label small fw-semibold text-muted">E-mail para envio da NF</label>
                                <input type="email" class="form-control form-control-sm" id="tomador_email" name="tomador_email" value="{{ old('tomador_email') }}">
                            </div>
                            <div class="col-md-5">
                                <label for="tomador_telefone" class="form-label small fw-semibold text-muted">Telefone</label>
                                <input type="text" class="form-control form-control-sm" id="tomador_telefone" name="tomador_telefone" value="{{ old('tomador_telefone') }}" placeholder="Apenas números">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-3">
                                <label for="tomador_cep" class="form-label small fw-semibold text-muted">CEP</label>
                                <input type="text" class="form-control form-control-sm" id="tomador_cep" name="tomador_cep" required value="{{ old('tomador_cep') }}" placeholder="Apenas números">
                            </div>
                            <div class="col-md-6">
                                <label for="tomador_endereco" class="form-label small fw-semibold text-muted">Logradouro / Endereço</label>
                                <input type="text" class="form-control form-control-sm" id="tomador_endereco" name="tomador_endereco" required value="{{ old('tomador_endereco') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="tomador_numero" class="form-label small fw-semibold text-muted">Número</label>
                                <input type="text" class="form-control form-control-sm" id="tomador_numero" name="tomador_numero" required value="{{ old('tomador_numero', 'S/N') }}">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-5">
                                <label for="tomador_bairro" class="form-label small fw-semibold text-muted">Bairro</label>
                                <input type="text" class="form-control form-control-sm" id="tomador_bairro" name="tomador_bairro" required value="{{ old('tomador_bairro') }}">
                            </div>
                            <div class="col-md-5">
                                <label for="tomador_cidade" class="form-label small fw-semibold text-muted">Cidade</label>
                                <input type="text" class="form-control form-control-sm" id="tomador_cidade" name="tomador_cidade" required value="{{ old('tomador_cidade') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="tomador_uf" class="form-label small fw-semibold text-muted">UF</label>
                                <input type="text" class="form-control form-control-sm" id="tomador_uf" name="tomador_uf" required size="2" maxlength="2" value="{{ old('tomador_uf') }}" placeholder="Ex: SC">
                            </div>
                        </div>

                        <!-- Grid de Campos do Serviço -->
                        <h6 class="fw-bold mb-3 text-primary border-bottom pb-1"><i class="fas fa-file-invoice-dollar me-1"></i> Detalhamento do Serviço e Impostos</h6>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="valor_nota" class="form-label small fw-semibold text-muted">Valor da Fatura (R$)</label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" step="0.01" class="form-control form-control-sm" id="valor_nota" name="valor_nota" required value="{{ old('valor_nota') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="aliquota_iss" class="form-label small fw-semibold text-muted">Alíquota ISS (%)</label>
                                <input type="number" step="0.01" class="form-control form-control-sm" id="aliquota_iss" name="aliquota_iss" required value="{{ old('aliquota_iss', '6.0') }}">
                            </div>
                            <div class="col-md-4">
                                <label for="codigo_servico" class="form-label small fw-semibold text-muted">Código Serviço (cTribNac)</label>
                                <input type="text" class="form-control form-control-sm" id="codigo_servico" name="codigo_servico" value="{{ old('codigo_servico') }}" placeholder="Ex: 170501">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="descricao_servico" class="form-label small fw-semibold text-muted">Descrição do Serviço</label>
                            <textarea class="form-control form-control-sm" id="descricao_servico" name="descricao_servico" rows="3" required>{{ old('descricao_servico', 'TAXA DE CONTRATAÇÃO E ADMINISTRAÇÃO DE CONTRATOS DE ESTÁGIOS') }}</textarea>
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="iss_retido" name="iss_retido" value="1" {{ old('iss_retido') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold small text-secondary" for="iss_retido">
                                ISS Retido pelo Tomador?
                            </label>
                        </div>
                    </div>
                    
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-paper-plane me-1"></i> Transmitir NFS-e</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script de autopreenchimento por JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // JS para autopreencher dados
            var select = document.getElementById('empresa_select');
            
            select.addEventListener('change', function () {
                var selected = select.options[select.selectedIndex];
                
                if (selected.value) {
                    document.getElementById('tomador_nome').value = selected.getAttribute('data-nome') || '';
                    document.getElementById('tomador_cnpj').value = selected.getAttribute('data-cnpj') || '';
                    document.getElementById('tomador_email').value = selected.getAttribute('data-email') || '';
                    document.getElementById('tomador_telefone').value = selected.getAttribute('data-telefone') || '';
                    document.getElementById('tomador_endereco').value = selected.getAttribute('data-endereco') || '';
                    document.getElementById('tomador_numero').value = selected.getAttribute('data-numero') || '';
                    document.getElementById('tomador_bairro').value = selected.getAttribute('data-bairro') || '';
                    document.getElementById('tomador_cep').value = selected.getAttribute('data-cep') || '';
                    document.getElementById('tomador_cidade').value = selected.getAttribute('data-cidade') || '';
                    document.getElementById('tomador_uf').value = selected.getAttribute('data-uf') || '';
                    document.getElementById('fk_id_empresa').value = selected.value;
                } else {
                    document.getElementById('tomador_nome').value = '';
                    document.getElementById('tomador_cnpj').value = '';
                    document.getElementById('tomador_email').value = '';
                    document.getElementById('tomador_telefone').value = '';
                    document.getElementById('tomador_endereco').value = '';
                    document.getElementById('tomador_numero').value = 'S/N';
                    document.getElementById('tomador_bairro').value = '';
                    document.getElementById('tomador_cep').value = '';
                    document.getElementById('tomador_cidade').value = '';
                    document.getElementById('tomador_uf').value = '';
                    document.getElementById('fk_id_empresa').value = '';
                }
            });

            // Ativa tooltips do Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        });
    </script>

@endsection
