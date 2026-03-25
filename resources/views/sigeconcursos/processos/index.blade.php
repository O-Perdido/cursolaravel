@extends('layouts.main')

@section('title', 'SIGE Concursos | Processos')

@section('content')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body pb-2 pt-3">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h4 class="mb-0">
                    <i class="fa-solid fa-folder-tree me-2 text-primary"></i>
                    Processos
                </h4>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('sigeconcursos.processos.create') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i> Novo Processo
                    </a>
                    <a href="{{ route('sigeconcursos.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left"></i> Voltar ao dashboard
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('sigeconcursos.processos.index') }}">
                <div class="row align-items-end g-2">
                    <div class="col-md-3">
                        <label for="titulo" class="form-label mb-1">Título</label>
                        <input type="text" class="form-control form-control-sm" id="titulo" name="titulo"
                            value="{{ request('titulo') }}" placeholder="Título do processo">
                    </div>

                    <div class="col-md-2">
                        <label for="numero_edital" class="form-label mb-1">Edital</label>
                        <input type="text" class="form-control form-control-sm" id="numero_edital" name="numero_edital"
                            value="{{ request('numero_edital') }}" placeholder="Número do edital">
                    </div>

                    <div class="col-md-2">
                        <label for="fk_id_empresa" class="form-label mb-1">Órgão</label>
                        <select class="form-select form-select-sm" id="fk_id_empresa" name="fk_id_empresa">
                            <option value="">Todos</option>
                            @foreach($orgaos as $orgao)
                                <option value="{{ $orgao->id_empresa }}" {{ (string) request('fk_id_empresa') === (string) $orgao->id_empresa ? 'selected' : '' }}>
                                    {{ $orgao->nome_razao_social }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="tipo_processo" class="form-label mb-1">Tipo</label>
                        <select class="form-select form-select-sm" id="tipo_processo" name="tipo_processo">
                            <option value="">Todos</option>
                            <option value="concurso_publico" {{ request('tipo_processo') === 'concurso_publico' ? 'selected' : '' }}>Concurso Público</option>
                            <option value="processo_seletivo" {{ request('tipo_processo') === 'processo_seletivo' ? 'selected' : '' }}>Processo Seletivo</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="status" class="form-label mb-1">Status</label>
                        <select class="form-select form-select-sm" id="status" name="status">
                            <option value="">Todos</option>
                            @foreach(['rascunho', 'publicado', 'inscricoes_abertas', 'inscricoes_encerradas', 'em_andamento', 'finalizado', 'suspenso'] as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-1 d-grid">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div class="row align-items-end g-2 mt-1">
                    <div class="col-md-2">
                        <label for="data_publicacao_inicio" class="form-label mb-1">Publicação de</label>
                        <input type="date" class="form-control form-control-sm" id="data_publicacao_inicio"
                            name="data_publicacao_inicio" value="{{ request('data_publicacao_inicio') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="data_publicacao_fim" class="form-label mb-1">Publicação até</label>
                        <input type="date" class="form-control form-control-sm" id="data_publicacao_fim"
                            name="data_publicacao_fim" value="{{ request('data_publicacao_fim') }}">
                    </div>
                    <div class="col-md-2">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="ordem_cadastro" name="ordem_cadastro"
                                value="1" {{ request('ordem_cadastro') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="ordem_cadastro">Recentes</label>
                        </div>
                    </div>
                    <div class="col-md-2 d-grid">
                        <a href="{{ route('sigeconcursos.processos.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-eraser me-1"></i> Limpar filtros
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="mb-2" style="font-weight: bold; font-size: 1.1em;">
        Total de processos: {{ method_exists($processos, 'total') ? $processos->total() : $processos->count() }}
    </div>

    @if (method_exists($processos, 'links'))
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center">
                <span class="text-muted small">
                    @if($processos->total() > 0)
                        Mostrando {{ $processos->firstItem() }}–{{ $processos->lastItem() }} de {{ $processos->total() }}
                    @else
                        Nenhum registro encontrado
                    @endif
                </span>
                @php $pp = request('per_page', '25'); @endphp
                <select class="form-select form-select-sm" onchange="changePerPage(this.value)"
                    style="display: inline-block; width: auto; margin-left: 10px; font-size: 0.875rem;">
                    <option value="25" {{ $pp == '25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $pp == '50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $pp == '100' ? 'selected' : '' }}>100</option>
                    <option value="200" {{ $pp == '200' ? 'selected' : '' }}>200</option>
                    <option value="all" {{ $pp == 'all' ? 'selected' : '' }}>Tudo</option>
                </select>
            </div>
            <div>
                {{ $processos->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <div style="max-height: 560px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Processo</th>
                    <th>Órgão</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th>Inscrições</th>
                    <th style="width: 250px;">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($processos as $processo)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $processo->titulo }}</div>
                            <div class="text-muted small">{{ $processo->numero_processo ?: 'Número pendente' }} • Edital {{ $processo->numero_edital }}</div>
                        </td>
                        <td>{{ $processo->empresa?->nome_razao_social }}</td>
                        <td>{{ $processo->tipo_processo === 'concurso_publico' ? 'Concurso Público' : 'Processo Seletivo' }}</td>
                        <td>
                            <span class="badge bg-primary">{{ ucfirst(str_replace('_', ' ', $processo->status)) }}</span>
                        </td>
                        <td>
                            <div class="small">{{ $processo->data_inicio_inscricoes?->format('d/m/Y H:i') ?: 'Não definido' }}</div>
                            <div class="small text-muted">até {{ $processo->data_fim_inscricoes?->format('d/m/Y H:i') ?: 'Não definido' }}</div>
                        </td>
                        <td>
                            <a href="{{ route('sigeconcursos.processos.show', $processo->id_processo) }}" class="btn btn-sm btn-info">Detalhes</a>
                            <a href="{{ route('sigeconcursos.processos.edit', $processo->id_processo) }}" class="btn btn-sm btn-primary">Editar</a>
                            <form action="{{ route('sigeconcursos.processos.destroy', $processo->id_processo) }}" method="POST"
                                style="display:inline-block" onsubmit="return confirm('Confirma a exclusão deste processo?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Nenhum processo encontrado com os filtros informados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (method_exists($processos, 'links'))
        <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="text-muted small">
                @if($processos->total() > 0)
                    Mostrando {{ $processos->firstItem() }}–{{ $processos->lastItem() }} de {{ $processos->total() }}
                @else
                    Nenhum registro encontrado
                @endif
            </div>
            <div>
                {{ $processos->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif

    <script>
        function changePerPage(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', value);
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }
    </script>
@endsection