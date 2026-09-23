<?php
/**
 * Data Layer: Berkas products.php
 * Penampung multidimensional array yang menyimpan data komoditas produk
 * (ID, Nama, Kategori, Harga, Stok, Deskripsi).
 */

$products = [
    [
        'id' => 'PRD-001',
        'nama' => 'Laptop ASUS Vivobook 14',
        'kategori' => 'Elektronik',
        'harga' => 8500000,
        'stok' => 5,
        'deskripsi' => 'Intel Core i5-1235U, RAM 8GB, SSD 512GB, Layar 14 FHD'
    ],
    [
        'id' => 'PRD-002',
        'nama' => 'Mouse Wireless Logitech M330',
        'kategori' => 'Aksesoris',
        'harga' => 250000,
        'stok' => 2, // Stok Kritis (< 3)
        'deskripsi' => 'Mouse silent click dengan koneksi wireless 2.4GHz'
    ],
    [
        'id' => 'PRD-003',
        'nama' => 'Keyboard Mekanikal Keychron K2',
        'kategori' => 'Aksesoris',
        'harga' => 1250000,
        'stok' => 8,
        'deskripsi' => 'Keyboard mekanikal wireless Bluetooth dengan Gateron Red Switch'
    ],
    [
        'id' => 'PRD-004',
        'nama' => 'Monitor Gaming LG 24 Inci',
        'kategori' => 'Elektronik',
        'harga' => 1950000,
        'stok' => 1, // Stok Kritis (< 3)
        'deskripsi' => 'Full HD IPS panel, refresh rate 75Hz, AMD FreeSync'
    ],
    [
        'id' => 'PRD-005',
        'nama' => 'Headset Bluetooth Sony WH-CH520',
        'kategori' => 'Audio',
        'harga' => 799000,
        'stok' => 12,
        'deskripsi' => 'Headphone wireless dengan daya tahan baterai hingga 50 jam'
    ],
    [
        'id' => 'PRD-006',
        'nama' => 'Flashdisk SanDisk Ultra 64GB',
        'kategori' => 'Penyimpanan',
        'harga' => 95000,
        'stok' => 0, // Stok Kritis (< 3)
        'deskripsi' => 'Flashdisk USB 3.0 kecepatan transfer hingga 130MB/s'
    ],
    [
        'id' => 'PRD-007',
        'nama' => 'Webcam HD Logitech C920',
        'kategori' => 'Elektronik',
        'harga' => 1100000,
        'stok' => 4,
        'deskripsi' => 'Webcam Full HD 1080p dengan autofokus dan mikrofon stereo'
    ]
];
