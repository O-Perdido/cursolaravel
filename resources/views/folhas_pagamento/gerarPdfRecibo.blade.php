<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Recibo de Pagamento Bolsa-Auxílio</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            color: #222;
            margin: 0;
            padding: 0;
            font-size: 10px;
            background: #fff;
        }

        .container {
            margin: 0 auto;
            padding: 8px 10px 0 10px;
            max-width: 700px;
            min-height: 320px;
            border-bottom: 1px solid #ccc;
        }

        .logo-topo {
            text-align: center;
            margin-bottom: 6px;
        }

        .logo-topo img {
            max-width: 400px;
            height: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            border-bottom: 1px solid #222;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .title {
            font-size: 13px;
            font-weight: bold;
            color: #222;
        }

        .referencia {
            font-size: 10px;
            color: #222;
            font-weight: bold;
            text-align: right;
        }

        .section {
            border: 1px solid #bbb;
            border-radius: 3px;
            margin-bottom: 7px;
            padding: 6px 8px;
            background: #fafafa;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #222;
            margin-bottom: 4px;
            margin-top: 0;
            border-bottom: 1px solid #bbb;
            padding-bottom: 2px;
        }

        .info-table,
        .values-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .info-table td {
            padding: 2px 4px;
            font-size: 10px;
            border: none;
        }

        .info-table .label {
            font-weight: bold;
            color: #222;
            width: 110px;
        }

        .values-table th,
        .values-table td {
            border: 1px solid #bbb;
            padding: 3px 4px;
            font-size: 10px;
        }

        .values-table th {
            background: #f5f5f5;
            color: #222;
            font-weight: bold;
        }

        .values-table tfoot td {
            background: #fafafa;
            font-weight: bold;
        }

        .estagiario {
            font-size: 10px;
            font-weight: bold;
            color: #222;
        }

        .footer {
            margin-top: 8px;
            text-align: right;
            font-size: 9px;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo-topo">
            <img src="{{ $linklogo }}" alt="Logo EBCP">
        </div>
        <div class="header">
            <div class="title">Recibo de Pagamento Bolsa-Auxílio</div>
            <div class="referencia">
                Mês Referência: {{ str_pad($folha->mes_referencia, 2, '0', STR_PAD_LEFT) }}/{{ $folha->ano_referencia }}
            </div>
        </div>

        <div class="section">
            <div class="section-title">Empresa / Estagiário</div>
            <table class="info-table" style="width:100%;">
                <tr>
                    <!-- Dados da Empresa -->
                    <td style="vertical-align:top; width:50%;">
                        <table style="width:100%;">
                            <tr>
                                <td class="label">Unidade Concedente:</td>
                                <td>{{ $folha->empresa->nome_empresa }}</td>
                            </tr>
                            <tr>
                                <td class="label">CNPJ:</td>
                                <td>{{ $folha->empresa->numero_cnpj }}</td>
                            </tr>
                            <tr>
                                <td class="label">Agente Integração:</td>
                                <td>ESCOLA BRASILEIRA DE CAPACITAÇÃO PROFISSIONAL</td>
                            </tr>
                            <tr>
                                <td class="label">Local:</td>
                                <td>{{ $folha->local ? $folha->local->descricao : 'Todos os locais / Não especificado' }}
                                </td>
                            </tr>
                        </table>
                    </td>
                    <!-- Dados do Estagiário -->
                    <td style="vertical-align:top; width:50%;">
                        <table style="width:100%;">
                            <tr>
                                <td class="label">Estagiário(a):</td>
                                <td class="estagiario">{{ $conteudo->termo->estagiario->nome_estagiario }}</td>
                            </tr>
                            <tr>
                                <td class="label">Início Estágio:</td>
                                <td>{{ \Carbon\Carbon::parse($conteudo->termo->data_inicio_estagio)->format('d/m/Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="label">Fim Estágio:</td>
                                <td>{{ \Carbon\Carbon::parse($conteudo->termo->data_fim_estagio)->format('d/m/Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="label">Pix:</td>
                                <td>
                                    @if(empty($conteudo->termo->estagiario->chave_pix))
                                        Chave PIX não informada
                                    @else
                                        {{ $conteudo->termo->estagiario->chave_pix }}
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Valores</div>
            <table class="values-table">
                <thead>
                    <tr>
                        <th>Cód.</th>
                        <th>Descrição</th>
                        <th>Vencimentos</th>
                        <th>
                            @if ($conteudo->descontos > 0)
                                Complementação Bolsa
                            @else
                                Descontos
                            @endif
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Bolsa Auxílio</td>
                        <td>R$ {{ number_format($conteudo->valor_bolsa_mes, 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($conteudo->descontos, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Auxílio Transporte</td>
                        <td>R$ {{ number_format($conteudo->valor_auxilio_transporte_mes, 2, ',', '.') }}</td>
                        <td></td>
                    </tr>
                    @if($conteudo->valor_recesso > 0)
                        <tr>
                            <td>3</td>
                            <td>Recesso</td>
                            <td>R$ {{ number_format($conteudo->valor_recesso, 2, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align:right;">Total Vencimentos</td>
                        <td>
                            R$
                            {{ number_format($conteudo->valor_bolsa_mes + $conteudo->valor_auxilio_transporte_mes + $conteudo->valor_recesso, 2, ',', '.') }}
                        </td>
                        <td>
                            R$ {{ number_format($conteudo->descontos, 2, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:right;">Valor Líquido</td>
                        <td colspan="2">
                            <strong>
                                R$
                                {{ number_format(($conteudo->valor_bolsa_mes + $conteudo->valor_auxilio_transporte_mes + $conteudo->valor_recesso) + $conteudo->descontos, 2, ',', '.') }}
                            </strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="footer">
            Documento gerado em {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
        </div>
    </div>
</body>

</html>