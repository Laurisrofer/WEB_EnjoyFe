from flask import Blueprint, request, jsonify
from flask_jwt_extended import create_access_token
from repositories.usuario_repo import UsuarioRepository
from werkzeug.security import check_password_hash

bp_auth = Blueprint('bp_auth', __name__)



@bp_auth.route('/login', methods=['POST'])
def login():
    datos = request.get_json()
    
    # Usamos EXACTAMENTE las claves que manda tu api.py
    usuario_req = datos.get('nombre_usuario') 
    contrasena_req = datos.get('contrasena')

    if not usuario_req or not contrasena_req:
        return jsonify(mensaje="Faltan credenciales"), 400

    # 1. Buscamos al usuario usando el REPOSITORIO SQL
    usuario_encontrado = UsuarioRepository.obtener_por_nombre_usuario(usuario_req)

    if not usuario_encontrado:
        return jsonify(mensaje="Usuario o contraseña incorrectos"), 401

    # 2. Comprobación de la contraseña
    hash_en_bd = usuario_encontrado.password_hash
    
    # Validamos usando el hashing scrypt de werkzeug
    contrasena_valida = check_password_hash(hash_en_bd, contrasena_req)
    


    if not contrasena_valida:
        return jsonify(mensaje="Usuario o contraseña incorrectos"), 401

    # 3. Generamos el Token JWT incluyendo el ROL en los claims
    token = create_access_token(
        identity=str(usuario_encontrado.id), 
        additional_claims={"rol": usuario_encontrado.rol} 
    )

    return jsonify(
        mensaje="Login exitoso",
        token=token,
        rol=usuario_encontrado.rol,
        usuario=usuario_encontrado.nombre_usuario
    ), 200