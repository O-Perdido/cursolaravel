<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .sidebar {
        width: 270px;
        position: fixed;
        /* margin: 16px; */
        z-index: 1030;
        border-top-right-radius: 16px;
        border-bottom-right-radius: 16px;
        background: #102e6c;
        top: 105px;
        height: calc(100vh - 132px);
        transition: 0.4s ease;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.7);
    }

    .sidebar.collapsed {
        width: 85px;
    }

    .sidebar-header {
        position: relative;
        display: flex;
        padding: 25px 20px;
        align-items: center;
        justify-content: space-between;

    }

    .sidebar-header .header-logo img {
        width: 46px;
        height: 46px;
        display: block;
        object-fit: contain;

    }

    .sidebar-header .toggler {
        position: absolute;
        right: 20px;
        height: 35px;
        width: 35px;
        border: none;
        color: #102e6c;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border-radius: 8px;
        background: #ffffff;
        transition: 0.4s ease;
    }

    .sidebar.collapsed .sidebar-header .toggler {
        transform: translate(-4px, 65px)
    }

    .sidebar-header .toggler:hover {
        background: #0248d3;
        color: #ffffff;
    }

    .sidebar-header .toggler span {
        font-size: 1.75rem;
        transition: 0.4s ease;
    }

    .sidebar.collapsed .sidebar-header .toggler span {
        transform: rotate(180deg);
    }

    .sidebar-nav .nav-list {
        list-style: none;
        display: flex;
        gap: 4px;
        padding: 0 15px;
        flex-direction: column;
        transform: translateY(15px);
        transition: 0.4s ease;

    }

    .sidebar-nav .primary-nav {
        overflow-y: auto;
        overflow-x: hidden;
        max-height: calc(100vh - 380px);
        padding-bottom: 20px;
        margin-bottom: 120px;
        padding-right: 5px;
    }

    .sidebar.collapsed .sidebar-nav .primary-nav {
        overflow: visible;
    }

    .sidebar-nav .primary-nav::-webkit-scrollbar {
        width: 6px;
    }

    .sidebar-nav .primary-nav::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }

    .sidebar-nav .primary-nav::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 10px;
    }

    .sidebar-nav .primary-nav::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    .sidebar.collapsed .sidebar-nav .primary-nav {
        transform: translateY(65px);
    }

    .sidebar-nav .nav-link {
        color: #ffffff;
        display: flex;
        gap: 12px;
        white-space: nowrap;
        border-radius: 8px;
        align-items: center;
        padding: 12px 15px;
        text-decoration: none;
        transition: 0.4s ease;
    }

    .sidebar-nav .nav-link:hover {
        color: #102e6c;
        background: #ffffff;
    }

    .sidebar-nav .nav-link .nav-label {
        transition: opacity 0.4s ease;
    }

    .sidebar.collapsed .sidebar-nav .nav-link .nav-label {
        opacity: 0;
        pointer-events: none;
    }

    .sidebar-nav .nav-item {
        position: relative;
    }

    .sidebar-nav .nav-tooltip {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        opacity: 0;
        display: none;
        pointer-events: none;
        left: calc(100% + 25px);
        color: #102e6c;
        padding: 6px 12px;
        border-radius: 8px;
        background: #ffffff;
        white-space: nowrap;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        transition: 0s;
        z-index: 10000;
    }

    .sidebar.collapsed .sidebar-nav .nav-tooltip {
        display: block;
    }

    .sidebar-nav .nav-item:hover .nav-tooltip {
        opacity: 1;
        pointer-events: auto;
        transition: 0.4s ease;
    }

    .sidebar-nav .secondary-nav {
        position: absolute;
        bottom: 20px;
        width: 100%;
        padding: 15px 15px 0;
        background: #102e6c;
        border-top: 1px solid rgba(255, 255, 255, 0.1);

    }

    .sidebar-nav .nav-submenu {
        list-style: none;
        padding-left: 40px;
        margin-top: 4px;
        display: flex;
        flex-direction: column;
        gap: 4px;
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        transition: max-height 0.4s ease, opacity 0.4s ease;
    }

    .sidebar:not(.collapsed) .nav-submenu {
        max-height: 500px;
        opacity: 1;
    }

    .sidebar.collapsed .nav-submenu {
        display: none;
    }

    .sidebar-nav .nav-sublink {
        font-size: 0.9rem;
        padding: 10px 15px;
    }

    .sidebar-nav button.nav-link {
        border: none;
        background: none;
        width: 100%;
        cursor: pointer;
        font-family: inherit;
    }

    .sidebar-nav button.nav-link #copyMessage {
        margin-left: 10px;
        font-size: 0.8rem;
        color: #4ade80;
    }

    #copyMessage {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #ffffff;
        color: #102e6c;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        z-index: 9999;
        font-weight: 600;
        font-size: 1rem;
        display: none;
        animation: popupFade 0.3s ease;
    }

    @keyframes popupFade {
        from {
            opacity: 0;
            transform: translate(-50%, -50%) scale(0.8);
        }

        to {
            opacity: 1;
            transform: translate(-50%, -50%) scale(1);
        }
    }
</style>

<aside class="sidebar collapsed">
    {{-- Sidebar Header --}}
    <header class="sidebar-header">
        <a href="/" class="header-logo">
            <img src="{{ asset('images/logo_sige_app.png') }}" alt="Logo SIGE">
        </a>
        <button class="toggler sidebar-toggler">
            <span class="material-symbols-outlined">chevron_left</span>
        </button>
    </header>

    <nav class="sidebar-nav">
        {{-- Primary top nav --}}
        <ul class="nav-list primary-nav">
            <li class="nav-item">
                <a href="/" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">dashboard</span>
                    <span class="nav-label">Dashboard</span>
                </a>
                <span class="nav-tooltip">Dashboard</span>
            </li>
            <li class="nav-item">
                <a href="{{ route('empresas.index') }}" class="nav-link nav-link-dropdown">
                    <span class="nav-icon material-symbols-outlined">work</span>
                    <span class="nav-label">Concedentes</span>
                </a>
                <span class="nav-tooltip">Concedentes</span>
                <ul class="nav-submenu">
                    <li class="nav-item">
                        <a href="{{ route('empresas.create') }}" class="nav-link nav-sublink">
                            <span class="nav-icon material-symbols-outlined">add</span>
                            <span class="nav-label">Cadastrar</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="{{ route('escolas.index') }}" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">book_2</span>
                    <span class="nav-label">Instituições</span>
                </a>
                <span class="nav-tooltip">Instituições</span>
                <ul class="nav-submenu">
                    <li class="nav-item">
                        <a href="#" class="nav-link nav-sublink">
                            <span class="nav-icon material-symbols-outlined">add</span>
                            <span class="nav-label">Cadastrar</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="{{ route('estagiarios.index') }}" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">school</span>
                    <span class="nav-label">Estagiários</span>
                </a>
                <span class="nav-tooltip">Estagiários</span>
                <ul class="nav-submenu">
                    <li class="nav-item">
                        <a href="{{ route('estagiarios.create') }}" class="nav-link nav-sublink">
                            <span class="nav-icon material-symbols-outlined">add</span>
                            <span class="nav-label">Cadastrar</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <button id="copyLinkButton" class="nav-link nav-sublink">
                            <span class="nav-icon material-symbols-outlined">content_copy</span>
                            <span class="nav-label">Copiar Link Cadastro</span>
                        </button>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="{{ route('termos.index') }}" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">text_snippet</span>
                    <span class="nav-label">Termos</span>
                </a>
                <span class="nav-tooltip">Termos</span>
                <ul class="nav-submenu">
                    <li class="nav-item">
                        <a href="{{ route('termos.create') }}" class="nav-link nav-sublink">
                            <span class="nav-icon material-symbols-outlined">add</span>
                            <span class="nav-label">Cadastrar</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="{{ route('supervisores.index') }}" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">supervisor_account</span>
                    <span class="nav-label">Supervisores</span>
                </a>
                <span class="nav-tooltip">Supervisores</span>
                <ul class="nav-submenu">
                    <li class="nav-item">
                        <a href="{{ route('supervisor.create') }}" class="nav-link nav-sublink">
                            <span class="nav-icon material-symbols-outlined">add</span>
                            <span class="nav-label">Cadastrar</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="{{ route('folhas.index') }}" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">business_center</span>
                    <span class="nav-label">Vagas</span>
                </a>
                <span class="nav-tooltip">Vagas</span>
            </li>
            @if (Auth::user()->nivel === 'admin')
                <li class="nav-item">
                    <a href="{{ route('folhas.index') }}" class="nav-link">
                        <span class="nav-icon material-symbols-outlined">request_quote</span>
                        <span class="nav-label">Folhas de Pagamento</span>
                    </a>
                    <span class="nav-tooltip">Folhas de Pagamento</span>
                </li>
            @endif
            <li class="nav-item">
                <a href="{{ route('usuarios.index') }}" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">groups</span>
                    <span class="nav-label">Usuários</span>
                </a>
                <span class="nav-tooltip">Usuários</span>
            </li>
            @if (Auth::user()->nivel === 'admin')
                <li class="nav-item">
                    <a href="{{ route('configuracoes.index') }}" class="nav-link">
                        <span class="nav-icon material-symbols-outlined">settings</span>
                        <span class="nav-label">Configurações</span>
                    </a>
                    <span class="nav-tooltip">Configurações</span>
                </li>
            @endif
        </ul>

        {{-- Secondary bottom nav --}}
        <ul class="nav-list secondary-nav">
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">account_circle</span>
                    <span class="nav-label">{{ Auth::user()->name }}</span>
                </a>
                <span class="nav-tooltip">{{ Auth::user()->name }}</span>
            </li>
            <li class="nav-item">
                <a href="{{ route('logout') }}" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">logout</span>
                    <span class="nav-label">Logout</span>
                </a>
                <span class="nav-tooltip">Logout</span>
            </li>
        </ul>
    </nav>

</aside>

{{-- Popup de confirmação --}}
<div id="copyMessage">✓ Link copiado com sucesso!</div>

<script>

    const sidebar = document.querySelector(".sidebar");
    const sidebarToggler = document.querySelector(".sidebar-toggler");

    // Toggle sidebar's collapsed state
    sidebarToggler.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
    });

    // Copy link to clipboard
    document.getElementById('copyLinkButton').addEventListener('click', function () {
        var link = "{{ route('novo-estagiario-ajax-create') }}";
        navigator.clipboard.writeText(link).then(function () {
            var message = document.getElementById('copyMessage');
            message.style.display = 'block';
            setTimeout(function () {
                message.style.display = 'none';
            }, 3000);
        });
    });

</script>