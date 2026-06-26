@extends('layouts.admin')

@section('title', 'Registrar producto')

@section('page-title', 'Registrar producto')

@section(
    'page-description',
    'Agrega productos de snacks o regalos al inventario'
)

@section('content')

<div class="page-header">
    <div>
        <h1>Nuevo producto</h1>

        <p>
            Registra la información, existencia y fotografía
            del producto.
        </p>
    </div>

    <a
        href="{{ route('products.index') }}"
        class="secondary-button"
    >
        Ver productos
    </a>
</div>

@if ($errors->any())
    <div class="alert alert--error">
        <span>!</span>

        <div>
            <strong>
                Revisa la información ingresada
            </strong>

            <p>
                Existen campos incompletos o incorrectos.
            </p>
        </div>
    </div>
@endif

<section class="card">

    <div class="card__header">
        <h2>Información del producto</h2>

        <p>
            Los campos marcados con * son obligatorios.
        </p>
    </div>

    <div class="card__body">

        <form
            method="POST"
            action="{{ route('products.store') }}"
            enctype="multipart/form-data"
        >
            @csrf

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
                        value="{{ old('name') }}"
                        class="form-control
                            @error('name')
                                form-control--error
                            @enderror"
                        placeholder="Ejemplo: Coca-Cola personal"
                        maxlength="120"
                        autofocus
                        required
                    >

                    @error('name')
                        <div class="form-error">
                            {{ $message }}
                        </div>
                    @enderror
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
                        class="form-control
                            @error('product_type')
                                form-control--error
                            @enderror"
                        required
                    >
                        <option value="">
                            Selecciona una opción
                        </option>

                        <option
                            value="snack"
                            {{ old('product_type') === 'snack'
                                ? 'selected'
                                : '' }}
                        >
                            Snack
                        </option>

                        <option
                            value="regalo"
                            {{ old('product_type') === 'regalo'
                                ? 'selected'
                                : '' }}
                        >
                            Regalo
                        </option>
                    </select>

                    @error('product_type')
                        <div class="form-error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label
                        for="quantity"
                        class="form-label"
                    >
                        Cantidad disponible
                        <span>*</span>
                    </label>

                    <input
                        type="number"
                        id="quantity"
                        name="quantity"
                        value="{{ old('quantity', 0) }}"
                        class="form-control
                            @error('quantity')
                                form-control--error
                            @enderror"
                        min="0"
                        step="1"
                        required
                    >

                    @error('quantity')
                        <div class="form-error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label
                        for="price"
                        class="form-label"
                    >
                        Precio de venta
                        <span>*</span>
                    </label>

                    <input
                        type="number"
                        id="price"
                        name="price"
                        value="{{ old('price') }}"
                        class="form-control
                            @error('price')
                                form-control--error
                            @enderror"
                        min="0"
                        step="0.01"
                        placeholder="0.00"
                        required
                    >

                    <div class="form-help">
                        Ingresa el precio en bolivianos.
                    </div>

                    @error('price')
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
                        class="form-control
                            @error('description')
                                form-control--error
                            @enderror"
                        maxlength="1000"
                        placeholder="Describe brevemente el producto..."
                    >{{ old('description') }}</textarea>

                    @error('description')
                        <div class="form-error">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">
                        Estado del producto
                    </label>

                    <label class="checkbox-card">
                        <input
                            type="checkbox"
                            name="available"
                            value="1"
                            {{ old('available', true)
                                ? 'checked'
                                : '' }}
                        >

                        <span>
                            <strong>
                                Producto disponible
                            </strong>

                            <span>
                                Se podrá mostrar y vender
                                desde el sistema.
                            </span>
                        </span>
                    </label>
                </div>

                <div class="form-group">
                    <label
                        for="image"
                        class="form-label"
                    >
                        Fotografía del producto
                    </label>

                    <div class="image-upload">

                        <div
                            class="image-preview"
                            id="imagePreview"
                        >
                            <span id="imagePlaceholder">
                                Sin fotografía
                            </span>

                            <img
                                id="previewImage"
                                src=""
                                alt="Vista previa"
                            >
                        </div>

                        <div>
                            <input
                                type="file"
                                id="image"
                                name="image"
                                class="form-control
                                    @error('image')
                                        form-control--error
                                    @enderror"
                                accept=".jpg,.jpeg,.png,.webp"
                            >

                            <div class="form-help">
                                Formatos permitidos:
                                JPG, JPEG, PNG y WEBP.
                                Máximo 2 MB.
                            </div>

                            @error('image')
                                <div class="form-error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>
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
                    Guardar producto
                </button>

            </div>

        </form>

    </div>

</section>

@endsection

@push('scripts')
<script>
    const imageInput =
        document.getElementById('image');

    const previewImage =
        document.getElementById('previewImage');

    const imagePlaceholder =
        document.getElementById('imagePlaceholder');

    imageInput.addEventListener('change', function (event) {
        const file = event.target.files[0];

        if (!file) {
            previewImage.src = '';
            previewImage.style.display = 'none';
            imagePlaceholder.style.display = 'block';
            return;
        }

        const reader = new FileReader();

        reader.onload = function (readerEvent) {
            previewImage.src = readerEvent.target.result;
            previewImage.style.display = 'block';
            imagePlaceholder.style.display = 'none';
        };

        reader.readAsDataURL(file);
    });
</script>
@endpush