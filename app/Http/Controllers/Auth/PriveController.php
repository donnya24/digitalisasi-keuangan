<?php

namespace App\Http\Controllers;

use App\Models\Prive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PriveController extends Controller
{
    public function index(Request $request)
    {
        $query = Prive::where('user_id', auth()->id());

        if ($request->has('month')) {
            $query->whereMonth('prive_date', Carbon::parse($request->month)->month)
                  ->whereYear('prive_date', Carbon::parse($request->month)->year);
        }

        $prives = $query->orderBy('prive_date', 'desc')->paginate(20);

        $totalPrive = Prive::where('user_id', auth()->id())->sum('amount');

        return view('prive.index', compact('prives', 'totalPrive'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'prive_date' => 'required|date',
            'purpose' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Prive::create([
            'user_id' => auth()->id(),
            'amount' => $request->amount,
            'description' => $request->description,
            'prive_date' => $request->prive_date,
            'purpose' => $request->purpose,
            'is_approved' => true
        ]);

        return redirect()->route('prive.index')
            ->with('success', 'Prive berhasil ditambahkan');
    }

    public function update(Request $request, Prive $prive)
    {
        $this->authorize('update', $prive);

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'prive_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $prive->update($request->only(['amount', 'description', 'prive_date', 'purpose']));

        return redirect()->route('prive.index')
            ->with('success', 'Prive berhasil diperbarui');
    }

    public function destroy(Prive $prive)
    {
        $this->authorize('delete', $prive);

        $prive->delete();

        return redirect()->route('prive.index')
            ->with('success', 'Prive berhasil dihapus');
    }
}
