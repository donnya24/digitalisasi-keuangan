<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of the transactions.
     */
/**
 * Display a listing of the transactions.
 */
public function index(Request $request)
{
    $user = auth()->user();
    
    $query = Transaction::with('category')
        ->where('user_id', $user->id);
    
    // Filter berdasarkan tanggal
    if ($request->has('start_date') && $request->has('end_date') && $request->start_date && $request->end_date) {
        $query->whereBetween('transaction_date', [$request->start_date, $request->end_date]);
    } elseif ($request->has('date') && $request->date) {
        $query->whereDate('transaction_date', $request->date);
    }
    
    // Filter berdasarkan tipe
    if ($request->has('type') && $request->type && in_array($request->type, ['pemasukan', 'pengeluaran'])) {
        $query->where('type', $request->type);
    }
    
    // Filter berdasarkan kategori
    if ($request->has('category_id') && $request->category_id) {
        $query->where('category_id', $request->category_id);
    }
    
    // ========== PENCARIAN (DESKRIPSI + KATEGORI) ==========
    if ($request->has('search') && !empty($request->search)) {
        $searchTerm = $request->search;
        
        $query->where(function($q) use ($searchTerm) {
            // 1. Cari di deskripsi transaksi
            $q->where('description', 'LIKE', '%' . $searchTerm . '%');
            
            // 2. ATAU cari di nama kategori (melalui relasi)
            $q->orWhereHas('category', function($categoryQuery) use ($searchTerm) {
                $categoryQuery->where('name', 'LIKE', '%' . $searchTerm . '%');
            });
        });
    }
    
    $transactions = $query->orderBy('transaction_date', 'desc')
        ->orderBy('created_at', 'desc')
        ->paginate(15)
        ->withQueryString(); // Penting untuk pagination dengan filter
    
    // Ambil kategori untuk filter dropdown
    $categories = Category::where('user_id', $user->id)
        ->where('is_active', 'active')
        ->orderBy('type')
        ->orderBy('name')
        ->get();
    
    // Hitung total (untuk data yang sudah difilter)
    $totalIncome = $transactions->where('type', 'pemasukan')->sum('amount');
    $totalExpense = $transactions->where('type', 'pengeluaran')->sum('amount');
    $balance = $totalIncome - $totalExpense;
    
    // Jika request AJAX, kembalikan hanya partial table
    if ($request->ajax()) {
        return view('transactions.partials.table', compact(
            'transactions', 
            'categories', 
            'totalIncome', 
            'totalExpense', 
            'balance'
        ));
    }
    
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
        
        return view('transactions.create', compact('type'));
    }

    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Bersihkan amount dari format Rupiah (hapus titik)
        $cleanAmount = $this->cleanAmount($request->amount);
        
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

        // Log untuk debugging
        Log::info('STORE - Raw amount from request:', ['amount' => $request->amount]);
        Log::info('STORE - Cleaned amount:', ['clean' => $cleanAmount]);

        // Cari atau buat kategori baru (tanpa icon dan color)
        $category = Category::firstOrCreate(
            [
                'user_id' => $user->id,
                'name' => $request->category_name,
                'type' => $request->type,
            ],
            [
                'is_active' => 'active',
            ]
        );

        // Generate nomor referensi
        $referenceNumber = 'TRX-' . Carbon::now()->format('Ymd') . '-' . strtoupper(Str::random(6));

        // Simpan dengan amount yang sudah dibersihkan
        Transaction::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => $request->type,
            'amount' => $cleanAmount,
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
        
        return view('transactions.edit', compact('transaction'));
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
        
        // Bersihkan amount dari format Rupiah (hapus titik)
        $cleanAmount = $this->cleanAmount($request->amount);
        
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

        // Log untuk debugging
        Log::info('UPDATE - Raw amount from request:', ['amount' => $request->amount]);
        Log::info('UPDATE - Cleaned amount:', ['clean' => $cleanAmount]);

        // Cari atau buat kategori baru (tanpa icon dan color)
        $category = Category::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'name' => $request->category_name,
                'type' => $transaction->type, // type tetap sama
            ],
            [
                'is_active' => 'active',
            ]
        );

        // Update transaksi dengan amount yang sudah dibersihkan
        $transaction->update([
            'category_id' => $category->id,
            'amount' => $cleanAmount,
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
        
        // Simpan informasi untuk log
        $transactionId = $transaction->id;
        $transactionDescription = $transaction->description;
        
        // Hapus transaksi
        $transaction->delete();
        
        // Log untuk debugging
        Log::info('Transaction deleted:', [
            'id' => $transactionId,
            'description' => $transactionDescription,
            'user_id' => auth()->id(),
            'time' => now()
        ]);
        
        // Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi "' . $transactionDescription . '" berhasil dihapus');
    }

    /**
     * Helper function untuk membersihkan format Rupiah menjadi angka
     */
    private function cleanAmount($amount)
    {
        if (empty($amount)) {
            return 0;
        }
        
        // Hapus semua karakter non-digit (titik, spasi, huruf, dll)
        $cleaned = preg_replace('/[^0-9]/', '', $amount);
        
        // Konversi ke integer
        return (int) $cleaned;
    }
}