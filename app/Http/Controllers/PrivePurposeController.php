<?php

namespace App\Http\Controllers;

use App\Models\PrivePurpose;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PrivePurposeController extends Controller
{
    /**
     * Display a listing of prive purposes.
     */
    public function index()
    {
        $user = auth()->user();
        
        $purposes = PrivePurpose::where('user_id', $user->id)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        
        return view('prive-purposes.index', compact('purposes'));
    }

    /**
     * Show the form for creating a new purpose.
     */
    public function create()
    {
        return view('prive-purposes.create');
    }

    /**
     * Store a newly created purpose.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Nama keperluan harus diisi',
            'name.max' => 'Nama maksimal 100 karakter',
            'sort_order.integer' => 'Urutan harus berupa angka',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Cek duplikasi nama
        $exists = PrivePurpose::where('user_id', $user->id)
            ->where('name', $request->name)
            ->exists();
        
        if ($exists) {
            return redirect()->back()
                ->with('error', 'Nama keperluan sudah ada')
                ->withInput();
        }

        PrivePurpose::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'name' => $request->name,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => 'active',
        ]);

        return redirect()->route('prive-purposes.index')
            ->with('success', 'Keperluan prive berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified purpose.
     */
    public function edit(PrivePurpose $privePurpose)
    {
        if ($privePurpose->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        
        return view('prive-purposes.edit', compact('privePurpose'));
    }

    /**
     * Update the specified purpose.
     */
    public function update(Request $request, PrivePurpose $privePurpose)
    {
        if ($privePurpose->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|in:active,inactive',
        ], [
            'name.required' => 'Nama keperluan harus diisi',
            'name.max' => 'Nama maksimal 100 karakter',
            'sort_order.integer' => 'Urutan harus berupa angka',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Cek duplikasi nama (kecuali dirinya sendiri)
        $exists = PrivePurpose::where('user_id', auth()->id())
            ->where('name', $request->name)
            ->where('id', '!=', $privePurpose->id)
            ->exists();
        
        if ($exists) {
            return redirect()->back()
                ->with('error', 'Nama keperluan sudah ada')
                ->withInput();
        }

        $privePurpose->update([
            'name' => $request->name,
            'description' => $request->description,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->is_active ?? 'active',
        ]);

        return redirect()->route('prive-purposes.index')
            ->with('success', 'Keperluan prive berhasil diperbarui');
    }

    /**
     * Remove the specified purpose.
     */
    public function destroy(PrivePurpose $privePurpose)
    {
        if ($privePurpose->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        
        // Cek apakah sudah digunakan di prive
        if ($privePurpose->prives()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Keperluan ini masih digunakan di ' . $privePurpose->prives()->count() . ' data prive');
        }
        
        $privePurpose->delete();

        return redirect()->route('prive-purposes.index')
            ->with('success', 'Keperluan prive berhasil dihapus');
    }

    /**
     * Toggle active status
     */
    public function toggle(PrivePurpose $privePurpose)
    {
        if ($privePurpose->user_id !== auth()->id()) {
            abort(403);
        }
        
        $newStatus = $privePurpose->is_active === 'active' ? 'inactive' : 'active';
        $privePurpose->update(['is_active' => $newStatus]);
        
        $message = $newStatus === 'active' ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()
            ->with('success', "Keperluan prive berhasil {$message}");
    }
}