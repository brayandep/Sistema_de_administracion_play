@extends('layouts.admin')

@section('title', 'Reporte de ventas')

@section('page-title', 'Reporte de ventas')

@section(
    'page-description',
    'Consulta ingresos por PS5, billar y promociones'
)

@section('content')

<section class="card">

    <div class="card__header report-header">

        <div>
            <h2>Reporte de ventas</h2>

            <p>
                Desde
                <strong>{{ $startDate->format('d/m/Y H:i') }}</strong>
                hasta
                <strong>{{ $endDate->format('d/m/Y H:i') }}</strong>
            </p>
        </div>

        <a
            href="{{ route(
                'reports.sales.pdf',
                request()->only('filter', 'date')
            ) }}"
            class="primary-button"
        >
            Descargar PDF
        </a>

    </div>

    <div class="card__body">

        <form
            method="GET"
            action="{{ route('reports.sales.index') }}"
            class="report-filter"
        >

            <label>
                <input
                    type="radio"
                    name="filter"
                    value="day"
                    {{ $filterType === 'day'
                        ? 'checked'
                        : '' }}
                >
                Día
            </label>

            <label>
                <input
                    type="radio"
                    name="filter"
                    value="week"
                    {{ $filterType === 'week'
                        ? 'checked'
                        : '' }}
                >
                Semana
            </label>

            <label>
                <input
                    type="radio"
                    name="filter"
                    value="month"
                    {{ $filterType === 'month'
                        ? 'checked'
                        : '' }}
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

            <button
                type="submit"
                class="primary-button"
            >
                Aplicar
            </button>

        </form>

        <div class="report-summary">

            <div class="summary-card">
                <span>Total ventas</span>
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
                <span>Mixto</span>
                <strong>
                    Bs {{ number_format($totals['mixto'], 2) }}
                </strong>
            </div>

            <div class="summary-card">
                <span>Descuentos</span>
                <strong>
                    Bs {{ number_format($totals['discount'], 2) }}
                </strong>
            </div>

            <div class="summary-card">
                <span>Reservas</span>
                <strong>
                    {{ $totals['count'] }}
                </strong>
            </div>

        </div>

        @if ($reservations->count() > 0)

            <div class="table-container">
                <table class="data-table">

                    <thead>
                        <tr>
                            <th>Nro</th>
                            <th>Cliente</th>
                            <th>Estación</th>
                            <th>Horario</th>
                            <th>Duración</th>
                            <th>Pago</th>
                            <th>Usuario</th>
                            <th>Subtotal</th>
                            <th>Desc.</th>
                            <th>Total</th>
                            <th>Estado</th>
                             @if (auth()->user()->role === 'super_admin')
            <th>Acciones</th>
        @endif
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($reservations as $reservation)
                            <tr>
                                <td>
                                    {{ $loop->iteration }}
                                </td>

                                <td>
                                    {{ $reservation->customer->full_name }}
                                </td>

                                <td>
                                    {{ $reservation->station->name }}
                                </td>

                                <td>
                                    {{ $reservation->started_at->format('d/m/Y H:i') }}
                                    -
                                    {{ $reservation->ended_at->format('H:i') }}
                                </td>

                                <td>
                                    {{ floor($reservation->duration_minutes / 60) }} h
                                    {{ $reservation->duration_minutes % 60 }} min
                                </td>

                                <td>
                                    {{ ucfirst($reservation->payment_method) }}
                                </td>

                                <td>
                                    {{ $reservation->user->name ?? 'Sin usuario' }}
                                </td>

                                <td>
                                    Bs {{ number_format($reservation->subtotal, 2) }}
                                </td>

                                <td>
                                    Bs {{ number_format($reservation->discount, 2) }}
                                </td>

                                <td>
                                    <strong>
                                        Bs {{ number_format($reservation->total, 2) }}
                                    </strong>
                                </td>

                                <td>
                                    {{ ucfirst($reservation->status) }}
                                </td>
                                @if (auth()->user()->role === 'super_admin')
    <td>
        <a
            href="{{ route('reservations.edit', ['reservation' => $reservation->id]) }}"
            class="action-button action-button--edit"
        >
            Editar
        </a>
    </td>
@endif
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
                    No se encontraron reservas dentro del rango seleccionado.
                </p>
            </div>

        @endif

    </div>

</section>

@endsection