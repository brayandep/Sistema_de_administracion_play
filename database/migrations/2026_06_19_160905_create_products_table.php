<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('name', 120);

            $table->unsignedInteger('quantity')
                ->default(0);

            $table->text('description')
                ->nullable();

            $table->decimal('price', 10, 2);

            $table->enum('product_type', [
                'snack',
                'regalo',
            ]);

            $table->boolean('available')
                ->default(true);

            $table->string('image')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}