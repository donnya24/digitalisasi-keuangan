<?php

namespace App\Http\Controllers;

use App\Models\Prive; // <-- TAMBAHKAN IMPORT INI
use Illuminate\Http\Request; // <-- TAMBAHKAN IMPORT INI
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // <-- TAMBAHKAN UNTUK DB::raw
use Illuminate\Support\Str;
use Carbon\Carbon; // <-- TAMBAHKAN IMPORT INI

class PriveController extends Controller
{
    /**
     * Display a listing of prive.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $query = Prive::where('user_id', $user->id);
        
        // Filter berdasarkan bulan
        if ($request->has('month') && $request->month) {
            $query->whereMonth('prive_date', Carbon::parse($request->month)->month)
                  ->whereYear('prive_date', Carbon::parse($request->month)->year);
        } else {
            // Default bulan ini
            $query->whereMonth('prive_date', Carbon::now()->month)
                  ->whereYear('prive_date', Carbon::now()->year);
        }
        
        // Filter berdasarkan status
        if ($request->has('status') && $request->status) {
            $query->where('is_approved', $request->status);
        }
        
        $prives = $query->orderBy('prive_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Hitung total prive bulan ini
        $totalPriveBulanIni = Prive::where('user_id', $user->id)
            ->whereMonth('prive_date', Carbon::now()->month)
            ->whereYear('prive_date', Carbon::now()->year)
            ->where('is_approved', 'approved')
            ->sum('amount');
        
        // Hitung total semua prive
        $totalAllPrive = Prive::where('user_id', $user->id)
            ->where('is_approved', 'approved')
            ->sum('amount');
        
        // Ambil bulan untuk filter - Menggunakan TO_CHAR untuk PostgreSQL
        $months = Prive::where('user_id', $user->id)
            ->select(DB::raw("DISTINCT TO_CHAR(prive_date, 'YYYY-MM') as month"))
            ->orderBy('month', 'desc')
            ->pluck('month');
        
        return view('prive.index', compact(
            'prives', 
            'totalPriveBulanIni', 
            'totalAllPrive', 
            'months'
        ));
    }

    /**
     * Show the form for creating a new prive.
     */
    public function create()
    {
        return view('prive.create');
    }

    /**
     * Store a newly created prive.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Bersihkan amount dari format Rupiah
        $cleanAmount = $this->cleanAmount($request->amount);
        
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'prive_date' => 'required|date',
            'purpose' => 'nullable|string|max:100',
        ], [
            'amount.required' => 'Jumlah harus diisi',
            'amount.numeric' => 'Jumlah harus berupa angka',
            'amount.min' => 'Jumlah minimal 0',
            'description.required' => 'Deskripsi harus diisi',
            'description.max' => 'Deskripsi maksimal 255 karakter',
            'prive_date.required' => 'Tanggal harus diisi',
            'prive_date.date' => 'Format tanggal tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Prive::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'amount' => $cleanAmount,
            'description' => $request->description,
            'prive_date' => $request->prive_date,
            'purpose' => $request->purpose,
            'is_approved' => 'approved', // Langsung approved, bisa diubah nanti
        ]);

        return redirect()->route('prive.index')
            ->with('success', 'Prive berhasil ditambahkan');
    }

    /**
     * Display the specified prive.
     */
    public function show(Prive $prive)
    {
        if ($prive->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        
        return view('prive.show', compact('prive'));
    }

    /**
     * Show the form for editing the specified prive.
     */
    public function edit(Prive $prive)
    {
        if ($prive->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        
        return view('prive.edit', compact('prive'));
    }

    /**
     * Update the specified prive.
     */
    public function update(Request $request, Prive $prive)
    {
        if ($prive->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        
        // Bersihkan amount dari format Rupiah
        $cleanAmount = $this->cleanAmount($request->amount);
        
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'prive_date' => 'required|date',
            'purpose' => 'nullable|string|max:100',
        ], [
            'amount.required' => 'Jumlah harus diisi',
            'amount.numeric' => 'Jumlah harus berupa angka',
            'amount.min' => 'Jumlah minimal 0',
            'description.required' => 'Deskripsi harus diisi',
            'description.max' => 'Deskripsi maksimal 255 karakter',
            'prive_date.required' => 'Tanggal harus diisi',
            'prive_date.date' => 'Format tanggal tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $prive->update([
            'amount' => $cleanAmount,
            'description' => $request->description,
            'prive_date' => $request->prive_date,
            'purpose' => $request->purpose,
        ]);

        return redirect()->route('prive.index')
            ->with('success', 'Prive berhasil diperbarui');
    }

    /**
     * Remove the specified prive.
     */
    public function destroy(Prive $prive)
    {
        if ($prive->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        
        $prive->delete();

        return redirect()->route('prive.index')
            ->with('success', 'Prive berhasil dihapus');
    }

    /**
     * Approve prive (optional)
     */
    public function approve(Prive $prive)
    {
        if ($prive->user_id !== auth()->id()) {
            abort(403);
        }
        
        $prive->update(['is_approved' => 'approved']);
        
        return redirect()->back()
            ->with('success', 'Prive disetujui');
    }

    /**
     * Helper function untuk membersihkan format Rupiah
     */
    private function cleanAmount($amount)
    {
        if (empty($amount)) {
            return 0;
        }
        
        $cleaned = preg_replace('/[^0-9]/', '', $amount);
        return (int) $cleaned;
    }
}