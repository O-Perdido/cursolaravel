@extends('layouts.main')

@section('title', 'Cadastrar Termo de Estágio')

@section('content')

    <h1>Alterar Termo de Estágio - Número: {{ $termo_selecionado->numero_termo }}/{{ $termo_selecionado->ano_termo }}
    </h1>
    <button onclick="window.NavigationHistory?.goBack('{{ route('alteracoes.index', $id_termo)}}');"
        class="btn btn-secondary mb-3" title="Voltar para a página anterior com filtros preservados">Voltar</button>
    <form action="{{ route('alteracao.store', $id_termo) }}" method="POST">
        @csrf
        @method('POST')
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="fk_id_supervisor">Selecione o Supervisor</label>
                    <select class="form-control" id="fk_id_supervisor" name="fk_id_supervisor">
                        <option value="">Escolha um Supervisor</option>
                        @foreach($supervisores as $supervisor)
                            <option value="{{ $supervisor->id_supervisor }}">{{ $supervisor->nome_supervisor }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="nome_orientador_alteracao">Nome do Orientador</label>
                    <input type="text" class="form-control" id="nome_orientador_alteracao" name="nome_orientador_alteracao">
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="cargo_orientador_alteracao">Cargo do Orientador</label>
                    <input type="text" class="form-control" id="cargo_orientador_alteracao"
                        name="cargo_orientador_alteracao">
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="data_fim_estagio_alteracao">Data de Término do Estágio</label>
                    <input type="date" class="form-control" id="data_fim_estagio_alteracao"
                        name="data_fim_estagio_alteracao">
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="valor_bolsa_alteracao">Valor da Bolsa</label>
                    <input type="text" class="form-control" id="valor_bolsa_alteracao" name="valor_bolsa_alteracao"
                        pattern="^\d+(\,\d{1,2})?$" title="Use vírgula para separar os decimais.">
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="auxilio_transporte_alteracao">Valor do Auxílio Transporte</label>
                    <input type="text" class="form-control" id="auxilio_transporte_alteracao"
                        name="auxilio_transporte_alteracao" pattern="^\d+(\,\d{1,2})?$"
                        title="Use vírgula para separar os decimais.">
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="horario_alteracao">Horário</label>
                    <textarea class="form-control" id="horario_alteracao" name="horario_alteracao" rows="3"></textarea>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="desc_atividades_alteracao">Atividades</label>
                    <textarea class="form-control" id="desc_atividades_alteracao" name="desc_atividades_alteracao"
                        rows="3"></textarea>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="form-group">
                    <label for="descricao">Descrição das Alterações</label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="10" required>
        Cláusula Única:
            Fica estabelecido as seguintes alterações:

            a) [Descreva as alterações aqui]

            E, por estarem as partes certas e compromissadas, assinam o presente instrumento de maneira eletrônica na forma da lei 14063/2020 que dispõe sobre assinaturas eletrônicas.
                                            </textarea>
                </div>
            </div>

            <div class="col-md-12 mb-3">
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
        </div>
    </form>

@endsection