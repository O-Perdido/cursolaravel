@extends('layouts.main')

@section('title', 'SIGE Concursos | Detalhes do Processo')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h2 class="mb-1">{{ $processo->titulo }}</h2>
            <p class="text-muted mb-0">
                Edital {{ $processo->numero_edital ?: ($processo->numero_processo ?: 'Sem numero') }} -
                {{ $processo->empresa?->nome_razao_social }}
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('sigeconcursos.publico.processos.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Voltar
            </a>
            <a href="{{ route('sigeconcursos.candidato.login') }}" class="btn btn-primary">
                <i class="fa-solid fa-right-to-bracket me-1"></i> Login do Candidato
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white"><strong>Resumo do Processo</strong></div>
                <div class="card-body">
                    <p class="mb-1"><strong>Tipo:</strong>
                        {{ $processo->tipo_processo === 'concurso_publico' ? 'Concurso Publico' : 'Processo Seletivo' }}</p>
                    <p class="mb-1"><strong>Publicacao:</strong>
                        {{ $processo->data_publicacao?->format('d/m/Y H:i') ?: 'Nao informada' }}</p>
                    <p class="mb-1"><strong>Inicio das inscricoes:</strong>
                        {{ $processo->data_inicio_inscricoes?->format('d/m/Y H:i') ?: 'Nao definido' }}</p>
                    <p class="mb-1"><strong>Fim das inscricoes:</strong>
                        {{ $processo->data_fim_inscricoes?->format('d/m/Y H:i') ?: 'Nao definido' }}</p>
                    <p class="mb-3"><strong>Data da prova:</strong>
                        {{ $processo->data_prova?->format('d/m/Y H:i') ?: 'Nao definida' }}</p>

                    @if($processo->resumo)
                        <h6 class="text-muted">Resumo</h6>
                        <div class="border rounded p-3 bg-light mb-3" style="white-space: pre-line;">{{ $processo->resumo }}
                        </div>
                    @endif

                    @if($processo->descricao)
                        <h6 class="text-muted">Descricao</h6>
                        <div class="border rounded p-3 bg-light" style="white-space: pre-line;">{{ $processo->descricao }}</div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white"><strong>Cargos Vinculados</strong></div>
                <div class="card-body">
                    @forelse($processo->processoCargos as $item)
                        <div class="border rounded p-3 mb-2 bg-light">
                            <div class="fw-semibold">{{ $item->cargo?->nome_cargo }}</div>
                            <div class="small text-muted">Vagas: {{ $item->quantidade_vagas ?? '0' }}</div>
                            <div class="small text-muted">Taxa:
                                {{ $item->valor_taxa_inscricao !== null ? 'R$ ' . number_format((float) $item->valor_taxa_inscricao, 2, ',', '.') : 'Seguir regra geral do processo' }}
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Nenhum cargo cadastrado.</p>
                    @endforelse
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white"><strong>Documentos Exigidos na Inscricao</strong></div>
                <div class="card-body">
                    @forelse($processo->documentosExigidos as $documento)
                        <div class="border rounded p-3 mb-2 bg-light">
                            <div class="fw-semibold">{{ $documento->titulo }}</div>
                            <div class="small text-muted">{{ $documento->obrigatorio ? 'Obrigatorio' : 'Opcional' }}</div>
                            <div class="small text-muted">{{ $documento->descricao ?: 'Sem orientacao adicional.' }}</div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Este processo nao possui documentos adicionais obrigatorios.</p>
                    @endforelse
                </div>
            </div>

            @if($processo->arquivos->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white"><strong>Arquivos do Processo</strong></div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($processo->arquivos as $arquivo)
                                <a href="{{ asset('storage/' . $arquivo->caminho_arquivo) }}" target="_blank"
                                    class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span>{{ $arquivo->nome_exibicao ?: 'Arquivo do processo' }}</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-muted"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white"><strong>Inscricao</strong></div>
                <div class="card-body">
                    @if($inscricaoExistente)
                        <div class="alert alert-success mb-3">
                            Voce ja possui inscricao neste processo.
                        </div>
                        <p class="mb-1"><strong>Numero:</strong> {{ $inscricaoExistente->numero_inscricao }}</p>
                        <p class="mb-1"><strong>Status:</strong>
                            {{ ucfirst(str_replace('_', ' ', (string) $inscricaoExistente->status_inscricao)) }}</p>
                        <a href="{{ route('sigeconcursos.candidato.minhas-inscricoes') }}"
                            class="btn btn-outline-success w-100 mt-2">
                            <i class="fa-solid fa-clipboard-list me-1"></i> Ir para minhas inscricoes
                        </a>
                    @elseif(!$podeInscrever)
                        <div class="alert alert-warning mb-0">
                            Este processo nao esta com inscricoes abertas no momento.
                        </div>
                    @else
                        @auth
                            @if(auth()->user()->nivel === 'candidato')
                                <a href="{{ route('sigeconcursos.candidato.processos.show', $processo->id_processo) }}"
                                    class="btn btn-primary w-100">
                                    <i class="fa-solid fa-file-signature me-1"></i> Ir para inscricao
                                </a>
                            @else
                                <div class="alert alert-info mb-3">
                                    A inscricao em concursos e exclusiva para usuarios com perfil de candidato.
                                </div>
                                <a href="{{ route('sigeconcursos.candidato.login') }}" class="btn btn-primary w-100">
                                    <i class="fa-solid fa-right-to-bracket me-1"></i> Entrar como candidato
                                </a>
                            @endif
                        @else
                            <div class="alert alert-info mb-3">
                                Para se inscrever, voce precisa ter cadastro e estar logado na area do candidato.
                            </div>
                            <div class="d-grid gap-2">
                                <a href="{{ route('sigeconcursos.candidato.login') }}" class="btn btn-primary">
                                    <i class="fa-solid fa-right-to-bracket me-1"></i> Ja tenho cadastro
                                </a>
                                <a href="{{ route('sigeconcursos.candidato.cadastro') }}" class="btn btn-outline-primary">
                                    <i class="fa-solid fa-user-plus me-1"></i> Quero me cadastrar
                                </a>
                            </div>
                        @endauth
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><strong>Regras rapidas</strong></div>
                <div class="card-body small text-muted">
                    <ul class="mb-0 ps-3">
                        <li>A inscricao e individual e vinculada ao seu cadastro de candidato.</li>
                        <li>Documentos exigidos no edital devem ser enviados no momento da inscricao.</li>
                        <li>Modalidades e regras de isencao seguem as configuracoes do processo.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection