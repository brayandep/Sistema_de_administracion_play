<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoyaltyMovementsTable extends Migration
{
    public function up()
    {
        Schema::create('loyalty_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('reservation_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('type', [
                'earned',
                'redeemed',
                'adjustment',
            ]);

            $table->integer('minutes');

            $table->string('description', 255)
                ->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('loyalty_movements');
    }
}