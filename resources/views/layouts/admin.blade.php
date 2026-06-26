<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>
        @yield('title', 'Panel administrativo')
        | {{ config('app.name') }}
    </title>

    <link
        rel="stylesheet"
        href="{{ asset('css/admin.css') }}"
    >
</head>

<body>

<div class="admin-layout">

    <aside class="sidebar" id="sidebar">

        <div class="sidebar__brand">
            <div class="sidebar__logo">
                P
            </div>

            <div>
                <strong>PIXEL PLAY</strong>
                <span>Panel administrativo</span>
            </div>
        </div>

        <nav class="sidebar__navigation">
            <div class="sidebar__section">
    SALAS
</div>

<a
    href="{{ route('reservations.index') }}"
    class="sidebar__link
        {{ request()->routeIs('reservations.*')
            ? 'sidebar__link--active'
            : '' }}"
>
    <span class="sidebar__icon">🎮</span>
    <span>Reservas</span>
</a>
<a
    href="{{ route('reports.sales.index') }}"
    class="sidebar__link
        {{ request()->routeIs('reports.sales.*')
            ? 'sidebar__link--active'
            : '' }}"
>
    <span class="sidebar__icon">📊</span>
    <span>Reporte de ventas</span>
</a>
<div class="sidebar__section">
    Snacks
</div>

<a
    href="{{ route('snack-sales.index') }}"
    class="sidebar__link
        {{ request()->routeIs('snack-sales.*')
            ? 'sidebar__link--active'
            : '' }}"
>
    <span class="sidebar__icon">🍿</span>
    <span>Venta de snacks</span>
</a>

<a
    href="{{ route('reports.snacks.index') }}"
    class="sidebar__link
        {{ request()->routeIs('reports.snacks.*')
            ? 'sidebar__link--active'
            : '' }}"
>
    <span class="sidebar__icon">📊</span>
    <span>Reporte snacks</span>
</a>

<div class="sidebar__section">
    Usuarios
</div>

<a
    href="{{ route('users.activity') }}"
    class="sidebar__link
        {{ request()->routeIs('users.activity')
            ? 'sidebar__link--active'
            : '' }}"
>
    <span class="sidebar__icon">👤</span>
    <span>Historial de ingreso</span>
</a>
            
            
            <a
                href="{{ route('dashboard') }}"
                class="sidebar__link
                    {{ request()->routeIs('dashboard')
                        ? 'sidebar__link--active'
                        : '' }}"
            >
                <span class="sidebar__icon">▦</span>
                <span>Dashboard</span>
            </a>

            <div class="sidebar__section">
                Productos
            </div>

            <a
                href="{{ route('products.index') }}"
                class="sidebar__link
                    {{ request()->routeIs('products.*')
                        ? 'sidebar__link--active'
                        : '' }}"
            >
                <span class="sidebar__icon">▣</span>
                <span>Productos</span>
            </a>

            <a
                href="{{ route('products.create') }}"
                class="sidebar__link"
            >
                <span class="sidebar__icon">＋</span>
                <span>Registrar producto</span>
            </a>

            <div class="sidebar__section">
                Próximamente
            </div>

            <a href="#" class="sidebar__link sidebar__link--disabled">
                <span class="sidebar__icon">🎮</span>
                <span>PlayStation</span>
            </a>

            <a href="#" class="sidebar__link sidebar__link--disabled">
                <span class="sidebar__icon">🛒</span>
                <span>Ventas</span>
            </a>

            <a href="#" class="sidebar__link sidebar__link--disabled">
                <span class="sidebar__icon">💵</span>
                <span>Caja</span>
            </a>

        </nav>

        <div class="sidebar__user">
            <div class="sidebar__avatar">
                {{ strtoupper(
                    substr(auth()->user()->name, 0, 1)
                ) }}
            </div>

            <div class="sidebar__user-data">
                <strong>
                    {{ auth()->user()->name }}
                </strong>

                <span>
                    {{ ucfirst(auth()->user()->role) }}
                </span>
            </div>
        </div>

    </aside>

    <section class="admin-content">

        <header class="topbar">

            <button
                type="button"
                class="menu-button"
                id="menuButton"
                aria-label="Abrir menú"
            >
                ☰
            </button>

            <div class="topbar__title">
                <strong>@yield('page-title', 'Dashboard')</strong>
                <span>
                    @yield(
                        'page-description',
                        'Administración del sistema'
                    )
                </span>
            </div>

            <form
                method="POST"
                action="{{ route('logout') }}"
            >
                @csrf

                <button
                    type="submit"
                    class="logout-button"
                >
                    Cerrar sesión
                </button>
            </form>

        </header>

        <main class="main-content">

            @if (session('success'))
                <div class="alert alert--success">
                    <span>✓</span>

                    <div>
                        <strong>Operación realizada</strong>
                        <p>{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert--error">
                    <span>!</span>

                    <div>
                        <strong>Ocurrió un problema</strong>
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @yield('content')

        </main>

    </section>

</div>

<div
    class="sidebar-overlay"
    id="sidebarOverlay"
></div>

<script>
    const sidebar = document.getElementById('sidebar');
    const menuButton = document.getElementById('menuButton');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    function toggleSidebar() {
        sidebar.classList.toggle('sidebar--open');
        sidebarOverlay.classList.toggle('sidebar-overlay--visible');
    }

    menuButton.addEventListener('click', toggleSidebar);
    sidebarOverlay.addEventListener('click', toggleSidebar);
</script>

@stack('scripts')

</body>
</html>