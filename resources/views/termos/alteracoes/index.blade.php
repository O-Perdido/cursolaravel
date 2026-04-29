@extends('layouts.main')

@section('title', 'Termos')

@section('content')

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <h1>Lista de Alterações</h1>

    @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
        <a href="{{ route('alteracao.create', $termo->id_termo) }}" class="btn btn-primary mb-3">Criar Nova Alteração</a>
    @endif

    <a href="{{ route('termos.show', $termo->id_termo) }}" class="btn btn-secondary mb-3">Voltar</a>
    <!-- Botão de Voltar -->
    <table class="table">
        <thead>
            <tr>
                <th>Número do Termo</th>
                <th>Data da Alteração</th>
                <th>Descrição da Alteração</th>
                <th style="width: 150px;">Status ZapSign</th>
                <th style="text-align: center">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($alteracoesTermo->where('fk_id_termo', $termo->id_termo) as $alteracaoTermo)
                <tr style="vertical-align: middle">
                    <td>{{ $alteracaoTermo->termo->numero_termo }}/{{ $alteracaoTermo->termo->ano_termo }}</td>
                    <td>{{ \Carbon\Carbon::parse($alteracaoTermo->data_alteracao)->format('d/m/Y') }}</td>
                    <td>{{ $alteracaoTermo->descricao }}</td>
                    <td>
                        @if($alteracaoTermo->zapsign_doc_token)
                            @php
                                $statusMap = [
                                    'signed' => ['bg-success', 'fas fa-check-circle', 'Assinado'],
                                    'finished' => ['bg-success', 'fas fa-check-circle', 'Concluído'],
                                    'concluded' => ['bg-success', 'fas fa-check-circle', 'Concluído'],
                                    'completed' => ['bg-success', 'fas fa-check-circle', 'Concluído'],
                                    'assinado' => ['bg-success', 'fas fa-check-circle', 'Assinado'],
                                    'concluido' => ['bg-success', 'fas fa-check-circle', 'Concluído'],
                                    'link_aberto' => ['bg-info', 'fas fa-envelope-open', 'Aguardando'],
                                    'waiting_signature' => ['bg-info', 'fas fa-envelope-open', 'Aguardando'],
                                    'enviado' => ['bg-warning text-dark', 'fas fa-paper-plane', 'Enviado'],
                                    'pending' => ['bg-warning text-dark', 'fas fa-paper-plane', 'Pendente'],
                                    'desconhecido' => ['bg-secondary', 'fas fa-question-circle', 'Desconhecido']
                                ];
                                $rawStatus = trim(strtolower($alteracaoTermo->zapsign_status ?? 'enviado'));
                                $status = $rawStatus ?: 'enviado';
                                $statusInfo = $statusMap[$status] ?? $statusMap['desconhecido'];
                            @endphp
                            <span class="badge {{ $statusInfo[0] }}">
                                <i class="{{ $statusInfo[1] }} me-1"></i>{{ $statusInfo[2] }}
                            </span>
                        @else
                            <span class="badge badge-secondary">
                                <i class="fas fa-clock me-1"></i>Não enviado
                            </span>
                        @endif
                    </td>
                    <td style="text-align: center; width: 150px;">
                        <a href="{{ route('alteracao.gerarPdf', [$alteracaoTermo->termo->id_termo, $alteracaoTermo->id_alteracao]) }}"
                            class="btn btn-sm btn-info" style="margin-bottom: 3px;" target="_blank">Gerar PDF</a>

                        @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                            <button type="button" style="margin-bottom: 3px;" class="btn btn-sm btn-dark" data-bs-toggle="modal"
                                data-bs-target="#assinaturasModalAlteracao{{ $alteracaoTermo->id_alteracao }}"
                                title="Ver assinaturas do ZapSign">
                                <i class="fas fa-file-signature"></i> Assinaturas
                            </button>

                            @if ($loop->last)
                                <form
                                    action="{{ route('alteracao.destroy', [$alteracaoTermo->termo->id_termo, $alteracaoTermo->id_alteracao]) }}"
                                    method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                                </form>
                            @else
                                <button class="btn btn-sm btn-danger" disabled>Excluir</button>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modais de Assinaturas ZapSign para cada Alteração -->
    @foreach ($alteracoesTermo->where('fk_id_termo', $termo->id_termo) as $alteracaoTermo)
        @php
            $zsRaw = trim(strtolower($alteracaoTermo->zapsign_status ?? ''));
            $zsLabel = $zsRaw === '' ? 'Não enviado' : ucfirst($zsRaw);
            $zsClass = 'secondary';
            $zsMap = [
                'enviado' => ['Enviado', 'info'],
                'pending' => ['Pendente', 'secondary'],
                'waiting' => ['Pendente', 'secondary'],
                'waiting_signature' => ['Pendente', 'secondary'],
                'processing' => ['Processando', 'secondary'],
                'partially_signed' => ['Parcialmente assinado', 'warning'],
                'partial' => ['Parcialmente assinado', 'warning'],
                'finished' => ['Assinado', 'success'],
                'signed' => ['Assinado', 'success'],
                'concluded' => ['Assinado', 'success'],
                'completed' => ['Assinado', 'success'],
                'canceled' => ['Cancelado', 'dark'],
                'cancelled' => ['Cancelado', 'dark'],
                'refused' => ['Recusado', 'danger'],
                'rejected' => ['Recusado', 'danger'],
                'declined' => ['Recusado', 'danger'],
                'error' => ['Erro', 'danger'],
                'failed' => ['Erro', 'danger'],
            ];
            if ($zsRaw !== '' && isset($zsMap[$zsRaw])) {
                $zsLabel = $zsMap[$zsRaw][0];
                $zsClass = $zsMap[$zsRaw][1];
            }

            $signerStatusMap = [
                '' => ['Pendente', 'secondary'],
                'pending' => ['Pendente', 'secondary'],
                'waiting' => ['Pendente', 'secondary'],
                'waiting_signature' => ['Pendente', 'secondary'],
                'sent' => ['Enviado', 'info'],
                'viewed' => ['Visualizado', 'info'],
                'opened' => ['Visualizado', 'info'],
                'signed' => ['Assinado', 'success'],
                'finished' => ['Assinado', 'success'],
                'completed' => ['Assinado', 'success'],
                'concluded' => ['Assinado', 'success'],
                'refused' => ['Recusado', 'danger'],
                'rejected' => ['Recusado', 'danger'],
                'declined' => ['Recusado', 'danger'],
                'canceled' => ['Cancelado', 'dark'],
                'cancelled' => ['Cancelado', 'dark'],
                'error' => ['Erro', 'danger'],
                'failed' => ['Erro', 'danger'],
            ];

            $signers = $zapSignSignatarios[$alteracaoTermo->id_alteracao] ?? [];
            $downloadAssinado = $zapSignDownload[$alteracaoTermo->id_alteracao] ?? null;
        @endphp
        <div class="modal fade" id="assinaturasModalAlteracao{{ $alteracaoTermo->id_alteracao }}" tabindex="-1"
            aria-labelledby="assinaturasModalAlteracaoLabel{{ $alteracaoTermo->id_alteracao }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assinaturasModalAlteracaoLabel{{ $alteracaoTermo->id_alteracao }}">
                            <i class="fas fa-file-signature me-2"></i>
                            Assinaturas - Termo de Alteração (TAE)
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-break">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                            <div>
                                <strong>Termo:</strong>
                                {{ $alteracaoTermo->termo->numero_termo }}/{{ $alteracaoTermo->termo->ano_termo }}
                                <span class="text-muted">(Alteracao
                                    {{ \Carbon\Carbon::parse($alteracaoTermo->data_alteracao)->format('d/m/Y') }})</span>
                            </div>
                            <span class="badge bg-{{ $zsClass }}">{{ $zsLabel }}</span>
                        </div>

                        @if(!$alteracaoTermo->zapsign_doc_token)
                            <p class="text-muted mb-3">Documento ainda nao enviado para assinatura.</p>
                        @else
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <a href="{{ route('alteracao.statusZapSign', [$alteracaoTermo->termo->id_termo, $alteracaoTermo->id_alteracao]) }}"
                                    class="btn btn-outline-secondary btn-sm" title="Atualizar status no ZapSign">
                                    <i class="fas fa-sync-alt"></i> Atualizar status
                                </a>
                                <form
                                    action="{{ route('alteracao.zapsign.excluir', [$alteracaoTermo->termo->id_termo, $alteracaoTermo->id_alteracao]) }}"
                                    method="POST" onsubmit="return confirm('Confirma excluir este documento do ZapSign?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash"></i> Excluir documento
                                    </button>
                                </form>
                                @if(!empty($downloadAssinado))
                                    <a href="{{ $downloadAssinado }}" class="btn btn-outline-primary btn-sm" target="_blank">
                                        <i class="fas fa-file-download"></i> Baixar PDF assinado
                                    </a>
                                @endif
                            </div>
                            <div class="small mb-3">
                                <strong>Doc token:</strong>
                                <span class="text-monospace">{{ $alteracaoTermo->zapsign_doc_token }}</span>
                                <button type="button" class="btn btn-outline-primary btn-sm ms-2"
                                    data-token="{{ $alteracaoTermo->zapsign_doc_token }}"
                                    onclick="copyEmailToClipboard(this.dataset.token, this)">
                                    <i class="fas fa-copy"></i> Copiar
                                </button>
                            </div>
                        @endif

                        <h6 class="mb-2">Destinatarios</h6>
                        @if(!empty($signers) && count($signers) > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-3">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nome</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($signers as $signer)
                                            @php
                                                $signerRaw = strtolower($signer['status'] ?? $signer['signer_status'] ?? $signer['state'] ?? '');
                                                $signerInfo = $signerStatusMap[$signerRaw] ?? ['Desconhecido', 'secondary'];
                                            @endphp
                                            <tr>
                                                <td>{{ $signer['name'] ?? '—' }}</td>
                                                <td class="text-monospace">{{ $signer['email'] ?? '—' }}</td>
                                                <td><span class="badge bg-{{ $signerInfo[1] }}">{{ $signerInfo[0] }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted">Nenhum destinatario retornado pelo ZapSign.</p>
                        @endif

                        <h6 class="mb-2">Lista prevista de assinantes</h6>
                        @if(!$alteracaoTermo->zapsign_doc_token)
                            <form id="formEnviarAlteracao{{ $alteracaoTermo->id_alteracao }}"
                                action="{{ route('alteracao.enviarZapSign', [$alteracaoTermo->termo->id_termo, $alteracaoTermo->id_alteracao]) }}"
                                method="POST">
                                @csrf
                        @endif
                        @if(isset($alteracaoTermo->termo->escola) && $alteracaoTermo->termo->escola->nao_assina_zapsign && !empty($alteracaoTermo->termo->escola->orientacao_assinatura))
                            <div class="alert alert-info py-2 px-3 small" role="alert">
                                <strong>Orientação da Instituição de Ensino:</strong>
                                {!! nl2br(e($alteracaoTermo->termo->escola->orientacao_assinatura)) !!}
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0" style="font-size: 9pt">
                                <thead class="table-light">
                                    <tr>
                                        @if(!$alteracaoTermo->zapsign_doc_token)
                                            <th style="width: 90px;">Remover?</th>
                                        @endif
                                        <th style="width: 120px;">Tipo</th>
                                        <th>Nome</th>
                                        <th style="width: 35%;">E-mail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @if(!$alteracaoTermo->zapsign_doc_token)
                                            <td class="text-center">
                                                @if(!empty($alteracaoTermo->termo->estagiario->email))
                                                    <input type="checkbox" class="form-check-input"
                                                        name="remover_destinatarios[]"
                                                        value="{{ $alteracaoTermo->termo->estagiario->email }}">
                                                @else
                                                    —
                                                @endif
                                            </td>
                                        @endif
                                        <td><i class="fas fa-user text-primary me-1"></i> Estagiario</td>
                                        <td>{{ $alteracaoTermo->termo->estagiario->nome_estagiario }}</td>
                                        <td>{{ $alteracaoTermo->termo->estagiario->email ?? '—' }}</td>
                                    </tr>
                                    <tr>
                                        @if(!$alteracaoTermo->zapsign_doc_token)
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input" name="remover_destinatarios[]"
                                                    value="moacirecetista@hotmail.com">
                                            </td>
                                        @endif
                                        <td><i class="fas fa-handshake text-info me-1"></i> Ag. Integracao</td>
                                        <td>EBCP CONSULTORIA LTDA</td>
                                        <td>moacirecetista@hotmail.com</td>
                                    </tr>
                                    {{-- Representantes da Empresa --}}
                                    @if(isset($alteracaoTermo->termo->empresa))
                                        @if($alteracaoTermo->termo->empresa->representantes->count() > 0)
                                            @foreach($alteracaoTermo->termo->empresa->representantes as $rep)
                                                <tr>
                                                    @if(!$alteracaoTermo->zapsign_doc_token)
                                                        <td class="text-center">
                                                            @if(!empty($rep->email))
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="remover_destinatarios[]" value="{{ $rep->email }}">
                                                            @else
                                                                —
                                                            @endif
                                                        </td>
                                                    @endif
                                                    <td><i class="fas fa-building text-secondary me-1"></i> Concedente</td>
                                                    <td>{{ $rep->nome }}</td>
                                                    <td>{{ $rep->email }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                @if(!$alteracaoTermo->zapsign_doc_token)
                                                    <td class="text-center">
                                                        @if(!empty($alteracaoTermo->termo->empresa->email))
                                                            <input type="checkbox" class="form-check-input"
                                                                name="remover_destinatarios[]"
                                                                value="{{ $alteracaoTermo->termo->empresa->email }}">
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                @endif
                                                <td><i class="fas fa-building text-secondary me-1"></i> Concedente</td>
                                                <td>{{ $alteracaoTermo->termo->empresa->nome_representante ?? $alteracaoTermo->termo->empresa->nome_empresa }}</td>
                                                <td>{{ $alteracaoTermo->termo->empresa->email ?? '—' }}</td>
                                            </tr>
                                        @endif
                                    @endif
                                    {{-- Representantes da Escola --}}
                                    @if(isset($alteracaoTermo->termo->escola) && !$alteracaoTermo->termo->escola->nao_assina_zapsign)
                                        @if($alteracaoTermo->termo->escola->representantes->count() > 0)
                                            @foreach($alteracaoTermo->termo->escola->representantes as $rep)
                                                <tr>
                                                    @if(!$alteracaoTermo->zapsign_doc_token)
                                                        <td class="text-center">
                                                            @if(!empty($rep->email))
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="remover_destinatarios[]" value="{{ $rep->email }}">
                                                            @else
                                                                —
                                                            @endif
                                                        </td>
                                                    @endif
                                                    <td><i class="fas fa-school text-success me-1"></i> Instituicao</td>
                                                    <td>{{ $rep->nome }}</td>
                                                    <td>{{ $rep->email }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                @if(!$alteracaoTermo->zapsign_doc_token)
                                                    <td class="text-center">
                                                        @if(!empty($alteracaoTermo->termo->escola->email))
                                                            <input type="checkbox" class="form-check-input"
                                                                name="remover_destinatarios[]"
                                                                value="{{ $alteracaoTermo->termo->escola->email }}">
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                @endif
                                                <td><i class="fas fa-school text-success me-1"></i> Instituicao</td>
                                                <td>{{ $alteracaoTermo->termo->escola->nome_representante ?? $alteracaoTermo->termo->escola->nome_escola }}
                                                </td>
                                                <td>{{ $alteracaoTermo->termo->escola->email ?? '—' }}</td>
                                            </tr>
                                        @endif
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @if(!$alteracaoTermo->zapsign_doc_token)
                            </form>
                        @endif
                    </div>
                    <div class="modal-footer">
                        @if(!$alteracaoTermo->zapsign_doc_token)
                            <button type="submit" class="btn btn-success"
                                form="formEnviarAlteracao{{ $alteracaoTermo->id_alteracao }}">
                                <i class="fas fa-paper-plane me-1"></i>
                                Enviar para assinatura
                            </button>
                        @endif
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection