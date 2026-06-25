<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Inscrições - {{ $processo->titulo }}</title>
    <style>
        @page {
            margin: 40px 30px;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8px;
            color: #334155; /* Slate-700 */
            line-height: 1.4;
        }

        /* Top Header Grid */
        .pdf-header {
            margin-bottom: 15px;
            border-bottom: 2px solid #102e6c;
            padding-bottom: 10px;
        }

        .pdf-header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .pdf-header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .title-area h1 {
            font-size: 15px;
            color: #102e6c;
            font-weight: bold;
            margin: 0 0 3px 0;
            letter-spacing: 0.5px;
        }

        .title-area h2 {
            font-size: 10.5px;
            color: #475569;
            margin: 0;
            font-weight: normal;
        }

        .logo-area {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            color: #102e6c;
        }

        .logo-subtitle {
            font-size: 7px;
            color: #64748b;
            font-weight: normal;
            margin-top: 2px;
            letter-spacing: 0.2px;
        }

        /* Metadata info box */
        .metadata-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }

        .metadata-table td {
            padding: 8px 12px;
            border: none;
            width: 25%;
            vertical-align: top;
        }

        .meta-label {
            font-size: 6.5px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .meta-value {
            font-size: 8.5px;
            color: #0f172a;
            font-weight: bold;
        }

        /* Main Records Table */
        .records-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        .records-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #cbd5e1;
            border-top: 1px solid #e2e8f0;
            border-left: none;
            border-right: none;
            padding: 7px 6px;
            text-align: left;
        }

        .records-table td {
            padding: 6px;
            border-bottom: 1px solid #e2e8f0;
            border-left: none;
            border-right: none;
            font-size: 8px;
            color: #334155;
            vertical-align: middle;
        }

        .records-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }

        /* Status Pills */
        .status-pill {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 12px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            letter-spacing: 0.3px;
        }

        .status-pill.inscrito {
            background-color: #fefbe7;
            color: #a16207;
            border: 1px solid #fde047;
        }

        .status-pill.deferido {
            background-color: #e8f8ef;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .status-pill.indeferido {
            background-color: #fdebee;
            color: #be123c;
            border: 1px solid #fecdd3;
        }

        /* Monospace Code */
        .code-text {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            color: #0f172a;
            font-size: 8.5px;
        }

        /* Summary & Footer */
        .summary-wrapper {
            margin-top: 15px;
            text-align: right;
        }

        .summary-badge {
            display: inline-block;
            background-color: #102e6c;
            color: white;
            padding: 6px 14px;
            font-weight: bold;
            font-size: 9px;
            border-radius: 4px;
            border-left: 3px solid #ecd00b;
        }

        .footer-info {
            margin-top: 25px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            text-align: center;
            color: #64748b;
            font-size: 7px;
        }

        .footer-info p {
            margin: 2px 0;
        }

        .text-center {
            text-align: center !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-nowrap {
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <!-- Top Header -->
    <div class="pdf-header">
        <table class="pdf-header-table">
            <tr>
                <td class="title-area">
                    <h1>RELAÇÃO DE INSCRIÇÕES</h1>
                    <h2>{{ $processo->titulo }}</h2>
                </td>
                <td class="logo-area">
                    <span>EBCP SIGE</span>
                    <div class="logo-subtitle">Integração e Gestão de Estágios</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Metadata Grid -->
    <table class="metadata-table">
        <tr>
            <td>
                <div class="meta-label">Processo Seletivo</div>
                <div class="meta-value">{{ $processo->numero_processo }}</div>
            </td>
            <td>
                <div class="meta-label">Período de Inscrição</div>
                <div class="meta-value">
                    {{ \Carbon\Carbon::parse($processo->data_inicio)->format('d/m/Y') }} a
                    {{ \Carbon\Carbon::parse($processo->data_fim)->format('d/m/Y') }}
                </div>
            </td>
            <td>
                <div class="meta-label">Filtro de Status</div>
                <div class="meta-value">{{ $statusFiltro }}</div>
            </td>
            <td>
                <div class="meta-label">Exportado em</div>
                <div class="meta-value">{{ $dataExportacao }}</div>
            </td>
        </tr>
    </table>

    <!-- Main Table -->
    <table class="records-table">
        <thead>
            <tr>
                @if(in_array('numero_inscricao', $colunas))
                    <th style="width: 10%;">Nº Inscrição</th>
                @endif
                @if(in_array('nome', $colunas))
                    <th>Candidato</th>
                @endif
                @if(in_array('email', $colunas))
                    <th>E-mail</th>
                @endif
                @if(in_array('telefone', $colunas))
                    <th style="width: 11%;">Telefone</th>
                @endif
                @if(in_array('cpf', $colunas))
                    <th style="width: 11%;">CPF</th>
                @endif
                @if(in_array('curso', $colunas))
                    <th>Curso</th>
                @endif
                @if(in_array('instituicao', $colunas))
                    <th>Instituição de Ensino</th>
                @endif
                @if(in_array('status', $colunas))
                    <th style="width: 9%;" class="text-center">Status</th>
                @endif
                @if(in_array('data_inscricao', $colunas))
                    <th style="width: 11%;" class="text-center">Data Inscrição</th>
                @endif
                @if(in_array('data_nascimento', $colunas))
                    <th style="width: 10%;" class="text-center">Nascimento</th>
                @endif
                @if(in_array('idade', $colunas))
                    <th style="width: 6%;" class="text-center">Idade</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse($inscricoes as $inscricao)
                <tr>
                    @if(in_array('numero_inscricao', $colunas))
                        <td class="code-text text-nowrap">{{ $inscricao->numero_inscricao ?? '—' }}</td>
                    @endif
                    @if(in_array('nome', $colunas))
                        <td style="font-weight: bold; color: #0f172a;">{{ $inscricao->estagiario->nome_estagiario ?? '—' }}</td>
                    @endif
                    @if(in_array('email', $colunas))
                        <td>{{ $inscricao->estagiario->email ?? '—' }}</td>
                    @endif
                    @if(in_array('telefone', $colunas))
                        <td class="text-nowrap">
                            {{ $inscricao->estagiario->numero_celular ?? $inscricao->estagiario->numero_telefone ?? '—' }}
                        </td>
                    @endif
                    @if(in_array('cpf', $colunas))
                        <td class="text-nowrap">{{ $inscricao->estagiario->numero_cpf ?? '—' }}</td>
                    @endif
                    @if(in_array('curso', $colunas))
                        <td>{{ $inscricao->estagiario->curso ?? '—' }}</td>
                    @endif
                    @if(in_array('instituicao', $colunas))
                        <td>{{ $inscricao->estagiario->instituicao_ensino ?? '—' }}</td>
                    @endif
                    @if(in_array('status', $colunas))
                        <td class="text-center">
                            <span class="status-pill {{ $inscricao->status_inscricao }}">
                                @switch($inscricao->status_inscricao)
                                    @case('deferido')
                                        Deferido
                                        @break
                                    @case('indeferido')
                                        Indeferido
                                        @break
                                    @case('inscrito')
                                        Pendente
                                        @break
                                    @default
                                        {{ ucfirst($inscricao->status_inscricao) }}
                                @endswitch
                            </span>
                        </td>
                    @endif
                    @if(in_array('data_inscricao', $colunas))
                        <td class="text-center text-nowrap">
                            {{ \Carbon\Carbon::parse($inscricao->created_at)->format('d/m/Y H:i') }}
                        </td>
                    @endif
                    @if(in_array('data_nascimento', $colunas))
                        <td class="text-center text-nowrap">
                            {{ $inscricao->estagiario->data_nascimento ?? '—' }}
                        </td>
                    @endif
                    @if(in_array('idade', $colunas))
                        <td class="text-center">
                            {{ $inscricao->estagiario->data_nascimento ? \Carbon\Carbon::createFromFormat('d/m/Y', $inscricao->estagiario->data_nascimento)->age . ' anos' : '—' }}
                        </td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="100" class="text-center" style="padding: 20px; color: #64748b;">
                        Nenhuma inscrição encontrada com os filtros selecionados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary Total -->
    <div class="summary-wrapper">
        <div class="summary-badge">
            Total de Inscritos: {{ $inscricoes->count() }}
        </div>
    </div>

    <!-- Page Footer -->
    <div class="footer-info">
        <p>Documento gerado eletronicamente pelo sistema SIGE em {{ $dataExportacao }}</p>
        <p>Disponibilizado para fins de auditoria, controle interno e divulgação oficial.</p>
    </div>
</body>

</html>