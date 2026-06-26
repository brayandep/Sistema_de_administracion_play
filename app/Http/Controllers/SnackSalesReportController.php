<?php

namespace App\Http\Controllers;

use App\Models\SnackSaleItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SnackSalesReportController extends Controller
{
    public function index(Request $request)
    {
        [$startDate, $endDate, $filterType] =
            $this->resolveDateRange($request);

        $productType =
            $request->get('product_type', 'todos');

        $items =
            $this->getItems($startDate, $endDate, $productType);

        $totals =
            $this->calculateTotals($items);

        return view(
            'reports.snacks.index',
            compact(
                'items',
                'totals',
                'startDate',
                'endDate',
                'filterType',
                'productType'
            )
        );
    }

    public function pdf(Request $request)
    {
        [$startDate, $endDate, $filterType] =
            $this->resolveDateRange($request);

        $productType =
            $request->get('product_type', 'todos');

        $items =
            $this->getItems($startDate, $endDate, $productType);

        $totals =
            $this->calculateTotals($items);

        $pdf = app('dompdf.wrapper');

        $pdf->loadView(
            'reports.snacks.pdf',
            compact(
                'items',
                'totals',
                'startDate',
                'endDate',
                'filterType',
                'productType'
            )
        );

        $fileName =
            'reporte-productos-' .
            now()->format('Ymd-His') .
            '.pdf';

        return $pdf->download($fileName);
    }

    private function resolveDateRange(Request $request)
    {
        $filterType =
            $request->get('filter', 'day');

        $date =
            $request->get(
                'date',
                now()->format('Y-m-d')
            );

        $baseDate =
            Carbon::parse($date);

        if ($filterType === 'week') {
            return [
                $baseDate->copy()->startOfWeek(),
                $baseDate->copy()->endOfWeek(),
                $filterType,
            ];
        }

        if ($filterType === 'month') {
            return [
                $baseDate->copy()->startOfMonth(),
                $baseDate->copy()->endOfMonth(),
                $filterType,
            ];
        }

        return [
            $baseDate->copy()->startOfDay(),
            $baseDate->copy()->endOfDay(),
            'day',
        ];
    }

    private function getItems(
        Carbon $startDate,
        Carbon $endDate,
        string $productType
    ) {
        return SnackSaleItem::query()
            ->with([
                'sale.user',
            ])
            ->whereHas('sale', function ($query) use (
                $startDate,
                $endDate
            ) {
                $query
                    ->whereBetween('sold_at', [
                        $startDate,
                        $endDate,
                    ])
                    ->where('status', 'completada');
            })
            ->when($productType !== 'todos', function ($query) use ($productType) {
                $query->where('product_type', $productType);
            })
            ->orderBy('created_at')
            ->get();
    }

    private function calculateTotals($items)
    {
        $efectivo = $items
            ->filter(function ($item) {
                return $item->sale->payment_method === 'efectivo';
            })
            ->sum('total');

        $qr = $items
            ->filter(function ($item) {
                return $item->sale->payment_method === 'qr';
            })
            ->sum('total');

        $mixto = $items
            ->filter(function ($item) {
                return $item->sale->payment_method === 'mixto';
            })
            ->sum('total');

        return [
            'efectivo' => $efectivo,
            'qr' => $qr,
            'mixto' => $mixto,
            'total' => $items->sum('total'),
            'items_count' => $items->sum('quantity'),
            'sales_count' => $items
                ->pluck('snack_sale_id')
                ->unique()
                ->count(),
        ];
    }
}