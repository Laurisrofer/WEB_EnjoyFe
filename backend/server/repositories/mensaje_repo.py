from db import db
from models.Mensaje import Mensaje

class MensajeRepository:
    
    @staticmethod
    def obtener_por_destinatario(id_usuario):
        """
        Devuelve todos los mensajes recibidos por un usuario, 
        ordenados del más reciente al más antiguo.
        """
        return Mensaje.query.filter_by(id_destinatario=id_usuario).order_by(Mensaje.fecha.desc()).all()

    @staticmethod
    def crear_mensaje(id_remitente, id_destinatario, asunto, cuerpo, adjunto=None):
        try:
            nuevo_mensaje = Mensaje(
                id_remitente=id_remitente, # type: ignore
                id_destinatario=id_destinatario, # type: ignore
                asunto=asunto, # type: ignore
                mensaje=cuerpo, # type: ignore 
                adjunto=adjunto # type: ignore
            )
            db.session.add(nuevo_mensaje)
            db.session.commit()
            return nuevo_mensaje
        except Exception as e:
            db.session.rollback()
            print(f"Error al guardar el mensaje: {e}")
            return None

    @staticmethod
    def eliminar_mensaje(id_mensaje, id_usuario):
        try:
            mensaje = Mensaje.query.get(id_mensaje)
            if not mensaje:
                return False, "Mensaje no encontrado"
            
            # Solo el destinatario o remitente puede eliminarlo (según lógica),
            # aquí permitimos al destinatario borrar de su bandeja
            if str(mensaje.id_destinatario) != str(id_usuario) and str(mensaje.id_remitente) != str(id_usuario):
                return False, "No tienes permiso para eliminar este mensaje"
                
            db.session.delete(mensaje)
            db.session.commit()
            return True, "Mensaje eliminado correctamente"
        except Exception as e:
            db.session.rollback()
            print(f"Error al eliminar el mensaje: {e}")
            return False, "Error interno al eliminar el mensaje"