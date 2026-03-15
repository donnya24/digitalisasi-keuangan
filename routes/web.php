<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\PriveController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PrivePurposeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\setting\ProfileController;
use App\Http\Controllers\setting\BusinessController;
use App\Http\Controllers\setting\PasswordController;
use App\Http\Controllers\setting\AccountController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleController;

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
| Public Pages (Bisa diakses semua orang, tidak perlu login)
|--------------------------------------------------------------------------
*/
Route::get('/syarat-ketentuan', [PageController::class, 'terms'])->name('terms');
Route::get('/kebijakan-privasi', [PageController::class, 'privacy'])->name('privacy');

/*
|--------------------------------------------------------------------------
| Guest Routes (Hanya untuk yang belum login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Login Routes (custom)
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Google OAuth Routes
    Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Hanya untuk yang sudah login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transactions
    Route::resource('transactions', TransactionController::class);

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export-pdf');
        Route::get('/print', [ReportController::class, 'print'])->name('print');
    });

    // Prive (Pemisahan Uang Pribadi)
    Route::resource('prive', PriveController::class);

    // Categories
    Route::resource('categories', CategoryController::class);

    // Categories AJAX route untuk load kategori berdasarkan tipe
    Route::get('/categories/by-type', function(\Illuminate\Http\Request $request) {
        $categories = App\Models\Category::where('user_id', auth()->id())
            ->where('type', $request->type)
            ->where('is_active', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);
        return response()->json($categories);
    })->name('categories.by-type');

    // Prive Purposes
    Route::resource('prive-purposes', PrivePurposeController::class)->except(['show']);
    Route::put('/prive-purposes/{privePurpose}/toggle', [PrivePurposeController::class, 'toggle'])->name('prive-purposes.toggle');

    // Setting Routes
    Route::prefix('setting')->name('setting.')->group(function () {
        // Halaman utama setting
        Route::get('/{tab?}', [SettingController::class, 'index'])->name('index');

        // API untuk setting (submissions)
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/business', [BusinessController::class, 'update'])->name('business.update');
        Route::put('/password', [PasswordController::class, 'update'])->name('password.update');
        Route::delete('/account', [AccountController::class, 'destroy'])->name('account.destroy');

        // API untuk suggest password
        Route::get('/password/suggest', [PasswordController::class, 'suggest'])->name('password.suggest');
    });

    // Logout Route (custom)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Route debug (opsional, bisa dihapus setelah tidak diperlukan)
Route::get('/debug-business', function() {
    $user = auth()->user();
    $business = $user->business;

    return [
        'user_id' => $user->id,
        'business_exists' => $business ? true : false,
        'business' => $business,
        'business_data' => $business ? [
            'business_name' => $business->business_name,
            'business_type' => $business->business_type,
            'phone' => $business->phone,
            'address' => $business->address,
            'city' => $business->city,
            'province' => $business->province,
            'postal_code' => $business->postal_code,
            'logo' => $business->logo,
        ] : null
    ];
})->middleware('auth');

/*
|--------------------------------------------------------------------------
| Notification Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-read');
    Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead'])->name('mark-read');
    Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    Route::delete('/read/destroy', [NotificationController::class, 'destroyRead'])->name('destroy-read');
    Route::delete('/all/destroy', [NotificationController::class, 'destroyAll'])->name('destroy-all');
    Route::post('/{id}/archive', [NotificationController::class, 'archive'])->name('archive');
    Route::delete('/archived/destroy', [NotificationController::class, 'destroyArchived'])->name('destroy-archived');

    // AJAX route untuk mengambil notifikasi terbaru
    Route::get('/latest', [NotificationController::class, 'latest'])->name('latest');
});

// Include auth routes (register, forgot password, reset password, dll)
require __DIR__.'/auth.php';
