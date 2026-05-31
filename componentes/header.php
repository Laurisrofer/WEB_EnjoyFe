<?php
// Obtener el rol de la sesión de PHP (por defecto 'alumno')
$rol_usuario = isset($_SESSION['rol']) ? $_SESSION['rol'] : 'alumno';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="icon" type="image/png" href="recursos/favicon.png">
    <meta charset="UTF-8">
    <title>Enjoyfe</title>
    <link rel="stylesheet" href="recursos/estilos.css?v=1.2">
    
    <script>
        // Aplicar tema oscuro inmediatamente antes de pintar para evitar destello blanco
        const storedTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', storedTheme);
        
        // Aplicar clase de accesibilidad para tamaño de fuente al cargar el DOM sin pisar el rol
        const storedFontSize = localStorage.getItem('fontSize') || 'medium';
        document.addEventListener('DOMContentLoaded', () => {
            document.body.classList.add('font-' + storedFontSize);
        });
    
    function confirmarLogoutGlobal(e) {
        e.preventDefault();
        mostrarConfirmacionGlobal('Cerrar sesin', 'Ests seguro de que deseas cerrar sesin?', function() {
            window.location.href = 'acciones/logout.php';
        });
    }

    </script>
    
    <?php 
    // Por si la página que lo llama necesita añadir estilos extra en su propio <style>
    if (isset($estilos_adicionales)) {
        echo $estilos_adicionales;
    }
    ?>
</head>
<body class="role-<?php echo $rol_usuario; ?>">

    <!-- Overlay para cuando la sidebar está abierta en móviles -->
    <div class="sidebar-overlay" id="sidebar_overlay" onclick="toggleSidebar()"></div>

    <div class="sidebar" id="sidebar" style="display: flex; flex-direction: column; position: relative;">
        <a href="dashboard.php" style="display:block; text-align:center; padding:15px 10px 5px 10px;">
            <img src="recursos/logo_enjoyfe.png" alt="Enjoyfe" style="width:90%; max-width:200px; height:auto; background:white; border-radius:12px; padding:4px; object-fit:contain; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
        </a>
        <a href="dashboard.php" class="<?php echo ($pagina_id == 'inicio') ? 'activo' : ''; ?>" style="margin-top: 10px;">Inicio</a>
        <a href="horario.php" class="<?php echo ($pagina_id == 'horario') ? 'activo' : ''; ?>">Horario</a>
        <a href="asignaturas.php" class="<?php echo ($pagina_id == 'asignaturas') ? 'activo' : ''; ?>">Asignaturas</a>
        <a href="notas.php" class="<?php echo ($pagina_id == 'notas') ? 'activo' : ''; ?>">Notas</a>
        <a href="asistencias.php" class="<?php echo ($pagina_id == 'asistencias') ? 'activo' : ''; ?>">Asistencias</a>
        <a href="estadisticas.php" class="<?php echo ($pagina_id == 'estadisticas') ? 'activo' : ''; ?>">Estadísticas</a>
        <a href="mensajeria.php" class="<?php echo ($pagina_id == 'mensajeria') ? 'activo' : ''; ?>">Mensajería</a>
        
        <style>
            .social-icon { color: var(--sidebar-text); opacity: 0.6; transition: opacity 0.2s, transform 0.2s, color 0.2s; display: flex; align-items: center; }
            .social-icon:hover { opacity: 1; transform: scale(1.15); color: #ffffff !important; }
        </style>
        <div style="margin-top: auto; display: flex; justify-content: center; gap: 20px; border-top: 1px solid var(--sidebar-border); padding-top: 20px;">
            <a href="https://x.com" target="_blank" class="social-icon" title="X (Twitter)">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </a>
            <a href="https://instagram.com" target="_blank" class="social-icon" title="Instagram">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm3.98-10.181a1.44 1.44 0 11-2.88 0 1.44 1.44 0 012.88 0z"/></svg>
            </a>
            <a href="https://linkedin.com" target="_blank" class="social-icon" title="LinkedIn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
            </a>
        </div>
    </div>
    
    <div class="main">
        <div class="header">
            <div class="header-title" style="display:flex; align-items:center;">
                <style>@media (min-width: 769px) { .hamburger-btn { display: none !important; } }</style><button class="hamburger-btn" onclick="toggleSidebar()">☰</button>
                <div>
                <?php 
                if ($titulo_seccion === 'Panel de control' || $titulo_seccion === 'Panel de control') {
                    echo 'Panel de control: <span class="nombre-usuario nombre-usuario-header">' . htmlspecialchars($_SESSION['nombre_usuario']) . '</span>';
                } else {
                    echo htmlspecialchars($titulo_seccion);
                }
                ?>
                </div>
            </div>
            
            <div class="header-actions">
                <!-- Campana de notificaciones con desplegable -->
                <div class="notif-bell-container" onclick="toggleNotifDropdown(event)" title="Ver notificaciones">
                    <span class="notif-bell">🔔</span>
                    <span class="notif-badge" id="notif_badge_count">0</span>
                    
                    <!-- Panel desplegable -->
                    <div class="notif-dropdown" id="notif_dropdown" onclick="event.stopPropagation()">
                        <div class="notif-dropdown-header">

                            <span>Notificaciones</span>
                            <button onclick="marcarTodasLeidas(event)">Marcar todo como leído</button>
                        </div>
                        <div class="notif-dropdown-list" id="notif_dropdown_list">
                            <div class="notif-empty">No tienes notificaciones nuevas</div>
                        </div>
                    </div>
                </div>
                
                <div class="dropdown">
                    <button class="dropbtn">⚙️ Mi cuenta</button>
                    <div class="dropdown-content">
                        <a href="perfil.php" class="<?php echo ($pagina_id == 'perfil') ? 'activo' : ''; ?>">👤 Mi perfil</a>
                        <a href="ajustes.php" class="<?php echo ($pagina_id == 'ajustes') ? 'activo' : ''; ?>">⚙️ Ajustes</a>
                        <a href="ayuda.php" class="<?php echo ($pagina_id == 'ayuda') ? 'activo' : ''; ?>">❓ Ayuda / soporte</a>
                        <hr class="dropdown-divider">
                        <a href="acciones/logout.php" class="logout-btn">Cerrar sesión</a>
                    </div>
                </div>
            </div>
        </div>

    <!-- Contenedor para Notificaciones Flotantes (Toasts) -->
    <div class="toast-container" id="toast_container"></div>

    <!-- Modal Premium para Detalles de Notificaciones -->
    <div id="modal_notif_detalle" class="modal-notif" onclick="if(event.target === this) cerrarModalNotif()">
        <div class="modal-notif-content">
            <div class="modal-notif-header">
                <span class="modal-notif-badge" id="modal_notif_badge">📢 Anuncio del centro</span>
                <button class="modal-notif-close" onclick="cerrarModalNotif()">&times;</button>
            </div>
            <h2 id="modal_notif_titulo">Título</h2>
            <div class="modal-notif-meta" id="modal_notif_meta">Fecha</div>
            <div class="modal-notif-body" id="modal_notif_body">Descripción</div>
            <div class="modal-notif-actions">
                <button class="btn-modal-close" onclick="cerrarModalNotif()">Entendido</button>
            </div>
        </div>
    </div>

    <!-- Modal genérico de confirmación -->
    <div id="modal_confirmacion_global" class="modal-notif" onclick="if(event.target === this) cerrarConfirmacion()">
        <div class="modal-notif-content" style="max-width:400px; text-align:center;">
            <div style="font-size:3em; margin-bottom:10px;">⚠️</div>
            <h2 id="confirmacion_titulo">¿Estás seguro?</h2>
            <div style="color:var(--text-muted); margin:15px 0;" id="confirmacion_mensaje">Esta acción no se puede deshacer.</div>
            <div style="display:flex; gap:15px; justify-content:center; margin-top:20px;">
                <button class="btn-modal-close" style="background:#ccc; color:#333; margin:0;" onclick="cerrarConfirmacion()">Cancelar</button>
                <button id="btn_confirmacion_aceptar" style="background:var(--danger-color); color:white; border:none; padding:10px 20px; border-radius:4px; font-weight:bold; cursor:pointer; margin:0;" onclick="ejecutarConfirmacion()">Sí, eliminar</button>
            </div>
        </div>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar_overlay');
            if(sidebar) sidebar.classList.toggle('open');
            if(overlay) overlay.classList.toggle('show');
        }

        let notificationsCache = {
            mensajes: [],
            calificaciones: [],
            asistencias: [],
            anuncios: []
        };

        function escapeHtml(unsafe) {
            if (!unsafe) return "";
            return unsafe
                 .toString()
                 .replace(/&/g, "&amp;")
                 .replace(/</g, "&lt;")
                 .replace(/>/g, "&gt;")
                 .replace(/"/g, "&quot;")
                 .replace(/'/g, "&#039;");
        }

        function showToast(message, type = 'info', onClickAction = null) {
            // Desactivado por petición del usuario
            return;
        }

        function toggleNotifDropdown(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('notif_dropdown');
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        }

        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', () => {
            const dropdown = document.getElementById('notif_dropdown');
            if (dropdown) {
                dropdown.classList.remove('show');
            }
        });

        function marcarComoLeida(id, event) {
            if (event) event.stopPropagation();
            let readNotifs = JSON.parse(localStorage.getItem('read_notifications') || '[]');
            if (!readNotifs.includes(id)) {
                readNotifs.push(id);
                localStorage.setItem('read_notifications', JSON.stringify(readNotifs));
                checkNotifications();
            }
        }

        function marcarTodasLeidas(event) {
            if (event) event.stopPropagation();
            let readNotifs = JSON.parse(localStorage.getItem('read_notifications') || '[]');
            
            if (notificationsCache) {
                if (notificationsCache.mensajes) {
                    notificationsCache.mensajes.forEach(msg => {
                        const notifId = `msg_${msg.id}`;
                        if (!readNotifs.includes(notifId)) readNotifs.push(notifId);
                    });
                }
                if (notificationsCache.calificaciones) {
                    notificationsCache.calificaciones.forEach(cal => {
                        const notifId = `grade_${cal.id}`;
                        if (!readNotifs.includes(notifId)) readNotifs.push(notifId);
                    });
                }
                if (notificationsCache.asistencias) {
                    notificationsCache.asistencias.forEach(a => {
                        const notifId = `att_${a.id}`;
                        if (!readNotifs.includes(notifId)) readNotifs.push(notifId);
                    });
                }
                if (notificationsCache.anuncios) {
                    notificationsCache.anuncios.forEach(an => {
                        const notifId = `ann_${an.id}`;
                        if (!readNotifs.includes(notifId)) readNotifs.push(notifId);
                    });
                }
            }
            
            localStorage.setItem('read_notifications', JSON.stringify(readNotifs));
            checkNotifications();
        }

        function mostrarModalNotif(titulo, descripcion, fecha = '', badgeText = '📢 Anuncio del centro') {
            const modal = document.getElementById('modal_notif_detalle');
            const tituloEl = document.getElementById('modal_notif_titulo');
            const bodyEl = document.getElementById('modal_notif_body');
            const fechaEl = document.getElementById('modal_notif_meta');
            const badgeEl = document.getElementById('modal_notif_badge');
            
            if (modal && tituloEl && bodyEl && fechaEl && badgeEl) {
                tituloEl.innerText = titulo;
                bodyEl.innerText = descripcion;
                fechaEl.innerText = fecha ? `Publicado el ${fecha}` : '';
                badgeEl.innerText = badgeText;
                modal.classList.add('show');
            }
        }

        function cerrarModalNotif() {
            const modal = document.getElementById('modal_notif_detalle');
            if (modal) {
                modal.classList.remove('show');
            }
        }

        let confirmacionCallback = null;

        function mostrarConfirmacionGlobal(titulo, mensaje, onConfirm) {
            const modal = document.getElementById('modal_confirmacion_global');
            document.getElementById('confirmacion_titulo').innerText = titulo;
            document.getElementById('confirmacion_mensaje').innerText = mensaje;
            confirmacionCallback = onConfirm;
            if (modal) modal.classList.add('show');
        }

        function cerrarConfirmacion() {
            const modal = document.getElementById('modal_confirmacion_global');
            if (modal) modal.classList.remove('show');
            confirmacionCallback = null;
        }

        function ejecutarConfirmacion() {
            if (confirmacionCallback) confirmacionCallback();
            cerrarConfirmacion();
        }

        function verDetalleNotif(notifId) {
            marcarComoLeida(notifId);
            
            const parts = notifId.split('_');
            const type = parts[0];
            const id = parseInt(parts[1]);
            
            if (type === 'ann') {
                const an = notificationsCache.anuncios.find(item => item.id === id);
                if (an) {
                    mostrarModalNotif(an.titulo, an.descripcion, an.fecha, '📢 Anuncio del centro');
                }
            } else if (type === 'msg') {
                const msg = notificationsCache.mensajes.find(item => item.id === id);
                if (msg) {
                    mostrarModalNotif(`Mensaje de ${msg.de}`, `Asunto: ${msg.asunto}`, msg.fecha, '✉️ Mensaje directo');
                }
            } else if (type === 'grade') {
                const cal = notificationsCache.calificaciones.find(item => item.id === id);
                if (cal) {
                    mostrarModalNotif(`Nueva Calificación en ${cal.asignatura}`, `Actividad: ${cal.actividad}\nNota: ${cal.nota}`, cal.fecha, '🎓 Calificación');
                }
            } else if (type === 'att') {
                const a = notificationsCache.asistencias.find(item => item.id === id);
                if (a) {
                    const tipoCap = a.tipo.charAt(0).toUpperCase() + a.tipo.slice(1);
                    mostrarModalNotif(`Control de Asistencia: ${tipoCap}`, `Asignatura: ${a.asignatura}\nJustificada: ${a.justificada ? 'Sí' : 'No'}`, a.fecha, '⚠️ Asistencia');
                }
            }
        }

        function checkNotifications() {
            // Cargar preferencias de localStorage (por defecto true si no existen)
            const notifNotesEnabled = localStorage.getItem('notif_notes') !== 'false';
            const notifMsgEnabled = localStorage.getItem('notif_msg') !== 'false';
            const notifAttendanceEnabled = localStorage.getItem('notif_attendance') !== 'false';

            fetch('acciones/notificaciones.php')
            .then(response => {
                if (response.status === 200) {
                    return response.json();
                }
                throw new Error('No autorizado');
            })
            .then(data => {
                notificationsCache = data;
                let totalAlerts = 0;
                let listHtml = '';
                
                let readNotifs = JSON.parse(localStorage.getItem('read_notifications') || '[]');
                let notifiedMsgs = JSON.parse(localStorage.getItem('notified_msgs') || '[]');
                let notifiedGrades = JSON.parse(localStorage.getItem('notified_grades') || '[]');
                let notifiedAttendance = JSON.parse(localStorage.getItem('notified_attendance') || '[]');
                let notifiedAnuncios = JSON.parse(localStorage.getItem('notified_anuncios') || '[]');

                const isUnread = (id) => !readNotifs.includes(id);

                // 1. Mensajes nuevos
                if (data.mensajes && Array.isArray(data.mensajes)) {
                    data.mensajes.forEach(msg => {
                        const notifId = `msg_${msg.id}`;
                        const unreadClass = isUnread(notifId) ? 'unread' : '';
                        if (isUnread(notifId)) totalAlerts++;
                        
                        if (notifMsgEnabled && !notifiedMsgs.includes(msg.id)) {
                            // showToast(`✉️ Mensaje de ${escapeHtml(msg.de)}: "${escapeHtml(msg.asunto)}"`, 'info', () => {
                            //     verDetalleNotif(notifId);
                            // });
                            notifiedMsgs.push(msg.id);
                        }
                        
                        listHtml += `
                            <div class="notif-item ${unreadClass}" onclick="verDetalleNotif('${notifId}')">
                                <div class="notif-item-content">
                                    <div class="notif-item-title">✉️ Mensaje de ${escapeHtml(msg.de)}</div>
                                    <div>"${escapeHtml(msg.asunto)}"</div>
                                    <div class="notif-item-time">${escapeHtml(msg.fecha)}</div>
                                </div>
                                ${isUnread(notifId) ? `<button class="notif-item-action" onclick="marcarComoLeida('${notifId}', event)" title="Marcar como leído">✔️</button>` : '<span style="color:var(--success-color); font-size: 0.85em; font-weight: bold;">Leído</span>'}
                            </div>
                        `;
                    });
                    localStorage.setItem('notified_msgs', JSON.stringify(notifiedMsgs));
                }

                // 2. Calificaciones nuevas
                if (data.calificaciones && Array.isArray(data.calificaciones)) {
                    data.calificaciones.forEach(cal => {
                        const notifId = `grade_${cal.id}`;
                        const unreadClass = isUnread(notifId) ? 'unread' : '';
                        if (isUnread(notifId)) totalAlerts++;
                        
                        if (notifNotesEnabled && !notifiedGrades.includes(cal.id)) {
                            // showToast(`🎓 Nota en ${escapeHtml(cal.asignatura)}: ${escapeHtml(cal.actividad)} (${cal.nota})`, 'success', () => {
                            //     verDetalleNotif(notifId);
                            // });
                            notifiedGrades.push(cal.id);
                        }
                        
                        listHtml += `
                            <div class="notif-item ${unreadClass}" onclick="verDetalleNotif('${notifId}')">
                                <div class="notif-item-content">
                                    <div class="notif-item-title">🎓 Nota en ${escapeHtml(cal.asignatura)}</div>
                                    <div>${escapeHtml(cal.actividad)} (Nota: ${cal.nota})</div>
                                    <div class="notif-item-time">${escapeHtml(cal.fecha)}</div>
                                </div>
                                ${isUnread(notifId) ? `<button class="notif-item-action" onclick="marcarComoLeida('${notifId}', event)" title="Marcar como leído">✔️</button>` : '<span style="color:var(--success-color); font-size: 0.85em; font-weight: bold;">Leído</span>'}
                            </div>
                        `;
                    });
                    localStorage.setItem('notified_grades', JSON.stringify(notifiedGrades));
                }

                // 3. Asistencias nuevas (faltas/retrasos)
                if (data.asistencias && Array.isArray(data.asistencias)) {
                    data.asistencias.forEach(a => {
                        const notifId = `att_${a.id}`;
                        const unreadClass = isUnread(notifId) ? 'unread' : '';
                        if (isUnread(notifId)) totalAlerts++;
                        
                        const tipoCapitalized = a.tipo.charAt(0).toUpperCase() + a.tipo.slice(1);
                        if (notifAttendanceEnabled && !notifiedAttendance.includes(a.id)) {
                            // showToast(`⚠️ ${escapeHtml(tipoCapitalized)} registrada en ${escapeHtml(a.asignatura)} el ${escapeHtml(a.fecha)}`, 'warning', () => {
                            //     verDetalleNotif(notifId);
                            // });
                            notifiedAttendance.push(a.id);
                        }
                        
                        listHtml += `
                            <div class="notif-item ${unreadClass}" onclick="verDetalleNotif('${notifId}')">
                                <div class="notif-item-content">
                                    <div class="notif-item-title">⚠️ ${escapeHtml(tipoCapitalized)} registrada</div>
                                    <div>En ${escapeHtml(a.asignatura)} el ${escapeHtml(a.fecha)}</div>
                                </div>
                                ${isUnread(notifId) ? `<button class="notif-item-action" onclick="marcarComoLeida('${notifId}', event)" title="Marcar como leído">✔️</button>` : '<span style="color:var(--success-color); font-size: 0.85em; font-weight: bold;">Leído</span>'}
                            </div>
                        `;
                    });
                    localStorage.setItem('notified_attendance', JSON.stringify(notifiedAttendance));
                }

                // 4. Anuncios nuevos del tablón de anuncios (siempre activos)
                if (data.anuncios && Array.isArray(data.anuncios)) {
                    data.anuncios.forEach(an => {
                        const notifId = `ann_${an.id}`;
                        const unreadClass = isUnread(notifId) ? 'unread' : '';
                        if (isUnread(notifId)) totalAlerts++;
                        
                        if (!notifiedAnuncios.includes(an.id)) {
                            // showToast(`📢 Nuevo anuncio: "${escapeHtml(an.titulo)}"`, 'danger', () => {
                            //     verDetalleNotif(notifId);
                            // });
                            notifiedAnuncios.push(an.id);
                        }
                        
                        listHtml += `
                            <div class="notif-item ${unreadClass}" onclick="verDetalleNotif('${notifId}')">
                                <div class="notif-item-content">
                                    <div class="notif-item-title">📢 Anuncio: ${escapeHtml(an.titulo)}</div>
                                    <div style="font-size:0.85em; color:var(--text-muted); margin-top:2px;">Haz clic para ver detalles</div>
                                    <div class="notif-item-time">${escapeHtml(an.fecha)}</div>
                                </div>
                                ${isUnread(notifId) ? `<button class="notif-item-action" onclick="marcarComoLeida('${notifId}', event)" title="Marcar como leído">✔️</button>` : '<span style="color:var(--success-color); font-size: 0.85em; font-weight: bold;">Leído</span>'}
                            </div>
                        `;
                    });
                    localStorage.setItem('notified_anuncios', JSON.stringify(notifiedAnuncios));
                }

                // Render list
                const dropdownList = document.getElementById('notif_dropdown_list');
                if (dropdownList) {
                    if (listHtml) {
                        dropdownList.innerHTML = listHtml;
                    } else {
                        dropdownList.innerHTML = '<div class="notif-empty">No tienes notificaciones nuevas</div>';
                    }
                }

                // Actualizar la campana
                const badge = document.getElementById('notif_badge_count');
                if (badge) {
                    if (totalAlerts > 0) {
                        badge.innerText = totalAlerts;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            })
            .catch(err => console.log('Error del cargador de notificaciones:', err));
        }

        // Ejecutar al cargar la página
        document.addEventListener('DOMContentLoaded', () => {
            checkNotifications();
            // Revisar actualizaciones cada 30 segundos
            setInterval(checkNotifications, 30000);
        });
    
    function confirmarLogoutGlobal(e) {
        e.preventDefault();
        mostrarConfirmacionGlobal('Cerrar sesi�n', '�Est�s seguro de que deseas cerrar sesi�n?', function() {
            window.location.href = 'acciones/logout.php';
        });
    }

    </script>








