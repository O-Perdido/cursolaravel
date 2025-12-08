<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabela de Estagiário</title>

    <style>
        /* Estilos gerais da tabela */
        .titulo1 {
            text-align: center;
            font-size: 16pt;
            font-weight: bold;
        }

        .titulo2 {
            text-align: left;
            font-size: 15pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        p {
            font-size: 13pt;
            text-align: justify;
        }

        .center {
            text-align: center;
        }
    </style>
</head>

<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin-left: 25pt; margin-right: 25pt;">

    <div class="center" style="margin-top: -40px; margin-bottom: -10px;">
        <img src="{{ $linklogo }}" alt="Logo" height="85px">
    </div>
    <p style="text-align: center;">41.813.282/0001-23 Escola Brasileira de Capacitação Profissional</p>
    <p class="titulo1">TERMO DE RESCISÃO DE CONTRATO DE ESTÁGIO</p>
    <p style="text-align: center;">Referente ao contrato de estágio Nº
        {{ $rescisao->termo->numero_termo }}/{{ $rescisao->termo->ano_termo }}
    </p>

    <p>Termo de rescisão de contrato de estágio firmado entre a
        <strong>{{ $rescisao->termo->empresa->nome_empresa }}</strong>,
        o estagiário <strong>{{ $rescisao->termo->estagiario->nome_estagiario }}</strong>, com interveniência da EBCP
        Consultoria LTDA,
        tendo seu estágio iniciado em
        <strong>{{ \Carbon\Carbon::parse($rescisao->termo->data_inicio_estagio)->format('d/m/Y') }}</strong>:
    </p>

    <p class="titulo2">Cláusula I</p>

    <p>Resolvem as partes rescindir o Termo de Compromisso de Estágio tendo como último dia de vigência
        <strong>{{ \Carbon\Carbon::parse($rescisao->data_rescisao)->format('d/m/Y') }}</strong>.
    </p>

    <p>Motivo: {{ $rescisao->motivo }}</p>

    <p>E por estarem de inteiro e comum acordo com as condições e dizeres deste termo de rescisão,
        as partes assinam-no eletronicamente de acordo com a legislação vigente.</p>

    <p>{{ $rescisao->termo->empresa->cidade->nm_cidade }},
        {{ \Carbon\Carbon::parse($rescisao->data_rescisao)->format('d/m/Y') }}.
    </p>

    @if(isset($paraZapSign) && $paraZapSign && isset($signatarios))
        {{-- Versão dinâmica para ZapSign --}}
        <div style="margin-top: 30px; padding: 8px 12px; background-color: #f8f9fa; border-left: 3px solid #0d6efd;">
            <p style="margin: 0 0 5px 0; color: #0d6efd; font-size: 11px; font-weight: bold;">
                📋 ASSINATURAS DIGITAIS
            </p>
            <p style="margin: 0 0 5px 0; font-size: 9px; line-height: 1.45;">
                Este documento será assinado digitalmente através da plataforma ZapSign em conformidade com a Lei nº
                14.063/2020.
            </p>
            <p style="margin: 5px 0 3px 0; font-size: 9px; font-weight: bold;">Signatários:</p>
            <ol style="margin: 0; padding-left: 15px; font-size: 9px; line-height: 1.5;">
                @foreach($signatarios as $index => $signatario)
                    <li>
                        <strong>{{ $signatario['tipo'] }}</strong>
                        @if(!empty($signatario['nome']))
                            - {{ $signatario['nome'] }}
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>

        {{-- Espaço reservado para as assinaturas do ZapSign --}}
        <div style="margin-top: 180px;"></div>
    @else
        {{-- Versão estática padrão (para impressão/download normal) --}}
        <table style="padding-top: 100px; width: 100%;">
            <tr>
                <td style="text-align: center;">______________________________</td>
                <td></td>
                <td style="text-align: center;">______________________________</td>
            </tr>
            <tr>
                <td style="text-align: center;">Assinatura da Concedente</td>
                <td></td>
                <td style="text-align: center;">EBCP Consultoria <br> (Agente de Integração)</td>
            </tr>
        </table>
        <table style="padding-top: 100px; width: 100%;">
            <tr>
                <td style="text-align: center;">______________________________</td>
            </tr>
            <tr>
                <td style="text-align: center;">Estagiário/Representante Legal</td>
            </tr>
        </table>
    @endif

</body>

</html>