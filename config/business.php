<?php

// config/business.php

return [
    /*
    |--------------------------------------------------------------------------
    | Target Laba Bulanan
    |--------------------------------------------------------------------------
    |
    | Target laba yang ingin dicapai setiap bulan (dalam Rupiah)
    |
    */
    'target_profit' => env('TARGET_PROFIT', 10000000),

    /*
    |--------------------------------------------------------------------------
    | Cache Duration
    |--------------------------------------------------------------------------
    |
    | Durasi cache dalam detik untuk berbagai jenis data
    |
    */
    'cache' => [
        'quick_stats' => 300,      // 5 menit untuk statistik harian
        'charts' => 600,           // 10 menit untuk grafik
        'balance' => 900,           // 15 menit untuk saldo
    ],

    /*
    |--------------------------------------------------------------------------
    | Alert Thresholds
    |--------------------------------------------------------------------------
    |
    | Batas-batas untuk memicu notifikasi
    |
    */
    'alerts' => [
        'low_balance' => 500000,   // Saldo menipis jika kurang dari Rp 500.000
        'expense_multiplier' => 1.5, // Pengeluaran besar jika > 1.5x rata-rata
    ],
];