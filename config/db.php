<?php
/**
 * Configuration Layer: db.php
 * Mengelola koneksi database via PDO, manajemen session, CSRF token,
 * dan fungsi penolong (helper functions) untuk keamanan aplikasi.
 * 
 * Fitur Tambahan: Otomatis membuat database 'store_db' dan skema tabel 
 * jika database belum dibuat di MySQL/phpMyAdmin.
 */

// Memastikan Session Aktif untuk CSRF dan Flash Messages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi Database (Dapat disesuaikan via environment variable)
$dbDriver = getenv('DB_DRIVER') ?: 'mysql';
$dbHost   = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort   = getenv('DB_PORT') ?: '3306';
$dbName   = getenv('DB_NAME') ?: 'store_db';
$dbUser   = getenv('DB_USER') ?: 'root';
$dbPass   = getenv('DB_PASS') ?: '';
$sqlitePath = getenv('SQLITE_PATH') ?: __DIR__ . '/../database/store_db.sqlite';

try {
    if ($dbDriver === 'sqlite' || (file_exists($sqlitePath) && !getenv('DB_HOST'))) {
        $dsn = "sqlite:" . $sqlitePath;
        $pdo = new PDO($dsn);
    } else {
        try {
            $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
            $pdo = new PDO($dsn, $dbUser, $dbPass);
        } catch (PDOException $e) {
            // Menangani Error 1049 (Unknown database 'store_db') secara otomatis
            if ($e->getCode() == 1049 || str_contains($e->getMessage(), '1049') || str_contains(strtolower($e->getMessage()), 'unknown database')) {
                // Koneksi ke server MySQL tanpa dbname terlebih dahulu
                $pdoRoot = new PDO("mysql:host={$dbHost};port={$dbPort};charset=utf8mb4", $dbUser, $dbPass);
                $pdoRoot->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Buat Database store_db otomatis
                $pdoRoot->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdoRoot->exec("USE `{$dbName}`");
                
                // Import file SQL skema tabel dan data awal
                $sqlPath = __DIR__ . '/../database/store_db.sql';
                if (file_exists($sqlPath)) {
                    $sqlContent = file_get_contents($sqlPath);
                    $pdoRoot->exec($sqlContent);
                }
                
                // Koneksi ulang ke database yang baru dibuat
                $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
                $pdo = new PDO($dsn, $dbUser, $dbPass);
            } else {
                throw $e;
            }
        }
    }

    // Mengatur Mode Error PDO ke Exception dan Fetch default ke Associative Array
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    // Jika koneksi gagal total (misal MySQL belum dinyalakan di XAMPP)
    die("Koneksi Database Gagal: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

/**
 * Helper: Escaping Output untuk Mencegah Cross-Site Scripting (XSS)
 * Syarat Slide: htmlspecialchars($product['nama'], ENT_QUOTES, 'UTF-8')
 */
function e(?string $value): string {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Helper: Normalisasi & Sanitasi Input String
 */
function sanitize_input(?string $data): string {
    return trim($data ?? '');
}

/**
 * Helper: Membuat CSRF Token Baru
 */
function generate_csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Helper: Verifikasi Token CSRF
 */
function verify_csrf_token(?string $token): bool {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Helper: Memberikan Flash Message (Pesan Sementara untuk User)
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

/**
 * Helper: Mengambil dan Menghapus Flash Message
 */
function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
