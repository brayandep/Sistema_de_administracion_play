@extends('layouts.admin')

@section('title', 'Historial de ingreso')

@section('page-title', 'Historial de ingreso')

@section(
    'page-description',
    'Registro de accesos de usuarios al sistema'
)

@section('content')

<section class="card">

    <div class="card__header">
        <h2>Ingresos y salidas del sistema</h2>

        <p>
            Control de actividad de cajeros y administradores.
        </p>
    </div>

    @if ($logs->count() > 0)

        <div class="table-container">

            <table class="data-table">

                <thead>
                    <tr>
                        <th>Nro</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Evento</th>
                        <th>IP</th>
                        <th>Navegador</th>
                        <th>Fecha y hora</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($logs as $log)
                        <tr>
                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                {{ $log->user->name ?? 'Usuario eliminado' }}
                            </td>

                            <td>
                                {{ $log->user->role ?? '-' }}
                            </td>

                            <td>
                                @if ($log->event === 'login')
                                    <span class="badge badge--available">
                                        Ingreso
                                    </span>
                                @else
                                    <span class="badge badge--unavailable">
                                        Salida
                                    </span>
                                @endif
                            </td>

                            <td>
                                {{ $log->ip_address }}
                            </td>

                            <td>
                                {{ \Illuminate\Support\Str::limit(
                                    $log->user_agent,
                                    55
                                ) }}
                            </td>

                            <td>
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>

        </div>

        <div class="pagination-wrapper">
            {{ $logs->links() }}
        </div>

    @else

        <div class="empty-state">
            <div class="empty-state__icon">
                👤
            </div>

            <h3>No hay registros de ingreso</h3>

            <p>
                Cuando los usuarios inicien o cierren sesión,
                aparecerán en esta lista.
            </p>
        </div>

    @endif

</section>

@endsection