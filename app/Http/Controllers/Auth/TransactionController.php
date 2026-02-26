<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with('category')
            ->where('user_id', auth()->id());

        // Filter berdasarkan tanggal
        if ($request->has('date')) {
            $query->whereDate('transaction_date', $request->date);
        }

        // Filter berdasarkan tipe
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter berdasarkan kategori
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->paginate(20);

        $categories = Category::where('user_id', auth()->id())
            ->where('is_active', true)
            ->get();

        return view('transactions.index', compact('transactions', 'categories'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'pemasukan');
        $categories = Category::where('user_id', auth()->id())
            ->where('type', $type)
            ->where('is_active', true)
            ->get();

        return view('transactions.create', compact('categories', 'type'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:pemasukan,pengeluaran',
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Transaction::create([
            'user_id' => auth()->id(),
            'type' => $request->type,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'transaction_date' => $request->transaction_date,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'reference_number' => 'TRX-' . time() . rand(100, 999)
        ]);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil ditambahkan');
    }

    public function edit(Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $categories = Category::where('user_id', auth()->id())
            ->where('type', $transaction->type)
            ->where('is_active', true)
            ->get();

        return view('transactions.edit', compact('transaction', 'categories'));
    }

    public function update(Request $request, Transaction $transaction)
    {
        $this->authorize('update', $transaction);

        $validator = Validator::make($request->all(), [
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $transaction->update($request->only([
            'category_id', 'amount', 'description',
            'transaction_date', 'notes'
        ]));

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui');
    }

    public function destroy(Transaction $transaction)
    {
        $this->authorize('delete', $transaction);

        $transaction->delete();

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil dihapus');
    }
}
