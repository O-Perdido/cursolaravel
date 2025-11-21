<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Folha de Pagamento PDF</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
            margin: -25px;
        }

        .header-logo {
            text-align: center;
            margin-bottom: 10px;
        }

        .ebcp-info {
            font-size: 11px;
            text-align: center;
            margin-bottom: 10px;
            color: #444;
        }

        .info-table {
            width: 100%;
            margin-bottom: 12px;
            background: #fff;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            padding: 10px;
        }

        .info-table td {
            border: none;
            padding: 4px 8px;
            font-size: 13px;
        }

        h5 {
            margin: 18px 0 8px 0;
            font-size: 15px;
            text-align: center;
            letter-spacing: 1px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 10px;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #dee2e6;
            padding: 4px 4px;
            font-size: 10px;
        }

        .data-table th {
            background: #e9ecef;
            color: #333;
            font-weight: bold;
        }

        .data-table tfoot td {
            font-weight: bold;
            background: #f1f3f5;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .badge {
            padding: 2px 7px;
            border-radius: 4px;
            color: #fff;
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .bg-danger {
            background: #dc3545;
        }

        .bg-success {
            background: #28a745;
        }

        .footer {
            margin-top: 18px;
            text-align: right;
            font-size: 10px;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="header-logo">
        <img src="{{ $linklogo }}" alt="Logo EBCP" height="70">
    </div>
    <table class="info-table">
        <tr>
            <td>
                <h2 style="margin-top: -10px; margin-bottom: -5px;">Folha de Pagamento -
                    {{ $folha->numero_folha }}/{{ \Carbon\Carbon::parse($folha->data_folha)->format('Y') }}
                </h2>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Cliente:</strong> {{ $folha->empresa->nome_empresa }}<br>
                <strong>CNPJ:</strong> {{ $folha->empresa->numero_cnpj }}<br>
                <strong>Endereço:</strong> {{ $folha->empresa->endereco }},
                {{ $folha->empresa->numero_endereco }},
                {{ $folha->empresa->complemento_endereco ? $folha->empresa->complemento_endereco . ', ' : '' }}{{ $folha->empresa->bairro }},
                {{  $folha->empresa->cidade->nm_cidade }}, {{ $folha->empresa->cidade->estado->uf_estado }}
                <br>
                <strong>Local:</strong>
                {{ $folha->local ? $folha->local->descricao : 'Todos os locais / Não especificado' }}
            </td>
            <td class="text-right">
                <strong>Mês Referência:</strong>
                {{ $folha->getMesReferenciaFormatado() }}/{{ $folha->ano_referencia }}<br>
                <strong>Data da Fatura:</strong> {{ \Carbon\Carbon::parse($folha->data_folha)->format('d/m/Y') }}<br>
                <strong>Data de Vencimento:</strong>
                {{ \Carbon\Carbon::parse($folha->vencimento_folha)->format('d/m/Y') }}
            </td>
        </tr>
        <tr>
            <td colspan="2" class="text-right" style="font-size: smaller;">
                <span style="font-weight: bold;">Tipo de Cálculo de Auxílio Transporte:</span>
                {{ $folha->tipo_calculo_auxilio_transporte === 'diario' ? 'Diário' : 'Mensal' }}
                @if ($folha->tipo_calculo_auxilio_transporte === 'diario')
                    <br>
                    <span style="font-weight: bold;">Dias Úteis no Mês:</span> {{ $folha->dias_uteis }}
                @endif
                <br>
                <!-- Exibe o modo de cálculo do recesso para transparência na fatura -->
                <span style="font-weight: bold;">Tipo de Cálculo de Recesso:</span>
                {{ $folha->tipo_calculo_recesso === 'com_saldo' ? 'Com Saldo de Recesso (dias não utilizados)' : 'Original' }}
            </td>
        </tr>
    </table>

    <h5>
        FATURA {{ $folha->numero_folha }}/{{ \Carbon\Carbon::parse($folha->data_folha)->format('Y') }} - DESCRITIVO
        VALORES
        FOLHA ESTAGIÁRIOS
    </h5>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 150px;">Estagiário(a)</th>
                <th class="text-center">Dias Trab.</th>
                <th class="text-center">Bolsa</th>
                <th class="text-center">Aux. Transp.</th>
                <th class="text-center">Bolsa Mês</th>
                <th class="text-center">Aux. Transp. Mês</th>
                <th class="text-center">Recesso</th>
                <th class="text-center">Taxa Adm</th>
                <th class="text-center">Inicio Contrato</th>
                <th class="text-center">Fim Contrato</th>
                <th class="text-center">Acertos</th>
                <th class="text-center">Total</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
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
                <tr>
                    <td style="font-weight: bold; font-size: 10px;">{{ $conteudo->termo->estagiario->nome_estagiario }}</td>
                    <td class="text-center">{{ $conteudo->dias_trabalhados }}</td>
                    <td class="text-center">R$ {{ number_format($conteudo->valor_bolsa, 2, ',', '.') }}</td>
                    <td class="text-center">R$ {{ number_format($conteudo->valor_auxilio_transporte, 2, ',', '.') }}</td>
                    <td class="text-center">R$ {{ number_format($conteudo->valor_bolsa_mes, 2, ',', '.') }}</td>
                    <td class="text-center">R$ {{ number_format($conteudo->valor_auxilio_transporte_mes, 2, ',', '.') }}
                    </td>
                    <td class="text-center">R$ {{ number_format($conteudo->valor_recesso, 2, ',', '.') }}</td>
                    <td class="text-center">R$ {{ number_format($conteudo->taxa_adm, 2, ',', '.') }}</td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($conteudo->termo->data_inicio_estagio)->format('d/m/Y') }}
                    </td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($conteudo->termo->data_fim_estagio)->format('d/m/Y') }}
                    </td>
                    <td class="text-center">R$ {{ number_format($conteudo->descontos, 2, ',', '.') }}</td>
                    <td class="text-center" style="font-weight: bold;">R$ {{ number_format($conteudo->total, 2, ',', '.') }}
                    </td>
                    <td class="text-center">
                        @if ($conteudo->termo->rescisao)
                            <span class="badge bg-danger">R</span>
                        @else
                            <span class="badge bg-success">A</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-center">
                    Total de Estagiários: {{ $conteudoFolha->count() }}
                </td>
                <td colspan="2" class="text-right">
                    Totais:
                </td>
                <td class="text-center">
                    R$ {{ number_format($conteudoFolha->sum('valor_bolsa_mes'), 2, ',', '.') }}
                </td>
                <td class="text-center">
                    R$ {{ number_format($conteudoFolha->sum('valor_auxilio_transporte_mes'), 2, ',', '.') }}
                </td>
                <td class="text-center">
                    R$ {{ number_format($conteudoFolha->sum('valor_recesso'), 2, ',', '.') }}
                </td>
                <td class="text-center">
                    R$ {{ number_format($conteudoFolha->sum('taxa_adm'), 2, ',', '.') }}
                </td>
                <td colspan="2" class="text-center"></td>
                <td class="text-right">
                    R$ {{ number_format($conteudoFolha->sum('descontos'), 2, ',', '.') }}
                </td>
                <td colspan="2" class="text-center">
                    R$ {{ number_format($conteudoFolha->sum('total'), 2, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td colspan="13" class="text-center" style="background: #e9ecef; font-weight: bold; font-size: 13px;">
                    TOTAL GERAL (Valor Bolsa + Taxa ADM): R$
                    {{ number_format($conteudoFolha->sum('total') + $conteudoFolha->sum('taxa_adm'), 2, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        Documento gerado em {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>
</body>

</html>