-- Script para actualizar la estructura de la tabla productos
-- IMPORTANTE: Hacer backup de la tabla antes de ejecutar

-- Agregar campos que faltan a la tabla productos
ALTER TABLE productos 
ADD COLUMN IF NOT EXISTS descripcion TEXT NULL AFTER nombre,
ADD COLUMN IF NOT EXISTS imagen VARCHAR(255) NULL AFTER stock,
ADD COLUMN IF NOT EXISTS tipo VARCHAR(50) NULL AFTER imagen,
ADD COLUMN IF NOT EXISTS color VARCHAR(30) NULL AFTER tipo,
ADD COLUMN IF NOT EXISTS galeria TEXT NULL AFTER color,
ADD COLUMN IF NOT EXISTS descuento INT(11) DEFAULT 0 AFTER galeria,
ADD COLUMN IF NOT EXISTS personalizable TINYINT(1) DEFAULT 0 AFTER descuento;

-- Crear tabla para especificaciones técnicas
CREATE TABLE IF NOT EXISTS producto_especificaciones (
    id INT(11) NOT NULL AUTO_INCREMENT,
    producto_id INT(11) NOT NULL,
    clave VARCHAR(100) NOT NULL,
    valor VARCHAR(255) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_producto (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Crear tabla para características principales
CREATE TABLE IF NOT EXISTS producto_caracteristicas (
    id INT(11) NOT NULL AUTO_INCREMENT,
    producto_id INT(11) NOT NULL,
    caracteristica VARCHAR(255) NOT NULL,
    orden INT(11) DEFAULT 0,
    PRIMARY KEY (id),
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_producto (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Crear tabla para beneficios
CREATE TABLE IF NOT EXISTS producto_beneficios (
    id INT(11) NOT NULL AUTO_INCREMENT,
    producto_id INT(11) NOT NULL,
    beneficio VARCHAR(255) NOT NULL,
    orden INT(11) DEFAULT 0,
    PRIMARY KEY (id),
    FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
    INDEX idx_producto (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar algunos datos de ejemplo si no existen productos
INSERT INTO productos (nombre, descripcion, precio, stock, imagen, tipo, color) 
SELECT 'Funda para SUV', 'Protección UV y resistencia al agua.', 22500.00, 5, 'funda-suv.jpg', 'Funda', 'Negro'
WHERE NOT EXISTS (SELECT 1 FROM productos WHERE nombre = 'Funda para SUV');

-- Si insertamos el producto de ejemplo, agregar sus especificaciones
SET @producto_id = LAST_INSERT_ID();

-- Solo insertar si se creó el producto
INSERT INTO producto_especificaciones (producto_id, clave, valor)
SELECT @producto_id, 'Marca', 'Covercars'
WHERE @producto_id > 0
UNION ALL
SELECT @producto_id, 'Material', 'Según selección'
WHERE @producto_id > 0
UNION ALL
SELECT @producto_id, 'Resistencia', 'Agua y UV'
WHERE @producto_id > 0
UNION ALL
SELECT @producto_id, 'Garantía', '12 meses'
WHERE @producto_id > 0
UNION ALL
SELECT @producto_id, 'Origen', 'Argentina'
WHERE @producto_id > 0
UNION ALL
SELECT @producto_id, 'Tiempo de entrega', '5-7 días hábiles'
WHERE @producto_id > 0
UNION ALL
SELECT @producto_id, 'Instalación', 'Incluida'
WHERE @producto_id > 0
UNION ALL
SELECT @producto_id, 'Mantenimiento', 'Mínimo'
WHERE @producto_id > 0;

-- Insertar características
INSERT INTO producto_caracteristicas (producto_id, caracteristica, orden)
SELECT @producto_id, 'Resistente al agua y rayos UV', 1
WHERE @producto_id > 0
UNION ALL
SELECT @producto_id, 'Fácil instalación y retiro', 2
WHERE @producto_id > 0
UNION ALL
SELECT @producto_id, 'Materiales de primera calidad', 3
WHERE @producto_id > 0
UNION ALL
SELECT @producto_id, 'Costuras reforzadas', 4
WHERE @producto_id > 0;

-- Insertar beneficios
INSERT INTO producto_beneficios (producto_id, beneficio, orden)
SELECT @producto_id, 'Protección 24/7', 1
WHERE @producto_id > 0
UNION ALL
SELECT @producto_id, 'Mantiene el valor del vehículo', 2
WHERE @producto_id > 0
UNION ALL
SELECT @producto_id, 'Ahorro en lavados', 3
WHERE @producto_id > 0
UNION ALL
SELECT @producto_id, 'Diseño elegante', 4
WHERE @producto_id > 0;