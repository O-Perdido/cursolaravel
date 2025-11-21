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
        top: -10px;
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
    }

    .sidebar.collapsed .sidebar-nav .nav-tooltip {
        display: block;
    }

    .sidebar-nav .nav-item:hover .nav-tooltip {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(50%);
        transition: 0.4s ease;
    }

    .sidebar-nav .secondary-nav {
        position: absolute;
        bottom: 30px;
        width: 100%;

    }
</style>

<aside class="sidebar collapsed">
    {{-- Sidebar Header --}}
    <header class="sidebar-header">
        <a href="#" class="header-logo">
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
                <a href="#" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">dashboard</span>
                    <span class="nav-label">Dashboard</span>
                </a>
                <span class="nav-tooltip">Dashboard</span>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">calendar_today</span>
                    <span class="nav-label">Calendar</span>
                </a>
                <span class="nav-tooltip">Calendar</span>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">notifications</span>
                    <span class="nav-label">Notifications</span>
                </a>
                <span class="nav-tooltip">Notifications</span>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">group</span>
                    <span class="nav-label">Team</span>
                </a>
                <span class="nav-tooltip">Team</span>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">analytics</span>
                    <span class="nav-label">Analytics</span>
                </a>
                <span class="nav-tooltip">Analytics</span>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">star</span>
                    <span class="nav-label">Bookmarks</span>
                </a>
                <span class="nav-tooltip">Bookmarks</span>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">settings</span>
                    <span class="nav-label">Settings</span>
                </a>
                <span class="nav-tooltip">Settings</span>
            </li>
        </ul>

        {{-- Secondary bottom nav --}}
        <ul class="nav-list secondary-nav">
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">account_circle</span>
                    <span class="nav-label">Profile</span>
                </a>
                <span class="nav-tooltip">Profile</span>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <span class="nav-icon material-symbols-outlined">logout</span>
                    <span class="nav-label">Logout</span>
                </a>
                <span class="nav-tooltip">Logout</span>
            </li>
        </ul>
    </nav>

</aside>

<script>

    const sidebar = document.querySelector(".sidebar");
    const sidebarToggler = document.querySelector(".sidebar-toggler");

    // Toggle sidebar's collapsed state
    sidebarToggler.addEventListener("click", () => {
        sidebar.classList.toggle("collapsed");
    });


</script>