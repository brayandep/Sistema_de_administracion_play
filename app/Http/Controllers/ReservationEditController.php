<?php

namespace App\Http\Controllers;

use App\Models\LoyaltyMovement;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationEditController extends Controller
{
    public function edit(Reservation $reservation)
    {
        $reservation->load([
            'customer',
            'station',
        ]);

        return view(
            'reservations.edit',
            compact('reservation')
        );
    }

    public function update(Request $request, Reservation $reservation)
    {
        $validated = $request->validate(
            [
                'started_at' => [
                    'required',
                    'date',
                ],

                'ended_at' => [
                    'required',
                    'date',
                    'after:started_at',
                ],

                'people_count' => [
                    'required',
                    'integer',
                    'min:1',
                    'max:20',
                ],

                'payment_method' => [
                    'required',
                    'in:efectivo,qr,mixto,cortesia',
                ],
            ],
            [
                'ended_at.after' =>
                    'La hora de salida debe ser mayor a la hora de inicio.',
            ]
        );

        DB::transaction(function () use ($validated, $reservation) {
            $reservation = Reservation::query()
                ->with('station')
                ->lockForUpdate()
                ->findOrFail($reservation->id);

            $startedAt = Carbon::parse($validated['started_at']);
            $endedAt = Carbon::parse($validated['ended_at']);

            $durationMinutes = $startedAt->diffInMinutes($endedAt);

            if ($durationMinutes < 15) {
                throw new \Exception(
                    'La reserva debe durar al menos 15 minutos.'
                );
            }

            $peopleCount = (int) $validated['people_count'];

            $hourlyRate = $peopleCount === 1
                ? 10
                : (float) $reservation->station->hourly_rate;

            $freeMinutes = min(
                (int) $reservation->free_minutes,
                $durationMinutes
            );

            $paidMinutes = max(
                $durationMinutes - $freeMinutes,
                0
            );

            $subtotal = round(
                ($durationMinutes / 60) * $hourlyRate,
                2
            );

            $total = round(
                ($paidMinutes / 60) * $hourlyRate,
                2
            );

            $discount = round($subtotal - $total, 2);

            $reservation->update([
                'started_at' => $startedAt,
                'ended_at' => $endedAt,
                'people_count' => $peopleCount,
                'duration_minutes' => $durationMinutes,
                'paid_minutes' => $paidMinutes,
                'free_minutes' => $freeMinutes,
                'hourly_rate' => $hourlyRate,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'payment_method' => $validated['payment_method'],
            ]);

            $movement = LoyaltyMovement::query()
                ->where('reservation_id', $reservation->id)
                ->where('type', 'earned')
                ->first();

            if ($movement) {
                $movement->update([
                    'minutes' => $paidMinutes,
                    'description' =>
                        'Corrección de reserva en ' .
                        $reservation->station->name,
                ]);
            }
        });

        return redirect()
            ->route('reservations.index')
            ->with('success', 'La reserva fue corregida correctamente.');
    }
}