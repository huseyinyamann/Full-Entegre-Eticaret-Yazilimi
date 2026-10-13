-- Müşteri üyelik sistemi (kayıt / giriş / hesabım / adresler)
-- Mevcut kurulumlara uygulanır; yeni kurulumlarda schema.sql bunları zaten içerir.
--   mysql -u root yaman_eticaret < database/migrations/2026_10_13_customer_accounts.sql

ALTER TABLE login_attempts
    ADD COLUMN scope VARCHAR(20) NOT NULL DEFAULT 'admin' AFTER id,
    DROP INDEX idx_login_ip,
    DROP INDEX idx_login_email,
    ADD KEY idx_login_ip (scope, ip, attempted_at),
    ADD KEY idx_login_email (scope, email, attempted_at);

ALTER TABLE customers
    ADD COLUMN last_login_at DATETIME NULL AFTER is_active;

ALTER TABLE orders
    ADD KEY idx_orders_customer (customer_id, created_at);

CREATE TABLE customer_addresses (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id  INT UNSIGNED NOT NULL,
    title        VARCHAR(60) NOT NULL,
    full_name    VARCHAR(160) NOT NULL,
    phone        VARCHAR(30) NOT NULL,
    city         VARCHAR(60) NOT NULL,
    district     VARCHAR(60) NOT NULL,
    address      VARCHAR(500) NOT NULL,
    is_default   TINYINT(1) NOT NULL DEFAULT 0,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_addresses_customer (customer_id),
    CONSTRAINT fk_addresses_customer FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
