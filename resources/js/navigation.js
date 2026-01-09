/**
 * Navigation History Manager
 * 
 * Preserva a URL anterior com filtros e parâmetros da query string
 * Permite voltar para a página anterior mantendo estado (filtros, página, etc)
 */

export const NavigationHistory = {
    STORAGE_KEY: 'lastNavigationUrl',
    FALLBACK_KEY: 'fallbackUrl',

    /**
     * Salva a URL atual no sessionStorage antes de navegar
     * Chamado antes de navegar para uma página de detalhes
     */
    saveCurrentUrl() {
        const currentUrl = window.location.href;
        sessionStorage.setItem(this.STORAGE_KEY, currentUrl);
    },

    /**
     * Obtém a URL anterior salva
     * Se não existir, tenta voltar no histórico do navegador
     */
    getPreviousUrl() {
        return sessionStorage.getItem(this.STORAGE_KEY);
    },

    /**
     * Define uma URL fallback (útil para quando vem de outras páginas)
     */
    setFallbackUrl(url) {
        sessionStorage.setItem(this.FALLBACK_KEY, url);
    },

    /**
     * Obtém URL fallback
     */
    getFallbackUrl() {
        return sessionStorage.getItem(this.FALLBACK_KEY);
    },

    /**
     * Volta para a URL anterior ou usa fallback
     */
    goBack(fallbackRoute = null) {
        const previousUrl = this.getPreviousUrl();

        if (previousUrl && previousUrl !== window.location.href) {
            window.location.href = previousUrl;
            return;
        }

        const fallbackUrl = this.getFallbackUrl();
        if (fallbackUrl) {
            window.location.href = fallbackUrl;
            return;
        }

        if (fallbackRoute) {
            window.location.href = fallbackRoute;
            return;
        }

        // Última opção: voltar no histórico do navegador
        window.history.back();
    },

    /**
     * Limpa o histórico salvo
     */
    clear() {
        sessionStorage.removeItem(this.STORAGE_KEY);
        sessionStorage.removeItem(this.FALLBACK_KEY);
    }
};

// Automaticamente salva a URL atual em cada mudança de página dentro da mesma aba
// Isso funciona para links normais de navegação
document.addEventListener('DOMContentLoaded', () => {
    // Salvar URL atual ao carregar a página de listagem
    // (qualquer página que não seja show/edit)
    if (!isDetailPage()) {
        NavigationHistory.saveCurrentUrl();
    }
});

/**
 * Verifica se a página atual é uma página de detalhes (show) ou edição
 * Pode ser customizado conforme suas rotas
 */
function isDetailPage() {
    const pathname = window.location.pathname;
    // Detecta rotas como /termos/123, /empresas/456, /folhas-pagamento/789, etc
    return /\/[a-z-]+\/(\d+)($|\/|[?#])/.test(pathname);
}

// Expor para uso global em templates
window.NavigationHistory = NavigationHistory;
