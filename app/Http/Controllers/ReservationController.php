<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\LoyaltyMovement;
use App\Models\Reservation;
use App\Models\RewardTicket;
use App\Models\Station;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class ReservationController extends Controller
{
    public function index()
{
    $stations = Station::query()
        ->with([
            'activeReservation.customer',
        ])
        ->where('active', true)
        ->orderBy('name')
        ->get();

    $recentReservations = Reservation::query()
        ->with([
            'customer',
            'station',
            'rewardTicket',
        ])
        ->latest()
        ->limit(8)
        ->get();

    $anonymousCustomer = Customer::firstOrCreate(
        [
            'full_name' => 'Cliente general',
        ],
        [
            'phone' => null,
            'notes' => 'Cliente automático para reservas sin registro.',
            'active' => true,
        ]
    );

    return view(
        'reservations.index',
        compact(
            'stations',
            'recentReservations',
            'anonymousCustomer'
        )
    );
}

   public function store(Request $request)
{
    $validated = $request->validate(
        [
            'customer_id' => [
                'required',
                'exists:customers,id',
            ],

            'station_id' => [
                'required',
                'exists:stations,id',
            ],

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

            'use_reward' => [
                'nullable',
                'boolean',
            ],
        ],
        [
            'customer_id.required' =>
                'Selecciona o registra un cliente.',

            'station_id.required' =>
                'Selecciona una estación.',

            'started_at.required' =>
                'Selecciona la hora de inicio.',

            'ended_at.required' =>
                'Selecciona la hora de salida.',

            'ended_at.after' =>
                'La hora de salida debe ser mayor a la hora de inicio.',

            'payment_method.required' =>
                'Selecciona el método de pago.',
        ]
    );

    try {
        DB::transaction(function () use ($validated, $request) {
            $customer = Customer::query()
                ->lockForUpdate()
                ->findOrFail($validated['customer_id']);

            $station = Station::query()
                ->lockForUpdate()
                ->findOrFail($validated['station_id']);

            if (!$station->active) {
                throw new \Exception(
                    'La estación seleccionada no está activa.'
                );
            }

            if ($station->status !== 'disponible') {
                throw new \Exception(
                    'La estación seleccionada no está disponible.'
                );
            }

            $hasActiveReservation = Reservation::query()
                ->where('station_id', $station->id)
                ->where('status', 'activa')
                ->exists();

            if ($hasActiveReservation) {
                throw new \Exception(
                    'La estación ya tiene una reserva activa.'
                );
            }

            $startedAt = Carbon::parse($validated['started_at']);
            $endedAt = Carbon::parse($validated['ended_at']);

            $durationMinutes = $startedAt->diffInMinutes($endedAt);

            if ($durationMinutes < 15) {
                throw new \Exception(
                    'La reserva debe durar al menos 15 minutos.'
                );
            }

            if ($durationMinutes > 720) {
                throw new \Exception(
                    'La reserva no puede superar las 12 horas.'
                );
            }

        $peopleCount = (int) $validated['people_count'];

        $hourlyRate = $peopleCount === 1
            ? 10
            : (float) $station->hourly_rate;
            $rewardTicket = null;
            $freeMinutes = 0;

            $useReward = $request->boolean('use_reward');

            if ($useReward) {
                $rewardTicket = $customer
                    ->availableRewardTickets()
                    ->lockForUpdate()
                    ->oldest()
                    ->first();

                if (
                    !$rewardTicket &&
                    $customer->availableLoyaltyMinutes() >= 240
                ) {
                    $rewardTicket =
                        $this->generateRewardTicket($customer);
                }

                if (!$rewardTicket) {
                    throw new \Exception(
                        'El cliente no tiene una hora gratis disponible.'
                    );
                }

                $freeMinutes = min(60, $durationMinutes);
            }

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

            $paymentMethod =
                $total <= 0
                    ? 'cortesia'
                    : $validated['payment_method'];

            $reservation = Reservation::create([
    'customer_id' => $customer->id,
    'station_id' => $station->id,
    'user_id' => auth()->id(),
    'people_count' => $peopleCount,
    'started_at' => $startedAt,
    'ended_at' => $endedAt,
    'duration_minutes' => $durationMinutes,
    'paid_minutes' => $paidMinutes,
    'free_minutes' => $freeMinutes,
    'hourly_rate' => $hourlyRate,
    'subtotal' => $subtotal,
    'discount' => $discount,
    'total' => $total,
    'payment_method' => $paymentMethod,
    'status' => 'activa',
    'reward_ticket_id' =>
        $rewardTicket ? $rewardTicket->id : null,
]);

            if ($rewardTicket) {
                $rewardTicket->update([
                    'status' => 'usado',
                    'used_at' => now(),
                    'reservation_id' => $reservation->id,
                ]);
            }

            if ($paidMinutes > 0) {
                LoyaltyMovement::create([
                    'customer_id' => $customer->id,
                    'reservation_id' => $reservation->id,
                    'type' => 'earned',
                    'minutes' => $paidMinutes,
                    'description' =>
                        'Horas pagadas en ' . $station->name,
                ]);
            }

            $this->generateAvailableRewardTickets($customer);

            $station->update([
                'status' => 'ocupado',
            ]);
        });

        return redirect()
            ->route('reservations.index')
            ->with(
                'success',
                'La reserva se registró correctamente.'
            );
    } catch (\Exception $exception) {
        return back()
            ->withInput()
            ->with(
                'error',
                $exception->getMessage()
            );
    }
}
    public function finish(Reservation $reservation)
    {
        try {
            DB::transaction(function () use ($reservation) {
                $reservation = Reservation::query()
                    ->lockForUpdate()
                    ->findOrFail($reservation->id);

                if ($reservation->status !== 'activa') {
                    throw new \Exception(
                        'La reserva ya no está activa.'
                    );
                }

                $reservation->update([
                    'status' => 'finalizada',
                ]);

                $reservation->station()->update([
                    'status' => 'disponible',
                ]);
            });

            return redirect()
                ->route('reservations.index')
                ->with(
                    'success',
                    'La estación fue liberada correctamente.'
                );
        } catch (\Exception $exception) {
            return back()
                ->with(
                    'error',
                    $exception->getMessage()
                );
        }
    }

    private function generateAvailableRewardTickets(Customer $customer)
    {
        while ($customer->availableLoyaltyMinutes() >= 240) {
            $this->generateRewardTicket($customer);
        }
    }

    private function generateRewardTicket(Customer $customer)
    {
        LoyaltyMovement::create([
            'customer_id' => $customer->id,
            'reservation_id' => null,
            'type' => 'redeemed',
            'minutes' => -240,
            'description' =>
                'Canje automático: 4 horas acumuladas por 1 hora gratis.',
        ]);

        return RewardTicket::create([
            'customer_id' => $customer->id,
            'code' => $this->generateUniqueTicketCode(),
            'free_minutes' => 60,
            'status' => 'disponible',
            'generated_at' => now(),
        ]);
    }

    private function generateUniqueTicketCode()
    {
        do {
            $code = 'PP-' .
                now()->format('ymd') .
                '-' .
                strtoupper(Str::random(6));
        } while (
            RewardTicket::where('code', $code)->exists()
        );

        return $code;
    }
}