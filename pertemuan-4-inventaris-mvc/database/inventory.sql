CREATE DATABASE IF NOT EXISTS db_inventory CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_inventory;

DROP TABLE IF EXISTS transactions, items, categories, users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role ENUM('admin','staff','manager') NOT NULL DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    category_id INT NULL,
    quantity INT NOT NULL DEFAULT 0,
    unit VARCHAR(20) NOT NULL DEFAULT 'unit',
    price DECIMAL(15,2) NOT NULL DEFAULT 0,
    entry_date DATE NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_items_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    item_id INT NOT NULL,
    user_id INT NOT NULL,
    type ENUM('IN','OUT') NOT NULL,
    quantity INT NOT NULL,
    notes VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_transactions_item FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
    CONSTRAINT fk_transactions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data contoh. Password semua user adalah 123456 (sudah di-hash dengan password_hash).
INSERT INTO users (username, password, full_name, role) VALUES
('admin', '$2y$10$Oz15/3vGN9l44tZX65SqvuDyhWRIF.lXuZAqQ8CN6FVfRe0gWMsji', 'Administrator', 'admin'),
('manager', '$2y$10$Oz15/3vGN9l44tZX65SqvuDyhWRIF.lXuZAqQ8CN6FVfRe0gWMsji', 'Manager Gudang', 'manager'),
('staff', '$2y$10$Oz15/3vGN9l44tZX65SqvuDyhWRIF.lXuZAqQ8CN6FVfRe0gWMsji', 'Staff Gudang', 'staff');

INSERT INTO categories (name, description) VALUES
('Elektronik', 'Perangkat elektronik'),
('Komputer', 'Komputer dan aksesorisnya'),
('Furniture', 'Perabot kantor'),
('Alat Tulis', 'Alat tulis kantor');

INSERT INTO items (code, name, category_id, quantity, unit, price, entry_date, description) VALUES
('ITM0001', 'Laptop Asus VivoBook', 2, 12, 'unit', 8500000, '2026-01-05', 'Laptop kerja 14 inci'),
('ITM0002', 'Mouse Logitech', 2, 45, 'pcs', 185000, '2026-01-05', 'Mouse wireless'),
('ITM0003', 'Keyboard Mechanical', 2, 30, 'pcs', 450000, '2026-01-06', 'Keyboard mechanical RGB'),
('ITM0004', 'Monitor 24 inci', 1, 18, 'unit', 1750000, '2026-01-07', 'Monitor Full HD'),
('ITM0005', 'Printer Laser', 1, 7, 'unit', 2350000, '2026-01-08', 'Printer laser A4'),
('ITM0006', 'Proyektor', 1, 4, 'unit', 4200000, '2026-01-09', 'Proyektor presentasi'),
('ITM0007', 'Meja Kerja', 3, 20, 'unit', 1250000, '2026-01-10', 'Meja kerja kayu'),
('ITM0008', 'Kursi Kantor', 3, 25, 'unit', 950000, '2026-01-10', 'Kursi kantor ergonomis'),
('ITM0009', 'Lemari Arsip', 3, 9, 'unit', 1800000, '2026-01-11', 'Lemari arsip 4 laci'),
('ITM0010', 'Buku Tulis', 4, 150, 'pcs', 12000, '2026-01-12', 'Buku tulis 38 lembar'),
('ITM0011', 'Pulpen', 4, 200, 'pcs', 5000, '2026-01-12', 'Pulpen tinta hitam'),
('ITM0012', 'Kertas A4', 4, 80, 'rim', 55000, '2026-01-13', 'Kertas A4 80 gsm');

INSERT INTO transactions (item_id, user_id, type, quantity, notes) VALUES
(1, 1, 'IN', 10, 'Restock awal'),
(2, 1, 'IN', 20, 'Restock awal'),
(1, 2, 'OUT', 2, 'Dipakai tim IT'),
(7, 3, 'OUT', 1, 'Untuk ruang baru');
