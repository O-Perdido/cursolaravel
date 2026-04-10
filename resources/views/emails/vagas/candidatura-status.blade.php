<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Atualização de candidatura</title>
</head>

<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.5;">
    <p>Olá, {{ $candidatura->estagiario->nome_estagiario ?? 'estagiário(a)' }}.</p>

    <p>
        Houve uma atualização na sua candidatura para a vaga
        <strong>{{ $candidatura->vaga->titulo_vaga ?? $candidatura->vaga->numero_vaga }}</strong>.
    </p>

    <p>
        <strong>Novo status:</strong> {{ $statusLabel }}<br>
        <strong>Unidade concedente:</strong> {{ $candidatura->vaga->empresa->nome_empresa ?? 'Não informada' }}
    </p>

    @if(!empty($candidatura->observacoes_internas))
        <p>
            <strong>Observação:</strong><br>
            {{ $candidatura->observacoes_internas }}
        </p>
    @endif

    <p>Se necessário, acompanhe suas candidaturas dentro do portal do estagiário.</p>
</body>

</html>