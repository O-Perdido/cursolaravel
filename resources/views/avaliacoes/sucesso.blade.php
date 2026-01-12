@extends('layouts.main')

@section('title', 'Avaliação Enviada com Sucesso')

@section('content')

    <style>
        .success-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
        }

        .success-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 3rem 2rem;
            text-align: center;
            max-width: 500px;
        }

        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 1rem;
            animation: scaleIn 0.5s ease;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-card h1 {
            color: #333;
            margin-bottom: 0.5rem;
        }

        .success-card p {
            color: #666;
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .success-message {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            padding: 1rem;
            margin: 1.5rem 0;
            color: #155724;
        }

        .next-steps {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 1rem;
            text-align: left;
            margin: 1.5rem 0;
            border-radius: 4px;
            color: #0d47a1;
        }

        .next-steps strong {
            display: block;
            margin-bottom: 0.5rem;
        }

        .btn-voltar {
            display: inline-block;
            padding: 0.75rem 2rem;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-voltar:hover {
            background: #5568d3;
            text-decoration: none;
        }

        .checkmark {
            display: inline-block;
            width: 80px;
            height: 80px;
            background: #d4edda;
            border-radius: 50%;
            position: relative;
            margin-bottom: 1rem;
        }

        .checkmark::before {
            content: '';
            position: absolute;
            width: 30px;
            height: 15px;
            border: 3px solid #28a745;
            border-top: none;
            border-right: none;
            transform: rotate(-45deg);
            left: 22px;
            top: 28px;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                transform: rotate(-45deg) translate(-10px, 10px);
                opacity: 0;
            }

            to {
                transform: rotate(-45deg) translate(0, 0);
                opacity: 1;
            }
        }
    </style>

    <div class="success-container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>

            <h1>Avaliação Enviada com Sucesso!</h1>

            <div class="success-message">
                <i class="fas fa-thumbs-up"></i>
                Sua avaliação foi recebida e registrada no sistema.
            </div>

            <p>
                Obrigado por dedicar tempo para avaliar o desempenho do estagiário.
            </p>

            <div class="next-steps">
                <strong>O que acontece agora?</strong>
                <ul style="margin: 0; padding-left: 1.5rem; font-size: 0.9rem;">
                    <li>Sua resposta foi salva com segurança</li>
                    <li>Este link não pode mais ser acessado</li>
                    <li>A equipe de RH revisará sua avaliação</li>
                </ul>
            </div>

            <p style="font-size: 0.9rem; color: #999; margin-bottom: 1.5rem;">
                Se tiver dúvidas ou observações adicionais, entre em contato com a equipe de recursos humanos.
            </p>

            <button class="btn-voltar" onclick="window.location.href='/'">
                <i class="fas fa-home"></i> Ir para a Página Inicial
            </button>
        </div>
    </div>

@endsection