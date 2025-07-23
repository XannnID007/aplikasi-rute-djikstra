<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Buat akun admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'peran' => 'admin',
        ]);

        // Buat akun pengguna contoh
        User::create([
            'name' => 'Pengguna Test',
            'email' => 'user@gmail.com',
            'password' => Hash::make('password'),
            'peran' => 'pengguna',
        ]);
    }
}
