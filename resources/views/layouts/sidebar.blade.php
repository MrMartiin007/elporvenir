<div id="sidebar" class="d-flex flex-column flex-shrink-0 p-3 transition-all shadow-lg"
    style="width: 180px; height: 100vh; overflow-y: auto; overflow-x: hidden; flex-shrink: 0; transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1); background-color: #fae1e5; color: #5c3d42; z-index: 1000;">

    {{-- Header: Logo + Toggle Button --}}
    <div class="sidebar-header d-flex align-items-center justify-content-between mb-3" style="min-height: 50px; flex-shrink: 0;">
        <!-- Logo Wrapper -->
        <a href="{{ route('dashboard') }}" class="d-flex align-items-center text-decoration-none logo-container"
            style="color: #5c3d42;">
            <div class="d-flex justify-content-center align-items-center" style="min-width: 40px; width: 40px;">
                <img src="{{ asset('logo.jpg') }}" alt="Logo" class="rounded-circle shadow-sm flex-shrink-0"
                    style="width: 35px; height: 35px; object-fit: cover; border: 2px solid #fff;">
            </div>
            <span class="fs-9 fw-bold sidebar-text ms-1 text-uppercase tracking-wider">El Porvenir</span>
        </a>

        <!-- Toggle Button -->
        <button id="sidebarToggle" class="btn btn-sm text-pink-dark p-1 border-0" style="color: #880e4f;"
            title="Alternar Menú">
            <i class="fas fa-bars fa-lg"></i>
        </button>
    </div>

    <hr class="border-white opacity-50 my-2 flex-shrink-0">

    {{-- Lista de navegación — esta sección es la que hace scroll --}}
    <ul class="nav nav-pills flex-column mb-auto" id="sidebar-nav">
        @role('superadmin')
        <li>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                title="Dashboard" style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-tachometer-alt fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ route('ventas.index') }}" class="nav-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}"
                title="Venta" style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-cash-register fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Venta</span>
            </a>
        </li>
        <li>
            <a href="{{ route('productos.consultar') }}"
                class="nav-link {{ request()->routeIs('productos.consultar') ? 'active' : '' }}" title="Consultar"
                style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-search fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Consultar</span>
            </a>
        </li>
        <li>
            <a href="{{ route('pedidos.index') }}"
                class="nav-link {{ request()->routeIs('pedidos.*') ? 'active' : '' }}" title="Pedidos Online"
                style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-shopping-basket fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Pedidos Online</span>
            </a>
        </li>
        <li>
            <a href="{{ route('calendario.index') }}"
                class="nav-link {{ request()->routeIs('calendario.*') ? 'active' : '' }}" title="Calendario"
                style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-calendar-alt fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Calendario</span>
            </a>
        </li>
        <li>
            <a href="{{ route('entradas.index') }}"
                class="nav-link {{ request()->routeIs('entradas.*') ? 'active' : '' }}" title="Entradas"
                style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-truck-loading fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Entradas</span>
            </a>
        </li>
        <li>
            <a href="{{ route('auditoria.index') }}"
                class="nav-link {{ request()->routeIs('auditoria.*') ? 'active' : '' }}" title="Auditoría"
                style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-clipboard-check fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Auditoría</span>
            </a>
        </li>
        <li>
            <a href="{{ route('productos.index') }}"
                class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}" title="Productos"
                style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-box fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Productos</span>
            </a>
        </li>
        <li>
            <a href="{{ route('marcas.index') }}" class="nav-link {{ request()->routeIs('marcas.*') ? 'active' : '' }}"
                title="Marcas" style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-tags fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Marcas</span>
            </a>
        </li>
        <li>
            <a href="{{ route('ubicaciones.index') }}"
                class="nav-link {{ request()->routeIs('ubicaciones.*') ? 'active' : '' }}" title="Ubicaciones"
                style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-map-marker-alt fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Ubicaciones</span>
            </a>
        </li>
        <li>
            <a href="{{ route('empresas.index') }}"
                class="nav-link {{ request()->routeIs('empresas.*') ? 'active' : '' }}" title="Empresas"
                style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-building fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Empresas</span>
            </a>
        </li>
        <li>
            <a href="{{ route('facturas.index') }}"
                class="nav-link {{ request()->routeIs('facturas.*') ? 'active' : '' }}" title="Facturas"
                style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-file-invoice-dollar fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Facturas</span>
            </a>
        </li>
        <li>
            <a href="{{ route('pagos.index') }}" class="nav-link {{ request()->routeIs('pagos.*') ? 'active' : '' }}"
                title="pagos" style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fa-solid fa-money-check-dollar"></i>
                </div>
                <span class="ms-2 sidebar-text">Pagos</span>
            </a>
        </li>
        <li>
            <a href="{{ route('reportes.index') }}"
                class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" title="Reporte"
                style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <span class="ms-2 sidebar-text">Reporte Venta</span>
            </a>
        </li>
        <li>
            <a href="{{ route('inventario.index') }}"
                class="nav-link {{ request()->routeIs('inventario.*') ? 'active' : '' }}" title="Inventario"
                style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-warehouse fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Inventario</span>
            </a>
        </li>

        @endrole

        @role('venta')
        <li>
            <a href="{{ route('ventas.index') }}" class="nav-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}"
                title="Venta" style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-cash-register fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Venta</span>
            </a>
        </li>
        <li>
            <a href="{{ route('productos.consultar') }}"
                class="nav-link {{ request()->routeIs('productos.consultar') ? 'active' : '' }}" title="Consultar"
                style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-search fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Consultar</span>
            </a>
        </li>
        <li>
            <a href="{{ route('productos.index') }}"
                class="nav-link {{ request()->routeIs('productos.*') ? 'active' : '' }}" title="Productos"
                style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-box fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Productos</span>
            </a>
        </li>
        <li>
            <a href="{{ route('marcas.index') }}" class="nav-link {{ request()->routeIs('marcas.*') ? 'active' : '' }}"
                title="Marcas" style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-tags fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Marcas</span>
            </a>
        </li>
        <li>
            <a href="{{ route('ubicaciones.index') }}"
                class="nav-link {{ request()->routeIs('ubicaciones.*') ? 'active' : '' }}" title="Ubicaciones"
                style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-map-marker-alt fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Ubicaciones</span>
            </a>
        </li>
        <li>
            <a href="{{ route('entradas.index') }}"
                class="nav-link {{ request()->routeIs('entradas.*') ? 'active' : '' }}" title="Entradas"
                style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-truck-loading fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Entradas</span>
            </a>
        </li>
        <li>
            <a href="{{ route('auditoria.index') }}"
                class="nav-link {{ request()->routeIs('auditoria.*') ? 'active' : '' }}" title="Auditoría"
                style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-clipboard-check fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Auditoría</span>
            </a>
        </li>
        <li>
            <a href="{{ route('pedidos.index') }}"
                class="nav-link {{ request()->routeIs('pedidos.*') ? 'active' : '' }}" title="Pedidos Online"
                style="color: #5c3d42;">
                <div class="icon-wrapper d-flex justify-content-center" style="min-width: 30px;">
                    <i class="fas fa-shopping-cart fa-lg"></i>
                </div>
                <span class="ms-2 sidebar-text">Pedidos Online</span>
            </a>
        </li>
        @endrole
    </ul>

    <hr class="border-white opacity-50 my-2 flex-shrink-0">

    <div class="dropdown flex-shrink-0">
        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser1"
            data-bs-toggle="dropdown" aria-expanded="false" style="color: #5c3d42;">
            <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=fff&color=5c3d42" alt=""
                width="32" height="32" class="rounded-circle flex-shrink-0 shadow-sm">
            <strong class="sidebar-text ms-2">{{ Auth::user()->name }}</strong>
        </a>
        <ul class="dropdown-menu text-small shadow border-0" aria-labelledby="dropdownUser1"
            style="background-color: #fce4ec;">
            <li><a class="dropdown-item" href="{{ route('profile.edit') }}" style="color: #5c3d42;">Perfil</a></li>
            <li>
                <hr class="dropdown-divider border-white opacity-50">
            </li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item" style="color: #5c3d42;">Cerrar Sesión</button>
                </form>
            </li>
        </ul>
    </div>
</div>

<style>
    /* Active State Style */
    .nav-link.active {
        background-color: #ffffff !important;
        color: #d63384 !important;
        /* Proper Pink */
        font-weight: bold;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
    }

    .nav-link {
        display: flex;
        align-items: center;
        border-radius: 12px;
        padding: 10px 0;
        white-space: nowrap;
        transition: all 0.2s ease;
    }

    .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.6);
        color: #d63384 !important;
    }

    /* --- Collapsed State Logic --- */
    .sidebar-collapsed {
        width: 60px !important;
    }

    /* Stack Header Vertically when collapsed to fit Button & Logo */
    .sidebar-collapsed .sidebar-header {
        flex-direction: column !important;
        gap: 10px;
        justify-content: center !important;
        padding-bottom: 5px;
    }

    /* Hide text when collapsed */
    .sidebar-collapsed .sidebar-text {
        display: none !important;
    }

    /* Center icons when collapsed */
    .sidebar-collapsed .nav-link {
        justify-content: center;
        padding-left: 0;
    }

    .sidebar-collapsed .dropdown-toggle::after {
        display: none !important;
    }

    /* Ensure Logo Wrapper centers perfectly */
    .sidebar-collapsed .logo-container {
        justify-content: center;
        width: 100%;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');

        // Restore state
        const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('sidebar-collapsed');
        }

        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('sidebar-collapsed'));
        });
    });
</script>