<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Comprovante de Local de Prova</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2933;
            line-height: 1.45;
        }

        .header {
            border-bottom: 2px solid #0f4c5c;
            padding-bottom: 10px;
            margin-bottom: 18px;
        }

        .title {
            font-size: 20px;
            font-weight: 700;
            color: #0f4c5c;
            margin: 0;
        }

        .subtitle {
            margin: 4px 0 0;
            color: #52606d;
            font-size: 12px;
        }

        .box {
            border: 1px solid #d9e2ec;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 12px;
        }

        .box h3 {
            margin: 0 0 8px;
            font-size: 13px;
            color: #0f4c5c;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .row {
            margin-bottom: 5px;
        }

        .label {
            font-weight: 700;
        }

        .muted {
            color: #7b8794;
            font-size: 11px;
        }

        .footer {
            margin-top: 18px;
            border-top: 1px solid #d9e2ec;
            padding-top: 8px;
            font-size: 11px;
            color: #52606d;
        }
    </style>
</head>

<body>
    @php
        $localAtribuido = $inscricao->localAtribuido?->processoLocal?->localProva;
        $salaAtribuida = $inscricao->salaAtribuida?->sala;
        $assento = $inscricao->salaAtribuida?->numero_assento;
    @endphp

    <div class="header">
        <h1 class="title">Comprovante de Local de Prova</h1>
        <p class="subtitle">SIGE Concursos</p>
    </div>

    <div class="box">
        <h3>Dados da Inscricao</h3>
        <div class="row"><span class="label">Numero da inscricao:</span> {{ $inscricao->numero_inscricao ?: '-' }}</div>
        <div class="row"><span class="label">Processo:</span> {{ $inscricao->processo?->titulo ?: '-' }}</div>
        <div class="row"><span class="label">Edital:</span> {{ $inscricao->processo?->numero_edital ?: '-' }}</div>
        @if($inscricao->processo?->data_prova)
            <div class="row"><span class="label">Data da prova:</span>
                {{ $inscricao->processo->data_prova->format('d/m/Y') }}</div>
        @endif
        <div class="row"><span class="label">Modalidade:</span> {{ $inscricao->modalidadeLabel() }}</div>
    </div>

    <div class="box">
        <h3>Dados do Candidato</h3>
        <div class="row"><span class="label">Nome completo:</span> {{ $candidato->nome_completo }}</div>
        <div class="row"><span class="label">CPF:</span>
            {{ preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $candidato->numero_cpf) }}</div>
    </div>

    <div class="box">
        <h3>Local de Prova</h3>
        <div class="row"><span class="label">Local:</span> {{ $localAtribuido?->nome_local ?: '-' }}</div>
        <div class="row"><span class="label">Endereco:</span>
            @if($localAtribuido)
                {{ $localAtribuido->endereco ?: '-' }}, {{ $localAtribuido->numero_endereco ?: 'S/N' }}
                @if($localAtribuido->complemento_endereco)
                    - {{ $localAtribuido->complemento_endereco }}
                @endif
            @else
                -
            @endif
        </div>
        <div class="row"><span class="label">Bairro:</span> {{ $localAtribuido?->bairro ?: '-' }}</div>
    </div>

    <div class="box">
        <h3>Sala e Assento</h3>
        <div class="row"><span class="label">Sala:</span> {{ $salaAtribuida?->nome_sala ?: '-' }}</div>
        <div class="row"><span class="label">Bloco:</span> {{ $salaAtribuida?->bloco ?: '-' }}</div>
        <div class="row"><span class="label">Assento:</span> {{ $assento ?: '-' }}</div>
    </div>

    <div class="footer">
        Documento emitido em {{ $emitidoEm->format('d/m/Y H:i:s') }}. Leve este comprovante no dia da prova, junto com
        documento oficial com foto.
    </div>
</body>

</html>