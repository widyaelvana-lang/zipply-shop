<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Elvana',
            'email' => 'admin@tokotas.com',
            'password' => Hash::make('$2y$12$'),
        ]);

        User::create([
            'name' => 'Axel',
            'email' => 'admin1@tokotas.com',
            'password' => Hash::make('123456'),
        ]);
    }
}
