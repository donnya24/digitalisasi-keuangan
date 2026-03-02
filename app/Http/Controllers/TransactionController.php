<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Transaction::with('category')
            ->where('user_id', $user->id);
        
        // Filter berdasarkan tanggal
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
        } elseif ($request->has('date')) {
            $query->whereDate('transaction_date', $request->date);
        }
        
        // Filter berdasarkan tipe
        if ($request->has('type') && in_array($request->type, ['pemasukan', 'pengeluaran'])) {
            $query->where('type', $request->type);
        }
        
        // Filter berdasarkan kategori
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Pencarian berdasarkan deskripsi
        if ($request->has('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        
        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Ambil kategori untuk filter
        $categories = Category::where('user_id', $user->id)
            ->where('is_active', 'active')
            ->orderBy('type')
            ->orderBy('name')
            ->get();
        
        // Hitung total
        $totalIncome = $transactions->where('type', 'pemasukan')->sum('amount');
        $totalExpense = $transactions->where('type', 'pengeluaran')->sum('amount');
        $balance = $totalIncome - $totalExpense;
        
        return view('transactions.index', compact(
            'transactions', 
            'categories', 
            'totalIncome', 
            'totalExpense', 
            'balance'
        ));
    }

    /**
     * Show the form for creating a new transaction.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $type = $request->get('type', 'pemasukan'); // Default pemasukan
        
        // Ambil kategori untuk referensi (opsional)
        $categories = Category::where('user_id', $user->id)
            ->where('type', $type)
            ->where('is_active', 'active')
            ->orderBy('name')
            ->get();
        
        return view('transactions.create', compact('categories', 'type'));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:pemasukan,pengeluaran',
            'category_name' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ], [
            'type.required' => 'Tipe transaksi harus dipilih',
            'type.in' => 'Tipe transaksi tidak valid',
            'category_name.required' => 'Nama kategori harus diisi',
            'category_name.max' => 'Nama kategori maksimal 100 karakter',
            'amount.required' => 'Jumlah harus diisi',
            'amount.numeric' => 'Jumlah harus berupa angka',
            'amount.min' => 'Jumlah minimal 0',
            'description.required' => 'Deskripsi harus diisi',
            'description.max' => 'Deskripsi maksimal 255 karakter',
            'transaction_date.required' => 'Tanggal transaksi harus diisi',
            'transaction_date.date' => 'Format tanggal tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Cari atau buat kategori baru
        $category = Category::firstOrCreate(
            [
                'user_id' => $user->id,
                'name' => $request->category_name,
                'type' => $request->type,
            ],
            [
                'icon' => $this->getIconForCategory($request->category_name, $request->type),
                'color' => $this->getColorForCategory($request->category_name, $request->type),
                'is_active' => 'active',
            ]
        );

        // Generate nomor referensi
        $referenceNumber = 'TRX-' . Carbon::now()->format('Ymd') . '-' . strtoupper(Str::random(6));

        Transaction::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'transaction_date' => $request->transaction_date,
            'payment_method' => $request->payment_method,
            'reference_number' => $referenceNumber,
            'notes' => $request->notes,
        ]);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil ditambahkan');
    }

    /**
     * Display the specified transaction.
     */
    public function show(Transaction $transaction)
    {
        // Pastikan user hanya bisa melihat transaksinya sendiri
        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        
        $transaction->load('category');
        
        return view('transactions.show', compact('transaction'));
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit(Transaction $transaction)
    {
        // Pastikan user hanya bisa mengedit transaksinya sendiri
        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        
        // Ambil kategori untuk referensi (opsional)
        $categories = Category::where('user_id', auth()->id())
            ->where('type', $transaction->type)
            ->where('is_active', 'active')
            ->orderBy('name')
            ->get();
        
        return view('transactions.edit', compact('transaction', 'categories'));
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        // Pastikan user hanya bisa update transaksinya sendiri
        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        
        $validator = Validator::make($request->all(), [
            'category_name' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ], [
            'category_name.required' => 'Nama kategori harus diisi',
            'category_name.max' => 'Nama kategori maksimal 100 karakter',
            'amount.required' => 'Jumlah harus diisi',
            'amount.numeric' => 'Jumlah harus berupa angka',
            'amount.min' => 'Jumlah minimal 0',
            'description.required' => 'Deskripsi harus diisi',
            'description.max' => 'Deskripsi maksimal 255 karakter',
            'transaction_date.required' => 'Tanggal transaksi harus diisi',
            'transaction_date.date' => 'Format tanggal tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Cari atau buat kategori baru
        $category = Category::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'name' => $request->category_name,
                'type' => $transaction->type, // type tetap sama
            ],
            [
                'icon' => $this->getIconForCategory($request->category_name, $transaction->type),
                'color' => $this->getColorForCategory($request->category_name, $transaction->type),
                'is_active' => 'active',
            ]
        );

        // Update transaksi
        $transaction->update([
            'category_id' => $category->id,
            'amount' => $request->amount,
            'description' => $request->description,
            'transaction_date' => $request->transaction_date,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui');
    }

    /**
     * Remove the specified transaction from storage.
     */
    public function destroy(Transaction $transaction)
    {
        // Pastikan user hanya bisa hapus transaksinya sendiri
        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        
        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus');
    }

    /**
     * Helper function untuk mendapatkan icon berdasarkan nama kategori
     */
    private function getIconForCategory($categoryName, $type)
    {
        $categoryName = strtolower($categoryName);
        
        $icons = [
            'kopi' => 'coffee',
            'minuman' => 'mug-hot',
            'makanan' => 'utensils',
            'snack' => 'cookie',
            'gorengan' => 'cookie',
            'nasi' => 'bowl-food',
            'mie' => 'bowl-food',
            'indomie' => 'bowl-food',
            'voucher' => 'bolt',
            'pulsa' => 'phone',
            'listrik' => 'bolt',
            
            // Pengeluaran
            'bahan baku' => 'cart-shopping',
            'belanja' => 'cart-shopping',
            'gula' => 'cart-shopping',
            'kopi bubuk' => 'cart-shopping',
            'gas' => 'fire',
            'listrik' => 'bolt',
            'air' => 'water',
            'sewa' => 'house',
            'gaji' => 'users',
            'karyawan' => 'users',
            'internet' => 'wifi',
            'telepon' => 'phone',
            'peralatan' => 'box',
            'perlengkapan' => 'box',
            'gelas' => 'box',
            'piring' => 'box',
            'sedotan' => 'box',
            'perbaikan' => 'wrench',
            'servis' => 'wrench',
            'transport' => 'truck',
            'bensin' => 'gas-pump',
            'promosi' => 'bullhorn',
            'iklan' => 'bullhorn',
            'pajak' => 'file-contract',
            'retribusi' => 'file-contract',
        ];
        
        foreach ($icons as $key => $icon) {
            if (strpos($categoryName, $key) !== false) {
                return $icon;
            }
        }
        
        return $type == 'pemasukan' ? 'arrow-down' : 'arrow-up';
    }

    /**
     * Helper function untuk mendapatkan color berdasarkan nama kategori
     */
    private function getColorForCategory($categoryName, $type)
    {
        $categoryName = strtolower($categoryName);
        
        if ($type == 'pemasukan') {
            if (strpos($categoryName, 'kopi') !== false) return '#4CAF50';
            if (strpos($categoryName, 'minuman') !== false) return '#8BC34A';
            if (strpos($categoryName, 'makanan') !== false) return '#FFC107';
            if (strpos($categoryName, 'snack') !== false) return '#CDDC39';
            return '#9C27B0'; // default pemasukan
        } else {
            if (strpos($categoryName, 'bahan') !== false) return '#F44336';
            if (strpos($categoryName, 'gas') !== false) return '#FF5722';
            if (strpos($categoryName, 'listrik') !== false) return '#2196F3';
            if (strpos($categoryName, 'air') !== false) return '#00BCD4';
            if (strpos($categoryName, 'sewa') !== false) return '#3F51B5';
            if (strpos($categoryName, 'gaji') !== false) return '#9C27B0';
            if (strpos($categoryName, 'internet') !== false) return '#00BCD4';
            if (strpos($categoryName, 'peralatan') !== false) return '#795548';
            if (strpos($categoryName, 'transport') !== false) return '#607D8B';
            if (strpos($categoryName, 'promosi') !== false) return '#E91E63';
            return '#FF9800'; // default pengeluaran
        }
    }
}