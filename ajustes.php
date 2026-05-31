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

include 'componentes/header.php';
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
            <div class="switch-container">
                <div>
                    <div class="switch-label">Faltas de asistencia</div>
                    <div class="switch-subtext">Recibir avisos de faltas o retrasos registrados por tus profesores.</div>
                </div>
                <label class="switch">
                    <input type="checkbox" id="notif_attendance_toggle" onchange="togglePreference('notif_attendance', this.checked)">
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <!-- GRUPO 3: HISTORIAL DE ACCESOS -->
        <div class="grupo-ajustes">
            <div class="seccion-titulo">Historial de accesos recientes</div>
            <p class="log-help">Registro de seguridad de los últimos inicios de sesión en tu cuenta.</p>
            
            <div class="table-responsive"><table class="log-table">
                <thead>
                    <tr>
                        <th>Fecha y hora</th>
                        <th>Detalle / acción</th>
                        <th>Dirección IP</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody id="access_log_body">
                    <!-- Rellenado por JS dinámicamente -->
                </tbody>
            </table></div>
        </div>
    </div>
</div>

<script>
    // Inicializar controles basados en localStorage al cargar la página
    document.addEventListener("DOMContentLoaded", () => {
        // 1. Tema oscuro
        const currentTheme = localStorage.getItem('theme') || 'light';
        document.getElementById('theme_toggle').checked = (currentTheme === 'dark');

        // 2. Tamaño de fuente
        const currentFontSize = localStorage.getItem('fontSize') || 'medium';
        document.getElementById('fontsize_select').value = currentFontSize;

        // 3. Preferencias de Notificaciones (Por defecto true)
        const notesEnabled = localStorage.getItem('notif_notes') !== 'false';
        const msgEnabled = localStorage.getItem('notif_msg') !== 'false';
        const attendanceEnabled = localStorage.getItem('notif_attendance') !== 'false';
        document.getElementById('notif_notes_toggle').checked = notesEnabled;
        document.getElementById('notif_msg_toggle').checked = msgEnabled;
        document.getElementById('notif_attendance_toggle').checked = attendanceEnabled;

        // 4. Generar historial de accesos
        generarLogAccesos();
    });

    // Cambiar Tema
    function toggleTheme(checkbox) {
        const theme = checkbox.checked ? 'dark' : 'light';
        localStorage.setItem('theme', theme);
        document.documentElement.setAttribute('data-theme', theme);
    }

    // Cambiar Tamaño de Letra
    function changeFontSize(value) {
        localStorage.setItem('fontSize', value);
        document.body.classList.remove('font-small', 'font-medium', 'font-large');
        document.body.classList.add('font-' + value);
    }

    // Guardar otras preferencias
    function togglePreference(key, value) {
        localStorage.setItem(key, value);
    }

    // Generar logs simulados de accesos
    function generarLogAccesos() {
        const tbody = document.getElementById('access_log_body');
        const hoy = new Date();
        
        const formatearFecha = (date, minsOffset) => {
            const d = new Date(date.getTime() - minsOffset * 60 * 1000);
            return d.toLocaleDateString('es-ES') + ' ' + d.toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'});
        };

        const logs = [
            { fecha: formatearFecha(hoy, 0), detalle: "Inicio de sesión (Este dispositivo)", ip: "127.0.0.1", estado: "Activo" },
            { fecha: formatearFecha(hoy, 180), detalle: "Inicio de sesión (Chrome / Windows)", ip: "127.0.0.1", estado: "Cerrado" },
            { fecha: formatearFecha(hoy, 1440), detalle: "Inicio de sesión (Firefox / Windows)", ip: "192.168.1.45", estado: "Cerrado" }
        ];

        tbody.innerHTML = logs.map(l => `
            <tr>
                <td>${l.fecha}</td>
                <td>${l.detalle}</td>
                <td class="ip-address">${l.ip}</td>
                <td class="${l.estado === 'Activo' ? 'log-status-active' : 'log-status-inactive'}">${l.estado}</td>
            </tr>
        `).join('');
    }
</script>

</body>
</html>

