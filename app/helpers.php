<?php

if (!function_exists('supabase_asset')) {
    /**
     * Generate public URL for Supabase storage
     *
     * @param string|null $filename
     * @param string $bucket
     * @return string|null
     */
    function supabase_asset($filename, $bucket = 'avatars')
    {
        if (empty($filename)) {
            return null;
        }
        
        $projectRef = env('SUPABASE_PROJECT_REF');
        
        if (!$projectRef) {
            return null;
        }
        
        return "https://{$projectRef}.supabase.co/storage/v1/object/public/{$bucket}/{$filename}";
    }
}

if (!function_exists('upload_to_supabase')) {
    /**
     * Upload file to Supabase Storage
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $bucket
     * @param string $prefix
     * @return string|null
     * @throws \Exception
     */
    function upload_to_supabase($file, $bucket = 'avatars', $prefix = '')
    {
        $projectRef = env('SUPABASE_PROJECT_REF');
        $serviceKey = env('SUPABASE_SERVICE_ROLE_KEY');
        
        if (!$projectRef || !$serviceKey) {
            throw new \Exception('Supabase credentials not configured');
        }
        
        $userId = auth()->id();
        $filename = $prefix . $userId . '_' . time() . '.' . $file->getClientOriginalExtension();
        $fileContent = file_get_contents($file->getRealPath());
        
        // PERBAIKAN: Gunakan withBody untuk mengirim konten file
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $serviceKey,
            'apiKey' => $serviceKey,
            'Content-Type' => $file->getMimeType(),
        ])->withBody($fileContent, $file->getMimeType())
          ->post("https://{$projectRef}.supabase.co/storage/v1/object/{$bucket}/{$filename}");
        
        if (!$response->successful()) {
            throw new \Exception('Upload failed: ' . $response->body());
        }
        
        return $filename;
    }
}

if (!function_exists('delete_from_supabase')) {
    /**
     * Delete file from Supabase Storage
     *
     * @param string $filename
     * @param string $bucket
     * @return bool
     */
    function delete_from_supabase($filename, $bucket = 'avatars')
    {
        if (empty($filename)) {
            return true;
        }
        
        $projectRef = env('SUPABASE_PROJECT_REF');
        $serviceKey = env('SUPABASE_SERVICE_ROLE_KEY');
        
        if (!$projectRef || !$serviceKey) {
            return false;
        }
        
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $serviceKey,
            'apiKey' => $serviceKey,
        ])->delete("https://{$projectRef}.supabase.co/storage/v1/object/{$bucket}/{$filename}");
        
        return $response->status() === 200 || $response->status() === 404;
    }
}