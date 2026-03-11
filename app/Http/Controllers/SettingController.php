<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Muat ulang relasi business untuk mendapatkan data terbaru
        $user->load('business');
        
        $business = $user->business;
        
        // Jika belum ada data, buat objek kosong (untuk form)
        if (!$business) {
            $business = new Business();
            $business->business_name = '';
            $business->business_type = '';
            $business->phone = '';
            $business->address = '';
            $business->city = '';
            $business->province = '';
            $business->postal_code = '';
            $business->logo = null;
        }
        
        $activeTab = $request->get('tab', session('setting_tab', 'profile'));
        session(['setting_tab' => $activeTab]);
        
        return view('setting.index', compact('user', 'business', 'activeTab'));
    }
}