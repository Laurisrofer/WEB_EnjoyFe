-- 1. CREACIÓN DE LA BASE DE DATOS
DROP DATABASE IF EXISTS Enjoyfe;
CREATE DATABASE IF NOT EXISTS Enjoyfe;
USE Enjoyfe;

-- 2. TABLA DE USUARIOS
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL, 
    nombre_completo VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    rol ENUM('admin', 'profesor', 'alumno') NOT NULL, 
    estado ENUM('activo', 'inactivo', 'borrado') DEFAULT 'activo',
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_ultimo_login DATETIME,
    primer_inicio TINYINT DEFAULT 1
);

-- 3. TABLA HISTÓRICO DE USUARIOS
CREATE TABLE usuarios_historico (
    id_historico INT AUTO_INCREMENT PRIMARY KEY,
    id_original INT NOT NULL,
    nombre_usuario VARCHAR(50),
    rol VARCHAR(20),
    email VARCHAR(100),
    fecha_borrado DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 4. TABLA LOGS DE ACCESO
CREATE TABLE logs_acceso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    accion VARCHAR(255) DEFAULT 'Inicio de sesión',
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
);

-- 5. TABLA CURSOS
CREATE TABLE cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255)
);

-- 6. TABLA ASIGNATURAS
CREATE TABLE asignaturas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    id_curso INT NOT NULL,
    id_profesor INT,
    FOREIGN KEY (id_curso) REFERENCES cursos(id),
    FOREIGN KEY (id_profesor) REFERENCES usuarios(id)
);

-- 7. TABLA MATRÍCULAS
CREATE TABLE matriculas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_alumno INT NOT NULL,
    id_asignatura INT NOT NULL,
    nota_final DECIMAL(4, 2),
    observaciones_globales TEXT,
    FOREIGN KEY (id_alumno) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_asignatura) REFERENCES asignaturas(id) ON DELETE CASCADE,
    UNIQUE(id_alumno, id_asignatura) 
);

-- 8. TABLA CALIFICACIONES
CREATE TABLE calificaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_matricula INT NOT NULL,
    nombre_actividad VARCHAR(100) NOT NULL,
    nota DECIMAL(4, 2),
    comentario TEXT,
    fecha_calificacion DATE DEFAULT (CURRENT_DATE),
    FOREIGN KEY (id_matricula) REFERENCES matriculas(id) ON DELETE CASCADE
);

-- 9. TABLA ASISTENCIAS
CREATE TABLE asistencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_alumno INT NOT NULL,
    id_asignatura INT NOT NULL,
    fecha DATE NOT NULL,
    tipo ENUM('asistencia', 'falta', 'retraso') NOT NULL DEFAULT 'falta',
    justificada BOOLEAN DEFAULT FALSE,
    observaciones VARCHAR(255),
    FOREIGN KEY (id_alumno) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_asignatura) REFERENCES asignaturas(id) ON DELETE CASCADE
);

-- 10. TABLA TUTORÍAS
CREATE TABLE tutorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_profesor INT NOT NULL,
    id_alumno INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    asunto VARCHAR(150),
    estado ENUM('Pendiente', 'Realizada', 'Cancelada') DEFAULT 'Pendiente',
    notas_reunion TEXT,
    FOREIGN KEY (id_profesor) REFERENCES usuarios(id),
    FOREIGN KEY (id_alumno) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- 11. TABLA HORARIOS
CREATE TABLE IF NOT EXISTS horarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_curso INT NOT NULL,
    id_asignatura INT NOT NULL,
    dia_semana INT NOT NULL COMMENT '1=Lunes, 2=Martes, 3=Miércoles, 4=Jueves, 5=Viernes',
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    FOREIGN KEY (id_curso) REFERENCES cursos(id) ON DELETE CASCADE,
    FOREIGN KEY (id_asignatura) REFERENCES asignaturas(id) ON DELETE CASCADE
);

-- 12. TABLA GMAIL
CREATE TABLE mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_remitente INT NOT NULL,
    id_destinatario INT NOT NULL,
    asunto VARCHAR(150),
    mensaje TEXT,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    leido TINYINT DEFAULT 0,
    FOREIGN KEY (id_remitente) REFERENCES usuarios(id),
    FOREIGN KEY (id_destinatario) REFERENCES usuarios(id)
);