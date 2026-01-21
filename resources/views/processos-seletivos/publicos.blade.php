@extends('layouts.main')

@section('title', 'Processos Seletivos Disponíveis')

@section('content')
    <div class="container-fluid py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('landing') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
                <h4 class="mb-0">Processos Seletivos Disponíveis</h4>
            </div>
            @auth
                <a href="{{ route('processos-seletivos.minhas-inscricoes') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-clipboard-list me-1"></i> Minhas Inscrições
                </a>
            @endauth
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-12 col-md-3">
                        <label for="search" class="form-label small fw-bold text-muted">Busca Rápida</label>
                        <input type="text" id="search" name="search" placeholder="Concedente, processo..."
                            class="form-control" value="{{ request('search') }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="curso" class="form-label small fw-bold text-muted">Curso</label>
                        <select id="curso" name="curso" class="form-select">
                            <option value="">Todos os cursos</option>
                            @foreach($todosCursos as $curso)
                                <option value="{{ $curso }}" {{ request('curso') == $curso ? 'selected' : '' }}>
                                    {{ $curso }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <label for="nivel" class="form-label small fw-bold text-muted">Nível</label>
                        <select id="nivel" name="nivel" class="form-select">
                            <option value="">Todos os níveis</option>
                            @foreach($todosNiveis as $nivel)
                                <option value="{{ $nivel }}" {{ request('nivel') == $nivel ? 'selected' : '' }}>
                                    {{ $nivel }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <label for="status" class="form-label small fw-bold text-muted">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="">Em andamento (padrão)</option>
                            <option value="encerrado" {{ request('status') == 'encerrado' ? 'selected' : '' }}>Encerrado
                                (etapas em andamento)</option>
                            <option value="finalizado" {{ request('status') == 'finalizado' ? 'selected' : '' }}>Concluído
                            </option>
                        </select>
                    </div>
                    <div class="col-12 col-md-2 d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Filtrar
                        </button>
                        <a href="{{ route('processos-seletivos.publicos') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-eraser me-1"></i> Limpar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        @php
            $statusLabels = [
                'aberto' => 'Publicado',
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
        @endphp

        @if($processos->count() > 0)
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                @foreach($processos as $processo)
                    <div class="col">
                        <div class="card h-100 shadow-sm border-0 overflow-hidden">
                            @if($processo->icone_processo)
                                <div class="position-relative bg-light d-flex align-items-center justify-content-center"
                                    style="height: 140px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <img src="{{ asset('storage/' . $processo->icone_processo) }}" alt="{{ $processo->titulo }}"
                                        class="rounded"
                                        style="width: 100px; height: 100px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                </div>
                            @endif
                            <div class="card-body d-flex flex-column gap-3">
                                <div class="d-flex align-items-center">
                                    @if($processo->empresa->logo_empresa)
                                        <img src="{{ Storage::url($processo->empresa->logo_empresa) }}"
                                            alt="{{ $processo->empresa->nome_empresa }}" class="rounded-circle me-2"
                                            style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2"
                                            style="width: 40px; height: 40px;">
                                            <i class="fas fa-building text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="flex-grow-1">
                                        <p class="mb-0 text-muted small">{{ $processo->empresa->nome_empresa }}</p>
                                        <span class="badge bg-light text-dark">{{ $processo->numero_processo }}</span>
                                    </div>
                                </div>

                                <div class="flex-grow-1">
                                    <h6 class="mb-2">{{ Str::limit($processo->titulo, 70) }}</h6>
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <span class="badge {{ $statusClasses[$processo->status] ?? 'bg-secondary' }}">
                                            {{ $statusLabels[$processo->status] ?? ucfirst($processo->status) }}
                                        </span>
                                    </div>
                                    @php
                                        $inicioInscricoes = $processo->data_inicio_inscricoes ?? $processo->data_abertura;
                                        $fimInscricoes = $processo->data_fechamento_inscricoes;
                                        $inscricaoEmBreve = $processo->inscricoesEmBreve();
                                    @endphp
                                    @if($inscricaoEmBreve && $inicioInscricoes)
                                        <p class="text-muted small mb-1"><i class="fas fa-calendar me-1"></i>Inscrições a partir de
                                            {{ $inicioInscricoes->format('d/m/Y') }}</p>
                                    @elseif($fimInscricoes)
                                        <p class="text-muted small mb-1"><i class="fas fa-calendar me-1"></i>Inscrições até
                                            {{ $fimInscricoes->format('d/m/Y') }}</p>
                                    @endif
                                    @if($processo->cursos_destino && count($processo->cursos_destino) > 0)
                                        <p class="text-muted small mb-0"><i
                                                class="fas fa-graduation-cap me-1"></i>{{ count($processo->cursos_destino) }} curso(s)
                                        </p>
                                    @endif
                                </div>

                                <div class="d-grid">
                                    <a href="{{ route('processos-seletivos.detalhes.publico', $processo->id_processo) }}"
                                        class="btn btn-primary">
                                        <i class="fas fa-eye me-1"></i> Ver Detalhes
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-center mt-4">
                {{ $processos->appends(request()->query())->links() }}
            </div>
        @else
            <div class="alert alert-info text-center py-4" role="alert">
                <i class="fas fa-info-circle me-2"></i> Nenhum processo seletivo disponível com os filtros selecionados.
            </div>
        @endif
    </div>
@endsection