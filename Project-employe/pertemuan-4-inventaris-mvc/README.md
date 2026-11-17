# Sistem Inventaris Barang — PHP MVC (Modul 04)

Aplikasi web MVC tanpa framework: PHP 8.x + MySQL/MariaDB + PDO, dengan
CRUD kategori dan barang, transaksi stok masuk/keluar, autentikasi session,
paginasi, pencarian, filter kategori, dan dashboard.

## 1. Persiapan

- PHP 8.x dengan ekstensi `pdo_mysql`
- MySQL/MariaDB berjalan
- Atau XAMPP (PHP + MySQL + Apache)

Cek PHP:

```bash
php -v
php -m | grep -i pdo_mysql
```

## 2. Impor database

Buat dan isi database dari `database/inventory.sql`, salah satu cara:

```bash
mysql -u root -p < database/inventory.sql
```

Atau lewat phpMyAdmin: buat database `db_inventory`, lalu Import file
`database/inventory.sql`.

## 3. Konfigurasi

Salin `.env.example` menjadi `.env` bila belum ada, lalu sesuaikan:

- `BASE_URL` — `http://localhost:8000` untuk PHP built-in server, atau
  `http://localhost/inventaris-mvc/public` untuk XAMPP
- `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS` — sesuaikan dengan
  MySQL kamu. Di XAMPP biasanya user `root` tanpa password, jadi
  `DB_PASS` dikosongkan.

## 4. Menjalankan

### PHP built-in server (paling mudah)

Dari folder proyek:

```bash
php -S localhost:8000 -t public
```

Buka http://localhost:8000

### XAMPP

Letakkan folder proyek di `C:\xampp\htdocs\inventaris-mvc`, lalu buka:

```
http://localhost/inventaris-mvc/public
```

## 5. Login demo

- Username: `admin` — password: `123456`
- Username: `manager` — password: `123456`
- Username: `staff` — password: `123456`

Tanpa login, akses ke dashboard, barang, kategori, transaksi, dan
pengguna akan dialihkan ke halaman login.

## 6. Catatan dari modul

- Properti `$controller` di `app/core/App.php` sengaja tidak diberi tipe
  `string`, karena nilainya berubah dari nama class menjadi objek
  controller. Memberi tipe string akan menimbulkan error.
- Hapus data memakai form `POST`, bukan tautan `GET`.
- Transaksi stok memakai database transaction: bila stok keluar melebihi
  stok yang ada, seluruh operasi dibatalkan (rollback) sehingga stok
  tidak negatif dan riwayat tidak bertambah.
- Folder `logs/` dibuat otomatis saat terjadi error dan tidak perlu
  di-commit (sudah ada di `.gitignore` bersama `.env`).

## Struktur folder

```
inventaris-mvc/
├── .env / .env.example / .gitignore
├── app/
│   ├── config/config.php
│   ├── core/ (App, Controller, Database, Model, Middleware, Logger, helpers)
│   ├── controllers/ (Home, Dashboard, Items, Categories, Transactions, Users, Auth)
│   ├── models/ (Category, Item, Transaction, User)
│   └── views/ (layouts, dashboard, items, categories, transactions, users, auth, errors)
├── database/inventory.sql
└── public/ (.htaccess, index.php)
```
