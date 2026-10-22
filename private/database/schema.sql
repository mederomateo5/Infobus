-- ============================================================
--  Base de datos: omnibus_app
--  Modelo normalizado hasta Tercera Forma Normal (3FN)
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
   tipo ENUM('usuario', 'compania', 'administrador') NOT NULL DEFAULT 'usuario',
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
   telefono VARCHAR(20) NOT NULL,
   password VARCHAR(255) NOT NULL,


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
   ruta_key VARCHAR(50) NOT NULL DEFAULT 'urbano',


   PRIMARY KEY (id),


   CONSTRAINT fk_omnibus_compania
       FOREIGN KEY (compania_id)
       REFERENCES compania(id)
       ON UPDATE CASCADE
       ON DELETE RESTRICT,


   CONSTRAINT uq_omnibus_compania_numero
       UNIQUE (compania_id, n_omnibus)


) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




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


) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




-- ============================================================
--  5. Relación ómnibus - paradas
-- ============================================================


CREATE TABLE IF NOT EXISTS recorre (
   omnibus_id INT UNSIGNED NOT NULL,
   parada_id INT UNSIGNED NOT NULL,
   orden INT UNSIGNED NOT NULL,

   -- La clave primaria ahora es la combinación del ómnibus y el orden del recorrido
   PRIMARY KEY (omnibus_id, orden),

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
--  6. Tabla de viajes (Modificada: Sin la columna fecha)
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
       ON DELETE RESTRICT,


   CONSTRAINT chk_viajes_origen_destino
       CHECK (origen_id <> destino_id)


) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




-- ============================================================
--  7. Tabla de días de la semana
-- ============================================================


CREATE TABLE IF NOT EXISTS dias_semana (
   id TINYINT UNSIGNED NOT NULL,
   nombre VARCHAR(20) NOT NULL UNIQUE,


   PRIMARY KEY (id)


) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




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


) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  9. Tabla de viajes favoritos (Relación N:M entre users y viajes)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- ============================================================
--  10. Tabla de compañías favoritas (Relación N:M entre users y compania)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  11. Tabla de viajes visitados
-- ============================================================
CREATE TABLE viaje_visita (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;