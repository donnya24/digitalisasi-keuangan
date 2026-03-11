<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email'   => 'Format email tidak valid',
            'password.required' => 'Password wajib diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        // Cek rate limiting (max 5 attempt per menit)
        $throttleKey = $this->throttleKey($request);
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', ['seconds' => $seconds]),
            ])->redirectTo(route('login'));
        }

        // Cek kredensial
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            // Hapus percobaan yang gagal jika login sukses
            RateLimiter::clear($throttleKey);

            // Regenerasi session
            $request->session()->regenerate();

            // Update last login
            $user = Auth::user();
            $user->last_login = now();
            $user->save();

            return redirect()->intended('/dashboard')
                ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
        }

        // Jika login gagal, increment hitungan percobaan
        RateLimiter::hit($throttleKey);

        // Kembalikan error
        return redirect()->back()
            ->withErrors(['password' => 'Email atau password salah.'])
            ->withInput($request->except('password'));
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Anda berhasil logout');
    }

    /**
     * Get the throttle key for the given request.
     */
    protected function throttleKey(Request $request)
    {
        return strtolower($request->input('email')) . '|' . $request->ip();
    }
}
