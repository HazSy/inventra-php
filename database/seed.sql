-- ============================================================================
--  Inventra · datos de ejemplo
--  Usuario demo:  demo@inventra.dev  /  demo1234
--  Uso:  mysql -u root -p inventra < database/seed.sql
-- ============================================================================

USE inventra;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE stock_movements;
TRUNCATE TABLE products;
TRUNCATE TABLE users;
SET FOREIGN_KEY_CHECKS = 1;

-- Contraseña: demo1234 (bcrypt)
INSERT INTO users (name, email, password) VALUES
('Oscar López', 'demo@inventra.dev', '$2y$12$2w2vNFp/HGygsptUVh0azuJBpyR2sFgu8lkvuSo0UOGvkXVZC.Jw6');

INSERT INTO products (sku, name, category, price, stock, min_stock) VALUES
('AUD-001', 'Auriculares Pulse Pro',     'Audio',        129.00,  42,  10),
('PER-002', 'Teclado Mecánico K7',       'Periféricos',   89.00,   8,  10),
('PAN-003', 'Monitor UltraWide 34"',     'Pantallas',    449.00,   6,   5),
('PER-004', 'Ratón Ergo Lite',           'Periféricos',   39.00, 120,  20),
('VID-005', 'Webcam Studio 4K',          'Vídeo',        159.00,   3,   6),
('ACC-006', 'Hub USB-C 8 en 1',          'Accesorios',    59.00,  64,  15),
('ACC-007', 'Soporte de monitor',        'Accesorios',    79.00,  17,  20),
('AUD-008', 'Altavoces Beam Mini',       'Audio',         69.00,  31,  10),
('PER-009', 'Alfombrilla XXL',           'Periféricos',   19.00, 210,  30),
('PAN-010', 'Monitor 24" Full HD',       'Pantallas',    169.00,  12,   8);
