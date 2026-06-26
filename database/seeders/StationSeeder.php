<?php

namespace Database\Seeders;

use App\Models\Station;
use Illuminate\Database\Seeder;

class StationSeeder extends Seeder
{
    public function run()
    {
        /*
        |--------------------------------------------------------------------------
        | Limpieza de estaciones anteriores
        |--------------------------------------------------------------------------
        | Esto elimina PS5, billar u otras estaciones anteriores.
        | Úsalo solo si todavía estás en desarrollo y no te importa borrar
        | las estaciones anteriores.
        */

        Station::query()->delete();

        /*
        |--------------------------------------------------------------------------
        | Crear 12 salas
        |--------------------------------------------------------------------------
        */

        for ($i = 1; $i <= 12; $i++) {
            Station::create([
                'name' => 'Sala ' . $i,
                'type' => 'otro',
                'hourly_rate' => 20,
                'status' => 'disponible',
                'active' => true,
            ]);
        }
    }
}