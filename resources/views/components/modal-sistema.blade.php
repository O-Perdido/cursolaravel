<!-- Modal Universal do Sistema de Avaliações -->
<div id="modalSistema" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <div class="modal-header-icone" id="modalIcone"></div>
            <button class="modal-close-btn" onclick="fecharModal()">&times;</button>
        </div>
        <div class="modal-content">
            <h2 id="modalTitulo" class="modal-titulo"></h2>
            <p id="modalMensagem" class="modal-mensagem"></p>
            <div id="modalDetalhes" class="modal-detalhes" style="display: none;"></div>
        </div>
        <div class="modal-footer" id="modalFooter"></div>
    </div>
</div>

<style>
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.3s ease-in;
    }

    .modal-overlay.ativo {
        display: flex;
    }

    .modal-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        max-width: 500px;
        width: 90%;
        overflow: hidden;
        animation: slideUp 0.4s ease-out;
    }

    .modal-header {
        padding: 2rem;
        text-align: center;
        position: relative;
    }

    .modal-header-icone {
        width: 80px;
        height: 80px;
        margin: 0 auto 1rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        animation: popIn 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    .modal-header-icone.sucesso {
        background: linear-gradient(135deg, #28a745 0%, #19b755 100%);
    }

    .modal-header-icone.erro {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    }

    .modal-header-icone.aviso {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    }

    .modal-header-icone.confirmacao {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .modal-close-btn {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: none;
        border: none;
        font-size: 2rem;
        color: #6c757d;
        cursor: pointer;
        transition: color 0.2s;
    }

    .modal-close-btn:hover {
        color: #000;
    }

    .modal-content {
        padding: 1.5rem 2rem;
        text-align: center;
    }

    .modal-titulo {
        font-size: 1.5rem;
        font-weight: 700;
        color: #102e6c;
        margin-bottom: 0.75rem;
    }

    .modal-mensagem {
        font-size: 1rem;
        color: #0a1f4d;
        line-height: 1.6;
        margin-bottom: 1rem;
    }

    .modal-detalhes {
        background: #f7f9fc;
        border-left: 4px solid #667eea;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1rem;
        font-size: 0.9rem;
        color: #0a1f4d;
        text-align: left;
    }

    .modal-footer {
        padding: 1.5rem 2rem;
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .modal-btn {
        padding: 0.75rem 2rem;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        font-size: 0.95rem;
    }

    .modal-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .modal-btn-primary {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }

    .modal-btn-secondary {
        background: #6c757d;
        color: white;
    }

    .modal-btn-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
    }

    .modal-btn-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes popIn {
        0% {
            transform: scale(0);
        }

        50% {
            transform: scale(1.1);
        }

        100% {
            transform: scale(1);
        }
    }
</style>

<script>
    // Variáveis globais
    let callbackConfirmacao = null;

    // Mostrar modal de sucesso
    function mostrarSucesso(titulo, mensagem, detalhes = null) {
        const modal = document.getElementById('modalSistema');
        const icone = document.getElementById('modalIcone');
        const detalhesDiv = document.getElementById('modalDetalhes');
        const footer = document.getElementById('modalFooter');

        document.getElementById('modalTitulo').textContent = titulo;
        document.getElementById('modalMensagem').textContent = mensagem;

        icone.className = 'modal-header-icone sucesso';
        icone.innerHTML = '<i class="fas fa-check"></i>';

        if (detalhes) {
            detalhesDiv.style.display = 'block';
            detalhesDiv.innerHTML = detalhes;
        } else {
            detalhesDiv.style.display = 'none';
        }

        footer.innerHTML = `
            <button class="modal-btn modal-btn-primary" onclick="fecharModal()">
                <i class="fas fa-check"></i> Fechar
            </button>
        `;

        modal.classList.add('ativo');
    }

    // Mostrar modal de erro
    function mostrarErro(titulo, mensagem, detalhes = null) {
        const modal = document.getElementById('modalSistema');
        const icone = document.getElementById('modalIcone');
        const detalhesDiv = document.getElementById('modalDetalhes');
        const footer = document.getElementById('modalFooter');

        document.getElementById('modalTitulo').textContent = titulo;
        document.getElementById('modalMensagem').textContent = mensagem;

        icone.className = 'modal-header-icone erro';
        icone.innerHTML = '<i class="fas fa-exclamation-circle"></i>';

        if (detalhes) {
            detalhesDiv.style.display = 'block';
            detalhesDiv.innerHTML = detalhes;
        } else {
            detalhesDiv.style.display = 'none';
        }

        footer.innerHTML = `
            <button class="modal-btn modal-btn-danger" onclick="fecharModal()">
                <i class="fas fa-times"></i> Fechar
            </button>
        `;

        modal.classList.add('ativo');
    }

    // Mostrar modal de aviso
    function mostrarAviso(titulo, mensagem, detalhes = null) {
        const modal = document.getElementById('modalSistema');
        const icone = document.getElementById('modalIcone');
        const detalhesDiv = document.getElementById('modalDetalhes');
        const footer = document.getElementById('modalFooter');

        document.getElementById('modalTitulo').textContent = titulo;
        document.getElementById('modalMensagem').textContent = mensagem;

        icone.className = 'modal-header-icone aviso';
        icone.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';

        if (detalhes) {
            detalhesDiv.style.display = 'block';
            detalhesDiv.innerHTML = detalhes;
        } else {
            detalhesDiv.style.display = 'none';
        }

        footer.innerHTML = `
            <button class="modal-btn modal-btn-info" onclick="fecharModal()">
                <i class="fas fa-check"></i> OK
            </button>
        `;

        modal.classList.add('ativo');
    }

    // Mostrar modal de confirmação
    function mostrarConfirmacao(titulo, mensagem, callback) {
        const modal = document.getElementById('modalSistema');
        const icone = document.getElementById('modalIcone');
        const detalhesDiv = document.getElementById('modalDetalhes');
        const footer = document.getElementById('modalFooter');

        document.getElementById('modalTitulo').textContent = titulo;
        document.getElementById('modalMensagem').textContent = mensagem;

        icone.className = 'modal-header-icone confirmacao';
        icone.innerHTML = '<i class="fas fa-question-circle"></i>';

        detalhesDiv.style.display = 'none';

        callbackConfirmacao = callback;

        footer.innerHTML = `
            <button class="modal-btn modal-btn-danger" onclick="confirmarAcao()">
                <i class="fas fa-check"></i> Confirmar
            </button>
            <button class="modal-btn modal-btn-secondary" onclick="fecharModal()">
                <i class="fas fa-times"></i> Cancelar
            </button>
        `;

        modal.classList.add('ativo');
    }

    // Confirmar ação
    function confirmarAcao() {
        if (callbackConfirmacao) {
            callbackConfirmacao();
        }
        fecharModal();
    }

    // Fechar modal
    function fecharModal() {
        const modal = document.getElementById('modalSistema');
        modal.classList.remove('ativo');
        callbackConfirmacao = null;
    }

    // Fechar ao clicar fora
    document.addEventListener('click', function (event) {
        const modal = document.getElementById('modalSistema');
        if (event.target === modal) {
            fecharModal();
        }
    });
</script>