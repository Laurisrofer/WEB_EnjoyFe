<?php
session_start();
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token'])) {
    header("Location: index.php");
    exit();
}

// --- CONFIGURACIÓN DEL HEADER ---
$pagina_id = 'ajustes';
$titulo_seccion = 'Ajustes del sistema';
$estilos_adicionales = '<link rel="stylesheet" href="recursos/ajustes.css">';

include __DIR__ . '/../componentes/header.php';
?>

<div class="contenedor-ajustes">
    <div class="tarjeta-ajustes">
        <h2>Panel de configuración</h2>
        <p class="ajustes-desc">Personaliza tu experiencia visual, accesibilidad y notificaciones del portal educativo.</p>

        <!-- GRUPO 1: ASPECTO Y ACCESIBILIDAD -->
        <div class="grupo-ajustes">
            <div class="seccion-titulo">Aspecto y accesibilidad</div>
            
            <!-- Switch Modo Oscuro -->
            <div class="switch-container">
                <div>
                    <div class="switch-label">Tema oscuro</div>
                </div>
                <label class="switch">
                    <input type="checkbox" id="theme_toggle" onchange="toggleTheme(this)">
                    <span class="slider"></span>
                </label>
            </div>

            <!-- Selector de Tamaño de Letra -->
            <div class="form-group-spaced">
                <label>Tamaño de fuente</label>
                <select class="select-fontsize" id="fontsize_select" onchange="changeFontSize(this.value)">
                    <option value="small">Pequeño (14px)</option>
                    <option value="medium">Normal (16px)</option>
                    <option value="large">Grande (19px)</option>
                </select>
            </div>
        </div>

        <!-- GRUPO 2: NOTIFICACIONES -->
        <div class="grupo-ajustes">
            <div class="seccion-titulo">Preferencias de notificaciones</div>

            <!-- Switch Notas -->
            <?php if ($_SESSION['rol'] !== 'profesor' && $_SESSION['rol'] !== 'admin'): ?>
            <div class="switch-container">
                <div>
                    <div class="switch-label">Calificaciones de asignaturas</div>
                    <div class="switch-subtext">Recibir avisos flotantes cuando los profesores publiquen notas.</div>
                </div>
                <label class="switch">
                    <input type="checkbox" id="notif_notes_toggle" onchange="togglePreference('notif_notes', this.checked)">
                    <span class="slider"></span>
                </label>
            </div>
            <?php endif; ?>

            <!-- Switch Anuncios -->
            <div class="switch-container">
                <div>
                    <div class="switch-label">Anuncios del centro</div>
                    <div class="switch-subtext">Recibir avisos cuando se publiquen noticias y avisos en el tablón.</div>
                </div>
                <label class="switch">
                    <input type="checkbox" id="notif_anuncios_toggle" onchange="togglePreference('notif_anuncios', this.checked)">
                    <span class="slider"></span>
                </label>
            </div>

            <!-- Switch Mensajería -->
            <div class="switch-container">
                <div>
                    <div class="switch-label">Mensajería interna</div>
                    <div class="switch-subtext">Recibir avisos de nuevos mensajes directos de otros usuarios.</div>
                </div>
                <label class="switch">
                    <input type="checkbox" id="notif_msg_toggle" onchange="togglePreference('notif_msg', this.checked)">
                    <span class="slider"></span>
                </label>
            </div>

            <!-- Switch Asistencias -->
            <?php if ($_SESSION['rol'] !== 'admin'): ?>
            <div class="switch-container">
                <div>
                    <div class="switch-label">Faltas de asistencia</div>
                    <div class="switch-subtext">
                        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'profesor'): ?>
                            Recibir avisos de faltas o retrasos registrados de tus alumnos.
                        <?php else: ?>
                            Recibir avisos de faltas o retrasos registrados por tus profesores.
                        <?php endif; ?>
                    </div>
                </div>
                <label class="switch">
                    <input type="checkbox" id="notif_attendance_toggle" onchange="togglePreference('notif_attendance', this.checked)">
                    <span class="slider"></span>
                </label>
            </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
    // Inicializar controles basados en localStorage al cargar la página
    document.addEventListener("DOMContentLoaded", () => {
        // 1. Tema oscuro
        const currentTheme = localStorage.getItem(getLocalKey('theme')) || 'light';
        document.getElementById('theme_toggle').checked = (currentTheme === 'dark');

        // 2. Tamaño de fuente
        const currentFontSize = localStorage.getItem(getLocalKey('fontSize')) || 'medium';
        document.getElementById('fontsize_select').value = currentFontSize;

        // 3. Preferencias de Notificaciones (Por defecto true)
        <?php if ($_SESSION['rol'] !== 'profesor' && $_SESSION['rol'] !== 'admin'): ?>
        const notesEnabled = localStorage.getItem(getLocalKey('notif_notes')) !== 'false';
        if(document.getElementById('notif_notes_toggle')) document.getElementById('notif_notes_toggle').checked = notesEnabled;
        <?php endif; ?>
        const msgEnabled = localStorage.getItem(getLocalKey('notif_msg')) !== 'false';
        const attendanceEnabled = localStorage.getItem(getLocalKey('notif_attendance')) !== 'false';
        const anunciosEnabled = localStorage.getItem(getLocalKey('notif_anuncios')) !== 'false';
        if(document.getElementById('notif_msg_toggle')) document.getElementById('notif_msg_toggle').checked = msgEnabled;
        if(document.getElementById('notif_attendance_toggle')) document.getElementById('notif_attendance_toggle').checked = attendanceEnabled;
        if(document.getElementById('notif_anuncios_toggle')) document.getElementById('notif_anuncios_toggle').checked = anunciosEnabled;
    });

    // Cambiar Tema
    function toggleTheme(checkbox) {
        const theme = checkbox.checked ? 'dark' : 'light';
        localStorage.setItem(getLocalKey('theme'), theme);
        document.documentElement.setAttribute('data-theme', theme);
    }

    // Cambiar Tamaño de Letra
    function changeFontSize(value) {
        localStorage.setItem(getLocalKey('fontSize'), value);
        document.body.classList.remove('font-small', 'font-medium', 'font-large');
        document.body.classList.add('font-' + value);
    }

    // Guardar otras preferencias
    function togglePreference(key, value) {
        localStorage.setItem(getLocalKey(key), value);
        if (typeof checkNotifications === 'function') checkNotifications();
    }

    // Generar logs simulados de accesos
</script>

<?php include __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>
