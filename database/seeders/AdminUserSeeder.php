<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            [
                'username' => 'admin',
            ],
            [
                'name' => 'brayandep',
                'email' => 'admin@pixelplay.local',
                'password' => Hash::make('76986478a'),
                'role' => 'administrador',
                'active' => true,
            ]
        );
    }
}