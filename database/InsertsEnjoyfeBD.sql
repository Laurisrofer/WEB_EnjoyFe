-- ------------------
-- 0. INSERTS CURSOS
-- ------------------
INSERT INTO cursos (nombre, descripcion) VALUES 
('1º DAM', 'Desarrollo de Aplicaciones Multiplataforma - Primer curso'),
('2º DAM', 'Desarrollo de Aplicaciones Multiplataforma - Segundo curso'),
('1º DAW', 'Desarrollo de Aplicaciones Web - Primer curso'),
('2º DAW', 'Desarrollo de Aplicaciones Web - Segundo curso'),
('1º SMR', 'Sistemas Microinformáticos y Redes - Primer curso'),
('2º SMR', 'Sistemas Microinformáticos y Redes - Segundo curso'),
('Master Ciberseguridad', 'Curso de Especialización en Ciberseguridad en Entornos TI'),
('3º DAM-DAW', 'Doble titulación: Curso puente entre DAM y DAW'),
('1º ASIR', 'Administración de Sistemas Informáticos en Red - Primer curso'),
('2º ASIR', 'Administración de Sistemas Informáticos en Red - Segundo curso');

-- ---------------------------------------------------------
-- 1. GRADO SUPERIOR DAM (Desarrollo de Aplicaciones Multiplataforma)
-- ---------------------------------------------------------

-- 1º DAM (ID Curso: 1)
INSERT INTO asignaturas (nombre, id_curso, id_profesor) VALUES 
('Sistemas informáticos', 1, NULL),
('Bases de Datos', 1, NULL),
('Programación', 1, NULL),
('Lenguajes de marcas y sistemas de gestión de información', 1, NULL),
('Entornos de desarrollo', 1, NULL),
('Inglés Profesional (Grado Superior)', 1, NULL),
('Itinerario personal para la empleabilidad I', 1, NULL),
('Digitalización aplicada a los sectores productivos', 1, NULL),
('Sostenibilidad aplicada al sistema productivo', 1, NULL);

-- 2º DAM (ID Curso: 2)
INSERT INTO asignaturas (nombre, id_curso, id_profesor) VALUES 
('Acceso a datos', 2, NULL),
('Desarrollo de interfaces', 2, NULL),
('Programación multimedia y dispositivos móviles', 2, NULL),
('Programación de servicios y procesos', 2, NULL),
('Sistemas de gestión empresarial', 2, NULL),
('Itinerario personal para la empleabilidad II', 2, NULL),
('Proyecto intermodular de desarrollo de aplicaciones multiplataforma', 2, NULL),
('Módulo profesional optativo', 2, NULL);


-- ---------------------------------------------------------
-- 2. GRADO SUPERIOR DAW (Desarrollo de Aplicaciones Web)
-- ---------------------------------------------------------

-- 1º DAW (ID Curso: 3) - (Común con 1º DAM)
INSERT INTO asignaturas (nombre, id_curso, id_profesor) VALUES 
('Sistemas informáticos', 3, NULL),
('Bases de Datos', 3, NULL),
('Programación', 3, NULL),
('Lenguajes de marcas y sistemas de gestión de información', 3, NULL),
('Entornos de desarrollo', 3, NULL),
('Inglés Profesional (Grado Superior)', 3, NULL),
('Itinerario personal para la empleabilidad I', 3, NULL),
('Digitalización aplicada a los sectores productivos', 3, NULL),
('Sostenibilidad aplicada al sistema productivo', 3, NULL);

-- 2º DAW (ID Curso: 4)
INSERT INTO asignaturas (nombre, id_curso, id_profesor) VALUES 
('Desarrollo web en entorno cliente', 4, NULL),
('Desarrollo web en entorno servidor', 4, NULL),
('Despliegue de aplicaciones web', 4, NULL),
('Diseño de interfaces WEB', 4, NULL),
('Itinerario personal para la empleabilidad II', 4, NULL),
('Proyecto intermodular de desarrollo de aplicaciones web', 4, NULL),
('Módulo profesional optativo', 4, NULL);


-- ---------------------------------------------------------
-- 3. GRADO MEDIO SMR (Sistemas Microinformáticos y Redes)
-- ---------------------------------------------------------

-- 1º SMR (ID Curso: 5)
INSERT INTO asignaturas (nombre, id_curso, id_profesor) VALUES 
('Montaje y mantenimiento de equipo', 5, NULL),
('Sistemas operativos monopuesto', 5, NULL),
('Aplicaciones ofimáticas', 5, NULL),
('Redes locales', 5, NULL),
('Inglés Profesional (Grado Medio)', 5, NULL),
('Itinerario personal para la empleabilidad I', 5, NULL),
('Digitalización aplicada a los sectores productivos', 5, NULL),
('Sostenibilidad aplicada al sistema productivo', 5, NULL);

-- 2º SMR (ID Curso: 6)
INSERT INTO asignaturas (nombre, id_curso, id_profesor) VALUES 
('Sistemas operativos en red', 6, NULL),
('Seguridad informática', 6, NULL),
('Servicios en red', 6, NULL),
('Aplicaciones web', 6, NULL),
('Itinerario personal para la empleabilidad II', 6, NULL),
('Proyecto intermodular', 6, NULL),
('Módulo profesional optativo', 6, NULL);


-- ---------------------------------------------------------
-- 4. MASTER CIBERSEGURIDAD (Curso de Especialización)
-- ---------------------------------------------------------

-- Master Ciberseguridad (ID Curso: 7) - Curso único
INSERT INTO asignaturas (nombre, id_curso, id_profesor) VALUES 
('Incidentes de ciberseguridad', 7, NULL),
('Bastionado de redes y sistemas', 7, NULL),
('Puesta en producción segura', 7, NULL),
('Análisis forense informático', 7, NULL),
('Hacking ético', 7, NULL),
('Normativa de ciberseguridad', 7, NULL);


-- ---------------------------------------------------------
-- 5. DOBLE TITULACIÓN 3º DAM-DAW
-- ---------------------------------------------------------

-- 3º DAM-DAW (ID Curso: 8) - Contenido igual a 2º DAW
INSERT INTO asignaturas (nombre, id_curso, id_profesor) VALUES 
('Desarrollo web en entorno cliente', 8, NULL),
('Desarrollo web en entorno servidor', 8, NULL),
('Despliegue de aplicaciones web', 8, NULL),
('Diseño de interfaces WEB', 8, NULL),
('Itinerario personal para la empleabilidad II', 8, NULL),
('Proyecto intermodular de desarrollo de aplicaciones web', 8, NULL),
('Módulo profesional optativo', 8, NULL);


-- ---------------------------------------------------------
-- 6. GRADO SUPERIOR ASIR (Administración de Sistemas)
-- ---------------------------------------------------------

-- 1º ASIR (ID Curso: 9)
INSERT INTO asignaturas (nombre, id_curso, id_profesor) VALUES 
('Implantación de sistemas operativos', 9, NULL),
('Planificación y administración de redes', 9, NULL),
('Fundamentos de hardware', 9, NULL),
('Gestión de bases de datos', 9, NULL),
('Lenguajes de marcas y sistemas de gestión de información', 9, NULL),
('Inglés Profesional (Grado Superior)', 9, NULL),
('Itinerario personal para la empleabilidad I', 9, NULL),
('Digitalización aplicada a los sectores productivos', 9, NULL),
('Sostenibilidad aplicada al sistema productivo', 9, NULL);

-- 2º ASIR (ID Curso: 10)
INSERT INTO asignaturas (nombre, id_curso, id_profesor) VALUES 
('Administración de sistemas operativos', 10, NULL),
('Servicios de red e Internet', 10, NULL),
('Implantación de aplicaciones web', 10, NULL),
('Administración de sistemas gestores de bases de datos', 10, NULL),
('Seguridad y alta disponibilidad', 10, NULL),
('Itinerario personal para la empleabilidad II', 10, NULL),
('Proyecto intermodular de administración de sistemas', 10, NULL),
('Módulo profesional optativo', 10, NULL);

-- ============================================================
-- 1. CREACIÓN DE USUARIOS (1 Admin, 5 Profesores, 10 Alumnos)
-- ============================================================
-- Contraseña para todos: 1234 (hash: iVTj8rY9kraqp7TM48OIhA==)

INSERT IGNORE INTO usuarios (nombre_usuario, password_hash, nombre_completo, email, rol, estado, primer_inicio) VALUES 
-- DIRECTOR (Administrador)
('director', 'iVTj8rY9kraqp7TM48OIhA==', 'Director Enjoyfe', 'director@enjoyfe.com', 'admin', 'activo', 0),

-- PROFESORES (5)
('profe_dam', 'iVTj8rY9kraqp7TM48OIhA==', 'David Badia', 'david.badia@enjoyfe.com', 'profesor', 'activo', 0),
('profe_daw', 'iVTj8rY9kraqp7TM48OIhA==', 'Andrés Piñeros', 'andres.pineros@enjoyfe.com', 'profesor', 'activo', 0),
('profe_smr', 'iVTj8rY9kraqp7TM48OIhA==', 'Mario Ríos', 'mario.rios@enjoyfe.com', 'profesor', 'activo', 0),
('profe_asir', 'iVTj8rY9kraqp7TM48OIhA==', 'Tere Martínez', 'tere.martinez@enjoyfe.com', 'profesor', 'activo', 0),
('profe_master', 'iVTj8rY9kraqp7TM48OIhA==', 'Francisco Méndez', 'francisco.mendez@enjoyfe.com', 'profesor', 'activo', 0),

-- ALUMNOS (10)
('alu_emilio', 'iVTj8rY9kraqp7TM48OIhA==', 'Emilio Monge Osuna', 'emilio.monge@student.enjoyfe.com', 'alumno', 'activo', 0),
('alu_laura', 'iVTj8rY9kraqp7TM48OIhA==', 'Laura Rodríguez Fernández', 'laura.rodriguez@student.enjoyfe.com', 'alumno', 'activo', 0),
('alu_carlos', 'iVTj8rY9kraqp7TM48OIhA==', 'Carlos Gómez Pérez', 'carlos.gomez@student.enjoyfe.com', 'alumno', 'activo', 0),
('alu_lucia', 'iVTj8rY9kraqp7TM48OIhA==', 'Lucía Fernández López', 'lucia.fernandez@student.enjoyfe.com', 'alumno', 'activo', 0),
('alu_javier', 'iVTj8rY9kraqp7TM48OIhA==', 'Javier Ruiz Martín', 'javier.ruiz@student.enjoyfe.com', 'alumno', 'activo', 0),
('alu_elena', 'iVTj8rY9kraqp7TM48OIhA==', 'Elena Martín Sánchez', 'elena.martin@student.enjoyfe.com', 'alumno', 'activo', 0),
('alu_alejandro', 'iVTj8rY9kraqp7TM48OIhA==', 'Alejandro López Torres', 'alejandro.lopez@student.enjoyfe.com', 'alumno', 'activo', 0),
('alu_carmen', 'iVTj8rY9kraqp7TM48OIhA==', 'Carmen Sánchez Navarro', 'carmen.sanchez@student.enjoyfe.com', 'alumno', 'activo', 0),
('alu_diego', 'iVTj8rY9kraqp7TM48OIhA==', 'Diego Torres Gómez', 'diego.torres@student.enjoyfe.com', 'alumno', 'activo', 0),
('alu_ana', 'iVTj8rY9kraqp7TM48OIhA==', 'Ana Navarro Ruiz', 'ana.navarro@student.enjoyfe.com', 'alumno', 'activo', 0);

-- ============================================================
-- 2. ASIGNAR PROFESORES A TUS ASIGNATURAS EXISTENTES
-- ============================================================
-- Asignamos de forma manual a cada profesor a su curso correspondiente usando los IDs reales

-- 1. David Badia (profe_dam) a 1º DAM (id_curso = 1)
UPDATE asignaturas 
SET id_profesor = (SELECT id FROM usuarios WHERE nombre_usuario = 'profe_dam') 
WHERE id_curso = 1;

-- 2. Andrés Piñeros (profe_daw) a 1º DAW (id_curso = 3)
UPDATE asignaturas 
SET id_profesor = (SELECT id FROM usuarios WHERE nombre_usuario = 'profe_daw') 
WHERE id_curso = 3;

-- 3. Mario Ríos (profe_smr) a 1º SMR (id_curso = 5)
UPDATE asignaturas 
SET id_profesor = (SELECT id FROM usuarios WHERE nombre_usuario = 'profe_smr') 
WHERE id_curso = 5;

-- 4. Francisco Méndez (profe_master) al Máster de Ciberseguridad (id_curso = 7)
UPDATE asignaturas 
SET id_profesor = (SELECT id FROM usuarios WHERE nombre_usuario = 'profe_master') 
WHERE id_curso = 7;

-- 5. Tere Martínez (profe_asir) a 1º ASIR (id_curso = 9)
UPDATE asignaturas 
SET id_profesor = (SELECT id FROM usuarios WHERE nombre_usuario = 'profe_asir') 
WHERE id_curso = 9;


-- ============================================================
-- 3. MATRICULACIÓN (Vincular Alumnos con Asignaturas)
-- ============================================================

-- 1. Matricular a Laura, Emilio y Carlos en TODAS las asignaturas de 1º DAM (id_curso = 1)
INSERT IGNORE INTO matriculas (id_alumno, id_asignatura)
SELECT u.id, a.id
FROM usuarios u
CROSS JOIN asignaturas a
WHERE u.nombre_usuario IN ('alu_laura', 'alu_emilio', 'alu_carlos') 
AND a.id_curso = 1;

-- 2. Matricular a Alejandro y Carmen en TODAS las asignaturas de 1º DAW (id_curso = 3)
INSERT IGNORE INTO matriculas (id_alumno, id_asignatura)
SELECT u.id, a.id
FROM usuarios u
CROSS JOIN asignaturas a
WHERE u.nombre_usuario IN ('alu_alejandro', 'alu_carmen')
AND a.id_curso = 3;

-- 3. Matricular a Diego y Ana en TODAS las asignaturas de 1º SMR (id_curso = 5)
INSERT IGNORE INTO matriculas (id_alumno, id_asignatura)
SELECT u.id, a.id
FROM usuarios u
CROSS JOIN asignaturas a
WHERE u.nombre_usuario IN ('alu_diego', 'alu_ana')
AND a.id_curso = 5;

-- 4. Matricular a Lucía, Javier y Elena en TODAS las asignaturas de 1º ASIR (id_curso = 9)
INSERT IGNORE INTO matriculas (id_alumno, id_asignatura)
SELECT u.id, a.id
FROM usuarios u
CROSS JOIN asignaturas a
WHERE u.nombre_usuario IN ('alu_lucia', 'alu_javier', 'alu_elena')
AND a.id_curso = 9;


-- ============================================================
-- 4. DATOS DE EJEMPLO (Notas, Asistencia, Tutorías)
-- ============================================================

-- ---------------------------------------------------------
-- A) NOTAS (Calificaciones)
-- ---------------------------------------------------------

-- Nota para Laura en Programación (1º DAM)
INSERT IGNORE INTO calificaciones (id_matricula, nombre_actividad, nota, fecha_calificacion)
SELECT m.id, '1ª Evaluación', 9.5, '2026-01-15'
FROM matriculas m 
JOIN usuarios u ON m.id_alumno = u.id 
JOIN asignaturas a ON m.id_asignatura = a.id
WHERE u.nombre_usuario = 'alu_laura' AND a.nombre = 'Programación' AND a.id_curso = 1;

-- Nota para Emilio en Programación (1º DAM)
INSERT IGNORE INTO calificaciones (id_matricula, nombre_actividad, nota, fecha_calificacion)
SELECT m.id, '1ª Evaluación', 7.2, '2026-01-15'
FROM matriculas m 
JOIN usuarios u ON m.id_alumno = u.id 
JOIN asignaturas a ON m.id_asignatura = a.id
WHERE u.nombre_usuario = 'alu_emilio' AND a.nombre = 'Programación' AND a.id_curso = 1;

-- Nota para Laura en Bases de Datos (1º DAM)
INSERT IGNORE INTO calificaciones (id_matricula, nombre_actividad, nota, fecha_calificacion)
SELECT m.id, 'Proyecto Final', 8.8, '2026-02-10'
FROM matriculas m 
JOIN usuarios u ON m.id_alumno = u.id 
JOIN asignaturas a ON m.id_asignatura = a.id
WHERE u.nombre_usuario = 'alu_laura' AND a.nombre = 'Bases de Datos' AND a.id_curso = 1;

-- Nota para Alejandro en Entornos de desarrollo (1º DAW)
INSERT IGNORE INTO calificaciones (id_matricula, nombre_actividad, nota, fecha_calificacion)
SELECT m.id, '1ª Evaluación', 6.0, '2026-01-20'
FROM matriculas m 
JOIN usuarios u ON m.id_alumno = u.id 
JOIN asignaturas a ON m.id_asignatura = a.id
WHERE u.nombre_usuario = 'alu_alejandro' AND a.nombre = 'Entornos de desarrollo' AND a.id_curso = 3;


-- ---------------------------------------------------------
-- B) FALTAS DE ASISTENCIA
-- ---------------------------------------------------------

-- Falta de Emilio en Programación (Sin justificar)
INSERT IGNORE INTO asistencias (id_alumno, id_asignatura, fecha, tipo, justificada)
SELECT u.id, a.id, '2026-02-05', 'falta', 0
FROM usuarios u, asignaturas a
WHERE u.nombre_usuario = 'alu_emilio' AND a.nombre = 'Programación' AND a.id_curso = 1;

-- Retraso de Carlos en Sistemas informáticos (Justificado)
INSERT IGNORE INTO asistencias (id_alumno, id_asignatura, fecha, tipo, justificada)
SELECT u.id, a.id, '2026-02-12', 'retraso', 1
FROM usuarios u, asignaturas a
WHERE u.nombre_usuario = 'alu_carlos' AND a.nombre = 'Sistemas informáticos' AND a.id_curso = 1;

-- Falta de Lucía en Gestión de bases de datos (1º ASIR)
INSERT IGNORE INTO asistencias (id_alumno, id_asignatura, fecha, tipo, justificada)
SELECT u.id, a.id, '2026-02-18', 'falta', 0
FROM usuarios u, asignaturas a
WHERE u.nombre_usuario = 'alu_lucia' AND a.nombre = 'Gestión de bases de datos' AND a.id_curso = 9;


-- ---------------------------------------------------------
-- C) TUTORÍAS
-- ---------------------------------------------------------

-- Tutoría de Laura con David Badia (profe_dam)
INSERT IGNORE INTO tutorias (id_profesor, id_alumno, fecha, hora, asunto, estado)
SELECT 
    (SELECT id FROM usuarios WHERE nombre_usuario = 'profe_dam'),
    (SELECT id FROM usuarios WHERE nombre_usuario = 'alu_laura'),
    '2026-02-25', '16:00:00', 'Revisión de examen de Programación', 'Pendiente';

-- Tutoría de Alejandro con Andrés Piñeros (profe_daw)
INSERT IGNORE INTO tutorias (id_profesor, id_alumno, fecha, hora, asunto, estado)
SELECT 
    (SELECT id FROM usuarios WHERE nombre_usuario = 'profe_daw'),
    (SELECT id FROM usuarios WHERE nombre_usuario = 'alu_alejandro'),
    '2026-02-15', '17:30:00', 'Dudas sobre el proyecto web', 'Realizada';

-- Tutoría de Ana con Mario Ríos (profe_smr)
INSERT IGNORE INTO tutorias (id_profesor, id_alumno, fecha, hora, asunto, estado)
SELECT 
    (SELECT id FROM usuarios WHERE nombre_usuario = 'profe_smr'),
    (SELECT id FROM usuarios WHERE nombre_usuario = 'alu_ana'),
    '2026-03-02', '18:00:00', 'Orientación laboral', 'Pendiente';


-- ---------------------------------------------------------
-- D) ACTUALIZAR NOTAS FINALES (Opcional, para que se vean en listados)
-- ---------------------------------------------------------
-- Actualizamos la matrícula de Laura en Programación para que su nota final refleje el 9.5
UPDATE matriculas m
JOIN usuarios u ON m.id_alumno = u.id
JOIN asignaturas a ON m.id_asignatura = a.id
SET m.nota_final = 9.5
WHERE u.nombre_usuario = 'alu_laura' AND a.nombre = 'Programación' AND a.id_curso = 1;

-- Actualizamos la matrícula de Emilio en Programación con su 7.2
UPDATE matriculas m
JOIN usuarios u ON m.id_alumno = u.id
JOIN asignaturas a ON m.id_asignatura = a.id
SET m.nota_final = 7.2
WHERE u.nombre_usuario = 'alu_emilio' AND a.nombre = 'Programación' AND a.id_curso = 1;
    
-- ============================================================
-- 5. HORARIOS Y MENSAJES DE PRUEBA
-- ============================================================

-- ---------------------------------------------------------
-- HORARIO COMPLETO 1º DAM (Lunes a Viernes - ID Curso: 1)
-- ---------------------------------------------------------

-- LUNES
INSERT INTO horarios (id_curso, id_asignatura, dia_semana, hora_inicio, hora_fin) VALUES 
(1, (SELECT id FROM asignaturas WHERE nombre='Sistemas informáticos' AND id_curso=1), 1, '08:00', '08:55'),
(1, (SELECT id FROM asignaturas WHERE nombre='Programación' AND id_curso=1), 1, '08:55', '09:50'),
(1, (SELECT id FROM asignaturas WHERE nombre='Bases de Datos' AND id_curso=1), 1, '09:50', '10:45'),
-- Recreo: 10:45 a 11:15
(1, (SELECT id FROM asignaturas WHERE nombre='Entornos de desarrollo' AND id_curso=1), 1, '11:15', '12:10'),
(1, (SELECT id FROM asignaturas WHERE nombre='Lenguajes de marcas y sistemas de gestión de información' AND id_curso=1), 1, '12:10', '13:05'),
(1, (SELECT id FROM asignaturas WHERE nombre='Inglés Profesional (Grado Superior)' AND id_curso=1), 1, '13:05', '14:00');

-- MARTES
INSERT INTO horarios (id_curso, id_asignatura, dia_semana, hora_inicio, hora_fin) VALUES 
(1, (SELECT id FROM asignaturas WHERE nombre='Programación' AND id_curso=1), 2, '08:00', '08:55'),
(1, (SELECT id FROM asignaturas WHERE nombre='Programación' AND id_curso=1), 2, '08:55', '09:50'),
(1, (SELECT id FROM asignaturas WHERE nombre='Sistemas informáticos' AND id_curso=1), 2, '09:50', '10:45'),
(1, (SELECT id FROM asignaturas WHERE nombre='Bases de Datos' AND id_curso=1), 2, '11:15', '12:10'),
(1, (SELECT id FROM asignaturas WHERE nombre='Bases de Datos' AND id_curso=1), 2, '12:10', '13:05'),
(1, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad I' AND id_curso=1), 2, '13:05', '14:00');

-- MIÉRCOLES
INSERT INTO horarios (id_curso, id_asignatura, dia_semana, hora_inicio, hora_fin) VALUES 
(1, (SELECT id FROM asignaturas WHERE nombre='Lenguajes de marcas y sistemas de gestión de información' AND id_curso=1), 3, '08:00', '08:55'),
(1, (SELECT id FROM asignaturas WHERE nombre='Lenguajes de marcas y sistemas de gestión de información' AND id_curso=1), 3, '08:55', '09:50'),
(1, (SELECT id FROM asignaturas WHERE nombre='Programación' AND id_curso=1), 3, '09:50', '10:45'),
(1, (SELECT id FROM asignaturas WHERE nombre='Entornos de desarrollo' AND id_curso=1), 3, '11:15', '12:10'),
(1, (SELECT id FROM asignaturas WHERE nombre='Entornos de desarrollo' AND id_curso=1), 3, '12:10', '13:05'),
(1, (SELECT id FROM asignaturas WHERE nombre='Digitalización aplicada a los sectores productivos' AND id_curso=1), 3, '13:05', '14:00');

-- JUEVES
INSERT INTO horarios (id_curso, id_asignatura, dia_semana, hora_inicio, hora_fin) VALUES 
(1, (SELECT id FROM asignaturas WHERE nombre='Bases de Datos' AND id_curso=1), 4, '08:00', '08:55'),
(1, (SELECT id FROM asignaturas WHERE nombre='Bases de Datos' AND id_curso=1), 4, '08:55', '09:50'),
(1, (SELECT id FROM asignaturas WHERE nombre='Sistemas informáticos' AND id_curso=1), 4, '09:50', '10:45'),
(1, (SELECT id FROM asignaturas WHERE nombre='Programación' AND id_curso=1), 4, '11:15', '12:10'),
(1, (SELECT id FROM asignaturas WHERE nombre='Programación' AND id_curso=1), 4, '12:10', '13:05'),
(1, (SELECT id FROM asignaturas WHERE nombre='Sostenibilidad aplicada al sistema productivo' AND id_curso=1), 4, '13:05', '14:00');

-- VIERNES
INSERT INTO horarios (id_curso, id_asignatura, dia_semana, hora_inicio, hora_fin) VALUES 
(1, (SELECT id FROM asignaturas WHERE nombre='Sistemas informáticos' AND id_curso=1), 5, '08:00', '08:55'),
(1, (SELECT id FROM asignaturas WHERE nombre='Sistemas informáticos' AND id_curso=1), 5, '08:55', '09:50'),
(1, (SELECT id FROM asignaturas WHERE nombre='Entornos de desarrollo' AND id_curso=1), 5, '09:50', '10:45'),
(1, (SELECT id FROM asignaturas WHERE nombre='Lenguajes de marcas y sistemas de gestión de información' AND id_curso=1), 5, '11:15', '12:10'),
(1, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad I' AND id_curso=1), 5, '12:10', '13:05'),
(1, (SELECT id FROM asignaturas WHERE nombre='Inglés Profesional (Grado Superior)' AND id_curso=1), 5, '13:05', '14:00');


-- ---------------------------------------------------------
-- HORARIO COMPLETO 1º ASIR (Lunes a Viernes - ID Curso: 9)
-- ---------------------------------------------------------

-- LUNES
INSERT INTO horarios (id_curso, id_asignatura, dia_semana, hora_inicio, hora_fin) VALUES 
(9, (SELECT id FROM asignaturas WHERE nombre='Implantación de sistemas operativos' AND id_curso=9), 1, '08:00', '08:55'),
(9, (SELECT id FROM asignaturas WHERE nombre='Planificación y administración de redes' AND id_curso=9), 1, '08:55', '09:50'),
(9, (SELECT id FROM asignaturas WHERE nombre='Gestión de bases de datos' AND id_curso=9), 1, '09:50', '10:45'),
(9, (SELECT id FROM asignaturas WHERE nombre='Fundamentos de hardware' AND id_curso=9), 1, '11:15', '12:10'),
(9, (SELECT id FROM asignaturas WHERE nombre='Lenguajes de marcas y sistemas de gestión de información' AND id_curso=9), 1, '12:10', '13:05'),
(9, (SELECT id FROM asignaturas WHERE nombre='Inglés Profesional (Grado Superior)' AND id_curso=9), 1, '13:05', '14:00');

-- MARTES
INSERT INTO horarios (id_curso, id_asignatura, dia_semana, hora_inicio, hora_fin) VALUES 
(9, (SELECT id FROM asignaturas WHERE nombre='Planificación y administración de redes' AND id_curso=9), 2, '08:00', '08:55'),
(9, (SELECT id FROM asignaturas WHERE nombre='Planificación y administración de redes' AND id_curso=9), 2, '08:55', '09:50'),
(9, (SELECT id FROM asignaturas WHERE nombre='Implantación de sistemas operativos' AND id_curso=9), 2, '09:50', '10:45'),
(9, (SELECT id FROM asignaturas WHERE nombre='Gestión de bases de datos' AND id_curso=9), 2, '11:15', '12:10'),
(9, (SELECT id FROM asignaturas WHERE nombre='Gestión de bases de datos' AND id_curso=9), 2, '12:10', '13:05'),
(9, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad I' AND id_curso=9), 2, '13:05', '14:00');

-- MIÉRCOLES
INSERT INTO horarios (id_curso, id_asignatura, dia_semana, hora_inicio, hora_fin) VALUES 
(9, (SELECT id FROM asignaturas WHERE nombre='Lenguajes de marcas y sistemas de gestión de información' AND id_curso=9), 3, '08:00', '08:55'),
(9, (SELECT id FROM asignaturas WHERE nombre='Lenguajes de marcas y sistemas de gestión de información' AND id_curso=9), 3, '08:55', '09:50'),
(9, (SELECT id FROM asignaturas WHERE nombre='Planificación y administración de redes' AND id_curso=9), 3, '09:50', '10:45'),
(9, (SELECT id FROM asignaturas WHERE nombre='Fundamentos de hardware' AND id_curso=9), 3, '11:15', '12:10'),
(9, (SELECT id FROM asignaturas WHERE nombre='Fundamentos de hardware' AND id_curso=9), 3, '12:10', '13:05'),
(9, (SELECT id FROM asignaturas WHERE nombre='Digitalización aplicada a los sectores productivos' AND id_curso=9), 3, '13:05', '14:00');

-- JUEVES
INSERT INTO horarios (id_curso, id_asignatura, dia_semana, hora_inicio, hora_fin) VALUES 
(9, (SELECT id FROM asignaturas WHERE nombre='Gestión de bases de datos' AND id_curso=9), 4, '08:00', '08:55'),
(9, (SELECT id FROM asignaturas WHERE nombre='Gestión de bases de datos' AND id_curso=9), 4, '08:55', '09:50'),
(9, (SELECT id FROM asignaturas WHERE nombre='Implantación de sistemas operativos' AND id_curso=9), 4, '09:50', '10:45'),
(9, (SELECT id FROM asignaturas WHERE nombre='Planificación y administración de redes' AND id_curso=9), 4, '11:15', '12:10'),
(9, (SELECT id FROM asignaturas WHERE nombre='Planificación y administración de redes' AND id_curso=9), 4, '12:10', '13:05'),
(9, (SELECT id FROM asignaturas WHERE nombre='Sostenibilidad aplicada al sistema productivo' AND id_curso=9), 4, '13:05', '14:00');

-- VIERNES
INSERT INTO horarios (id_curso, id_asignatura, dia_semana, hora_inicio, hora_fin) VALUES 
(9, (SELECT id FROM asignaturas WHERE nombre='Implantación de sistemas operativos' AND id_curso=9), 5, '08:00', '08:55'),
(9, (SELECT id FROM asignaturas WHERE nombre='Implantación de sistemas operativos' AND id_curso=9), 5, '08:55', '09:50'),
(9, (SELECT id FROM asignaturas WHERE nombre='Fundamentos de hardware' AND id_curso=9), 5, '09:50', '10:45'),
(9, (SELECT id FROM asignaturas WHERE nombre='Lenguajes de marcas y sistemas de gestión de información' AND id_curso=9), 5, '11:15', '12:10'),
(9, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad I' AND id_curso=9), 5, '12:10', '13:05'),
(9, (SELECT id FROM asignaturas WHERE nombre='Inglés Profesional (Grado Superior)' AND id_curso=9), 5, '13:05', '14:00');

-- ---------------------------------------------------------
-- HORARIO COMPLETO 2º DAM (ID Curso: 2)
-- ---------------------------------------------------------
INSERT INTO horarios (id_curso, id_asignatura, dia_semana, hora_inicio, hora_fin) VALUES 
-- LUNES
(2, (SELECT id FROM asignaturas WHERE nombre='Acceso a datos' AND id_curso=2), 1, '08:00', '08:55'),
(2, (SELECT id FROM asignaturas WHERE nombre='Acceso a datos' AND id_curso=2), 1, '08:55', '09:50'),
(2, (SELECT id FROM asignaturas WHERE nombre='Desarrollo de interfaces' AND id_curso=2), 1, '09:50', '10:45'),
(2, (SELECT id FROM asignaturas WHERE nombre='Desarrollo de interfaces' AND id_curso=2), 1, '11:15', '12:10'),
(2, (SELECT id FROM asignaturas WHERE nombre='Programación multimedia y dispositivos móviles' AND id_curso=2), 1, '12:10', '13:05'),
(2, (SELECT id FROM asignaturas WHERE nombre='Programación multimedia y dispositivos móviles' AND id_curso=2), 1, '13:05', '14:00'),
-- MARTES
(2, (SELECT id FROM asignaturas WHERE nombre='Programación de servicios y procesos' AND id_curso=2), 2, '08:00', '08:55'),
(2, (SELECT id FROM asignaturas WHERE nombre='Programación de servicios y procesos' AND id_curso=2), 2, '08:55', '09:50'),
(2, (SELECT id FROM asignaturas WHERE nombre='Sistemas de gestión empresarial' AND id_curso=2), 2, '09:50', '10:45'),
(2, (SELECT id FROM asignaturas WHERE nombre='Sistemas de gestión empresarial' AND id_curso=2), 2, '11:15', '12:10'),
(2, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad II' AND id_curso=2), 2, '12:10', '13:05'),
(2, (SELECT id FROM asignaturas WHERE nombre='Módulo profesional optativo' AND id_curso=2), 2, '13:05', '14:00'),
-- MIÉRCOLES
(2, (SELECT id FROM asignaturas WHERE nombre='Acceso a datos' AND id_curso=2), 3, '08:00', '08:55'),
(2, (SELECT id FROM asignaturas WHERE nombre='Acceso a datos' AND id_curso=2), 3, '08:55', '09:50'),
(2, (SELECT id FROM asignaturas WHERE nombre='Desarrollo de interfaces' AND id_curso=2), 3, '09:50', '10:45'),
(2, (SELECT id FROM asignaturas WHERE nombre='Desarrollo de interfaces' AND id_curso=2), 3, '11:15', '12:10'),
(2, (SELECT id FROM asignaturas WHERE nombre='Programación multimedia y dispositivos móviles' AND id_curso=2), 3, '12:10', '13:05'),
(2, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular de desarrollo de aplicaciones multiplataforma' AND id_curso=2), 3, '13:05', '14:00'),
-- JUEVES
(2, (SELECT id FROM asignaturas WHERE nombre='Programación de servicios y procesos' AND id_curso=2), 4, '08:00', '08:55'),
(2, (SELECT id FROM asignaturas WHERE nombre='Programación de servicios y procesos' AND id_curso=2), 4, '08:55', '09:50'),
(2, (SELECT id FROM asignaturas WHERE nombre='Sistemas de gestión empresarial' AND id_curso=2), 4, '09:50', '10:45'),
(2, (SELECT id FROM asignaturas WHERE nombre='Sistemas de gestión empresarial' AND id_curso=2), 4, '11:15', '12:10'),
(2, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad II' AND id_curso=2), 4, '12:10', '13:05'),
(2, (SELECT id FROM asignaturas WHERE nombre='Módulo profesional optativo' AND id_curso=2), 4, '13:05', '14:00'),
-- VIERNES
(2, (SELECT id FROM asignaturas WHERE nombre='Programación multimedia y dispositivos móviles' AND id_curso=2), 5, '08:00', '08:55'),
(2, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular de desarrollo de aplicaciones multiplataforma' AND id_curso=2), 5, '08:55', '09:50'),
(2, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular de desarrollo de aplicaciones multiplataforma' AND id_curso=2), 5, '09:50', '10:45'),
(2, (SELECT id FROM asignaturas WHERE nombre='Acceso a datos' AND id_curso=2), 5, '11:15', '12:10'),
(2, (SELECT id FROM asignaturas WHERE nombre='Desarrollo de interfaces' AND id_curso=2), 5, '12:10', '13:05'),
(2, (SELECT id FROM asignaturas WHERE nombre='Módulo profesional optativo' AND id_curso=2), 5, '13:05', '14:00');

-- ---------------------------------------------------------
-- HORARIO COMPLETO 1º DAW (ID Curso: 3) - Copia exacta 1º DAM
-- ---------------------------------------------------------
INSERT INTO horarios (id_curso, id_asignatura, dia_semana, hora_inicio, hora_fin) VALUES 
-- LUNES
(3, (SELECT id FROM asignaturas WHERE nombre='Sistemas informáticos' AND id_curso=3), 1, '08:00', '08:55'),
(3, (SELECT id FROM asignaturas WHERE nombre='Programación' AND id_curso=3), 1, '08:55', '09:50'),
(3, (SELECT id FROM asignaturas WHERE nombre='Bases de Datos' AND id_curso=3), 1, '09:50', '10:45'),
(3, (SELECT id FROM asignaturas WHERE nombre='Entornos de desarrollo' AND id_curso=3), 1, '11:15', '12:10'),
(3, (SELECT id FROM asignaturas WHERE nombre='Lenguajes de marcas y sistemas de gestión de información' AND id_curso=3), 1, '12:10', '13:05'),
(3, (SELECT id FROM asignaturas WHERE nombre='Inglés Profesional (Grado Superior)' AND id_curso=3), 1, '13:05', '14:00'),
-- MARTES
(3, (SELECT id FROM asignaturas WHERE nombre='Programación' AND id_curso=3), 2, '08:00', '08:55'),
(3, (SELECT id FROM asignaturas WHERE nombre='Programación' AND id_curso=3), 2, '08:55', '09:50'),
(3, (SELECT id FROM asignaturas WHERE nombre='Sistemas informáticos' AND id_curso=3), 2, '09:50', '10:45'),
(3, (SELECT id FROM asignaturas WHERE nombre='Bases de Datos' AND id_curso=3), 2, '11:15', '12:10'),
(3, (SELECT id FROM asignaturas WHERE nombre='Bases de Datos' AND id_curso=3), 2, '12:10', '13:05'),
(3, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad I' AND id_curso=3), 2, '13:05', '14:00'),
-- MIÉRCOLES
(3, (SELECT id FROM asignaturas WHERE nombre='Lenguajes de marcas y sistemas de gestión de información' AND id_curso=3), 3, '08:00', '08:55'),
(3, (SELECT id FROM asignaturas WHERE nombre='Lenguajes de marcas y sistemas de gestión de información' AND id_curso=3), 3, '08:55', '09:50'),
(3, (SELECT id FROM asignaturas WHERE nombre='Programación' AND id_curso=3), 3, '09:50', '10:45'),
(3, (SELECT id FROM asignaturas WHERE nombre='Entornos de desarrollo' AND id_curso=3), 3, '11:15', '12:10'),
(3, (SELECT id FROM asignaturas WHERE nombre='Entornos de desarrollo' AND id_curso=3), 3, '12:10', '13:05'),
(3, (SELECT id FROM asignaturas WHERE nombre='Digitalización aplicada a los sectores productivos' AND id_curso=3), 3, '13:05', '14:00'),
-- JUEVES
(3, (SELECT id FROM asignaturas WHERE nombre='Bases de Datos' AND id_curso=3), 4, '08:00', '08:55'),
(3, (SELECT id FROM asignaturas WHERE nombre='Bases de Datos' AND id_curso=3), 4, '08:55', '09:50'),
(3, (SELECT id FROM asignaturas WHERE nombre='Sistemas informáticos' AND id_curso=3), 4, '09:50', '10:45'),
(3, (SELECT id FROM asignaturas WHERE nombre='Programación' AND id_curso=3), 4, '11:15', '12:10'),
(3, (SELECT id FROM asignaturas WHERE nombre='Programación' AND id_curso=3), 4, '12:10', '13:05'),
(3, (SELECT id FROM asignaturas WHERE nombre='Sostenibilidad aplicada al sistema productivo' AND id_curso=3), 4, '13:05', '14:00'),
-- VIERNES
(3, (SELECT id FROM asignaturas WHERE nombre='Sistemas informáticos' AND id_curso=3), 5, '08:00', '08:55'),
(3, (SELECT id FROM asignaturas WHERE nombre='Sistemas informáticos' AND id_curso=3), 5, '08:55', '09:50'),
(3, (SELECT id FROM asignaturas WHERE nombre='Entornos de desarrollo' AND id_curso=3), 5, '09:50', '10:45'),
(3, (SELECT id FROM asignaturas WHERE nombre='Lenguajes de marcas y sistemas de gestión de información' AND id_curso=3), 5, '11:15', '12:10'),
(3, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad I' AND id_curso=3), 5, '12:10', '13:05'),
(3, (SELECT id FROM asignaturas WHERE nombre='Inglés Profesional (Grado Superior)' AND id_curso=3), 5, '13:05', '14:00');

-- ---------------------------------------------------------
-- HORARIO COMPLETO 2º DAW (ID Curso: 4)
-- ---------------------------------------------------------
INSERT INTO horarios (id_curso, id_asignatura, dia_semana, hora_inicio, hora_fin) VALUES 
-- LUNES
(4, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno cliente' AND id_curso=4), 1, '08:00', '08:55'),
(4, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno cliente' AND id_curso=4), 1, '08:55', '09:50'),
(4, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno servidor' AND id_curso=4), 1, '09:50', '10:45'),
(4, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno servidor' AND id_curso=4), 1, '11:15', '12:10'),
(4, (SELECT id FROM asignaturas WHERE nombre='Despliegue de aplicaciones web' AND id_curso=4), 1, '12:10', '13:05'),
(4, (SELECT id FROM asignaturas WHERE nombre='Despliegue de aplicaciones web' AND id_curso=4), 1, '13:05', '14:00'),
-- MARTES
(4, (SELECT id FROM asignaturas WHERE nombre='Diseño de interfaces WEB' AND id_curso=4), 2, '08:00', '08:55'),
(4, (SELECT id FROM asignaturas WHERE nombre='Diseño de interfaces WEB' AND id_curso=4), 2, '08:55', '09:50'),
(4, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular de desarrollo de aplicaciones web' AND id_curso=4), 2, '09:50', '10:45'),
(4, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular de desarrollo de aplicaciones web' AND id_curso=4), 2, '11:15', '12:10'),
(4, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad II' AND id_curso=4), 2, '12:10', '13:05'),
(4, (SELECT id FROM asignaturas WHERE nombre='Módulo profesional optativo' AND id_curso=4), 2, '13:05', '14:00'),
-- MIÉRCOLES
(4, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno cliente' AND id_curso=4), 3, '08:00', '08:55'),
(4, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno cliente' AND id_curso=4), 3, '08:55', '09:50'),
(4, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno servidor' AND id_curso=4), 3, '09:50', '10:45'),
(4, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno servidor' AND id_curso=4), 3, '11:15', '12:10'),
(4, (SELECT id FROM asignaturas WHERE nombre='Despliegue de aplicaciones web' AND id_curso=4), 3, '12:10', '13:05'),
(4, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad II' AND id_curso=4), 3, '13:05', '14:00'),
-- JUEVES
(4, (SELECT id FROM asignaturas WHERE nombre='Diseño de interfaces WEB' AND id_curso=4), 4, '08:00', '08:55'),
(4, (SELECT id FROM asignaturas WHERE nombre='Diseño de interfaces WEB' AND id_curso=4), 4, '08:55', '09:50'),
(4, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular de desarrollo de aplicaciones web' AND id_curso=4), 4, '09:50', '10:45'),
(4, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular de desarrollo de aplicaciones web' AND id_curso=4), 4, '11:15', '12:10'),
(4, (SELECT id FROM asignaturas WHERE nombre='Despliegue de aplicaciones web' AND id_curso=4), 4, '12:10', '13:05'),
(4, (SELECT id FROM asignaturas WHERE nombre='Módulo profesional optativo' AND id_curso=4), 4, '13:05', '14:00'),
-- VIERNES
(4, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno cliente' AND id_curso=4), 5, '08:00', '08:55'),
(4, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno servidor' AND id_curso=4), 5, '08:55', '09:50'),
(4, (SELECT id FROM asignaturas WHERE nombre='Diseño de interfaces WEB' AND id_curso=4), 5, '09:50', '10:45'),
(4, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular de desarrollo de aplicaciones web' AND id_curso=4), 5, '11:15', '12:10'),
(4, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad II' AND id_curso=4), 5, '12:10', '13:05'),
(4, (SELECT id FROM asignaturas WHERE nombre='Módulo profesional optativo' AND id_curso=4), 5, '13:05', '14:00');

-- ---------------------------------------------------------
-- HORARIO COMPLETO 1º SMR (ID Curso: 5)
-- ---------------------------------------------------------
INSERT INTO horarios (id_curso, id_asignatura, dia_semana, hora_inicio, hora_fin) VALUES 
-- LUNES
(5, (SELECT id FROM asignaturas WHERE nombre='Montaje y mantenimiento de equipo' AND id_curso=5), 1, '08:00', '08:55'),
(5, (SELECT id FROM asignaturas WHERE nombre='Montaje y mantenimiento de equipo' AND id_curso=5), 1, '08:55', '09:50'),
(5, (SELECT id FROM asignaturas WHERE nombre='Sistemas operativos monopuesto' AND id_curso=5), 1, '09:50', '10:45'),
(5, (SELECT id FROM asignaturas WHERE nombre='Sistemas operativos monopuesto' AND id_curso=5), 1, '11:15', '12:10'),
(5, (SELECT id FROM asignaturas WHERE nombre='Aplicaciones ofimáticas' AND id_curso=5), 1, '12:10', '13:05'),
(5, (SELECT id FROM asignaturas WHERE nombre='Aplicaciones ofimáticas' AND id_curso=5), 1, '13:05', '14:00'),
-- MARTES
(5, (SELECT id FROM asignaturas WHERE nombre='Redes locales' AND id_curso=5), 2, '08:00', '08:55'),
(5, (SELECT id FROM asignaturas WHERE nombre='Redes locales' AND id_curso=5), 2, '08:55', '09:50'),
(5, (SELECT id FROM asignaturas WHERE nombre='Inglés Profesional (Grado Medio)' AND id_curso=5), 2, '09:50', '10:45'),
(5, (SELECT id FROM asignaturas WHERE nombre='Inglés Profesional (Grado Medio)' AND id_curso=5), 2, '11:15', '12:10'),
(5, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad I' AND id_curso=5), 2, '12:10', '13:05'),
(5, (SELECT id FROM asignaturas WHERE nombre='Digitalización aplicada a los sectores productivos' AND id_curso=5), 2, '13:05', '14:00'),
-- MIÉRCOLES
(5, (SELECT id FROM asignaturas WHERE nombre='Montaje y mantenimiento de equipo' AND id_curso=5), 3, '08:00', '08:55'),
(5, (SELECT id FROM asignaturas WHERE nombre='Montaje y mantenimiento de equipo' AND id_curso=5), 3, '08:55', '09:50'),
(5, (SELECT id FROM asignaturas WHERE nombre='Sistemas operativos monopuesto' AND id_curso=5), 3, '09:50', '10:45'),
(5, (SELECT id FROM asignaturas WHERE nombre='Sistemas operativos monopuesto' AND id_curso=5), 3, '11:15', '12:10'),
(5, (SELECT id FROM asignaturas WHERE nombre='Aplicaciones ofimáticas' AND id_curso=5), 3, '12:10', '13:05'),
(5, (SELECT id FROM asignaturas WHERE nombre='Sostenibilidad aplicada al sistema productivo' AND id_curso=5), 3, '13:05', '14:00'),
-- JUEVES
(5, (SELECT id FROM asignaturas WHERE nombre='Redes locales' AND id_curso=5), 4, '08:00', '08:55'),
(5, (SELECT id FROM asignaturas WHERE nombre='Redes locales' AND id_curso=5), 4, '08:55', '09:50'),
(5, (SELECT id FROM asignaturas WHERE nombre='Inglés Profesional (Grado Medio)' AND id_curso=5), 4, '09:50', '10:45'),
(5, (SELECT id FROM asignaturas WHERE nombre='Inglés Profesional (Grado Medio)' AND id_curso=5), 4, '11:15', '12:10'),
(5, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad I' AND id_curso=5), 4, '12:10', '13:05'),
(5, (SELECT id FROM asignaturas WHERE nombre='Digitalización aplicada a los sectores productivos' AND id_curso=5), 4, '13:05', '14:00'),
-- VIERNES
(5, (SELECT id FROM asignaturas WHERE nombre='Aplicaciones ofimáticas' AND id_curso=5), 5, '08:00', '08:55'),
(5, (SELECT id FROM asignaturas WHERE nombre='Sostenibilidad aplicada al sistema productivo' AND id_curso=5), 5, '08:55', '09:50'),
(5, (SELECT id FROM asignaturas WHERE nombre='Sostenibilidad aplicada al sistema productivo' AND id_curso=5), 5, '09:50', '10:45'),
(5, (SELECT id FROM asignaturas WHERE nombre='Montaje y mantenimiento de equipo' AND id_curso=5), 5, '11:15', '12:10'),
(5, (SELECT id FROM asignaturas WHERE nombre='Sistemas operativos monopuesto' AND id_curso=5), 5, '12:10', '13:05'),
(5, (SELECT id FROM asignaturas WHERE nombre='Redes locales' AND id_curso=5), 5, '13:05', '14:00');

-- ---------------------------------------------------------
-- HORARIO COMPLETO 2º SMR (ID Curso: 6)
-- ---------------------------------------------------------
INSERT INTO horarios (id_curso, id_asignatura, dia_semana, hora_inicio, hora_fin) VALUES 
-- LUNES
(6, (SELECT id FROM asignaturas WHERE nombre='Sistemas operativos en red' AND id_curso=6), 1, '08:00', '08:55'),
(6, (SELECT id FROM asignaturas WHERE nombre='Sistemas operativos en red' AND id_curso=6), 1, '08:55', '09:50'),
(6, (SELECT id FROM asignaturas WHERE nombre='Seguridad informática' AND id_curso=6), 1, '09:50', '10:45'),
(6, (SELECT id FROM asignaturas WHERE nombre='Seguridad informática' AND id_curso=6), 1, '11:15', '12:10'),
(6, (SELECT id FROM asignaturas WHERE nombre='Servicios en red' AND id_curso=6), 1, '12:10', '13:05'),
(6, (SELECT id FROM asignaturas WHERE nombre='Servicios en red' AND id_curso=6), 1, '13:05', '14:00'),
-- MARTES
(6, (SELECT id FROM asignaturas WHERE nombre='Aplicaciones web' AND id_curso=6), 2, '08:00', '08:55'),
(6, (SELECT id FROM asignaturas WHERE nombre='Aplicaciones web' AND id_curso=6), 2, '08:55', '09:50'),
(6, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular' AND id_curso=6), 2, '09:50', '10:45'),
(6, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular' AND id_curso=6), 2, '11:15', '12:10'),
(6, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad II' AND id_curso=6), 2, '12:10', '13:05'),
(6, (SELECT id FROM asignaturas WHERE nombre='Módulo profesional optativo' AND id_curso=6), 2, '13:05', '14:00'),
-- MIÉRCOLES
(6, (SELECT id FROM asignaturas WHERE nombre='Sistemas operativos en red' AND id_curso=6), 3, '08:00', '08:55'),
(6, (SELECT id FROM asignaturas WHERE nombre='Sistemas operativos en red' AND id_curso=6), 3, '08:55', '09:50'),
(6, (SELECT id FROM asignaturas WHERE nombre='Seguridad informática' AND id_curso=6), 3, '09:50', '10:45'),
(6, (SELECT id FROM asignaturas WHERE nombre='Seguridad informática' AND id_curso=6), 3, '11:15', '12:10'),
(6, (SELECT id FROM asignaturas WHERE nombre='Servicios en red' AND id_curso=6), 3, '12:10', '13:05'),
(6, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad II' AND id_curso=6), 3, '13:05', '14:00'),
-- JUEVES
(6, (SELECT id FROM asignaturas WHERE nombre='Aplicaciones web' AND id_curso=6), 4, '08:00', '08:55'),
(6, (SELECT id FROM asignaturas WHERE nombre='Aplicaciones web' AND id_curso=6), 4, '08:55', '09:50'),
(6, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular' AND id_curso=6), 4, '09:50', '10:45'),
(6, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular' AND id_curso=6), 4, '11:15', '12:10'),
(6, (SELECT id FROM asignaturas WHERE nombre='Servicios en red' AND id_curso=6), 4, '12:10', '13:05'),
(6, (SELECT id FROM asignaturas WHERE nombre='Módulo profesional optativo' AND id_curso=6), 4, '13:05', '14:00'),
-- VIERNES
(6, (SELECT id FROM asignaturas WHERE nombre='Sistemas operativos en red' AND id_curso=6), 5, '08:00', '08:55'),
(6, (SELECT id FROM asignaturas WHERE nombre='Seguridad informática' AND id_curso=6), 5, '08:55', '09:50'),
(6, (SELECT id FROM asignaturas WHERE nombre='Aplicaciones web' AND id_curso=6), 5, '09:50', '10:45'),
(6, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular' AND id_curso=6), 5, '11:15', '12:10'),
(6, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad II' AND id_curso=6), 5, '12:10', '13:05'),
(6, (SELECT id FROM asignaturas WHERE nombre='Módulo profesional optativo' AND id_curso=6), 5, '13:05', '14:00');

-- ---------------------------------------------------------
-- HORARIO COMPLETO MASTER CIBERSEGURIDAD (ID Curso: 7)
-- ---------------------------------------------------------
INSERT INTO horarios (id_curso, id_asignatura, dia_semana, hora_inicio, hora_fin) VALUES 
-- LUNES
(7, (SELECT id FROM asignaturas WHERE nombre='Incidentes de ciberseguridad' AND id_curso=7), 1, '08:00', '08:55'),
(7, (SELECT id FROM asignaturas WHERE nombre='Incidentes de ciberseguridad' AND id_curso=7), 1, '08:55', '09:50'),
(7, (SELECT id FROM asignaturas WHERE nombre='Bastionado de redes y sistemas' AND id_curso=7), 1, '09:50', '10:45'),
(7, (SELECT id FROM asignaturas WHERE nombre='Bastionado de redes y sistemas' AND id_curso=7), 1, '11:15', '12:10'),
(7, (SELECT id FROM asignaturas WHERE nombre='Puesta en producción segura' AND id_curso=7), 1, '12:10', '13:05'),
(7, (SELECT id FROM asignaturas WHERE nombre='Puesta en producción segura' AND id_curso=7), 1, '13:05', '14:00'),
-- MARTES
(7, (SELECT id FROM asignaturas WHERE nombre='Análisis forense informático' AND id_curso=7), 2, '08:00', '08:55'),
(7, (SELECT id FROM asignaturas WHERE nombre='Análisis forense informático' AND id_curso=7), 2, '08:55', '09:50'),
(7, (SELECT id FROM asignaturas WHERE nombre='Hacking ético' AND id_curso=7), 2, '09:50', '10:45'),
(7, (SELECT id FROM asignaturas WHERE nombre='Hacking ético' AND id_curso=7), 2, '11:15', '12:10'),
(7, (SELECT id FROM asignaturas WHERE nombre='Normativa de ciberseguridad' AND id_curso=7), 2, '12:10', '13:05'),
(7, (SELECT id FROM asignaturas WHERE nombre='Normativa de ciberseguridad' AND id_curso=7), 2, '13:05', '14:00'),
-- MIÉRCOLES
(7, (SELECT id FROM asignaturas WHERE nombre='Incidentes de ciberseguridad' AND id_curso=7), 3, '08:00', '08:55'),
(7, (SELECT id FROM asignaturas WHERE nombre='Incidentes de ciberseguridad' AND id_curso=7), 3, '08:55', '09:50'),
(7, (SELECT id FROM asignaturas WHERE nombre='Bastionado de redes y sistemas' AND id_curso=7), 3, '09:50', '10:45'),
(7, (SELECT id FROM asignaturas WHERE nombre='Bastionado de redes y sistemas' AND id_curso=7), 3, '11:15', '12:10'),
(7, (SELECT id FROM asignaturas WHERE nombre='Puesta en producción segura' AND id_curso=7), 3, '12:10', '13:05'),
(7, (SELECT id FROM asignaturas WHERE nombre='Puesta en producción segura' AND id_curso=7), 3, '13:05', '14:00'),
-- JUEVES
(7, (SELECT id FROM asignaturas WHERE nombre='Análisis forense informático' AND id_curso=7), 4, '08:00', '08:55'),
(7, (SELECT id FROM asignaturas WHERE nombre='Análisis forense informático' AND id_curso=7), 4, '08:55', '09:50'),
(7, (SELECT id FROM asignaturas WHERE nombre='Hacking ético' AND id_curso=7), 4, '09:50', '10:45'),
(7, (SELECT id FROM asignaturas WHERE nombre='Hacking ético' AND id_curso=7), 4, '11:15', '12:10'),
(7, (SELECT id FROM asignaturas WHERE nombre='Normativa de ciberseguridad' AND id_curso=7), 4, '12:10', '13:05'),
(7, (SELECT id FROM asignaturas WHERE nombre='Normativa de ciberseguridad' AND id_curso=7), 4, '13:05', '14:00'),
-- VIERNES
(7, (SELECT id FROM asignaturas WHERE nombre='Incidentes de ciberseguridad' AND id_curso=7), 5, '08:00', '08:55'),
(7, (SELECT id FROM asignaturas WHERE nombre='Bastionado de redes y sistemas' AND id_curso=7), 5, '08:55', '09:50'),
(7, (SELECT id FROM asignaturas WHERE nombre='Puesta en producción segura' AND id_curso=7), 5, '09:50', '10:45'),
(7, (SELECT id FROM asignaturas WHERE nombre='Análisis forense informático' AND id_curso=7), 5, '11:15', '12:10'),
(7, (SELECT id FROM asignaturas WHERE nombre='Hacking ético' AND id_curso=7), 5, '12:10', '13:05'),
(7, (SELECT id FROM asignaturas WHERE nombre='Normativa de ciberseguridad' AND id_curso=7), 5, '13:05', '14:00');

-- ---------------------------------------------------------
-- HORARIO COMPLETO 3º DAM-DAW (ID Curso: 8) - Equivalente a 2º DAW
-- ---------------------------------------------------------
INSERT INTO horarios (id_curso, id_asignatura, dia_semana, hora_inicio, hora_fin) VALUES 
-- LUNES
(8, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno cliente' AND id_curso=8), 1, '08:00', '08:55'),
(8, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno cliente' AND id_curso=8), 1, '08:55', '09:50'),
(8, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno servidor' AND id_curso=8), 1, '09:50', '10:45'),
(8, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno servidor' AND id_curso=8), 1, '11:15', '12:10'),
(8, (SELECT id FROM asignaturas WHERE nombre='Despliegue de aplicaciones web' AND id_curso=8), 1, '12:10', '13:05'),
(8, (SELECT id FROM asignaturas WHERE nombre='Despliegue de aplicaciones web' AND id_curso=8), 1, '13:05', '14:00'),
-- MARTES
(8, (SELECT id FROM asignaturas WHERE nombre='Diseño de interfaces WEB' AND id_curso=8), 2, '08:00', '08:55'),
(8, (SELECT id FROM asignaturas WHERE nombre='Diseño de interfaces WEB' AND id_curso=8), 2, '08:55', '09:50'),
(8, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular de desarrollo de aplicaciones web' AND id_curso=8), 2, '09:50', '10:45'),
(8, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular de desarrollo de aplicaciones web' AND id_curso=8), 2, '11:15', '12:10'),
(8, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad II' AND id_curso=8), 2, '12:10', '13:05'),
(8, (SELECT id FROM asignaturas WHERE nombre='Módulo profesional optativo' AND id_curso=8), 2, '13:05', '14:00'),
-- MIÉRCOLES
(8, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno cliente' AND id_curso=8), 3, '08:00', '08:55'),
(8, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno cliente' AND id_curso=8), 3, '08:55', '09:50'),
(8, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno servidor' AND id_curso=8), 3, '09:50', '10:45'),
(8, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno servidor' AND id_curso=8), 3, '11:15', '12:10'),
(8, (SELECT id FROM asignaturas WHERE nombre='Despliegue de aplicaciones web' AND id_curso=8), 3, '12:10', '13:05'),
(8, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad II' AND id_curso=8), 3, '13:05', '14:00'),
-- JUEVES
(8, (SELECT id FROM asignaturas WHERE nombre='Diseño de interfaces WEB' AND id_curso=8), 4, '08:00', '08:55'),
(8, (SELECT id FROM asignaturas WHERE nombre='Diseño de interfaces WEB' AND id_curso=8), 4, '08:55', '09:50'),
(8, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular de desarrollo de aplicaciones web' AND id_curso=8), 4, '09:50', '10:45'),
(8, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular de desarrollo de aplicaciones web' AND id_curso=8), 4, '11:15', '12:10'),
(8, (SELECT id FROM asignaturas WHERE nombre='Despliegue de aplicaciones web' AND id_curso=8), 4, '12:10', '13:05'),
(8, (SELECT id FROM asignaturas WHERE nombre='Módulo profesional optativo' AND id_curso=8), 4, '13:05', '14:00'),
-- VIERNES
(8, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno cliente' AND id_curso=8), 5, '08:00', '08:55'),
(8, (SELECT id FROM asignaturas WHERE nombre='Desarrollo web en entorno servidor' AND id_curso=8), 5, '08:55', '09:50'),
(8, (SELECT id FROM asignaturas WHERE nombre='Diseño de interfaces WEB' AND id_curso=8), 5, '09:50', '10:45'),
(8, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular de desarrollo de aplicaciones web' AND id_curso=8), 5, '11:15', '12:10'),
(8, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad II' AND id_curso=8), 5, '12:10', '13:05'),
(8, (SELECT id FROM asignaturas WHERE nombre='Módulo profesional optativo' AND id_curso=8), 5, '13:05', '14:00');

-- ---------------------------------------------------------
-- HORARIO COMPLETO 2º ASIR (ID Curso: 10)
-- ---------------------------------------------------------
INSERT INTO horarios (id_curso, id_asignatura, dia_semana, hora_inicio, hora_fin) VALUES 
-- LUNES
(10, (SELECT id FROM asignaturas WHERE nombre='Administración de sistemas operativos' AND id_curso=10), 1, '08:00', '08:55'),
(10, (SELECT id FROM asignaturas WHERE nombre='Administración de sistemas operativos' AND id_curso=10), 1, '08:55', '09:50'),
(10, (SELECT id FROM asignaturas WHERE nombre='Servicios de red e Internet' AND id_curso=10), 1, '09:50', '10:45'),
(10, (SELECT id FROM asignaturas WHERE nombre='Servicios de red e Internet' AND id_curso=10), 1, '11:15', '12:10'),
(10, (SELECT id FROM asignaturas WHERE nombre='Implantación de aplicaciones web' AND id_curso=10), 1, '12:10', '13:05'),
(10, (SELECT id FROM asignaturas WHERE nombre='Implantación de aplicaciones web' AND id_curso=10), 1, '13:05', '14:00'),
-- MARTES
(10, (SELECT id FROM asignaturas WHERE nombre='Administración de sistemas gestores de bases de datos' AND id_curso=10), 2, '08:00', '08:55'),
(10, (SELECT id FROM asignaturas WHERE nombre='Administración de sistemas gestores de bases de datos' AND id_curso=10), 2, '08:55', '09:50'),
(10, (SELECT id FROM asignaturas WHERE nombre='Seguridad y alta disponibilidad' AND id_curso=10), 2, '09:50', '10:45'),
(10, (SELECT id FROM asignaturas WHERE nombre='Seguridad y alta disponibilidad' AND id_curso=10), 2, '11:15', '12:10'),
(10, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad II' AND id_curso=10), 2, '12:10', '13:05'),
(10, (SELECT id FROM asignaturas WHERE nombre='Módulo profesional optativo' AND id_curso=10), 2, '13:05', '14:00'),
-- MIÉRCOLES
(10, (SELECT id FROM asignaturas WHERE nombre='Administración de sistemas operativos' AND id_curso=10), 3, '08:00', '08:55'),
(10, (SELECT id FROM asignaturas WHERE nombre='Administración de sistemas operativos' AND id_curso=10), 3, '08:55', '09:50'),
(10, (SELECT id FROM asignaturas WHERE nombre='Servicios de red e Internet' AND id_curso=10), 3, '09:50', '10:45'),
(10, (SELECT id FROM asignaturas WHERE nombre='Servicios de red e Internet' AND id_curso=10), 3, '11:15', '12:10'),
(10, (SELECT id FROM asignaturas WHERE nombre='Implantación de aplicaciones web' AND id_curso=10), 3, '12:10', '13:05'),
(10, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular de administración de sistemas' AND id_curso=10), 3, '13:05', '14:00'),
-- JUEVES
(10, (SELECT id FROM asignaturas WHERE nombre='Administración de sistemas gestores de bases de datos' AND id_curso=10), 4, '08:00', '08:55'),
(10, (SELECT id FROM asignaturas WHERE nombre='Administración de sistemas gestores de bases de datos' AND id_curso=10), 4, '08:55', '09:50'),
(10, (SELECT id FROM asignaturas WHERE nombre='Seguridad y alta disponibilidad' AND id_curso=10), 4, '09:50', '10:45'),
(10, (SELECT id FROM asignaturas WHERE nombre='Seguridad y alta disponibilidad' AND id_curso=10), 4, '11:15', '12:10'),
(10, (SELECT id FROM asignaturas WHERE nombre='Itinerario personal para la empleabilidad II' AND id_curso=10), 4, '12:10', '13:05'),
(10, (SELECT id FROM asignaturas WHERE nombre='Módulo profesional optativo' AND id_curso=10), 4, '13:05', '14:00'),
-- VIERNES
(10, (SELECT id FROM asignaturas WHERE nombre='Implantación de aplicaciones web' AND id_curso=10), 5, '08:00', '08:55'),
(10, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular de administración de sistemas' AND id_curso=10), 5, '08:55', '09:50'),
(10, (SELECT id FROM asignaturas WHERE nombre='Proyecto intermodular de administración de sistemas' AND id_curso=10), 5, '09:50', '10:45'),
(10, (SELECT id FROM asignaturas WHERE nombre='Administración de sistemas operativos' AND id_curso=10), 5, '11:15', '12:10'),
(10, (SELECT id FROM asignaturas WHERE nombre='Servicios de red e Internet' AND id_curso=10), 5, '12:10', '13:05'),
(10, (SELECT id FROM asignaturas WHERE nombre='Módulo profesional optativo' AND id_curso=10), 5, '13:05', '14:00');

-- ---------------------------------------------------------
-- MENSAJE DE PRUEBA
-- ---------------------------------------------------------
INSERT INTO mensajes (id_remitente, id_destinatario, asunto, mensaje) 
VALUES (
    (SELECT id FROM usuarios WHERE nombre_usuario = 'director' LIMIT 1), 
    (SELECT id FROM usuarios WHERE nombre_usuario = 'profe_dam' LIMIT 1), 
    'Bienvenido a Enjoyfe', 
    'Este es tu primer mensaje interno. Por favor, revisa tus tutorías pendientes.'
);