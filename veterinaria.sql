-- =========================================================
-- LIMPIEZA DE TABLAS PREVIAS (ELIMINA SI YA EXISTEN)
-- =========================================================
DROP TABLE IF EXISTS APLICACION_VACUNA;
DROP TABLE IF EXISTS VACUNA;
DROP TABLE IF EXISTS CONSULTA;
DROP TABLE IF EXISTS VETERINARIO;
DROP TABLE IF EXISTS MASCOTA;
DROP TABLE IF EXISTS CLIENTE;
DROP TABLE IF EXISTS RAZA;
DROP TABLE IF EXISTS ESPECIE;


-- =========================================================
-- PARTE 4. MODELO FÍSICO - CREACIÓN DE TABLAS
-- =========================================================

CREATE TABLE ESPECIE (
    id_especie INT NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    CONSTRAINT PK_ESPECIE PRIMARY KEY (id_especie)
);

CREATE TABLE RAZA (
    id_raza INT NOT NULL,
    id_especie INT NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    CONSTRAINT PK_RAZA PRIMARY KEY (id_raza),
    CONSTRAINT FK_RAZA_ESPECIE FOREIGN KEY (id_especie) REFERENCES ESPECIE(id_especie)
);

CREATE TABLE CLIENTE (
    id_cliente INT NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    correo VARCHAR(100),
    direccion VARCHAR(200),
    CONSTRAINT PK_CLIENTE PRIMARY KEY (id_cliente)
);

CREATE TABLE MASCOTA (
    id_mascota INT NOT NULL,
    id_cliente INT NOT NULL,
    id_raza INT NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    fecha_nacimiento DATE,
    sexo VARCHAR(10),
    peso DECIMAL(5,2),
    color VARCHAR(30),
    CONSTRAINT PK_MASCOTA PRIMARY KEY (id_mascota),
    CONSTRAINT FK_MASCOTA_CLIENTE FOREIGN KEY (id_cliente) REFERENCES CLIENTE(id_cliente),
    CONSTRAINT FK_MASCOTA_RAZA FOREIGN KEY (id_raza) REFERENCES RAZA(id_raza)
);

CREATE TABLE VETERINARIO (
    id_veterinario INT NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    colegiado VARCHAR(20) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(100),
    CONSTRAINT PK_VETERINARIO PRIMARY KEY (id_veterinario)
);

CREATE TABLE CONSULTA (
    id_consulta INT NOT NULL,
    id_mascota INT NOT NULL,
    id_veterinario INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    motivo VARCHAR(250) NOT NULL,
    diagnostico VARCHAR(500),
    observaciones VARCHAR(500),
    CONSTRAINT PK_CONSULTA PRIMARY KEY (id_consulta),
    CONSTRAINT FK_CONSULTA_MASCOTA FOREIGN KEY (id_mascota) REFERENCES MASCOTA(id_mascota),
    CONSTRAINT FK_CONSULTA_VETERINARIO FOREIGN KEY (id_veterinario) REFERENCES VETERINARIO(id_veterinario)
);

CREATE TABLE VACUNA (
    id_vacuna INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(250),
    CONSTRAINT PK_VACUNA PRIMARY KEY (id_vacuna)
);

CREATE TABLE APLICACION_VACUNA (
    id_aplicacion INT NOT NULL,
    id_mascota INT NOT NULL,
    id_vacuna INT NOT NULL,
    fecha_aplicacion DATE NOT NULL,
    proxima_fecha_refuerzo DATE,
    CONSTRAINT PK_APLICACION_VACUNA PRIMARY KEY (id_aplicacion),
    CONSTRAINT FK_APLICACION_MASCOTA FOREIGN KEY (id_mascota) REFERENCES MASCOTA(id_mascota),
    CONSTRAINT FK_APLICACION_VACUNA FOREIGN KEY (id_vacuna) REFERENCES VACUNA(id_vacuna)
);


-- =========================================================
-- PARTE 5. INSERCIÓN DE DATOS DE PRUEBA
-- =========================================================

-- Inserción de 3 Especies
INSERT INTO ESPECIE VALUES (1, 'Perro'), (2, 'Gato'), (3, 'Conejo');

-- Inserción de 8 Razas
INSERT INTO RAZA VALUES 
(1, 1, 'Labrador'), (2, 1, 'Pastor Alemán'), (3, 1, 'Chihuahua'),
(4, 2, 'Siamés'), (5, 2, 'Persa'), (6, 2, 'Angora'),
(7, 3, 'Cabeza de León'), (8, 3, 'Belier');

-- Inserción de 5 Clientes
INSERT INTO CLIENTE VALUES 
(1, 'Carlos', 'Mendoza', '5555-1234', 'carlos@mail.com', 'Zona 1, Guatemala'),
(2, 'Ana', 'Gómez', '4444-5678', 'ana@mail.com', 'Zona 10, Guatemala'),
(3, 'Luis', 'Martínez', '3333-9012', 'luis@mail.com', 'Zona 5, Guatemala'),
(4, 'María', 'Rodríguez', '2222-3456', 'maria@mail.com', 'Mixco, Guatemala'),
(5, 'Jorge', 'López', '6666-7890', 'jorge@mail.com', 'Villa Nueva, Guatemala');

-- Inserción de 10 Mascotas
INSERT INTO MASCOTA VALUES 
(1, 1, 1, 'Rocky', '2022-03-15', 'Macho', 25.50, 'Café'),
(2, 1, 3, 'Luna', '2023-06-20', 'Hembra', 3.20, 'Blanco'),
(3, 2, 4, 'Michi', '2021-01-10', 'Macho', 4.50, 'Gris'),
(4, 2, 5, 'Pelusa', '2022-08-05', 'Hembra', 3.80, 'Blanco'),
(5, 3, 2, 'Rex', '2020-11-22', 'Macho', 30.00, 'Negro'),
(6, 4, 6, 'Simba', '2023-02-18', 'Macho', 5.10, 'Amarillo'),
(7, 4, 7, 'Tambor', '2024-01-05', 'Macho', 1.80, 'Gris'),
(8, 5, 1, 'Bella', '2021-05-30', 'Hembra', 22.40, 'Dorado'),
(9, 5, 3, 'Chispa', '2023-10-12', 'Hembra', 2.50, 'Manchado'),
(10, 3, 8, 'Copito', '2024-02-01', 'Macho', 1.60, 'Blanco');

-- Inserción de 4 Veterinarios
INSERT INTO VETERINARIO VALUES 
(1, 'Dr. Alejandro', 'Pérez', 'COL-9876', '7777-1111', 'alejandro@veterinaria.com'),
(2, 'Dra. Beatriz', 'Sosa', 'COL-5432', '7777-2222', 'beatriz@veterinaria.com'),
(3, 'Dr. Carlos', 'Fuentes', 'COL-1234', '7777-3333', 'carlos@veterinaria.com'),
(4, 'Dra. Diana', 'Morales', 'COL-8765', '7777-4444', 'diana@veterinaria.com');

-- Inserción de 10 Consultas
INSERT INTO CONSULTA VALUES 
(1, 1, 1, '2026-08-01', '09:30:00', 'Control anual', 'Sano', 'Programar refuerzos'),
(2, 3, 2, '2026-08-01', '10:15:00', 'Vómitos', 'Infección leve', 'Dieta blanda por 3 días'),
(3, 5, 3, '2026-08-02', '11:00:00', 'Cojera pata trasera', 'Esguince', 'Reposo y antiinflamatorios'),
(4, 2, 1, '2026-08-02', '14:30:00', 'Vacunación', 'Sano', 'Aplicar antirrábica'),
(5, 6, 4, '2026-08-03', '08:45:00', 'Revisión de oídos', 'Otitis', 'Limpieza y gotas óticas'),
(6, 8, 2, '2026-08-03', '16:00:00', 'Pérdida de apetito', 'Fiebre', 'Antibiótico inyectado'),
(7, 1, 1, '2026-08-04', '10:00:00', 'Chequeo post-operatorio', 'Excelente evolución', 'Alta médica'),
(8, 4, 3, '2026-08-04', '15:15:00', 'Corte de uñas y baño', 'Estética', 'Ninguna'),
(9, 7, 4, '2026-08-05', '09:00:00', 'Vacunación', 'Sano', 'Aplicar triple viral'),
(10, 3, 2, '2026-08-05', '11:30:00', 'Revisión de puntos', 'Herida cerrada', 'Retirar puntos');

-- Inserción de 5 Vacunas
INSERT INTO VACUNA VALUES 
(1, 'Antirrábica', 'Protección contra el virus de la rabia'),
(2, 'Triple Viral Felina', 'Protección contra panleucopenia, calicivirus y rinotraqueitis'),
(3, 'Parvovirus', 'Protección contra parvovirus canino'),
(4, 'Quíntuple Canina', 'Protección múltiple para perros'),
(5, 'Rabbit Hemorrhagic', 'Protección contra enfermedad hemorrágica del conejo');

-- Inserción de 10 Aplicaciones de Vacunas
INSERT INTO APLICACION_VACUNA VALUES 
(1, 1, 1, '2026-08-01', '2027-08-01'),
(2, 1, 4, '2026-08-01', '2027-02-01'),
(3, 3, 2, '2026-08-01', '2027-08-01'),
(4, 2, 1, '2026-08-02', '2027-08-02'),
(5, 4, 2, '2026-08-04', '2027-08-04'),
(6, 5, 4, '2026-08-02', '2027-08-02'),
(7, 6, 2, '2026-08-03', '2027-08-03'),
(8, 7, 5, '2026-08-05', '2027-08-05'),
(9, 8, 1, '2026-08-03', '2027-08-03'),
(10, 8, 3, '2026-08-03', '2027-02-03');


-- =========================================================
-- PARTE 6. REGISTRO DE DATOS PERSONALES (TAREA)
-- =========================================================

-- 1. Insertar tus datos personales como Cliente
INSERT INTO CLIENTE (id_cliente, nombres, apellidos, telefono, correo, direccion) 
VALUES (6, 'Luis Javier', 'Betancourth Morales', '39342536', 'luisbetancourth88@gmail.com', '16 av 24-18 zona, Guatemala');

-- 2. Insertar a tu gato 'Tutto'
INSERT INTO MASCOTA (id_mascota, id_cliente, id_raza, nombre, fecha_nacimiento, sexo, peso, color) 
VALUES (11, 6, 4, 'Tutto', '2025-01-15', 'Macho', 4.00, 'Gris');

-- =========================================================
-- PARTE 6. CONSULTAS DE SELECCIÓN REQUERIDAS
-- =========================================================

-- 1. Mostrar todas las mascotas junto con el nombre de su propietario.
SELECT M.id_mascota, M.nombre AS nombre_mascota, C.nombres AS nombre_propietario, C.apellidos AS apellido_propietario
FROM MASCOTA M
INNER JOIN CLIENTE C ON M.id_cliente = C.id_cliente;

-- 2. Mostrar todas las mascotas con su especie y raza.
SELECT M.id_mascota, M.nombre AS nombre_mascota, E.nombre AS especie, R.nombre AS raza
FROM MASCOTA M
INNER JOIN RAZA R ON M.id_raza = R.id_raza
INNER JOIN ESPECIE E ON R.id_especie = E.id_especie;

-- 3. Mostrar las consultas realizadas por cada veterinario.
SELECT V.nombres AS veterinario_nombre, V.apellidos AS veterinario_apellido, C.id_consulta, C.fecha, C.motivo
FROM VETERINARIO V
INNER JOIN CONSULTA C ON V.id_veterinario = C.id_veterinario;

-- 4. Listar el historial de consultas de una mascota (Mascota ID 1 - Rocky).
SELECT M.nombre AS nombre_mascota, C.id_consulta, C.fecha, C.hora, C.motivo, C.diagnostico
FROM CONSULTA C
INNER JOIN MASCOTA M ON C.id_mascota = M.id_mascota
WHERE M.id_mascota = 1;

-- 5. Mostrar las vacunas aplicadas a cada mascota.
SELECT M.nombre AS nombre_mascota, V.nombre AS nombre_vacuna, AV.fecha_aplicacion, AV.proxima_fecha_refuerzo
FROM APLICACION_VACUNA AV
INNER JOIN MASCOTA M ON AV.id_mascota = M.id_mascota
INNER JOIN VACUNA V ON AV.id_vacuna = V.id_vacuna;

-- 6. Contar cuántas mascotas tiene registrado cada cliente.
SELECT C.nombres, C.apellidos, COUNT(M.id_mascota) AS total_mascotas
FROM CLIENTE C
LEFT JOIN MASCOTA M ON C.id_cliente = M.id_cliente
GROUP BY C.id_cliente, C.nombres, C.apellidos;

-- 7. Contar cuántas consultas ha realizado cada veterinario.
SELECT V.nombres, V.apellidos, COUNT(C.id_consulta) AS total_consultas
FROM VETERINARIO V
LEFT JOIN CONSULTA C ON V.id_veterinario = C.id_veterinario
GROUP BY V.id_veterinario, V.nombres, V.apellidos;

-- 8. Mostrar las especies junto con la cantidad de razas registradas.
SELECT E.nombre AS especie, COUNT(R.id_raza) AS cantidad_razas
FROM ESPECIE E
LEFT JOIN RAZA R ON E.id_especie = R.id_especie
GROUP BY E.id_especie, E.nombre;

-- 9. Mostrar las mascotas que aún no tienen ninguna consulta registrada.
SELECT M.id_mascota, M.nombre AS nombre_mascota
FROM MASCOTA M
LEFT JOIN CONSULTA C ON M.id_mascota = C.id_mascota
WHERE C.id_consulta IS NULL;

-- 10. Mostrar las mascotas que han recibido más de una vacuna.
SELECT M.nombre AS nombre_mascota, COUNT(AV.id_aplicacion) AS total_vacunas_recibidas
FROM MASCOTA M
INNER JOIN APLICACION_VACUNA AV ON M.id_mascota = AV.id_mascota
GROUP BY M.id_mascota, M.nombre
HAVING COUNT(AV.id_aplicacion) > 1;