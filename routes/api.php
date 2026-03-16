<?php
// routes/api.php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route dasar untuk testing
Route::get('/ping', function () {
    return response()->json([
        'success' => true,
        'message' => 'pong',
        'time' => now()->toDateTimeString()
    ]);
});

// Route untuk kesehatan API
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'app' => config('app.name'),
        'env' => app()->environment()
    ]);
});

// Tambahkan route API lainnya di sini nanti
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
