USE db_inventory;

-- 1. Pengguna: status aktif & jejak login
ALTER TABLE users
    ADD COLUMN is_active     TINYINT(1) NOT NULL DEFAULT 1 AFTER role,
    ADD COLUMN last_login_at DATETIME NULL AFTER is_active;

-- 2. Kategori: soft delete
ALTER TABLE categories
    ADD COLUMN deleted_at DATETIME NULL AFTER description,
    ADD INDEX idx_categories_deleted_at (deleted_at);
    
-- 3. Barang: foto + soft delete
ALTER TABLE items
    ADD COLUMN photo      VARCHAR(255) NULL AFTER description,
    ADD COLUMN deleted_at DATETIME NULL AFTER photo,
    ADD INDEX idx_items_deleted_at (deleted_at);

-- 4. Audit trail
CREATE TABLE activity_logs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NULL,
    action      VARCHAR(50)  NOT NULL,
    entity      VARCHAR(50)  NULL,
    entity_id   INT          NULL,
    description VARCHAR(255) NULL,
    ip_address  VARCHAR(45)  NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_activity_created (created_at),
    INDEX idx_activity_action (action),
    CONSTRAINT fk_activity_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Token API (hash, bukan teks asli)
CREATE TABLE api_tokens (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    user_id      INT NOT NULL,
    token_hash   CHAR(64) NOT NULL UNIQUE,
    name         VARCHAR(100) NOT NULL DEFAULT 'default',
    last_used_at DATETIME NULL,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_api_tokens_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;