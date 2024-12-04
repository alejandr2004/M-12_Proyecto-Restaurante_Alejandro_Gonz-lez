CREATE DATABASE elmanantial;

USE elmanantial;

CREATE TABLE tbl_sala (
    id_sala INT PRIMARY KEY,
    nombre_sala VARCHAR(25) NOT NULL,
    tipo_sala VARCHAR (20),
    capacidad_total INT NOT NULL,
    FOREIGN KEY (tipo_sala) REFERENCES tbl_tipo_sala(nombre_tipo_sala)
);

CREATE TABLE tbl_tipo_sala (
    id_tipo_sala INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    nombre_tipo_sala VARCHAR(20)
);

CREATE TABLE tbl_mesa (
    id_mesa INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    id_sala INT NOT NULL,
    num_sillas_mesa INT NOT NULL,
    estado_mesa ENUM('libre', 'ocupada') NOT NULL DEFAULT 'libre',
    FOREIGN KEY (id_sala) REFERENCES tbl_sala(id_sala)
);

CREATE TABLE tbl_usuario ( 
    id_usuario INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    nombre_usuario VARCHAR(30) NOT NULL,
    nombre_real VARCHAR(30) NOT NULL,
    apellidos VARCHAR(40) NOT NULL,
    pwd_usuario VARCHAR(20) NOT NULL,
    rol_usuario VARCHAR(20) NOT NULL,
    FOREIGN KEY (rol_usuario) REFERENCES tbl_rol(rol)
);

CREATE TABLE tbl_rol (
    id_rol INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    rol VARCHAR(20)
);

CREATE TABLE tbl_ocupacion (
    id_ocupacion INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    id_mesa INT NOT NULL,
    id_camarero INT NOT NULL,
    fecha_hora_ocupacion DATETIME NOT NULL,
    fecha_hora_desocupacion DATETIME,
    FOREIGN KEY (id_mesa) REFERENCES tbl_mesa(id_mesa),
    FOREIGN KEY (id_camarero) REFERENCES tbl_camarero(id_camarero)
);

CREATE TABLE tbl_reserva (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    dia_reserva INT NOT NULL,
    id_mesa INT NOT NULL,
    id_franja INT NOT NULL,
    FOREIGN KEY (id_mesa) REFERENCES tbl_mesa(id_mesa),
    FOREIGN KEY (id_franja) REFERENCES tbl_franjas_horarias(id_franja)
);

CREATE TABLE tbl_franjas_horarias (
    id_franja INT AUTO_INCREMENT PRIMARY KEY,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    id_mesa INT NOT NULL,
    FOREIGN KEY (id_mesa) REFERENCES tbl_mesa(id_mesa)
);

CREATE TABLE tbl_stock (
    id_stock INT AUTO_INCREMENT PRIMARY KEY NOT NULL,
    id_sala INT NOT NULL,
    cantidad_mesas INT NOT NULL,
    cantidad_stock INT NOT NULL,
    FOREIGN KEY (id_sala) REFERENCES tbl_sala(id_sala)
);



INSERT INTO tbl_sala (id_sala, nombre_sala, tipo_sala, capacidad_total) VALUES
(1, 'terraza_principal', 'terraza', 14),
(2, 'terraza_secundaria', 'terraza', 14),
(3, 'terraza_terciaria', 'terraza', 14),
(4, 'comedor_interior', 'comedor', 29),
(5, 'comedor_exterior', 'comedor', 24),
(6, 'sala_privada_1', 'privada', 10),
(7, 'sala_privada_2', 'privada', 10),
(8, 'sala_privada_3', 'privada', 10),
(9, 'sala_privada_4', 'privada', 10);

INSERT INTO tbl_mesa (id_sala, num_sillas_mesa, estado_mesa) VALUES
-- Terraza Principal
(1, 2, 'libre'),
(1, 3, 'libre'),
(1, 4, 'libre'),
(1, 5, 'libre'),

-- Terraza Secundaria
(2, 2, 'libre'),
(2, 3, 'libre'),
(2, 4, 'libre'),
(2, 5, 'libre'),

-- Terraza Terciaria
(3, 2, 'libre'),
(3, 3, 'libre'),
(3, 4, 'libre'),
(3, 5, 'libre'),

-- Comedor Interior
(4, 4, 'libre'),
(4, 5, 'libre'),
(4, 2, 'libre'),
(4, 4, 'libre'),
(4, 3, 'libre'),
(4, 5, 'libre'),
(4, 6, 'libre'),

-- Comedor Exterior
(5, 4, 'libre'),
(5, 5, 'libre'),
(5, 3, 'libre'),
(5, 6, 'libre'),
(5, 4, 'libre'),
(5, 2, 'libre'),

-- Sala Privada 1
(6, 10, 'libre'),

-- Sala Privada 2
(7, 10, 'libre'),

-- Sala Privada 3
(8, 10, 'libre'),

-- Sala Privada 4
(9, 10, 'libre');

INSERT INTO tbl_rol (rol) VALUES
('Administrador'),
('Camarero'),
('Recepcionista'),
('Jefe de Sala'),
('Chef'),
('Ayudante de Cocina');

-- Insertar usuarios con contraseñas adaptadas para cumplir las validaciones
-- Administrador
INSERT INTO tbl_usuario (nombre_usuario, nombre_real, apellidos, pwd_usuario, rol_usuario) VALUES
('admin01', 'Juan', 'Pérez', 'Admin1234', 'Administrador');

-- Camarero
INSERT INTO tbl_usuario (nombre_usuario, nombre_real, apellidos, pwd_usuario, rol_usuario) VALUES
('camarero01', 'Carlos', 'García', 'Camarero123', 'Camarero');
INSERT INTO tbl_usuario (nombre_usuario, nombre_real, apellidos, pwd_usuario, rol_usuario) VALUES
('camarero02', 'Laura', 'Sánchez', 'Camarero456', 'Camarero');

-- Recepcionista
INSERT INTO tbl_usuario (nombre_usuario, nombre_real, apellidos, pwd_usuario, rol_usuario) VALUES
('recepcionista01', 'María', 'López', 'Recepcionista1', 'Recepcionista');

-- Jefe de Sala
INSERT INTO tbl_usuario (nombre_usuario, nombre_real, apellidos, pwd_usuario, rol_usuario) VALUES
('jefe_sala01', 'Pedro', 'Martínez', 'JefeSala123', 'Jefe de Sala');

-- Chef
INSERT INTO tbl_usuario (nombre_usuario, nombre_real, apellidos, pwd_usuario, rol_usuario) VALUES
('chef01', 'Ana', 'Rodríguez', 'Chef1234', 'Chef');

-- Ayudante de Cocina
INSERT INTO tbl_usuario (nombre_usuario, nombre_real, apellidos, pwd_usuario, rol_usuario) VALUES
('ayudante01', 'Luis', 'Hernández', 'Ayudante123', 'Ayudante de Cocina');

