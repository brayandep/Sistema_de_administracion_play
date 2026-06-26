<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\SnackSale;
use App\Models\SnackSaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SnackSaleController extends Controller
{
    public function index()
    {
        $products = Product::query()
    ->whereIn('product_type', [
        'snack',
        'regalo',
    ])
    ->where('available', true)
    ->orderBy('product_type')
    ->orderBy('name')
    ->get();
        $recentSales = SnackSale::query()
            ->with([
                'items',
                'user',
            ])
            ->latest()
            ->limit(10)
            ->get();

        return view(
            'snack-sales.index',
            compact(
                'products',
                'recentSales'
            )
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'payment_method' => [
                    'required',
                    'in:efectivo,qr',
                ],

                'items' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'items.*.product_id' => [
                    'required',
                    'exists:products,id',
                ],

                'items.*.quantity' => [
                    'required',
                    'integer',
                    'min:1',
                ],
            ],
            [
                'payment_method.required' =>
                    'Selecciona el método de pago.',

                'items.required' =>
                    'Agrega al menos un producto a la venta.',

                'items.min' =>
                    'Agrega al menos un producto a la venta.',

                'items.*.product_id.required' =>
                    'Existe un producto inválido en la venta.',

                'items.*.quantity.required' =>
                    'Indica la cantidad de cada producto.',

                'items.*.quantity.min' =>
                    'La cantidad debe ser mayor a cero.',
            ]
        );

        try {
            DB::transaction(function () use ($validated) {
                $subtotal = 0;
                $preparedItems = [];

                foreach ($validated['items'] as $item) {
                    $product = Product::query()
                        ->lockForUpdate()
                        ->findOrFail($item['product_id']);

                    if (!in_array($product->product_type, ['snack', 'regalo'])) {
    throw new \Exception(
        'El producto ' . $product->name . ' no tiene un tipo válido.'
    );
}

                    if (!$product->available) {
                        throw new \Exception(
                            'El producto ' . $product->name . ' no está disponible.'
                        );
                    }

                    $quantity = (int) $item['quantity'];

                    if ($product->quantity < $quantity) {
                        throw new \Exception(
                            'Stock insuficiente para ' . $product->name .
                            '. Disponible: ' . $product->quantity
                        );
                    }

                    $unitPrice = (float) $product->price;
                    $lineTotal = round($unitPrice * $quantity, 2);

                    $subtotal += $lineTotal;

                    $preparedItems[] = [
                        'product' => $product,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total' => $lineTotal,
                    ];
                }

                $subtotal = round($subtotal, 2);
                $total = $subtotal;

                $sale = SnackSale::create([
                    'sale_number' => $this->generateSaleNumber(),
                    'user_id' => auth()->id(),
                    'payment_method' => $validated['payment_method'],
                    'subtotal' => $subtotal,
                    'total' => $total,
                    'status' => 'completada',
                    'sold_at' => now(),
                ]);

                foreach ($preparedItems as $preparedItem) {
                    SnackSaleItem::create([
    'snack_sale_id' => $sale->id,
    'product_id' => $preparedItem['product']->id,
    'product_name' => $preparedItem['product']->name,
    'product_type' => $preparedItem['product']->product_type,
    'quantity' => $preparedItem['quantity'],
    'unit_price' => $preparedItem['unit_price'],
    'total' => $preparedItem['total'],
]);

                    $preparedItem['product']->decrement(
                        'quantity',
                        $preparedItem['quantity']
                    );
                }
            });

            return redirect()
                ->route('snack-sales.index')
                ->with(
                    'success',
                    'La venta se registró correctamente.'
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

    private function generateSaleNumber()
    {
        do {
            $number =
                'SN-' .
                now()->format('Ymd') .
                '-' .
                str_pad(
                    (SnackSale::whereDate('created_at', today())->count() + 1),
                    4,
                    '0',
                    STR_PAD_LEFT
                );
        } while (
            SnackSale::where('sale_number', $number)->exists()
        );

        return $number;
    }
}