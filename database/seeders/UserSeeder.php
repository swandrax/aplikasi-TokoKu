<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Gunakan Hash::make() agar password terenkripsi dan bisa digunakan untuk login
        User::create([
            'nama' => 'Administrator',
            'email' => 'admin@gmail.com',
            'role' => '1',
            'status' => '1',
            'password' => Hash::make('password'),
            'hp' => '081234567890'
        ]);

        User::create([
            'nama' => 'User Admin',
            'email' => 'useradmin@gmail.com',
            'role' => '2',
            'status' => '1',
            'password' => Hash::make('password'),
            'hp' => '081234567891'
        ]);

        User::create([
            'nama' => 'Customer',
            'email' => 'customer@gmail.com',
            'role' => '0',
            'status' => '1',
            'password' => Hash::make('password'),
            'hp' => '081234567892'
        ]);
    }
}
