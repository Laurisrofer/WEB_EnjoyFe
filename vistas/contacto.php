<?php
session_start();
if (!isset($_SESSION['nombre_usuario']) || !isset($_SESSION['token'])) {
    header("Location: index.php");
    exit();
}
$rol_usuario = $_SESSION['rol'];
$pagina_id = 'contacto';
$titulo_seccion = 'Contacto';
$estilos_adicionales = '';
include __DIR__ . '/../componentes/header.php';
?>
<div class="contenedor-datos" style="padding: 20px;">
    <div class="tarjeta" style="padding: 30px; border-radius: 12px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
        
        <div>
            <h2>Información de Contacto</h2>
            <p style="color: var(--text-muted); margin-bottom: 25px;">Si necesitas ayuda técnica o administrativa, no dudes en contactar con el soporte del Centro Educativo EnjoyFe.</p>
            
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                <div style="font-size: 24px;">📧</div>
                <div>
                    <div style="font-weight: bold; color: var(--text-muted); font-size: 0.9em;">Correo Electrónico</div>
                    <div style="font-size: 1.1em;">director@enjoyfe.com</div>
                </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                <div style="font-size: 24px;">📞</div>
                <div>
                    <div style="font-weight: bold; color: var(--text-muted); font-size: 0.9em;">Teléfono (Secretaría)</div>
                    <div style="font-size: 1.1em;">+34 91 123 45 67</div>
                </div>
            </div>
            
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                <div style="font-size: 24px;">📍</div>
                <div>
                    <div style="font-weight: bold; color: var(--text-muted); font-size: 0.9em;">Dirección</div>
                    <div style="font-size: 1.1em;">Madrid, España</div>
                </div>
            </div>
        </div>

        <div style="background-color: var(--bg-color); padding: 25px; border-radius: 12px; border: 1px solid var(--border-color);">
            <h3 style="margin-top: 0;">Envíanos un mensaje</h3>
            <div id="alertaContacto" style="display: none; padding: 15px; margin-bottom: 20px; border-radius: 6px; font-weight: bold;"></div>
            <form id="formContacto" onsubmit="enviarMensajeSoporte(event)">
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 0.9em; color: var(--text-muted);">Asunto</label>
                    <input type="text" id="asuntoContacto" required placeholder="Problema con mi cuenta..." style="width: 100%; padding: 10px; border: 1px solid var(--input-border); border-radius: 6px; background-color: var(--input-bg); color: var(--text-color);">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 0.9em; color: var(--text-muted);">Mensaje</label>
                    <textarea id="mensajeContacto" required rows="4" placeholder="Describe tu problema con detalle..." style="width: 100%; padding: 10px; border: 1px solid var(--input-border); border-radius: 6px; background-color: var(--input-bg); color: var(--text-color); resize: vertical;"></textarea>
                </div>
                <style>
                    .btn-contacto-submit {
                        width: 100%; 
                        background: var(--primary-color); 
                        color: white; 
                        padding: 12px 24px; 
                        border: none; 
                        border-radius: 6px; 
                        font-weight: bold; 
                        font-size: 1.05em; 
                        cursor: pointer; 
                        transition: all 0.2s; 
                        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        gap: 8px;
                    }
                    .btn-contacto-submit:hover {
                        background: var(--primary-hover);
                        transform: translateY(-2px);
                        box-shadow: 0 6px 10px rgba(0,0,0,0.15);
                    }
                </style>
                <button type="submit" id="btnContacto" class="btn-contacto-submit">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                    Enviar mensaje al Director
                </button>
            </form>
        </div>

    </div>
</div>

<script>
function enviarMensajeSoporte(e) {
    e.preventDefault();
    const btn = document.getElementById('btnContacto');
    const alerta = document.getElementById('alertaContacto');
    
    btn.disabled = true;
    btn.innerHTML = 'Enviando...';
    alerta.style.display = 'none';
    
    const formData = new FormData();
    formData.append('destinatario', 'director'); // Enviamos al admin
    formData.append('asunto', '[Soporte] ' + document.getElementById('asuntoContacto').value);
    formData.append('cuerpo', document.getElementById('mensajeContacto').value);
    
    fetch('acciones/gestion_mensajes.php?action=enviar', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) return response.json();
        return response.json().then(err => { throw new Error(err.mensaje || 'Error al enviar'); });
    })
    .then(data => {
        alerta.style.display = 'block';
        alerta.style.backgroundColor = 'rgba(46, 204, 113, 0.2)';
        alerta.style.color = 'var(--success-color)';
        alerta.style.border = '1px solid var(--success-color)';
        alerta.innerText = "¡Mensaje enviado! El director lo recibirá en su bandeja de entrada.";
        document.getElementById('formContacto').reset();
    })
    .catch(err => {
        alerta.style.display = 'block';
        alerta.style.backgroundColor = 'rgba(231, 76, 60, 0.2)';
        alerta.style.color = 'var(--danger-color)';
        alerta.style.border = '1px solid var(--danger-color)';
        alerta.innerText = "Error: " + err.message;
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg> Enviar mensaje al Director';
    });
}
</script>

<style>
    @media (max-width: 768px) {
        .tarjeta {
            grid-template-columns: 1fr !important;
            gap: 20px !important;
        }
    }
</style>

<?php include __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>
