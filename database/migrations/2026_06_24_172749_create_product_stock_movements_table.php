<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStockMovementsTable extends Migration
{
    public function up()
    {
        Schema::create('product_stock_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', [
                'entrada',
                'ajuste',
            ])->default('entrada');

            $table->unsignedInteger('quantity');

            $table->unsignedInteger('previous_quantity');

            $table->unsignedInteger('new_quantity');

            $table->string('description', 255)
                ->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_stock_movements');
    }
}