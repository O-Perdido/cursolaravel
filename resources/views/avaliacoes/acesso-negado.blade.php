@extends('layouts.main')

@section('title', 'Acesso Negado')

@section('content')

    <style>
        .error-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 60vh;
        }

        .error-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 3rem 2rem;
            text-align: center;
            max-width: 500px;
        }

        .error-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }

        .error-card h1 {
            color: #333;
            margin-bottom: 0.5rem;
        }

        .error-card p {
            color: #666;
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .error-reasons {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 1rem;
            margin: 1.5rem 0;
            text-align: left;
            color: #721c24;
        }

        .error-reasons strong {
            display: block;
            margin-bottom: 0.5rem;
        }

        .error-reasons ul {
            margin: 0;
            padding-left: 1.5rem;
        }

        .error-reasons li {
            margin-bottom: 0.3rem;
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
    </style>

    <div class="error-container">
        <div class="error-card">
            <div class="error-icon">
                <i class="fas fa-lock"></i>
            </div>

            <h1>Acesso Negado</h1>

            <p>
                Desculpe, você não pode acessar esta avaliação no momento.
            </p>

            <div class="error-reasons">
                <strong>Motivos possíveis:</strong>
                <ul>
                    <li>O link expirou (avaliação já foi respondida)</li>
                    <li>O link é inválido ou foi digitado incorretamente</li>
                    <li>A avaliação foi cancelada ou removida</li>
                </ul>
            </div>

            <p style="font-size: 0.9rem; color: #999;">
                Se acredita que isso é um erro, entre em contato com o suporte.
            </p>

            <button class="btn-voltar" onclick="history.back()">
                <i class="fas fa-arrow-left"></i> Voltar
            </button>
        </div>
    </div>

@endsection