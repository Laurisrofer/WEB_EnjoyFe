<?php
session_start();
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token'])) {
    header("Location: index.php");
    exit();
}

// --- CONFIGURACIÓN DEL HEADER ---
$pagina_id = 'ayuda';
$titulo_seccion = 'Ayuda y soporte';
$estilos_adicionales = '<link rel="stylesheet" href="recursos/ayuda.css">';

include 'componentes/header.php';
?>

<div class="contenedor-ayuda">
    <div class="tarjeta-ayuda">
        <h2>Centro de ayuda al usuario</h2>
        <p class="ayuda-desc">Consulta las preguntas más frecuentes sobre el uso del portal académico de Enjoyfe o accede a los manuales interactivos.</p>

        <!-- SECCIÓN 1: MANUALES -->
        <div>
            <div class="seccion-titulo">Recursos y manuales</div>
            <div class="card-manual">
                <div class="manual-info">
                    <div class="manual-title">Manual de usuario Enjoyfe (interactivo)</div>
                    <div class="manual-subtitle">Una guía interactiva web completa con opción de guardado nativo a PDF. Aprende a gestionar tu perfil, horarios, asignaturas y notificaciones.</div>
                </div>
                <a href="manual.php" target="_blank" class="btn-descargar">
                    📄 Leer manual
                </a>
            </div>
        </div>

        <!-- SECCIÓN 2: PREGUNTAS FRECUENTES (FAQ) -->
        <div>
            <div class="seccion-titulo">Preguntas frecuentes (FAQ)</div>
            
            <div class="faq-container">
                
                <!-- Pregunta 1 -->
                <div class="faq-item">
                    <button class="faq-question">¿Cómo puedo añadir un evento al calendario?</button>
                    <div class="faq-answer">
                        <p>Para añadir una entrega, examen o evento a tu agenda personal, ve al <strong>Panel de control (inicio)</strong>. En el lateral derecho verás el "Calendario mensual". Haz clic directamente sobre el número del día que quieras programar. Se abrirá una ventana flotante (formulario) donde podrás ingresar el título, la hora exacta y el tipo de evento (personal, entrega o examen), y finalmente presiona "Guardar". El evento aparecerá inmediatamente en tu lista.</p>
                    </div>
                </div>

                <!-- Pregunta 2 -->
                <div class="faq-item">
                    <button class="faq-question">¿Qué hago si he olvidado mi contraseña?</button>
                    <div class="faq-answer">
                        <p>Por motivos de seguridad académica y al encontrarse la base de datos en un servidor interno local, no existe un botón de restablecimiento por correo. En caso de olvido, debes ponerte en contacto con el <strong>director del centro</strong> (administrador del sistema) para que pueda restablecer tu contraseña a la clave por defecto (<code>1234</code>) o asignarte una nueva manualmente.</p>
                    </div>
                </div>

                <!-- Pregunta 3 -->
                <div class="faq-item">
                    <button class="faq-question">¿Cómo puedo enviar un mensaje a un profesor?</button>
                    <div class="faq-answer">
                        <p>Haz clic en la opción <strong>Mensajería</strong> del menú lateral izquierdo. Selecciona "Redactar mensaje". El sistema cargará automáticamente la agenda con los contactos disponibles (profesores y resto de alumnos). Escribe el nombre de usuario exacto del destinatario en el campo correspondiente, añade un asunto, escribe tu consulta en el cuerpo del mensaje y pulsa "Enviar".</p>
                    </div>
                </div>

                <!-- Pregunta 4 -->
                <div class="faq-item">
                    <button class="faq-question">¿Dónde puedo ver mis calificaciones detalladas y la guía docente?</button>
                    <div class="faq-answer">
                        <p>En la sección de <strong>Asignaturas</strong> verás un listado de todas las materias en las que te has matriculado. Al hacer clic en el botón "Ver detalles" de cualquiera de ellas, accederás a una pantalla exclusiva con dos pestañas. En la pestaña "Guía docente" verás el contenido temático completo e inventado de forma profesional, y en la pestaña "Recursos" tendrás acceso a los enlaces a Classroom, Drive y apuntes que tu profesor haya compartido.</p>
                    </div>
                </div>

                <!-- Pregunta 5 -->
                <div class="faq-item">
                    <button class="faq-question">¿Por qué mi panel de control cambia de color?</button>
                    <div class="faq-answer">
                        <p>El portal académico cuenta con una tematización visual basada en roles que facilita la identificación de permisos: el color <strong>verde</strong> está asignado a los alumnos (coincidente con los colores de Joyfe), el <strong>azul</strong> a los profesores y el <strong>rojo</strong> a los administradores. Los botones, barras laterales y elementos de enfoque variarán su color dinámicamente de acuerdo a tu rol de inicio de sesión.</p>
                    </div>
                </div>

                <!-- Pregunta 6 -->
                <div class="faq-item">
                    <button class="faq-question">¿Cómo puedo silenciar ciertos avisos flotantes?</button>
                    <div class="faq-answer">
                        <p>Si deseas silenciar temporalmente los avisos flotantes de calificaciones, mensajería o faltas de asistencia, dirígete a la opción **Ajustes** en el menú de tu cuenta (esquina superior derecha). En la sección de "Preferencias de notificaciones", puedes activar o desactivar cada switch de forma independiente. Estos cambios se guardarán en tu navegador de forma persistente y silenciosa.</p>
                    </div>
                </div>

                <!-- Pregunta 7 -->
                <div class="faq-item">
                    <button class="faq-question">¿Por qué las asignaturas tienen profesores distintos ahora?</button>
                    <div class="faq-answer">
                        <p>Hemos actualizado la asignación de materias en la base de datos de Joyfe. Anteriormente, el tutor del curso aparecía como docente en todas las asignaturas por defecto. Ahora, las asignaturas se han distribuido equitativamente entre los diferentes profesores disponibles en la plataforma para reflejar un claustro docente realista y balanceado.</p>
                    </div>
                </div>

                <!-- Pregunta 8 -->
                <div class="faq-item">
                    <button class="faq-question">¿Cómo sé si tengo un nuevo anuncio del centro?</button>
                    <div class="faq-answer">
                        <p>El tablón de anuncios del panel de control se actualiza de forma automática. Cuando un docente o administrador publique un anuncio general o para tu curso, aparecerá un aviso flotante rojo en la esquina superior derecha indicando la publicación del nuevo anuncio para que no te pierdas ninguna circular del colegio.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    // JS interactivo para el acordeón de FAQs
    document.querySelectorAll('.faq-question').forEach(button => {
        button.addEventListener('click', () => {
            const item = button.parentElement;
            const isActive = item.classList.contains('active');
            
            // Cerrar el resto de acordeones activos
            document.querySelectorAll('.faq-item').forEach(otherItem => {
                otherItem.classList.remove('active');
                otherItem.querySelector('.faq-answer').style.maxHeight = null;
            });
            
            // Si no estaba activo, abrir el actual
            if (!isActive) {
                item.classList.add('active');
                const answer = item.querySelector('.faq-answer');
                answer.style.maxHeight = answer.scrollHeight + "px";
            }
        });
    });
</script>

</body>
</html>
