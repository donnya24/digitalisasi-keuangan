<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    protected $redirectTo = '/dashboard';
    protected $maxAttempts = 5;
    protected $decayMinutes = 1;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
{
    // Validasi input
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
        'password' => 'required|string|min:8',
    ], [
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email harus valid (contoh: nama@domain.com)',
        'email.exists' => 'Email tidak terdaftar dalam sistem',
        'password.required' => 'Password wajib diisi',
        'password.min' => 'Password minimal 8 karakter',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput($request->except('password'));
    }

    // Cek rate limiting
    if ($this->hasTooManyLoginAttempts($request)) {
        $this->fireLockoutEvent($request);
        
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($request)
        );

        return redirect()->back()
            ->withErrors([
                'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ])
            ->withInput($request->except('password'));
    }

    // Cek status user aktif (ENUM)
    $user = User::where('email', $request->email)->first();
    
    if ($user && $user->is_active !== 'active') {
        $statusMessages = [
            'inactive' => 'Akun Anda belum aktif. Silakan cek email untuk verifikasi.',
            'suspended' => 'Akun Anda telah diblokir. Silakan hubungi admin.',
        ];
        
        $message = $statusMessages[$user->is_active] ?? 'Akun tidak dapat digunakan.';
        
        return redirect()->back()
            ->withErrors(['email' => $message])
            ->withInput($request->except('password'));
    }

    // Attempt login
    $credentials = $request->only('email', 'password');
    $remember = $request->boolean('remember');

    if (Auth::attempt($credentials, $remember)) {
        $this->clearLoginAttempts($request);
        $request->session()->regenerate();

        $user = Auth::user();
        $user->last_login = now();
        $user->save();

        return redirect()->intended($this->redirectTo)
            ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
    }

    $this->incrementLoginAttempts($request);

    return redirect()->back()
        ->withErrors(['password' => 'Password yang Anda masukkan salah.'])
        ->withInput($request->except('password'));
}

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Anda berhasil logout');
    }
}
