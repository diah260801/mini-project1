<?php
/**
 * Presentation & Action Layer: public/edit.php
 * Halaman edit produk berdasarkan ID dengan form terisi otomatis, validasi unik, Prepared Statements, dan PRG Pattern.
 */

require_once __DIR__ . '/../config/db.php';

// Ambil ID dari GET parameter
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    set_flash('danger', 'ID produk tidak valid!');
    header('Location: index.php');
    exit;
}

// Fetch data produk lama via Prepared Statement (Syarat Slide)
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    set_flash('danger', 'Produk tidak ditemukan!');
    header('Location: index.php');
    exit;
}

$errors = [];
$name     = $product['name'];
$category = $product['category'];
$price    = $product['price'];
$stock    = $product['stock'];

// Proses form saat disubmit via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Sanitasi Input
    $name     = sanitize_input($_POST['name'] ?? '');
    $category = sanitize_input($_POST['category'] ?? '');
    $price    = sanitize_input($_POST['price'] ?? '');
    $stock    = sanitize_input($_POST['stock'] ?? '');

    // 2. Validasi Input (Syarat Wajib Slide)
    if (mb_strlen($name) < 3) {
        $errors[] = "Nama produk wajib diisi dan minimal 3 karakter.";
    } else {
        // Validasi Nama Unik (Mengecualikan produk yang sedang di-edit ini)
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM products WHERE LOWER(name) = LOWER(:name) AND id != :id");
        $stmtCheck->execute([':name' => $name, ':id' => $id]);
        if ($stmtCheck->fetchColumn() > 0) {
            $errors[] = "Nama produk '{$name}' sudah digunakan oleh produk lain! Silakan gunakan nama lain.";
        }
    }

    if (empty($category)) {
        $errors[] = "Kategori produk wajib dipilih/diisi.";
    }

    if ($price === '' || !is_numeric($price) || (float)$price < 0) {
        $errors[] = "Harga produk harus berupa angka dan tidak boleh kurang dari 0.";
    }

    if ($stock === '' || !is_numeric($stock) || (int)$stock < 0 || (float)$stock != (int)$stock) {
        $errors[] = "Stok produk harus berupa angka bulat (integer) dan minimal 0.";
    }

    // 3. Eksekusi Update jika Validasi Lolos
    if (empty($errors)) {
        try {
            $sql = "UPDATE products SET name = :name, category = :category, price = :price, stock = :stock WHERE id = :id";
            $stmtUpdate = $pdo->prepare($sql);
            $stmtUpdate->execute([
                ':name'     => $name,
                ':category' => $category,
                ':price'    => (float)$price,
                ':stock'    => (int)$stock,
                ':id'       => $id
            ]);

            // Post-Redirect-Get (PRG) Pattern dengan Session Flash Message
            set_flash('success', "Produk '" . e($name) . "' berhasil diperbarui!");
            header("Location: index.php");
            exit;

        } catch (PDOException $e) {
            $errors[] = "Terjadi kesalahan database: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk — Product Manager</title>
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
            <a href="index.php" class="btn btn-outline-light btn-sm rounded-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-md-9">
                
                <div class="form-card p-4 p-md-5">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="stat-icon bg-primary bg-opacity-20 text-primary fs-3">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </div>
                        <div>
                            <h3 class="fw-bold mb-0">Edit Produk</h3>
                            <p class="text-muted small mb-0">Perbarui rincian data produk ID #<?= (int)$product['id']; ?>.</p>
                        </div>
                    </div>

                    <!-- Error Alert Box -->
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger rounded-3 shadow-sm mb-4">
                            <div class="fw-bold mb-2"><i class="fa-solid fa-triangle-exclamation me-2"></i> Gagal Memperbarui Produk:</div>
                            <ul class="mb-0 ps-3">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= e($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!-- Form Edit Produk -->
                    <form action="edit.php?id=<?= (int)$product['id']; ?>" method="POST" class="needs-validation" novalidate>
                        
                        <!-- Nama Produk -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   placeholder="Contoh: Laptop Asus Zenbook" value="<?= e($name); ?>" required>
                            <div class="form-text">Minimal 3 karakter dan harus unik.</div>
                        </div>

                        <!-- Kategori Produk -->
                        <div class="mb-3">
                            <label for="category" class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="category" name="category" 
                                   placeholder="Contoh: Elektronik, Aksesori, Hardware" value="<?= e($category); ?>" list="category-suggestions" required>
                            <datalist id="category-suggestions">
                                <option value="Elektronik">
                                <option value="Aksesori">
                                <option value="Penyimpanan">
                                <option value="Periferal">
                            </datalist>
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Harga Produk -->
                            <div class="col-md-6">
                                <label for="price" class="form-label fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" step="0.01" min="0" class="form-control" id="price" name="price" 
                                           placeholder="0" value="<?= e($price); ?>" required>
                                </div>
                                <div class="form-text">Harga harus &ge; 0.</div>
                            </div>

                            <!-- Stok Produk -->
                            <div class="col-md-6">
                                <label for="stock" class="form-label fw-semibold">Stok <span class="text-danger">*</span></label>
                                <input type="number" min="0" step="1" class="form-control" id="stock" name="stock" 
                                       placeholder="0" value="<?= e($stock); ?>" required>
                                <div class="form-text">Stok harus &ge; 0.</div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top">
                            <a href="index.php" class="btn btn-light px-4">Batal</a>
                            <button type="submit" class="btn btn-primary fw-semibold px-4 d-flex align-items-center gap-2">
                                <i class="fa-solid fa-rotate"></i>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
