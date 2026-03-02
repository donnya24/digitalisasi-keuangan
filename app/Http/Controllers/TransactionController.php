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
        
        // Filter berdasarkan tipe (pemasukan/pengeluaran)
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
            'category_id.required' => 'Kategori harus dipilih',
            'category_id.exists' => 'Kategori tidak ditemukan',
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

        // Generate nomor referensi
        $referenceNumber = 'TRX-' . Carbon::now()->format('Ymd') . '-' . strtoupper(Str::random(6));

        Transaction::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'category_id' => $request->category_id,
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
            'category_id.required' => 'Kategori harus dipilih',
            'category_id.exists' => 'Kategori tidak ditemukan',
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

        $transaction->update([
            'category_id' => $request->category_id,
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
     * Export transactions to PDF (optional)
     */
    public function export(Request $request)
    {
        // TODO: Implement export to PDF
        return redirect()->back()->with('info', 'Fitur export sedang dalam pengembangan');
    }
}