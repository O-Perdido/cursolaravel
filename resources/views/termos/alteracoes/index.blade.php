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
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($alteracoesTermo->where('fk_id_termo', $termo->id_termo) as $alteracaoTermo)
                <tr style="vertical-align: middle">
                    <td>{{ $alteracaoTermo->termo->numero_termo }}/{{ $alteracaoTermo->termo->ano_termo }}</td>
                    <td>{{ \Carbon\Carbon::parse($alteracaoTermo->data_alteracao)->format('d/m/Y') }}</td>
                    <td>{{ $alteracaoTermo->descricao }}</td>
                    <td style="text-align: center; width: 200px;">
                        <a href="{{ route('alteracao.gerarPdf', [$alteracaoTermo->termo->id_termo, $alteracaoTermo->id_alteracao]) }}"
                            class="btn btn-sm btn-info" target="_blank">Gerar PDF</a>
                        @if (Auth::user()->nivel == 'admin' || Auth::user()->nivel == 'operador')
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
@endsection