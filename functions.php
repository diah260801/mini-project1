<?php
/**
 * Processing Layer: Berkas functions.php
 * Berisi fungsi hitungTotalNilaiStok() untuk mengalkulasi nilai aset gudang
 * serta logika conditional untuk menyaring warna baris tabel jika stok kritis (< 3).
 */

/**
 * Mengalkulasi total nilai aset gudang (Jumlah dari Harga * Stok seluruh produk)
 *
 * @param array $products Array multidimensi data produk
 * @return float Total nilai aset gudang
 */
function hitungTotalNilaiStok(array $products): float
{
    $totalNilai = 0;
    foreach ($products as $product) {
        $totalNilai += ($product['harga'] * $product['stok']);
    }
    return $totalNilai;
}

/**
 * Memeriksa apakah stok produk berada dalam kondisi kritis (< 3)
 *
 * @param int $stok Jumlah stok produk
 * @return bool True jika stok < 3, false jika sebaliknya
 */
function isStokKritis(int $stok): bool
{
    return $stok < 3;
}

/**
 * Mengembalikan class CSS untuk mewarnai baris tabel berdasarkan kondisi stok
 *
 * @param int $stok Jumlah stok produk
 * @return string Class CSS (misal: 'table-danger' untuk stok kritis, '' jika normal)
 */
function getRowStyleClass(int $stok): string
{
    if (isStokKritis($stok)) {
        return 'table-danger stok-kritis-row';
    }
    return '';
}

/**
 * Menghitung jumlah jenis produk yang stoknya tergolong kritis (< 3)
 *
 * @param array $products Array multidimensi data produk
 * @return int Jumlah item ber-stok kritis
 */
function hitungJumlahStokKritis(array $products): int
{
    $jumlah = 0;
    foreach ($products as $product) {
        if (isStokKritis($product['stok'])) {
            $jumlah++;
        }
    }
    return $jumlah;
}

/**
 * Format angka ke mata uang Rupiah (Rp)
 *
 * @param float $angka Nominal angka
 * @return string Format Rupiah
 */
function formatRupiah(float $angka): string
{
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
