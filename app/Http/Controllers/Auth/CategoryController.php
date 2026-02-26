<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        $incomeCategories = Category::where('user_id', auth()->id())
            ->where('type', 'pemasukan')
            ->where('is_active', true)
            ->get();

        $expenseCategories = Category::where('user_id', auth()->id())
            ->where('type', 'pengeluaran')
            ->where('is_active', true)
            ->get();

        return view('categories.index', compact('incomeCategories', 'expenseCategories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'type' => 'required|in:pemasukan,pengeluaran',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Cek apakah kategori dengan nama yang sama sudah ada
        $exists = Category::where('user_id', auth()->id())
            ->where('name', $request->name)
            ->where('type', $request->type)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->with('error', 'Kategori dengan nama tersebut sudah ada')
                ->withInput();
        }

        Category::create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'type' => $request->type,
            'icon' => $request->icon ?? 'tag',
            'color' => $request->color ?? '#6B7280',
            'description' => $request->description,
            'is_active' => true
        ]);

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'description' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $category->update($request->only(['name', 'icon', 'color', 'description']));

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        // Cek apakah kategori memiliki transaksi
        if ($category->transactions()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Kategori tidak dapat dihapus karena masih memiliki transaksi');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Kategori berhasil dihapus');
    }
}
