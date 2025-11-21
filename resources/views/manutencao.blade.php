<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Sistema em Manutenção</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:700,400&display=swap" rel="stylesheet">
    <style>
        /* Paleta de Cores Principal
   Azul Escuro: #102e6c
   Verde: #19b755
   Amarelo: #ecd00b
   Branco: #ffffff
   Azul Complementar: #0a1f4d
   Verde Complementar: #148a3b
   Amarelo Complementar: #bfa00a
*/

        body {
            background: linear-gradient(135deg, #102e6c 0%, #0a1f4d 100%);
            color: #22223b;
            font-family: 'Montserrat', Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            text-align: center;
            background: #fff;
            padding: 48px 36px;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(34, 34, 59, 0.10);
            max-width: 370px;
            width: 100%;
        }

        .icon {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #19b755 0%, #148a3b 100%);
            border-radius: 50%;
            width: 80px;
            height: 80px;
            margin: 0 auto 22px auto;
            font-size: 44px;
            color: #fff;
            box-shadow: 0 2px 12px rgba(251, 191, 36, 0.18);
        }

        h1 {
            color: #102e6c;
            font-size: 2.1em;
            margin-bottom: 14px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        p {
            font-size: 1.13em;
            color: #4b5563;
            margin-bottom: 0;
            line-height: 1.6;
        }

        .footer {
            margin-top: 28px;
            font-size: 0.97em;
            color: #a0aec0;
        }

        @media (max-width: 480px) {
            .container {
                padding: 28px 8px;
            }

            .icon {
                width: 60px;
                height: 60px;
                font-size: 32px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon">🛠️</div>
        <h1>SISTEMA EM MANUTENÇÃO</h1>
        <p>Estamos efetuando atualizações para aprimorar nossos serviços.<br>
            Por favor, retorne em breve.</p>
        <div class="footer">
            &copy; {{ date('Y') }} SIGE - Sistema de Integração e Gestão de Estágios. Todos os direitos reservados.
        </div>
    </div>
</body>

</html>