@extends('layouts.main')

@section('title', 'Detalhes da Vaga')

@section('content')
    <style>
        .vaga-detalhe-shell {
            max-width: 1160px;
        }

        .vaga-detalhe-card {
            border: 0;
            border-radius: 28px;
            background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
            box-shadow: 0 22px 55px rgba(15, 40, 92, 0.1);
        }

        .vaga-detalhe-top {
            padding: 1.5rem 1.5rem 0;
        }

        .vaga-detalhe-body {
            padding: 1rem 1.5rem 1.5rem;
        }

        .vaga-info-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .vaga-info-box {
            border-radius: 20px;
            background: #f2f7fb;
            padding: 1rem 1.1rem;
            min-height: 100%;
        }

        .vaga-info-label {
            display: block;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7a90;
            margin-bottom: 0.35rem;
            font-weight: 700;
        }

        .vaga-info-value {
            font-size: 0.98rem;
            color: #20304d;
            font-weight: 600;
            line-height: 1.45;
        }

        .vaga-section-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #102e6c;
            margin-bottom: 0.65rem;
        }

        .vaga-candidatura-box {
            border-radius: 22px;
            background: linear-gradient(180deg, #f9fcff 0%, #eef6ff 100%);
            border: 1px solid #dbe8fb;
        }

        .vaga-candidatura-box .form-control,
        .vaga-candidatura-box .btn {
            min-height: 48px;
            border-radius: 14px;
        }

        @media (max-width: 991.98px) {
            .vaga-info-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767.98px) {
            .vaga-detalhe-shell {
                padding-left: 0.35rem;
                padding-right: 0.35rem;
            }

            .vaga-detalhe-card {
                border-radius: 22px;
            }

            .vaga-detalhe-top,
            .vaga-detalhe-body {
                padding-left: 1rem;
                padding-right: 1rem;
            }

            .vaga-detalhe-top .btn,
            .vaga-detalhe-top .d-flex {
                width: 100%;
            }
        }
    </style>

    @php
        $cidade = $vaga->empresa->cidade->nm_cidade ?? null;
        $uf = $vaga->empresa->cidade->estado->uf_estado ?? null;
        $bairro = $vaga->empresa->bairro ?? null;
        $enderecoResumido = implode(' • ', array_filter([$bairro, $cidade, $uf ? 'UF ' . $uf : null]));
    @endphp

    <div class="container py-3 vaga-detalhe-shell">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="card vaga-detalhe-card">
            <div class="vaga-detalhe-top">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
                    <a href="{{ route('vagas.publicas.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Voltar
                    </a>
                    @auth
                        @if(Auth::user()->nivel === 'estagiario')
                            <a href="{{ route('vagas.publicas.minhas-candidaturas') }}" class="btn btn-outline-primary btn-sm">Minhas candidaturas</a>
                        @endif
                    @endauth
                </div>

                <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
                    <div>
                        <div class="small text-uppercase fw-semibold text-muted mb-2" style="letter-spacing: 0.12em;">Vaga disponível</div>
                        <h3 class="mb-2 fw-bold">{{ $vaga->titulo_vaga }}</h3>
                        <div class="text-muted fw-semibold">{{ $vaga->empresa->nome_empresa ?? 'Unidade não informada' }}</div>
                    </div>
                    <div class="text-lg-end">
                        <span class="badge bg-light text-dark">{{ $vaga->numero_vaga }}</span>
                        <div class="small text-muted mt-2">Bolsa auxílio de R$ {{ number_format($vaga->valor_bolsa, 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>

            <div class="vaga-detalhe-body">
                <div class="vaga-info-grid">
                    <div class="vaga-info-box">
                        <span class="vaga-info-label">Endereço da unidade concedente</span>
                        <span class="vaga-info-value">{{ $enderecoResumido !== '' ? $enderecoResumido : 'Endereço não informado' }}</span>
                    </div>
                    <div class="vaga-info-box">
                        <span class="vaga-info-label">Bolsa auxílio</span>
                        <span class="vaga-info-value">R$ {{ number_format($vaga->valor_bolsa, 2, ',', '.') }}</span>
                    </div>
                    <div class="vaga-info-box">
                        <span class="vaga-info-label">Horário</span>
                        <span class="vaga-info-value">{{ $vaga->horario ?: 'Não informado' }}</span>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="vaga-section-title">Atividades</div>
                    <p class="mb-0">{!! nl2br(e($vaga->atividades)) !!}</p>
                </div>

                @auth
                    @if(Auth::user()->nivel === 'estagiario')
                        @if($jaCandidatado)
                            <div class="alert alert-info mb-0">Você já se candidatou para esta vaga.</div>
                        @else
                            <div class="vaga-candidatura-box p-3 p-md-4">
                                <div class="vaga-section-title mb-3">Candidatar-se</div>
                                <form action="{{ route('vagas.publicas.candidatar', $vaga->id_vaga) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="curriculo_arquivo" class="form-label">Currículo</label>
                                        <input type="file" name="curriculo_arquivo" id="curriculo_arquivo" class="form-control"
                                            accept=".pdf,.doc,.docx" required>
                                        <small class="text-muted">Formatos aceitos: PDF, IMAGEM e DOCX. Máximo de 5 MB.</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="observacoes_estagiario" class="form-label">Apresentação rápida <small
                                                class="text-muted">(opcional)</small></label>
                                        <textarea name="observacoes_estagiario" id="observacoes_estagiario" rows="3"
                                            class="form-control">{{ old('observacoes_estagiario') }}</textarea>
                                    </div>
                                    <div class="d-grid d-md-flex justify-content-md-end">
                                        <button type="submit" class="btn btn-primary px-4">Enviar candidatura</button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning mb-0">A candidatura está disponível apenas para usuários com perfil de
                            estagiário.</div>
                    @endif
                @else
                    <div class="alert alert-info mb-0">
                        Faça <a href="{{ route('login') }}">login</a> como estagiário para se candidatar a esta vaga.
                    </div>
                @endauth
            </div>
        </div>
    </div>
@endsection