<?php
session_start();
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token'])) {
    header("Location: index.php");
    exit();
}

$rol_usuario = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'alumno';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="recursos/favicon.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual de usuario - Enjoyfe</title>
    <link rel="stylesheet" href="recursos/estilos.css?v=1.2">
    <link rel="stylesheet" href="recursos/manual.css">
    <script>
        // Aplicar tema oscuro inmediatamente antes de pintar para evitar destello blanco
        const storedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', storedTheme);
        
        // Aplicar clase de accesibilidad para tamaño de fuente al cargar el DOM sin pisar el rol
        const storedFontSize = localStorage.getItem('fontSize') || 'medium';
        document.addEventListener('DOMContentLoaded', () => {
            document.body.classList.add('font-' + storedFontSize);
        });
    </script>
</head>
<body class="role-<?php echo $rol_usuario; ?>">

<div class="manual-layout">
    <!-- Navegación lateral del manual -->
    <div class="manual-nav">
        <h3>Contenido</h3>
        <a href="#introduccion" class="nav-link active">1. Introducción</a>
        <a href="#inicio" class="nav-link">2. Primeros pasos</a>
        <a href="#calendario" class="nav-link">3. Agenda y eventos</a>
        <a href="#asignaturas" class="nav-link">4. Materias y guías</a>
        <a href="#notificaciones" class="nav-link">5. Notificaciones</a>
        <a href="#ajustes" class="nav-link">6. Ajustes y accesibilidad</a>
    </div>

    <!-- Contenido del manual -->
    <div class="manual-content">
        <div class="manual-header">
            <div class="manual-title">
                <h1>Manual de usuario</h1>
                <p>Guía de uso oficial del portal educativo EnjoyFe</p>
            </div>
            <button class="btn-print" onclick="window.print()">
                Guardar en PDF
            </button>
        </div>

        <!-- SECCIÓN 1: INTRODUCCIÓN -->
        <div id="introduccion" class="section-block">
            <h2>1. Introducción</h2>
            <p>El portal académico de <strong>EnjoyFe</strong> es una plataforma web integrada diseñada para facilitar la comunicación, el seguimiento docente, la gestión del calendario escolar y la consulta de calificaciones para toda nuestra comunidad educativa.</p>
            <p>Este sistema adapta su aspecto, colores y herramientas en función del rol de la persona que acceda:</p>
            <ul>
                <li><strong>Alumnos (Verde):</strong> Acceso a asignaturas, descarga de materiales de Classroom/Drive, consulta de faltas, notas y agenda personal.</li>
                <li><strong>Profesores (Azul):</strong> Gestión docente, publicación de tramos de horario, y consulta de tutorías asignadas.</li>
                <li><strong>Administradores (Rojo):</strong> Control global del centro, altas de cursos y supervisión del historial de seguridad.</li>
            </ul>
        </div>

        <!-- SECCIÓN 2: PRIMEROS PASOS -->
        <div id="inicio" class="section-block">
            <h2>2. Primeros pasos</h2>
            <h3>Acceso a la plataforma</h3>
            <p>Para ingresar al portal, introduce tu nombre de usuario corporativo (ej: <code>alu_laura</code>) y tu contraseña en la pantalla principal de login. Si es la primera vez que accedes, tu tutor te proporcionará una clave temporal que deberás cambiar por motivos de seguridad.</p>
            <h3>El panel de control</h3>
            <p>Una vez dentro, el <strong>Panel de control</strong> te dará una bienvenida personalizada y mostrará:</p>
            <ol>
                <li><strong>Tablón de anuncios:</strong> Comunicaciones urgentes publicadas por la dirección o los coordinadores de curso.</li>
                <li><strong>Próximos eventos y entregas:</strong> Lista cronológica de exámenes y tareas pendientes de entrega.</li>
                <li><strong>Calendario mensual:</strong> Visualización interactiva para planificar tus semanas.</li>
            </ol>
            <div class="highlight-box">
                <p>💡 Tip de navegación: Puedes volver en cualquier momento al inicio haciendo clic en "Inicio" en el menú de navegación izquierdo.</p>
            </div>
        </div>

        <!-- SECCIÓN 3: AGENDA Y EVENTOS -->
        <div id="calendario" class="section-block">
            <h2>3. Agenda y eventos</h2>
            <h3>Creación de eventos personales</h3>
            <p>Como estudiante o docente, puedes añadir eventos privados a tu calendario mensual para organizar tus horas de estudio o reuniones:</p>
            <ul>
                <li>Haz clic directamente sobre la celda del día en el **Calendario mensual** derecho.</li>
                <li>Rellena el formulario emergente indicando el **Título**, la **Hora** del evento y el **Tipo** (Personal, Entrega o Examen).</li>
                <li>Pulsa en **Guardar**. El evento se pintará en el calendario y se añadirá a tu lista de tareas.</li>
            </ul>
            <h3>Eliminación y edición</h3>
            <p>Junto a cada tarea o examen listado en el centro del panel de control, dispondrás de botones rápidos para editar (✏️) los campos del evento o borrarlo (❌) de tu agenda personal.</p>
        </div>

        <!-- SECCIÓN 4: MATERIAS Y GUÍAS -->
        <div id="asignaturas" class="section-block">
            <h2>4. Materias y guías</h2>
            <h3>Mis asignaturas</h3>
            <p>Accediendo a la sección de **Asignaturas** en el menú izquierdo, verás las tarjetas correspondientes a todas las materias en las que estás inscrito. Cada tarjeta detalla el nombre de la materia y el profesor titular asignado de manera individual.</p>
            <h3>Detalle de la materia</h3>
            <p>Al pulsar sobre "Ver detalles" en cualquier asignatura, se abrirá un panel dividido en dos pestañas principales:</p>
            <ol>
                <li><strong>Guía docente:</strong> Presenta la descripción metodológica oficial de la materia, los bloques temáticos de informática estructurados por unidades y los criterios porcentuales de evaluación (exámenes vs. proyectos prácticos).</li>
                <li><strong>Recursos:</strong> Contiene enlaces directos configurados por el docente para acceder a las carpetas de apuntes en Google Drive, Classroom y vídeos de apoyo educativo.</li>
            </ol>
        </div>

        <!-- SECCIÓN 5: NOTIFICACIONES -->
        <div id="notificaciones" class="section-block">
            <h2>5. Notificaciones</h2>
            <h3>Avisos flotantes (Toasts)</h3>
            <p>La plataforma cuenta con un cargador automático en segundo plano que busca novedades en tiempo real cada 30 segundos. Si existen cambios registrados en las últimas 48 horas, se lanzarán toasts flotantes de aviso en la esquina superior derecha:</p>
            <ul>
                <li><strong>🎓 Calificaciones (Verde):</strong> Notifica la publicación de una nueva nota detallando la actividad y la calificación obtenida.</li>
                <li><strong>✉️ Mensajería (Azul):</strong> Alertas inmediatas al recibir un mensaje directo de un docente o compañero de clase.</li>
                <li><strong>⚠️ Asistencia (Naranja/Amarillo):</strong> Informa de inmediato si se ha registrado una falta o retraso injustificado en alguna materia.</li>
                <li><strong>📢 Anuncios (Rojo):</strong> Toasts de aviso cuando hay noticias publicadas en el tablón de anuncios del centro.</li>
            </ul>
            <div class="highlight-box">
                <p>🔔 La campana del header: Muestra el recuento total de notificaciones no vistas de la sesión. Al hacer clic te redirigirá al panel de control.</p>
            </div>
        </div>

        <!-- SECCIÓN 6: AJUSTES Y ACCESIBILIDAD -->
        <div id="ajustes" class="section-block">
            <h2>6. Ajustes y accesibilidad</h2>
            <h3>Personalización visual</h3>
            <p>Desde el panel de **Ajustes**, puedes personalizar tu experiencia interactiva sin ventanas emergentes molestas:</p>
            <ul>
                <li><strong>Tema oscuro:</strong> Alterna entre el modo noche y modo claro. El sistema de colores HSL se adaptará automáticamente para conservar el contraste en horarios y listados.</li>
                <li><strong>Tamaño de fuente:</strong> Configura el texto global del portal en tamaños Pequeño (14px), Normal (16px) o Grande (19px) para mayor comodidad de lectura.</li>
                <li><strong>Filtros de notificaciones:</strong> Activa o desactiva de manera independiente la recepción de avisos para notas, mensajes directos o faltas de asistencia según tus preferencias.</li>
            </ul>
        </div>
    </div>
</div>

<script>
    // Resaltado de enlaces según scroll
    const sections = document.querySelectorAll('.section-block');
    const navLinks = document.querySelectorAll('.manual-nav a');

    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (pageYOffset >= (sectionTop - 80)) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href').slice(1) === current) {
                link.classList.add('active');
            }
        });
    });
</script>

<?php include __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>
