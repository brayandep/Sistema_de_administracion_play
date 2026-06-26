<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        [$startDate, $endDate, $filterType] =
            $this->resolveDateRange($request);

        $reservations = $this->getReservations(
            $startDate,
            $endDate
        );

        $totals = $this->calculateTotals($reservations);

        return view(
            'reports.sales.index',
            compact(
                'reservations',
                'totals',
                'startDate',
                'endDate',
                'filterType'
            )
        );
    }

    public function pdf(Request $request)
    {
        [$startDate, $endDate, $filterType] =
            $this->resolveDateRange($request);

        $reservations = $this->getReservations(
            $startDate,
            $endDate
        );

        $totals = $this->calculateTotals($reservations);

        $pdf = app('dompdf.wrapper');

        $pdf->loadView(
            'reports.sales.pdf',
            compact(
                'reservations',
                'totals',
                'startDate',
                'endDate',
                'filterType'
            )
        );

        $fileName =
            'reporte-ventas-' .
            now()->format('Ymd-His') .
            '.pdf';

        return $pdf->download($fileName);
    }

    private function resolveDateRange(Request $request)
    {
        $filterType = $request->get('filter', 'day');

        $date = $request->get(
            'date',
            now()->format('Y-m-d')
        );

        $baseDate = Carbon::parse($date);

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

    private function getReservations(
        Carbon $startDate,
        Carbon $endDate
    ) {
        return Reservation::query()
            ->with([
                'customer',
                'station',
                'user',
                'rewardTicket',
            ])
            ->whereBetween('started_at', [
                $startDate,
                $endDate,
            ])
            ->whereIn('status', [
                'activa',
                'finalizada',
            ])
            ->orderBy('started_at')
            ->get();
    }

    private function calculateTotals($reservations)
    {
        return [
            'efectivo' => $reservations
                ->where('payment_method', 'efectivo')
                ->sum('total'),

            'qr' => $reservations
                ->where('payment_method', 'qr')
                ->sum('total'),

            'mixto' => $reservations
                ->where('payment_method', 'mixto')
                ->sum('total'),

            'cortesia' => $reservations
                ->where('payment_method', 'cortesia')
                ->sum('discount'),

            'subtotal' => $reservations->sum('subtotal'),

            'discount' => $reservations->sum('discount'),

            'total' => $reservations->sum('total'),

            'paid_minutes' => $reservations->sum('paid_minutes'),

            'free_minutes' => $reservations->sum('free_minutes'),

            'count' => $reservations->count(),
        ];
    }
}