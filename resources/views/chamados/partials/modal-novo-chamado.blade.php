<!-- Modal de Seleção de Tipo de Chamado -->
<div class="modal fade" id="modalNovoChamado" tabindex="-1" aria-labelledby="modalNovoChamadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalNovoChamadoLabel">
                    <i class="fas fa-headset me-2"></i>Abrir Novo Chamado
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formSelecionarTipo">
                    <div class="mb-3">
                        <label for="tipo_chamado_select" class="form-label">Selecione o tipo de chamado</label>
                        <select class="form-select" id="tipo_chamado_select" required>
                            <option value="">Selecione...</option>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-arrow-right me-2"></i>Continuar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modalNovoChamado');
        const select = document.getElementById('tipo_chamado_select');
        const form = document.getElementById('formSelecionarTipo');

        // Carrega tipos de chamados quando o modal é aberto
        modal.addEventListener('show.bs.modal', function () {
            fetch('{{ route("api.tipos-chamados.ativos") }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro HTTP: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    select.innerHTML = '<option value="">Selecione...</option>';
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(tipo => {
                            const option = document.createElement('option');
                            option.value = tipo.id_tipo_chamado;
                            option.textContent = tipo.nome;
                            if (tipo.descricao) {
                                option.title = tipo.descricao;
                            }
                            select.appendChild(option);
                        });
                    } else {
                        console.warn('Nenhum tipo de chamado encontrado');
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar tipos de chamado:', error);
                    alert('Erro ao carregar tipos de chamado. Tente novamente.\nDetalhes: ' + error.message);
                });
        });

        // Ao submeter, redireciona para o formulário específico do tipo
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const tipoId = select.value;
            if (tipoId) {
                window.location.href = '{{ route("chamados.create") }}?tipo=' + tipoId;
            }
        });
    });
</script>