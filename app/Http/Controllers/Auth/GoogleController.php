<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleController extends Controller
{
    /**
     * Redirect ke Google untuk autentikasi
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->redirect();
    }

    /**
     * Handle callback dari Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = $googleUser->getEmail();
            
            // CEK APAKAH EMAIL SUDAH TERDAFTAR DI DATABASE
            $existingUser = User::where('email', $email)->first();
            
            if (!$existingUser) {
                // EMAIL TIDAK TERDAFTAR - Kembali ke login dengan pesan error
                return redirect()->route('login')
                    ->with('error', 'Email ' . $email . ' belum terdaftar. Silakan registrasi terlebih dahulu.');
            }
            
            // EMAIL TERDAFTAR - Update data Google dan login
            if (!$existingUser->google_id) {
                $existingUser->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'last_login' => now(),
                ]);
            } else {
                $existingUser->update([
                    'last_login' => now(),
                ]);
            }
            
            // Login user
            Auth::login($existingUser);
            
            return redirect()->intended('/dashboard')
                ->with('success', 'Selamat datang kembali, ' . $existingUser->name . '!');
            
        } catch (Exception $e) {
            Log::error('Google Login Error: ' . $e->getMessage());
            
            return redirect()->route('login')
                ->with('error', 'Gagal login dengan Google. Silakan coba lagi.');
        }
    }
}