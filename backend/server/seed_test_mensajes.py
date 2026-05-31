from application import app
from db import db
from models.Mensaje import Mensaje
from repositories.usuario_repo import UsuarioRepository
from repositories.mensaje_repo import MensajeRepository

with app.app_context():
    # Obtener algunos usuarios
    from models.Usuario import Usuario
    usuarios = Usuario.query.all()
    if len(usuarios) >= 2:
        u1 = usuarios[0]
        u2 = usuarios[1]
        
        m1 = MensajeRepository.crear_mensaje(
            id_remitente=u2.id,
            id_destinatario=u1.id,
            asunto="Bienvenida al sistema",
            cuerpo="Hola, te escribo este mensaje de prueba para comprobar que la bandeja de entrada funciona correctamente. ¡Un saludo!",
            adjunto=None
        )
        
        m2 = MensajeRepository.crear_mensaje(
            id_remitente=u2.id,
            id_destinatario=u1.id,
            asunto="Aviso importante: TFG",
            cuerpo="Recuerda que mañana tenemos que revisar las vistas de mensajería para asegurarnos de que todo va bien. Te adjunto el PDF del temario.",
            adjunto="uploads/123456789_temario.pdf"
        )
        
        print("Mensajes de prueba creados con éxito.")
    else:
        print("No hay suficientes usuarios en la base de datos para crear mensajes.")
