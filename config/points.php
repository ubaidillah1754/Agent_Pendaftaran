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
    | Prefix Referensi
    |--------------------------------------------------------------------------
    | Format prefix untuk nomor transaksi ledger poin dan kode redemption.
    */
    'reference_prefix' => [
        'earn'       => 'EARN',
        'redeem'     => 'RED',
        'adjustment' => 'ADJ',
        'reversal'   => 'REV',
    ],
];
