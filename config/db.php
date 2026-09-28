<?php
/**
 * Configuration Layer: db.php
 * Mengelola koneksi database via PDO, manajemen session, CSRF token,
 * dan fungsi penolong (helper functions) untuk keamanan aplikasi.
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
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
        $pdo = new PDO($dsn, $dbUser, $dbPass);
    }

    // Mengatur Mode Error PDO ke Exception dan Fetch default ke Associative Array
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    // Jika koneksi gagal, tampilkan pesan error yang rapi
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
