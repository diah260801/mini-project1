-- ============================================================
-- Schema Database: store_db
-- Deskripsi: Tabel products untuk Mini Project Product Manager
-- ============================================================

CREATE DATABASE IF NOT EXISTS store_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE store_db;

-- Hapus tabel jika sudah ada sebelumnya
DROP TABLE IF EXISTS products;

-- Struktur Tabel products
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(50) NOT NULL,
    price DECIMAL(12, 2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data Awal (Sample Products)
INSERT INTO products (name, category, price, stock) VALUES
('Laptop Asus Zenbook 14', 'Elektronik', 14500000.00, 12),
('Mouse Wireless Logitech MX Master 3S', 'Aksesori', 1499000.00, 25),
('Keyboard Mechanical Keychron K2', 'Aksesori', 1250000.00, 3),
('Monitor LG 27 Inch 4K UHD', 'Elektronik', 4850000.00, 8),
('Flashdisk SanDisk 64GB USB 3.0', 'Penyimpanan', 115000.00, 2),
('SSD NVMe Samsung 980 Pro 1TB', 'Penyimpanan', 1850000.00, 15);
