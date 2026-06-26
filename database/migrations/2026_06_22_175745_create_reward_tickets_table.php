<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRewardTicketsTable extends Migration
{
    public function up()
    {
        Schema::create('reward_tickets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('code', 40)
                ->unique();

            $table->unsignedInteger('free_minutes')
                ->default(60);

            $table->enum('status', [
                'disponible',
                'usado',
                'anulado',
                'vencido',
            ])->default('disponible');

            $table->dateTime('generated_at')
                ->nullable();

            $table->dateTime('used_at')
                ->nullable();

            $table->foreignId('reservation_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reward_tickets');
    }
}