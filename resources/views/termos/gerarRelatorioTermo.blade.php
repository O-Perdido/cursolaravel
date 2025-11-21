<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RELATORIO</title>
</head>

<body>

    <table>

        <tr style="height: 20px">
            <td colspan="12" style="text-align: center; padding-left: 895px;">
                <img src="{{ $linklogo }}" alt="Logo" height="80px">
            </td>
        </tr>


        <tr style="text-align: center; font-weight: bold;">
            <td style="width: 110px; word-wrap: break-word; text-align: center;">
                Nº TERMO
            </td>
            <td style="width: 110px; word-wrap: break-word; text-align: center;">
                DATA DO CONTRATO
            </td>
            <td style="width: 230px; word-wrap: break-word; text-align: center;">
                ESTAGIARIO
            </td>
            <td style="width: 230px; word-wrap: break-word; text-align: center;">
                UNIDADE CONCEDENTE
            </td>
            <td style="width:  230px; word-wrap: break-word; text-align: center;">
                INSTITUIÇÃO DE ENSINO
            </td>
            <td style="width: 230px; word-wrap: break-word; text-align: center;">
                SUPERVISOR
            </td>
            <td style="width: 110px; word-wrap: break-word; text-align: center;">
                DATA DE INICIO
            </td>
            <td style="width: 110px; word-wrap: break-word; text-align: center;">
                DATA FINAL
            </td>
            <td style="width: 110px; word-wrap: break-word; text-align: center;">
                (R$) BOLSA
            </td>
            <td style="width: 110px; word-wrap: break-word; text-align: center;">
                (R$) AUXILIO TRANSPORTE
            </td>
            <td style="width: 300px; border: 1px solid black; text-align: center;">
                LOTAÇÃO
            </td>
            <td style="width: 300px; border: 1px solid black; text-align: center;">
                LOCAL
            </td>
            <td style="width: 110px; word-wrap: break-word; text-align: center;">
                STATUS
            </td>
            <td style="width: 350px; word-wrap: break-word; text-align: center;">
                HORARIO
            </td>
        </tr>

        @foreach ($termos as $termo)
            <tr style="border: 0.5px solid-black;">
                <td style="text-align: center;">
                    {{ $termo->numero_termo }}/{{ $termo->ano_termo }}
                </td>
                <td style="text-align: center;">
                    {{ \Carbon\Carbon::parse($termo->data)->format('d/m/Y') }}
                </td>
                <td>
                    {{ $termo->estagiario->nome_estagiario }}
                </td>
                <td>
                    {{ $termo->empresa->nome_empresa }}
                </td>
                <td>
                    {{ $termo->escola->nome_escola }}
                </td>
                <td>
                    {{ $termo->supervisor->nome_supervisor }}
                </td>
                <td style="text-align: center;">
                    {{ \Carbon\Carbon::parse($termo->data_inicio_estagio)->format('d/m/Y') }}
                </td>
                <td style="text-align: center;">
                    {{ \Carbon\Carbon::parse($termo->data_fim_estagio)->format('d/m/Y') }}
                </td>
                <td style="text-align: center;">
                    R$ {{ $termo->valor_bolsa }}
                </td>
                <td style="text-align: center;">
                    R$ {{ $termo->auxilio_transporte }}
                </td>
                <td style="text-align: left;">
                    {{ $termo->lotacao }}
                </td>
                <td style="text-align: left;">
                    {{ $termo->local->descricao ?? '' }}
                </td>
                <td style="text-align: center;">
                    @php
                        $isVencido = \Carbon\Carbon::parse($termo->data_fim_estagio)->isPast() && !$termo->rescisao;
                    @endphp
                    @if ($termo->rescisao)
                        <span>Rescindido</span>
                    @elseif ($isVencido)
                        <span>Vencido</span>
                    @else
                        <span>Ativo</span>
                    @endif
                </td>
                <td>
                    {{ $termo->horario }}
                </td>

            </tr>
        @endforeach
    </table>

</body>

</html>