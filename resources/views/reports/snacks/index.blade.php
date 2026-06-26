@extends('layouts.admin')

@section('title', 'Reporte de productos')

@section('page-title', 'Reporte de productos')

@section(
    'page-description',
    'Consulta ventas de snacks y regalos por día, semana o mes'
)

@section('content')

<section class="card">

    <div class="card__header report-header">

        <div>
            <h2>Reporte de ventas de productos</h2>

            <p>
                Desde
                <strong>{{ $startDate->format('d/m/Y H:i') }}</strong>
                hasta
                <strong>{{ $endDate->format('d/m/Y H:i') }}</strong>
            </p>
        </div>

        <a
            href="{{ route(
                'reports.snacks.pdf',
                request()->only('filter', 'date', 'product_type')
            ) }}"
            class="primary-button"
        >
            Descargar PDF
        </a>

    </div>

    <div class="card__body">

        <form
            method="GET"
            action="{{ route('reports.snacks.index') }}"
            class="report-filter"
        >

            <label>
                <input
                    type="radio"
                    name="filter"
                    value="day"
                    {{ $filterType === 'day' ? 'checked' : '' }}
                >
                Día
            </label>

            <label>
                <input
                    type="radio"
                    name="filter"
                    value="week"
                    {{ $filterType === 'week' ? 'checked' : '' }}
                >
                Semana
            </label>

            <label>
                <input
                    type="radio"
                    name="filter"
                    value="month"
                    {{ $filterType === 'month' ? 'checked' : '' }}
                >
                Mes
            </label>

            <input
                type="date"
                name="date"
                value="{{ request(
                    'date',
                    now()->format('Y-m-d')
                ) }}"
                class="form-control"
            >

            <select
                name="product_type"
                class="form-control"
            >
                <option
                    value="todos"
                    {{ $productType === 'todos' ? 'selected' : '' }}
                >
                    Todos
                </option>

                <option
                    value="snack"
                    {{ $productType === 'snack' ? 'selected' : '' }}
                >
                    Snacks
                </option>

                <option
                    value="regalo"
                    {{ $productType === 'regalo' ? 'selected' : '' }}
                >
                    Regalos
                </option>
            </select>

            <button
                type="submit"
                class="primary-button"
            >
                Aplicar
            </button>

        </form>

        <div class="report-summary">

            <div class="summary-card">
                <span>Total productos</span>
                <strong>
                    Bs {{ number_format($totals['total'], 2) }}
                </strong>
            </div>

            <div class="summary-card">
                <span>Efectivo</span>
                <strong>
                    Bs {{ number_format($totals['efectivo'], 2) }}
                </strong>
            </div>

            <div class="summary-card">
                <span>QR</span>
                <strong>
                    Bs {{ number_format($totals['qr'], 2) }}
                </strong>
            </div>

            <div class="summary-card">
                <span>Ventas</span>
                <strong>
                    {{ $totals['sales_count'] }}
                </strong>
            </div>

            <div class="summary-card">
                <span>Productos vendidos</span>
                <strong>
                    {{ $totals['items_count'] }}
                </strong>
            </div>

        </div>

        @if ($items->count() > 0)

            <div class="table-container">

                <table class="data-table">

                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Código venta</th>
                            <th>Usuario</th>
                            <th>Producto</th>
                            <th>Tipo</th>
                            <th>Cantidad</th>
                            <th>Precio unit.</th>
                            <th>Pago</th>
                            <th>Total</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    {{ $item->sale->sale_number }}
                                </td>

                                <td>
                                    {{ $item->sale->user->name ?? 'Sin usuario' }}
                                </td>

                                <td>
                                    {{ $item->product_name }}
                                </td>

                                <td>
                                    {{ ucfirst($item->product_type) }}
                                </td>

                                <td>
                                    {{ $item->quantity }}
                                </td>

                                <td>
                                    Bs {{ number_format($item->unit_price, 2) }}
                                </td>

                                <td>
                                    {{ ucfirst($item->sale->payment_method) }}
                                </td>

                                <td>
                                    <strong>
                                        Bs {{ number_format($item->total, 2) }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $item->sale->sold_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>

            </div>

        @else

            <div class="empty-state">
                <div class="empty-state__icon">
                    📊
                </div>

                <h3>No hay ventas registradas</h3>

                <p>
                    No se encontraron ventas de productos
                    dentro del rango seleccionado.
                </p>
            </div>

        @endif

    </div>

</section>

@endsection