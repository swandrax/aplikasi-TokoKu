<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'nama'     => 'Administrator',
                'role'     => '1',
                'status'   => 1,
                'hp'       => '0812345678901',
                'password' => bcrypt('P@55word'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'useradmin@gmail.com'],
            [
                'nama'     => 'User Admin',
                'role'     => '0',
                'status'   => 1,
                'hp'       => '081234567892',
                'password' => bcrypt('P@55word'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@gmail.com'],
            [
                'nama'     => 'Customer Demo',
                'role'     => '2',
                'status'   => 1,
                'hp'       => '081234567893',
                'password' => bcrypt('P@55word'),
            ]
        );

        $kategoriList = ['Brownies', 'Combro', 'Dawet', 'Mochi', 'Wingko'];
        foreach ($kategoriList as $nama) {
            Kategori::firstOrCreate(['nama_kategori' => $nama]);
        }
    }
}
