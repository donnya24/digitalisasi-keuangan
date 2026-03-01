<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business;
use App\Models\User;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Cek apakah sudah punya business
            if (!$user->business) {
                if ($user->email == 'budi@warkop96.com') {
                    Business::create([
                        'user_id' => $user->id,
                        'business_name' => 'Warkop 96',
                        'business_type' => 'warkop',
                        'phone' => '081234567890',
                        'address' => 'Jl. Sudirman No. 123',
                        'city' => 'Jakarta',
                        'province' => 'DKI Jakarta',
                        'postal_code' => '12345',
                        'opening_hours' => json_encode([
                            'senin' => '07:00-22:00',
                            'selasa' => '07:00-22:00',
                            'rabu' => '07:00-22:00',
                            'kamis' => '07:00-22:00',
                            'jumat' => '07:00-23:00',
                            'sabtu' => '08:00-23:00',
                            'minggu' => '08:00-22:00',
                        ]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } elseif ($user->email == 'tes@gmail.com') {
                    Business::create([
                        'user_id' => $user->id,
                        'business_name' => 'Warkop Test',
                        'phone' => '081298765432',
                        'business_type' => 'warkop',
                        'city' => 'Bandung',
                        'province' => 'Jawa Barat',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    Business::create([
                        'user_id' => $user->id,
                        'business_name' => 'Usaha ' . $user->name,
                        'business_type' => 'warkop',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info('✅ Business berhasil dibuat: ' . Business::count() . ' bisnis');
    }
}
