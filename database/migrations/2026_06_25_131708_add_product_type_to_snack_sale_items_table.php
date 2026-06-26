<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductTypeToSnackSaleItemsTable extends Migration
{
    public function up()
    {
        Schema::table('snack_sale_items', function (Blueprint $table) {
            $table->enum('product_type', [
                'snack',
                'regalo',
            ])
                ->default('snack')
                ->after('product_name');
        });
    }

    public function down()
    {
        Schema::table('snack_sale_items', function (Blueprint $table) {
            $table->dropColumn('product_type');
        });
    }
}