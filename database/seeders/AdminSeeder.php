<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'name' => 'Administrator',
            'email' => 'admin@ummada.ac.id',
            'password' => Hash::make('password123'),
            'phone' => '081234567890',
            'is_active' => true,
        ]);
    }
}
