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

    <title>Iniciar sesión | {{ config('app.name') }}</title>

    <link
        rel="stylesheet"
        href="{{ asset('css/login.css') }}"
    >
</head>

<body>

<header class="topbar">
    <div class="topbar__content">
        <a href="{{ route('login') }}" class="brand">
            <span class="brand__icon">P</span>

            <div>
                <strong>LEVEL UP </strong>
                <small>Sistema de gestión</small>
            </div>
        </a>

        <div class="topbar__status">
            <span class="status-dot"></span>
            Acceso administrativo
        </div>
    </div>
</header>

<main class="login-layout">

    <section class="login-panel">
        <div class="login-card">

            <div class="login-card__heading">
                <span class="login-card__eyebrow">
                    CENTRO DE ENTRETENIMIENTO
                </span>

                <h1>Bienvenido a<br>Pixel Play</h1>

                <p>
                    Ingresa tus credenciales para acceder al control
                    de reservas, sesiones de juego, caja y snacks.
                </p>
            </div>

            @if (session('success'))
                <div class="alert alert--success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert--error">
                    <strong>No se pudo iniciar sesión.</strong>

                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form
                method="POST"
                action="{{ route('login.attempt') }}"
                class="login-form"
                novalidate
            >
                @csrf

                <div class="form-group">
                    <label for="username">
                        Nombre de usuario
                    </label>

                    <div class="input-wrapper">
                        <span class="input-icon">👤</span>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="{{ old('username') }}"
                            placeholder="Ingresa tu usuario"
                            autocomplete="username"
                            autofocus
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">
                        Contraseña
                    </label>

                    <div class="input-wrapper">
                        <span class="input-icon">🔒</span>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Ingresa tu contraseña"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            id="togglePassword"
                            class="password-toggle"
                            aria-label="Mostrar contraseña"
                        >
                            Mostrar
                        </button>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-option">
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                        >

                        <span>Recordar sesión</span>
                    </label>

                    <span class="secure-access">
                        Acceso seguro
                    </span>
                </div>

                <button type="submit" class="login-button">
                    Iniciar sesión
                    <span>→</span>
                </button>
            </form>

            <div class="login-help">
                <span class="login-help__icon">i</span>

                <p>
                    Este sistema es de uso exclusivo para el personal
                    autorizado del establecimiento.
                </p>
            </div>

        </div>
    </section>

    <section
        class="visual-panel"
        style="
            background-image:
                linear-gradient(
                    135deg,
                    rgba(2, 18, 34, 0.30),
                    rgba(3, 24, 45, 0.72)
                ),
                url('{{ asset('images/login-gaming.png') }}');
        "
    >
        <div class="visual-panel__content">
            <span class="visual-panel__badge">
                JUEGA · COMPARTE · DISFRUTA
            </span>

            <h2>
                Todo el negocio<br>
                desde un solo sistema
            </h2>

            <p>
                Controla sesiones de PlayStation, ventas,
                inventario y caja de manera rápida.
            </p>

            <div class="feature-list">
                <div class="feature">
                    <span>🎮</span>
                    Reservas y tiempo de juego
                </div>

                <div class="feature">
                    <span>🍿</span>
                    Ventas de snacks
                </div>

                <div class="feature">
                    <span>📊</span>
                    Control diario del negocio
                </div>
            </div>
        </div>
    </section>

</main>

<footer class="footer">
    <div class="footer__content">

        <div>
            <strong>Zona Game</strong>
            <p>Centro de entretenimiento juvenil y familiar.</p>
        </div>

        <div>
            <strong>Funciones principales</strong>
            <p>PlayStation · Snacks · Caja</p>
        </div>

        <div>
            <strong>Seguridad</strong>
            <p>Acceso exclusivo para personal autorizado.</p>
        </div>

        <div class="footer__copyright">
            © {{ date('Y') }} {{ config('app.name') }}
        </div>

    </div>
</footer>

<script>
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');

    togglePassword.addEventListener('click', function () {
        const isPassword =
            passwordInput.getAttribute('type') === 'password';

        passwordInput.setAttribute(
            'type',
            isPassword ? 'text' : 'password'
        );

        togglePassword.textContent =
            isPassword ? 'Ocultar' : 'Mostrar';

        togglePassword.setAttribute(
            'aria-label',
            isPassword
                ? 'Ocultar contraseña'
                : 'Mostrar contraseña'
        );
    });
</script>

</body>
</html>