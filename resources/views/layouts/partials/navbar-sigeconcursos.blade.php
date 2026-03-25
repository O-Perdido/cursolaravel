<li class="nav-item" style="vertical-align: middle;">
    <a class="nav-link {{ request()->routeIs('sigeconcursos.dashboard') ? 'active' : '' }}"
        href="{{ route('sigeconcursos.dashboard') }}">
        <i class="fa-solid fa-gauge-high fa-2x"></i><br>
        <small>Dashboard</small>
    </a>
</li>

<li class="nav-item" style="vertical-align: middle;">
    <a class="nav-link {{ request()->routeIs('sigeconcursos.processos.*') ? 'active' : '' }}"
        href="{{ route('sigeconcursos.processos.index') }}">
        <i class="fa-solid fa-folder-tree fa-2x"></i><br>
        <small>Processos</small>
    </a>
</li>

<li class="nav-item" style="vertical-align: middle;">
    <a class="nav-link {{ request()->routeIs('sigeconcursos.cargos.*') ? 'active' : '' }}"
        href="{{ route('sigeconcursos.cargos.index') }}">
        <i class="fa-solid fa-briefcase fa-2x"></i><br>
        <small>Cargos</small>
    </a>
</li>

<li class="nav-item" style="vertical-align: middle;">
    <a class="nav-link {{ request()->routeIs('sigeconcursos.locais-prova.*') ? 'active' : '' }}"
        href="{{ route('sigeconcursos.locais-prova.index') }}">
        <i class="fa-solid fa-school fa-2x"></i><br>
        <small>Locais</small>
    </a>
</li>

<li class="nav-item" style="vertical-align: middle;">
    <a class="nav-link {{ request()->routeIs('sigeconcursos.orgaos.*') ? 'active' : '' }}"
        href="{{ route('sigeconcursos.orgaos.index') }}">
        <i class="fa-solid fa-building-columns fa-2x"></i><br>
        <small>Órgãos/Empresas</small>
    </a>
</li>

<li class="nav-item" style="vertical-align: middle;">
    <a class="nav-link {{ request()->routeIs('sigeconcursos.candidatos.*') ? 'active' : '' }}"
        href="{{ route('sigeconcursos.candidatos.index') }}">
        <i class="fa-solid fa-users-viewfinder fa-2x"></i><br>
        <small>Candidatos</small>
    </a>
</li>

<li class="nav-item d-none d-lg-block" style="vertical-align: middle; height: 100%;">
    <div class="vr mx-3 bg-white" style="height: 100%; width: 2px"></div>
</li>