-- ============================================================
--  Archivo: data.sql
--  Datos iniciales basados estrictamente en la información oficial
-- ============================================================

USE omnibus_app;

-- ============================================================
--  1. Usuarios (la contraseña es: Admin123!)
-- ============================================================

INSERT INTO users
(nombre, apellido, email, telefono, password, tipo)
VALUES
(
   'Administrador',
   'Sistema',
   'admin@omnibus.com',
   '099000001',
   '$2y$10$nAtkg1.6iSydCeNp4CoOGOHXc5t.Im9G8Msorq2HHGy4xvJDe.hVi',
   'administrador'
),
(
   'Juan',
   'Jose',
   'compania@omnibus.com',
   '099000002',
   '$2y$10$nAtkg1.6iSydCeNp4CoOGOHXc5t.Im9G8Msorq2HHGy4xvJDe.hVi',
   'compania'
),
(
   'Mateo',
   'Medero',
   'usuario@usuario.com',
   '099000003',
   '$2y$10$nAtkg1.6iSydCeNp4CoOGOHXc5t.Im9G8Msorq2HHGy4xvJDe.hVi',
   'usuario'
);

-- ============================================================
--  2. Compañías (la contraseña es la misma que usan los usuarios)
-- ============================================================
INSERT INTO compania (nombre, email, telefono, password)
VALUES
('COT', 'contacto@cot.com.uy', '099000003', '$2y$10$nAtkg1.6iSydCeNp4CoOGOHXc5t.Im9G8Msorq2HHGy4xvJDe.hVi'),
('TURIL', 'contacto@turil.com.uy', '099000004', '$2y$10$nAtkg1.6iSydCeNp4CoOGOHXc5t.Im9G8Msorq2HHGy4xvJDe.hVi'),
('TAB', 'contacto@tab.com.uy', '099000005', '$2y$10$nAtkg1.6iSydCeNp4CoOGOHXc5t.Im9G8Msorq2HHGy4xvJDe.hVi'),
('URBANO', 'contacto@urbano.com.uy', '099000006', '$2y$10$nAtkg1.6iSydCeNp4CoOGOHXc5t.Im9G8Msorq2HHGy4xvJDe.hVi');

-- ============================================================
--  3. Días de la semana
-- ============================================================
INSERT INTO dias_semana (id, nombre)
VALUES
(1, 'Lunes'),
(2, 'Martes'),
(3, 'Miercoles'),
(4, 'Jueves'),
(5, 'Viernes'),
(6, 'Sabado'),
(7, 'Domingo');

-- ============================================================
--  4. Paradas
-- ============================================================

INSERT INTO paradas (nombre, latitud, longitud, tipo)
VALUES
('Entre la Plaza de Palos y el minimercado Los Amigos', -34.4179560, -57.4522300, 'urbano'),
('Entre la Calle.4 y Calle.1', -34.4157700, -57.4508890, 'urbano'),
('Entre la calle Juana de Ibarbourou y Julio Rodriguez', -34.4130350, -57.4499560, 'urbano'),
('En la Ruta 54, cerca del Destacamento de Bomberos', -34.4181860, -57.4470380, 'urbano/interurbano'),
('Parada 1, Ruta 54', -34.4153540, -57.4470160, 'urbano/interurbano'),
('Parada 12, Ruta 54', -34.4133890, -57.4470380, 'urbano/interurbano'),
('Parada 9, Ruta 54', -34.4204160, -57.4469730, 'urbano/interurbano'),
('Parada 8, Ruta 54', -34.4226640, -57.4469520, 'urbano/interurbano'),
('Parada 21, Ruta 54', -34.4223280, -57.4474450, 'urbano/interurbano'),
('Parada 19, Ruta 54', -34.4176200, -57.4474670, 'urbano/interurbano'),
('Parada 7, Ruta 54, Liceo n°1', -34.4244520, -57.4460720, 'urbano/interurbano'),
('Parada 6, Ruta 54, Ancap', -34.4269565, -57.4436045, 'urbano/interurbano'),
('Parada 6, Ruta 54, Ancap', -34.4276030, -57.4444200, 'urbano/interurbano'),
('Parada Los Simpson', -34.4298680, -57.4433680, 'urbano/interurbano'),
('Parada 5, Esquina de Salto y Av Gral Artigas', -34.4296380, -57.4431970, 'urbano/interurbano'),
('Parada 25, Estatua La Embarazada', -34.4315670, -57.4416410, 'urbano/interurbano'),
('Parada 26, Estatua Las Manos', -34.4315050, -57.4393240, 'urbano/interurbano'),
('Parada 2, Esquina de Carlos Gardel y Don Bosco', -34.4328240, -57.4351820, 'urbano/interurbano'),
('Parada 27, Calle Don Bosco', -34.4327710, -57.4354180, 'urbano/interurbano'),
('Parada entre calle Simón Betarte y Don Bosco, Kiosco', -34.4361240, -57.4354180, 'urbano/interurbano'),
('Parada 1, TATA', -34.4365310, -57.4354080, 'urbano/interurbano'),
('Parada 29, Esquina del banco viejo', -34.4349560, -57.4376820, 'urbano/interurbano'),
('Esquina Club Uruguay', -34.4311690, -57.4361480, 'urbano/interurbano'),
('Parada 33, Agencia COT', -34.4347350, -57.4401660, 'interurbano'),
('Parada 33, Agencia TAB', -34.4349030, -57.4401340, 'interurbano'),
('Agencia Turil', -34.4356690, -57.4387230, 'interurbano'),
('Parada 17, Ruta 54, Julio Rodriguez', -34.4130260, -57.4474240, 'urbano/interurbano'),
('Parada 16,,Ruta 54, 18 de Mayo', -34.4100250, -57.4474670, 'urbano/interurbano'),
('Esquina entre Calle 33 y Jaime Roca', -34.4200890, -57.4554760, 'urbano'),
('Entre la plaza y Calle Jaime Roca', -34.4201020, -57.4569670, 'urbano'),
('Esquina entre Calle 48 y Mario Echeverría', -34.4177700, -57.4572090, 'urbano'),
('Esquina entre Calle 48 y 44', -34.4161590, -57.4571870, 'urbano'),
('Esquina entre Calle 48 y 46', -34.4143450, -57.4571870, 'urbano'),
('Esquina entre Calle 46 y 47', -34.4143180, -57.4552990, 'urbano'),
('Esquina entre Calle 44 y 47', -34.4159110, -57.4552880, 'urbano'),
('Esquina entre Calle 47 y Mario Echeverría', -34.4176990, -57.4552770, 'urbano'),
('Grito de asencio', -34.3344175, -57.3410844, 'interurbano'),
('Ruta 2 grito de asencio/de lo cumba', -34.3253697, -57.3472101, 'interurbano'),
('Grito de asencio/bvar.Jose Enrique Rodo', -34.3210831, -57.3503399, 'interurbano'),
('Bvar.Jose Enrique Rodo/dr.Ruperto Borras', -34.3175177, -57.3488464, 'interurbano'),
('Gral. artigas/19 de abril', -34.3149070, -57.3544748, 'interurbano'),
('Agencia TAB', -34.3117066, -57.3523464, 'interurbano'),
('Agencia COT', -34.3097590, -57.3510178, 'interurbano'),
('Bvar.Jose Enrique Rodo/25 de mayo', -34.3143331, -57.3472894, 'interurbano'),
('Colonia', -34.4723142, -57.8425525, 'interurbano'),
('Montevideo', -34.8936262, -56.1675478, 'interurbano'),
('Radial de Juan Lacaze', -34.3623357, -57.4469652, 'interurbano'),
('Radial Rosario', -34.3375730,  -57.3398201, 'interurbano'), 
('Juan Lacaze', -34.4356982, -57.4385496, 'urbano/interurbano'),
('UTU', -34.4373289, -57.4347900, 'urbano');

-- ============================================================
--  5. Ómnibus (Asignados a las compañías)
-- ============================================================
INSERT INTO omnibus (compania_id, n_omnibus, asientos, ruta_key)
VALUES
(1, 1, 40, 'interurbano'), -- COT
(2, 1, 40, 'interurbano'), -- TURIL
(3, 1, 40, 'interurbano'), -- TAB
(4, 1, 40, 'urbano'); -- URBANO

-- ============================================================
--  6. Viajes
-- ============================================================
INSERT INTO viajes (id, omnibus_id, origen_id, destino_id, precio, ya_salio, hora, hora_llegada, es_directo, asientos_disponibles)
VALUES
-- TURIL: Radial Rosario a Colonia (7:35 -> 8:20, Lunes a Viernes) [Radial Rosario = 48, Colonia = 45]
(1, 2, 48, 45, 164.00, FALSE, '07:35:00', '08:20:00', FALSE, 40),
-- TURIL: Radial Rosario a Colonia (8:56 -> 9:40, Siempre activo) [Radial Rosario = 48, Colonia = 45]
(2, 2, 48, 45, 164.00, FALSE, '08:56:00', '09:40:00', FALSE, 40),
-- TURIL: Radial Rosario a Colonia (14:41 -> 15:25, Lunes a Sábados) [Radial Rosario = 48, Colonia = 45]
(3, 2, 48, 45, 164.00, FALSE, '14:41:00', '15:25:00', FALSE, 40),
-- TURIL: Radial Rosario a Colonia (23:29 -> 00:10, Viernes y Domingos) [Radial Rosario = 48, Colonia = 45]
(4, 2, 48, 45, 164.00, FALSE, '23:29:00', '00:10:00', FALSE, 40),

-- TURIL: Juan Lacaze a Colonia (7:20, Lunes a Viernes) [Juan Lacaze = 49, Colonia = 45]
(5, 2, 49, 45, 164.00, FALSE, '07:20:00', '08:00:00', FALSE, 40),
-- TURIL: Juan Lacaze a Colonia (14:25, Lunes a Sábados) [Juan Lacaze = 49, Colonia = 45]
(6, 2, 49, 45, 164.00, FALSE, '14:25:00', '15:05:00', FALSE, 40),
-- TURIL: Juan Lacaze a Colonia (23:15, Viernes y Domingos) [Juan Lacaze = 49, Colonia = 45]
(7, 2, 49, 45, 164.00, FALSE, '23:15:00', '23:55:00', FALSE, 40),

-- TAB: Juan Lacaze a Colonia (04:45, Lunes a Viernes) [Juan Lacaze = 49, Colonia = 45]
(8, 3, 49, 45, 150.00, FALSE, '04:45:00', '05:30:00', FALSE, 40),
-- TAB: Juan Lacaze a Colonia (06:15, Lunes a Viernes - Directo) [Juan Lacaze = 49, Colonia = 45]
(9, 3, 49, 45, 150.00, FALSE, '06:15:00', '06:55:00', TRUE, 40),
-- TAB: Juan Lacaze a Colonia (14:00, Lunes a Viernes - Directo) [Juan Lacaze = 49, Colonia = 45]
(10, 3, 49, 45, 150.00, FALSE, '14:00:00', '14:40:00', TRUE, 40),

-- COT: Juan Lacaze a Montevideo (05:40, Menos Domingos) [Juan Lacaze = 49, Montevideo = 46]
(11, 1, 49, 46, 164.00, FALSE, '05:40:00', '08:30:00', FALSE, 40);

-- ============================================================
--  7. Relación Viaje - Días de la semana
-- ============================================================
INSERT INTO viaje_dia (viaje_id, dia_id)
VALUES
-- Viaje 1 (Lunes a Viernes)
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5),
-- Viaje 2 (Siempre activo: Lunes a Domingo)
(2, 1), (2, 2), (2, 3), (2, 4), (2, 5), (2, 6), (2, 7),
-- Viaje 3 (Lunes a Sábados)
(3, 1), (3, 2), (3, 3), (3, 4), (3, 5), (3, 6),
-- Viaje 4 (Viernes y Domingos)
(4, 5), (4, 7),
-- Viaje 5 (Lunes a Viernes)
(5, 1), (5, 2), (5, 3), (5, 4), (5, 5),
-- Viaje 6 (Lunes a Sábados)
(6, 1), (6, 2), (6, 3), (6, 4), (6, 5), (6, 6),
-- Viaje 7 (Viernes y Domingos)
(7, 5), (7, 7),
-- Viaje 8 (Lunes a Viernes)
(8, 1), (8, 2), (8, 3), (8, 4), (8, 5),
-- Viaje 9 (Lunes a Viernes)
(9, 1), (9, 2), (9, 3), (9, 4), (9, 5),
-- Viaje 10 (Lunes a Viernes)
(10, 1), (10, 2), (10, 3), (10, 4), (10, 5),
-- Viaje 11 (Menos Domingos: Lunes a Sábado)
(11, 1), (11, 2), (11, 3), (11, 4), (11, 5), (11, 6);
