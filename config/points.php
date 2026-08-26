<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Nilai Poin Default
    |--------------------------------------------------------------------------
    | Jumlah poin yang diperoleh karyawan ketika berhasil mendaftarkan
    | seorang pasien baru yang valid ke dalam sistem.
    */
    'earn_per_new_patient' => env('POINTS_PER_NEW_PATIENT', 10),

    /*
    |--------------------------------------------------------------------------
    | Tukar Poin → Uang Tunai (Cash Redemption)
    |--------------------------------------------------------------------------
    | cash_rate_per_point : Nilai rupiah per 1 poin (default: Rp 1.000)
    | cash_min_points     : Minimum poin yang bisa ditukar ke cash (default: 100 poin = Rp 100.000)
    | cash_max_points     : Maksimum poin yang bisa ditukar per pengajuan (default: 5.000 poin)
    */
    'cash_rate_per_point' => env('POINTS_CASH_RATE_PER_POINT', 1000),   // 1 poin = Rp 1.000
    'cash_min_points'     => env('POINTS_CASH_MIN', 100),                // Minimum 100 poin (= Rp 100.000)
    'cash_max_points'     => env('POINTS_CASH_MAX', 5000),               // Maksimum 5.000 poin per pengajuan

    /*
    |--------------------------------------------------------------------------
    | Prefix Referensi
    |--------------------------------------------------------------------------
    | Format prefix untuk nomor transaksi ledger poin dan kode redemption.
    */
    'reference_prefix' => [
        'earn'       => 'EARN',
        'redeem'     => 'RED',
        'cash'       => 'CASH',
        'adjustment' => 'ADJ',
        'reversal'   => 'REV',
    ],
];

