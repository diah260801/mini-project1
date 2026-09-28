<?php
/**
 * Action Layer: public/delete.php
 * Menangani penghapusan produk dengan keamanan POST Method + CSRF Token + PDO Prepared Statements.
 */

require_once __DIR__ . '/../config/db.php';

// Syarat Wajib Slide: Metode penyerahan HANYA boleh POST (Mencegah serangan via GET request)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('danger', 'Metode permintaan tidak diizinkan!');
    header('Location: index.php');
    exit;
}

// Syarat Wajib Slide: Verifikasi Token CSRF
$csrfToken = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($csrfToken)) {
    set_flash('danger', 'Token CSRF tidak valid atau sesi telah berakhir! Permintaan ditolak.');
    header('Location: index.php');
    exit;
}

// Validasi ID Produk
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    set_flash('danger', 'ID produk tidak valid!');
    header('Location: index.php');
    exit;
}

try {
    // Ambil nama produk terlebih dahulu untuk pesan konfirmasi flash
    $stmtFind = $pdo->prepare("SELECT name FROM products WHERE id = :id");
    $stmtFind->execute([':id' => $id]);
    $productName = $stmtFind->fetchColumn();

    if ($productName) {
        // Syarat Wajib Slide: DELETE -> $pdo->prepare(...)
        $stmtDelete = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmtDelete->execute([':id' => $id]);

        set_flash('success', "Produk '" . e($productName) . "' berhasil dihapus!");
    } else {
        set_flash('warning', "Produk tidak ditemukan atau telah dihapus sebelumnya.");
    }

} catch (PDOException $e) {
    set_flash('danger', "Terjadi kesalahan saat menghapus produk: " . $e->getMessage());
}

// Post-Redirect-Get (PRG) Redirect ke halaman utama
header('Location: index.php');
exit;
