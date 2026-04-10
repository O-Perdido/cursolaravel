@extends('layouts.main')

@section('title', 'Candidaturas da Vaga')

@section('content')
    <div class="container-fluid py-3">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h4 class="mb-1">{{ $vaga->titulo_vaga }}</h4>
                        <div class="text-muted small">
                            <span class="me-2"><strong>Número:</strong> {{ $vaga->numero_vaga }}</span>
                            <span class="me-2"><strong>Unidade:</strong> {{ $vaga->empresa->nome_empresa ?? '-' }}</span>
                            <span class="badge bg-light text-dark">{{ $vaga->candidaturas_count }} candidatura(s)</span>
                            @if($vaga->tem_termo_pendente)
                                <span class="badge bg-warning text-dark">Termo pendente</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('vagas.index') }}" class="btn btn-outline-secondary btn-sm">Voltar</a>
                        @if(!$vaga->fk_id_termo && $vaga->fk_id_estagiario_definido && in_array(Auth::user()->nivel, ['admin', 'operador'], true))
                            <a href="{{ route('termos.create', ['empresa_id' => $vaga->fk_id_empresa, 'vaga_id' => $vaga->id_vaga, 'return_to' => 'vagas.index']) }}"
                                class="btn btn-info btn-sm">Gerar termo</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Estagiário</th>
                            <th>Contato</th>
                            <th>Status</th>
                            <th>Currículo</th>
                            <th>Data</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($candidaturas as $candidatura)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $candidatura->estagiario->nome_estagiario ?? '-' }}</div>
                                    <div class="small text-muted">{{ $candidatura->estagiario->curso ?? '-' }}</div>
                                </td>
                                <td>
                                    <div>{{ $candidatura->estagiario->email ?? '-' }}</div>
                                    <div class="small text-muted">
                                        {{ $candidatura->estagiario->numero_celular ?? $candidatura->estagiario->numero_telefone ?? '-' }}
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $candidatura->status_candidatura === 'definido' ? 'bg-success' : 'bg-info text-dark' }}">
                                        {{ $candidatura->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('vagas.candidaturas.curriculo', $candidatura->id_candidatura) }}"
                                        class="btn btn-outline-primary btn-sm">Baixar</a>
                                </td>
                                <td>{{ $candidatura->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center flex-wrap">
                                        <a href="{{ route('estagiario.show', $candidatura->fk_id_estagiario) }}" target="_blank"
                                            class="btn btn-outline-secondary btn-sm" title="Abrir perfil">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-primary btn-sm btn-alterar-status"
                                            data-candidatura-id="{{ $candidatura->id_candidatura }}"
                                            data-estagiario="{{ $candidatura->estagiario->nome_estagiario ?? 'Estagiário' }}"
                                            data-status-atual="{{ $candidatura->status_candidatura }}"
                                            data-observacoes="{{ $candidatura->observacoes_internas }}">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        @if($candidatura->status_candidatura !== 'definido')
                                            <button type="button" class="btn btn-outline-success btn-sm btn-definir"
                                                data-candidatura-id="{{ $candidatura->id_candidatura }}"
                                                data-estagiario="{{ $candidatura->estagiario->nome_estagiario ?? 'Estagiário' }}">
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Nenhuma candidatura registrada para esta
                                    vaga.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="pt-3">{{ $candidaturas->links() }}</div>
    </div>

    <div class="modal fade" id="statusCandidaturaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Atualizar candidatura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form method="POST" action="{{ route('vagas.candidaturas.status', $vaga->id_vaga) }}">
                    @csrf
                    <input type="hidden" name="candidatura_id" id="modal_candidatura_id">
                    <div class="modal-body">
                        <p class="mb-3">Atualizar a candidatura de <strong id="modal_estagiario_nome"></strong>.</p>
                        <div class="mb-3">
                            <label for="modal_novo_status" class="form-label">Novo status</label>
                            <select name="novo_status" id="modal_novo_status" class="form-select" required>
                                @foreach($statusDisponiveis as $status => $label)
                                    <option value="{{ $status }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-0">
                            <label for="modal_observacoes_internas" class="form-label">Observação interna</label>
                            <textarea name="observacoes_internas" id="modal_observacoes_internas" rows="3"
                                class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <div class="text-muted small">Ao salvar, enviar e-mail para estagiário?</div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" name="enviar_email" value="0" class="btn btn-outline-primary">Não
                                enviar</button>
                            <button type="submit" name="enviar_email" value="1" class="btn btn-primary">Enviar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="definirCandidaturaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Definir estagiário para a vaga</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <form method="POST" action="{{ route('vagas.candidaturas.status', $vaga->id_vaga) }}">
                    @csrf
                    <input type="hidden" name="candidatura_id" id="modal_definir_candidatura_id">
                    <input type="hidden" name="novo_status" value="definido">
                    <div class="modal-body">
                        <p class="mb-3">Definir <strong id="modal_definir_estagiario_nome"></strong> como estagiário
                            escolhido para esta vaga?</p>
                        <div class="mb-0">
                            <label for="modal_definir_observacoes" class="form-label">Observação interna</label>
                            <textarea name="observacoes_internas" id="modal_definir_observacoes" rows="3"
                                class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <div class="text-muted small">Ao salvar, enviar e-mail para estagiário?</div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" name="enviar_email" value="0" class="btn btn-outline-primary">Não
                                enviar</button>
                            <button type="submit" name="enviar_email" value="1" class="btn btn-primary">Enviar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusModalEl = document.getElementById('statusCandidaturaModal');
            const definirModalEl = document.getElementById('definirCandidaturaModal');
            const statusModal = window.bootstrap?.Modal.getOrCreateInstance(statusModalEl);
            const definirModal = window.bootstrap?.Modal.getOrCreateInstance(definirModalEl);

            document.querySelectorAll('.btn-alterar-status').forEach(function (button) {
                button.addEventListener('click', function () {
                    document.getElementById('modal_candidatura_id').value = this.dataset.candidaturaId;
                    document.getElementById('modal_estagiario_nome').textContent = this.dataset.estagiario;
                    document.getElementById('modal_novo_status').value = this.dataset.statusAtual;
                    document.getElementById('modal_observacoes_internas').value = this.dataset.observacoes || '';
                    statusModal.show();
                });
            });

            document.querySelectorAll('.btn-definir').forEach(function (button) {
                button.addEventListener('click', function () {
                    document.getElementById('modal_definir_candidatura_id').value = this.dataset.candidaturaId;
                    document.getElementById('modal_definir_estagiario_nome').textContent = this.dataset.estagiario;
                    document.getElementById('modal_definir_observacoes').value = '';
                    definirModal.show();
                });
            });
        });
    </script>
@endsection