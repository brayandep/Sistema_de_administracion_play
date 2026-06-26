@extends('layouts.admin')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section(
    'page-description',
    'Resumen general del centro de entretenimiento'
)

@section('content')

<section class="card">

    <div class="card__body">

        <h1>
            Bienvenido, {{ auth()->user()->name }}
        </h1>

        <p style="
            margin-top: 10px;
            color: #65727e;
            line-height: 1.6;
        ">
            Has iniciado sesión como
            <strong>
                {{ ucfirst(auth()->user()->role) }}
            </strong>.
            Desde este panel podrás administrar los
            productos, ventas y reservas.
        </p>

    </div>

</section>

<div
    style="
        display: grid;
        grid-template-columns:
            repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-top: 24px;
    "
>

    <a
        href="{{ route('products.create') }}"
        class="card"
        style="
            padding: 25px;
            color: inherit;
            text-decoration: none;
        "
    >
        <div style="font-size: 31px; margin-bottom: 14px;">
            ＋
        </div>

        <h2 style="font-size: 19px;">
            Registrar producto
        </h2>

        <p style="
            margin-top: 9px;
            color: #65727e;
            font-size: 13px;
            line-height: 1.5;
        ">
            Agrega snacks o productos de regalos
            al inventario.
        </p>
    </a>

    <a
        href="{{ route('products.index') }}"
        class="card"
        style="
            padding: 25px;
            color: inherit;
            text-decoration: none;
        "
    >
        <div style="font-size: 31px; margin-bottom: 14px;">
            📦
        </div>

        <h2 style="font-size: 19px;">
            Productos
        </h2>

        <p style="
            margin-top: 9px;
            color: #65727e;
            font-size: 13px;
            line-height: 1.5;
        ">
            Consulta productos, existencias,
            precios y disponibilidad.
        </p>
    </a>

    <article
        class="card"
        style="
            padding: 25px;
            opacity: 0.65;
        "
    >
        <div style="font-size: 31px; margin-bottom: 14px;">
            🎮
        </div>

        <h2 style="font-size: 19px;">
            PlayStation
        </h2>

        <p style="
            margin-top: 9px;
            color: #65727e;
            font-size: 13px;
            line-height: 1.5;
        ">
            Próximamente controlaremos reservas
            y sesiones.
        </p>
    </article>

</div>

@endsection