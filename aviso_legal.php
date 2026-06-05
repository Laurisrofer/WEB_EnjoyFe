<?php
session_start();
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token'])) {
    header("Location: index.php");
    exit();
}
$rol_usuario = $_SESSION['rol'];
$pagina_id = 'legal';
$titulo_seccion = 'Aviso legal';
$estilos_adicionales = '';
include 'componentes/header.php';
?>
<div class="contenedor-datos" style="padding: 20px;">
    <div class="tarjeta" style="padding: 30px; border-radius: 12px;">
        <h2>Aviso Legal y Condiciones de Uso</h2>
        
        <h3 style="margin-top: 25px;">1. Titularidad de la plataforma</h3>
        <p>En cumplimiento de lo dispuesto en el artículo 10 de la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y de Comercio Electrónico (LSSI-CE), se informa que esta plataforma web es propiedad del Centro Educativo EnjoyFe, con domicilio en Madrid, España.</p>

        <h3 style="margin-top: 25px;">2. Condiciones de uso</h3>
        <p>El acceso y uso de esta plataforma atribuye la condición de Usuario (ya sea alumno, profesor o administrador). El uso de la plataforma es de carácter exclusivamente educativo y académico. El Usuario se compromete a hacer un uso adecuado y lícito del sitio web y de sus contenidos, de conformidad con la legislación aplicable, el presente Aviso Legal, la moral y las buenas costumbres generalmente aceptadas y el orden público.</p>
        
        <h3 style="margin-top: 25px;">3. Propiedad intelectual</h3>
        <p>El código fuente, los diseños gráficos, la información y los contenidos que se recogen en esta plataforma web están protegidos por la legislación española sobre los derechos de propiedad intelectual e industrial a favor del Centro Educativo EnjoyFe. No se permite la reproducción, publicación, tratamiento informático o transmisión de parte alguna de esta web, sin el permiso previo y por escrito del titular.</p>

        <h3 style="margin-top: 25px;">4. Protección de datos de carácter personal</h3>
        <p>De acuerdo con la normativa vigente en materia de protección de datos (RGPD), se informa que los datos personales recogidos a través de esta plataforma (nombre, DNI/NIE, correo electrónico, calificaciones y asistencias) serán tratados con la exclusiva finalidad de gestionar el expediente académico y facilitar la comunicación dentro de la comunidad educativa. Las contraseñas se almacenan de forma segura (hasheadas).</p>
        
        <h3 style="margin-top: 25px;">5. Limitación de responsabilidad</h3>
        <p>El Centro Educativo EnjoyFe no se hace responsable de los posibles errores de seguridad que se puedan producir, ni de los posibles daños que puedan causarse al sistema informático del usuario (hardware y software), o a los ficheros o documentos almacenados en el mismo, como consecuencia de un mal uso de la plataforma.</p>

        <div style="background-color: var(--bg-color); padding: 15px; border-radius: 8px; margin-top: 30px; border-left: 4px solid var(--primary-color);">
            <p style="margin: 0; text-align: center; color: var(--text-muted); font-size: 0.9em;">Este documento fue actualizado por última vez en junio de 2026.</p>
        </div>
    </div>
</div>
<?php include 'componentes/footer.php'; ?>
</body>
</html>
