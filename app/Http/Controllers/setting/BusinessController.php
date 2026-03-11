<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BusinessController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:255',
            'business_type' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20|regex:/^[0-9+\-\s]+$/',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10|regex:/^[0-9]+$/',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = [
            'business_name' => $request->business_name,
            'business_type' => $request->business_type,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
        ];

        // Handle logo upload jika ada
        if ($request->hasFile('logo')) {
            try {
                $business = $user->business;
                
                // Hapus logo lama jika ada
                if ($business && $business->logo) {
                    delete_from_supabase($business->logo, 'logos');
                }
                
                // Upload file baru (asumsi helper sudah ada)
                $filename = upload_to_supabase($request->file('logo'), 'logos', 'logo_');
                $data['logo'] = $filename;
                
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Gagal mengupload logo: ' . $e->getMessage())
                    ->withInput();
            }
        }

        // Update atau create business
        Business::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return redirect()->route('setting.index', ['tab' => 'business'])
            ->with('success', 'Profil usaha berhasil diperbarui');
    }
}