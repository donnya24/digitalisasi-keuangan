<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request; 
use App\Http\Controllers\PriveController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PageController; 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root berdasarkan status login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Guest Routes (Hanya untuk yang belum login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Google OAuth Routes
    Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

    // Register Routes (dari auth.php bawaan Laravel)
    // Sudah include di require __DIR__.'/auth.php';
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Hanya untuk yang sudah login)
|--------------------------------------------------------------------------
*/
// Categories AJAX route untuk load kategori berdasarkan tipe
/*
|--------------------------------------------------------------------------
| Notification Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::post('/mark-all-fread', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::post('/{id}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::delete('/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
    Route::delete('/read/destroy', [App\Http\Controllers\NotificationController::class, 'destroyRead'])->name('destroy-read');
    Route::post('/{id}/archive', [App\Http\Controllers\NotificationController::class, 'archive'])->name('archive');
    Route::delete('/archived/destroy', [App\Http\Controllers\NotificationController::class, 'destroyArchived'])->name('destroy-archived');
    Route::delete('/all/destroy', [App\Http\Controllers\NotificationController::class, 'destroyAll'])->name('destroy-all');
    
    // AJAX route untuk mengambil notifikasi terbaru
    Route::get('/latest', [App\Http\Controllers\NotificationController::class, 'latest'])->name('latest');
});

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transactions
    Route::resource('transactions', TransactionController::class);

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export-pdf');
    });

    // Prive (Pemisahan Uang Pribadi)
    Route::resource('prive', PriveController::class);

    // Categories
    Route::resource('categories', CategoryController::class);

    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/business', [ProfileController::class, 'updateBusiness'])->name('update-business');
        Route::put('/', [ProfileController::class, 'updateProfile'])->name('update');
        Route::put('/password', [ProfileController::class, 'changePassword'])->name('change-password');
    });

    // Logout Route
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Include auth routes dari Laravel Breeze (register, forgot password, reset password, etc)
require __DIR__.'/auth.php';

/*
|--------------------------------------------------------------------------
| Public Pages (Bisa diakses semua orang, tidak perlu login)
|--------------------------------------------------------------------------
*/
Route::get('/syarat-ketentuan', [PageController::class, 'terms'])->name('terms');
Route::get('/kebijakan-privasi', [PageController::class, 'privacy'])->name('privacy');