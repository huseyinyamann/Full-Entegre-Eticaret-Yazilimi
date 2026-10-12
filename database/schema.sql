-- Yaman Ticaret veritabanı şeması (MySQL 8+ / MariaDB 10.6+)
-- Kurulum: php bin/install.php

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------------
-- Yönetim
-- ---------------------------------------------------------------------------

CREATE TABLE admins (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100) NOT NULL,
    email           VARCHAR(190) NOT NULL,
    password_hash   VARCHAR(255) NOT NULL,
    role            ENUM('owner', 'admin', 'editor') NOT NULL DEFAULT 'editor',
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    last_login_at   DATETIME NULL,
    last_login_ip   VARCHAR(45) NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_admins_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Brute-force koruması: IP ve e-posta bazlı başarısız giriş sayımı
CREATE TABLE login_attempts (
    id           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ip           VARCHAR(45) NOT NULL,
    email        VARCHAR(190) NOT NULL,
    success      TINYINT(1) NOT NULL DEFAULT 0,
    attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_login_ip (ip, attempted_at),
    KEY idx_login_email (email, attempted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE admin_logs (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    admin_id    INT UNSIGNED NULL,
    action      VARCHAR(40) NOT NULL,
    entity      VARCHAR(40) NOT NULL,
    entity_id   INT UNSIGNED NULL,
    summary     VARCHAR(255) NULL,
    ip          VARCHAR(45) NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_logs_created (created_at),
    CONSTRAINT fk_logs_admin FOREIGN KEY (admin_id) REFERENCES admins (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE settings (
    `key`       VARCHAR(80) NOT NULL PRIMARY KEY,
    `value`     TEXT NULL,
    updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Katalog
-- ---------------------------------------------------------------------------

CREATE TABLE categories (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id         INT UNSIGNED NULL,
    name              VARCHAR(120) NOT NULL,
    slug              VARCHAR(140) NOT NULL,
    image             VARCHAR(255) NULL,
    description       TEXT NULL,
    sort_order        INT NOT NULL DEFAULT 0,
    is_active         TINYINT(1) NOT NULL DEFAULT 1,
    meta_title        VARCHAR(190) NULL,
    meta_description  VARCHAR(300) NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_categories_slug (slug),
    KEY idx_categories_parent (parent_id, sort_order),
    CONSTRAINT fk_categories_parent FOREIGN KEY (parent_id) REFERENCES categories (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id       INT UNSIGNED NULL,
    name              VARCHAR(190) NOT NULL,
    slug              VARCHAR(210) NOT NULL,
    sku               VARCHAR(64) NOT NULL,
    summary           TEXT NULL,
    description       MEDIUMTEXT NULL,
    price             DECIMAL(12,2) NOT NULL DEFAULT 0,
    old_price         DECIMAL(12,2) NULL,
    stock             INT NOT NULL DEFAULT 0,
    badge             ENUM('yeni', 'indirim', 'cok-satan') NULL,
    colors            JSON NULL,   -- [{"name":"Siyah","hex":"#111111"}]
    sizes             JSON NULL,   -- ["S","M","L"]
    rating            DECIMAL(2,1) NOT NULL DEFAULT 0,
    review_count      INT UNSIGNED NOT NULL DEFAULT 0,
    is_active         TINYINT(1) NOT NULL DEFAULT 1,
    is_featured       TINYINT(1) NOT NULL DEFAULT 0,
    meta_title        VARCHAR(190) NULL,
    meta_description  VARCHAR(300) NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_products_slug (slug),
    UNIQUE KEY uq_products_sku (sku),
    KEY idx_products_category (category_id, is_active),
    KEY idx_products_created (created_at),
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_images (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id  INT UNSIGNED NOT NULL,
    path        VARCHAR(255) NOT NULL,
    sort_order  INT NOT NULL DEFAULT 0,
    KEY idx_images_product (product_id, sort_order),
    CONSTRAINT fk_images_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Müşteri & sipariş
-- ---------------------------------------------------------------------------

CREATE TABLE customers (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name     VARCHAR(80) NOT NULL,
    last_name      VARCHAR(80) NOT NULL,
    email          VARCHAR(190) NOT NULL,
    phone          VARCHAR(30) NULL,
    password_hash  VARCHAR(255) NULL,
    is_active      TINYINT(1) NOT NULL DEFAULT 1,
    created_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_customers_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_no          VARCHAR(20) NOT NULL,
    customer_id       INT UNSIGNED NULL,
    customer_name     VARCHAR(160) NOT NULL,
    email             VARCHAR(190) NOT NULL,
    phone             VARCHAR(30) NULL,
    city              VARCHAR(60) NULL,
    district          VARCHAR(60) NULL,
    address           TEXT NULL,
    subtotal          DECIMAL(12,2) NOT NULL DEFAULT 0,
    shipping_total    DECIMAL(12,2) NOT NULL DEFAULT 0,
    discount_total    DECIMAL(12,2) NOT NULL DEFAULT 0,
    grand_total       DECIMAL(12,2) NOT NULL DEFAULT 0,
    payment_method    ENUM('kredi-karti', 'havale', 'kapida-odeme') NOT NULL DEFAULT 'kredi-karti',
    payment_status    ENUM('bekliyor', 'odendi', 'iade') NOT NULL DEFAULT 'bekliyor',
    status            ENUM('beklemede', 'hazirlaniyor', 'kargoda', 'teslim-edildi', 'iptal', 'iade') NOT NULL DEFAULT 'beklemede',
    cargo_company     VARCHAR(60) NULL,
    tracking_no       VARCHAR(60) NULL,
    customer_note     TEXT NULL,
    admin_note        TEXT NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_orders_no (order_no),
    KEY idx_orders_status (status, created_at),
    KEY idx_orders_created (created_at),
    CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ürün bilgileri sipariş anındaki haliyle saklanır (ürün silinse/değişse de sipariş bozulmaz)
CREATE TABLE order_items (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id      INT UNSIGNED NOT NULL,
    product_id    INT UNSIGNED NULL,
    product_name  VARCHAR(190) NOT NULL,
    sku           VARCHAR(64) NULL,
    options       VARCHAR(190) NULL,
    unit_price    DECIMAL(12,2) NOT NULL,
    quantity      INT UNSIGNED NOT NULL DEFAULT 1,
    line_total    DECIMAL(12,2) NOT NULL,
    KEY idx_items_order (order_id),
    CONSTRAINT fk_items_order FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
    CONSTRAINT fk_items_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- İçerik
-- ---------------------------------------------------------------------------

CREATE TABLE pages (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title             VARCHAR(190) NOT NULL,
    slug              VARCHAR(210) NOT NULL,
    content           MEDIUMTEXT NULL,
    sort_order        INT NOT NULL DEFAULT 0,
    is_active         TINYINT(1) NOT NULL DEFAULT 1,
    meta_title        VARCHAR(190) NULL,
    meta_description  VARCHAR(300) NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_pages_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE posts (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title         VARCHAR(190) NOT NULL,
    slug          VARCHAR(210) NOT NULL,
    image         VARCHAR(255) NULL,
    excerpt       VARCHAR(500) NULL,
    content       MEDIUMTEXT NULL,
    is_active     TINYINT(1) NOT NULL DEFAULT 1,
    published_at  DATE NOT NULL,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_posts_slug (slug),
    KEY idx_posts_published (is_active, published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE slides (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(190) NOT NULL,
    text         VARCHAR(300) NULL,
    button_text  VARCHAR(60) NULL,
    link         VARCHAR(255) NULL,
    image        VARCHAR(255) NULL,
    sort_order   INT NOT NULL DEFAULT 0,
    is_active    TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE testimonials (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    city        VARCHAR(60) NULL,
    text        VARCHAR(600) NOT NULL,
    rating      TINYINT UNSIGNED NOT NULL DEFAULT 5,
    sort_order  INT NOT NULL DEFAULT 0,
    is_active   TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- Tema altyapısı (tema profili / AI ile düzenleme için zemin)
-- ---------------------------------------------------------------------------

CREATE TABLE themes (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name             VARCHAR(120) NOT NULL,
    slug             VARCHAR(120) NOT NULL,
    profile_version  VARCHAR(20) NOT NULL DEFAULT 'v1',
    is_active        TINYINT(1) NOT NULL DEFAULT 0,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_themes_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE theme_versions (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    theme_id    INT UNSIGNED NOT NULL,
    bundle      LONGTEXT NOT NULL,
    status      ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft',
    created_by  INT UNSIGNED NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_theme_versions (theme_id, status),
    CONSTRAINT fk_theme_versions_theme FOREIGN KEY (theme_id) REFERENCES themes (id) ON DELETE CASCADE,
    CONSTRAINT fk_theme_versions_admin FOREIGN KEY (created_by) REFERENCES admins (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
