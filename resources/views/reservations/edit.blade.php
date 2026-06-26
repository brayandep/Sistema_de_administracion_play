@extends('layouts.admin')

@section('title', 'Editar reserva')

@section('page-title', 'Editar reserva')

@section(
    'page-description',
    'Corrección exclusiva para super administrador'
)

@section('content')

<div class="page-header">
    <div>
        <h1>Editar reserva</h1>

        <p>
            Puedes corregir horario, personas y método de pago.
        </p>
    </div>

    <a
        href="{{ route('reservations.index') }}"
        class="secondary-button"
    >
        Volver
    </a>
</div>

<section class="card">

    <div class="card__header">
        <h2>
            {{ $reservation->station->name }}
        </h2>

       <p>
    Cliente:
    <strong>{{ $reservation->customer->full_name }}</strong>
    · Estado:
    <strong>{{ ucfirst($reservation->status) }}</strong>
</p>

    </div>

    <div class="card__body">

        <form
            method="POST"
            action="{{ route('reservations.update', $reservation) }}"
        >
            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="form-group">
                    <label
                        for="started_at"
                        class="form-label"
                    >
                        Hora de inicio
                    </label>

                    <input
                        type="datetime-local"
                        id="started_at"
                        name="started_at"
                        value="{{ old(
                            'started_at',
                            $reservation->started_at->format('Y-m-d\TH:i')
                        ) }}"
                        class="form-control"
                        required
                    >
                </div>

                <div class="form-group">
                    <label
                        for="ended_at"
                        class="form-label"
                    >
                        Hora de salida
                    </label>

                    <input
                        type="datetime-local"
                        id="ended_at"
                        name="ended_at"
                        value="{{ old(
                            'ended_at',
                            $reservation->ended_at->format('Y-m-d\TH:i')
                        ) }}"
                        class="form-control"
                        required
                    >
                </div>

                <div class="form-group">
                    <label
                        for="people_count"
                        class="form-label"
                    >
                        Número de personas
                    </label>

                    <input
                        type="number"
                        id="people_count"
                        name="people_count"
                        value="{{ old(
                            'people_count',
                            $reservation->people_count
                        ) }}"
                        class="form-control"
                        min="1"
                        max="20"
                        required
                    >
                </div>

                <div class="form-group">
                    <label
                        for="payment_method"
                        class="form-label"
                    >
                        Método de pago
                    </label>

                    <select
                        id="payment_method"
                        name="payment_method"
                        class="form-control"
                        required
                    >
                        <option
                            value="efectivo"
                            {{ old('payment_method', $reservation->payment_method) === 'efectivo'
                                ? 'selected'
                                : '' }}
                        >
                            Efectivo
                        </option>

                        <option
                            value="qr"
                            {{ old('payment_method', $reservation->payment_method) === 'qr'
                                ? 'selected'
                                : '' }}
                        >
                            QR
                        </option>

                        <option
                            value="mixto"
                            {{ old('payment_method', $reservation->payment_method) === 'mixto'
                                ? 'selected'
                                : '' }}
                        >
                            Mixto
                        </option>

                        <option
                            value="cortesia"
                            {{ old('payment_method', $reservation->payment_method) === 'cortesia'
                                ? 'selected'
                                : '' }}
                        >
                            Cortesía
                        </option>
                    </select>
                </div>

                <div class="form-group form-group--full">
                    <div class="payment-summary">

                        <div>
                            <span>Subtotal actual</span>
                            <strong>
                                Bs {{ number_format($reservation->subtotal, 2) }}
                            </strong>
                        </div>

                        <div>
                            <span>Descuento actual</span>
                            <strong>
                                Bs {{ number_format($reservation->discount, 2) }}
                            </strong>
                        </div>

                        <div class="payment-summary__total">
                            <span>Total actual</span>
                            <strong>
                                Bs {{ number_format($reservation->total, 2) }}
                            </strong>
                        </div>

                    </div>
                </div>

            </div>

            <div class="form-actions">

                <a
                    href="{{ route('reservations.index') }}"
                    class="secondary-button"
                >
                    Cancelar
                </a>

                <button
                    type="submit"
                    class="primary-button"
                >
                    Guardar corrección
                </button>

            </div>

        </form>

    </div>

</section>

@endsection