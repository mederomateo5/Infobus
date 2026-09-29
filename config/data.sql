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
('UTU', -34.4373289, -57.4347900, 'urbano'),
('Esquina entre Dr.Rupero Borras y Ituzaingo0', -34.3163347, -57.3528040, 'interurbano'),
('Parada 1, TATA 2', -34.3163347, -57.3528040, 'interurbano');


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
--Turil
--Turil: Juan Lacaze a Colonia (Luneas a viernes)
(1, 2, 26, 45, 164.00, FALSE, '7:20:00', '8:20:00', FALSE, 40),
--Turil: Juan Lacaze a Colonia
(2, 2, 26, 45, 164.00, FALSE, '8:40:00', '9:40:00', FALSE, 40),
--Turil: Juan Lacaze a Colonia
(3, 2, 26, 45, 164.00, FALSE, '10:25:00', '11:25:00', FALSE, 40),
--Turil: Juan Lacaze a Colonia
(4, 2, 26, 45, 164.00, FALSE, '11:50:00', '12:50:00', FALSE, 40),
--Turil: Juan Lacaze a Colonia (Luneas a sabados)
(5, 2, 26, 45, 164.00, FALSE, '14:25:00', '15:25:00', FALSE, 40),
--Turil: Juan Lacaze a Colonia
(6, 2, 26, 45, 164.00, FALSE, '16:25:00', '17:25:00', FALSE, 40),
--turil: Juan Lacaze a Colonia
(7, 2, 26, 45, 164.00, FALSE, '18:25:00', '19:25:00', FALSE, 40),
--turil: Juan Lacaze a Colonia
(8, 2, 26, 45, 164.00, FALSE, '20:25:00', '21:25:00', FALSE, 40),
--turil: Juan Lacaze a Colonia
(9, 2, 26, 45, 164.00, FALSE, '22:20:00', '23:20:00', FALSE, 40),
--turil: Juan Lacaze a Colonia (Activo los viernes y domingos)
(10, 2, 26, 45, 164.00, FALSE, '23:15:00', '00:15:00', FALSE, 40),
--turil: Rosario a Colonia (Activo los lunes y viernes)
(11, 2, 48, 45, 164.00, FALSE, '7:35:00', '8:20:00', FALSE, 40),
--turil: Rosario a Colonia
(12, 2, 48, 45, 164.00, FALSE, '8:56:00', '9:40:00', FALSE, 40),
--turil: Rosario a Colonia
(13, 2, 48, 45, 164.00, FALSE, '10:38:00', '11:25:00', FALSE, 40),
--turil: Rosario a Colonia (luneas a sabados)
(14, 2, 48, 45, 164.00, FALSE, '14:41:00', '15:25:00', FALSE, 40),
--turil: Rosario a Colonia
(15, 2, 48, 45, 164.00, FALSE, '16:41:00', '17:25:00', FALSE, 40),
--turil:rosario a Colonia
(16, 2, 48, 45, 164.00, FALSE, '18:41:00', '19:05:00', FALSE, 40),
--turil: Rosario a Colonia
(17, 2, 48, 45, 164.00, FALSE, '20:42:00', '21:25:00', FALSE, 40),
--turil: Rosario a Colonia
(18, 2, 48, 45, 164.00, FALSE, '22:35:00', '23:15:00', FALSE, 40),
--turil: Rosario a Colonia (Activo los viernes y domingos)
(19, 2, 48, 45, 164.00, FALSE, '23:29:00', '00:10:00', FALSE, 40),
--turil: colonia a rosario (Activo los lunes y viernes)
(20, 2, 45, 48, 164.00, FALSE, '5:00:00', '5:47:00', FALSE, 40),
--turil: colonia a rosario
(21, 2, 45, 48, 164.00, FALSE, '6:00:00', '6:47:00', FALSE, 40),
--turil: colonia a rosario
(22, 2, 45, 48, 164.00, FALSE, '8:30:00', '9:17:00', FALSE, 40),
--turil: colonia a rosario
(23, 2, 45, 48, 164.00, FALSE, '10:30:00', '11:17:00', FALSE, 40),
--turil: colonia a rosario (activo los lunes a sabados)
(24, 2, 45, 48, 164.00, FALSE, '12:00:00', '12:47:00', FALSE, 40),
--turil: colonia a rosario
(25, 2, 45, 48, 164.00, FALSE, '14:00:00', '14:47:00', FALSE, 40),
--turil: colonia a rosario
(26, 2, 45, 48, 164.00, FALSE, '16:30:00', '17:17:00', FALSE, 40),
--turil: colonia a rosario
(27, 2, 45, 48, 164.00, FALSE, '18:00:00', '18:47:00', FALSE, 40),
--turil: colonia a rosario
(28, 2, 45, 48, 164.00, FALSE, '20:00:00', '20:49:00', FALSE, 40),
--turil: colonia a rosario (Activo los viernes y domingos)
(29, 2, 45, 48, 164.00, FALSE, '21:15:00', '22:00:00', FALSE, 40),
--turil: colonia a juan lacaze (Activo los lunes y viernes)
(30, 2, 45, 49, 164.00, FALSE, '5:00:00', '5:35:00', FALSE, 40),
--turil: colonia a juan lacaze
(31, 2, 45, 49, 164.00, FALSE, '6:00:00', '6:35:00', FALSE, 40),
--turil: colonia a juan lacaze
(32, 2, 45, 49, 164.00, FALSE, '8:30:00', '9:05:00', FALSE, 40),
--turil: colonia a juan lacaze
(33, 2, 45, 49, 164.00, FALSE, '10:30:00', '11:05:00', FALSE, 40),
--turil: colonia a juan lacaze (activo los lunes a sabados)
(34, 2, 45, 49, 164.00, FALSE, '12:00:00', '12:35:00', FALSE, 40),
--turil: colonia a juan lacaze
(35, 2, 45, 49, 164.00, FALSE, '14:00:00', '14:35:00', FALSE, 40),
--turil: colonia a juan lacaze
(36, 2, 45, 49, 164.00, FALSE, '16:30:00', '17:05:00', FALSE, 40),
--turil: colonia a juan lacaze
(37, 2, 45, 49, 164.00, FALSE, '18:00:00', '18:35:00', FALSE, 40),
--turil: colonia a juan lacaze
(38, 2, 45, 49, 164.00, FALSE, '20:00:00', '20:35:00', FALSE, 40),

--TAB
--Tab:juan lacaze a colonia (Activo los lunes y viernes)
(39, 3, 42, 45, 164.00, FALSE, '4:45:00', '5:35:00', FALSE, 40),
--Tab:juan lacaze a colonia
(40, 3, 42, 45, 164.00, FALSE, '6:15:00', '7:05:00', FALSE, 40),
--tab:juan lacaze a colonia
(41, 3, 42, 45, 164.00, FALSE, '8:40:00', '9:30:00', FALSE, 40),
--tab:juan lacaze a colonia
(42, 3, 42, 45, 164.00, FALSE, '11:45:00', '12:35:00', FALSE, 40),
--tab:juan lacaze a colonia
(43, 3, 42, 45, 164.00, FALSE, '14:00:00', '14:50:00', FALSE, 40),
--tab:colonia a juan lacaze (Activo los lunes y viernes)
(44, 3, 45, 42, 164.00, FALSE, '5:40:00', '6:30:00', FALSE, 40),
--tab:colonia a juan lacaze
(45, 3, 45, 42, 164.00, FALSE, '7:40:00', '8:30:00', FALSE, 40),
--tab:colonia  a juan lacaze
(46, 3, 45, 42, 164.00, FALSE, '10:20:00', '11:10:00', FALSE, 40),
--tab:colonia a juan lacaze
(47, 3, 45, 42, 164.00, FALSE, '12:55:00', '13:45:00', FALSE, 40),
--tab:colonia  a juan lacaze
(48, 3, 45, 42, 164.00, FALSE, '15:55:00', '16:45:00', FALSE, 40),
--tab:colonia a juan lacaze
(49, 3, 45, 42, 164.00, FALSE, '18:30:00', '19:20:00', FALSE, 40),
--tab:juan lacaze a rosario (Activo los lunes y viernes)
(50, 3, 42, 48, 100.00, FALSE, '6:35:00', '7:05:00', FALSE, 40),
--tab:juan lacaze a rosario
(51, 3, 42, 48, 100.00, FALSE, '9:00:00', '9:30:00', FALSE, 40),
--tab:juan lacaze a rosario (Activo los lunes a viernes)
(52, 3, 42, 48, 100.00, FALSE, '12:30:00', '13:00:00', FALSE, 40),
--tab:juan lacaze a rosario  (Activo los lunes y viernes)
(53, 3, 42, 48, 100.00, FALSE, '14:00:00', '14:30:00', FALSE, 40),
--tab:juan lacaze a rosario (Activo los lunes y viernes)
(54, 3, 42, 48, 100.00, FALSE, '17:30:00', '18:00:00', FALSE, 40),
--tab:juan lacaze a rosario (Activo los lunes y viernes)
(55, 3, 42, 48, 100.00, FALSE, '18:50:00', '19:20:00', FALSE, 40),
--tab:rosario a juan lacaze (Activo los lunes y viernes)
(56, 3, 48, 42, 100.00, FALSE, '7:15:00', '7:45:00', FALSE, 40),
--tab:rosario a juan lacaze
(57, 3, 48, 42, 100.00, FALSE, '11:15:00', '11:45:00', FALSE, 40),
--tab:rosario a juan lacaze (Activo los lunes y viernes)
(58, 3, 48, 42, 100.00, FALSE, '13:15:00', '13:45:00', FALSE, 40),
--tab:rosario a juan lacaze (Activo los lunes y viernes)
(59, 3, 48, 42, 100.00, FALSE, '15:15:00', '15:45:00', FALSE, 40),
--tab:rosario a juan lacaze (Activo los lunes y viernes)
(60, 3, 48, 42, 100.00, FALSE, '18:10:00', '18:40:00', FALSE, 40),
--tab:rosario a juan lacaze (Activo los lunes y viernes)
(61, 3, 48, 42, 100.00, FALSE, '19:55:00', '20:25:00', FALSE, 40);
--tab:rosario a colonia
(62, 3, 48, 45, 164.00, FALSE, '21:10:00', '21:50:00', FALSE, 40),
--tab:colonia a rosario
(63, 3, 45, 48, 164.00, FALSE, '10:40:00', '11:25:00', FALSE, 40),

--COT
--cot:rosario a colonia (menos domingos)
(64, 1, 48, 45, 164.00, FALSE, '6:40:00', '7:25:00', TRUE, 40),
--cot:rosario a colonia
(65, 1, 48, 45, 164.00, FALSE, '7:40:00', '8:25:00', TRUE, 40),
--cot:rosario a colonia
(66, 1, 48, 45, 164.00, FALSE, '9:35:00', '10:20:00', TRUE, 40),
--cot:rosario a colonia
(67, 1, 48, 45, 164.00, FALSE, '11:20:00', '12:05:00', TRUE, 40),
--cot:rosario a colonia
(68, 1, 48, 45, 164.00, FALSE, '13:20:00', '14:05:00', TRUE, 40),
--cot:rosario a colonia
(69, 1, 48, 45, 164.00, FALSE, '15:25:00', '16:10:00', TRUE, 40),
--cot:rosario a colonia
(70, 1, 48, 45, 164.00, FALSE, '17:20:00', '18:05:00', TRUE, 40),
--cot:rosario a colonia
(71, 1, 48, 45, 164.00, FALSE, '19:25:00', '20:10:00', TRUE, 40),
--cot:rosario a colonia
(72, 1, 48, 45, 164.00, FALSE, '21:00:00', '22:45:00', TRUE, 40),
--cot:rosario a colonia
(73, 1, 48, 45, 164.00, FALSE, '22:30:00', '23:15:00', TRUE, 40),
--cot:rosario a colonia
(74, 1, 48, 45, 164.00, FALSE, '23:30:00', '00:15:00', TRUE, 40),
--cot:rosario a colonia
(75, 1, 48, 45, 164.00, FALSE, '1:35:00', '2:20:00', TRUE, 40),
--cot:colonia a rosario
(76, 1, 45, 48, 164.00, FALSE, '5:30:00', '6:15:00', TRUE, 40),
--cot:colonia a rosario
(77, 1, 45, 48, 164.00, FALSE, '6:45:00', '7:30:00', TRUE, 40),
--cot:colonia a rosario (lunes a sabados)
(78, 1, 45, 48, 164.00, FALSE, '8:00:00', '8:45:00', TRUE, 40),
--cot:colonia a rosario
(79, 1, 45, 48, 164.00, FALSE, '9:15:00', '10:00:00', TRUE, 40),
--cot:colonia a rosario
(80, 1, 45, 48, 164.00, FALSE, '11:00:00', '11:45:00', TRUE, 40),
--cot:colonia a rosario
(81, 1, 45, 48, 164.00, FALSE, '13:00:00', '13:45:00', TRUE, 40),
--cot:colonia a rosario
(82, 1, 45, 48, 164.00, FALSE, '15:00:00', '15:45:00', TRUE, 40)
--cot:colonia a rosario
(83, 1, 45, 48, 164.00, FALSE, '16:00:00', '16:45:00', TRUE, 40),
--cot:colonia a rosario
(84, 1, 45, 48, 164.00, FALSE, '17:30:00', '18:15:00', TRUE, 40),
--cot:colonia a rosario
(85, 1, 45, 48, 164.00, FALSE, '19:00:00', '19:45:00', TRUE, 40),
--cot:colonia a rosario
(86, 1, 45, 48, 164.00, FALSE, '20:30:00', '21:15:00', TRUE, 40),
--cot:colonia a rosario
(87, 1, 45, 48, 164.00, FALSE, '22:00:00', '22:45:00', TRUE, 40),
--cot:juan lacaze a colonia (menos domingos)
(88, 1, 49, 45, 164.00, FALSE, '6:50:00', '7:40:00', TRUE, 40),
--cot:juan lacaze a colonia
(89, 1, 49, 45, 164.00, FALSE, '7:45:00', '8:35:00', TRUE, 40),
--cot:juan lacaze a colonia
(90, 1, 49, 45, 164.00, FALSE, '9:30:00', '10:20:00', TRUE, 40),
--cot:juan lacaze a colonia
(91, 1, 49, 45, 164.00, FALSE, '11:15:00', '12:05:00', TRUE, 40),
--cot:juan lacaze a colonia
(92, 1, 49, 45, 164.00, FALSE, '13:10:00', '14:00:00', TRUE, 40)
--cot:juan lacaze a colonia
(93, 1, 49, 45, 164.00, FALSE, '15:15:00', '16:05:00', TRUE, 40),
--cot:juan lacaze a colonia
(94, 1, 49, 45, 164.00, FALSE, '17:45:00', '18:35:00', TRUE, 40),
--cot:juan lacaze a colonia
(95, 1, 49, 45, 164.00, FALSE, '19:15:00', '20:05:00', TRUE, 40),
--cot:juan lacaze a colonia
(96, 1, 49, 45, 164.00, FALSE, '20:45:00', '22:45:00', TRUE, 40),
--cot:juan lacaze a colonia
(97, 1, 49, 45, 164.00, FALSE, '22:15:00', '23:05:00', TRUE, 40),
--cot:colonia a juan lacaze
(98, 1, 45, 49, 164.00, FALSE, '5:30:00', '6:05:00', TRUE, 40),
--cot:colonia a juan lacaze
(99, 1, 45, 49, 164.00, FALSE, '6:45:00', '7:20:00', TRUE, 40),
--cot:colonia a juan lacaze (lunes a sabados)
(100, 1, 45, 49, 164.00, FALSE, '8:00:00', '8:35:00', TRUE, 40),
--cot:colonia a juan lacaze
(101, 1, 45, 49, 164.00, FALSE, '9:15:00', '9:50:00', TRUE, 40),
--cot:colonia a juan lacaze
(102, 1, 45, 49, 164.00, FALSE, '11:00:00', '11:35:00', TRUE, 40),
--cot:colonia a juan lacaze
(103, 1, 45, 49, 164.00, FALSE, '13:00:00', '13:35:00', TRUE, 40),
--cot:colonia a juan lacaze
(104, 1, 45, 49, 164.00, FALSE, '15:00:00', '15:35:00', TRUE, 40),
--cot:colonia a juan lacaze
(105, 1, 45, 49, 164.00, FALSE, '16:00:00', '16:35:00', TRUE, 40),
--cot:colonia a juan lacaze
(106, 1, 45, 49, 164.00, FALSE, '17:30:00', '18:05:00', TRUE, 40),
--cot:colonia a juan lacaze
(107, 1, 45, 49, 164.00, FALSE, '19:00:00', '19:35:00', TRUE, 40),
--cot:colonia a juan lacaze
(108, 1, 45, 49, 164.00, FALSE, '20:30:00', '21:05:00', TRUE, 40),
--cot:colonia a juan lacaze
(109, 1, 45, 49, 164.00, FALSE, '22:00:00', '22:35:00', TRUE, 40),
--cot:rosario a juan lacaze (menos domingos)
(110, 1, 48, 49, 100.00, FALSE, '6:40:00', '7:05:00', TRUE, 40),
--cot:rosario a juan lacaze
(111, 1, 48, 49, 100.00, FALSE, '7:40:00', '8:05:00', TRUE, 40),
--cot:rosario a juan lacaze
(112, 1, 48, 49, 100.00, FALSE, '9:35:00', '10:00:00', TRUE, 40),
--cot:rosario a juan lacaze
(113, 1, 48, 49, 100.00, FALSE, '11:20:00', '11:45:00', TRUE, 40),
--cot:rosario a juan lacaze
(114, 1, 48, 49, 100.00, FALSE, '13:20:00', '13:45:00', TRUE, 40),
--cot:rosario a juan lacaze
(115, 1, 48, 49, 100.00, FALSE, '15:25:00', '15:50:00', TRUE, 40),
--cot:rosario a juan lacaze
(116, 1, 48, 49, 100.00, FALSE, '17:20:00', '17:45:00', TRUE, 40),
--cot:rosario a juan lacaze
(117, 1, 48, 49, 100.00, FALSE, '19:25:00', '19:50:00', TRUE, 40),
--cot:rosario a juan lacaze
(118, 1, 48, 49, 100.00, FALSE, '21:00:00', '21:25:00', TRUE, 40),
--cot:rosario a juan lacaze
(119, 1, 48, 49, 100.00, FALSE, '22:30:00', '22:55:00', TRUE, 40),
--cot:rosario a juan lacaze
(120, 1, 48, 49, 100.00, FALSE, '23:30:00', '23:55:00', TRUE, 40),
--cot:rosario a juan lacaze
(121, 1, 48, 49, 100.00, FALSE, '1:30:00', '1:55:00', TRUE, 40),
--cot:juan lacaze a rosario (menos domingos)
(122, 1, 49, 48, 100.00, FALSE, '5:40:00', '5:55:00', TRUE, 40),
--cot:juan lacaze a rosario
(123, 1, 49, 48, 100.00, FALSE, '6:50:00', '7:15:00', TRUE, 40),
--cot:juan lacaze a rosario
(124, 1, 49, 48, 100.00, FALSE, '8:15:00', '8:40:00', TRUE, 40),
--cot:juan lacaze a rosario
(125, 1, 49, 48, 100.00, FALSE, '9:30:00', '9:55:00', TRUE, 40),
--cot:juan lacaze a rosario
(126, 1, 49, 48, 100.00, FALSE, '11:15:00', '11:40:00', TRUE, 40),
--cot:juan lacaze a rosario
(127, 1, 49, 48, 100.00, FALSE, '13:10:00', '13:35:00', TRUE, 40),
--cot:juan lacaze a rosario
(128, 1, 49, 48, 100.00, FALSE, '15:10:00', '15:35:00', TRUE, 40),
--cot:juan lacaze a rosario
(129, 1, 49, 48, 100.00, FALSE, '17:25:00', '17:50:00', TRUE, 40),
--cot:juan lacaze a rosario
(130, 1, 49, 48, 100.00, FALSE, '19:25:00', '19:50:00', TRUE, 40),
--cot:juan lacaze a rosario
(131, 1, 49, 48, 100.00, FALSE, '21:20:00', '21:45:00', TRUE, 40),
--cot:juan lacaze a rosario
(132, 1, 49, 48, 100.00, FALSE, '22:45:00', '23:10:00', TRUE, 40),
--cot:juan lacaze a rosario
(133, 1, 49, 48, 100.00, FALSE, '23:45:00', '00:10:00', TRUE, 40),
--cot:juan lacaze a rosario
(134, 1, 49, 48, 100.00, FALSE, '1:40:00', '2:05:00', TRUE, 40);


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
