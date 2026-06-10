<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
use WithoutModelEvents;
 
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@kulaan.id')],
            [
                'name'         => 'Admin Kulaan',
                'password'     => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'phone_number' => '0812' . rand(10000000, 99999999),
                'roles'        => ['admin'],
            ]
        );
 
        // Jalankan seeder pesanan (termasuk seller + buyer + produk dummy)
        $this->call(OrderSeeder::class);
    }
}
