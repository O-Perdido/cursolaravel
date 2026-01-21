@extends('layouts.main')

@section('title', 'Inscrições do Processo Seletivo')

@section('content')
<div class="container-fluid py-3">
    <div class="card shadow-sm mb-3">
        <div class="card-body pb-3 pt-3">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                <div>
                    <h4 class="mb-1">{{ $processo->titulo }}</h4>
                    <div class="text-muted small">
                        <span class="me-2"><strong>Número:</strong> {{ $processo->numero_processo }}</span>
                        <span class="me-2"><strong>Empresa:</strong> {{ $processo->empresa->nome_empresa }}</span>
                        <span class="badge @switch($processo->status) @case('rascunho') bg-secondary @break @case('aberto') bg-success @break @case('inscricoes') bg-info @break @case('encerrado') bg-warning @break @case('finalizado') bg-dark @break @default bg-light text-dark @endswitch align-middle ms-1">{{ ucfirst($processo->status) }}</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <div class="d-flex align-items-center me-2 text-primary fw-semibold">
                        <i class="fas fa-users me-2"></i>{{ $processo->inscricoesCount() }} inscrito(s)
                    </div>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="fas fa-download me-1"></i> Exportar
                    </button>
                    <a href="{{ route('processos-seletivos.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="fas fa-list me-2 text-primary"></i>Inscrições</h6>
            <span class="text-muted small">Atualizado em {{ now()->format('d/m/Y H:i') }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-nowrap">ID</th>
                        <th>Estagiário</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Curso</th>
                        <th>Status</th>
                        <th class="text-nowrap">Inscrição</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inscricoes as $inscricao)
                        <tr>
                            <td class="text-muted">{{ $inscricao->id_inscricao }}</td>
                            <td class="fw-semibold">
                                <div class="d-flex align-items-center gap-2">
                                    {{ $inscricao->estagiario->nome_estagiario }}
                                    <a href="{{ route('estagiario.show', $inscricao->fk_id_estagiario) }}" target="_blank" class="btn btn-sm btn-link p-0" title="Abrir perfil em nova aba">
                                        <i class="fas fa-external-link-alt text-primary"></i>
                                    </a>
                                </div>
                            </td>
                            <td>{{ $inscricao->estagiario->email }}</td>
                            <td>{{ $inscricao->estagiario->numero_celular ?? $inscricao->estagiario->numero_telefone }}</td>
                            <td>{{ $inscricao->estagiario->curso ?? '-' }}</td>
                            <td>
                                <span class="badge @switch($inscricao->status_inscricao) @case('inscrito') bg-info @break @case('deferido') bg-success @break @case('indeferido') bg-danger @break @default bg-secondary @endswitch">
                                    {{ ucfirst($inscricao->status_inscricao) }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $inscricao->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    @if($inscricao->status_inscricao !== 'deferido')
                                        <form action="{{ route('processos-seletivos.inscricoes', $processo->id_processo) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="inscricao_id" value="{{ $inscricao->id_inscricao }}">
                                            <input type="hidden" name="novo_status" value="deferido">
                                            <button type="submit" class="btn btn-outline-success" title="Marcar como Deferido">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($inscricao->status_inscricao !== 'indeferido')
                                        <form action="{{ route('processos-seletivos.inscricoes', $processo->id_processo) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="inscricao_id" value="{{ $inscricao->id_inscricao }}">
                                            <input type="hidden" name="novo_status" value="indeferido">
                                            <button type="submit" class="btn btn-outline-danger" title="Marcar como Indeferido">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Nenhuma inscrição encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pt-3">
        {{ $inscricoes->links() }}
    </div>
</div>

<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exportar Inscrições</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('processos-seletivos.exportar-inscricoes', $processo->id_processo) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="format" class="form-label">Formato de Exportação</label>
                        <select class="form-select" id="format" name="format" required>
                            <option value="excel">Excel (.xlsx)</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download me-1"></i> Exportar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
