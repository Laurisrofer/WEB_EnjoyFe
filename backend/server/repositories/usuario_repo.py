from models.Usuario import Usuario
from db import db
from werkzeug.security import generate_password_hash, check_password_hash

class UsuarioRepository:
    
    @staticmethod
    def obtener_por_id(id_usuario):
        return Usuario.query.get(id_usuario)

    @staticmethod
    def obtener_todos():
        return Usuario.query.all()

    @staticmethod
    def obtener_por_nombre_usuario(nombre):
        return Usuario.query.filter_by(nombre_usuario=nombre).first()

    @staticmethod
    def actualizar_contrasena(id_usuario, contrasena_actual, contrasena_nueva):
        usuario = Usuario.query.get(id_usuario)
        if not usuario:
            return False, "Usuario no encontrado"
        
        # 1. Seguridad Máxima: Verificamos si la contraseña actual introducida coincide con el hash de la DB
        if not check_password_hash(usuario.password_hash, contrasena_actual):
            return False, "La contraseña actual es incorrecta"
        
        # 2. Encriptación: Hasheamos la nueva contraseña antes de que toque la base de datos
        usuario.password_hash = generate_password_hash(contrasena_nueva)
        
        # Guardamos los cambios
        db.session.commit()
        return True, "Contraseña actualizada correctamente"

    @staticmethod
    def crear(usuario):
        try:
            db.session.add(usuario)
            db.session.commit()
            return True
        except Exception as e:
            db.session.rollback()
            print(f"Error al crear usuario: {e}")
            return False

    @staticmethod
    def actualizar(usuario):
        try:
            db.session.commit()
            return True
        except Exception as e:
            db.session.rollback()
            print(f"Error al actualizar usuario: {e}")
            return False

    @staticmethod
    def eliminar(id_usuario):
        try:
            from models.Matricula import Matricula
            from models.Asignatura import Asignatura
            usuario = Usuario.query.get(id_usuario)
            if not usuario:
                return False
                
            # Impedir eliminación si tiene asignaturas asignadas
            if usuario.rol == 'alumno':
                matriculas = Matricula.query.filter_by(id_alumno=id_usuario).count()
                if matriculas > 0:
                    return False # Tiene asignaturas asignadas
            elif usuario.rol == 'profesor':
                asignaturas = Asignatura.query.filter_by(id_profesor=id_usuario).count()
                if asignaturas > 0:
                    return False # Tiene asignaturas asignadas
            
            # Borrado en cascada manual para no violar integridad referencial
            from models.Mensaje import Mensaje
            from models.Evento import Evento
            from models.Asistencia import Asistencia
            
            # 1. Borrar todos los mensajes enviados o recibidos
            Mensaje.query.filter((Mensaje.id_remitente == id_usuario) | (Mensaje.id_destinatario == id_usuario)).delete()
            
            # 2. Borrar eventos creados por el usuario
            Evento.query.filter_by(id_usuario=id_usuario).delete()
            
            # 3. Borrar asistencias asociadas si es alumno
            if usuario.rol == 'alumno':
                Asistencia.query.filter_by(id_alumno=id_usuario).delete()
                    
            db.session.delete(usuario)
            db.session.commit()
            return True
        except Exception as e:
            db.session.rollback()
            print(f"Error al eliminar usuario: {e}")
            return False