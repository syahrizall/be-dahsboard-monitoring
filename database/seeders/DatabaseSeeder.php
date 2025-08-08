<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create default user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@polri.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);
    }
}
