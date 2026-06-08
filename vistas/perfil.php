<?php
session_start();
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token'])) {
    header("Location: index.php");
    exit();
}

$url = "http://127.0.0.1:5000/academico/perfil"; 
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

$perfil_data = [];
if ($http_code == 200) {
    $perfil_data = json_decode($respuesta, true);
}

$nombre = $perfil_data['nombre'] ?? 'Usuario';
$rol = ucfirst($perfil_data['rol'] ?? 'Alumno');
$curso = $perfil_data['curso'] ?? 'Sin curso';
$dni = $perfil_data['dni'] ?? 'No especificado';
$email = $perfil_data['email'] ?? 'No especificado';
$tutoria = $perfil_data['tutoria'] ?? 'Ninguna';

$cursos_profesor = [];
if (strtolower($rol) === 'profesor') {
    $url_cursos = "http://127.0.0.1:5000/academico/mis-cursos";
    $ch_cursos = curl_init($url_cursos);
    curl_setopt($ch_cursos, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch_cursos, CURLOPT_HTTPGET, true);
    curl_setopt($ch_cursos, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . $_SESSION['token'],
        'Content-Type: application/json'
    ]);
    $res_cursos = curl_exec($ch_cursos);
    if (curl_getinfo($ch_cursos, CURLINFO_HTTP_CODE) == 200) {
        $cursos_profesor = json_decode($res_cursos, true);
    }
    curl_close($ch_cursos);
}

// --- CONFIGURACIÓN DEL HEADER ---
$pagina_id = 'perfil';
$titulo_seccion = 'Mi perfil';
$estilos_adicionales = '<link rel="stylesheet" href="recursos/perfil.css">';

include __DIR__ . '/../componentes/header.php';
?>

        <div class="contenedor-perfil">
            <div class="tarjeta-perfil">
                
                <div class="cabecera-perfil">
                    <span><?php echo $rol; ?></span>
                    <h2><?php echo htmlspecialchars($nombre); ?></h2>
                </div>

                <div id="caja_notificacion" class="notificacion"></div>

                <div class="seccion-titulo">Datos personales</div>
                <?php if (strtolower($rol) === 'admin'): ?>
                    <div class="form-group">
                        <label>Rol del sistema</label>
                        <input type="text" class="input-readonly" value="Administrador del sistema" readonly>
                    </div>
                    <div class="form-group">
                        <label>Permisos</label>
                        <input type="text" class="input-readonly" value="Altas, bajas y modificaciones de usuarios, cursos y asignaturas" readonly>
                    </div>
                <?php elseif (strtolower($rol) === 'profesor'): ?>
                    <div class="form-group">
                        <label>Tutoría asignada</label>
                        <input type="text" class="input-readonly" value="<?php echo htmlspecialchars($tutoria); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Mis cursos impartidos</label>
                        <?php if (empty($cursos_profesor)): ?>
                            <input type="text" class="input-readonly" value="No se han asignado cursos" readonly>
                        <?php else: ?>
                            <ul class="input-readonly" style="list-style-type:disc; margin:0; padding-left:30px; padding-top:10px; padding-bottom:10px; min-height:42px;">
                                <?php foreach ($cursos_profesor as $cp): ?>
                                    <li><?php echo htmlspecialchars($cp['nombre']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="form-group">
                        <label>Curso actual</label>
                        <input type="text" class="input-readonly" value="<?php echo htmlspecialchars($curso); ?>" readonly>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label>DNI / NIE</label>
                    <input type="text" class="input-readonly" value="<?php echo htmlspecialchars($dni); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Correo electrónico</label>
                    <input type="email" class="input-readonly" value="<?php echo htmlspecialchars($email); ?>" readonly>
                </div>

                <hr class="separador">

                <div class="seccion-titulo">Contraseña</div>
                <form id="form_perfil" onsubmit="cambiar_contrasena(event)">
                    <div class="form-group">
                        <label>Contraseña actual</label>
                        <input type="password" id="pass_actual" class="input-editable" required autocomplete="off" placeholder="Ingresa tu contraseña actual">
                    </div>
                    <div class="form-group">
                        <label>Nueva contraseña</label>
                        <input type="password" id="pass_nueva" class="input-editable" required autocomplete="off" placeholder="Escribe tu nueva contraseña">
                    </div>
                    <div class="form-group">
                        <label>Repetir nueva contraseña</label>
                        <input type="password" id="pass_repe" class="input-editable" required autocomplete="off" placeholder="Vuelve a escribir la nueva contraseña">
                    </div>
                    
                    <button type="submit" class="btn-guardar">Cambiar contraseña</button>
                </form>

            </div>
        </div>
    </div> 

    <script>
        function mostrar_mensaje(texto, es_error) {
            const caja = document.getElementById('caja_notificacion');
            caja.style.display = 'block';
            caja.innerText = texto;
            caja.className = es_error ? 'notificacion error' : 'notificacion exito';
            setTimeout(() => { caja.style.display = 'none'; }, 5000);
        }

        function cambiar_contrasena(evento_formulario) {
            evento_formulario.preventDefault();
            
            const valor_pass_actual = document.getElementById('pass_actual').value;
            const valor_pass_nueva = document.getElementById('pass_nueva').value;
            const valor_pass_repe = document.getElementById('pass_repe').value;

            if (valor_pass_nueva !== valor_pass_repe) {
                mostrar_mensaje("Las contraseñas nuevas no coinciden. Revisa lo que has escrito.", true);
                return;
            }

            const datos_peticion = {
                contrasena_actual: valor_pass_actual,
                contrasena_nueva: valor_pass_nueva,
                contrasena_repetida: valor_pass_repe
            };

            fetch('acciones/actualizar_perfil.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos_peticion)
            })
            .then(respuesta_servidor => respuesta_servidor.json())
            .then(datos_respuesta => {
                if(datos_respuesta.mensaje === "Contraseña actualizada correctamente") {
                    mostrar_mensaje("¡Éxito! Tu contraseña ha sido actualizada.", false);
                    document.getElementById('form_perfil').reset(); 
                } else {
                    mostrar_mensaje(datos_respuesta.mensaje, true);
                }
            })
            .catch(error_conexion => {
                console.error('Error:', error_conexion);
                mostrar_mensaje("Hubo un problema de conexión con el servidor.", true);
            });
        }
    </script>
<?php include __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>