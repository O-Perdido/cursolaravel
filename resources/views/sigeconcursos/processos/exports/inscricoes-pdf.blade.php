<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Relatório de Inscrições</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2d33;
        }

        h1 {
            font-size: 18px;
            margin: 0 0 4px;
            color: #11313a;
        }

        .subtitle {
            font-size: 11px;
            color: #5c7078;
            margin-bottom: 14px;
        }

        .meta {
            margin-bottom: 14px;
            padding: 10px 12px;
            border: 1px solid #dbe4e8;
            background: #f8fbfc;
        }

        .meta-row {
            margin-bottom: 4px;
        }

        .meta-row:last-child {
            margin-bottom: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #d9e1e5;
            padding: 6px 7px;
            vertical-align: top;
        }

        th {
            background: #11313a;
            color: #fff;
            text-align: left;
            font-size: 10px;
        }

        tbody tr:nth-child(even) {
            background: #f7fafb;
        }

        .muted {
            color: #6f7f86;
        }

        .footer {
            margin-top: 10px;
            font-size: 10px;
            color: #6f7f86;
        }
    </style>
</head>

<body>
    <h1>Relatório de Inscrições</h1>
    <div class="subtitle">
        {{ $processo->titulo }} — Edital {{ $processo->numero_edital }}
        @if($processo->empresa)
            — {{ $processo->empresa->nome_razao_social }}
        @endif
    </div>

    <div class="meta">
        <div class="meta-row"><strong>Gerado em:</strong> {{ $dataExportacao }}</div>
        <div class="meta-row"><strong>Total de registros:</strong> {{ $linhas->count() }}</div>
        <div class="meta-row"><strong>Filtros aplicados:</strong>
            @if(empty($filtrosAplicados))
                <span class="muted">nenhum filtro adicional</span>
            @else
                {{ collect($filtrosAplicados)->map(fn($valor, $chave) => $chave . ': ' . $valor)->implode(' | ') }}
            @endif
        </div>
        <div class="meta-row"><strong>CPF:</strong> {{ $cpfCensurado ? 'censurado' : 'completo' }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nº inscrição</th>
                <th>Nome</th>
                <th>CPF</th>
                <th>Modalidade</th>
                <th>Local de prova</th>
                <th>Status inscrição</th>
                <th>Status isenção</th>
            </tr>
        </thead>
        <tbody>
            @foreach($linhas as $linha)
                <tr>
                    <td>{{ $linha['numero_inscricao'] }}</td>
                    <td>{{ $linha['nome'] }}</td>
                    <td>{{ $linha['cpf'] }}</td>
                    <td>{{ $linha['modalidade'] }}</td>
                    <td>{{ $linha['local_prova'] }}</td>
                    <td>{{ $linha['status_inscricao'] }}</td>
                    <td>{{ $linha['status_isencao'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($cpfCensurado)
        <div class="footer">
            CPF apresentado com mascaramento parcial para uso mais seguro em relatórios e publicações.
        </div>
    @endif
</body>

</html>