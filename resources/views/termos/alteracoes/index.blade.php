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
                            @if(!$alteracaoTermo->zapsign_doc_token)
                                <button type="button" style="margin-bottom: 3px;" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                    data-bs-target="#zapSignModalAlteracao{{ $alteracaoTermo->id_alteracao }}"
                                    title="Enviar Alteração para Assinatura ZapSign">
                                    <i class="fas fa-file-signature"></i> Enviar ZapSign
                                </button>
                            @else
                                <a href="{{ route('alteracao.statusZapSign', [$alteracaoTermo->termo->id_termo, $alteracaoTermo->id_alteracao]) }}"
                                    class="btn btn-sm btn-secondary" style="margin-bottom: 3px;" title="Atualizar status no ZapSign">
                                    <i class="fas fa-sync-alt"></i> Atualizar
                                </a>
                            @endif

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

    <!-- Modais ZapSign para cada Alteração -->
    @foreach ($alteracoesTermo->where('fk_id_termo', $termo->id_termo) as $alteracaoTermo)
        @if(!$alteracaoTermo->zapsign_doc_token)
            <div class="modal fade" id="zapSignModalAlteracao{{ $alteracaoTermo->id_alteracao }}" tabindex="-1"
                aria-labelledby="zapSignModalAlteracaoLabel{{ $alteracaoTermo->id_alteracao }}" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="zapSignModalAlteracaoLabel{{ $alteracaoTermo->id_alteracao }}">
                                <i class="fas fa-file-signature me-2"></i>
                                Enviar Alteração para Assinatura ZapSign
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-break">
                            <p>Deseja enviar este termo de alteração para assinatura eletrônica via ZapSign?</p>
                            <p><strong>Termo:</strong>
                                {{ $alteracaoTermo->termo->numero_termo }}/{{ $alteracaoTermo->termo->ano_termo }}</p>
                            <p><strong>Estagiário:</strong> {{ $alteracaoTermo->termo->estagiario->nome_estagiario }}</p>
                            <p><strong>Data da Alteração:</strong>
                                {{ \Carbon\Carbon::parse($alteracaoTermo->data_alteracao)->format('d/m/Y') }}</p>

                            <hr class="my-2">
                            <p class="mb-1">
                                <strong>Destinatários que receberão o documento:</strong>
                            </p>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-2" style="font-size: 9pt">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 120px;">Tipo</th>
                                            <th>Nome</th>
                                            <th style="width: 35%;">E-mail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><i class="fas fa-user text-primary me-1"></i> Estagiário</td>
                                            <td>{{ $alteracaoTermo->termo->estagiario->nome_estagiario }}</td>
                                            <td>{{ $alteracaoTermo->termo->estagiario->email ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td><i class="fas fa-handshake text-info me-1"></i> Ag. Integração</td>
                                            <td>EBCP CONSULTORIA LTDA</td>
                                            <td>moacirecetista@hotmail.com</td>
                                        </tr>
                                        {{-- Representantes da Empresa --}}
                                        @if(isset($alteracaoTermo->termo->empresa) && $alteracaoTermo->termo->empresa->representantes->count() > 0)
                                            @foreach($alteracaoTermo->termo->empresa->representantes as $rep)
                                                <tr>
                                                    <td><i class="fas fa-building text-secondary me-1"></i> Concedente</td>
                                                    <td>{{ $rep->nome }}</td>
                                                    <td>{{ $rep->email }}</td>
                                                </tr>
                                            @endforeach
                                        @elseif(isset($alteracaoTermo->termo->empresa))
                                            <tr>
                                                <td><i class="fas fa-building text-secondary me-1"></i> Concedente</td>
                                                <td>{{ $alteracaoTermo->termo->empresa->nome_representante ?? $alteracaoTermo->termo->empresa->nome_empresa }}
                                                </td>
                                                <td>{{ $alteracaoTermo->termo->empresa->email ?? '—' }}</td>
                                            </tr>
                                        @endif
                                        {{-- Representantes da Escola --}}
                                        @if(isset($alteracaoTermo->termo->escola) && $alteracaoTermo->termo->escola->representantes->count() > 0)
                                            @foreach($alteracaoTermo->termo->escola->representantes as $rep)
                                                <tr>
                                                    <td><i class="fas fa-school text-success me-1"></i> Instituição</td>
                                                    <td>{{ $rep->nome }}</td>
                                                    <td>{{ $rep->email }}</td>
                                                </tr>
                                            @endforeach
                                        @elseif(isset($alteracaoTermo->termo->escola))
                                            <tr>
                                                <td><i class="fas fa-school text-success me-1"></i> Instituição</td>
                                                <td>{{ $alteracaoTermo->termo->escola->nome_representante ?? $alteracaoTermo->termo->escola->nome_escola }}
                                                </td>
                                                <td>{{ $alteracaoTermo->termo->escola->email ?? '—' }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-muted small mb-0">
                                <strong>Observação:</strong> Todos os representantes cadastrados receberão o documento para
                                assinatura digital.
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <form
                                action="{{ route('alteracao.enviarZapSign', [$alteracaoTermo->termo->id_termo, $alteracaoTermo->id_alteracao]) }}"
                                method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-paper-plane me-1"></i>
                                    Enviar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection