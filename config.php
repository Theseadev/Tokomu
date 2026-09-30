<?php

/**
 * Tokomu POS - Konfigurasi Database
 * 
 * Pengaturan koneksi database:
 * - 'auto'   : Otomatis mendeteksi MySQL InfinityFree saat dihosting.
 *              Jika sedang dijalankan lokal/offline, otomatis fallback ke SQLite.
 * - 'mysql'  : Memaksa koneksi ke MySQL.
 * - 'sqlite' : Memaksa koneksi ke SQLite lokal (data/warung.db).
 */

return [
    // Mode koneksi ('auto', 'mysql', atau 'sqlite')
    'db_connection' => 'auto',

    // Konfigurasi MySQL (InfinityFree)
    'mysql' => [
        'host'     => 'sql107.infinityfree.com',
        'database' => 'if0_39237979_Tokomu',
        'username' => 'if0_39237979',
        'password' => 'Fahrul200505',
        'port'     => 3306,
        'charset'  => 'utf8mb4'
    ],

    // Konfigurasi SQLite (Lokal Laragon)
    'sqlite' => [
        'database' => __DIR__ . '/data/warung.db'
    ]
];
