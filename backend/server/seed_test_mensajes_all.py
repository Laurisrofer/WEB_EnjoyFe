from application import app
from db import db
from models.Usuario import Usuario
from repositories.mensaje_repo import MensajeRepository

with app.app_context():
    usuarios = Usuario.query.all()
    # Asumimos que el primer usuario de la lista puede hacer de "Remitente del Sistema"
    remitente = usuarios[0] if usuarios else None
    
    if remitente:
        count = 0
        for u in usuarios:
            m = MensajeRepository.crear_mensaje(
                id_remitente=remitente.id,
                id_destinatario=u.id,
                asunto="¡Bienvenido a Enjoyfe!",
                cuerpo="Hola " + u.nombre_completo + ", te damos la bienvenida al sistema de mensajería del portal. Aquí podrás comunicarte con profesores y alumnos. ¡Un saludo!",
                adjunto=None
            )
            count += 1
        print(f"Mensaje de bienvenida enviado a {count} usuarios.")
    else:
        print("No hay usuarios en la base de datos.")
