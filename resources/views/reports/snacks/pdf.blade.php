<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>Reporte de productos</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
        }

        h1 {
            margin-bottom: 4px;
            font-size: 22px;
        }

        p {
            margin-top: 0;
            color: #4b5563;
        }

        .filter-label {
            margin-top: 6px;
            font-size: 12px;
            color: #374151;
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
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
            font-weight: bold;
        }

        .summary {
            margin-top: 18px;
            width: 50%;
        }

        .summary td {
            font-weight: bold;
        }

        .total {
            background: #fef3c7;
        }

        .empty {
            margin-top: 20px;
            padding: 14px;
            border: 1px solid #d1d5db;
            background: #f9fafb;
            color: #4b5563;
        }
    </style>
</head>

<body>

    <h1>Reporte de ventas de productos</h1>

    <p>
        Desde
        <strong>{{ $startDate->format('d/m/Y H:i') }}</strong>
        hasta
        <strong>{{ $endDate->format('d/m/Y H:i') }}</strong>
    </p>

    <div class="filter-label">
        Tipo de producto:
        <strong>
            @if ($productType === 'todos')
                Todos
            @elseif ($productType === 'snack')
                Snacks
            @elseif ($productType === 'regalo')
                Regalos
            @else
                {{ ucfirst($productType) }}
            @endif
        </strong>
    </div>

    @if ($items->count() > 0)

        <table>
            <thead>
                <tr>
                    <th>Nro</th>
                    <th>Código</th>
                    <th>Usuario</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th>Cant.</th>
                    <th>Precio</th>
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
                            Bs {{ number_format($item->total, 2) }}
                        </td>

                        <td>
                            {{ $item->sale->sold_at->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @else

        <div class="empty">
            No se encontraron ventas de productos dentro del rango seleccionado.
        </div>

    @endif

    <table class="summary">
        <tr>
            <td>Total efectivo</td>
            <td>
                Bs {{ number_format($totals['efectivo'], 2) }}
            </td>
        </tr>

        <tr>
            <td>Total QR</td>
            <td>
                Bs {{ number_format($totals['qr'], 2) }}
            </td>
        </tr>

        @if (isset($totals['mixto']))
            <tr>
                <td>Total mixto</td>
                <td>
                    Bs {{ number_format($totals['mixto'], 2) }}
                </td>
            </tr>
        @endif

        <tr>
            <td>Total ventas</td>
            <td>
                {{ $totals['sales_count'] }}
            </td>
        </tr>

        <tr>
            <td>Productos vendidos</td>
            <td>
                {{ $totals['items_count'] }}
            </td>
        </tr>

        <tr class="total">
            <td>Total general</td>
            <td>
                Bs {{ number_format($totals['total'], 2) }}
            </td>
        </tr>
    </table>

</body>
</html>