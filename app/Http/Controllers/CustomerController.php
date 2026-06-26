<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'q' => [
                'nullable',
                'string',
                'max:120',
            ],
        ]);

        $query = trim($request->get('q', ''));

        $customers = Customer::query()
            ->where('active', true)
            ->when($query !== '', function ($builder) use ($query) {
                $builder->where(function ($subQuery) use ($query) {
                    $subQuery
                        ->where('full_name', 'like', '%' . $query . '%')
                        ->orWhere('phone', 'like', '%' . $query . '%');
                });
            })
            ->latest()
            ->limit(8)
            ->get()
            ->map(function ($customer) {
                return $this->customerPayload($customer);
            });

        return response()->json($customers);
    }

    public function quickStore(Request $request)
    {
        $validated = $request->validate(
            [
                'full_name' => [
                    'required',
                    'string',
                    'max:120',
                ],
                'phone' => [
                    'nullable',
                    'string',
                    'max:30',
                ],
            ],
            [
                'full_name.required' =>
                    'El nombre del cliente es obligatorio.',
            ]
        );

        $customer = Customer::create([
            'full_name' => $validated['full_name'],
            'phone' => $validated['phone'] ?? null,
            'active' => true,
        ]);

        return response()->json(
            $this->customerPayload($customer),
            201
        );
    }

    private function customerPayload(Customer $customer)
    {
        $availableMinutes =
            $customer->availableLoyaltyMinutes();

        $ticketsCount =
            $customer->availableRewardTickets()->count();

        return [
            'id' => $customer->id,
            'full_name' => $customer->full_name,
            'phone' => $customer->phone,
            'available_minutes' => $availableMinutes,
            'available_hours' => round($availableMinutes / 60, 2),
            'progress_minutes' => $availableMinutes % 240,
            'progress_hours' => round(($availableMinutes % 240) / 60, 2),
            'available_tickets_count' => $ticketsCount,
            'can_use_reward' =>
                $ticketsCount > 0 || $availableMinutes >= 240,
        ];
    }
}