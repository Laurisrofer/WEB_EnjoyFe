<?php
session_start();
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token'])) {
    header("Location: index.php");
    exit();
}

// --- CONFIGURACIÓN DEL HEADER ---
$pagina_id = 'mensajeria';
$titulo_seccion = 'Mensajería interna';
$estilos_adicionales = '<link rel="stylesheet" href="recursos/mensajeria.css">';

include __DIR__ . '/../componentes/header.php';
?>

<div class="contenedor-mensajeria">
    <div class="mensajeria-layout">
        
        <!-- COLUMNA IZQUIERDA: LISTADO DE MENSAJES -->
        <div class="seccion-lista-mensajes">
            <div class="cabecera-lista">
                <h2>Bandeja de entrada</h2>
                <button class="btn-redactar" onclick="abrirRedaccion()">✏️ Redactar mensaje</button>
            </div>
            
            <div class="lista-mensajes-scroll" id="lista_mensajes">
                <!-- Rellenado dinámicamente por JavaScript -->
                <div class="estado-lista-vacio">Cargando mensajes...</div>
            </div>
        </div>
        
        <!-- COLUMNA DERECHA: DETALLE O FORMULARIO -->
        <div class="seccion-detalle-mensaje" id="panel_derecho">
            
            <!-- VISTA VACÍA (INICIAL) -->
            <div class="detalle-vacio" id="vista_vacia">
                <div class="icono">✉️</div>
                <h3>Bandeja de mensajes</h3>
                <p>Selecciona un mensaje de la bandeja de entrada para leerlo o pulsa "Redactar mensaje" para enviar uno nuevo.</p>
            </div>
            
            <!-- VISTA LECTURA -->
            <div class="panel-lectura" id="vista_lectura" style="display: none;">
                <div class="lectura-cabecera">
                    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                        <h2 class="lectura-asunto" id="lectura_asunto">Asunto del mensaje</h2>
                        <button class="btn-eliminar-mensaje" id="btn_eliminar_lectura" style="background:var(--danger-color); color:white; border:none; padding:8px 12px; border-radius:4px; cursor:pointer; font-weight:bold; font-size:0.9em;" onclick="eliminarMensajeActivo()">Eliminar</button>
                    </div>
                    <div class="lectura-meta-fila" style="margin-top: 10px;">
                        <div class="avatar-mensaje" id="lectura_avatar">U</div>
                        <div>
                            <div class="lectura-usuario-nombre" id="lectura_de">De: remitente</div>
                            <div class="lectura-usuario-para" id="lectura_para" style="font-size: 0.8em; color: var(--text-muted);">Para: destinatario</div>
                        </div>
                        <div class="lectura-fecha" id="lectura_fecha">Fecha y hora</div>
                    </div>
                </div>
                
                <div class="lectura-cuerpo" id="lectura_cuerpo">
                    Cuerpo del mensaje...
                </div>
                
                <!-- Sección para descargar adjunto -->
                <div class="lectura-adjunto" id="lectura_adjunto_seccion" style="display: none;">
                    <div class="lectura-adjunto-titulo">Archivo adjunto</div>
                    <a href="#" download id="lectura_adjunto_link" class="pildora-descarga">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 5px;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"></path></svg> Descargar adjunto
                    </a>
                </div>
            </div>
            
            <!-- VISTA REDACCIÓN -->
            <div class="panel-redaccion" id="vista_redaccion" style="display: none;">
                <h3>Nuevo mensaje</h3>
                <div id="caja_alerta" class="banner-alerta" style="display: none;"></div>
                
                <form class="form-redaccion" id="form_redaccion" onsubmit="enviarMensaje(event)">
                    
                    <!-- Destinatario -->
                    <div class="fila-formulario">
                        <label for="destinatario_select">Destinatario</label>
                        <div class="select-buscador-wrapper" style="position: relative;">
                            <input type="hidden" id="destinatario_usuario_real" required>
                            <input type="text" id="destinatario_buscador" class="input-redaccion" placeholder="Haz clic o escribe para buscar un usuario..." autocomplete="off" onfocus="mostrarSugerencias()" onblur="ocultarSugerenciasConRetraso()" onkeyup="filtrarSugerencias()" required>
                            <div id="sugerencias_contactos" style="display:none; position:absolute; top:100%; left:0; width:100%; max-height:180px; overflow-y:auto; background:white; border:1px solid #ccc; border-radius:4px; z-index:999; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-top:2px;">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Asunto -->
                    <div class="fila-formulario">
                        <label for="asunto_input">Asunto</label>
                        <input type="text" id="asunto_input" class="input-redaccion" placeholder="Escribe el asunto del mensaje" required autocomplete="off">
                    </div>
                    
                    <!-- Cuerpo del mensaje -->
                    <div class="fila-formulario">
                        <label for="cuerpo_textarea">Mensaje</label>
                        <textarea id="cuerpo_textarea" class="input-redaccion textarea-redaccion" placeholder="Escribe tu mensaje aquí..." required></textarea>
                    </div>
                    
                    <!-- Adjuntar archivo -->
                    <div class="fila-formulario">
                        <label>Adjuntar archivo (opcional)</label>
                        <div class="input-file-wrapper">
                            <label for="archivo_adjunto" class="label-file">
                                📁 Elegir archivo
                            </label>
                            <input type="file" id="archivo_adjunto" class="input-file-oculto" onchange="actualizarNombreArchivo(this)">
                            <span class="file-status-text" id="archivo_nombre_texto">Ningún archivo seleccionado</span>
                            <button type="button" class="btn-quitar-adjunto" id="btn_quitar_adjunto" style="display:none; border:none; background:transparent; color:var(--danger-color); cursor:pointer; font-size: 0.9em; margin-left:10px; font-weight:bold;" onclick="quitarAdjunto()">❌ Quitar</button>
                        </div>
                    </div>
                    
                    <!-- Acciones -->
                    <div class="redaccion-acciones">
                        <button type="button" class="btn-cancelar-redaccion" onclick="cancelarRedaccion()">Cancelar</button>
                        <button type="submit" class="btn-enviar-mensaje" id="btn_enviar">Enviar Mensaje</button>
                    </div>
                    
                </form>
            </div>
            
        </div>
    </div>
</div>

<script>
    window.EnjoyfeConfig = window.EnjoyfeConfig || {};
    window.EnjoyfeConfig.nombreUsuario = "<?php echo htmlspecialchars($_SESSION['nombre_usuario'] ?? '', ENT_QUOTES, 'UTF-8'); ?>";
</script>
<script src="recursos/js/mensajeria.js"></script>

<?php include __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>
