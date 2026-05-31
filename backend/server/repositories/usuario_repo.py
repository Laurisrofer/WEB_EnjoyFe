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