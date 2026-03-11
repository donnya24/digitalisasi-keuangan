<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update data dasar
        $user->name = $request->name;
        $user->phone = $request->phone;

        // Handle avatar upload ke Supabase
        if ($request->hasFile('avatar')) {
            try {
                // Hapus avatar lama jika ada
                if ($user->avatar) {
                    delete_from_supabase($user->avatar, 'avatars');
                }
                
                // Upload file baru
                $filename = upload_to_supabase($request->file('avatar'), 'avatars', 'avatar_');
                
                // Simpan nama file ke database
                $user->avatar = $filename;
                
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Gagal mengupload avatar: ' . $e->getMessage())
                    ->withInput();
            }
        }

        $user->save();

        return redirect()->route('setting.index', ['tab' => 'profile'])
            ->with('success', 'Profil berhasil diperbarui');
    }
}