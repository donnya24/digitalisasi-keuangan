<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('is_active', 'active')->get();

        // Hapus data lama
        Transaction::truncate();

        $totalTransactions = 0;

        foreach ($users as $user) {
            // Ambil kategori untuk user ini
            $incomeCategories = Category::where('user_id', $user->id)
                ->where('type', 'pemasukan')
                ->where('is_active', 'active')
                ->pluck('id')
                ->toArray();

            $expenseCategories = Category::where('user_id', $user->id)
                ->where('type', 'pengeluaran')
                ->where('is_active', 'active')
                ->pluck('id')
                ->toArray();

            if (empty($incomeCategories) || empty($expenseCategories)) {
                $this->command->warn("⚠️ User {$user->email} belum punya kategori, lewati...");
                continue;
            }

            // Generate transaksi untuk 30 hari terakhir
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);

                // Random jumlah transaksi per hari (3-8 transaksi)
                $numTransactions = rand(3, 8);

                for ($j = 0; $j < $numTransactions; $j++) {
                    // 60% kemungkinan pemasukan, 40% pengeluaran
                    $isIncome = rand(1, 100) <= 60;

                    if ($isIncome) {
                        $categoryId = $incomeCategories[array_rand($incomeCategories)];
                        $amount = rand(15000, 150000);
                        $description = $this->getRandomIncomeDescription();
                        $type = 'pemasukan';
                    } else {
                        $categoryId = $expenseCategories[array_rand($expenseCategories)];
                        $amount = rand(10000, 500000);
                        $description = $this->getRandomExpenseDescription();
                        $type = 'pengeluaran';
                    }

                    Transaction::create([
                        'id' => Str::uuid(),
                        'user_id' => $user->id,
                        'category_id' => $categoryId,
                        'type' => $type,
                        'amount' => $amount,
                        'description' => $description,
                        'transaction_date' => $date->format('Y-m-d'),
                        'payment_method' => $this->getRandomPaymentMethod(),
                        'reference_number' => 'TRX-' . $date->format('Ymd') . '-' . strtoupper(Str::random(6)),
                        'notes' => rand(0, 1) ? 'Catatan: ' . $this->getRandomNotes() : null,
                        'created_at' => $date->copy()->setTime(rand(8, 20), rand(0, 59)),
                        'updated_at' => $date->copy()->setTime(rand(8, 20), rand(0, 59)),
                    ]);

                    $totalTransactions++;
                }
            }

            // Tambah beberapa transaksi untuk hari ini
            $today = Carbon::now();
            for ($j = 0; $j < rand(2, 5); $j++) {
                $isIncome = rand(1, 100) <= 60;

                Transaction::create([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'category_id' => $isIncome ? $incomeCategories[array_rand($incomeCategories)] : $expenseCategories[array_rand($expenseCategories)],
                    'type' => $isIncome ? 'pemasukan' : 'pengeluaran',
                    'amount' => rand(15000, 200000),
                    'description' => $isIncome ? 'Penjualan ' . $this->getRandomItem() : 'Pembelian ' . $this->getRandomItem(),
                    'transaction_date' => $today->format('Y-m-d'),
                    'payment_method' => 'tunai',
                    'reference_number' => 'TRX-' . $today->format('Ymd') . '-' . strtoupper(Str::random(6)),
                    'created_at' => $today->copy()->setTime(rand(8, 20), rand(0, 59)),
                    'updated_at' => $today->copy()->setTime(rand(8, 20), rand(0, 59)),
                ]);

                $totalTransactions++;
            }
        }

        $this->command->info("✅ Transaksi berhasil dibuat: {$totalTransactions} transaksi");
    }

    private function getRandomIncomeDescription()
    {
        $items = [
            'Kopi Tubruk', 'Kopi Susu', 'Kopi Aren', 'Cappuccino', 'Latte',
            'Nasi Goreng', 'Mie Goreng', 'Kentang Goreng', 'Pisang Goreng',
            'Teh Manis', 'Teh Tarik', 'Jus Jeruk', 'Jus Alpukat', 'Air Mineral',
            'Roti Bakar', 'Indomie', 'Cireng', 'Basreng', 'Cilok'
        ];
        return 'Penjualan ' . $items[array_rand($items)];
    }

    private function getRandomExpenseDescription()
    {
        $items = [
            'Beli Kopi Bubuk', 'Beli Gula Pasir', 'Beli Susu', 'Beli Sirup',
            'Beli Beras', 'Beli Mie Instan', 'Beli Minyak Goreng',
            'Beli Gas Elpiji', 'Beli Listrik', 'Bayar Air PDAM',
            'Bayar Internet', 'Beli Cangkir', 'Beli Sendok/Garpu',
            'Beli Tisu', 'Beli Sabun Cuci Piring', 'Gaji Karyawan',
            'Sewa Tempat', 'Beli Perlengkapan'
        ];
        return $items[array_rand($items)];
    }

    private function getRandomPaymentMethod()
    {
        $methods = ['tunai', 'qris', 'transfer bank', 'gopay', 'ovo', 'dana'];
        return $methods[array_rand($methods)];
    }

    private function getRandomItem()
    {
        $items = ['Kopi', 'Makanan', 'Minuman', 'Snack', 'Bahan Baku'];
        return $items[array_rand($items)];
    }

    private function getRandomNotes()
    {
        $notes = [
            'Pelanggan ramai',
            'Diskon 10%',
            'Orderan online',
            'Pelanggan langganan',
            'Harga naik',
            'Stock menipis',
            'Promo akhir pekan'
        ];
        return $notes[array_rand($notes)];
    }
}
