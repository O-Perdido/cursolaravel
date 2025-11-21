<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Código de verificação</title>
</head>

<body>
    <p>Olá, {{ $userName }}!</p>
    <p>Use o código abaixo para confirmar seu e-mail. Ele expira em 15 minutos.</p>
    <h2 style="letter-spacing:4px;">{{ $code }}</h2>
    <p>Se você não solicitou este código, ignore esta mensagem.</p>
</body>

</html>