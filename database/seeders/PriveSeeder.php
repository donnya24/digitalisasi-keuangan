<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Prive;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PriveSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_active', 'active')->get();

        // Hapus data lama jika ada
        Prive::truncate();

        $totalPrive = 0;

        foreach ($users as $user) {
            // Generate prive untuk 30 hari terakhir (2-5 kali per bulan)
            $numPrive = rand(2, 5);

            for ($i = 0; $i < $numPrive; $i++) {
                $date = Carbon::now()->subDays(rand(1, 30));

                Prive::create([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'amount' => rand(100000, 500000),
                    'description' => $this->getRandomPriveDescription(),
                    'prive_date' => $date->format('Y-m-d'),
                    'purpose' => $this->getRandomPurpose(),
                    'is_approved' => 'approved', // ENUM: 'approved', bukan 1 atau true
                    'created_at' => $date->copy()->setTime(rand(8, 20), rand(0, 59)),
                    'updated_at' => $date->copy()->setTime(rand(8, 20), rand(0, 59)),
                ]);

                $totalPrive++;
            }
        }

        $this->command->info("✅ Prive berhasil dibuat: {$totalPrive} prive");
    }

    private function getRandomPriveDescription()
    {
        $descriptions = [
            'Ambil uang untuk kebutuhan pribadi',
            'Penarikan untuk belanja bulanan',
            'Uang jajan anak',
            'Bayar sekolah anak',
            'Beli pulsa dan kuota',
            'Bensin motor',
            'Bayar cicilan',
            'Beli obat keluarga',
        ];
        return $descriptions[array_rand($descriptions)];
    }

    private function getRandomPurpose()
    {
        $purposes = [
            'kebutuhan pribadi',
            'belanja bulanan',
            'pendidikan',
            'kesehatan',
            'transportasi',
            'hiburan',
        ];
        return $purposes[array_rand($purposes)];
    }
}
