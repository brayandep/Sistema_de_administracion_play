@extends('layouts.admin')

@section('title', 'Añadir stock')

@section('page-title', 'Añadir stock')

@section(
    'page-description',
    'Agrega nueva cantidad al producto seleccionado'
)

@section('content')

<div class="page-header">
    <div>
        <h1>Añadir stock</h1>

        <p>
            Esta acción suma cantidad al inventario sin permitir
            editar manualmente el stock total.
        </p>
    </div>

    <a
        href="{{ route('products.index') }}"
        class="secondary-button"
    >
        Volver
    </a>
</div>

@if ($errors->any())
    <div class="alert alert--error">
        <span>!</span>

        <div>
            <strong>Revisa la información</strong>

            <p>
                Ingresa una cantidad válida.
            </p>
        </div>
    </div>
@endif

<section class="card">

    <div class="card__header">
        <h2>{{ $product->name }}</h2>

        <p>
            Stock actual:
            <strong>{{ $product->quantity }}</strong>
        </p>
    </div>

    <div class="card__body">

        <form
            method="POST"
            action="{{ route('products.addStock', $product) }}"
        >
            @csrf
            @method('PATCH')

            <div class="form-grid">

                <div class="form-group">
                    <label
                        for="quantity"
                        class="form-label"
                    >
                        Cantidad a añadir
                        <span>*</span>
                    </label>

                    <input
                        type="number"
                        id="quantity"
                        name="quantity"
                        value="{{ old('quantity', 1) }}"
                        class="form-control"
                        min="1"
                        step="1"
                        required
                        autofocus
                    >

                    @error('quantity')
                        <div class="form-error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Nuevo stock estimado
                    </label>

                    <input
                        type="text"
                        id="newStockPreview"
                        class="form-control"
                        value="{{ $product->quantity + 1 }}"
                        disabled
                    >
                </div>

                <div class="form-group form-group--full">
                    <label
                        for="description"
                        class="form-label"
                    >
                        Observación
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        class="form-control"
                        placeholder="Ejemplo: Compra de proveedor, reposición de stock..."
                    >{{ old('description') }}</textarea>
                </div>

            </div>

            <div class="form-actions">

                <a
                    href="{{ route('products.index') }}"
                    class="secondary-button"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="primary-button"
                >
                    Añadir cantidad
                </button>

            </div>

        </form>

    </div>

</section>

@endsection

@push('scripts')
<script>
    const quantityInput =
        document.getElementById('quantity');

    const newStockPreview =
        document.getElementById('newStockPreview');

    const currentStock =
        {{ $product->quantity }};

    function updatePreview() {
        const quantity =
            parseInt(quantityInput.value || 0, 10);

        newStockPreview.value =
            currentStock + quantity;
    }

    quantityInput.addEventListener(
        'input',
        updatePreview
    );

    updatePreview();
</script>
@endpush