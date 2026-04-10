@extends('layouts.main')

@section('title', 'Minhas Candidaturas em Vagas')

@section('content')
    <style>
        .minhas-candidaturas-shell {
            max-width: 1160px;
        }

        .minhas-candidaturas-header {
            border: 0;
            border-radius: 26px;
            background: linear-gradient(135deg, #0f285c 0%, #16438f 100%);
            color: #fff;
            box-shadow: 0 22px 55px rgba(15, 40, 92, 0.18);
        }

        .minhas-candidaturas-card {
            border: 0;
            border-radius: 24px;
            box-shadow: 0 18px 40px rgba(15, 40, 92, 0.08);
            overflow: hidden;
        }

        .candidatura-mobile-card {
            border-radius: 20px;
            border: 1px solid #e6eef8;
            background: #fff;
            box-shadow: 0 12px 28px rgba(15, 40, 92, 0.06);
        }

        @media (max-width: 767.98px) {
            .minhas-candidaturas-shell {
                padding-left: 0.35rem;
                padding-right: 0.35rem;
            }
        }
    </style>

    <div class="container-fluid py-3 minhas-candidaturas-shell">
        <div class="card minhas-candidaturas-header mb-4">
            <div class="card-body p-4 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
                <div>
                    <div class="small text-uppercase fw-semibold mb-2" style="letter-spacing: 0.12em; opacity: 0.75;">Área
                        do estagiário</div>
                    <h3 class="mb-1 fw-bold">Minhas candidaturas em vagas</h3>
                    <p class="mb-0" style="opacity: 0.88;">Acompanhe o andamento das oportunidades para as quais você já
                        enviou currículo.</p>
                </div>
                <a href="{{ route('vagas.publicas.index') }}" class="btn btn-light px-4">Ver vagas</a>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3 d-none">
            <div>
                <h3 class="mb-1">Minhas candidaturas em vagas</h3>
                <p class="text-muted mb-0">Acompanhe a evolução das suas candidaturas.</p>
            </div>
            <a href="{{ route('vagas.publicas.index') }}" class="btn btn-outline-primary">Ver vagas</a>
        </div>

        <div class="card minhas-candidaturas-card d-none d-md-block">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Vaga</th>
                            <th>Unidade</th>
                            <th>Status</th>
                            <th>Data</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($candidaturas as $candidatura)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $candidatura->vaga->titulo_vaga ?? '-' }}</div>
                                    <div class="small text-muted">{{ $candidatura->vaga->numero_vaga ?? '-' }}</div>
                                </td>
                                <td>{{ $candidatura->vaga->empresa->nome_empresa ?? '-' }}</td>
                                <td><span class="badge bg-info text-dark">{{ $candidatura->status_label }}</span></td>
                                <td>{{ $candidatura->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <a href="{{ route('vagas.publicas.show', $candidatura->fk_id_vaga) }}"
                                        class="btn btn-outline-secondary btn-sm">Abrir vaga</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Você ainda não possui candidaturas em vagas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-grid gap-3 d-md-none">
            @forelse($candidaturas as $candidatura)
                <div class="candidatura-mobile-card p-3">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <div class="fw-semibold">{{ $candidatura->vaga->titulo_vaga ?? '-' }}</div>
                            <div class="small text-muted">{{ $candidatura->vaga->empresa->nome_empresa ?? '-' }}</div>
                        </div>
                        <span class="badge bg-info text-dark">{{ $candidatura->status_label }}</span>
                    </div>
                    <div class="small text-muted mb-3">{{ $candidatura->created_at?->format('d/m/Y H:i') }}</div>
                    <div class="d-grid">
                        <a href="{{ route('vagas.publicas.show', $candidatura->fk_id_vaga) }}"
                            class="btn btn-outline-secondary btn-sm">Abrir vaga</a>
                    </div>
                </div>
            @empty
                <div class="alert alert-light border-0 shadow-sm text-muted mb-0" style="border-radius: 20px;">Você ainda não
                    possui candidaturas em vagas.</div>
            @endforelse
        </div>

        <div class="pt-3">{{ $candidaturas->links() }}</div>
    </div>
@endsection