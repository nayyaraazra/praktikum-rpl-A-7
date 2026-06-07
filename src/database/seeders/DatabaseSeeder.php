<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
use WithoutModelEvents;
 
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@kulaan.id'],
            [
                'name'         => 'Admin Kulaan',
                'password'     => Hash::make('password'),
                'phone_number' => '081200000000',
                'roles'        => ['admin'],
            ]
        );
 
        // Jalankan seeder pesanan (termasuk seller + buyer + produk dummy)
        $this->call(OrderSeeder::class);
    }
}
