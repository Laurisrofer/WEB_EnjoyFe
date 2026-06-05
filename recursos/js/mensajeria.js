let mensajesCache = [];
    let contactosCache = [];
    let mensajeActivoId = null;

    // Formateador de fechas
    function formatearFechaMensaje(isoString) {
        const d = new Date(isoString);
        return d.toLocaleDateString('es-ES') + ' ' + d.toLocaleTimeString('es-ES', {hour: '2-digit', minute:'2-digit'});
    }

    // Cargar bandeja de entrada
    function cargarBandeja() {
        const listaCont = document.getElementById('lista_mensajes');
        
        fetch('acciones/gestion_mensajes.php?action=obtener')
        .then(response => {
            if (response.ok) return response.json();
            throw new Error('Error al cargar la bandeja');
        })
        .then(data => {
            mensajesCache = data;
            
            if (data.length === 0) {
                listaCont.innerHTML = '<div class="estado-lista-vacio">No tienes mensajes en tu bandeja de entrada.</div>';
                return;
            }
            
            let readNotifs = JSON.parse(localStorage.getItem('read_notifications') || '[]');
            
            listaCont.innerHTML = data.map(msg => {
                const isUnread = !readNotifs.includes(`msg_${msg.id}`);
                const unreadClass = isUnread ? 'no-leido' : '';
                const inicial = msg.de.charAt(0).toUpperCase();
                const extracto = msg.cuerpo ? msg.cuerpo.substring(0, 50) + (msg.cuerpo.length > 50 ? '...' : '') : '';
                const fecha = formatearFechaMensaje(msg.fecha);
                
                return `
                    <div class="mensaje-item ${unreadClass}" id="msg_item_${msg.id}" onclick="leerMensaje(${msg.id})">
                        <div class="avatar-mensaje">${inicial}</div>
                        <div class="mensaje-item-info">
                            <div class="mensaje-item-cabecera">
                                <span class="mensaje-item-remitente">${escapeHtml(msg.de)}</span>
                                <span class="mensaje-item-fecha">${fecha}</span>
                            </div>
                            <div class="mensaje-item-asunto">${escapeHtml(msg.asunto)}</div>
                            <div class="mensaje-item-extracto">${escapeHtml(extracto)}</div>
                        </div>
                        ${isUnread ? `<span class="indicador-no-leido" id="unread_dot_${msg.id}"></span>` : ''}
                    </div>
                `;
            }).join('');
        })
        .catch(err => {
            console.error(err);
            listaCont.innerHTML = '<div class="estado-lista-vacio" style="color:var(--danger-color);">⚠️ Error de conexión con el servidor.</div>';
        });
    }

    // Cargar la agenda de contactos y renderizar buscador
    function cargarContactos() {
        fetch('acciones/gestion_mensajes.php?action=contactos')
        .then(response => {
            if (response.ok) return response.json();
            throw new Error('Error al cargar contactos');
        })
        .then(data => {
            // Filtrar para no escribirnos a nosotros mismos
            const miUsuario = window.EnjoyfeConfig.nombreUsuario;
            contactosCache = data.filter(c => c.nombre_usuario !== miUsuario);
        })
        .catch(err => console.error('Error al cargar contactos:', err));
    }

    function renderizarSugerencias(lista) {
        const contenedor = document.getElementById('sugerencias_contactos');
        if (lista.length === 0) {
            contenedor.innerHTML = '<div style="padding:10px; color:#999; font-size:0.9em;">No se encontraron usuarios</div>';
            return;
        }
        
        contenedor.innerHTML = lista.map(c => {
            const rolEs = c.rol.charAt(0).toUpperCase() + c.rol.slice(1);
            return `<div style="padding:10px; cursor:pointer; border-bottom:1px solid #eee; color:#333;" 
                         onmouseover="this.style.background='#f5f5f5'" 
                         onmouseout="this.style.background='white'"
                         onclick="seleccionarContacto('${c.nombre_usuario}', '${c.nombre_completo}')">
                        <strong>${c.nombre_completo}</strong> <span style="font-size:0.85em; color:#666;">(${rolEs})</span>
                    </div>`;
        }).join('');
    }

    function mostrarSugerencias() {
        document.getElementById('sugerencias_contactos').style.display = 'block';
        // Si no hay texto, mostrar todos
        const texto = document.getElementById('destinatario_buscador').value.toLowerCase();
        if (texto === '') {
            renderizarSugerencias(contactosCache);
        } else {
            filtrarSugerencias();
        }
    }

    function ocultarSugerenciasConRetraso() {
        // Retraso para dar tiempo a que se registre el clic en la opción
        setTimeout(() => {
            document.getElementById('sugerencias_contactos').style.display = 'none';
        }, 200);
    }

    function filtrarSugerencias() {
        const texto = document.getElementById('destinatario_buscador').value.toLowerCase();
        const filtrados = contactosCache.filter(c => 
            c.nombre_completo.toLowerCase().includes(texto) || 
            c.nombre_usuario.toLowerCase().includes(texto) ||
            c.rol.toLowerCase().includes(texto)
        );
        renderizarSugerencias(filtrados);
    }

    function seleccionarContacto(usuario, nombreCompleto) {
        document.getElementById('destinatario_usuario_real').value = usuario;
        document.getElementById('destinatario_buscador').value = nombreCompleto;
        document.getElementById('sugerencias_contactos').style.display = 'none';
    }

    // Leer mensaje seleccionado
    function leerMensaje(id) {
        mensajeActivoId = id;
        const msg = mensajesCache.find(m => m.id === id);
        if (!msg) return;
        
        // Cambiar estilos de activos en la lista
        document.querySelectorAll('.mensaje-item').forEach(el => el.classList.remove('activo'));
        const itemEl = document.getElementById(`msg_item_${id}`);
        if (itemEl) {
            itemEl.classList.add('activo');
            itemEl.classList.remove('no-leido');
        }
        
        // Quitar bolita azul de no leído
        const dot = document.getElementById(`unread_dot_${id}`);
        if (dot) dot.remove();
        
        // Marcar en localStorage para sincronizar con la campana de notificaciones del Header
        let readNotifs = JSON.parse(localStorage.getItem('read_notifications') || '[]');
        if (!readNotifs.includes(`msg_${id}`)) {
            readNotifs.push(`msg_${id}`);
            localStorage.setItem('read_notifications', JSON.stringify(readNotifs));
            // Forzar recarga de la campana en el header si existe la función
            if (typeof checkNotifications === 'function') {
                checkNotifications();
            }
        }
        
        // Rellenar panel de lectura
        document.getElementById('lectura_asunto').innerText = msg.asunto;
        document.getElementById('lectura_de').innerText = `De: ${msg.de}`;
        document.getElementById('lectura_para').innerText = `Para: ${msg.para}`;
        document.getElementById('lectura_fecha').innerText = formatearFechaMensaje(msg.fecha);
        document.getElementById('lectura_avatar').innerText = msg.de.charAt(0).toUpperCase();
        document.getElementById('lectura_cuerpo').innerText = msg.cuerpo;
        
        // Procesar adjunto
        const adjSec = document.getElementById('lectura_adjunto_seccion');
        const adjLink = document.getElementById('lectura_adjunto_link');
        
        if (msg.adjunto) {
            adjSec.style.display = 'block';
            adjLink.href = msg.adjunto;
            // Extraer nombre limpio del archivo quitando el timestamp inicial
            const baseName = msg.adjunto.split('/').pop();
            const cleanName = baseName.includes('_') ? baseName.substring(baseName.indexOf('_') + 1) : baseName;
            adjLink.innerHTML = `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 5px;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg> Descargar: ${escapeHtml(cleanName)}`;
        } else {
            adjSec.style.display = 'none';
        }
        
        // Intercambiar paneles
        document.getElementById('vista_vacia').style.display = 'none';
        document.getElementById('vista_redaccion').style.display = 'none';
        document.getElementById('vista_lectura').style.display = 'flex';
    }

    // Abrir formulario de redacción
    function abrirRedaccion() {
        document.getElementById('form_redaccion').reset();
        document.getElementById('destinatario_usuario_real').value = '';
        document.getElementById('archivo_nombre_texto').innerText = "Ningún archivo seleccionado";
        document.getElementById('btn_quitar_adjunto').style.display = 'none';
        document.getElementById('caja_alerta').style.display = 'none';
        
        document.getElementById('vista_vacia').style.display = 'none';
        document.getElementById('vista_lectura').style.display = 'none';
        document.getElementById('vista_redaccion').style.display = 'flex';
        
        // Quitar marcas activas del listado
        document.querySelectorAll('.mensaje-item').forEach(el => el.classList.remove('activo'));
    }

    // Cancelar envío de mensaje
    function cancelarRedaccion() {
        document.getElementById('vista_redaccion').style.display = 'none';
        document.getElementById('vista_lectura').style.display = 'none';
        document.getElementById('vista_vacia').style.display = 'flex';
    }

    // Mostrar nombre del archivo seleccionado en el selector file
    function actualizarNombreArchivo(input) {
        const span = document.getElementById('archivo_nombre_texto');
        const btnQuitar = document.getElementById('btn_quitar_adjunto');
        if (input.files && input.files.length > 0) {
            span.innerText = input.files[0].name;
            btnQuitar.style.display = 'inline-block';
        } else {
            span.innerText = "Ningún archivo seleccionado";
            btnQuitar.style.display = 'none';
        }
    }

    // Quitar archivo adjunto
    function quitarAdjunto() {
        const input = document.getElementById('archivo_adjunto');
        input.value = "";
        actualizarNombreArchivo(input);
    }
    
    // Eliminar mensaje
    function eliminarMensajeActivo() {
        if (!mensajeActivoId) return;
        
        mostrarConfirmacionGlobal(
            "Eliminar mensaje",
            "¿Estás seguro de que deseas eliminar este mensaje de forma permanente?",
            () => {
                const btn = document.getElementById('btn_eliminar_lectura');
                btn.disabled = true;
                btn.innerText = "Eliminando...";
                
                const formData = new FormData();
                formData.append('id', mensajeActivoId);
                
                fetch('acciones/gestion_mensajes.php?action=eliminar', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.ok) return response.json();
                    throw new Error('Error al eliminar');
                })
                .then(data => {
                    if (typeof showToast === 'function') {
                        showToast("Mensaje eliminado correctamente", "success");
                    }
                    // Volver a la vista vacía
                    cancelarRedaccion();
                    // Recargar bandeja
                    cargarBandeja();
                })
                .catch(err => {
                    console.error(err);
                    if (typeof showToast === 'function') {
                        showToast("Error al eliminar el mensaje", "danger");
                    } else {
                        alert("Error al eliminar el mensaje");
                    }
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerText = "Eliminar";
                });
            }
        );
    }

    // Enviar mensaje
    function enviarMensaje(e) {
        e.preventDefault();
        
        const btn = document.getElementById('btn_enviar');
        const alertBox = document.getElementById('caja_alerta');
        
        btn.disabled = true;
        btn.innerText = "Enviando...";
        alertBox.style.display = 'none';
        
        const destReal = document.getElementById('destinatario_usuario_real').value;
        if (!destReal) {
            alertBox.className = 'banner-alerta error';
            alertBox.innerText = `⚠️ Por favor, selecciona un usuario válido de la lista de sugerencias.`;
            alertBox.style.display = 'block';
            btn.disabled = false;
            btn.innerText = "Enviar Mensaje";
            return;
        }

        const formData = new FormData();
        formData.append('destinatario', destReal);
        formData.append('asunto', document.getElementById('asunto_input').value);
        formData.append('cuerpo', document.getElementById('cuerpo_textarea').value);
        
        const fileInput = document.getElementById('archivo_adjunto');
        if (fileInput.files.length > 0) {
            formData.append('adjunto', fileInput.files[0]);
        }
        
        fetch('acciones/gestion_mensajes.php?action=enviar', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) return response.json();
            return response.json().then(err => { throw new Error(err.mensaje || 'Error al enviar'); });
        })
        .then(data => {
            // Mostrar mensaje de éxito temporal
            alertBox.className = 'banner-alerta exito';
            alertBox.innerText = "¡Mensaje enviado con éxito!";
            alertBox.style.display = 'block';
            
            // Recargar bandeja de entrada
            cargarBandeja();
            
            // Volver a la vista vacía tras 1.5 segundos
            setTimeout(() => {
                cancelarRedaccion();
                if (typeof showToast === 'function') {
                    showToast("✉️ Mensaje enviado correctamente", "success");
                }
            }, 1500);
        })
        .catch(err => {
            console.error(err);
            alertBox.className = 'banner-alerta error';
            alertBox.innerText = `⚠️ ${err.message}`;
            alertBox.style.display = 'block';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = "Enviar Mensaje";
        });
    }

    // Inicialización al cargar la página
    document.addEventListener('DOMContentLoaded', () => {
        cargarBandeja();
        cargarContactos();
    });