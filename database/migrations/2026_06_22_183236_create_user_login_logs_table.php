<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLoginLogsTable extends Migration
{
    public function up()
    {
        Schema::create('user_login_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->enum('event', [
                'login',
                'logout',
            ]);

            $table->string('ip_address', 60)
                ->nullable();

            $table->text('user_agent')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_login_logs');
    }
}