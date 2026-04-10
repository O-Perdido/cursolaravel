<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Lista de Presença por Sala</title>
    <style>
        @page {
            margin: 24px 22px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2d33;
            font-size: 10px;
        }

        .page {
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        h1 {
            margin: 0 0 6px;
            font-size: 17px;
            color: #11313a;
        }

        .subtitle {
            margin-bottom: 8px;
            color: #5f7076;
            font-size: 10px;
        }

        .header-box {
            border: 1px solid #dbe4e8;
            background: #f8fbfc;
            padding: 8px 10px;
            margin-bottom: 10px;
        }

        .header-row {
            margin-bottom: 3px;
        }

        .header-row:last-child {
            margin-bottom: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #d9e1e5;
            padding: 6px 5px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            background: #11313a;
            color: #fff;
            font-size: 9px;
            text-align: left;
        }

        td {
            line-height: 1.35;
        }

        .col-assento {
            width: 6%;
            text-align: center;
        }

        .col-inscricao {
            width: 16%;
        }

        .col-nome {
            width: 24%;
        }

        .col-cpf {
            width: 12%;
        }

        .col-assinatura {
            width: 28%;
        }

        .col-observacoes {
            width: 14%;
        }

        .muted {
            color: #5f7076;
        }

        .cell-empty {
            display: block;
            min-height: 24px;
        }
    </style>
</head>

<body>
    @foreach($salas as $item)
        @php
            $sala = $item['sala'];
            $local = $item['local'];
            $atribuicoes = $item['atribuicoes'];
        @endphp

        <div class="page">
            <h1>Lista de Presença por Sala</h1>
            <div class="subtitle">
                {{ $processo->titulo }} — Edital {{ $processo->numero_edital }}
            </div>

            <div class="header-box">
                <div class="header-row"><strong>Local de prova:</strong> {{ $local?->nome_local ?? '-' }}</div>
                <div class="header-row"><strong>Endereço:</strong>
                    {{ $local?->endereco ? $local->endereco . ', ' . $local->numero_endereco : '-' }}
                </div>
                <div class="header-row"><strong>Sala:</strong> {{ $sala?->nome_sala ?? '-' }}
                    @if($sala?->bloco)
                        — <strong>Bloco:</strong> {{ $sala->bloco }}
                    @endif
                    @if($sala?->capacidade_maxima)
                        — <strong>Capacidade:</strong> {{ $sala->capacidade_maxima }}
                    @endif
                </div>
                <div class="header-row"><strong>Total de candidatos:</strong> {{ $atribuicoes->count() }}
                    — <strong>Gerado em:</strong> {{ $dataGeracao }}
                </div>
            </div>

            <table>
                <colgroup>
                    <col class="col-assento">
                    <col class="col-inscricao">
                    <col class="col-nome">
                    <col class="col-cpf">
                    <col class="col-assinatura">
                    <col class="col-observacoes">
                </colgroup>
                <thead>
                    <tr>
                        <th class="col-assento">Assento</th>
                        <th class="col-inscricao">Nº inscrição</th>
                        <th class="col-nome">Nome</th>
                        <th class="col-cpf">CPF</th>
                        <th class="col-assinatura">Assinatura</th>
                        <th class="col-observacoes">Observações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($atribuicoes as $atribuicao)
                        @php
                            $inscricao = $atribuicao->inscricao;
                            $candidato = $inscricao?->candidato;
                            $cpf = preg_replace('/\D/', '', (string) ($candidato?->numero_cpf ?? ''));
                            $cpfFormatado = strlen($cpf) === 11
                                ? substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2)
                                : ($candidato?->numero_cpf ?? '-');
                        @endphp
                        <tr>
                            <td class="col-assento">{{ $atribuicao->numero_assento ?: '-' }}</td>
                            <td class="col-inscricao">{{ $inscricao?->numero_inscricao ?: '-' }}</td>
                            <td class="col-nome">{{ $candidato?->nome_completo ?: '-' }}</td>
                            <td class="col-cpf">{{ $cpfFormatado }}</td>
                            <td class="col-assinatura"><span class="cell-empty"></span></td>
                            <td class="col-observacoes"><span class="cell-empty"></span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p class="muted" style="margin-top: 10px;">
                Uso operacional: conferência de identidade, recepção e controle de presença no dia da prova.
            </p>
        </div>
    @endforeach
</body>

</html>