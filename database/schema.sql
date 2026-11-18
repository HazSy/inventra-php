-- ============================================================================
--  Inventra · esquema de base de datos
--  MySQL 8 / MariaDB 10.4+ (compatible con XAMPP)
--
--  Uso:  mysql -u root -p < database/schema.sql
-- ============================================================================

CREATE DATABASE IF NOT EXISTS inventra
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE inventra;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS stock_movements;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------------
-- Usuarios
-- ----------------------------------------------------------------------------
CREATE TABLE users (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name       VARCHAR(80)  NOT NULL,
    email      VARCHAR(190) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_email (email)
) ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Productos
-- ----------------------------------------------------------------------------
CREATE TABLE products (
    id         INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    sku        VARCHAR(20)    NOT NULL,
    name       VARCHAR(120)   NOT NULL,
    category   VARCHAR(60)    NOT NULL,
    price      DECIMAL(10, 2) NOT NULL,
    stock      INT UNSIGNED   NOT NULL DEFAULT 0,
    min_stock  INT UNSIGNED   NOT NULL DEFAULT 5,
    created_at TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_products_sku (sku),
    KEY idx_products_name (name),
    KEY idx_products_category (category),
    CONSTRAINT chk_products_price CHECK (price >= 0)
) ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Histórico de movimientos de stock (lo rellena un trigger)
-- ----------------------------------------------------------------------------
CREATE TABLE stock_movements (
    id         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    product_id INT UNSIGNED    NOT NULL,
    delta      INT             NOT NULL,
    reason     VARCHAR(40)     NOT NULL DEFAULT 'update',
    created_at TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_movements_product (product_id, created_at),
    CONSTRAINT fk_movements_product FOREIGN KEY (product_id)
        REFERENCES products (id) ON DELETE CASCADE
) ENGINE = InnoDB;

-- ----------------------------------------------------------------------------
-- Vista: productos por debajo del stock mínimo
-- ----------------------------------------------------------------------------
CREATE OR REPLACE VIEW vw_low_stock AS
SELECT id, sku, name, category, stock, min_stock, price
  FROM products
 WHERE stock <= min_stock;

-- ----------------------------------------------------------------------------
-- Trigger: registra automáticamente cada cambio de stock
-- ----------------------------------------------------------------------------
DROP TRIGGER IF EXISTS trg_products_stock_after_update;

DELIMITER //
CREATE TRIGGER trg_products_stock_after_update
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    IF OLD.stock <> NEW.stock THEN
        INSERT INTO stock_movements (product_id, delta, reason)
        VALUES (NEW.id, CAST(NEW.stock AS SIGNED) - CAST(OLD.stock AS SIGNED), 'update');
    END IF;
END//
DELIMITER ;
