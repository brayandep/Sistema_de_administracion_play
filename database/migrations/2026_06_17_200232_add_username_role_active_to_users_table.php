<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsernameRoleActiveToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)
                ->unique()
                ->after('name');

            $table->string('role', 30)
                ->default('cajero')
                ->after('password');

            $table->boolean('active')
                ->default(true)
                ->after('role');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'role',
                'active',
            ]);
        });
    }
}