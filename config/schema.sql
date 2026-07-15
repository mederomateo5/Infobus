-- ============================================================
--  Base de datos: omnibus_app
-- ============================================================

CREATE DATABASE IF NOT EXISTS omnibus_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE omnibus_app;

-- ------------------------------------------------------------
--  Tabla de usuarios
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    nombre     VARCHAR(100)     NOT NULL,
    apellido   VARCHAR(100)     NOT NULL,
    email      VARCHAR(255)     NOT NULL UNIQUE,
    telefono   VARCHAR(20)      NOT NULL,
    password   VARCHAR(255)     NOT NULL,
    -- tipo: 'usuario' lo crea el formulario de registro
    --      'chofer' y 'administrador' los crea un administrador desde la pagina o base de datos
    tipo       ENUM('usuario','chofer','administrador') NOT NULL DEFAULT 'usuario',
    created_at TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  Tabla de ómnibus (Actualizada con ruta_key)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS omnibus (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    n_omnibus INT UNSIGNED NOT NULL,
    asientos INT UNSIGNED NOT NULL,
    asientos_disponibles INT UNSIGNED NOT NULL,
    origen VARCHAR(50) NOT NULL,
    destino VARCHAR(50) NOT NULL,
    precio DECIMAL(7,2) NOT NULL,
    agencia ENUM('COT','TAB','TURIL','URBANO') NOT NULL,
    ya_salio BOOLEAN NOT NULL DEFAULT FALSE, -- Por defecto los viajes nuevos no han salido
    fecha DATE NOT NULL, 
    hora TIME NOT NULL,
    hora_llegada TIME NOT NULL,
    ruta_key VARCHAR(50) NOT NULL DEFAULT 'urbano', -- <--- ¡AQUÍ SE AGREGA LA VARIABLE DE RUTA!
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ------------------------------------------------------------
--  Usuarios de testeo, contraseña: Admin123!
-- ------------------------------------------------------------
INSERT INTO users (nombre, apellido, email, telefono, password, tipo)
VALUES
('Administrador', 'Sistema', 'admin@omnibus.com', '099000001',
'$2y$10$nAtkg1.6iSydCeNp4CoOGOHXc5t.Im9G8Msorq2HHGy4xvJDe.hVi', 'administrador'),

('Juan', 'Jose', 'chofer@omnibus.com', '099000002',
'$2y$10$nAtkg1.6iSydCeNp4CoOGOHXc5t.Im9G8Msorq2HHGy4xvJDe.hVi', 'chofer');


-- ------------------------------------------------------------
--  Viajes de prueba (Modificados para testear el buscador)
-- ------------------------------------------------------------
-- NOTA: ya_salio ahora es FALSE (0) para que aparezcan en las búsquedas
-- Se agregaron fechas de prueba más lógicas y se definió su 'ruta_key' correspondiente.
INSERT INTO omnibus (n_omnibus, asientos, asientos_disponibles, origen, destino, precio, agencia, ya_salio, fecha, hora, hora_llegada, ruta_key)
VALUES 
(1, 20, 15, 'Montevideo', 'Juan Lacaze', 350.00, 'COT', FALSE, '2026-07-20', '10:30:00', '13:30:00', 'urbano'),
(2, 40, 28, 'Montevideo', 'Salto', 980.00, 'TURIL', FALSE, '2026-07-20', '08:00:00', '14:00:00', 'mvd_salto');