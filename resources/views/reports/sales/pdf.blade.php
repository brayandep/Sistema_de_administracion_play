<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>Reporte de ventas</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
        }

        h1 {
            margin-bottom: 4px;
            font-size: 24px;
        }

        p {
            margin-top: 0;
            color: #4b5563;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }

        th,
        td {
            padding: 7px;
            border: 1px solid #d1d5db;
            text-align: left;
        }

        th {
            background: #f3f4f6;
            font-weight: bold;
        }

        .summary {
            margin-top: 18px;
            width: 45%;
        }

        .summary td {
            font-weight: bold;
        }

        .total {
            background: #fef3c7;
        }
    </style>
</head>

<body>

<h1>Reporte de ventas</h1>

<p>
    Desde
    <strong>{{ $startDate->format('d/m/Y H:i') }}</strong>
    hasta
    <strong>{{ $endDate->format('d/m/Y H:i') }}</strong>
</p>

<table>
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
        </tr>
    </thead>

    <tbody>
        @foreach ($reservations as $reservation)
            <tr>
                <td>{{ $loop->iteration }}</td>

                <td>{{ $reservation->customer->full_name }}</td>

                <td>{{ $reservation->station->name }}</td>

                <td>
                    {{ $reservation->started_at->format('d/m/Y H:i') }}
                    -
                    {{ $reservation->ended_at->format('H:i') }}
                </td>

                <td>
                    {{ floor($reservation->duration_minutes / 60) }} h
                    {{ $reservation->duration_minutes % 60 }} min
                </td>

                <td>{{ ucfirst($reservation->payment_method) }}</td>

                <td>{{ $reservation->user->name ?? 'Sin usuario' }}</td>

                <td>Bs {{ number_format($reservation->subtotal, 2) }}</td>

                <td>Bs {{ number_format($reservation->discount, 2) }}</td>

                <td>Bs {{ number_format($reservation->total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="summary">
    <tr>
        <td>Total efectivo</td>
        <td>Bs {{ number_format($totals['efectivo'], 2) }}</td>
    </tr>

    <tr>
        <td>Total QR</td>
        <td>Bs {{ number_format($totals['qr'], 2) }}</td>
    </tr>

    <tr>
        <td>Total mixto</td>
        <td>Bs {{ number_format($totals['mixto'], 2) }}</td>
    </tr>

    <tr>
        <td>Total descuentos</td>
        <td>Bs {{ number_format($totals['discount'], 2) }}</td>
    </tr>

    <tr class="total">
        <td>Total general</td>
        <td>Bs {{ number_format($totals['total'], 2) }}</td>
    </tr>
</table>

</body>
</html>