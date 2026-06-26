<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStationsTable extends Migration
{
    public function up()
    {
        Schema::create('stations', function (Blueprint $table) {
            $table->id();

            $table->string('name', 80);

            $table->enum('type', [
                'ps5',
                'billar',
                'otro',
            ]);

            $table->decimal('hourly_rate', 10, 2)
                ->default(20);

            $table->enum('status', [
                'disponible',
                'ocupado',
                'reservado',
                'mantenimiento',
            ])->default('disponible');

            $table->boolean('active')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stations');
    }
}