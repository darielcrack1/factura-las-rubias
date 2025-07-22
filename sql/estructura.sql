CREATE DATABASE IF NOT EXISTS la_rubia_ventas;
USE la_rubia_ventas;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL
);

-- Usuario: admin | Clave: 1234 (cifrada)
INSERT INTO usuarios (usuario, clave)
VALUES ('admin', '$2y$10$O6phKPktd4xqrcZsJc08n.jHR2p8YmleY61uv9PPIKfYaTbBl2rjG');

CREATE TABLE IF NOT EXISTS facturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE,
    numero_recibo VARCHAR(20),
    codigo_cliente VARCHAR(20),
    nombre_cliente VARCHAR(100),
    total DECIMAL(10,2),
    comentario TEXT
);

CREATE TABLE IF NOT EXISTS detalles_factura (
    id INT AUTO_INCREMENT PRIMARY KEY,
    factura_id INT,
    articulo VARCHAR(100),
    cantidad INT,
    precio DECIMAL(10,2),
    total DECIMAL(10,2),
    FOREIGN KEY (factura_id) REFERENCES facturas(id)
);
