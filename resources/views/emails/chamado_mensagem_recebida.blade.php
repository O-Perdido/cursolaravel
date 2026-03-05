<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo retorno no chamado</title>
</head>

<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="margin-bottom: 8px;">Seu chamado {{ $chamado->protocolo }} recebeu um novo retorno</h2>

    <p style="margin-top: 0;">O usuário <strong>{{ $remetenteNome }}</strong> enviou uma nova mensagem.</p>

    <div style="background: #f5f7fb; border-left: 4px solid #2b6cb0; padding: 12px; margin: 12px 0;">
        <strong>Mensagem:</strong><br>
        {!! nl2br(e(\Illuminate\Support\Str::limit($mensagem->mensagem, 600))) !!}
    </div>

    <p>Acesse a plataforma para visualizar o chamado completo:</p>
    <p>
        <a href="{{ $urlChamado }}"
            style="display:inline-block; background:#2b6cb0; color:#fff; text-decoration:none; padding:10px 14px; border-radius:4px;">
            Abrir chamado
        </a>
    </p>

    <p style="font-size: 13px; color: #666;">Este e-mail foi enviado automaticamente pelo sistema SIGE.</p>
</body>

</html>