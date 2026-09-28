<?php
/**
 * Presentation Layer: public/index.php
 * Halaman utama untuk READ & SEARCH data produk.
 * Menggunakan Flexbox / Grid Responsive Cards dan PDO Prepared Statements.
 */

require_once __DIR__ . '/../config/db.php';

// Ambil parameter pencarian dari query GET (Slide 18 - Bonus Search Feature)
$q = sanitize_input($_GET['q'] ?? '');

if ($q !== '') {
    // Parameterized search query aman dari SQL Injection
    $sql = "SELECT * FROM products WHERE name LIKE :name_search OR category LIKE :category_search ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name_search' => "%{$q}%",
        ':category_search' => "%{$q}%",
    ]);
} else {
    // Read seluruh produk
    $sql = "SELECT * FROM products ORDER BY id DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
}

$products = $stmt->fetchAll();

// Kalkulasi statistik inventaris
$totalProduk = count($products);
$totalNilaiAset = 0;
$totalStokKritis = 0;

foreach ($products as $p) {
    $totalNilaiAset += ($p['price'] * $p['stock']);
    if ($p['stock'] < 5) {
        $totalStokKritis++;
    }
}

// Generate Token CSRF untuk form Delete
$csrfToken = generate_csrf_token();
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Manager — Inventaris Produk</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <!-- Header / Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom py-3">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <i class="fa-solid fa-boxes-stacked me-2 text-warning fs-4"></i>
                <span>Product Manager</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                <a href="create.php" class="btn btn-warning fw-semibold px-3 rounded-3 shadow-sm d-flex align-items-center gap-2">
                    <i class="fa-solid fa-plus"></i>
                    <span>Tambah Produk</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-4">

        <!-- Flash Message Notification -->
        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']); ?> alert-dismissible fade show rounded-3 shadow-sm mb-4" role="alert">
                <i class="fa-solid <?= $flash['type'] === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation'; ?> me-2"></i>
                <?= e($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Dashboard Summary Stats -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card p-3 d-flex align-items-center">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Jenis Produk</div>
                        <div class="fs-4 fw-bold text-dark"><?= number_format($totalProduk, 0, ',', '.'); ?> SKU</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card p-3 d-flex align-items-center">
                    <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Total Nilai Aset</div>
                        <div class="fs-4 fw-bold text-dark">Rp <?= number_format($totalNilaiAset, 0, ',', '.'); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card p-3 d-flex align-items-center">
                    <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold text-uppercase">Stok Kritis (< 5)</div>
                        <div class="fs-4 fw-bold text-dark"><?= $totalStokKritis; ?> Produk</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Bar & Title Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-1">Daftar Produk Inventaris</h3>
                <p class="text-muted small mb-0">Kelola katalog produk, pantau stok, dan perbarui data dengan aman.</p>
            </div>
            
            <!-- Search Form (GET Method) -->
            <form action="index.php" method="GET" class="search-box" style="min-width: 280px;">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="q" class="form-control form-control-lg fs-6" 
                       placeholder="Cari nama atau kategori..." value="<?= e($q); ?>">
                <?php if ($q !== ''): ?>
                    <a href="index.php" class="btn btn-sm btn-light position-absolute end-0 top-50 translate-middle-y me-2 text-muted" title="Reset">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Notification search filter -->
        <?php if ($q !== ''): ?>
            <div class="mb-3">
                <span class="badge bg-secondary px-3 py-2 fs-6 fw-normal rounded-3">
                    Hasil pencarian untuk: <strong>"<?= e($q); ?>"</strong>
                    <a href="index.php" class="text-white ms-2"><i class="fa-solid fa-times"></i></a>
                </span>
            </div>
        <?php endif; ?>

        <!-- Product Cards Container (Flexbox & Grid Responsive) -->
        <?php if (count($products) > 0): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($products as $product): ?>
                    <?php 
                        $isKritis = $product['stock'] < 5;
                        $statusClass = $isKritis ? 'stok-kritis' : 'stok-aman';
                    ?>
                    <div class="col">
                        <div class="product-card <?= $statusClass; ?> p-4">
                            <div>
                                <!-- Category & Header -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="category-badge"><?= e($product['category']); ?></span>
                                    <span class="badge badge-stock <?= $isKritis ? 'badge-kritis' : 'badge-aman'; ?>">
                                        <i class="fa-solid <?= $isKritis ? 'fa-triangle-exclamation' : 'fa-circle-check'; ?> me-1"></i>
                                        Stok: <?= (int)$product['stock']; ?>
                                    </span>
                                </div>
                                
                                <!-- Product Name -->
                                <h5 class="fw-bold mb-3 text-dark text-truncate" title="<?= e($product['name']); ?>">
                                    <?= e($product['name']); ?>
                                </h5>
                                
                                <!-- Price -->
                                <div class="mb-3">
                                    <div class="text-muted extra-small uppercase fw-semibold">Harga Satuan</div>
                                    <div class="price-tag">Rp <?= number_format($product['price'], 0, ',', '.'); ?></div>
                                </div>
                            </div>

                            <!-- Actions Footer (Edit & Delete via POST + CSRF) -->
                            <div class="pt-3 border-top d-flex align-items-center justify-content-between gap-2 mt-3">
                                <a href="edit.php?id=<?= (int)$product['id']; ?>" class="btn btn-outline-primary btn-sm rounded-2 flex-grow-1 d-flex align-items-center justify-content-center gap-1">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>Edit</span>
                                </a>
                                
                                <!-- Form Delete dengan Method POST + Token CSRF (Syarat Wajib Slide) -->
                                <form action="delete.php" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                                    <input type="hidden" name="csrf_token" value="<?= e($csrfToken); ?>">
                                    <input type="hidden" name="id" value="<?= (int)$product['id']; ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-2 d-flex align-items-center justify-content-center gap-1">
                                        <i class="fa-solid fa-trash"></i>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fa-solid fa-box-open"></i>
                </div>
                <h4 class="fw-bold">Tidak ada produk ditemukan</h4>
                <p class="text-muted">
                    <?= $q !== '' ? 'Tidak ada produk yang cocok dengan kata kunci pencarian Anda.' : 'Belum ada produk yang tersimpan dalam sistem.'; ?>
                </p>
                <div class="mt-3">
                    <?php if ($q !== ''): ?>
                        <a href="index.php" class="btn btn-secondary px-4 me-2">Lihat Semua Produk</a>
                    <?php endif; ?>
                    <a href="create.php" class="btn btn-warning px-4">Tambah Produk Baru</a>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
