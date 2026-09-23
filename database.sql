-- =========================================================
-- Café Origen Cusco - Script de Base de Datos
-- Compatible con MySQL 5.7+ / MariaDB (phpMyAdmin - cPanel)
-- =========================================================



-- ---------------------------------------------------------
-- Tabla: categorias
-- ---------------------------------------------------------
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS categorias;

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(60) NOT NULL,
    slug VARCHAR(60) NOT NULL UNIQUE,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Tabla: productos
-- ---------------------------------------------------------
CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nombre VARCHAR(120) NOT NULL,
    descripcion VARCHAR(255) NOT NULL,
    precio DECIMAL(8,2) NOT NULL,
    imagen_emoji VARCHAR(10) DEFAULT NULL,
    disponible TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_producto_categoria
        FOREIGN KEY (categoria_id) REFERENCES categorias(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Datos: categorías
-- ---------------------------------------------------------
INSERT INTO categorias (nombre, slug) VALUES
('Bebidas', 'bebidas'),
('Granos de Café', 'granos');

-- ---------------------------------------------------------
-- Datos: productos (mínimo 6, se incluyen 8)
-- ---------------------------------------------------------
INSERT INTO productos (categoria_id, nombre, descripcion, precio, imagen_emoji, disponible) VALUES
(1, 'Espresso', 'Shot puro de café cusqueño de altura, cuerpo intenso y notas achocolatadas.', 8.00, '☕', 1),
(1, 'Cappuccino', 'Espresso con leche vaporizada y espuma cremosa, servido en taza de 200ml.', 12.00, '🥛', 1),
(1, 'Café Americano', 'Espresso diluido en agua caliente, suave y aromático, ideal para el día a día.', 9.00, '☕', 1),
(1, 'Latte Macchiato', 'Capas de leche vaporizada y espresso, un clásico cremoso y equilibrado.', 13.00, '🥛', 1),
(1, 'Cold Brew Cusco', 'Café de extracción en frío durante 18 horas, refrescante y de baja acidez.', 14.00, '🧊', 1),
(2, 'Geisha La Convención 250g', 'Variedad Geisha de altura, notas florales y a jazmín, tueste claro en grano entero.', 65.00, '🫘', 1),
(2, 'Blend Cusco 500g', 'Mezcla equilibrada de la región, notas a caramelo y frutos secos, tueste medio.', 45.00, '🫘', 1),
(2, 'Café Orgánico Quillabamba 250g', 'Cultivo orgánico certificado del valle de Quillabamba, cuerpo medio y final dulce.', 38.00, '🫘', 1);
