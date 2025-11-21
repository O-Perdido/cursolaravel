<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RELATORIO</title>
</head>

<style>
    @page {
        margin-left: 0.3cm;
        margin-right: 0.3cm;
        margin-top: 0.3cm;
        margin-bottom: 10px
    }

    /* Estilos gerais da tabela */

    p {
        font-size: 8pt;
    }

    table {
        width: max-content;
        border-collapse: collapse;
    }

    /* Estilos para células (th e td) */
    td {
        padding-left: 2px;
        font-size: 6pt;
    }


    /* Estilo para células específicas (opcional) */
    .destaque {
        background-color: #e0f7fa;
    }

    .center {
        text-align: center;
    }
</style>

<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif">
    <table style="margin-bottom: -20px;">
        <tr>
            <td style="width: 600px;">
                <img src="{{ $linklogo }}" alt="Logo" height="75px">
            </td>
            <td>
                <p>Relatório gerado em: {{ date('d/m/Y') }} às {{ date('H:i:s') }}
                    <br>Usuário: {{ Auth::user()->name }}
                    <br>Filtros aplicados:
                    <br>{{ $request->empresa == null ? '' : 'Unidade Concedente: ' . $empresas->where('id_empresa', $request->empresa)->first()->nome_empresa }}
                    <br>{{ $request->escola == null ? '' : 'Instituição de Ensino: ' . $escolas->where('id_escola', $request->escola)->first()->nome_escola }}
                    <br>{{ $request->estagiario == null ? '' : 'Nome do Estagiário: ' . $request->estagiario }}
                    <br><!--Intervalo de pesquisa: Data de Início: - Data Final:-->
                </p>
            </td>
        </tr>
    </table>

    <p style="text-align: center; font-weight: bold; font-size: 11pt;">RELATÓRIO DE TERMOS DE ESTÁGIO</p>
    <p>Total de termos: {{ $termos->count() }}</p>

    <table style="border: 1px solid black; margin-top: -5px; margin-bottom: 15px; word-wrap: break-word;">
        <tr style="text-align: center; font-weight: bold;">
            <td style="border: 1px solid black; text-align: center;">
                Nº TERMO
            </td>
            <td style="border: 1px solid black; text-align: center;">
                DATA DO CONTRATO
            </td>
            <td style="border: 1px solid black; width: 135px;">
                ESTAGIARIO
            </td>
            <td style="border: 1px solid black; width: 150px;">
                UNIDADE CONCEDENTE
            </td>
            <td style="border: 1px solid black; width: 150px;">
                INSTITUIÇÃO DE ENSINO
            </td>
            <td style="border: 1px solid black; width: 135px;">
                SUPERVISOR
            </td>
            <td style="border: 1px solid black; text-align: center;">
                DATA DE INICIO
            </td>
            <td style="border: 1px solid black; text-align: center;">
                DATA FINAL
            </td>
            <td style="border: 1px solid black; width: 50px; text-align: center;">
                (R$) BOLSA
            </td>
            <td style="border: 1px solid black; width: 50px; text-align: center;">
                (R$) AUXILIO TRANSPORTE
            </td>
            <td style="border: 1px solid black; width: 105px;">
                HORARIO
            </td>
            <td>
                STATUS
            </td>
        </tr>


        @foreach ($termos as $termo)
            <tr style="border: 0.5px solid black;">
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
                <td>
                    {{ $termo->horario }}
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
            </tr>
        @endforeach
    </table>
    <script type='text/php'>
            if (isset($pdf)) 
            {               
                $pdf->page_text(15, $pdf->get_height() - 15, "{PAGE_NUM} de {PAGE_COUNT}", null, 7, array(0,0,0));
            }
        </script>


</body>

</html>