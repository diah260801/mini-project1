# 📦 Product Manager — Web Application (PHP & MySQL PDO)

Aplikasi Web Manajemen Inventaris Produk modern yang dibangun menggunakan **PHP Native (PDO)** dan **MySQL** sesuai dengan spesifikasi tugas akhir *Pemrograman Web - Pertemuan 3*.

Aplikasi ini menerapkan arsitektur terpisah (*Separation of Concerns*), standar keamanan web industri (CSRF Protection, XSS Output Escaping, Prepared Statements), serta antarmuka responsif berbasis Card Flexbox.

---

## ✨ Fitur Utama

- ** Create**: Tambah produk baru dengan validasi server-side ketat (nama min. 3 karakter & unik, harga $\ge 0$, stok $\ge 0$).
- ** Read**: Tampilan produk berbasis kartu responsif (Flexbox / Grid) dilengkapi indikator stok kritis ($< 5$), total estimasi nilai aset, dan ringkasan statistik.
- ** Update**: Edit data produk berdasarkan ID dengan form terisi otomatis dan validasi nama unik terhadap produk lain.
- ** Delete**: Penghapusan data produk yang aman menggunakan **Metode POST** & **Token CSRF** untuk mencegah penyerangan unauthorized action.
- ** Search (Fitur Bonus)**: Pencarian cepat produk berdasarkan nama atau kategori menggunakan parameter `GET` dan PDO Prepared Statement (`WHERE name LIKE :q OR category LIKE :q`).
- ** CSRF Protection & PRG Pattern**: Menggunakan **Post-Redirect-Get (PRG)** dan session flash message untuk mencegah pengiriman data ganda saat refresh halaman.
- ** Output Escaping**: Seluruh output variabel di-escape menggunakan `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')` untuk mencegah celah keamanan Cross-Site Scripting (XSS).

---

## 📁 Struktur Proyek

```
product-manager/
├── config/
│   └── db.php          # Koneksi PDO MySQL/SQLite, Helper CSRF & Escaping
├── database/
│   └── store_db.sql    # Skema Database & Sample Initial Data
├── public/
│   ├── assets/
│   │   └── style.css   # Custom Responsive Styling & Animations
│   ├── create.php      # CREATE Endpoint (Form + Validasi + PRG)
│   ├── delete.php      # DELETE Endpoint (POST + CSRF Token)
│   ├── edit.php        # UPDATE Endpoint (READ by ID + Update + PRG)
│   └── index.php       # READ & SEARCH Endpoint (Dashboard Cards)
├── index.php           # Entry Redirect ke public/index.php
└── README.md           # Dokumentasi Lengkap Proyek
```

---

## 🛠️ Persyaratan Sistem & Instalasi

### 1. Prasyarat
- PHP 8.0 atau yang lebih baru (dengan ekstensi `pdo_mysql` atau `pdo_sqlite`).
- MySQL / MariaDB Server (misalnya via XAMPP, Laragon, atau Standalone MySQL).

### 2. Import Database SQL
1. Buka phpMyAdmin / MySQL CLI / DBeaver.
2. Buat database baru bernama `store_db` atau jalankan file `database/store_db.sql`:
   ```bash
   mysql -u root -p < database/store_db.sql
   ```

### 3. Konfigurasi Database (`config/db.php`)
Secara default, aplikasi mengonfigurasi koneksi ke:
- **Host**: `127.0.0.1`
- **Database**: `store_db`
- **Username**: `root`
- **Password**: *(kosong)*

Jika menggunakan kredensial berbeda, Anda dapat menyesuaikannya langsung di file `config/db.php` atau melalui Environment Variables.

---

## 🚀 Cara Menjalankan Aplikasi

Jalankan PHP Built-in Web Server dari direktori proyek:

```bash
php -S localhost:8000 -t public
```

Buka peramban (browser) dan akses:
👉 **[http://localhost:8000](http://localhost:8000)**

---

## 🛡️ Jaminan Keamanan & Best Practices

1. **Prepared Statements (PDO)**:
   Semua query ke database (`INSERT`, `SELECT`, `UPDATE`, `DELETE`, `SEARCH`) menggunakan PDO Prepared Statements untuk mencegah **SQL Injection**.
2. **XSS Escaping**:
   Penggunaan fungsi `e()` atau `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')` pada seluruh data yang dirender ke elemen HTML.
3. **Mencegah Duplicate Submission (PRG)**:
   Setiap proses penambahan, pengubahan, dan penghapusan data selalu diakhiri dengan `header('Location: index.php')` dan pengalihan pesan via `$_SESSION['flash']`.
4. **Proteksi CSRF**:
   Penghapusan produk mewajibkan token unik `csrf_token` yang diverifikasi menggunakan `hash_equals()` pada request `POST`.

---

Developed with ❤️ for Pemrograman Web Mini Project.
