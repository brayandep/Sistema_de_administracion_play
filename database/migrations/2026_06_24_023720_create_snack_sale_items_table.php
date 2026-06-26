<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSnackSaleItemsTable extends Migration
{
    public function up()
    {
        Schema::create('snack_sale_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('snack_sale_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('product_name', 120);

            $table->unsignedInteger('quantity');

            $table->decimal('unit_price', 10, 2);

            $table->decimal('total', 10, 2);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('snack_sale_items');
    }
}