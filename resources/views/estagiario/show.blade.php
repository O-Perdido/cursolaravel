@extends('layouts.main')

@section('title', 'Detalhes do Estagiário')

@section('content')
    <h1>Detalhes do Estagiário</h1>
    <button onclick="window.NavigationHistory?.goBack('{{ route('estagiarios.index') }}')" class="btn btn-secondary mb-3"
        title="Voltar para a página anterior com filtros preservados">Voltar</button>
    <div class="card shadow-sm">
        <div class="card-header text-black">
            <h5 class="mb-0">{{ $estagiario->nome_estagiario }}</h5>
            @if(!empty($estagiario->nome_secundario))
                <small class="text-muted">Nome civil: {{ $estagiario->nome_secundario }}</small>
            @endif
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Coluna 1: Dados Pessoais e Endereço -->
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Dados Pessoais</h6>
                    <p class="mb-1"><strong>Nome:</strong> {{ $estagiario->nome_estagiario }}</p>
                    @if(!empty($estagiario->nome_secundario))
                        <p class="mb-1 text-muted"><strong>Nome civil:</strong> {{ $estagiario->nome_secundario }}</p>
                    @endif
                    <p class="mb-1"><strong>CPF:</strong>
                        {{ $estagiario->numero_cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $estagiario->numero_cpf) : '' }}
                    </p>
                    <p class="mb-1"><strong>Data de Nascimento:</strong> {{ $estagiario->data_nascimento }}</p>
                    <p class="mb-1"><strong>Telefone:</strong>
                        {{ $estagiario->numero_telefone ? preg_replace('/(\d{2})(\d{4,5})(\d{4})/', '($1) $2-$3', $estagiario->numero_telefone) : '' }}
                    </p>
                    <p class="mb-1"><strong>Celular:</strong>
                        {{ $estagiario->numero_celular ? preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $estagiario->numero_celular) : '' }}
                        @if($estagiario->numero_celular)
                            <a href="https://wa.me/55{{ preg_replace('/[^0-9]/', '', $estagiario->numero_celular) }}"
                                target="_blank"
                                class="btn btn-success btn-sm ms-2"
                                title="Abrir WhatsApp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        @endif
                    </p>
                    <p class="mb-1"><strong>Email:</strong> {{ $estagiario->email }}
                        @if($estagiario->email)
                            <button type="button" class="btn btn-outline-primary btn-sm ms-2"
                                    data-email="{{ $estagiario->email }}"
                                    onclick="copyEmailToClipboard(this.dataset.email, this)"
                                    title="Copiar email">
                                <i class="fas fa-copy"></i>
                            </button>
                        @endif
                    </p>
                    <hr class="my-2">
                    <h6 class="text-muted mb-3">Endereço</h6>
                    <p class="mb-1"><strong>CEP:</strong>
                        {{ $estagiario->numero_cep ? preg_replace('/(\d{5})(\d{3})/', '$1-$2', $estagiario->numero_cep) : '' }}
                    </p>
                    <p class="mb-1"><strong>Endereço:</strong> {{ $estagiario->endereco }},
                        {{ $estagiario->numero_endereco }}
                    </p>
                    <p class="mb-1"><strong>Complemento:</strong> {{ $estagiario->complemento_endereco }}</p>
                    <p class="mb-1"><strong>Bairro:</strong> {{ $estagiario->bairro }}</p>
                    <p class="mb-1"><strong>Cidade:</strong> {{ $estagiario->cidade->nm_cidade }}</p>
                    <p class="mb-1"><strong>Estado:</strong> {{ $estagiario->cidade->estado->nm_estado }}</p>
                </div>
                <!-- Coluna 2: Dados Acadêmicos e Documentos -->
                <div class="col-md-6">
                    <h6 class="text-muted mb-3">Dados Acadêmicos</h6>
                    <p class="mb-1"><strong>Instituição de Ensino:</strong> {{ $estagiario->instituicao_ensino }}</p>
                    <p class="mb-1"><strong>Curso:</strong> {{ $estagiario->curso }}</p>
                    <p class="mb-1"><strong>Nível do Curso:</strong> {{ $estagiario->nivel_curso }}</p>
                    <p class="mb-1"><strong>Área de Estágio:</strong> {{ $estagiario->area_de_estagio }}</p>
                    <p class="mb-1"><strong>Nome da Mãe:</strong> {{ $estagiario->nome_mae }}</p>
                    <p class="mb-1"><strong>PIS:</strong> {{ $estagiario->numero_pis }}</p>
                    <p class="mb-1"><strong>Tipo de Chave PIX:</strong> {{ $estagiario->tipo_chave_pix }}</p>
                    <p class="mb-1"><strong>Chave PIX:</strong> {{ $estagiario->chave_pix }}</p>
                    <hr class="my-2">
                    <h6 class="text-muted mb-3">Documentos</h6>
                    <a href="{{ route('estagiarios.download', ['id' => $estagiario->id_estagiario, 'campo' => 'foto_documento']) }}"
                        class="btn btn-outline-primary btn-sm mb-2" target="_blank">
                        Baixar Documento de Identidade
                    </a><br>
                    <a href="{{ route('estagiarios.download', ['id' => $estagiario->id_estagiario, 'campo' => 'comprovante_residencia']) }}"
                        class="btn btn-outline-primary btn-sm mb-2" target="_blank">
                        Baixar Comprovante de Residência
                    </a><br>
                    <a href="{{ route('estagiarios.download', ['id' => $estagiario->id_estagiario, 'campo' => 'comprovante_escolar']) }}"
                        class="btn btn-outline-primary btn-sm mb-2" target="_blank">
                        Baixar Comprovante Escolar
                    </a>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('estagiarios.edit', $estagiario->id_estagiario) }}" class="btn btn-info">Editar</a>
            <form action="{{ route('estagiario.destroy', $estagiario->id_estagiario) }}" method="POST"
                style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Excluir</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
function copyEmailToClipboard(email, button) {
    if (!email) return;
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(email).then(function() {
            // Feedback visual
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i> Copiado!';
            button.classList.add('btn-success');
            button.classList.remove('btn-outline-primary');
            
            setTimeout(function() {
                button.innerHTML = originalHTML;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-primary');
            }, 2000);
        }).catch(function(err) {
            console.error('Erro ao copiar:', err);
            fallbackCopyTextToClipboard(email);
        });
    } else {
        fallbackCopyTextToClipboard(email);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.width = "2em";
    textArea.style.height = "2em";
    textArea.style.padding = "0";
    textArea.style.border = "none";
    textArea.style.outline = "none";
    textArea.style.boxShadow = "none";
    textArea.style.background = "transparent";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            alert('E-mail copiado: ' + text);
        }
    } catch (err) {
        console.error('Erro ao copiar texto: ', err);
    }
    
    document.body.removeChild(textArea);
}
</script>
@endsection