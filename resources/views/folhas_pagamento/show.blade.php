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
        <a href="{{ route('folhas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
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


@endsection