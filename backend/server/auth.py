# auth.py
from functools import wraps
from flask import jsonify
from flask_jwt_extended import verify_jwt_in_request, get_jwt, get_jwt_identity

def requerir_rol(rol_necesario):
    """
    Decorador personalizado para proteger rutas según el rol.
    Uso: @rol_required('admin')
    """
    def wrapper(fn):
        @wraps(fn)
        def decorator(*args, **kwargs):
            # 1. Verificar que el token es válido
            verify_jwt_in_request()
            
            # 2. Obtener los datos del token (claims)
            claims = get_jwt()
            
            # 3. Verificar si el usuario tiene el rol adecuado
            # Si el rol necesario es 'admin', solo entra admin.
            # Si es 'profesor', pueden entrar admin y profesor (jerarquía básica).
            # Si es 'alumno', pueden entrar todos (o ajustamos según lógica).
            
            mi_rol = claims.get("rol")
            
            if rol_necesario == 'admin' and mi_rol != 'admin':
                return jsonify(msg="Acceso denegado: Se requiere administrador"), 403
            
            if rol_necesario == 'profesor' and mi_rol not in ['admin', 'profesor']:
                return jsonify(msg="Acceso denegado: Se requiere ser profesor o admin"), 403

            # Si pasa el filtro, ejecutamos la función original
            return fn(*args, **kwargs)
        return decorator
    return wrapper

def obtener_id_usuario_jwt():
    """
    Función auxiliar para obtener el ID (entero) del usuario desde el token.
    """
    verify_jwt_in_request()
    identidad = get_jwt_identity()
    return int(identidad) if identidad else None