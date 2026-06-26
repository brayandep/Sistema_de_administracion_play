<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductStockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Mostrar el listado de productos.
     */
    public function index(Request $request)
{
    $search = $request->get('search');
    $type = $request->get('type', 'todos');

    $products = Product::query()
        ->when($search, function ($query) use ($search) {
            $query->where(function ($query) use ($search) {
                $query
                    ->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        })
        ->when($type !== 'todos', function ($query) use ($type) {
            $query->where('product_type', $type);
        })
        ->orderBy('name')
        ->paginate(10)
        ->withQueryString();

    return view(
        'products.index',
        compact(
            'products',
            'search',
            'type'
        )
    );
}

    /**
     * Mostrar el formulario de registro.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Registrar un producto.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'name' => [
                    'required',
                    'string',
                    'max:120',
                ],

                'quantity' => [
                    'required',
                    'integer',
                    'min:0',
                ],

                'description' => [
                    'nullable',
                    'string',
                    'max:1000',
                ],

                'price' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:99999999.99',
                ],

                'product_type' => [
                    'required',
                    'in:snack,regalo',
                ],

                'image' => [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:2048',
                ],
            ],
            [
                'name.required' =>
                    'El nombre del producto es obligatorio.',

                'quantity.required' =>
                    'La cantidad es obligatoria.',

                'quantity.integer' =>
                    'La cantidad debe ser un número entero.',

                'quantity.min' =>
                    'La cantidad no puede ser negativa.',

                'description.max' =>
                    'La descripción no puede superar los 1000 caracteres.',

                'price.required' =>
                    'El precio es obligatorio.',

                'price.numeric' =>
                    'El precio debe ser un valor numérico.',

                'price.min' =>
                    'El precio no puede ser negativo.',

                'product_type.required' =>
                    'Selecciona el tipo de producto.',

                'product_type.in' =>
                    'El tipo de producto seleccionado no es válido.',

                'image.image' =>
                    'El archivo seleccionado debe ser una imagen.',

                'image.mimes' =>
                    'La fotografía debe ser JPG, JPEG, PNG o WEBP.',

                'image.max' =>
                    'La fotografía no puede superar los 2 MB.',
            ]
        );

        $validated['available'] =
            $request->boolean('available');

        if ($request->hasFile('image')) {
            $validated['image'] = $request
                ->file('image')
                ->store('products', 'public');
        }

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with(
                'success',
                'El producto se registró correctamente.'
            );
    }
    public function edit(Product $product)
{
    return view('products.edit', compact('product'));
}
public function update(Request $request, Product $product)
{
    $rules = [
        'name' => [
            'required',
            'string',
            'max:120',
        ],

        'description' => [
            'nullable',
            'string',
            'max:1000',
        ],

        'price' => [
            'required',
            'numeric',
            'min:0',
            'max:99999999.99',
        ],

        'product_type' => [
            'required',
            'in:snack,regalo',
        ],

        'available' => [
            'nullable',
            'boolean',
        ],

        'image' => [
            'nullable',
            'image',
            'mimes:jpg,jpeg,png,webp',
            'max:2048',
        ],
    ];

    if (auth()->user()->role === 'super_admin') {
        $rules['quantity'] = [
            'required',
            'integer',
            'min:0',
            'max:100000',
        ];
    }

    $validated = $request->validate(
        $rules,
        [
            'name.required' => 'El nombre del producto es obligatorio.',
            'price.required' => 'El precio es obligatorio.',
            'product_type.required' => 'Selecciona el tipo de producto.',
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.min' => 'La cantidad no puede ser negativa.',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.max' => 'La imagen no puede superar los 2 MB.',
        ]
    );

    $validated['available'] =
        $request->boolean('available');

    $previousQuantity =
        (int) $product->quantity;

    $newQuantity =
        $previousQuantity;

    if (auth()->user()->role === 'super_admin') {
        $newQuantity =
            (int) $validated['quantity'];
    }

    if ($request->hasFile('image')) {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $validated['image'] = $request
            ->file('image')
            ->store('products', 'public');
    }

    DB::transaction(function () use (
        $product,
        $validated,
        $previousQuantity,
        $newQuantity
    ) {
        $product = Product::query()
            ->lockForUpdate()
            ->findOrFail($product->id);

        $dataToUpdate = $validated;

        if (auth()->user()->role !== 'super_admin') {
            unset($dataToUpdate['quantity']);
        }

        $product->update($dataToUpdate);

        if (
            auth()->user()->role === 'super_admin' &&
            $previousQuantity !== $newQuantity
        ) {
            ProductStockMovement::create([
                'product_id' => $product->id,
                'user_id' => auth()->id(),
                'type' => 'ajuste',
                'quantity' => abs($newQuantity - $previousQuantity),
                'previous_quantity' => $previousQuantity,
                'new_quantity' => $newQuantity,
                'description' => 'Ajuste manual de stock desde edición de producto.',
            ]);
        }
    });

    return redirect()
        ->route('products.index')
        ->with('success', 'El producto se actualizó correctamente.');
}

public function addStockForm(Product $product)
{
    return view('products.add-stock', compact('product'));
}

public function addStock(Request $request, Product $product)
{
    $validated = $request->validate(
        [
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:100000',
            ],

            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
        ],
        [
            'quantity.required' => 'Ingresa la cantidad a añadir.',
            'quantity.min' => 'La cantidad debe ser mayor a cero.',
        ]
    );

    DB::transaction(function () use ($validated, $product) {
        $product = Product::query()
            ->lockForUpdate()
            ->findOrFail($product->id);

        $previousQuantity = $product->quantity;
        $quantityToAdd = (int) $validated['quantity'];
        $newQuantity = $previousQuantity + $quantityToAdd;

        $product->update([
            'quantity' => $newQuantity,
        ]);

        ProductStockMovement::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'type' => 'entrada',
            'quantity' => $quantityToAdd,
            'previous_quantity' => $previousQuantity,
            'new_quantity' => $newQuantity,
            'description' => $validated['description'] ?? null,
        ]);
    });

    return redirect()
        ->route('products.index')
        ->with('success', 'La cantidad del producto se actualizó correctamente.');
}
}