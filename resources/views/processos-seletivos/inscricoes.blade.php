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
                        <th class="text-nowrap">Nº Inscrição</th>
                        <th>Estagiário</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Curso</th>
                        <th>Status</th>
                        <th>Anexo</th>
                        <th class="text-nowrap">Data</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inscricoes as $inscricao)
                        <tr>
                            <td class="fw-bold text-primary">
                                {{ $inscricao->numero_inscricao ?? '—' }}
                            </td>
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
                            <td>
                                @if($inscricao->arquivo_inscricao)
                                    <a href="{{ Storage::url($inscricao->arquivo_inscricao) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-paperclip me-1"></i> Abrir
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-muted">{{ $inscricao->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm" role="group">
                                    @if($inscricao->status_inscricao !== 'deferido')
                                        <form action="{{ route('processos-seletivos.inscricoes.atualizar-status', $processo->id_processo) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="inscricao_id" value="{{ $inscricao->id_inscricao }}">
                                            <input type="hidden" name="novo_status" value="deferido">
                                            <button type="submit" class="btn btn-outline-success" title="Marcar como Deferido">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($inscricao->status_inscricao !== 'indeferido')
                                        <form action="{{ route('processos-seletivos.inscricoes.atualizar-status', $processo->id_processo) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="inscricao_id" value="{{ $inscricao->id_inscricao }}">
                                            <input type="hidden" name="novo_status" value="indeferido">
                                            <button type="submit" class="btn btn-outline-danger" title="Marcar como Indeferido">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($inscricao->status_inscricao !== 'inscrito')
                                        <form action="{{ route('processos-seletivos.inscricoes.atualizar-status', $processo->id_processo) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="inscricao_id" value="{{ $inscricao->id_inscricao }}">
                                            <input type="hidden" name="novo_status" value="inscrito">
                                            <button type="submit" class="btn btn-outline-secondary" title="Reverter para Inscrito">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">Nenhuma inscrição encontrada.</td>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-download me-2"></i>Exportar Inscrições</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('processos-seletivos.exportar-inscricoes', $processo->id_processo) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Formato -->
                    <div class="mb-4">
                        <label class="form-label fw-bold"><i class="fas fa-file me-2 text-primary"></i>Formato de Exportação</label>
                        <div class="d-flex gap-3">
                            <div class="form-check form-check-inline flex-fill">
                                <input class="form-check-input" type="radio" name="format" id="formatPdf" value="pdf" checked>
                                <label class="form-check-label w-100" for="formatPdf">
                                    <div class="border rounded p-3 text-center" style="cursor: pointer;">
                                        <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>
                                        <div class="fw-semibold">PDF</div>
                                    </div>
                                </label>
                            </div>
                            <div class="form-check form-check-inline flex-fill">
                                <input class="form-check-input" type="radio" name="format" id="formatExcel" value="excel">
                                <label class="form-check-label w-100" for="formatExcel">
                                    <div class="border rounded p-3 text-center" style="cursor: pointer;">
                                        <i class="fas fa-file-excel fa-2x text-success mb-2"></i>
                                        <div class="fw-semibold">Excel</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Filtro por Status -->
                    <div class="mb-4">
                        <label class="form-label fw-bold"><i class="fas fa-filter me-2 text-primary"></i>Filtrar por Status</label>
                        <select class="form-select" name="status_filter" required>
                            <option value="todos">📋 Todos os inscritos</option>
                            <option value="deferido">✅ Apenas Deferidos</option>
                            <option value="indeferido">❌ Apenas Indeferidos</option>
                            <option value="inscrito">📝 Apenas Inscritos (pendentes)</option>
                        </select>
                    </div>

                    <!-- Seleção de Colunas -->
                    <div class="mb-3">
                        <label class="form-label fw-bold"><i class="fas fa-columns me-2 text-primary"></i>Colunas a Incluir</label>
                        <div class="border rounded p-3 bg-light">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="numero_inscricao" id="colNumero" checked>
                                        <label class="form-check-label" for="colNumero">
                                            <i class="fas fa-hashtag me-1 text-muted"></i> Nº Inscrição
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="nome" id="colNome" checked>
                                        <label class="form-check-label" for="colNome">
                                            <i class="fas fa-user me-1 text-muted"></i> Nome Completo
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="email" id="colEmail" checked>
                                        <label class="form-check-label" for="colEmail">
                                            <i class="fas fa-envelope me-1 text-muted"></i> E-mail
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="telefone" id="colTelefone" checked>
                                        <label class="form-check-label" for="colTelefone">
                                            <i class="fas fa-phone me-1 text-muted"></i> Telefone
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="cpf" id="colCpf">
                                        <label class="form-check-label" for="colCpf">
                                            <i class="fas fa-id-card me-1 text-muted"></i> CPF
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="curso" id="colCurso" checked>
                                        <label class="form-check-label" for="colCurso">
                                            <i class="fas fa-graduation-cap me-1 text-muted"></i> Curso
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="instituicao" id="colInstituicao">
                                        <label class="form-check-label" for="colInstituicao">
                                            <i class="fas fa-school me-1 text-muted"></i> Instituição de Ensino
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="status" id="colStatus" checked>
                                        <label class="form-check-label" for="colStatus">
                                            <i class="fas fa-info-circle me-1 text-muted"></i> Status
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="colunas[]" value="data_inscricao" id="colData" checked>
                                        <label class="form-check-label" for="colData">
                                            <i class="fas fa-calendar me-1 text-muted"></i> Data da Inscrição
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 border-top pt-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selecionarTodas()">
                                    <i class="fas fa-check-double me-1"></i> Selecionar Todas
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="desselecionarTodas()">
                                    <i class="fas fa-times me-1"></i> Limpar Seleção
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download me-1"></i> Exportar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function selecionarTodas() {
    document.querySelectorAll('input[name="colunas[]"]').forEach(cb => cb.checked = true);
}
function desselecionarTodas() {
    document.querySelectorAll('input[name="colunas[]"]').forEach(cb => cb.checked = false);
}
</script>
@endsection
