<?php
session_start();
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token']) || $_SESSION['rol'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$url = "http://127.0.0.1:5000/usuarios"; 
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $_SESSION['token'],
    'Content-Type: application/json'
]);

$respuesta = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$usuarios = [];
if ($http_code == 200) {
    $usuarios = json_decode($respuesta, true);
}

// --- CONFIGURACIÓN DEL HEADER ---
$pagina_id = 'gestion_usuarios';
$titulo_seccion = 'Gestión de usuarios';
$estilos_adicionales = '<link rel="stylesheet" href="recursos/dashboard.css">';
include 'componentes/header.php';
?>

        <div class="contenedor-datos">
            <div class="tarjeta">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <h2 style="margin:0;">Listado de Usuarios</h2>
                        <select id="filtroRol" class="form-control" style="padding: 5px 10px; border-radius: 4px; border: 1px solid var(--border-color);" onchange="filtrarUsuarios()">
                            <option value="todos">Todos los roles</option>
                            <option value="admin">Administrador</option>
                            <option value="profesor">Profesor</option>
                            <option value="alumno">Alumno</option>
                        </select>
                    </div>
                    <button class="btn btn-primario btn-sm" onclick="abrirModalUsuario()" style="display:flex; align-items:center; gap: 8px; border-radius:20px; padding: 6px 16px; background-color:var(--success-color); border:none; font-weight:bold; cursor:pointer; color:white; font-size:14px;">
                        <span style="display:flex; align-items:center; justify-content:center; width:22px; height:22px; background:rgba(255,255,255,0.3); border-radius:50%; font-size:18px; line-height:1;">+</span> 
                        Añadir nuevo usuario
                    </button>
                </div>
                
                <div id="caja_notificacion" class="notificacion"></div>

                <div class="tabla-responsiva">
                    <table class="tabla-academica" id="tablaUsuarios">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Nombre Completo</th>
                                <th>DNI</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th style="text-align: center;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($usuarios)): ?>
                                <tr>
                                    <td colspan="6" class="estado-vacio">No hay usuarios registrados o no se pudo cargar la información.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($usuarios as $u): ?>
                                    <tr>
                                        <td style="text-align: center;"><strong><?php echo htmlspecialchars($u['nombre_usuario']); ?></strong></td>
                                        <td style="text-align: center;"><?php echo htmlspecialchars($u['nombre_completo']); ?></td>
                                        <td style="text-align: center;"><?php echo htmlspecialchars($u['dni'] ?? '-'); ?></td>
                                        <td style="text-align: center;"><?php echo htmlspecialchars($u['email'] ?? '-'); ?></td>
                                        <td style="text-align: center;">
                                            <span class="badge badge-<?php echo strtolower($u['rol']); ?>">
                                                <?php echo ucfirst(htmlspecialchars($u['rol'])); ?>
                                            </span>
                                        </td>
                                        <td style="text-align: center; white-space: nowrap;">
                                            <button class="btn-icon" title="Editar usuario" onclick='prepararEdicion(<?php echo json_encode($u); ?>)' style="margin-right: 5px;">✏️</button>
                                            <?php if ($u['nombre_usuario'] != $_SESSION['nombre_usuario']): ?>
                                            <button class="btn-icon" title="Eliminar usuario" onclick="confirmarEliminar(<?php echo $u['id']; ?>)">❌</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL USUARIO -->
    <div id="modalUsuario" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Nuevo Usuario</h3>
            </div>
            <form id="formUsuario" onsubmit="guardarUsuario(event)">
                <input type="hidden" id="editIdInput">
                <div class="form-group">
                    <label>Nombre de usuario</label>
                    <input type="text" id="nombreUsuarioInput" required placeholder="Ej: jlopez">
                </div>
                <div class="form-group">
                    <label>Nombre completo</label>
                    <input type="text" id="nombreCompletoInput" required placeholder="Ej: Juan López">
                </div>
                <div class="form-group">
                    <label>DNI</label>
                    <input type="text" id="dniInput" placeholder="Ej: 12345678A">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="emailInput" required placeholder="Ej: juan@enjoyfe.es">
                </div>
                <div class="form-group">
                    <label>Rol</label>
                    <select id="rolInput" required>
                        <option value="alumno">Alumno</option>
                        <option value="profesor">Profesor</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="form-group">
                    <label id="labelPassword">Contraseña</label>
                    <input type="password" id="passwordInput" placeholder="Contraseña">
                    <small id="helpPassword" style="color: var(--text-muted); display: none;">Deja este campo en blanco si no quieres cambiar la contraseña.</small>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="btn-guardar">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="recursos/js/gestion_usuarios.js"></script>

<?php include 'componentes/footer.php'; ?>
</body>
</html>
