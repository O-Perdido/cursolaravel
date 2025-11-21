@extends('layouts.main')

@section('title', 'Detalhes da Instituição de Ensino')

@section('content')
    <h1>Detalhes da Instituição de Ensino</h1>
    <a href="{{ route('escolas.index') }}" class="btn btn-secondary mb-3">Voltar</a>
    <div class="card shadow-sm">
        <div class="card-header text-black">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $escola->nome_escola }}</h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#representantesModal">
                    <i class="fas fa-users me-1"></i> Gerenciar Representantes
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Coluna 1 -->
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Informações Gerais</h6>                    
                    <p class="mb-1"><strong>Nome:</strong> {{ $escola->nome_escola }}</p>
                    <p class="mb-1"><strong>CNPJ:</strong> {{ $escola->numero_cnpj ? preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $escola->numero_cnpj) : '' }}</p>
                    <p class="mb-1"><strong>Telefone:</strong> {{ $escola->numero_telefone ? preg_replace('/(\d{2})(\d{4,5})(\d{4})/', '($1) $2-$3', $escola->numero_telefone) : '' }}</p>
                    <p class="mb-1"><strong>Celular:</strong> {{ $escola->numero_celular ? preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $escola->numero_celular) : '' }}</p>
                    <p class="mb-1"><strong>Email:</strong> {{ $escola->email }}</p>
                    <hr class="my-2">
                    <h6 class="text-muted mb-3">Endereço</h6>
                    <p class="mb-1"><strong>CEP:</strong> {{ $escola->numero_cep ? preg_replace('/(\d{5})(\d{3})/', '$1-$2', $escola->numero_cep) : '' }}</p>
                    <p class="mb-1"><strong>Endereço:</strong> {{ $escola->endereco }}</p>
                    <p class="mb-1"><strong>Número:</strong> {{ $escola->numero_endereco }}</p>
                    <p class="mb-1"><strong>Complemento:</strong> {{ $escola->complemento_endereco }}</p>
                    <p class="mb-1"><strong>Bairro:</strong> {{ $escola->bairro }}</p>
                    <p class="mb-1"><strong>Cidade:</strong> {{ $escola->cidade->nm_cidade }}</p>
                    <p class="mb-1"><strong>Estado:</strong> {{ $escola->cidade->estado->nm_estado }}</p>
                </div>
                <!-- Coluna 2 -->
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Representante Principal (Legado)</h6>
                    <p class="mb-1"><strong>Nome:</strong> {{ $escola->nome_representante }}</p>
                    <p class="mb-1"><strong>Cargo:</strong> {{ $escola->cargo_representante }}</p>
                    <p class="mb-1"><strong>CPF:</strong> {{ $escola->cpf_representante ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $escola->cpf_representante) : '' }}</p>
                    <p class="text-muted small">
                        <i class="fas fa-info-circle"></i> Use "Gerenciar Representantes" para cadastrar múltiplos representantes com emails para assinatura digital.
                    </p>
                    <hr class="my-2">
                    <h6 class="text-muted mb-3">Seguro</h6>
                    <p class="mb-1"><strong>Número da Apólice:</strong> {{ $escola->numero_apolice }}</p>
                    <p class="mb-1"><strong>Nome da Seguradora:</strong> {{ $escola->nome_seguradora }}</p>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('escolas.edit', $escola->id_escola) }}" class="btn btn-info">Editar</a>
            <form action="{{ route('escolas.destroy', $escola->id_escola) }}" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Excluir</button>
            </form>
        </div>
    </div>

    <!-- Modal: Gerenciar Representantes -->
    <div class="modal fade" id="representantesModal" tabindex="-1" aria-labelledby="representantesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="representantesModalLabel">
                        <i class="fas fa-users me-2"></i>Representantes - {{ $escola->nome_escola }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info small">
                        <i class="fas fa-info-circle me-1"></i>
                        Cadastre os representantes que poderão assinar digitalmente os documentos via ZapSign.
                    </div>

                    <!-- Botão Adicionar -->
                    <div class="mb-3">
                        <button type="button" class="btn btn-success btn-sm" onclick="mostrarFormRepresentante()">
                            <i class="fas fa-plus me-1"></i> Adicionar Representante
                        </button>
                    </div>

                    <!-- Formulário de Cadastro (oculto inicialmente) -->
                    <div id="formRepresentante" style="display: none;" class="card mb-3">
                        <div class="card-body">
                            <form action="{{ route('representantes.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="representavel_type" value="App\Models\Escola">
                                <input type="hidden" name="representavel_id" value="{{ $escola->id_escola }}">

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Nome <span class="text-danger">*</span></label>
                                        <input type="text" name="nome" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Cargo <span class="text-danger">*</span></label>
                                        <input type="text" name="cargo" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">E-mail <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">CPF (opcional)</label>
                                        <input type="text" name="cpf" class="form-control form-control-sm" maxlength="14" placeholder="000.000.000-00">
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-3">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-save me-1"></i> Salvar
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="ocultarFormRepresentante()">
                                        Cancelar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Lista de Representantes -->
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nome</th>
                                    <th>Cargo</th>
                                    <th>E-mail</th>
                                    <th style="width: 100px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($escola->representantes as $rep)
                                    <tr id="row-rep-{{ $rep->id_representante }}">
                                        <td class="rep-nome">{{ $rep->nome }}</td>
                                        <td class="rep-cargo">{{ $rep->cargo }}</td>
                                        <td class="rep-email">{{ $rep->email }}</td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-info btn-sm me-1" 
                                                    onclick="editarRepresentante({{ $rep->id_representante }}, '{{ $rep->nome }}', '{{ $rep->cargo }}', '{{ $rep->email }}', '{{ $rep->cpf ?? '' }}')" 
                                                    title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('representantes.destroy', $rep->id_representante) }}" method="POST" style="display: inline;" onsubmit="return confirm('Confirma exclusão?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            Nenhum representante cadastrado ainda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let modoEdicao = false;
        let idEdicao = null;

        function mostrarFormRepresentante() {
            modoEdicao = false;
            idEdicao = null;
            document.getElementById('formRepresentante').style.display = 'block';
            document.querySelector('#formRepresentante form').reset();
            document.querySelector('#formRepresentante form').action = '{{ route("representantes.store") }}';
            document.querySelector('#formRepresentante form input[name="_method"]')?.remove();
        }

        function ocultarFormRepresentante() {
            document.getElementById('formRepresentante').style.display = 'none';
        }

        function editarRepresentante(id, nome, cargo, email, cpf) {
            modoEdicao = true;
            idEdicao = id;
            
            const form = document.querySelector('#formRepresentante form');
            form.action = `/representantes/${id}`;
            
            // Adicionar método PUT se não existir
            if (!form.querySelector('input[name="_method"]')) {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);
            }
            
            // Preencher campos
            form.querySelector('input[name="nome"]').value = nome;
            form.querySelector('input[name="cargo"]').value = cargo;
            form.querySelector('input[name="email"]').value = email;
            form.querySelector('input[name="cpf"]').value = cpf || '';
            
            document.getElementById('formRepresentante').style.display = 'block';
            form.querySelector('input[name="nome"]').focus();
        }
    </script>
@endsection