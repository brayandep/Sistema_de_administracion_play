@extends('layouts.admin')

@section('title', 'Reservas')

@section('page-title', 'Reservas')

@section(
    'page-description',
    'Control de salas y sesiones registradas'
)

@section('content')

<div class="page-header">
    <div>
        <h1>Reservas de estaciones</h1>
<p>
    Registra sesiones de juego, controla salas ocupadas
    y calcula el total automáticamente.
</p>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert--error">
        <span>!</span>

        <div>
            <strong>Revisa los datos ingresados</strong>

            <p>
                Algunos campos están incompletos o incorrectos.
            </p>
        </div>
    </div>
@endif<div class="stations-overview">

    <section class="card">

        <div class="card__header">
            <h2>Estado de salas</h2>

            <p>
                Vista rápida de todas las salas disponibles y ocupadas.
            </p>
        </div>

        <div class="card__body">

            <div class="station-grid station-grid--compact">

                @foreach ($stations as $station)

                    @php
                        $activeReservation = $station->activeReservation;
                    @endphp

                    <article
                        class="station-card-compact
                            station-card-compact--{{ $station->status }}"
                    >
                        <div class="station-card-compact__header">
                            <div>
                                <strong>
                                    {{ $station->name }}
                                </strong>

                                <span>
                                    Bs {{ number_format($station->hourly_rate, 2) }}/h
                                </span>
                            </div>

                            <span class="station-card-compact__status">
                                {{ ucfirst($station->status) }}
                            </span>
                        </div>

                        @if ($activeReservation)

                            <div class="station-card-compact__body">

                                <p>
                                    <strong>Cliente:</strong>
                                    {{ \Illuminate\Support\Str::limit(
                                        $activeReservation->customer->full_name,
                                        24
                                    ) }}
                                </p>

                                <div class="station-time-row">
                                    <span>
                                        Inicio
                                        <strong>
                                            {{ $activeReservation->started_at->format('H:i') }}
                                        </strong>
                                    </span>

                                    <span>
                                        Salida
                                        <strong>
                                            {{ $activeReservation->ended_at->format('H:i') }}
                                        </strong>
                                    </span>
                                </div>

                                <p>
                                    <strong>Personas:</strong>
                                    {{ $activeReservation->people_count }}
                                </p>

                                <p>
                                    <strong>Total:</strong>
                                    Bs {{ number_format($activeReservation->total, 2) }}
                                </p>

                            </div>

                            @if (auth()->user()->role === 'super_admin')
                                <a
                                    href="{{ route('reservations.edit', ['reservation' => $activeReservation->id]) }}"
                                    class="action-button action-button--edit"
                                    style="width: 100%; margin-bottom: 8px;"
                                >
                                    Editar reserva
                                </a>
                            @endif

                            <form
                                method="POST"
                                action="{{ route(
                                    'reservations.finish',
                                    $activeReservation
                                ) }}"
                            >
                                @csrf

                                <button
                                    type="submit"
                                    class="finish-button finish-button--compact"
                                >
                                    Liberar
                                </button>
                            </form>

                        @else

                            <div class="station-card-compact__body station-card-compact__body--free">
                                <span>
                                    Sala libre para reservar.
                                </span>
                            </div>

                        @endif

                    </article>

                @endforeach

            </div>

        </div>

    </section>

</div>
<div class="reservation-layout reservation-layout--single">

    <section class="card">

        <div class="card__header">
            <h2>Nueva reserva</h2>

            <p>
                Selecciona sala, cantidad de personas, horario y método de pago.
            </p>
        </div>

        <div class="card__body">

            <form
                method="POST"
                action="{{ route('reservations.store') }}"
                id="reservationForm"
            >
                @csrf

                <input
                    type="hidden"
                    name="customer_id"
                    id="customerId"
                    value="{{ $anonymousCustomer->id }}"
                >

                <div class="form-grid">

                    <div class="form-group">
                        <label
                            for="station_id"
                            class="form-label"
                        >
                            Sala
                            <span>*</span>
                        </label>

                        <select
                            id="station_id"
                            name="station_id"
                            class="form-control"
                            required
                        >
                            <option value="">
                                Selecciona una sala
                            </option>

                            @foreach ($stations as $station)
                                <option
                                    value="{{ $station->id }}"
                                    data-rate="{{ $station->hourly_rate }}"
                                    data-status="{{ $station->status }}"
                                    {{ old('station_id') == $station->id
                                        ? 'selected'
                                        : '' }}
                                    {{ $station->status !== 'disponible'
                                        ? 'disabled'
                                        : '' }}
                                >
                                    {{ $station->name }}
                                    -
                                    Bs {{ number_format($station->hourly_rate, 2) }}/h

                                    @if ($station->status !== 'disponible')
                                        -
                                        {{ ucfirst($station->status) }}
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        @error('station_id')
                            <div class="form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label
                            for="people_count"
                            class="form-label"
                        >
                            Número de personas
                            <span>*</span>
                        </label>

                        <select
                            id="people_count"
                            name="people_count"
                            class="form-control"
                            required
                        >
                            <option
                                value="1"
                                {{ old('people_count') == 1 ? 'selected' : '' }}
                            >
                                1 persona - Bs 10/h
                            </option>

                            <option
                                value="2"
                                {{ old('people_count', 2) == 2 ? 'selected' : '' }}
                            >
                                2 personas - Bs 20/h
                            </option>

                            <option
                                value="3"
                                {{ old('people_count') == 3 ? 'selected' : '' }}
                            >
                                3 personas - Bs 20/h
                            </option>

                            <option
                                value="4"
                                {{ old('people_count') == 4 ? 'selected' : '' }}
                            >
                                4 personas - Bs 20/h
                            </option>

                            <option
                                value="5"
                                {{ old('people_count') == 5 ? 'selected' : '' }}
                            >
                                5 personas - Bs 20/h
                            </option>

                            <option
                                value="6"
                                {{ old('people_count') == 6 ? 'selected' : '' }}
                            >
                                6 personas - Bs 20/h
                            </option>
                        </select>

                        @error('people_count')
                            <div class="form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label
                            for="started_at"
                            class="form-label"
                        >
                            Hora de inicio
                            <span>*</span>
                        </label>

                        <input
                            type="datetime-local"
                            id="started_at"
                            name="started_at"
                            value="{{ old(
                                'started_at',
                                now()->format('Y-m-d\TH:i')
                            ) }}"
                            class="form-control"
                            required
                        >

                        @error('started_at')
                            <div class="form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label
                            for="ended_at"
                            class="form-label"
                        >
                            Hora de salida
                            <span>*</span>
                        </label>

                        <input
                            type="datetime-local"
                            id="ended_at"
                            name="ended_at"
                            value="{{ old(
                                'ended_at',
                                now()->addHour()->format('Y-m-d\TH:i')
                            ) }}"
                            class="form-control"
                            required
                        >

                        @error('ended_at')
                            <div class="form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group form-group--full">
                        <label class="form-label">
                            Accesos rápidos de tiempo
                        </label>

                        <div class="quick-time-buttons">
                            <button type="button" data-minutes="30">
                                30 min
                            </button>

                            <button type="button" data-minutes="60">
                                1 hora
                            </button>

                            <button type="button" data-minutes="90">
                                1 h 30 min
                            </button>

                            <button type="button" data-minutes="120">
                                2 horas
                            </button>

                            <button type="button" data-minutes="150">
                                2 h 30 min
                            </button>

                            <button type="button" data-minutes="180">
                                3 horas
                            </button>

                            <button type="button" data-minutes="240">
                                4 horas
                            </button>

                            <button type="button" data-minutes="300">
                                5 horas
                            </button>

                            <button type="button" data-minutes="360">
                                6 horas
                            </button>
                        </div>

                        <div class="duration-preview">
                            Duración calculada:
                            <strong id="durationPreview">
                                1 hora
                            </strong>
                        </div>
                    </div>

                    <div class="form-group">
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

                            <option value="mixto">
                                Mixto
                            </option>

                            <option value="cortesia">
                                Cortesía
                            </option>
                        </select>

                        @error('payment_method')
                            <div class="form-error">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group form-group--full">
                        <div class="payment-summary">

                            <div>
                                <span>Subtotal</span>
                                <strong id="subtotalPreview">
                                    Bs 20.00
                                </strong>
                            </div>

                            <div>
                                <span>Descuento</span>
                                <strong id="discountPreview">
                                    Bs 0.00
                                </strong>
                            </div>

                            <div class="payment-summary__total">
                                <span>Total a pagar</span>
                                <strong id="totalPreview">
                                    Bs 20.00
                                </strong>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="form-actions">
                    <button
                        type="submit"
                        class="primary-button"
                    >
                        Registrar reserva
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
        <h2>Últimas reservas</h2>

        <p>
            Historial reciente de sesiones registradas.
        </p>
    </div>

    @if ($recentReservations->count() > 0)

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Sala</th>
                        <th>Personas</th>
                        <th>Horario</th>
                        <th>Duración</th>
                        <th>Total</th>
                        <th>Estado</th>

                        @if (auth()->user()->role === 'super_admin')
                            <th>Acciones</th>
                        @endif
                    </tr>
                </thead>

                <tbody>
                    @foreach ($recentReservations as $reservation)
                    <tr>
    <td>
        {{ $reservation->station->name }}
    </td>

    <td>
        {{ $reservation->people_count }}
    </td>

    <td>
        {{ $reservation->started_at->format('d/m/Y H:i') }}
        -
        {{ $reservation->ended_at->format('H:i') }}
    </td>

    <td>
        {{ floor($reservation->duration_minutes / 60) }}
        h
        {{ $reservation->duration_minutes % 60 }}
        min
    </td>

    <td>
        Bs {{ number_format($reservation->total, 2) }}
    </td>

    <td>
        {{ ucfirst($reservation->status) }}
    </td>

    @if (auth()->user()->role === 'super_admin')
        <td>
            <a
                href="{{ route('reservations.edit', ['reservation' => $reservation->id]) }}"
                class="action-button action-button--edit"
            >
                Editar
            </a>
        </td>
    @endif
</tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @else

        <div class="empty-state">
            <div class="empty-state__icon">
                🎮
            </div>

            <h3>
                Todavía no existen reservas
            </h3>

            <p>
                Registra la primera sesión de PS5 o billar.
            </p>
        </div>

    @endif

</section>

@endsection
@push('scripts')
<script>
    const stationSelect =
        document.getElementById('station_id');

    const peopleCountSelect =
        document.getElementById('people_count');

    const startedAtInput =
        document.getElementById('started_at');

    const endedAtInput =
        document.getElementById('ended_at');

    const durationPreview =
        document.getElementById('durationPreview');

    const quickTimeButtons =
        document.querySelectorAll('.quick-time-buttons button');

    const subtotalPreview =
        document.getElementById('subtotalPreview');

    const discountPreview =
        document.getElementById('discountPreview');

    const totalPreview =
        document.getElementById('totalPreview');

    function money(value) {
        return 'Bs ' + Number(value).toFixed(2);
    }

    function getSelectedRate() {
        const peopleCount =
            parseInt(peopleCountSelect.value || 2, 10);

        if (peopleCount === 1) {
            return 10;
        }

        const option =
            stationSelect.options[stationSelect.selectedIndex];

        if (!option || !option.dataset.rate) {
            return 20;
        }

        return parseFloat(option.dataset.rate);
    }

    function formatDatetimeLocal(date) {
        const year = date.getFullYear();

        const month = String(date.getMonth() + 1)
            .padStart(2, '0');

        const day = String(date.getDate())
            .padStart(2, '0');

        const hours = String(date.getHours())
            .padStart(2, '0');

        const minutes = String(date.getMinutes())
            .padStart(2, '0');

        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    function getDurationMinutes() {
        if (!startedAtInput.value || !endedAtInput.value) {
            return 0;
        }

        const startedAt =
            new Date(startedAtInput.value);

        const endedAt =
            new Date(endedAtInput.value);

        const diffMs =
            endedAt.getTime() - startedAt.getTime();

        return Math.max(
            Math.round(diffMs / 60000),
            0
        );
    }

    function formatDuration(minutes) {
        if (minutes <= 0) {
            return 'Hora inválida';
        }

        const hours =
            Math.floor(minutes / 60);

        const remainingMinutes =
            minutes % 60;

        if (hours === 0) {
            return `${remainingMinutes} min`;
        }

        if (remainingMinutes === 0) {
            return hours === 1
                ? '1 hora'
                : `${hours} horas`;
        }

        return `${hours} h ${remainingMinutes} min`;
    }

    function updateDurationPreview() {
        const duration =
            getDurationMinutes();

        durationPreview.textContent =
            formatDuration(duration);
    }

    function updateTotals() {
        const rate =
            getSelectedRate();

        const duration =
            getDurationMinutes();

        if (duration <= 0) {
            subtotalPreview.textContent = money(0);
            discountPreview.textContent = money(0);
            totalPreview.textContent = money(0);
            return;
        }

        const subtotal =
            (duration / 60) * rate;

        const discount = 0;

        const total =
            subtotal - discount;

        subtotalPreview.textContent =
            money(subtotal);

        discountPreview.textContent =
            money(discount);

        totalPreview.textContent =
            money(total);
    }

    function refreshReservationPreview() {
        updateDurationPreview();
        updateTotals();
    }

    function setQuickDuration(minutes) {
        if (!startedAtInput.value) {
            startedAtInput.value =
                formatDatetimeLocal(new Date());
        }

        const startedAt =
            new Date(startedAtInput.value);

        const endedAt =
            new Date(startedAt.getTime());

        endedAt.setMinutes(
            endedAt.getMinutes() + minutes
        );

        endedAtInput.value =
            formatDatetimeLocal(endedAt);

        refreshReservationPreview();
    }

    quickTimeButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const minutes =
                parseInt(button.dataset.minutes, 10);

            setQuickDuration(minutes);
        });
    });

    stationSelect.addEventListener(
        'change',
        refreshReservationPreview
    );

    peopleCountSelect.addEventListener(
        'change',
        refreshReservationPreview
    );

    startedAtInput.addEventListener(
        'input',
        refreshReservationPreview
    );

    startedAtInput.addEventListener(
        'change',
        refreshReservationPreview
    );

    endedAtInput.addEventListener(
        'input',
        refreshReservationPreview
    );

    endedAtInput.addEventListener(
        'change',
        refreshReservationPreview
    );

    refreshReservationPreview();
</script>
@endpush