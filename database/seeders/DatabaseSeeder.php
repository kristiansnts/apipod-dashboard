<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Admin User untuk Filament
        User::updateOrCreate(
            ['email' => 'admin@apipod.app'],
            [
                'name' => 'Kristian Admin',
                'password' => Hash::make('password123'),
                'active' => true,
            ]
        );

        // 2. Jalankan Seeder Fitur (Paket, Model, Test User)
        $this->call(ApiPodTestSeeder::class);
    }
}
