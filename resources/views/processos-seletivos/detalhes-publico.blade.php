@extends('layouts.main')

@section('title', $processo->titulo)

@section('content')
    <div class="container-fluid py-3">
        <div class="mb-3">
            <a href="{{ route('processos-seletivos.publicos') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
        @php
            $statusLabels = [
                'aberto' => 'Publicado (inscrições em breve)',
                'inscricoes' => 'Inscrições Abertas',
                'encerrado' => 'Inscrições Encerradas',
                'finalizado' => 'Processo Concluído',
            ];
            $statusClasses = [
                'aberto' => 'bg-success',
                'inscricoes' => 'bg-primary',
                'encerrado' => 'bg-warning text-dark',
                'finalizado' => 'bg-dark',
            ];
            $inicioInscricoes = $processo->data_inicio_inscricoes ?? $processo->data_abertura;
            $fimInscricoes = $processo->data_fechamento_inscricoes;
            $inscricaoAbertaAgora = $processo->periodoInscricoesAberto();
            $inscricaoPermitida = $inscricaoAbertaAgora;
            $inscricaoEmBreve = $processo->inscricoesEmBreve();
            $processoEncerrado = $processo->inscricoesEncerradas();
            $processoFinalizado = $processo->status === 'finalizado';
        @endphp

        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm mb-3 overflow-hidden">
                    <div class="position-relative d-flex align-items-center justify-content-between gap-3 p-4 text-white"
                        style="background: linear-gradient(135deg, #102E6C 0%, #0A1F4D 100%); min-height: 160px;">
                        <div class="flex-grow-1">
                            <h3 class="mb-2">{{ $processo->titulo }}</h3>
                            <p class="text-white-50 mb-1 small">Processo {{ $processo->numero_processo }} •
                                {{ $processo->empresa->nome_empresa }}
                            </p>
                            <span class="badge {{ $statusClasses[$processo->status] ?? 'bg-secondary' }}">
                                {{ $statusLabels[$processo->status] ?? ucfirst($processo->status) }}
                            </span>
                        </div>
                        @if($processo->icone_processo)
                            <img src="{{ asset('storage/' . $processo->icone_processo) }}" alt="{{ $processo->titulo }}"
                                class="rounded"
                                style="width: 120px; height: 120px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                        @elseif($processo->empresa->logo_empresa)
                            <img src="{{ Storage::url($processo->empresa->logo_empresa) }}"
                                alt="{{ $processo->empresa->nome_empresa }}" class="rounded"
                                style="width: 120px; height: 120px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                        @else
                            <div class="rounded bg-white d-flex align-items-center justify-content-center"
                                style="width: 120px; height: 120px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                                <i class="fas fa-briefcase text-muted" style="font-size: 48px;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($processo->data_abertura || $inicioInscricoes || $fimInscricoes)
                            <div class="row g-2 mb-3">
                                @if($processo->data_abertura)
                                    <div class="col-md-4">
                                        <div class="p-2 border rounded text-center h-100">
                                            <div class="text-muted small"><i class="fas fa-bullhorn me-1"></i>Publicação</div>
                                            <div class="fw-semibold">{{ $processo->data_abertura->format('d/m H:i') }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if($inicioInscricoes)
                                    <div class="col-md-4">
                                        <div class="p-2 border rounded text-center h-100">
                                            <div class="text-muted small"><i class="fas fa-play me-1"></i>Início das Inscrições
                                            </div>
                                            <div class="fw-semibold">{{ $inicioInscricoes->format('d/m H:i') }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if($fimInscricoes)
                                    <div class="col-md-4">
                                        <div class="p-2 border rounded text-center h-100">
                                            <div class="text-muted small"><i class="fas fa-stop me-1"></i>Fim das Inscrições</div>
                                            <div class="fw-semibold">{{ $fimInscricoes->format('d/m H:i') }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                @php
                    $fases = $processo->fases ?? [];
                    $vagasNiveis = $processo->vagas_por_nivel ?? [];
                @endphp

                @if($fases && count($fases) > 0)
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-list me-2"></i>Fases do Processo</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Descrição</th>
                                            <th style="width: 35%">Período/Data</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($fases as $fase)
                                            <tr>
                                                <td>{{ $fase['descricao'] ?? '-' }}</td>
                                                <td>{{ $fase['periodo'] ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @elseif($processo->descricao_fases)
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-list me-2"></i>Fases do Processo</h6>
                        </div>
                        <div class="card-body">
                            <div style="white-space: pre-wrap; line-height: 1.6;">{{ $processo->descricao_fases }}</div>
                        </div>
                    </div>
                @endif

                @if($vagasNiveis && count($vagasNiveis) > 0)
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Vagas por Nível</h6>
                        </div>
                        <div class="card-body">
                            @foreach($vagasNiveis as $nivel)
                                <div class="mb-3">
                                    <h6 class="mb-2">{{ $nivel['nivel'] ?? 'Nível' }}</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Curso</th>
                                                    <th style="width: 25%">Vagas</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(($nivel['itens'] ?? []) as $curso)
                                                    <tr>
                                                        <td>{{ $curso['curso'] ?? '-' }}</td>
                                                        <td>{{ $curso['vagas'] ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif($processo->cursos_destino && count($processo->cursos_destino) > 0)
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Cursos Destinados</h6>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-sm-2 g-2">
                                @foreach($processo->cursos_destino as $curso)
                                    <div class="col">
                                        <div class="border rounded p-2 h-100 small">
                                            @if(is_array($curso))
                                                {{ $curso['nome'] ?? $curso['nome_curso'] ?? 'Curso' }}
                                            @else
                                                {{ $curso->nome_curso ?? $curso->nome ?? $curso }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if($processo->requisitos)
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Requisitos</h6>
                        </div>
                        <div class="card-body">
                            <div style="white-space: pre-wrap; line-height: 1.6;">{{ $processo->requisitos }}</div>
                        </div>
                    </div>
                @endif

                @if($processo->observacoes)
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-note me-2"></i>Observações</h6>
                        </div>
                        <div class="card-body">
                            <div style="white-space: pre-wrap; line-height: 1.6;">{{ $processo->observacoes }}</div>
                        </div>
                    </div>
                @endif

                @php
                    $editalArquivo = $processo->arquivos()->first();
                @endphp
                @if($editalArquivo)
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-header bg-light border-0">
                            <h5 class="mb-0"><i class="fas fa-file-pdf me-2 text-danger"></i>Documentos</h5>
                        </div>
                        <div class="card-body">
                            <div class="list-group">
                                <a href="{{ route('processos-seletivos.arquivos.download', $editalArquivo->id_arquivo) }}"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file-pdf me-2 text-danger"></i>
                                        <strong>{{ $editalArquivo->nome_exibicao ?? 'Edital do Processo' }}</strong>
                                    </div>
                                    <i class="fas fa-download text-muted"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-12 col-lg-4">
                <div class="card shadow-sm h-100 sticky-top" style="top: 20px;">
                    <div class="card-body d-flex flex-column gap-3">
                        @if($processo->empresa->logo_empresa)
                            <div class="text-center mb-2">
                                <img src="{{ Storage::url($processo->empresa->logo_empresa) }}"
                                    alt="{{ $processo->empresa->nome_empresa }}" class="img-fluid rounded"
                                    style="max-height: 100px;">
                            </div>
                        @endif

                        <div class="mb-2">
                            <p class="mb-1"><small class="text-muted">EMPRESA</small></p>
                            <p class="mb-0"><strong>{{ $processo->empresa->nome_empresa }}</strong></p>
                        </div>

                        @if($inicioInscricoes || $fimInscricoes)
                            <div class="mb-2">
                                <p class="mb-1"><small class="text-muted">JANELA DE INSCRIÇÃO</small></p>
                                <p class="mb-0">
                                    @if($inicioInscricoes)
                                        <strong>{{ $inicioInscricoes->format('d/m/Y H:i') }}</strong>
                                    @endif
                                    @if($inicioInscricoes && $fimInscricoes)
                                        <span class="text-muted mx-1">até</span>
                                    @endif
                                    @if($fimInscricoes)
                                        <strong>{{ $fimInscricoes->format('d/m/Y H:i') }}</strong>
                                    @endif
                                </p>
                                <div class="mt-1">
                                    @if($inscricaoPermitida)
                                        <span class="badge bg-success">Inscrições Abertas</span>
                                    @elseif($inscricaoEmBreve)
                                        <span class="badge bg-warning text-dark">Em breve</span>
                                    @elseif($processoFinalizado)
                                        <span class="badge bg-dark">Concluído</span>
                                    @elseif($processoEncerrado)
                                        <span class="badge bg-warning text-dark">Encerrado</span>
                                    @else
                                        <span class="badge bg-danger">Inscrições Fechadas</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($processo->vagas)
                            <div class="mb-2">
                                <p class="mb-1"><small class="text-muted">VAGAS DISPONÍVEIS</small></p>
                                <p class="mb-0"><strong>{{ $processo->vagas }} vaga(s)</strong></p>
                            </div>
                        @endif

                        @if($processo->salario_bolsa)
                            <div class="mb-2">
                                <p class="mb-1"><small class="text-muted">BOLSA</small></p>
                                <p class="mb-0"><strong>R$ {{ number_format($processo->salario_bolsa, 2, ',', '.') }}</strong>
                                </p>
                            </div>
                        @endif

                        <!-- CTA Inscrição de acordo com status -->
                        @if($inscricaoPermitida)
                            @auth
                                @if(Auth::user()->nivel === 'estagiario')
                                    @if($jaInscrito)
                                        <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <div>Você já está inscrito neste processo.</div>
                                        </div>
                                    @else
                                        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                                            data-bs-target="#modalInscrever">
                                            <i class="fas fa-pen-fancy me-1"></i> Inscrever-me
                                        </button>
                                    @endif
                                @else
                                    <div class="alert alert-info d-flex align-items-center mb-0" role="alert">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <div>Apenas estagiários podem se inscrever.</div>
                                    </div>
                                @endif
                            @else
                                <div class="d-grid gap-2">
                                    <a href="{{ route('login') }}?redirect={{ route('processos-seletivos.detalhes.publico', $processo->id_processo) }}"
                                        class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt me-1"></i> Entrar para inscrever
                                    </a>
                                    <a href="{{ route('novo-estagiario-ajax-create') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-user-plus me-1"></i> Criar conta
                                    </a>
                                </div>
                            @endauth
                        @elseif($inscricaoEmBreve)
                            <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                                <i class="fas fa-clock me-2"></i>
                                <div>Inscrições em breve @if($inicioInscricoes) (a partir de
                                {{ $inicioInscricoes->format('d/m H:i') }}) @endif.
                                </div>
                            </div>
                        @elseif($processoFinalizado)
                            <div class="alert alert-secondary d-flex align-items-center mb-0" role="alert">
                                <i class="fas fa-flag-checkered me-2"></i>
                                <div>Processo concluído.</div>
                            </div>
                        @elseif($processoEncerrado)
                            <div class="alert alert-warning d-flex align-items-center mb-0" role="alert">
                                <i class="fas fa-hourglass-end me-2"></i>
                                <div>Inscrições encerradas. Fases em andamento.</div>
                            </div>
                        @elseif($processo->status === 'inscricoes')
                            <div class="alert alert-info d-flex align-items-center mb-0" role="alert">
                                <i class="fas fa-sync-alt me-2"></i>
                                <div>Inscrições abertas, tente novamente em instantes.</div>
                            </div>
                        @else
                            <div class="alert alert-danger d-flex align-items-center mb-0" role="alert">
                                <i class="fas fa-times-circle me-2"></i>
                                <div>Inscrições indisponíveis no momento.</div>
                            </div>
                        @endif

                        <div class="border-top pt-2">
                            <div class="d-flex gap-2">
                                <a href="https://wa.me/?text={{ urlencode($processo->titulo . ' ' . url()->current()) }}"
                                    target="_blank" class="btn btn-sm btn-outline-success" title="WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}"
                                    target="_blank" class="btn btn-sm btn-outline-primary" title="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ url()->current() }}&text={{ urlencode($processo->titulo) }}"
                                    target="_blank" class="btn btn-sm btn-outline-info" title="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    onclick="navigator.clipboard.writeText('{{ url()->current() }}');" title="Copiar Link">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Inscrição (para usuários autenticados) -->
    @auth
        @if($inscricaoPermitida && Auth::user()->nivel === 'estagiario' && !$jaInscrito)
            <div class="modal fade" id="modalInscrever" tabindex="-1" aria-labelledby="labelInscrever" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="labelInscrever">Confirmar Inscrição</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>Você está prestes a se inscrever no processo:</p>
                            <div class="alert alert-info">
                                <strong>{{ $processo->titulo }}</strong><br>
                                <small>{{ $processo->empresa->nome_empresa }}</small>
                            </div>
                            <p>Deseja continuar?</p>

                            @if($processo->solicitar_upload_inscricao)
                                <div class="mb-3">
                                    <label for="arquivo_inscricao_publico" class="form-label">Anexe o arquivo para concluir</label>
                                    <input type="file" class="form-control" id="arquivo_inscricao_publico" name="arquivo_inscricao"
                                        accept=".pdf,image/*" required>
                                    <small class="text-muted">Envie um PDF ou imagem conforme orientações do processo.</small>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <form method="POST" action="{{ route('processos-seletivos.inscrever', $processo->id_processo) }}"
                                class="d-inline" enctype="multipart/form-data">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check me-1"></i> Confirmar Inscrição
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endauth
@endsection