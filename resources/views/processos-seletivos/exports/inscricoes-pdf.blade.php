<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscrições - {{ $processo->titulo }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            color: #102e6c;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #102e6c;
        }

        .header h1 {
            font-size: 18px;
            color: #102e6c;
            margin-bottom: 5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header h2 {
            font-size: 13px;
            color: #19b755;
            margin-bottom: 3px;
            font-weight: 600;
        }

        .info-box {
            background-color: #f7f9fc;
            padding: 8px;
            margin-bottom: 12px;
            border-left: 4px solid #ecd00b;
            border-radius: 4px;
            font-size: 9px;
        }

        .info-box .row {
            margin-bottom: 3px;
        }

        .info-box strong {
            color: #102e6c;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table thead {
            background-color: #102e6c;
            color: white;
        }

        table thead th {
            padding: 6px 4px;
            text-align: left;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #102e6c;
        }

        table tbody td {
            padding: 5px 4px;
            border: 1px solid #dfe6f3;
            font-size: 9px;
            vertical-align: top;
        }

        table tbody tr:nth-child(even) {
            background-color: #f7f9fc;
        }

        table tbody tr:hover {
            background-color: #e8f0fe;
        }

        .status {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status.inscrito {
            background-color: #ffc107;
            color: #000;
        }

        .status.deferido {
            background-color: #28a745;
            color: white;
        }

        .status.indeferido {
            background-color: #dc3545;
            color: white;
        }

        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #dfe6f3;
            text-align: center;
            font-size: 8px;
            color: #0a1f4d;
        }

        .total-box {
            background-color: #102e6c;
            color: white;
            padding: 6px;
            margin-top: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            border-radius: 4px;
            border-left: 4px solid #ecd00b;
        }

        .text-center {
            text-align: center;
        }

        .text-nowrap {
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>RELAÇÃO DE INSCRIÇÕES</h1>
        <h2>{{ $processo->titulo }}</h2>
    </div>

    <div class="info-box">
        <div class="row">
            <strong>Processo:</strong> {{ $processo->titulo }}
        </div>
        <div class="row">
            <strong>Período:</strong> {{ \Carbon\Carbon::parse($processo->data_inicio)->format('d/m/Y') }} a
            {{ \Carbon\Carbon::parse($processo->data_fim)->format('d/m/Y') }}
        </div>
        <div class="row">
            <strong>Filtro de Status:</strong> {{ $statusFiltro }}
        </div>
        <div class="row">
            <strong>Data de Exportação:</strong> {{ $dataExportacao }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                @if(in_array('numero_inscricao', $colunas))
                    <th style="width: 10%;">{{ $colunasLabels['numero_inscricao'] }}</th>
                @endif
                @if(in_array('nome', $colunas))
                    <th style="width: 18%;">{{ $colunasLabels['nome'] }}</th>
                @endif
                @if(in_array('email', $colunas))
                    <th style="width: 15%;">{{ $colunasLabels['email'] }}</th>
                @endif
                @if(in_array('telefone', $colunas))
                    <th style="width: 10%;">{{ $colunasLabels['telefone'] }}</th>
                @endif
                @if(in_array('cpf', $colunas))
                    <th style="width: 10%;">{{ $colunasLabels['cpf'] }}</th>
                @endif
                @if(in_array('curso', $colunas))
                    <th style="width: 12%;">{{ $colunasLabels['curso'] }}</th>
                @endif
                @if(in_array('instituicao', $colunas))
                    <th style="width: 12%;">{{ $colunasLabels['instituicao'] }}</th>
                @endif
                @if(in_array('status', $colunas))
                    <th style="width: 8%;" class="text-center">{{ $colunasLabels['status'] }}</th>
                @endif
                @if(in_array('data_inscricao', $colunas))
                    <th style="width: 10%;" class="text-center">{{ $colunasLabels['data_inscricao'] }}</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($inscricoes as $inscricao)
                <tr>
                    @if(in_array('numero_inscricao', $colunas))
                        <td class="text-nowrap">{{ $inscricao->numero_inscricao ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('nome', $colunas))
                        <td>{{ $inscricao->estagiario->nome_estagiario ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('email', $colunas))
                        <td>{{ $inscricao->estagiario->email ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('telefone', $colunas))
                        <td class="text-nowrap">
                            {{ $inscricao->estagiario->numero_celular ?? $inscricao->estagiario->numero_telefone ?? 'N/A' }}
                        </td>
                    @endif
                    @if(in_array('cpf', $colunas))
                        <td class="text-nowrap">{{ $inscricao->estagiario->numero_cpf ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('curso', $colunas))
                        <td>{{ $inscricao->estagiario->curso ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('instituicao', $colunas))
                        <td>{{ $inscricao->estagiario->instituicao_ensino ?? 'N/A' }}</td>
                    @endif
                    @if(in_array('status', $colunas))
                        <td class="text-center">
                            <span class="status {{ $inscricao->status_inscricao }}">
                                {{ ucfirst($inscricao->status_inscricao) }}
                            </span>
                        </td>
                    @endif
                    @if(in_array('data_inscricao', $colunas))
                        <td class="text-center text-nowrap">
                            {{ \Carbon\Carbon::parse($inscricao->created_at)->format('d/m/Y H:i') }}
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($colunas) }}" class="text-center" style="padding: 20px;">
                        Nenhuma inscrição encontrada com os filtros selecionados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-box">
        Total de Inscrições: {{ $inscricoes->count() }}
    </div>

    <div class="footer">
        <p>Documento gerado automaticamente pelo sistema SIGE em {{ $dataExportacao }}</p>
        <p>Este documento é válido sem assinatura para fins de conferência e controle interno</p>
    </div>
</body>

</html>