@extends('layouts.admin')

@section('title', 'Productos')

@section('page-title', 'Productos')

@section(
    'page-description',
    'Administra los snacks y regalos del negocio'
)

@section('content')

<div class="page-header">

    <div>
        <h1>Productos registrados</h1>

        <p>
            Consulta la existencia, precio y disponibilidad
            de cada producto.
        </p>
    </div>

    <a
        href="{{ route('products.create') }}"
        class="primary-button"
    >
        <span>＋</span>
        Registrar producto
    </a>

</div>

<section class="card">

    <div class="card__header">
        <h2>Inventario de productos</h2>

        <p>
            Total registrado:
            {{ $products->total() }}
        </p>
    </div>
<form
    method="GET"
    action="{{ route('products.index') }}"
    class="product-filter"
>
    <div class="product-filter__search">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            class="form-control"
            placeholder="Buscar producto por nombre..."
        >
    </div>

    <select
        name="type"
        class="form-control"
    >
        <option
            value="todos"
            {{ request('type', 'todos') === 'todos' ? 'selected' : '' }}
        >
            Todos
        </option>

        <option
            value="snack"
            {{ request('type') === 'snack' ? 'selected' : '' }}
        >
            Snacks
        </option>

        <option
            value="regalo"
            {{ request('type') === 'regalo' ? 'selected' : '' }}
        >
            Regalos
        </option>
    </select>

    <button
        type="submit"
        class="primary-button"
    >
        Buscar
    </button>

    <a
        href="{{ route('products.index') }}"
        class="secondary-button"
    >
        Limpiar
    </a>
</form>
    @if ($products->count() > 0)

        <div class="table-container">

            <table class="data-table">

                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach ($products as $product)

                        <tr>

                            <td>
                                <div class="product-cell">

                                    <div class="product-image">

                                        @if ($product->image)
                                            <img
                                                src="{{ asset(
                                                    'storage/' .
                                                    $product->image
                                                ) }}"
                                                alt="{{ $product->name }}"
                                            >
                                        @else
                                            Sin foto
                                        @endif

                                    </div>

                                    <div>
                                        <strong>
                                            {{ $product->name }}
                                        </strong>

                                        <span>
                                            {{ $product->description
                                                ? \Illuminate\Support\Str::limit(
                                                    $product->description,
                                                    55
                                                )
                                                : 'Sin descripción' }}
                                        </span>
                                    </div>

                                </div>
                            </td>

                            <td>
                                @if (
                                    $product->product_type === 'snack'
                                )
                                    <span class="badge badge--snack">
                                        Snack
                                    </span>
                                @else
                                    <span class="badge badge--gift">
                                        Regalo
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ $product->quantity }}
                            </td>

                            <td>
                                Bs
                                {{ number_format(
                                    $product->price,
                                    2,
                                    ',',
                                    '.'
                                ) }}
                            </td>

                            <td>
                                @if ($product->available)
                                    <span
                                        class="badge
                                            badge--available"
                                    >
                                        Disponible
                                    </span>
                                @else
                                    <span
                                        class="badge
                                            badge--unavailable"
                                    >
                                        No disponible
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ $product->created_at
                                    ->format('d/m/Y') }}
                            </td>
                            <td>
    <div class="action-buttons">

        @if (auth()->user()->role === 'super_admin')
            <a
                href="{{ route('products.edit', $product) }}"
                class="action-button action-button--edit"
            >
                Editar
            </a>
        @endif

        <a
            href="{{ route('products.addStockForm', $product) }}"
            class="action-button action-button--add"
        >
            Add
        </a>

    </div>
</td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        <div class="pagination-wrapper">
@if ($products->hasPages())
    <div class="custom-pagination">

        <div class="custom-pagination__info">
            Mostrando
            <strong>{{ $products->firstItem() }}</strong>
            a
            <strong>{{ $products->lastItem() }}</strong>
            de
            <strong>{{ $products->total() }}</strong>
            productos
        </div>

        <div class="custom-pagination__buttons">

            @if ($products->onFirstPage())
                <span class="custom-pagination__button custom-pagination__button--disabled">
                    Anterior
                </span>
            @else
                <a
                    href="{{ $products->previousPageUrl() }}"
                    class="custom-pagination__button"
                >
                    Anterior
                </a>
            @endif

            @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                @if (
                    $page === 1 ||
                    $page === $products->lastPage() ||
                    abs($page - $products->currentPage()) <= 2
                )
                    @if ($page === $products->currentPage())
                        <span class="custom-pagination__button custom-pagination__button--active">
                            {{ $page }}
                        </span>
                    @else
                        <a
                            href="{{ $url }}"
                            class="custom-pagination__button"
                        >
                            {{ $page }}
                        </a>
                    @endif
                @endif
            @endforeach

            @if ($products->hasMorePages())
                <a
                    href="{{ $products->nextPageUrl() }}"
                    class="custom-pagination__button"
                >
                    Siguiente
                </a>
            @else
                <span class="custom-pagination__button custom-pagination__button--disabled">
                    Siguiente
                </span>
            @endif

        </div>

    </div>
@endif        </div>

    @else

        <div class="empty-state">

            <div class="empty-state__icon">
                📦
            </div>

            <h3>
                Todavía no existen productos
            </h3>

            <p>
                Registra el primer snack o producto
                de regalos del negocio.
            </p>

            <a
                href="{{ route('products.create') }}"
                class="primary-button"
            >
                Registrar producto
            </a>

        </div>

    @endif

</section>

@endsection