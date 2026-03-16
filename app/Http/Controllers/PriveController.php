<?php

namespace App\Http\Controllers;

use App\Models\Prive;
use App\Models\PrivePurpose;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PriveController extends Controller
{
    /**
     * Display a listing of prive.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Prive::with('purposeModel')
            ->where('user_id', $user->id);

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

        // Filter berdasarkan keperluan
        if ($request->has('purpose_id') && $request->purpose_id) {
            $query->where('purpose_id', $request->purpose_id);
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

        // Ambil bulan untuk filter
        $months = Prive::where('user_id', $user->id)
            ->select(DB::raw("DISTINCT TO_CHAR(prive_date, 'YYYY-MM') as month"))
            ->orderBy('month', 'desc')
            ->pluck('month');

        // Ambil semua keperluan untuk filter
        $purposes = PrivePurpose::where('user_id', $user->id)
            ->where('is_active', 'active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('prive.index', compact(
            'prives',
            'totalPriveBulanIni',
            'totalAllPrive',
            'months',
            'purposes'
        ));
    }

    /**
     * Show the form for creating a new prive.
     */
    public function create()
    {
        $purposes = PrivePurpose::where('user_id', auth()->id())
            ->where('is_active', 'active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('prive.create', compact('purposes'));
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
            'purpose_id' => 'nullable|exists:prive_purposes,id',
            'purpose' => 'nullable|string|max:100',
        ], [
            'amount.required' => 'Jumlah harus diisi',
            'amount.numeric' => 'Jumlah harus berupa angka',
            'amount.min' => 'Jumlah minimal 0',
            'description.required' => 'Deskripsi harus diisi',
            'description.max' => 'Deskripsi maksimal 255 karakter',
            'prive_date.required' => 'Tanggal harus diisi',
            'prive_date.date' => 'Format tanggal tidak valid',
            'purpose_id.exists' => 'Keperluan yang dipilih tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Jika purpose_id diisi, ambil nama dari tabel purposes
        $purposeName = null;
        if ($request->purpose_id) {
            $purpose = PrivePurpose::find($request->purpose_id);
            $purposeName = $purpose ? $purpose->name : null;
        } else {
            $purposeName = $request->purpose;
        }

        // Simpan prive
        $prive = Prive::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'purpose_id' => $request->purpose_id,
            'amount' => $cleanAmount,
            'description' => $request->description,
            'prive_date' => $request->prive_date,
            'purpose' => $purposeName,
            'is_approved' => 'approved',
        ]);

        // ✅ BUAT NOTIFIKASI HANYA SEKALI!
        Notification::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'type' => 'prive',
            'title' => '💸 Prive',
            'message' => 'Anda menarik Rp ' . number_format($prive->amount, 0, ',', '.') . ' untuk ' . ($purposeName ?? 'kebutuhan pribadi'),
            'is_read' => 'unread',
            'icon' => 'hand-holding-usd',
            'bg_color' => 'bg-purple-100',
            'text_color' => 'text-purple-600',
            'data' => json_encode([
                'amount' => $prive->amount,
                'prive_id' => $prive->id,
                'description' => $prive->description,
                'purpose' => $purposeName,
            ]),
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

        $prive->load('purposeModel');

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

        $purposes = PrivePurpose::where('user_id', auth()->id())
            ->where('is_active', 'active')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('prive.edit', compact('prive', 'purposes'));
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
            'purpose_id' => 'nullable|exists:prive_purposes,id',
            'purpose' => 'nullable|string|max:100',
        ], [
            'amount.required' => 'Jumlah harus diisi',
            'amount.numeric' => 'Jumlah harus berupa angka',
            'amount.min' => 'Jumlah minimal 0',
            'description.required' => 'Deskripsi harus diisi',
            'description.max' => 'Deskripsi maksimal 255 karakter',
            'prive_date.required' => 'Tanggal harus diisi',
            'prive_date.date' => 'Format tanggal tidak valid',
            'purpose_id.exists' => 'Keperluan yang dipilih tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Jika purpose_id diisi, ambil nama dari tabel purposes
        $purposeName = null;
        if ($request->purpose_id) {
            $purpose = PrivePurpose::find($request->purpose_id);
            $purposeName = $purpose ? $purpose->name : null;
        } else {
            $purposeName = $request->purpose;
        }

        // Simpan data lama untuk perbandingan
        $oldAmount = $prive->amount;
        $oldPurpose = $prive->purpose;

        // Update prive
        $prive->update([
            'purpose_id' => $request->purpose_id,
            'amount' => $cleanAmount,
            'description' => $request->description,
            'prive_date' => $request->prive_date,
            'purpose' => $purposeName,
        ]);

        // ✅ UPDATE NOTIFIKASI TERKAIT
        $notification = Notification::where('user_id', auth()->id())
            ->where('type', 'prive')
            ->whereRaw("data->>'prive_id' = ?", [$prive->id])
            ->first();

        if ($notification) {
            $notification->update([
                'message' => 'Prive diperbarui: Rp ' . number_format($prive->amount, 0, ',', '.') . ' untuk ' . ($purposeName ?? 'kebutuhan pribadi'),
                'data' => json_encode([
                    'amount' => $prive->amount,
                    'prive_id' => $prive->id,
                    'description' => $prive->description,
                    'purpose' => $purposeName,
                    'old_amount' => $oldAmount,
                    'old_purpose' => $oldPurpose,
                    'updated_at' => now()->toDateTimeString(),
                ]),
            ]);
        }

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

        // ✅ HAPUS NOTIFIKASI TERKAIT
        Notification::where('user_id', auth()->id())
            ->where('type', 'prive')
            ->whereRaw("data->>'prive_id' = ?", [$prive->id])
            ->delete();

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
