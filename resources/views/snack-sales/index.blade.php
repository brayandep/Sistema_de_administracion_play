@extends('layouts.admin')

@section('title', 'Venta de snacks')

@section('page-title', 'Venta de snacks')

@section(
    'page-description',
    'Agrega varios productos, calcula el total y registra el pago'
)

@section('content')

<div class="page-header">
    <div>
        <h1>Venta de snacks</h1>

        <p>
            Agrega todos los productos que el cliente va a consumir
            y cobra el total en efectivo o QR.
        </p>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert--error">
        <span>!</span>

        <div>
            <strong>Revisa la venta</strong>

            <p>
                Faltan datos o existe un producto inválido.
            </p>
        </div>
    </div>
@endif

<div class="snack-sale-layout">

    <section class="card">

        <div class="card__header">
            <h2>Productos disponibles</h2>

            <p>
                Selecciona los productos que pidió el cliente.
            </p>
        </div>

        <div class="card__body">

            <div class="snack-search">
                <input
                    type="text"
                    id="productSearch"
                    class="form-control"
                    placeholder="Buscar producto..."
                >
            </div>

            <div class="snack-product-grid" id="productGrid">

                @foreach ($products as $product)

                    <button
                        type="button"
                        class="snack-product-card"
                        data-product-id="{{ $product->id }}"
                        data-product-name="{{ $product->name }}"
                        data-product-price="{{ $product->price }}"
                        data-product-stock="{{ $product->quantity }}"
                    >
                        <div class="snack-product-card__image">
                            @if ($product->image)
                                <img
                                    src="{{ asset('storage/' . $product->image) }}"
                                    alt="{{ $product->name }}"
                                >
                            @else
                                🍿
                            @endif
                        </div>

                        <div class="snack-product-card__info">
                            <strong>
                                {{ $product->name }}
                            </strong>

                            <span>
                                Bs {{ number_format($product->price, 2) }}
                            </span>

                            <small>
                                Stock: {{ $product->quantity }}
                            </small>
                        </div>
                    </button>

                @endforeach

            </div>

        </div>

    </section>

    <section class="card">

        <div class="card__header">
            <h2>Detalle de venta</h2>

            <p>
                Revisa cantidades y total antes de cobrar.
            </p>
        </div>

        <div class="card__body">

            <form
                method="POST"
                action="{{ route('snack-sales.store') }}"
                id="snackSaleForm"
            >
                @csrf

                <div id="cartInputs"></div>

                <div class="cart-table-wrapper">

                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cant.</th>
                                <th>Precio</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody id="cartBody">
                            <tr id="emptyCartRow">
                                <td colspan="5">
                                    No hay productos agregados.
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>

                <div class="payment-method-box">
                    <label
                        for="payment_method"
                        class="form-label"
                    >
                        Método de pago
                        <span>*</span>
                    </label>

                    <select
                        id="payment_method"
                        name="payment_method"
                        class="form-control"
                        required
                    >
                        <option value="efectivo">
                            Efectivo
                        </option>

                        <option value="qr">
                            QR
                        </option>
                    </select>
                </div>

                <div class="snack-total-box">

                    <span>Total a cobrar</span>

                    <strong id="cartTotal">
                        Bs 0.00
                    </strong>

                </div>

                <div class="form-actions">
                    <button
                        type="button"
                        class="secondary-button"
                        id="clearCartButton"
                    >
                        Vaciar venta
                    </button>

                    <button
                        type="submit"
                        class="primary-button"
                        id="submitSaleButton"
                        disabled
                    >
                        Registrar venta
                    </button>
                </div>

            </form>

        </div>

    </section>

</div>

<section
    class="card"
    style="margin-top: 25px;"
>
    <div class="card__header">
        <h2>Ventas recientes</h2>

        <p>
            Últimas ventas de snacks registradas.
        </p>
    </div>

    @if ($recentSales->count() > 0)

        <div class="table-container">

            <table class="data-table">

                <thead>
                    <tr>
                        <th>Nro</th>
                        <th>Código</th>
                        <th>Usuario</th>
                        <th>Productos</th>
                        <th>Pago</th>
                        <th>Total</th>
                        <th>Hora</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($recentSales as $sale)
                        <tr>
                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $sale->sale_number }}
                            </td>

                            <td>
                                {{ $sale->user->name ?? 'Sin usuario' }}
                            </td>

                            <td>
                                {{ $sale->items->sum('quantity') }}
                                producto(s)
                            </td>

                            <td>
                                {{ ucfirst($sale->payment_method) }}
                            </td>

                            <td>
                                <strong>
                                    Bs {{ number_format($sale->total, 2) }}
                                </strong>
                            </td>

                            <td>
                                {{ $sale->sold_at->format('d/m/Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

    @else

        <div class="empty-state">
            <div class="empty-state__icon">
                🍿
            </div>

            <h3>No hay ventas registradas</h3>

            <p>
                Cuando registres ventas de snacks aparecerán aquí.
            </p>
        </div>

    @endif

</section>

@endsection

@push('scripts')
<script>
    const productCards =
        document.querySelectorAll('.snack-product-card');

    const productSearch =
        document.getElementById('productSearch');

    const cartBody =
        document.getElementById('cartBody');

    const cartInputs =
        document.getElementById('cartInputs');

    const emptyCartRow =
        document.getElementById('emptyCartRow');

    const cartTotal =
        document.getElementById('cartTotal');

    const clearCartButton =
        document.getElementById('clearCartButton');

    const submitSaleButton =
        document.getElementById('submitSaleButton');

    let cart = [];

    function money(value) {
        return 'Bs ' + Number(value).toFixed(2);
    }

    function addProduct(product) {
        const existingItem =
            cart.find(item => item.id === product.id);

        if (existingItem) {
            if (existingItem.quantity + 1 > product.stock) {
                alert('No hay más stock disponible de este producto.');
                return;
            }

            existingItem.quantity++;
        } else {
            if (product.stock <= 0) {
                alert('Este producto no tiene stock.');
                return;
            }

            cart.push({
                id: product.id,
                name: product.name,
                price: product.price,
                stock: product.stock,
                quantity: 1,
            });
        }

        renderCart();
    }

    function updateQuantity(productId, quantity) {
        const item =
            cart.find(product => product.id === productId);

        if (!item) {
            return;
        }

        const newQuantity =
            parseInt(quantity, 10);

        if (newQuantity <= 0) {
            removeProduct(productId);
            return;
        }

        if (newQuantity > item.stock) {
            alert(
                'No puedes vender más de ' +
                item.stock +
                ' unidad(es) de este producto.'
            );

            item.quantity = item.stock;
        } else {
            item.quantity = newQuantity;
        }

        renderCart();
    }

    function removeProduct(productId) {
        cart =
            cart.filter(product => product.id !== productId);

        renderCart();
    }

    function clearCart() {
        cart = [];
        renderCart();
    }

    function calculateTotal() {
        return cart.reduce(function (total, item) {
            return total + (item.price * item.quantity);
        }, 0);
    }

    function renderCartInputs() {
        cartInputs.innerHTML = '';

        cart.forEach(function (item, index) {
            const productInput =
                document.createElement('input');

            productInput.type = 'hidden';
            productInput.name =
                `items[${index}][product_id]`;

            productInput.value = item.id;

            const quantityInput =
                document.createElement('input');

            quantityInput.type = 'hidden';
            quantityInput.name =
                `items[${index}][quantity]`;

            quantityInput.value = item.quantity;

            cartInputs.appendChild(productInput);
            cartInputs.appendChild(quantityInput);
        });
    }

    function renderCart() {
        cartBody.innerHTML = '';

        if (cart.length === 0) {
            cartBody.appendChild(emptyCartRow);
            cartTotal.textContent = money(0);
            submitSaleButton.disabled = true;
            renderCartInputs();
            return;
        }

        cart.forEach(function (item) {
            const row =
                document.createElement('tr');

            row.innerHTML = `
                <td>
                    <strong>${item.name}</strong>
                </td>

                <td>
                    <input
                        type="number"
                        min="1"
                        max="${item.stock}"
                        value="${item.quantity}"
                        class="cart-quantity-input"
                        data-product-id="${item.id}"
                    >
                </td>

                <td>
                    ${money(item.price)}
                </td>

                <td>
                    <strong>${money(item.price * item.quantity)}</strong>
                </td>

                <td>
                    <button
                        type="button"
                        class="cart-remove-button"
                        data-product-id="${item.id}"
                    >
                        ×
                    </button>
                </td>
            `;

            cartBody.appendChild(row);
        });

        cartTotal.textContent =
            money(calculateTotal());

        submitSaleButton.disabled = false;

        renderCartInputs();

        document
            .querySelectorAll('.cart-quantity-input')
            .forEach(function (input) {
                input.addEventListener('change', function () {
                    updateQuantity(
                        parseInt(input.dataset.productId, 10),
                        input.value
                    );
                });

                input.addEventListener('input', function () {
                    updateQuantity(
                        parseInt(input.dataset.productId, 10),
                        input.value
                    );
                });
            });

        document
            .querySelectorAll('.cart-remove-button')
            .forEach(function (button) {
                button.addEventListener('click', function () {
                    removeProduct(
                        parseInt(button.dataset.productId, 10)
                    );
                });
            });
    }

    productCards.forEach(function (card) {
        card.addEventListener('click', function () {
            addProduct({
                id: parseInt(card.dataset.productId, 10),
                name: card.dataset.productName,
                price: parseFloat(card.dataset.productPrice),
                stock: parseInt(card.dataset.productStock, 10),
            });
        });
    });

    productSearch.addEventListener('input', function () {
        const searchValue =
            productSearch.value
                .trim()
                .toLowerCase();

        productCards.forEach(function (card) {
            const productName =
                card.dataset.productName
                    .toLowerCase();

            card.style.display =
                productName.includes(searchValue)
                    ? 'flex'
                    : 'none';
        });
    });

    clearCartButton.addEventListener(
        'click',
        clearCart
    );

    renderCart();
</script>
@endpush