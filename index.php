<?php
/**
 * Presentation Layer: Berkas index.php
 * Merajut seluruh komponen menggunakan require_once dan merender data
 * ke layout tabel HTML via perulangan foreach.
 */

// Import komponen Data Layer dan Processing Layer
require_once 'products.php';
require_once 'functions.php';

// Kalkulasi nilai aset & statistik menggunakan fungsi pada Processing Layer
$totalNilaiAset = hitungTotalNilaiStok($products);
$totalJenisProduk = count($products);
$totalStokKritis = hitungJumlahStokKritis($products);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Information System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }
        .table-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .stok-kritis-row {
            background-color: #ffe6e6 !important;
        }
        .stok-kritis-row:hover {
            background-color: #ffcccc !important;
        }
        .badge-kritis {
            background-color: #dc3545;
            color: #fff;
            animation: pulse 2s infinite;
        }
        .badge-aman {
            background-color: #198754;
            color: #fff;
        }
    </style>
</head>
<body>

    <!-- Header / Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fa-solid fa-boxes-stacked me-2 text-warning"></i>
                Product Information System
            </a>
            <span class="navbar-text text-light fs-6">
                Mini Project PHP — 3-Tier Architecture
            </span>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-4">
        
        <!-- Hero Title -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h2 class="fw-bold text-dark mb-1">Manajemen & Informasi Produk Gudang</h2>
                <p class="text-muted">Sistem monitoring inventaris produk, kalkulasi nilai aset, dan pemantauan stok kritis.</p>
            </div>
            <div class="col-md-4 text-md-end d-flex align-items-center justify-content-md-end">
                <span class="badge bg-secondary p-2 me-2"><i class="fa-solid fa-layer-group me-1"></i> 3-Tier Concept</span>
                <span class="badge bg-primary p-2"><i class="fa-brands fa-php me-1"></i> PHP Native</span>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card stat-card p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-primary-subtle text-primary me-3">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Total Jenis Produk</span>
                            <h3 class="fw-bold mb-0 text-dark"><?= $totalJenisProduk ?> Item</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-success-subtle text-success me-3">
                            <i class="fa-solid fa-sack-dollar"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Total Nilai Aset Gudang</span>
                            <h3 class="fw-bold mb-0 text-success"><?= formatRupiah($totalNilaiAset) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-danger-subtle text-danger me-3">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Produk Stok Kritis (&lt; 3)</span>
                            <h3 class="fw-bold mb-0 text-danger"><?= $totalStokKritis ?> Item</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Data Produk -->
        <div class="card table-card bg-white">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-list me-2"></i>Daftar Informasi Produk</h5>
                <div class="small">
                    <span class="d-inline-block me-3"><span class="badge bg-danger p-1 me-1">&nbsp;</span> Baris Merah: Stok Kritis (&lt; 3)</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" style="width: 60px;">No</th>
                                <th style="width: 110px;">ID Produk</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th class="text-end">Harga Satuan</th>
                                <th class="text-center" style="width: 100px;">Stok</th>
                                <th class="text-end">Subtotal Aset</th>
                                <th>Deskripsi</th>
                                <th class="text-center" style="width: 130px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            // Presentation Layer: Render data produk menggunakan perulangan foreach
                            foreach ($products as $product) : 
                                $subtotal = $product['harga'] * $product['stok'];
                                $rowClass = getRowStyleClass($product['stok']);
                                $stokKritis = isStokKritis($product['stok']);
                            ?>
                                <tr class="<?= $rowClass ?>">
                                    <td class="text-center fw-bold"><?= $no++ ?></td>
                                    <td><span class="badge bg-outline-dark border text-dark"><?= htmlspecialchars($product['id']) ?></span></td>
                                    <td class="fw-semibold text-dark"><?= htmlspecialchars($product['nama']) ?></td>
                                    <td><span class="badge bg-light text-secondary border"><?= htmlspecialchars($product['kategori']) ?></span></td>
                                    <td class="text-end fw-medium"><?= formatRupiah($product['harga']) ?></td>
                                    <td class="text-center fw-bold fs-6">
                                        <?= $product['stok'] ?>
                                    </td>
                                    <td class="text-end fw-semibold text-primary"><?= formatRupiah($subtotal) ?></td>
                                    <td class="small text-muted"><?= htmlspecialchars($product['deskripsi']) ?></td>
                                    <td class="text-center">
                                        <?php if ($stokKritis) : ?>
                                            <span class="badge badge-kritis p-2">
                                                <i class="fa-solid fa-triangle-exclamation me-1"></i> Kritis (&lt;3)
                                            </span>
                                        <?php else : ?>
                                            <span class="badge badge-aman p-2">
                                                <i class="fa-solid fa-circle-check me-1"></i> Aman
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="table-light border-top">
                            <tr>
                                <td colspan="6" class="text-end fw-bold fs-6 py-3">Total Akumulasi Aset Gudang:</td>
                                <td class="text-end fw-bold fs-5 text-success py-3"><?= formatRupiah($totalNilaiAset) ?></td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footnote / Info -->
        <div class="alert alert-info mt-4 d-flex align-items-center" role="alert">
            <i class="fa-solid fa-circle-info fa-lg me-3"></i>
            <div>
                <strong>Catatan Arsitektur:</strong> Sistem ini dibangun berdasarkan arsitektur 3-Tier Konseptual:
                <code>products.php</code> (Data Layer), <code>functions.php</code> (Processing Layer), dan <code>index.php</code> (Presentation Layer).
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-top py-3 text-center text-muted small mt-5">
        <div class="container">
            Product Information System &copy; <?= date('Y') ?> — Mini Project PHP
        </div>
    </footer>

</body>
</html>
