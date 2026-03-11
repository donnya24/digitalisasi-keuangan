<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    /**
     * Change user password and logout.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
        ], [
            'new_password.required' => 'Password baru harus diisi',
            'new_password.min' => 'Password baru minimal 8 karakter',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok',
            'new_password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan karakter khusus (@$!%*?&)',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        // Logout user
        Auth::logout();

        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to login with success message
        return redirect()->route('login')
            ->with('success', 'Password berhasil diubah. Silakan login kembali dengan password baru Anda.');
    }

    /**
     * Generate suggested password.
     */
    public function suggest()
    {
        $length = 12;
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $special = '@$!%*?&';
        
        $all = $uppercase . $lowercase . $numbers . $special;
        
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];
        
        for ($i = 4; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }
        
        return response()->json([
            'password' => str_shuffle($password)
        ]);
    }
}