<?php
session_start();
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token'])) {
    header("Location: index.php");
    exit();
}
$rol_usuario = $_SESSION['rol'];
$pagina_id = 'cookies';
$titulo_seccion = 'Política de cookies';
$estilos_adicionales = '';
include 'componentes/header.php';
?>
<div class="contenedor-datos" style="padding: 20px;">
    <div class="tarjeta" style="padding: 30px; border-radius: 12px;">
        <h2>¿Qué son las cookies?</h2>
        <p>Una cookie es un pequeño fichero de texto que se almacena en su navegador cuando visita casi cualquier página web. Su utilidad es que la web sea capaz de recordar su visita cuando vuelva a navegar por esa página.</p>

        <h2 style="margin-top: 25px;">Cookies utilizadas en esta plataforma</h2>
        <p>Esta plataforma educativa (EnjoyFe) utiliza las siguientes cookies y tecnologías de almacenamiento local:</p>
        
        <ul style="margin-top: 15px; margin-left: 20px; line-height: 1.6;">
            <li><strong>Cookies de sesión (Esenciales)</strong>: Son cookies temporales que permanecen en el archivo de cookies de su navegador hasta que abandona la página web, por lo que ninguna queda registrada en el disco duro del usuario. En nuestro caso, usamos <code>PHPSESSID</code> para mantener su sesión activa.</li>
            <li><strong>Almacenamiento local (Preferencias)</strong>: Utilizamos el <code>localStorage</code> de su navegador para guardar sus preferencias visuales y de usabilidad, como el tema (claro/oscuro), el tamaño de fuente elegido, y sus preferencias de notificaciones y alertas leídas. Esto no se transmite al servidor y solo sirve para mejorar su experiencia de uso.</li>
        </ul>

        <h2 style="margin-top: 25px;">Cómo gestionar las cookies</h2>
        <p>Usted puede permitir, bloquear o eliminar las cookies instaladas en su equipo mediante la configuración de las opciones del navegador instalado en su ordenador. Sin embargo, si desactiva las cookies de sesión, no podrá acceder a su cuenta de EnjoyFe.</p>
        
        <div style="background-color: var(--bg-color); padding: 15px; border-radius: 8px; margin-top: 25px; border-left: 4px solid var(--primary-color);">
            <p style="margin: 0;"><strong>¿Dudas?</strong> Si tiene alguna duda sobre esta política de cookies, puede contactar con nosotros a través de nuestro <a href="contacto.php" style="color: var(--primary-color); font-weight: bold; text-decoration: none;">formulario de contacto</a>.</p>
        </div>
    </div>
</div>
<?php include 'componentes/footer.php'; ?>
</body>
</html>
