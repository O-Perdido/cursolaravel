@extends('layouts.main')

@section('title', 'Detalhes do Termo de Estágio')

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
    @section('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                    new bootstrap.Tooltip(tooltipTriggerEl);
                });

                var reverterBtn = document.getElementById('reverter-rescisao-btn');
                var reverterForm = document.getElementById('reverter-rescisao-form');
                if (reverterBtn && reverterForm) {
                    reverterBtn.addEventListener('click', function (event) {
                        event.preventDefault();
                        if (typeof Swal === 'undefined') {
                            if (confirm('Tem certeza que deseja reverter a rescisao? Esta acao nao pode ser desfeita.')) {
                                reverterForm.submit();
                            }
                            return;
                        }

                        Swal.fire({
                            title: 'Reverter rescisao?',
                            text: 'Esta acao nao pode ser desfeita.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Sim, reverter',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                reverterForm.submit();
                            }
                        });
                    });
                }
            });
        </script>
    @endsection

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
        <h1 class="mb-0">Detalhes do Termo de Estágio</h1>
        <div class="btn-group mt-2 mt-md-0">
            <button onclick="window.NavigationHistory?.goBack('{{ route('termos.index') }}')" class="btn btn-secondary"
                title="Voltar para a página anterior com filtros preservados">
                <i class="fas fa-arrow-left"></i> Voltar
            </button>
            <a href="{{ route('termos.gerarPdf', $termo->id_termo) }}" class="btn btn-primary" target="_blank">
                <i class="fas fa-file-pdf"></i> PDF Termo
            </a>
            @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                <!-- Botão Recesso -->
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#recessoModal"
                    title="Conceder Recesso">
                    <i class="fas fa-umbrella-beach"></i> Recesso
                </button>
                @if($termo->rescisao)
                    <a href="{{ route('rescisoes.gerarPdf', $termo->rescisao->id_rescisao) }}" class="btn btn-primary"
                        target="_blank">
                        <i class="fas fa-file-pdf"></i> PDF Rescisão
                    </a>
                @else
                    <button type="button" class="btn btn-warning" style="color: crimson; font-weight: 900;" data-toggle="modal"
                        data-target="#exampleModal">
                        <i class="fas fa-ban"></i> Rescindir
                    </button>
                @endif
                <a href="{{ route('alteracao.create', $termo->id_termo) }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Nova Alteração
                </a>
                <a href="{{ route('avaliacoes.por-termo', $termo->id_termo) }}" class="btn btn-warning">
                    <i class="fas fa-star"></i> Avaliações
                </a>
            @elseif (Auth::user()->nivel == 'empresa')
                <!-- Empresa: apenas Recesso -->
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#recessoModal"
                    title="Conceder Recesso">
                    <i class="fas fa-umbrella-beach"></i> Recesso
                </button>
            @endif
            <a href="{{ route('alteracoes.index', $termo->id_termo) }}" class="btn btn-primary">
                <i class="fas fa-list"></i> Alterações
            </a>
            @if (Auth::user()->nivel == 'admin')
                @php $temAlteracao = $termo->alteracaoTermo()->exists(); @endphp
                @if(!$termo->rescisao && !$temAlteracao)
                    <a href="{{ route('termos.edit', $termo->id_termo) }}" class="btn btn-outline-primary">
                        <i class="fas fa-pen"></i> Editar TCE
                    </a>
                @else
                    <span class="btn btn-outline-primary disabled" tabindex="0" role="button" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="Só é possível editar se o termo não tiver rescisão nem alteração."
                        style="pointer-events: auto;">
                        <i class="fas fa-pen"></i> Editar TCE
                    </span>
                @endif
                @if($termo->rescisao)
                    <form id="reverter-rescisao-form" action="{{ route('termos.reverterRescisao', $termo->id_termo) }}"
                        method="POST" class="d-none">
                        @csrf
                    </form>
                    <button type="submit" form="reverter-rescisao-form" id="reverter-rescisao-btn" class="btn btn-outline-danger"
                        data-bs-toggle="tooltip" data-bs-placement="top" title="Reverter rescisão e restaurar data final do termo.">
                        <i class="fas fa-undo"></i> Reverter Rescisão
                    </button>
                @endif
            @endif
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header text-white d-flex justify-content-between align-items-center"
            style="background-color: #102e6c;">
            <div>
                <strong>Número do Termo:</strong>
                {{ $termo->numero_termo }}/{{ $termo->ano_termo }}
                @if ($termo->fk_id_vaga && $termo->vaga)
                    <span class="ms-3">
                        <strong>Nº Vaga:</strong> {{ $termo->vaga->numero_vaga }}
                    </span>
                @endif
            </div>
            @php
                $isVencido = \Carbon\Carbon::parse($termo->data_fim_estagio)->isPast() && !$termo->rescisao;
            @endphp
            @if($termo->rescisao)
                <span class="badge badge-danger px-3 py-2" style="font-size:1rem;">
                    Encerrado
                </span>
            @elseif($isVencido)
                <span class="badge badge-warning px-3 py-2" style="font-size:1rem;">
                    Contrato Vencido
                </span>
            @else
                <span class="badge badge-success px-3 py-2" style="font-size:1rem;">
                    Ativo
                </span>
            @endif
        </div>
        <div class="card-body">
            @php
                $zsRaw = strtolower($termo->zapsign_status ?? '');
                $zsMap = [
                    '' => ['Não enviado', 'secondary'],
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
                $zsLabel = $zsMap[$zsRaw][0] ?? ucfirst($zsRaw);
                $zsClass = $zsMap[$zsRaw][1] ?? 'secondary';
            @endphp
            @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                @php
                    $zsRawTce = strtolower($termo->zapsign_status ?? '');
                    $zsLabelTce = $zsMap[$zsRawTce][0] ?? ($zsRawTce === '' ? 'Não enviado' : ucfirst($zsRawTce));
                    $zsClassTce = $zsMap[$zsRawTce][1] ?? 'secondary';

                    $zsRawTre = strtolower(optional($termo->rescisao)->zapsign_status ?? '');
                    $zsLabelTre = $zsMap[$zsRawTre][0] ?? ($zsRawTre === '' ? 'Não enviado' : ucfirst($zsRawTre));
                    $zsClassTre = $zsMap[$zsRawTre][1] ?? 'secondary';

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
                @endphp
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header text-white d-flex justify-content-between align-items-center"
                        style="background-color: #1f2937;">
                        <div>
                            <i class="fas fa-file-signature me-2"></i>
                            <strong>ZapSign</strong>
                        </div>
                        <span class="badge bg-light text-dark">Assinaturas</span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <div class="card h-100 border">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <strong>Assinatura TCE</strong>
                                        <span class="badge bg-{{ $zsClassTce }}">{{ $zsLabelTce }}</span>
                                    </div>
                                    <div class="card-body">
                                        @if(!$termo->zapsign_doc_token)
                                            <p class="text-muted mb-3">Documento ainda não enviado para assinatura.</p>
                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#zapSignModalShow" title="Enviar para Assinatura ZapSign">
                                                <i class="fas fa-paper-plane"></i> Enviar para assinatura
                                            </button>
                                        @else
                                            <div class="d-flex flex-wrap gap-2 mb-3">
                                                <a href="{{ route('termos.statusZapSign', $termo->id_termo) }}"
                                                    class="btn btn-outline-secondary btn-sm">
                                                    <i class="fas fa-sync-alt"></i> Atualizar status
                                                </a>
                                                <form action="{{ route('termos.zapsign.excluir', $termo->id_termo) }}" method="POST"
                                                    onsubmit="return confirm('Confirma excluir este documento do ZapSign?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                                        <i class="fas fa-trash"></i> Excluir documento
                                                    </button>
                                                </form>
                                                @if(!empty($downloadAssinadoTce))
                                                    <a href="{{ $downloadAssinadoTce }}" class="btn btn-outline-primary btn-sm"
                                                        target="_blank">
                                                        <i class="fas fa-file-download"></i> Baixar PDF assinado
                                                    </a>
                                                @endif
                                            </div>
                                            <div class="small">
                                                <strong>Doc token:</strong>
                                                <span class="text-monospace">{{ $termo->zapsign_doc_token }}</span>
                                                <button type="button" class="btn btn-outline-primary btn-sm ms-2"
                                                    data-token="{{ $termo->zapsign_doc_token }}"
                                                    onclick="copyEmailToClipboard(this.dataset.token, this)">
                                                    <i class="fas fa-copy"></i> Copiar
                                                </button>
                                            </div>
                                        @endif
                                        <hr class="my-3">
                                        <h6 class="mb-2">Destinatários</h6>
                                        @if(!empty($signatariosTce) && count($signatariosTce) > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Nome</th>
                                                            <th>Email</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($signatariosTce as $signer)
                                                            @php
                                                                $signerRaw = strtolower($signer['status'] ?? $signer['signer_status'] ?? $signer['state'] ?? '');
                                                                $signerInfo = $signerStatusMap[$signerRaw] ?? ['Desconhecido', 'secondary'];
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $signer['name'] ?? '—' }}</td>
                                                                <td class="text-monospace">{{ $signer['email'] ?? '—' }}</td>
                                                                <td><span
                                                                        class="badge bg-{{ $signerInfo[1] }}">{{ $signerInfo[0] }}</span>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="text-muted mb-0">Nenhum destinatário retornado pelo ZapSign.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="card h-100 border">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <strong>Assinatura TRE</strong>
                                        @if($termo->rescisao)
                                            <span class="badge bg-{{ $zsClassTre }}">{{ $zsLabelTre }}</span>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        @if(!$termo->rescisao)
                                            <div class="alert alert-success mb-0">
                                                <i class="fas fa-check-circle me-1"></i> Contrato ainda ativo.
                                            </div>
                                        @else
                                            @if(!$termo->rescisao->zapsign_doc_token)
                                                <p class="text-muted mb-3">Rescisão criada, mas documento ainda não enviado.</p>
                                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#zapSignModalRescisao"
                                                    title="Enviar Rescisão para Assinatura ZapSign">
                                                    <i class="fas fa-paper-plane"></i> Enviar rescisão para assinatura
                                                </button>
                                            @else
                                                <div class="d-flex flex-wrap gap-2 mb-3">
                                                    <a href="{{ route('rescisao.statusZapSign', $termo->rescisao->id_rescisao) }}"
                                                        class="btn btn-outline-secondary btn-sm">
                                                        <i class="fas fa-sync-alt"></i> Atualizar status
                                                    </a>
                                                    <form
                                                        action="{{ route('rescisao.zapsign.excluir', $termo->rescisao->id_rescisao) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Confirma excluir este documento do ZapSign?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                                            <i class="fas fa-trash"></i> Excluir documento
                                                        </button>
                                                    </form>
                                                    @if(!empty($downloadAssinadoTre))
                                                        <a href="{{ $downloadAssinadoTre }}" class="btn btn-outline-primary btn-sm"
                                                            target="_blank">
                                                            <i class="fas fa-file-download"></i> Baixar PDF assinado
                                                        </a>
                                                    @endif
                                                </div>
                                                <div class="small">
                                                    <strong>Doc token:</strong>
                                                    <span class="text-monospace">{{ $termo->rescisao->zapsign_doc_token }}</span>
                                                    <button type="button" class="btn btn-outline-primary btn-sm ms-2"
                                                        data-token="{{ $termo->rescisao->zapsign_doc_token }}"
                                                        onclick="copyEmailToClipboard(this.dataset.token, this)">
                                                        <i class="fas fa-copy"></i> Copiar
                                                    </button>
                                                </div>
                                            @endif
                                            <hr class="my-3">
                                            <h6 class="mb-2">Destinatários</h6>
                                            @if(!empty($signatariosTre) && count($signatariosTre) > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Nome</th>
                                                                <th>Email</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($signatariosTre as $signer)
                                                                @php
                                                                    $signerRaw = strtolower($signer['status'] ?? $signer['signer_status'] ?? $signer['state'] ?? '');
                                                                    $signerInfo = $signerStatusMap[$signerRaw] ?? ['Desconhecido', 'secondary'];
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $signer['name'] ?? '—' }}</td>
                                                                    <td class="text-monospace">{{ $signer['email'] ?? '—' }}</td>
                                                                    <td><span
                                                                            class="badge bg-{{ $signerInfo[1] }}">{{ $signerInfo[0] }}</span>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <p class="text-muted mb-0">Nenhum destinatário retornado pelo ZapSign.</p>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="mt-4">
                <!-- Card de Vaga Vinculada -->
                {{-- Card de Vaga Vinculada removido. Exibição simplificada no cabeçalho. --}}

                @php
                    $isVencido = \Carbon\Carbon::parse($termo->data_fim_estagio)->isPast() && !$termo->rescisao;
                @endphp

                @if($termo->rescisao)
                    {{-- Card unificado de Rescisão com Status ZapSign --}}
                    <div class="card border-danger shadow-sm">
                        <div class="card-header bg-danger text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-ban me-2"></i>Contrato Rescindido
                                </h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <p class="mb-2">
                                        <strong><i class="fas fa-calendar me-2 text-danger"></i>Data da Rescisão:</strong><br>
                                        <span
                                            style="margin-left: 24px;">{{ \Carbon\Carbon::parse($termo->rescisao->data_rescisao)->format('d/m/Y') }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <p class="mb-2">
                                        <strong><i class="fas fa-file-pdf me-2 text-danger"></i>Documento:</strong><br>
                                        <span style="margin-left: 24px;">
                                            <a href="{{ route('rescisoes.gerarPdf', $termo->rescisao->id_rescisao) }}"
                                                target="_blank" class="link-danger">
                                                Visualizar PDF <i class="fas fa-external-link-alt ms-1"></i>
                                            </a>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <p class="mb-0">
                                <strong><i class="fas fa-info-circle me-2 text-danger"></i>Motivo:</strong><br>
                                <span style="margin-left: 24px;">{{ $termo->rescisao->motivo }}</span>
                            </p>
                        </div>
                    </div>
                @elseif($isVencido)
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                        <div>
                            <h5 class="alert-heading mb-1">Contrato Vencido</h5>
                            <p class="mb-0">Este contrato de estágio expirou e não foi rescindido. Considere registrar uma
                                rescisão.</p>
                        </div>
                    </div>
                @else
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="fas fa-check-circle fa-2x me-3"></i>
                        <div>
                            <h5 class="alert-heading mb-1">Contrato Ativo</h5>
                            <p class="mb-0">Este contrato de estágio está ativo e em vigência.</p>
                        </div>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Informações Gerais</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Estagiário:</strong>
                                    {{ $termo->estagiario->nome_estagiario }}
                                    @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                                        <a href="{{ route('estagiario.show', $termo->estagiario->id_estagiario) }}"
                                            target="_blank" class="ml-1" title="Ver detalhes do estagiário">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @endif
                                </li>
                                <li class="list-group-item"><strong>Unidade Concedente:</strong>
                                    {{ $termo->empresa->nome_empresa }}
                                    @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                                        <a href="{{ route('empresas.show', $termo->empresa->id_empresa) }}" target="_blank"
                                            class="ml-1" title="Ver detalhes da empresa">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @endif
                                </li>
                                <li class="list-group-item"><strong>Instituição de Ensino:</strong>
                                    {{ $termo->escola->nome_escola }}
                                    @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                                        <a href="{{ route('escolas.show', $termo->escola->id_escola) }}" target="_blank"
                                            class="ml-1" title="Ver detalhes da instituição">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @endif
                                </li>
                                <li class="list-group-item"><strong>Supervisor:</strong>
                                    {{ $termo->supervisor->nome_supervisor }}
                                    <a href="{{ route((Auth::user()->nivel ?? '') === 'empresa' ? 'empresa.supervisores.index' : 'supervisores.index', ['id_supervisor' => $termo->supervisor->id_supervisor]) }}"
                                        target="_blank" class="ml-1" title="Ver detalhes do supervisor">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </li>
                                <li class="list-group-item"><strong>Orientador:</strong> {{ $termo->nome_orientador }}</li>
                                <li class="list-group-item"><strong>Cargo do Orientador:</strong>
                                    {{ $termo->cargo_orientador }}</li>
                                <li class="list-group-item"><strong>Descrição das Atividades:</strong>
                                    {{ $termo->desc_atividades }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Detalhes do Estágio</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>Data de Início:</strong>
                                    {{ \Carbon\Carbon::parse($termo->data_inicio_estagio)->format('d/m/Y') }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Data de Término:</strong>
                                    {{ \Carbon\Carbon::parse($termo->data_fim_estagio)->format('d/m/Y') }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Horário de Estágio:</strong> {{ $termo->horario }}
                                </li>
                                @if ($termo->local)
                                    <li class="list-group-item">
                                        <strong>Local de Estágio:</strong>
                                        {{ $termo->local->descricao }}
                                    </li>
                                @endif
                                @if ($termo->lotacao)
                                    <li class="list-group-item">
                                        <strong>Lotação:</strong>
                                        {{ $termo->lotacao }}
                                    </li>
                                @endif
                                <li class="list-group-item">
                                    <strong>Valor da Bolsa:</strong> R$
                                    {{ number_format($termo->valor_bolsa, 2, ',', '.') }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Auxílio Transporte:</strong> R$
                                    {{ number_format($termo->auxilio_transporte, 2, ',', '.') }}
                                </li>
                                <li class="list-group-item">
                                    <strong>Saldo de Recesso Disponível:</strong>
                                    {{ (int) ($termo->saldo_recesso ?? 0) }} dia(s)
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @php
                // Nomes
                $nomeEstagiario = $termo->estagiario->nome_estagiario ?? $termo->estagiario->nome ?? null;
                $nomeRepEmpresa = $termo->empresa->representante
                    ?? $termo->empresa->nome_representante
                    ?? $termo->empresa->representante_legal
                    ?? null;
                $nomeRepEscola = $termo->escola->representante
                    ?? $termo->escola->nome_representante
                    ?? $termo->escola->representante_legal
                    ?? null;

                // E-mails
                $emailEstagiario = $termo->estagiario->email ?? $termo->estagiario->email_estagiario ?? null;
                $emailEmpresa = $termo->empresa->email ?? $termo->empresa->email_empresa ?? null;
                $emailEscola = $termo->escola->email ?? $termo->escola->email_escola ?? null;
            @endphp
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-3">Contatos de E-mail</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <div class="mb-2 mb-sm-0">
                                        <div>
                                            <strong>Estagiário:</strong>
                                            @if(!empty($nomeEstagiario))
                                                <span>{{ $nomeEstagiario }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <strong>E-mail:</strong>
                                            @if(!empty($emailEstagiario))
                                                <span class="text-monospace">{{ $emailEstagiario }}</span>
                                            @else
                                                <span class="text-muted">E-mail não informado.</span>
                                            @endif
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                        @if(empty($emailEstagiario)) disabled @endif data-email="{{ $emailEstagiario }}"
                                        onclick="copyEmailToClipboard(this.dataset.email, this)">
                                        <i class="fas fa-copy"></i> Copiar
                                    </button>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <div class="mb-2 mb-sm-0">
                                        <div>
                                            <strong>Unidade Concedente (Representante):</strong>
                                            @if(!empty($nomeRepEmpresa))
                                                <span>{{ $nomeRepEmpresa }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <strong>E-mail:</strong>
                                            @if(!empty($emailEmpresa))
                                                <span class="text-monospace">{{ $emailEmpresa }}</span>
                                            @else
                                                <span class="text-muted">E-mail não informado.</span>
                                            @endif
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" @if(empty($emailEmpresa))
                                    disabled @endif data-email="{{ $emailEmpresa }}"
                                        onclick="copyEmailToClipboard(this.dataset.email, this)">
                                        <i class="fas fa-copy"></i> Copiar
                                    </button>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                    <div class="mb-2 mb-sm-0">
                                        <div>
                                            <strong>Instituição de Ensino (Representante):</strong>
                                            @if(!empty($nomeRepEscola))
                                                <span>{{ $nomeRepEscola }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <strong>E-mail:</strong>
                                            @if(!empty($emailEscola))
                                                <span class="text-monospace">{{ $emailEscola }}</span>
                                            @else
                                                <span class="text-muted">E-mail não informado.</span>
                                            @endif
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" @if(empty($emailEscola))
                                    disabled @endif data-email="{{ $emailEscola }}"
                                        onclick="copyEmailToClipboard(this.dataset.email, this)">
                                        <i class="fas fa-copy"></i> Copiar
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Histórico de Concessões de Recesso -->
    @if(Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador' || Auth::user()->nivel == 'empresa')
        <div class="card shadow-sm mb-4">
            <div class="card-header text-white d-flex justify-content-between align-items-center"
                style="background-color: #0f766e;">
                <div>
                    <i class="fas fa-history me-2"></i>
                    <strong>Histórico de Concessões de Recesso</strong>
                </div>
                @php $concessoesAtivas = $termo->concessoesRecesso->where('status', 'ativo'); @endphp
                <span class="badge bg-light text-dark">{{ $concessoesAtivas->count() }} registro(s)</span>
            </div>
            <div class="card-body">
                @if($concessoesAtivas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Data Concessão</th>
                                    <th>Período</th>
                                    <th>Dias</th>
                                    <th>Usuário</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($concessoesAtivas->sortByDesc('data_concessao') as $concessao)
                                    <tr>
                                        <td>{{ $concessao->data_concessao->format('d/m/Y H:i') }}</td>
                                        <td>{{ $concessao->data_inicio_recesso->format('d/m/Y') }} a
                                            {{ $concessao->data_fim_recesso->format('d/m/Y') }}
                                        </td>
                                        <td><span class="badge bg-info">{{ $concessao->total_dias }} dia(s)</span></td>
                                        <td class="small">{{ $concessao->usuario->name ?? 'N/A' }}</td>
                                        <td class="d-flex gap-2">
                                            <!-- Botão para abrir/imprimir o PDF desta concessão -->
                                            <a href="{{ route('termos.recesso.pdf', $concessao->id_concessao) }}" target="_blank"
                                                class="btn btn-sm btn-outline-danger" title="Abrir PDF do Recesso">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </a>
                                            @if(Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                    data-bs-target="#modalExcluirConcessao{{ $concessao->id_concessao }}">
                                                    <i class="fas fa-trash"></i> Excluir
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                                    data-bs-target="#modalSolicitarExclusao{{ $concessao->id_concessao }}">
                                                    <i class="fas fa-info-circle"></i> Excluir
                                                </button>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Modal de confirmação de exclusão -->
                                    @if(Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
                                        <div class="modal fade" id="modalExcluirConcessao{{ $concessao->id_concessao }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Excluir Concessão de Recesso</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('termos.recesso.excluir', $concessao->id_concessao) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class="modal-body">
                                                            <p>Tem certeza que deseja excluir esta concessão?</p>
                                                            <ul class="mb-3">
                                                                <li><strong>Período:</strong>
                                                                    {{ $concessao->data_inicio_recesso->format('d/m/Y') }} a
                                                                    {{ $concessao->data_fim_recesso->format('d/m/Y') }}
                                                                </li>
                                                                <li><strong>Dias:</strong> {{ $concessao->total_dias }}</li>
                                                            </ul>
                                                            <div class="alert alert-info">
                                                                <i class="fas fa-info-circle"></i> Os dias serão devolvidos ao saldo de
                                                                recesso do estagiário.
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fas fa-trash"></i> Confirmar Exclusão
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Modal informativo para Empresa -->
                                        <div class="modal fade" id="modalSolicitarExclusao{{ $concessao->id_concessao }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Solicitar Exclusão</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Para excluir uma concessão de recesso, por favor solicite à equipe
                                                            <strong>EBCP</strong>.
                                                        </p>
                                                        <ul class="mb-3">
                                                            <li><strong>Período:</strong>
                                                                {{ $concessao->data_inicio_recesso->format('d/m/Y') }} a
                                                                {{ $concessao->data_fim_recesso->format('d/m/Y') }}
                                                            </li>
                                                            <li><strong>Dias:</strong> {{ $concessao->total_dias }}</li>
                                                        </ul>
                                                        <div class="alert alert-secondary">
                                                            <i class="fas fa-envelope"></i> Entre em contato pelos canais habituais
                                                            informando o período e o motivo.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-primary"
                                                            data-bs-dismiss="modal">Entendi</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mb-0"><i class="fas fa-info-circle"></i> Nenhuma concessão de recesso registrada ainda.</p>
                @endif
            </div>
        </div>
    @endif

    <!-- Modal para rescindir o contrato -->
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Rescindir Contrato</h5>
                    <button type="button" data-dismiss="modal" aria-label="Close" style="border: none; background: none;">
                        <span aria-hidden="true" style="font-weight: bold; font-size: 150%;">✕</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('rescisoes.store', $termo->id_termo) }}" method="POST" id="simpleForm">
                        @csrf
                        <input type="hidden" name="fk_id_termo" value="{{ $termo->id_termo }}">
                        <div class="form-group">
                            <label for="data_rescisao">Data da Rescisão</label>
                            <input type="date" class="form-control" id="data_rescisao" name="data_rescisao"
                                min="{{ \Carbon\Carbon::parse($termo->data_inicio_estagio)->format('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="motivo">Motivo</label>
                            <textarea class="form-control" id="motivo" name="motivo" rows="3" required
                                placeholder="Descreva o motivo da rescisão"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary" form="simpleForm">Enviar</button>
                </div>
            </div>
        </div>
    </div>

    @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
        <!-- Modal ZapSign -->
        <div class="modal fade" id="zapSignModalShow" tabindex="-1" aria-labelledby="zapSignModalShowLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="zapSignModalShowLabel">
                            <i class="fas fa-file-signature me-2"></i>
                            Enviar para Assinatura ZapSign
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-break">
                        <p>Deseja enviar este termo para assinatura eletrônica via ZapSign?</p>
                        <p><strong>Termo:</strong> {{ $termo->numero_termo }}/{{ $termo->ano_termo }}</p>
                        <p><strong>Estagiário:</strong> {{ $termo->estagiario->nome_estagiario }}</p>
                        @if($termo->estagiario->email)
                            <p><strong>Email:</strong> {{ $termo->estagiario->email }}</p>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Atenção: Este estagiário não possui email cadastrado!
                            </div>
                        @endif

                        <hr class="my-2">
                        <form action="{{ route('termos.enviarZapSign', $termo->id_termo) }}" method="POST"
                            style="display:inline-block; width: 100%;">
                            @csrf
                        <p class="mb-1">
                            <strong>Destinatários</strong>
                            <span class="text-muted small">(clique nas setas para reordenar)</span>
                        </p>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-2 table-recipients" style="font-size: 9pt "
                                id="tabelaDestinatariosShow">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 90px;">Remover?</th>
                                        <th style="width: 50px;">Ordem</th>
                                        <th style="width: 120px;">Tipo</th>
                                        <th>Nome</th>
                                        <th style="width: 35%;">E-mail</th>
                                        <th style="width: 20%;">Representante</th>
                                    </tr>
                                </thead>
                                <tbody id="tbodyDestinatariosShow">
                                    <tr data-ordem="1" data-tipo="estagiario">
                                        <td class="text-center">
                                            @if(!empty($termo->estagiario->email))
                                                <input type="checkbox" class="form-check-input"
                                                    name="remover_destinatarios[]" value="{{ $termo->estagiario->email }}">
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-link p-0"
                                                onclick="moverLinhaShow(this, -1)" title="Mover para cima">
                                                <i class="fas fa-arrow-up text-primary"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-link p-0"
                                                onclick="moverLinhaShow(this, 1)" title="Mover para baixo">
                                                <i class="fas fa-arrow-down text-primary"></i>
                                            </button>
                                        </td>
                                        <td><i class="fas fa-user text-primary me-1"></i> Estagiário</td>
                                        <td>{{ $termo->estagiario->nome_estagiario }}</td>
                                        <td>{{ $termo->estagiario->email ?? '—' }}</td>
                                        <td>—</td>
                                    </tr>
                                    <tr data-ordem="2" data-tipo="ebcp">
                                        <td class="text-center">
                                            <input type="checkbox" class="form-check-input" name="remover_destinatarios[]"
                                                value="moacirecetista@hotmail.com">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-link p-0"
                                                onclick="moverLinhaShow(this, -1)" title="Mover para cima">
                                                <i class="fas fa-arrow-up text-primary"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-link p-0"
                                                onclick="moverLinhaShow(this, 1)" title="Mover para baixo">
                                                <i class="fas fa-arrow-down text-primary"></i>
                                            </button>
                                        </td>
                                        <td><i class="fas fa-handshake text-info me-1"></i> Ag. Integração
                                        </td>
                                        <td>EBCP CONSULTORIA LTDA</td>
                                        <td>moacirecetista@hotmail.com</td>
                                        <td>Moacir Aguiar</td>
                                    </tr>

                                    {{-- Representantes da Empresa --}}
                                    @php $ordem = 3; @endphp
                                    @if(isset($termo->empresa) && $termo->empresa->representantes->count() > 0)
                                        @foreach($termo->empresa->representantes as $rep)
                                            <tr data-ordem="{{ $ordem++ }}" data-tipo="empresa_rep">
                                                <td class="text-center">
                                                    @if(!empty($rep->email))
                                                        <input type="checkbox" class="form-check-input"
                                                            name="remover_destinatarios[]" value="{{ $rep->email }}">
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-link p-0"
                                                        onclick="moverLinhaShow(this, -1)" title="Mover para cima">
                                                        <i class="fas fa-arrow-up text-primary"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-link p-0"
                                                        onclick="moverLinhaShow(this, 1)" title="Mover para baixo">
                                                        <i class="fas fa-arrow-down text-primary"></i>
                                                    </button>
                                                </td>
                                                <td><i class="fas fa-building text-secondary me-1"></i> Unidade</td>
                                                <td>{{ $termo->empresa->nome_empresa }}</td>
                                                <td>{{ $rep->email }}</td>
                                                <td>{{ $rep->nome }}</td>
                                            </tr>
                                        @endforeach
                                    @elseif(isset($termo->empresa))
                                        <tr data-ordem="{{ $ordem++ }}" data-tipo="empresa_legado">
                                            <td class="text-center">
                                                @if(!empty($termo->empresa->email))
                                                    <input type="checkbox" class="form-check-input"
                                                        name="remover_destinatarios[]" value="{{ $termo->empresa->email }}">
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-sm btn-link p-0"
                                                    onclick="moverLinhaShow(this, -1)" title="Mover para cima">
                                                    <i class="fas fa-arrow-up text-primary"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-link p-0"
                                                    onclick="moverLinhaShow(this, 1)" title="Mover para baixo">
                                                    <i class="fas fa-arrow-down text-primary"></i>
                                                </button>
                                            </td>
                                            <td><i class="fas fa-building text-secondary me-1"></i> Unidade</td>
                                            <td>{{ $termo->empresa->nome_empresa }}</td>
                                            <td>{{ $termo->empresa->email ?? '—' }}</td>
                                            <td>{{ $termo->empresa->nome_representante ?? '—' }}</td>
                                        </tr>
                                    @endif

                                    {{-- Representantes da Escola --}}
                                    @if(isset($termo->escola) && !$termo->escola->nao_assina_zapsign)
                                        @if($termo->escola->representantes->count() > 0)
                                            @foreach($termo->escola->representantes as $rep)
                                                <tr data-ordem="{{ $ordem++ }}" data-tipo="escola_rep">
                                                    <td class="text-center">
                                                        @if(!empty($rep->email))
                                                            <input type="checkbox" class="form-check-input"
                                                                name="remover_destinatarios[]"
                                                                value="{{ $rep->email }}">
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-link p-0"
                                                            onclick="moverLinhaShow(this, -1)" title="Mover para cima">
                                                            <i class="fas fa-arrow-up text-primary"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-link p-0"
                                                            onclick="moverLinhaShow(this, 1)" title="Mover para baixo">
                                                            <i class="fas fa-arrow-down text-primary"></i>
                                                        </button>
                                                    </td>
                                                    <td><i class="fas fa-school text-success me-1"></i> Instituição</td>
                                                    <td>{{ $termo->escola->nome_escola }}</td>
                                                    <td>{{ $rep->email }}</td>
                                                    <td>{{ $rep->nome }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr data-ordem="{{ $ordem++ }}" data-tipo="escola_legado">
                                                <td class="text-center">
                                                    @if(!empty($termo->escola->email))
                                                        <input type="checkbox" class="form-check-input"
                                                            name="remover_destinatarios[]" value="{{ $termo->escola->email }}">
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-link p-0"
                                                        onclick="moverLinhaShow(this, -1)" title="Mover para cima">
                                                        <i class="fas fa-arrow-up text-primary"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-link p-0"
                                                        onclick="moverLinhaShow(this, 1)" title="Mover para baixo">
                                                        <i class="fas fa-arrow-down text-primary"></i>
                                                    </button>
                                                </td>
                                                <td><i class="fas fa-school text-success me-1"></i> Instituição</td>
                                                <td>{{ $termo->escola->nome_escola }}</td>
                                                <td>{{ $termo->escola->email ?? '—' }}</td>
                                                <td>{{ $termo->escola->nome_representante ?? '—' }}</td>
                                            </tr>
                                        @endif
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <p class="text-muted small mb-0">
                            <strong>Observação:</strong> Marque em <strong>Remover?</strong> quem não deve receber
                            este envio específico no ZapSign.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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

    <!-- Modal Recesso -->
    <div class="modal fade" id="recessoModal" tabindex="-1" aria-labelledby="recessoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recessoModalLabel">
                        <i class="fas fa-umbrella-beach me-2"></i>
                        Concessão de Recesso
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @php
                        $hoje = \Carbon\Carbon::today();
                        $inicioContrato = \Carbon\Carbon::parse($termo->data_inicio_estagio);
                        $diasTrabalhados = max(0, $inicioContrato->diffInDays($hoje));
                        $recessoAcumulado = (30 * $diasTrabalhados) / 360;
                        $saldoAtual = (int) ($termo->saldo_recesso ?? 30);
                        $jaUsado = 30 - $saldoAtual;
                        $recessoDisponivel = max(0, $recessoAcumulado - $jaUsado);
                        $recessoDisponivelInt = (int) round($recessoDisponivel);
                    @endphp

                    <div class="alert alert-info">
                        <div><strong>Este estagiário tem direito a <span
                                    id="diasDisponiveisSpan">{{ $recessoDisponivelInt }}</span> dia(s) de recesso
                                disponíveis hoje.</strong></div>
                        <hr class="my-2">
                        <div style="font-size: 0.95rem;">
                            Cálculo:
                            <ul class="mb-0">
                                <li>Base: 30 dias por 360 dias trabalhados</li>
                                <li>Dias trabalhados desde o início do contrato: <strong>{{ $diasTrabalhados }}</strong>
                                </li>
                                <li>Recesso acumulado = (30 × {{ $diasTrabalhados }}) ÷ 360 =
                                    <strong>{{ number_format($recessoAcumulado, 2, ',', '.') }}</strong> dia(s)
                                </li>
                                <li>Já utilizado: 30 − saldo_recesso ({{ $saldoAtual }}) = <strong>{{ $jaUsado }}</strong>
                                    dia(s)</li>
                                <li>Disponível = acumulado − já utilizado =
                                    <strong>{{ number_format($recessoDisponivel, 2, ',', '.') }}</strong> ⇒ considerado
                                    <strong>{{ $recessoDisponivelInt }}</strong> dia(s) inteiros
                                </li>
                            </ul>
                        </div>
                    </div>

                    <form id="formRecesso" action="{{ route('termos.recesso.gerar', $termo->id_termo) }}" method="POST">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-sm-4">
                                <label for="data_inicio_recesso" class="form-label">Início do recesso</label>
                                <input type="date" class="form-control" id="data_inicio_recesso" name="data_inicio_recesso"
                                    min="{{ \Carbon\Carbon::parse($termo->data_inicio_estagio)->format('Y-m-d') }}"
                                    max="{{ \Carbon\Carbon::parse($termo->data_fim_estagio)->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-sm-4">
                                <label for="data_fim_recesso" class="form-label">Fim do recesso</label>
                                <input type="date" class="form-control" id="data_fim_recesso" name="data_fim_recesso"
                                    min="{{ \Carbon\Carbon::parse($termo->data_inicio_estagio)->format('Y-m-d') }}"
                                    max="{{ \Carbon\Carbon::parse($termo->data_fim_estagio)->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-text mb-1">Total do intervalo selecionado</div>
                                <div class="h5 mb-0"><span id="diasIntervaloSpan">0</span> dia(s)</div>
                                <div id="avisoExcede" class="text-danger small d-none mt-1"><i
                                        class="fas fa-exclamation-triangle"></i> Intervalo selecionado excede o disponível.
                                </div>
                                <div id="avisoConflito" class="text-danger small d-none mt-1"><i
                                        class="fas fa-exclamation-triangle"></i> Intervalo selecionado conflita com um
                                    recesso já registrado.</div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnGerarRecesso" onclick="enviarRecessoNovaGuia()"
                        disabled>
                        <i class="fas fa-file-pdf me-1"></i>
                        Gerar PDF e abater saldo
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Função para mover linha na modal da página de detalhes
        function moverLinhaShow(botao, direcao) {
            const tr = botao.closest('tr');
            const tbody = document.getElementById('tbodyDestinatariosShow');
            const linhas = Array.from(tbody.querySelectorAll('tr'));
            const indexAtual = linhas.indexOf(tr);

            const novoIndex = indexAtual + direcao;

            if (novoIndex < 0 || novoIndex >= linhas.length) {
                return;
            }

            if (direcao === -1) {
                tbody.insertBefore(tr, linhas[novoIndex]);
            } else {
                if (novoIndex + 1 < linhas.length) {
                    tbody.insertBefore(tr, linhas[novoIndex + 1]);
                } else {
                    tbody.appendChild(tr);
                }
            }

            atualizarOrdensShow();
        }

        function atualizarOrdensShow() {
            const tbody = document.getElementById('tbodyDestinatariosShow');
            const linhas = tbody.querySelectorAll('tr');
            linhas.forEach((tr, index) => {
                tr.setAttribute('data-ordem', index + 1);
            });
        }

        function copyEmailToClipboard(email, btn) {
            if (!email) return;
            const originalHTML = btn.innerHTML;
            const setCopied = () => {
                btn.innerHTML = '<i class="fas fa-check"></i> Copiado!';
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('btn-success');
                setTimeout(() => {
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-primary');
                }, 1500);
            };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(email).then(setCopied).catch(() => fallbackCopy(email, btn, setCopied));
            } else {
                fallbackCopy(email, btn, setCopied);
            }
        }

        function fallbackCopy(text, btn, onSuccess) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.top = '-9999px';
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            try {
                const successful = document.execCommand('copy');
                if (successful && typeof onSuccess === 'function') onSuccess();
            } catch (err) {
                console.error('Falha ao copiar:', err);
            }
            document.body.removeChild(textarea);
        }

        // ===== Recesso: cálculo do intervalo e validação =====
        (function () {
            const inicio = document.getElementById('data_inicio_recesso');
            const fim = document.getElementById('data_fim_recesso');
            const diasIntervaloSpan = document.getElementById('diasIntervaloSpan');
            const avisoExcede = document.getElementById('avisoExcede');
            const avisoConflito = document.getElementById('avisoConflito');
            const btnGerar = document.getElementById('btnGerarRecesso');
            const diasDisponiveis = parseInt(document.getElementById('diasDisponiveisSpan')?.innerText || '0', 10);

            // Lista de concessões ativas para checar conflito no cliente
            const concessoesAtivas = @json(
                ($termo->concessoesRecesso->where('status', 'ativo')->values()->map(fn($c) => [
                    'inicio' => $c->data_inicio_recesso->format('Y-m-d'),
                    'fim' => $c->data_fim_recesso->format('Y-m-d')
                ]))
            );

            function existeConflito(di, df) {
                if (!Array.isArray(concessoesAtivas)) return false;
                const diTime = di.getTime();
                const dfTime = df.getTime();
                for (const c of concessoesAtivas) {
                    const ci = new Date(c.inicio).getTime();
                    const cf = new Date(c.fim).getTime();
                    // Sobreposição inclusiva: (di <= cf) && (df >= ci)
                    if (diTime <= cf && dfTime >= ci) {
                        return true;
                    }
                }
                return false;
            }

            function atualizar() {
                let habilitar = false;
                let total = 0;
                let conflito = false;
                if (inicio && fim && inicio.value && fim.value) {
                    const di = new Date(inicio.value);
                    const df = new Date(fim.value);
                    if (df >= di) {
                        const diffMs = df.getTime() - di.getTime();
                        total = Math.floor(diffMs / (1000 * 60 * 60 * 24)) + 1; // Inclusivo
                        conflito = existeConflito(di, df);
                        habilitar = total > 0 && total <= diasDisponiveis && !conflito;
                    }
                }
                diasIntervaloSpan && (diasIntervaloSpan.innerText = String(total));
                if (avisoExcede) {
                    if (total > diasDisponiveis) {
                        avisoExcede.classList.remove('d-none');
                    } else {
                        avisoExcede.classList.add('d-none');
                    }
                }
                if (avisoConflito) {
                    if (conflito) {
                        avisoConflito.classList.remove('d-none');
                    } else {
                        avisoConflito.classList.add('d-none');
                    }
                }
                if (btnGerar) btnGerar.disabled = !habilitar;
            }

            inicio && inicio.addEventListener('change', atualizar);
            fim && fim.addEventListener('change', atualizar);
        })();

        // ===== Recesso: enviar via form e abrir em nova guia =====
        function enviarRecessoNovaGuia() {
            const form = document.getElementById('formRecesso');
            if (!form) return;

            // Mudar action do form temporariamente para abrir em nova guia
            form.setAttribute('target', '_blank');
            form.submit();

            // Fechar modal e recarregar página após envio
            setTimeout(() => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('recessoModal'));
                modal && modal.hide();
                // Recarregar a página para refletir o saldo atualizado
                window.location.reload();
            }, 500);
        }
    </script>

    <!-- Modal ZapSign Rescisão -->
    @if($termo->rescisao)
        @if(!$termo->rescisao->zapsign_doc_token)
            <div class="modal fade" id="zapSignModalRescisao" tabindex="-1" aria-labelledby="zapSignModalRescisaoLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="zapSignModalRescisaoLabel">
                                <i class="fas fa-file-signature me-2"></i>
                                Enviar Rescisão para Assinatura ZapSign
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-break">
                            <p>Deseja enviar este termo de rescisão para assinatura eletrônica via ZapSign?</p>
                            <p><strong>Termo:</strong> {{ $termo->numero_termo }}/{{ $termo->ano_termo }}</p>
                            <p><strong>Estagiário:</strong> {{ $termo->estagiario->nome_estagiario }}</p>
                            <p><strong>Data da Rescisão:</strong>
                                {{ \Carbon\Carbon::parse($termo->rescisao->data_rescisao)->format('d/m/Y') }}</p>

                            <hr class="my-2">
                            <p class="mb-1">
                                <strong>Destinatários que receberão o documento:</strong>
                            </p>
                            <div class="table-responsive">
                                <form action="{{ route('rescisoes.enviarZapSign', $termo->rescisao->id_rescisao) }}" method="POST"
                                    style="display:inline-block; width: 100%;">
                                    @csrf
                                <table class="table table-sm table-bordered mb-2" style="font-size: 9pt">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 90px;">Remover?</th>
                                            <th style="width: 120px;">Tipo</th>
                                            <th>Nome</th>
                                            <th style="width: 35%;">E-mail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">
                                                @if(!empty($termo->estagiario->email))
                                                    <input type="checkbox" class="form-check-input"
                                                        name="remover_destinatarios[]" value="{{ $termo->estagiario->email }}">
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td><i class="fas fa-user text-primary me-1"></i> Estagiário</td>
                                            <td>{{ $termo->estagiario->nome_estagiario }}</td>
                                            <td>{{ $termo->estagiario->email ?? '—' }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">
                                                <input type="checkbox" class="form-check-input" name="remover_destinatarios[]"
                                                    value="moacirecetista@hotmail.com">
                                            </td>
                                            <td><i class="fas fa-handshake text-info me-1"></i> Ag. Integração</td>
                                            <td>EBCP CONSULTORIA LTDA</td>
                                            <td>moacirecetista@hotmail.com</td>
                                        </tr>
                                        {{-- Representantes da Empresa --}}
                                        @if(isset($termo->empresa) && $termo->empresa->representantes->count() > 0)
                                            @foreach($termo->empresa->representantes as $rep)
                                                <tr>
                                                    <td class="text-center">
                                                        @if(!empty($rep->email))
                                                            <input type="checkbox" class="form-check-input"
                                                                name="remover_destinatarios[]" value="{{ $rep->email }}">
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td><i class="fas fa-building text-secondary me-1"></i> Concedente</td>
                                                    <td>{{ $rep->nome }}</td>
                                                    <td>{{ $rep->email }}</td>
                                                </tr>
                                            @endforeach
                                        @elseif(isset($termo->empresa))
                                            <tr>
                                                <td class="text-center">
                                                    @if(!empty($termo->empresa->email))
                                                        <input type="checkbox" class="form-check-input"
                                                            name="remover_destinatarios[]" value="{{ $termo->empresa->email }}">
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td><i class="fas fa-building text-secondary me-1"></i> Concedente</td>
                                                <td>{{ $termo->empresa->nome_representante ?? $termo->empresa->nome_empresa }}</td>
                                                <td>{{ $termo->empresa->email ?? '—' }}</td>
                                            </tr>
                                        @endif
                                        {{-- Representantes da Escola --}}
                                        @if(isset($termo->escola) && !$termo->escola->nao_assina_zapsign)
                                            @if($termo->escola->representantes->count() > 0)
                                                @foreach($termo->escola->representantes as $rep)
                                                    <tr>
                                                        <td class="text-center">
                                                            @if(!empty($rep->email))
                                                                <input type="checkbox" class="form-check-input"
                                                                    name="remover_destinatarios[]" value="{{ $rep->email }}">
                                                            @else
                                                                —
                                                            @endif
                                                        </td>
                                                        <td><i class="fas fa-school text-success me-1"></i> Instituição</td>
                                                        <td>{{ $rep->nome }}</td>
                                                        <td>{{ $rep->email }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td class="text-center">
                                                        @if(!empty($termo->escola->email))
                                                            <input type="checkbox" class="form-check-input"
                                                                name="remover_destinatarios[]" value="{{ $termo->escola->email }}">
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td><i class="fas fa-school text-success me-1"></i> Instituição</td>
                                                    <td>{{ $termo->escola->nome_representante ?? $termo->escola->nome_escola }}</td>
                                                    <td>{{ $termo->escola->email ?? '—' }}</td>
                                                </tr>
                                            @endif
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <p class="text-muted small mb-0">
                                <strong>Observação:</strong> Marque em <strong>Remover?</strong> quem não deve receber
                                este envio específico.
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
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
    @endif
@endsection