<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'email' => 'admin@bioskop.com',
            'password' => Hash::make('password'),
            'nama_lengkap' => 'Admin Bioskop',
            'phone' => '081234567890',
            'role' => 'admin',
            'status_aktif' => true,
        ]);

        User::create([
            'email' => 'owner@bioskop.com',
            'password' => Hash::make('password'),
            'nama_lengkap' => 'Owner Bioskop',
            'phone' => '081234567891',
            'role' => 'owner',
            'status_aktif' => true,
        ]);

        User::create([
            'email' => 'kasir@bioskop.com',
            'password' => Hash::make('password'),
            'nama_lengkap' => 'Kasir Bioskop',
            'phone' => '081234567892',
            'role' => 'kasir',
            'status_aktif' => true,
        ]);

        User::create([
            'email' => 'pelanggan@gmail.com',
            'password' => Hash::make('password'),
            'nama_lengkap' => 'Pelanggan Test',
            'phone' => '081234567893',
            'role' => 'pelanggan',
            'status_aktif' => true,
        ]);
    }
}
