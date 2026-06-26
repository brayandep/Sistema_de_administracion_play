@extends('layouts.admin')

@section('title', 'Editar producto')

@section('page-title', 'Editar producto')

@section(
    'page-description',
    'Modifica datos del producto sin alterar la cantidad'
)

@section('content')

<div class="page-header">
    <div>
        <h1>Editar producto</h1>

        <p>
            Puedes modificar nombre, precio, tipo, estado e imagen.
            La cantidad no se edita desde aquí.
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
                Algunos campos están incompletos o incorrectos.
            </p>
        </div>
    </div>
@endif

<section class="card">

    <div class="card__header">
        <h2>Datos del producto</h2>

        <p>
            Stock actual:
            <strong>{{ $product->quantity }}</strong>
        </p>
    </div>

    <div class="card__body">

        <form
            method="POST"
            action="{{ route('products.update', $product) }}"
            enctype="multipart/form-data"
        >
            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="form-group">
                    <label
                        for="name"
                        class="form-label"
                    >
                        Nombre del producto
                        <span>*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $product->name) }}"
                        class="form-control"
                        required
                    >
                </div>

                <div class="form-group">
                    <label
                        for="product_type"
                        class="form-label"
                    >
                        Tipo de producto
                        <span>*</span>
                    </label>

                    <select
                        id="product_type"
                        name="product_type"
                        class="form-control"
                        required
                    >
                        <option
                            value="snack"
                            {{ old('product_type', $product->product_type) === 'snack'
                                ? 'selected'
                                : '' }}
                        >
                            Snack
                        </option>

                        <option
                            value="regalo"
                            {{ old('product_type', $product->product_type) === 'regalo'
                                ? 'selected'
                                : '' }}
                        >
                            Regalo
                        </option>
                    </select>
                </div>

                <div class="form-group">
                    <label
                        for="price"
                        class="form-label"
                    >
                        Precio
                        <span>*</span>
                    </label>

                    <input
                        type="number"
                        id="price"
                        name="price"
                        value="{{ old('price', $product->price) }}"
                        class="form-control"
                        min="0"
                        step="0.01"
                        required
                    >
                </div>

                <div class="form-group">
    <label
        for="quantity"
        class="form-label"
    >
        Cantidad actual
        <span>*</span>
    </label>

    @if (auth()->user()->role === 'super_admin')
        <input
            type="number"
            id="quantity"
            name="quantity"
            value="{{ old('quantity', $product->quantity) }}"
            class="form-control"
            min="0"
            step="1"
            required
        >

        <div class="form-help">
            Como super administrador puedes corregir la cantidad manualmente.
            Este cambio quedará registrado como ajuste de stock.
        </div>
    @else
        <input
            type="text"
            value="{{ $product->quantity }}"
            class="form-control"
            disabled
        >

        <div class="form-help">
            La cantidad no se puede editar aquí.
            Usa el botón Add para añadir stock.
        </div>
    @endif

    @error('quantity')
        <div class="form-error">
            {{ $message }}
        </div>
    @enderror
</div>

                <div class="form-group form-group--full">
                    <label
                        for="description"
                        class="form-label"
                    >
                        Descripción
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        class="form-control"
                    >{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Estado
                    </label>

                    <label class="checkbox-card">
                        <input
                            type="checkbox"
                            name="available"
                            value="1"
                            {{ old('available', $product->available)
                                ? 'checked'
                                : '' }}
                        >

                        <span>
                            <strong>Producto disponible</strong>
                            <span>Se podrá vender desde el sistema.</span>
                        </span>
                    </label>
                </div>

                <div class="form-group">
                    <label
                        for="image"
                        class="form-label"
                    >
                        Nueva fotografía
                    </label>

                    <input
                        type="file"
                        id="image"
                        name="image"
                        class="form-control"
                        accept=".jpg,.jpeg,.png,.webp"
                    >

                    @if ($product->image)
                        <div style="margin-top: 12px;">
                            <img
                                src="{{ asset('storage/' . $product->image) }}"
                                alt="{{ $product->name }}"
                                style="
                                    width: 90px;
                                    height: 90px;
                                    object-fit: cover;
                                    border-radius: 10px;
                                "
                            >
                        </div>
                    @endif
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
                    Guardar cambios
                </button>

            </div>

        </form>

    </div>

</section>

@endsection