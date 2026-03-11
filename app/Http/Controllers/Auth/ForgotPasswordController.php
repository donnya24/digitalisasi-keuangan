<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    /**
     * Show the form to request a password reset link.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Kirim reset link menggunakan Password facade [citation:1][citation:2]
        $response = Password::sendResetLink(
            $request->only('email')
        );

        // Cek response
        if ($response === Password::RESET_LINK_SENT) {
            return redirect()->back()
                ->with('status', 'Kami telah mengirimkan link reset password ke email Anda.');
        }

        // Jika gagal (email tidak ditemukan, dll), tetap beri pesan sukses untuk keamanan [citation:1]
        return redirect()->back()
            ->with('status', 'Jika email terdaftar, link reset akan dikirim.');
    }
}
