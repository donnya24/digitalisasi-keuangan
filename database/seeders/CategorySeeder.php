<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        $incomeCategories = [
            ['name' => 'Penjualan Kopi', 'icon' => 'coffee', 'color' => '#4CAF50'],
            ['name' => 'Penjualan Makanan Ringan', 'icon' => 'cookie', 'color' => '#8BC34A'],
            ['name' => 'Penjualan Minuman', 'icon' => 'mug-hot', 'color' => '#CDDC39'],
            ['name' => 'Penjualan Makanan Berat', 'icon' => 'utensils', 'color' => '#FFC107'],
            ['name' => 'Pendapatan Lainnya', 'icon' => 'coins', 'color' => '#9C27B0'],
        ];

        $expenseCategories = [
            ['name' => 'Pembelian Bahan Baku', 'icon' => 'cart-shopping', 'color' => '#F44336'],
            ['name' => 'Operasional', 'icon' => 'gear', 'color' => '#FF9800'],
            ['name' => 'Gaji Karyawan', 'icon' => 'users', 'color' => '#9C27B0'],
            ['name' => 'Listrik & Air', 'icon' => 'bolt', 'color' => '#2196F3'],
            ['name' => 'Sewa Tempat', 'icon' => 'house', 'color' => '#3F51B5'],
            ['name' => 'Internet & Telepon', 'icon' => 'wifi', 'color' => '#00BCD4'],
            ['name' => 'Perawatan & Perbaikan', 'icon' => 'wrench', 'color' => '#795548'],
            ['name' => 'Transportasi', 'icon' => 'truck', 'color' => '#607D8B'],
            ['name' => 'Promosi & Marketing', 'icon' => 'bullhorn', 'color' => '#E91E63'],
            ['name' => 'Perlengkapan', 'icon' => 'box', 'color' => '#673AB7'],
        ];

        $totalCategories = 0;

        foreach ($users as $user) {
            // Insert kategori pemasukan
            foreach ($incomeCategories as $category) {
                Category::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'name' => $category['name'],
                        'type' => 'pemasukan',
                    ],
                    [
                        'id' => Str::uuid(),
                        'icon' => $category['icon'],
                        'color' => $category['color'],
                        'is_active' => 'active', // ENUM: 'active'
                        'description' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                $totalCategories++;
            }

            // Insert kategori pengeluaran
            foreach ($expenseCategories as $category) {
                Category::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'name' => $category['name'],
                        'type' => 'pengeluaran',
                    ],
                    [
                        'id' => Str::uuid(),
                        'icon' => $category['icon'],
                        'color' => $category['color'],
                        'is_active' => 'active', // ENUM: 'active'
                        'description' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                $totalCategories++;
            }
        }

        $this->command->info("✅ Kategori berhasil dibuat: {$totalCategories} kategori");
    }
}
