@extends('layouts.main')

@section('title', 'SIGE Concursos | Detalhes do Órgão/Empresa')

@section('content')
    <h1>Detalhes do Órgão Público / Empresa</h1>
    <button onclick="window.NavigationHistory?.goBack('{{ route('sigeconcursos.orgaos.index') }}')"
        class="btn btn-secondary mb-3" title="Voltar para a página anterior com filtros preservados">Voltar</button>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header text-black d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $orgao->nome_razao_social }}</h5>
            <span class="badge bg-primary">SIGE Concursos</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Informações Gerais</h6>
                    <p class="mb-1"><strong>Nome/Razão Social:</strong> {{ $orgao->nome_razao_social }}</p>
                    <p class="mb-1"><strong>CNPJ:</strong>
                        {{ $orgao->numero_cnpj ? preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $orgao->numero_cnpj) : '' }}
                    </p>
                    <p class="mb-1"><strong>Telefone:</strong>
                        {{ $orgao->numero_telefone ? preg_replace('/(\d{2})(\d{4,5})(\d{4})/', '($1) $2-$3', $orgao->numero_telefone) : 'Não informado' }}
                    </p>
                    <p class="mb-1"><strong>Celular:</strong>
                        {{ $orgao->numero_celular ? preg_replace('/(\d{2})(\d{4,5})(\d{4})/', '($1) $2-$3', $orgao->numero_celular) : 'Não informado' }}
                    </p>
                    <p class="mb-1"><strong>E-mail:</strong> {{ $orgao->email }}</p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Representante</h6>
                    <p class="mb-1"><strong>Nome:</strong> {{ $orgao->nome_representante }}</p>
                    <p class="mb-1"><strong>Cargo:</strong> {{ $orgao->cargo_representante }}</p>
                    <p class="mb-1"><strong>CPF:</strong>
                        {{ $orgao->cpf_representante ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $orgao->cpf_representante) : '' }}
                    </p>
                </div>
            </div>

            <hr class="my-3">

            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Endereço</h6>
                    <p class="mb-1"><strong>CEP:</strong>
                        {{ $orgao->numero_cep ? preg_replace('/(\d{5})(\d{3})/', '$1-$2', $orgao->numero_cep) : '' }}
                    </p>
                    <p class="mb-1"><strong>Endereço:</strong> {{ $orgao->endereco }}</p>
                    <p class="mb-1"><strong>Número:</strong> {{ $orgao->numero_endereco }}</p>
                    <p class="mb-1"><strong>Complemento:</strong> {{ $orgao->complemento_endereco ?: 'Não informado' }}</p>
                    <p class="mb-1"><strong>Bairro:</strong> {{ $orgao->bairro }}</p>
                    <p class="mb-1"><strong>Cidade:</strong> {{ $orgao->cidade->nm_cidade ?? 'Não informado' }}</p>
                    <p class="mb-1"><strong>Estado:</strong> {{ $orgao->cidade?->estado?->nm_estado ?? 'Não informado' }}
                    </p>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Dados Bancários</h6>
                    <div class="border rounded p-3 bg-light" style="white-space: pre-line; min-height: 150px;">
                        {{ $orgao->dados_bancarios ?: 'Nenhum dado bancário informado.' }}
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('sigeconcursos.orgaos.edit', $orgao->id_empresa) }}" class="btn btn-info">Editar</a>
            <form action="{{ route('sigeconcursos.orgaos.destroy', $orgao->id_empresa) }}" method="POST"
                style="display: inline;" onsubmit="return confirm('Confirma a exclusão deste cadastro?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Excluir</button>
            </form>
        </div>
    </div>
@endsection