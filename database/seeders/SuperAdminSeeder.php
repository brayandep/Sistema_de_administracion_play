<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['username' => 'superadmin'],
            [
                'name' => 'Super Administrador',
                'email' => 'superadmin@pixelplay.local',
                'password' => Hash::make('68506805aA@'),
                'role' => 'super_admin',
                'active' => true,
            ]
        );
    }
}