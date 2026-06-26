<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSnackSalesTable extends Migration
{
    public function up()
    {
        Schema::create('snack_sales', function (Blueprint $table) {
            $table->id();

            $table->string('sale_number', 30)
                ->unique();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('payment_method', [
                'efectivo',
                'qr',
            ]);

            $table->decimal('subtotal', 10, 2)
                ->default(0);

            $table->decimal('total', 10, 2)
                ->default(0);

            $table->enum('status', [
                'completada',
                'anulada',
            ])->default('completada');

            $table->dateTime('sold_at');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('snack_sales');
    }
}