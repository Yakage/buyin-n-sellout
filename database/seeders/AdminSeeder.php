<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    User::create(
        [
            'name' => "Admin",
            'email' => "admin@gmail.com",
            'password' => '12345678',
            'role' => 1,
            'phone' => "09095738283",
        ]);
    }   
}
