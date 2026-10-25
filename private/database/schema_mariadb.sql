-- ============================================================
--  Base de datos: omnibus_app
--  Adaptado para AlmaLinux 9.x + MariaDB (10.5+ / cliente "15.1")
--  Mismo modelo (3FN) que schema.sql; solo cambian detalles de compatibilidad.
--
--  Importar con:
--    mysql -u root -p < schema_mariadb.sql
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

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
   tipo ENUM('usuario', 'compania', 'administrador') NOT NULL DEFAULT 'usuario',
   created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

   PRIMARY KEY (id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
--  2. Tabla de compañías
-- ============================================================

CREATE TABLE IF NOT EXISTS compania (
   id INT UNSIGNED NOT NULL AUTO_INCREMENT,
   nombre VARCHAR(100) NOT NULL,
   email VARCHAR(255) NOT NULL UNIQUE,
   telefono VARCHAR(20) NOT NULL,
   password VARCHAR(255) NOT NULL,

   PRIMARY KEY (id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
--  3. Tabla de ómnibus
-- ============================================================

CREATE TABLE IF NOT EXISTS omnibus (
   id INT UNSIGNED NOT NULL AUTO_INCREMENT,
   compania_id INT UNSIGNED NOT NULL,
   n_omnibus INT UNSIGNED NOT NULL,
   asientos INT UNSIGNED NOT NULL,
   ruta_key VARCHAR(50) NOT NULL DEFAULT 'urbano',

   PRIMARY KEY (id),

   CONSTRAINT fk_omnibus_compania
       FOREIGN KEY (compania_id)
       REFERENCES compania(id)
       ON UPDATE CASCADE
       ON DELETE RESTRICT,

   CONSTRAINT uq_omnibus_compania_numero
       UNIQUE (compania_id, n_omnibus)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
--  4. Tabla de paradas
-- ============================================================

CREATE TABLE IF NOT EXISTS paradas (
   id INT UNSIGNED NOT NULL,
   nombre VARCHAR(100) NOT NULL,
   latitud DECIMAL(10,7) NOT NULL,
   longitud DECIMAL(10,7) NOT NULL,
   tipo ENUM('urbano','interurbano','urbano/interurbano') NOT NULL DEFAULT 'urbano',

   PRIMARY KEY (id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


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
       ON DELETE CASCADE,

   CONSTRAINT uq_recorre_orden
       UNIQUE (omnibus_id, orden)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
--  6. Tabla de viajes
-- ============================================================

CREATE TABLE IF NOT EXISTS viajes (
   id INT UNSIGNED NOT NULL AUTO_INCREMENT,
   omnibus_id INT UNSIGNED NOT NULL,
   origen_id INT UNSIGNED NOT NULL,
   destino_id INT UNSIGNED NOT NULL,
   precio DECIMAL(7,2) NOT NULL,
   ya_salio BOOLEAN NOT NULL DEFAULT FALSE,
   hora TIME NOT NULL,
   hora_llegada TIME NOT NULL,
   es_directo BOOLEAN NOT NULL DEFAULT FALSE,
   asientos_disponibles INT UNSIGNED NOT NULL,

   PRIMARY KEY (id),

   INDEX idx_viajes_origen (origen_id),
   INDEX idx_viajes_destino (destino_id),

   CONSTRAINT fk_viajes_omnibus
       FOREIGN KEY (omnibus_id)
       REFERENCES omnibus(id)
       ON UPDATE CASCADE
       ON DELETE RESTRICT,

   CONSTRAINT fk_viajes_origen
       FOREIGN KEY (origen_id)
       REFERENCES paradas(id)
       ON UPDATE CASCADE
       ON DELETE RESTRICT,

   CONSTRAINT fk_viajes_destino
       FOREIGN KEY (destino_id)
       REFERENCES paradas(id)
       ON UPDATE CASCADE
       ON DELETE RESTRICT

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
--  6b. Validación origen <> destino (reemplazo del CHECK)
--  MariaDB no permite un CHECK sobre columnas que tienen una FK con
--  ON UPDATE CASCADE (error 1901), por eso se usan triggers.
--  Nota: DELIMITER solo funciona con el cliente `mysql`/`mariadb`
--  (no en phpMyAdmin si importás desde ahí sin cambiar el delimitador).
-- ============================================================

DELIMITER //

CREATE TRIGGER IF NOT EXISTS trg_viajes_origen_destino_ins
BEFORE INSERT ON viajes
FOR EACH ROW
BEGIN
    IF NEW.origen_id = NEW.destino_id THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El origen y el destino del viaje no pueden ser la misma parada.';
    END IF;
END//

CREATE TRIGGER IF NOT EXISTS trg_viajes_origen_destino_upd
BEFORE UPDATE ON viajes
FOR EACH ROW
BEGIN
    IF NEW.origen_id = NEW.destino_id THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El origen y el destino del viaje no pueden ser la misma parada.';
    END IF;
END//

DELIMITER ;


-- ============================================================
--  7. Tabla de días de la semana
-- ============================================================

CREATE TABLE IF NOT EXISTS dias_semana (
   id TINYINT UNSIGNED NOT NULL,
   nombre VARCHAR(20) NOT NULL UNIQUE,

   PRIMARY KEY (id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
--  8. Relación viajes - días de la semana
-- ============================================================

CREATE TABLE IF NOT EXISTS viaje_dia (
   viaje_id INT UNSIGNED NOT NULL,
   dia_id TINYINT UNSIGNED NOT NULL,

   PRIMARY KEY (viaje_id, dia_id),

   CONSTRAINT fk_viaje_dia_viaje
       FOREIGN KEY (viaje_id)
       REFERENCES viajes(id)
       ON UPDATE CASCADE
       ON DELETE CASCADE,

   CONSTRAINT fk_viaje_dia_dia
       FOREIGN KEY (dia_id)
       REFERENCES dias_semana(id)
       ON UPDATE CASCADE
       ON DELETE RESTRICT

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
--  9. Viajes favoritos (N:M entre users y viajes)
-- ============================================================

CREATE TABLE IF NOT EXISTS usuario_viaje_favorito (
    user_id INT UNSIGNED NOT NULL,
    viaje_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (user_id, viaje_id),

    CONSTRAINT fk_usu_viaje_fav_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_usu_viaje_fav_viaje
        FOREIGN KEY (viaje_id)
        REFERENCES viajes(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
--  10. Compañías favoritas (N:M entre users y compania)
-- ============================================================

CREATE TABLE IF NOT EXISTS usuario_compania_favorita (
    user_id INT UNSIGNED NOT NULL,
    compania_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (user_id, compania_id),

    CONSTRAINT fk_usu_comp_fav_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_usu_comp_fav_compania
        FOREIGN KEY (compania_id)
        REFERENCES compania(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
--  11. Viajes visitados
--  (agregado IF NOT EXISTS: el original fallaba al re-importar)
-- ============================================================

CREATE TABLE IF NOT EXISTS viaje_visita (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    viaje_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NULL,
    fecha_visita TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    INDEX idx_viaje_visita_viaje_fecha (viaje_id, fecha_visita),

    CONSTRAINT fk_viaje_visita_viaje
        FOREIGN KEY (viaje_id)
        REFERENCES viajes(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_viaje_visita_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;


-- ============================================================
--  12. (Opcional) Usuario dedicado para la aplicación
--  En MariaDB/AlmaLinux root usa autenticación por socket, así que
--  conviene que PHP se conecte con un usuario propio.
--  Cambiá la contraseña antes de ejecutar y descomentá.
-- ============================================================

-- CREATE USER IF NOT EXISTS 'omnibus_user'@'localhost' IDENTIFIED BY 'CAMBIAR_ESTA_CLAVE';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON omnibus_app.* TO 'omnibus_user'@'localhost';
-- FLUSH PRIVILEGES;
