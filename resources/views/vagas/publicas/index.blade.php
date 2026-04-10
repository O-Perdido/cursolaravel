@extends('layouts.main')

@section('title', 'Vagas de Estágio')

@section('content')
    <style>
        .vagas-publicas-shell {
            max-width: 1240px;
        }

        .vagas-hero {
            border: 0;
            border-radius: 28px;
            overflow: hidden;
            background:
                radial-gradient(circle at top left, rgba(255, 214, 102, 0.18), transparent 32%),
                linear-gradient(135deg, #0f285c 0%, #143a82 55%, #0f766e 100%);
            color: #fff;
            box-shadow: 0 22px 55px rgba(15, 40, 92, 0.22);
        }

        .vagas-hero .form-control,
        .vagas-hero .btn {
            min-height: 52px;
            border-radius: 16px;
        }

        .vaga-card {
            height: 100%;
            border: 0;
            border-radius: 24px;
            overflow: hidden;
            background: linear-gradient(180deg, #ffffff 0%, #f7fbff 100%);
            box-shadow: 0 18px 45px rgba(15, 40, 92, 0.08);
        }

        .vaga-card-top {
            padding: 1.25rem 1.25rem 0;
        }

        .vaga-card-body {
            padding: 0 1.25rem 1.25rem;
        }

        .vaga-card-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.4rem 0.75rem;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
        }

        .vaga-card-badge-code {
            background: #eef4ff;
            color: #163c86;
        }

        .vaga-card-badge-status {
            background: #dff7ea;
            color: #177245;
        }

        .vaga-chip-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
            margin: 1rem 0 1.1rem;
        }

        .vaga-chip {
            border-radius: 18px;
            padding: 0.85rem 0.95rem;
            background: #f3f7fb;
            min-height: 100%;
        }

        .vaga-chip-label {
            display: block;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #6b7a90;
            margin-bottom: 0.35rem;
            font-weight: 700;
        }

        .vaga-chip-value {
            color: #20304d;
            font-size: 0.94rem;
            font-weight: 600;
            line-height: 1.35;
        }

        .vaga-card-actions .btn {
            min-height: 46px;
            border-radius: 14px;
            font-weight: 700;
        }

        @media (max-width: 767.98px) {
            .vagas-publicas-shell {
                padding-left: 0.35rem;
                padding-right: 0.35rem;
            }

            .vagas-hero {
                border-radius: 22px;
            }

            .vaga-chip-grid {
                grid-template-columns: 1fr;
            }

            .vaga-card-top,
            .vaga-card-body {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
    </style>

    @php
        $formatarEndereco = function ($empresa) {
            if (!$empresa) {
                return 'Endereço não informado';
            }

            $cidade = $empresa->cidade->nm_cidade ?? null;
            $uf = $empresa->cidade->estado->uf_estado ?? null;
            $bairro = $empresa->bairro ?? null;

            $partes = array_filter([$bairro, $cidade, $uf ? ' ' . $uf : null]);

            return !empty($partes) ? implode(' • ', $partes) : 'Endereço não informado';
        };
    @endphp

    <div class="container-fluid py-3 vagas-publicas-shell">
        <div class="card vagas-hero mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                    <div>
                        <div class="small text-uppercase fw-semibold mb-2" style="letter-spacing: 0.12em; opacity: 0.75;">Portal de vagas</div>
                        <h3 class="mb-2 fw-bold">Vagas de Estágio</h3>
                        <p class="mb-0" style="max-width: 720px; opacity: 0.9;">Encontre oportunidades divulgadas pelas unidades concedentes e confira as informações essenciais da vaga antes de enviar seu currículo.</p>
                    </div>
                    @auth
                        @if (Auth::user()->nivel === 'estagiario')
                            <a href="{{ route('vagas.publicas.minhas-candidaturas') }}" class="btn btn-light px-4">
                                <i class="fas fa-user-check me-1"></i> Minhas candidaturas
                            </a>
                        @endif
                    @endauth
                </div>
                <form method="GET" action="{{ route('vagas.publicas.index') }}" class="row g-2 mt-4">
                    <div class="col-lg-9">
                        <input type="text" name="busca" value="{{ request('busca') }}" class="form-control border-0"
                            placeholder="Buscar por título, unidade concedente, bairro, cidade, UF ou horário">
                    </div>
                    <div class="col-lg-3 d-grid">
                        <button type="submit" class="btn btn-warning fw-bold text-dark">Buscar</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4">
            @forelse($vagas as $vaga)
                <div class="col-md-6 col-xl-4">
                    <div class="card vaga-card">
                        <div class="vaga-card-top d-flex justify-content-between align-items-start gap-2">
                            <span class="vaga-card-badge vaga-card-badge-code">{{ $vaga->numero_vaga }}</span>
                            <span class="vaga-card-badge vaga-card-badge-status">Disponível</span>
                        </div>
                        <div class="vaga-card-body d-flex flex-column">
                            <div class="mt-3 mb-2">
                                <h5 class="card-title mb-2 fw-bold">{{ $vaga->titulo_vaga }}</h5>
                                <div class="text-muted small fw-semibold">{{ $vaga->empresa->nome_empresa ?? 'Unidade não informada' }}</div>
                            </div>

                            <div class="vaga-chip-grid">
                                <div class="vaga-chip">
                                    <span class="vaga-chip-label">Endereço</span>
                                    <span class="vaga-chip-value">{{ $formatarEndereco($vaga->empresa) }}</span>
                                </div>
                                <div class="vaga-chip">
                                    <span class="vaga-chip-label">Bolsa auxílio</span>
                                    <span class="vaga-chip-value">R$ {{ number_format($vaga->valor_bolsa, 2, ',', '.') }}</span>
                                </div>
                                <div class="vaga-chip" style="grid-column: 1 / -1;">
                                    <span class="vaga-chip-label">Horário</span>
                                    <span class="vaga-chip-value">{{ $vaga->horario ?: 'Não informado' }}</span>
                                </div>
                            </div>

                            <p class="text-muted small flex-grow-1 mb-3">{{ \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($vaga->atividades))), 150) }}</p>

                            <div class="vaga-card-actions mt-auto d-grid">
                            <a href="{{ route('vagas.publicas.show', $vaga->id_vaga) }}"
                                class="btn btn-outline-primary">Ver detalhes</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border-0 shadow-sm text-muted mb-0" style="border-radius: 20px;">
                        Nenhuma vaga pública encontrada no momento.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="pt-3">
            {{ $vagas->withQueryString()->links() }}
        </div>
    </div>
@endsection