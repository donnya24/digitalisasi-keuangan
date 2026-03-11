<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AccountController extends Controller
{
    /**
     * Show account information (read-only).
     */
    public function index()
    {
        // This is just for reference, actual data is passed via main controller
        return redirect()->route('setting.index', ['tab' => 'account']);
    }

    /**
     * Delete account.
     */
    public function destroy(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'password' => 'required|current_password',
        ], [
            'password.required' => 'Password harus diisi',
            'password.current_password' => 'Password salah',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        try {
            // Hapus avatar dari Supabase jika ada
            if ($user->avatar) {
                $this->deleteFromSupabase($user->avatar, 'avatars');
            }

            // Hapus logo bisnis dari Supabase jika ada
            if ($user->business && $user->business->logo) {
                $this->deleteFromSupabase($user->business->logo, 'logos');
            }
        } catch (\Exception $e) {
            Log::error('Failed to delete files from Supabase', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
            // Continue with account deletion even if file deletion fails
        }

        // Logout sebelum delete
        Auth::logout();
        
        // Hapus user (akan cascade ke business & related data)
        $user->delete();

        return redirect('/')->with('success', 'Akun Anda telah dihapus');
    }

    /**
     * Delete file from Supabase Storage.
     */
    private function deleteFromSupabase($filename, $bucket)
    {
        $projectRef = env('SUPABASE_PROJECT_REF');
        $serviceKey = env('SUPABASE_SERVICE_ROLE_KEY');
        
        if (!$projectRef || !$serviceKey) {
            throw new \Exception('Supabase credentials not configured');
        }
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $serviceKey,
            'apiKey' => $serviceKey,
        ])->delete("https://{$projectRef}.supabase.co/storage/v1/object/{$bucket}/{$filename}");
        
        if ($response->status() >= 400 && $response->status() != 404) {
            throw new \Exception('Delete failed: ' . $response->body());
        }
        
        return true;
    }
}