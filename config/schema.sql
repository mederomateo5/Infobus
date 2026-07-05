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
    --        'chofer' y 'administrador' los crea un admin desde la BD
    tipo       ENUM('usuario','chofer','administrador') NOT NULL DEFAULT 'usuario',
    created_at TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------------------------------------------
--  Usuarios iniciales
-- ------------------------------------------------------------
INSERT INTO users (nombre, apellido, email, telefono, password, tipo)
VALUES
('Administrador', 'Sistema', 'admin@omnibus.com', '099000001',
'$2y$10$nAtkg1.6iSydCeNp4CoOGOHXc5t.Im9G8Msorq2HHGy4xvJDe.hVi', 'administrador'),

('Juan', 'Jose', 'chofer@omnibus.com', '099000002',
'$2y$10$nAtkg1.6iSydCeNp4CoOGOHXc5t.Im9G8Msorq2HHGy4xvJDe.hVi', 'chofer');