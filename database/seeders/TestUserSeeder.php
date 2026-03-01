<?php
// database/seeders/TestUserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'tes@gmail.com')->first();

        if (!$user) {
            User::create([
                'name' => 'Test User',
                'email' => 'tes@gmail.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'is_active' => 'active',  // Gunakan 'active' bukan true/false
            ]);

            $this->command->info('✅ User tes@gmail.com berhasil dibuat!');
            $this->command->info('📧 Email: tes@gmail.com');
            $this->command->info('🔑 Password: password123');
        } else {
            $this->command->info('⚠️ User tes@gmail.com sudah ada!');
        }
    }
}
