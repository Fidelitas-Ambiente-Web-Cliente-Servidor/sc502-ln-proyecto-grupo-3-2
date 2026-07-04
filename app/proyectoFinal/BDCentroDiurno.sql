-- Crear base de datos
CREATE DATABASE centro_diurno;
USE centro_diurno;

CREATE USER 'Pruebas'@'localhost' IDENTIFIED BY 'Pruebas123';

GRANT ALL PRIVILEGES ON centro_diurno.* TO 'Pruebas'@'localhost';
FLUSH PRIVILEGES;
-- Usar la base creada por docker-compose
USE centro_diurno;

-- =========================
-- 1. Tabla de roles
-- =========================
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

-- =========================
-- 2. Tabla de usuarios
-- =========================
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    rol_id INT NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_roles
        FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- =========================
-- 3. Tabla de adultos mayores
-- =========================
CREATE TABLE adultos_mayores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(150) NOT NULL,
    fecha_nacimiento DATE,
    cedula VARCHAR(20),
    direccion VARCHAR(255),
    condiciones_medicas TEXT,
    contacto_familiar_id INT NOT NULL,
    estado VARCHAR(20) DEFAULT 'pendiente',
    CONSTRAINT fk_adultos_usuarios
        FOREIGN KEY (contacto_familiar_id) REFERENCES usuarios(id)
);

-- =========================
-- 4. Tabla de solicitudes de inscripción
-- =========================
CREATE TABLE solicitudes_inscripcion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    adulto_id INT NOT NULL,
    fecha_solicitud DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(20) DEFAULT 'pendiente',
    observaciones_admin TEXT,
    CONSTRAINT fk_solicitudes_adulto
        FOREIGN KEY (adulto_id) REFERENCES adultos_mayores(id)
);

-- =========================
-- 5. Tabla de actividades
-- =========================
CREATE TABLE actividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha DATE NOT NULL,
    hora TIME,
    tipo VARCHAR(50),
    creado_por INT,
    CONSTRAINT fk_actividades_usuarios
        FOREIGN KEY (creado_por) REFERENCES usuarios(id)
);

-- =========================
-- 6. Tabla de citas (módulo de administración de visitas)
-- =========================
CREATE TABLE citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    adulto_id INT NOT NULL,
    familiar_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    motivo TEXT NOT NULL,
    estado VARCHAR(20) DEFAULT 'pendiente',
    creada_por INT NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_citas_adulto
        FOREIGN KEY (adulto_id) REFERENCES adultos_mayores(id),
    CONSTRAINT fk_citas_familiar
        FOREIGN KEY (familiar_id) REFERENCES usuarios(id),
    CONSTRAINT fk_citas_creada_por
        FOREIGN KEY (creada_por) REFERENCES usuarios(id)
);

-- =========================
-- 7. Tabla de contactos del centro (público)
-- =========================
CREATE TABLE contactos_centro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(50),
    valor VARCHAR(100),
    descripcion VARCHAR(100)
);

-- =========================
-- Datos de ejemplo mínimos
-- =========================

INSERT INTO roles (nombre) VALUES ('familiar'), ('admin');

INSERT INTO usuarios (nombre, correo, password, telefono, rol_id)
VALUES ('Admin Centro', 'admin@centro.com', 'password_encriptado', '0000-0000', 2);

INSERT INTO usuarios (nombre, correo, password, telefono, rol_id)
VALUES ('Ana Familiar', 'ana@familia.com', 'password_encriptado', '8888-8888', 1);

INSERT INTO adultos_mayores (nombre_completo, fecha_nacimiento, cedula, direccion, condiciones_medicas, contacto_familiar_id, estado)
VALUES ('Carlos Gómez', '1950-05-10', '1-2345-6789', 'San José, Costa Rica', 'Hipertensión controlada', 2, 'activo');

INSERT INTO actividades (titulo, descripcion, fecha, hora, tipo, creado_por)
VALUES ('Clase de ejercicios suaves', 'Sesión de movilidad para adultos mayores.', '2026-07-15', '09:30:00', 'recreativa', 1);

INSERT INTO citas (adulto_id, familiar_id, fecha, hora, motivo, estado, creada_por)
VALUES (1, 2, '2026-07-20', '10:00:00', 'Visita familiar programada', 'pendiente', 2);