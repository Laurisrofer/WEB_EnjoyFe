from flask import Blueprint, jsonify, request
from models import Usuario # Importamos el modelo
from repositories.usuario_repo import UsuarioRepository # Importamos el repositorio
from auth import requerir_rol
from werkzeug.security import generate_password_hash
from datetime import datetime

bp_usuarios = Blueprint('bp_usuarios', __name__)

# 1. LISTAR (Usando Repositorio)
@bp_usuarios.route('', methods=['GET'])
@requerir_rol('admin')
def obtener_usuarios():
    # LLAMADA AL REPOSITORIO
    usuarios_orm = UsuarioRepository.obtener_todos()
    
    # Convertimos a JSON usando el método helper que creamos en el modelo
    lista = [u.to_dict() for u in usuarios_orm]
    return jsonify(lista), 200

# 1 bis. Cualquier usuario puede obtener esta lista de contactos para el envío de mensajes
@bp_usuarios.route('/contactos', methods=['GET'])
@requerir_rol('cualquiera')
def obtener_contactos():
    usuarios_orm = UsuarioRepository.obtener_todos()
    lista_contactos = []
    for u in usuarios_orm:
        lista_contactos.append({
            "nombre_usuario": u.nombre_usuario,   
            "nombre_completo": u.nombre_completo, 
            "rol": u.rol
        })
    return jsonify(lista_contactos), 200

# 2. CREAR (Usando Repositorio)
@bp_usuarios.route('', methods=['POST'])
@requerir_rol('admin')
def crear_usuario():
    datos = request.get_json()
    # Validar campos obligatorios
    if not datos.get('nombre_usuario') or not datos.get('contrasena') or not datos.get('nombre_completo'):
        return jsonify(mensaje="Faltan campos obligatorios"), 400

    # Validar rol
    rol = datos.get('rol', 'alumno')
    if rol not in ['admin', 'profesor', 'alumno']:
        return jsonify(mensaje="Rol inválido"), 400
        
    # Validación con repositorio
    if UsuarioRepository.obtener_por_nombre_usuario(datos['nombre_usuario']):
        return jsonify(mensaje="El usuario ya existe"), 400

    # Creamos objeto Modelo (NO diccionario)
    nuevo = Usuario(
        nombre_usuario=datos['nombre_usuario'], # type: ignore
        password_hash=generate_password_hash(datos['contrasena']), # type: ignore
        nombre_completo=datos['nombre_completo'], # type: ignore
        email=datos.get('email', ''), # type: ignore
        rol=rol, # type: ignore
        dni=datos.get('dni') # type: ignore
    )
    
    # Guardamos con repositorio
    UsuarioRepository.crear(nuevo)
    return jsonify(mensaje="Usuario creado", id=nuevo.id), 201

# 3. MODIFICAR USUARIO (PUT)
@bp_usuarios.route('/<int:id_usuario>', methods=['PUT'])
@requerir_rol('admin')
def modificar_usuario(id_usuario):
    datos = request.get_json()
    
    # 1. Buscamos el usuario en la base de datos
    usuario = UsuarioRepository.obtener_por_id(id_usuario)
    if not usuario:
        return jsonify(mensaje="Usuario no encontrado"), 404

    # 2. Actualizamos solo los campos que vengan en la petición
    if 'nombre_usuario' in datos:
        usuario.nombre_usuario = datos['nombre_usuario']
    
    if 'nombre_completo' in datos:
        usuario.nombre_completo = datos['nombre_completo']
        
    if 'email' in datos:
        usuario.email = datos['email']
        
    if 'rol' in datos:
        usuario.rol = datos['rol']
        
    if 'dni' in datos:
        usuario.dni = datos['dni']
        
    # Si viene contraseña nueva y no está vacía, la encriptamos
    if 'contrasena' in datos and datos['contrasena'].strip() != "":
        usuario.password_hash = encriptar(datos['contrasena'])

    # 3. Guardamos los cambios
    if UsuarioRepository.actualizar(usuario):
        return jsonify(mensaje="Usuario modificado correctamente"), 200
    else:
        return jsonify(mensaje="Error al modificar el usuario"), 500

# 4. ELIMINAR USUARIO (DELETE)
@bp_usuarios.route('/<int:id_usuario>', methods=['DELETE'])
@requerir_rol('admin')
def borrar_usuario(id_usuario):
    exito = UsuarioRepository.eliminar(id_usuario)
    
    if exito:
        return jsonify(mensaje="Usuario eliminado correctamente"), 200
    else:
        return jsonify(mensaje="No se pudo eliminar el usuario. Es posible que tenga asignaturas o cursos asignados, o que no exista."), 400