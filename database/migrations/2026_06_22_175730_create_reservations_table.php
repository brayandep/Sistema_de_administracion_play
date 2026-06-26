<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('station_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->dateTime('started_at');

            $table->dateTime('ended_at');

            $table->unsignedInteger('duration_minutes');

            $table->unsignedInteger('paid_minutes')
                ->default(0);

            $table->unsignedInteger('free_minutes')
                ->default(0);

            $table->decimal('hourly_rate', 10, 2)
                ->default(20);

            $table->decimal('subtotal', 10, 2)
                ->default(0);

            $table->decimal('discount', 10, 2)
                ->default(0);

            $table->decimal('total', 10, 2)
                ->default(0);

            $table->enum('payment_method', [
                'efectivo',
                'qr',
                'mixto',
                'cortesia',
            ])->default('efectivo');

            $table->enum('status', [
                'activa',
                'finalizada',
                'cancelada',
            ])->default('activa');

            $table->unsignedBigInteger('reward_ticket_id')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservations');
    }
}