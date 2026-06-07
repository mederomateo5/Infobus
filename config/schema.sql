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
--  Ejemplo: crear un administrador inicial (cambiá el hash)
--  Generá el hash con: php -r "echo password_hash('TuPassword1!', PASSWORD_DEFAULT);"
-- ------------------------------------------------------------
-- INSERT INTO users (nombre, apellido, email, telefono, password, tipo)
-- VALUES ('Admin', 'Sistema', 'admin@omnibus.com', '099000000',
--         '$2y$12$HASH_AQUI', 'administrador');
