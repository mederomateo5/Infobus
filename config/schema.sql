-- ============================================================
--  Base de datos: omnibus_app
-- ============================================================

CREATE DATABASE IF NOT EXISTS omnibus_app
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE omnibus_app;

-- ============================================================
--  1. Tabla de usuarios
-- ============================================================

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefono VARCHAR(20) NOT NULL,
    password VARCHAR(255) NOT NULL,
    tipo ENUM('usuario','chofer','administrador') NOT NULL DEFAULT 'usuario',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  2. Tabla de compañías
-- ============================================================

CREATE TABLE IF NOT EXISTS compania (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,

    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  3. Tabla de ómnibus
-- ============================================================

CREATE TABLE IF NOT EXISTS omnibus (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    compania_id INT UNSIGNED NOT NULL,
    n_omnibus INT UNSIGNED NOT NULL,
    asientos INT UNSIGNED NOT NULL,
    asientos_disponibles INT UNSIGNED NOT NULL,
    ruta_key VARCHAR(50) NOT NULL DEFAULT 'urbano',

    PRIMARY KEY (id),

    CONSTRAINT chk_asientos
        CHECK (asientos_disponibles <= asientos),

    CONSTRAINT fk_omnibus_compania
        FOREIGN KEY (compania_id)
        REFERENCES compania(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  4. Tabla de paradas
-- ============================================================

CREATE TABLE IF NOT EXISTS paradas (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    tipo ENUM('urbano','interurbano','urbano/interurbano') NOT NULL DEFAULT 'urbano',
    latitud DECIMAL(10,7) NOT NULL,
    longitud DECIMAL(10,7) NOT NULL,

    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  5. Relación ómnibus - paradas
-- ============================================================

CREATE TABLE IF NOT EXISTS recorre (
    omnibus_id INT UNSIGNED NOT NULL,
    parada_id INT UNSIGNED NOT NULL,
    orden INT UNSIGNED NOT NULL,

    PRIMARY KEY (omnibus_id, parada_id),

    CONSTRAINT fk_recorre_omnibus
        FOREIGN KEY (omnibus_id)
        REFERENCES omnibus(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_recorre_parada
        FOREIGN KEY (parada_id)
        REFERENCES paradas(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  6. Tabla de viajes
-- ============================================================

CREATE TABLE IF NOT EXISTS viajes (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    omnibus_id INT UNSIGNED NOT NULL,
    origen VARCHAR(50) NOT NULL,
    destino VARCHAR(50) NOT NULL,
    precio DECIMAL(7,2) NOT NULL,
    ya_salio BOOLEAN NOT NULL DEFAULT FALSE,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    hora_llegada TIME NOT NULL,
    es_directo BOOLEAN NOT NULL DEFAULT FALSE,
    dias_semana SET('Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado', 'Domingo') NOT NULL DEFAULT 'Lunes,Martes,Miercoles,Jueves,Viernes',
    PRIMARY KEY (id),
    INDEX idx_origen (origen),
    INDEX idx_destino (destino),
    INDEX idx_fecha (fecha),
    CONSTRAINT fk_viajes_omnibus
        FOREIGN KEY (omnibus_id)
        REFERENCES omnibus(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  DATOS DE PRUEBA
-- ============================================================

-- Usuarios

INSERT INTO users (nombre, apellido, email, telefono, password, tipo)
VALUES
('Administrador', 'Sistema', 'admin@omnibus.com', '099000001',
'$2y$10$nAtkg1.6iSydCeNp4CoOGOHXc5t.Im9G8Msorq2HHGy4xvJDe.hVi',
'administrador'),

('Juan', 'Jose', 'chofer@omnibus.com', '099000002',
'$2y$10$nAtkg1.6iSydCeNp4CoOGOHXc5t.Im9G8Msorq2HHGy4xvJDe.hVi',
'chofer');

-- Compañías

INSERT INTO compania (nombre, email)
VALUES
('COT', 'contacto@cot.com.uy'),
('TURIL', 'contacto@turil.com.uy');

-- Ómnibus

INSERT INTO omnibus
(compania_id, n_omnibus, asientos, asientos_disponibles, ruta_key)
VALUES
(1, 1, 20, 15, 'urbano'),
(2, 2, 40, 28, 'mvd_salto');

-- Paradas

INSERT INTO paradas (nombre, latitud, longitud)
VALUES
('Montevideo', -34.9011120, -56.1645320),
('Juan Lacaze', -34.4188700, -57.4523400),
('Salto', -31.3833300, -57.9666700);

-- Viajes

INSERT INTO viajes
(omnibus_id, origen, destino, precio, ya_salio, fecha, hora, hora_llegada)
VALUES
(1, 'Montevideo', 'Juan Lacaze', 350.00, FALSE,
'2026-09-15', '10:30:00', '13:30:00'),

(2, 'Montevideo', 'Salto', 980.00, FALSE,
'2026-09-16', '08:00:00', '14:00:00');

INSERT INTO paradas (nombre, tipo, latitud, longitud)
VALUES
('Rosario y José Enrique Rodó', 'urbano', -34.4179560, -57.4522300),
('Rosario y Zorrilla de San Martín', 'interurbano', -34.4157700, -57.4508890);