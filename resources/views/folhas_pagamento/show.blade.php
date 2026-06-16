@extends('layouts.main')

@section('title', 'Folhas de Pagamento')

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('delete'))
        <div class="alert alert-danger">
            {{ session('delete') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <button onclick="window.NavigationHistory?.goBack('{{ route('folhas.index') }}')" class="btn btn-secondary"
            title="Voltar para a página anterior com filtros preservados">
            <i class="fas fa-arrow-left"></i> Voltar
        </button>
        <div>
            @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                <a href="{{ route('folha_pagamento.prepararRemessa', $folha->id_folha_pagamento) }}" class="btn btn-success">
                    <i class="fas fa-file-export"></i> Preparar Remessa CNAB240
                </a>
                <a href="{{ route('folha_pagamento.export', $folha->id_folha_pagamento) }}" class="btn btn-info">
                    <i class="fas fa-file-excel"></i> Exportar Excel
                </a>
                <a href="{{ route('folhas.edit', $folha->id_folha_pagamento) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Editar
                </a>
            @endif
            <a href="{{ route('folha_pagamento.gerarPdf', $folha->id_folha_pagamento) }}" target="_blank"
                class="btn btn-primary">
                <i class="fas fa-file-pdf"></i> Gerar PDF
            </a>
        </div>
    </div>

    <!-- Painel de Integração NFS-e Notaas -->
    @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
        <div class="card mb-4 border shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                <h6 class="mb-0 fw-semibold text-secondary d-flex align-items-center gap-2">
                    <i class="fas fa-file-invoice text-primary"></i>
                    Nota Fiscal Eletrônica (NFS-e via Notaas)
                </h6>
                @if ($folha->notaFiscal && !empty($folha->notaFiscal->notaas_invoice_id))
                    <div class="d-flex align-items-center gap-2">
                        <span class="small text-muted">Status:</span>
                        @switch($folha->notaFiscal->notaas_status)
                            @case('queued')
                                <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Na Fila (Notaas)</span>
                                @break
                            @case('processing')
                                <span class="badge bg-info"><i class="fas fa-spinner fa-spin"></i> Processando</span>
                                @break
                            @case('issued')
                                <span class="badge bg-success"><i class="fas fa-check-circle"></i> Emitida</span>
                                @break
                            @case('error')
                                <span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> Erro de Emissão</span>
                                @break
                            @case('cancelled')
                                <span class="badge bg-secondary"><i class="fas fa-ban"></i> Cancelada</span>
                                @break
                            @default
                                <span class="badge bg-light text-dark">{{ $folha->notaFiscal->notaas_status }}</span>
                        @endswitch
                    </div>
                @else
                    <span class="badge bg-light text-dark border">Não emitida</span>
                @endif
            </div>
            <div class="card-body py-3">
                @if (!$folha->notaFiscal || empty($folha->notaFiscal->notaas_invoice_id))
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <p class="mb-0 text-muted small">Esta folha de pagamento ainda não possui nota fiscal eletrônica associada.</p>
                            <span class="text-xs text-muted">Valores disponíveis: Taxa Adm: <strong>R$ {{ number_format($folha->total_taxa_adm, 2, ',', '.') }}</strong> | Total Folha: <strong>R$ {{ number_format($folha->total_folha, 2, ',', '.') }}</strong> | Soma: <strong>R$ {{ number_format($folha->total_taxa_adm + $folha->total_folha, 2, ',', '.') }}</strong></span>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#emitirNfseModal">
                            <i class="fas fa-plus-circle me-1"></i> Emitir NFS-e via Notaas
                        </button>
                    </div>
                @else
                    <div class="row align-items-center g-3">
                        <div class="col-md-7">
                            <div class="small text-muted mb-1">
                                <span class="fw-semibold">ID da Nota (Notaas):</span> <code>{{ $folha->notaFiscal->notaas_invoice_id }}</code>
                            </div>
                            @if ($folha->notaFiscal->notaas_emitted_at)
                                <div class="small text-muted mb-1">
                                    <span class="fw-semibold">Data de Emissão:</span> {{ \Carbon\Carbon::parse($folha->notaFiscal->notaas_emitted_at)->format('d/m/Y H:i:s') }}
                                </div>
                            @endif
                            @if ($folha->notaFiscal->notaas_error_message)
                                <div class="alert alert-danger py-2 px-3 mt-2 mb-0 small">
                                    <strong>Erro retornado pela SEFAZ:</strong> {{ $folha->notaFiscal->notaas_error_message }}
                                </div>
                            @endif
                        </div>
                        <div class="col-md-5 d-flex justify-content-end align-items-center gap-2 flex-wrap">
                            <!-- Formulário para sincronizar status -->
                            <form action="{{ route('notaas.sincronizar', $folha->notaFiscal->id) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-sync-alt me-1"></i> Sincronizar Status
                                </button>
                            </form>

                            @if ($folha->notaFiscal->notaas_status === 'issued')
                                @if ($folha->notaFiscal->notaas_pdf_url)
                                    <a href="{{ $folha->notaFiscal->notaas_pdf_url }}" target="_blank" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-file-pdf me-1"></i> Visualizar PDF
                                    </a>
                                @endif
                                @if ($folha->notaFiscal->notaas_xml_url)
                                    <a href="{{ $folha->notaFiscal->notaas_xml_url }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-file-code me-1"></i> Baixar XML
                                    </a>
                                @endif
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelarNfseModal">
                                    <i class="fas fa-ban me-1"></i> Cancelar NFS-e
                                </button>
                            @endif

                            @if ($folha->notaFiscal->notaas_status === 'error')
                                <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#emitirNfseModal">
                                    <i class="fas fa-redo me-1"></i> Tentar Reemitir Nota
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <h1>Folha de Pagamento - {{ $folha->numero_folha }}/{{ \Carbon\Carbon::parse($folha->data_folha)->format('Y') }}
            </h1>
            <h5><span style="font-weight: bold;">Cliente:</span> {{ $folha->empresa->nome_empresa }}</h5>
            <p>
                <span style="font-weight: bold;">CNPJ:</span> {{ $folha->empresa->numero_cnpj }} <br>
                <span style="font-weight: bold;">Endereço:</span> {{ $folha->empresa->endereco }},
                {{ $folha->empresa->numero_endereco }},
                {{ $folha->empresa->complemento_endereco ? $folha->empresa->complemento_endereco . ', ' : '' }}{{ $folha->empresa->bairro }},
                {{  $folha->empresa->cidade->nm_cidade }}, {{ $folha->empresa->cidade->estado->uf_estado }}
            </p>


        </div>
        <div class="col-md-6">
            <!-- Conteúdo da segunda coluna -->
            <h2 style="text-align: right;">Mês Referência:
                {{ $folha->getMesReferenciaFormatado() }}/{{ $folha->ano_referencia }}
            </h2>
            <p style="text-align: right;">
                Data da Fatura: {{ \Carbon\Carbon::parse($folha->data_folha)->format('d/m/Y') }} <br>
                <span style="font-weight: bold;">Data de Vencimento:
                    {{ \Carbon\Carbon::parse($folha->vencimento_folha)->format('d/m/Y') }}</span>
            </p>
            <p style="text-align: right;">
                <span style="font-weight: bold;">Tipo de Cálculo de Auxílio Transporte:</span>
                {{ $folha->tipo_calculo_auxilio_transporte === 'diario' ? 'Diário' : 'Mensal' }} <br>
                @if ($folha->tipo_calculo_auxilio_transporte === 'diario')
                    <span style="font-weight: bold;">Dias Úteis no Mês:</span> {{ $folha->dias_uteis }}
                @endif
            </p>
            <p style="text-align: right;">
                <span style="font-weight: bold;">Tipo de Cálculo de Recesso:</span>
                {{ $folha->tipo_calculo_recesso === 'com_saldo' ? 'Com Saldo de Recesso' : 'Original' }}
            </p>
            <p style="text-align: right;">
                <span style="font-weight: bold;">Local:</span>
                {{ $folha->local ? $folha->local->descricao : 'Todos os locais / Não especificado' }}
            </p>
        </div>
    </div>

    <h5 style="text-align: center; margin-top: 15px;"> FATURA
        {{ $folha->numero_folha }}/{{ Carbon\Carbon::parse($folha->data_folha)->format('Y') }} - DESCRITIVO VALORES FOLHA
        ESTAGIÁRIOS
    </h5>
    <div>
        <table class="table">
            <thead style="font-size: small;">
                <tr style="vertical-align: middle;">
                    <th>Estagiário(a)</th>
                    <th style="text-align: center;">Dias Trabalhados</th>
                    <th style="text-align: center;">Bolsa</th>
                    <th style="text-align: center;">Aux. Transp.</th>
                    <th style="text-align: center;">Bolsa Mês</th>
                    <th style="text-align: center;">Aux. Transp. Mês</th>
                    <th style="text-align: center;">Recesso</th>
                    <th style="text-align: center;">Taxa Adm</th>
                    <th style="text-align: center;">Inicio Contrato</th>
                    <th style="text-align: center;">Fim Contrato</th>
                    <th style="text-align: center;">Acertos</th>
                    <th style="text-align: center;">Total</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: center;">Ações</th>
                </tr>
            </thead>
            <tbody style="font-size: small;">
                @php
                    $conteudoFolha = $conteudoFolha->sortBy(function ($item) {
                        $nome = $item->termo->estagiario->nome_estagiario;
                        // Remove acentos para ordenação
                        $nome = mb_strtoupper($nome, 'UTF-8');
                        $nome = str_replace(
                            ['Á', 'À', 'Â', 'Ã', 'Ä', 'É', 'È', 'Ê', 'Ë', 'Í', 'Ì', 'Î', 'Ï', 'Ó', 'Ò', 'Ô', 'Õ', 'Ö', 'Ú', 'Ù', 'Û', 'Ü', 'Ç'],
                            ['A', 'A', 'A', 'A', 'A', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'C'],
                            $nome
                        );
                        return $nome;
                    })->values();
                @endphp
                @foreach($conteudoFolha as $conteudo)
                    <tr style="vertical-align: middle;">
                        <td>{{ $conteudo->termo->estagiario->nome_estagiario }}
                            <a href="{{ route('estagiario.show', $conteudo->termo->estagiario->id_estagiario) }}"
                                target="_blank" class="ml-1" title="Ver detalhes do estagiário">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </td>
                        <td style="text-align: center;">{{ $conteudo->dias_trabalhados }}</td>
                        <td style="text-align: center; width: 90px;"><span style="font-size: xx-small;">R$</span>
                            {{ number_format($conteudo->valor_bolsa, 2, ',', '.') }}</td>
                        <td style="text-align: center; width: 90px;"><span style="font-size: xx-small;">R$</span>
                            {{ number_format($conteudo->valor_auxilio_transporte, 2, ',', '.') }}</td>
                        <td style="text-align: center; width: 90px;"><span style="font-size: xx-small;">R$</span>
                            {{ number_format($conteudo->valor_bolsa_mes, 2, ',', '.') }}</td>
                        <td style="text-align: center; width: 90px;"><span style="font-size: xx-small;">R$</span>
                            {{ number_format($conteudo->valor_auxilio_transporte_mes, 2, ',', '.') }}</td>
                        <td style="text-align: center; width: 90px;"><span style="font-size: xx-small;">R$</span>
                            {{ number_format($conteudo->valor_recesso, 2, ',', '.') }}</td>
                        <td style="text-align: center; width: 90px;"><span style="font-size: xx-small;">R$</span>
                            {{ number_format($conteudo->taxa_adm, 2, ',', '.') }}</td>
                        <td style="text-align: center;">
                            {{ \Carbon\Carbon::parse($conteudo->termo->data_inicio_estagio)->format('d/m/Y') }}
                        </td>
                        <td style="text-align: center;">
                            {{ \Carbon\Carbon::parse($conteudo->termo->data_fim_estagio)->format('d/m/Y') }}
                        </td>
                        <td style="text-align: center; width: 90px;"><span style="font-size: xx-small;">R$</span>
                            {{ number_format($conteudo->descontos, 2, ',', '.') }}</td>
                        <td style="text-align: center; font-weight: bold; width: 90px;"><span
                                style="font-size: xx-small;">R$</span>
                            {{ number_format($conteudo->total, 2, ',', '.') }}</td>
                        <td style="text-align: center; vertical-align: middle;">
                            @if ($conteudo->termo->rescisao)
                                <span class="badge bg-danger">R</span>
                            @else
                                <span class="badge bg-success">A</span>
                            @endif
                        </td>
                        <td style="text-align: center; vertical-align: middle;">
                            <!-- Botão para gerar pdf do recibo -->
                            <a href="{{ route('folha_pagamento.gerarRecibo', [$folha->id_folha_pagamento, $conteudo->id]) }}"
                                class="btn btn-sm btn-primary" style="font-size: x-small; font-weight: bold;" target="_blank">
                                Recibo
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot style="background-color: #e9ecef; font-weight: bold; border-top: 2px solid #dee2e6; font-size: small;">
                <tr>
                    <td colspan="2" style="text-align: center; background-color: #e9ecef;">Total de Estagiários:
                        {{ $conteudoFolha->count() }}
                    </td>
                    <td colspan="2" style="text-align: center; background-color: #e9ecef;">Totais:</td>
                    <td style="text-align: center; background-color: #e9ecef;">
                        <span style="font-size: xx-small;">R$</span>
                        {{ number_format($conteudoFolha->sum('valor_bolsa_mes'), 2, ',', '.') }}
                    </td>
                    <td style="text-align: center; background-color: #e9ecef;">
                        <span style="font-size: xx-small;">R$</span>
                        {{ number_format($conteudoFolha->sum('valor_auxilio_transporte_mes'), 2, ',', '.') }}
                    </td>
                    <td style="text-align: center; background-color: #e9ecef;">
                        <span style="font-size: xx-small;">R$</span>
                        {{ number_format($conteudoFolha->sum('valor_recesso'), 2, ',', '.') }}
                    </td>
                    <td style="text-align: center; background-color: #e9ecef;">
                        <span style="font-size: xx-small;">R$</span>
                        {{ number_format($conteudoFolha->sum('taxa_adm'), 2, ',', '.') }}
                    </td>
                    <td colspan="2" style="text-align: center; background-color: #e9ecef;"></td>
                    <td style="text-align: center; background-color: #e9ecef;">
                        <span style="font-size: xx-small;">R$:</span>
                        {{ number_format($conteudoFolha->sum('descontos'), 2, ',', '.') }}
                    </td>
                    <td style="text-align: right; background-color: #e9ecef;">
                        TOTAL R$:
                    <td colspan="2" style="text-align: center; background-color: #e9ecef;">
                        {{ number_format($conteudoFolha->sum('total'), 2, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="14"
                        style="text-align: center; background-color: #e9ecef; font-weight: bold; font-size: 16px;">
                        TOTAL GERAL (Valor Bolsa + Taxa ADM): R$
                        {{ number_format($conteudoFolha->sum('total') + $conteudoFolha->sum('taxa_adm'), 2, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
        <!-- Modal Emitir NFS-e -->
        <div class="modal fade" id="emitirNfseModal" tabindex="-1" aria-labelledby="emitirNfseModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="{{ route('folhas.emitirNfse', $folha->id_folha_pagamento) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="emitirNfseModalLabel">Emitir Nota Fiscal (NFS-e via Notaas)</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Seleção de Valor com botões rápidos -->
                            <div class="mb-3">
                                <label for="valor_nota" class="form-label fw-bold">Valor da Nota Fiscal (R$)</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" step="0.01" class="form-control" id="valor_nota" name="valor_nota" 
                                        value="{{ $folha->total_taxa_adm }}" required>
                                </div>
                                <div class="mt-2">
                                    <button type="button" class="btn btn-outline-secondary btn-sm me-2" 
                                        onclick="setValorNota('{{ $folha->total_taxa_adm }}')">
                                        Usar Taxa Adm (R$ {{ number_format($folha->total_taxa_adm, 2, ',', '.') }})
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm me-2" 
                                        onclick="setValorNota('{{ $folha->total_folha }}')">
                                        Usar Total Folha (R$ {{ number_format($folha->total_folha, 2, ',', '.') }})
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" 
                                        onclick="setValorNota('{{ $folha->total_taxa_adm + $folha->total_folha }}')">
                                        Usar Ambos (R$ {{ number_format($folha->total_taxa_adm + $folha->total_folha, 2, ',', '.') }})
                                    </button>
                                </div>
                            </div>

                            <!-- Descrição do Serviço -->
                            <div class="mb-3">
                                <label for="descricao_servico" class="form-label fw-bold">Descrição do Serviço Prestado</label>
                                <textarea class="form-control" id="descricao_servico" name="descricao_servico" rows="4" required>TAXA DE CONTRATAÇÃO E ADMINISTRAÇÃO DE CONTRATOS DE ESTÁGIOS.</textarea><!--, referente à folha de pagamento nº {{ $folha->numero_folha }}, mês de referência: {{ $folha->getMesReferenciaFormatado() }}/{{ $folha->ano_referencia }}.-->
                                <small class="text-muted">Este texto constará na nota fiscal emitida.</small>
                            </div>

                            <div class="row">
                                <!-- Código de Serviço -->
                                <div class="col-md-6 mb-3">
                                    <label for="codigo_servico" class="form-label fw-bold">Código de Serviço (cTribNac - opcional)</label>
                                    <input type="text" class="form-control" id="codigo_servico" name="codigo_servico" 
                                        placeholder="Ex: 170501">
                                    <small class="text-muted">Deixe em branco para usar o padrão da Notaas.</small>
                                </div>

                                <!-- Alíquota de ISS -->
                                <div class="col-md-6 mb-3">
                                    <label for="aliquota_iss" class="form-label fw-bold">Alíquota ISS (%)</label>
                                    <input type="number" step="0.01" min="0" max="100" class="form-control" id="aliquota_iss" name="aliquota_iss" 
                                        value="6.0" required>
                                    <small class="text-muted">Informe a alíquota de ISS aplicável (ex: 6.0 para 6%).</small>
                                </div>
                            </div>

                            <!-- ISS Retido -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="iss_retido" name="iss_retido" value="1">
                                <label class="form-check-label fw-semibold" for="iss_retido">ISS retido pelo tomador (empresa)?</label>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Transmitir para Notaas</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($folha->notaFiscal && $folha->notaFiscal->notaas_status === 'issued')
            <!-- Modal Cancelar NFS-e -->
            <div class="modal fade" id="cancelarNfseModal" tabindex="-1" aria-labelledby="cancelarNfseModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content text-start">
                        <form action="{{ route('notaas.cancelar', $folha->notaFiscal->id) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title text-danger" id="cancelarNfseModalLabel">Solicitar Cancelamento de NFS-e</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                            </div>
                            <div class="modal-body text-start">
                                <p class="text-danger fw-semibold"><i class="fas fa-exclamation-triangle"></i> Atenção: Esta ação enviará uma solicitação de cancelamento da nota diretamente à prefeitura/SEFAZ.</p>
                                <div class="mb-3">
                                    <label for="motivo_cancelamento" class="form-label fw-bold">Motivo do Cancelamento</label>
                                    <textarea class="form-control" id="motivo_cancelamento" name="motivo_cancelamento" rows="3" 
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

        <script>
            function setValorNota(value) {
                document.getElementById('valor_nota').value = parseFloat(value).toFixed(2);
            }
        </script>
    @endif

@endsection