from flask import Blueprint, jsonify, request
from flask_jwt_extended import get_jwt_identity
from repositories.mensaje_repo import MensajeRepository
from repositories.usuario_repo import UsuarioRepository
from auth import requerir_rol

bp_mensajes = Blueprint('bp_mensajes', __name__)

# --- 1. VER BANDEJA DE ENTRADA ---
@bp_mensajes.route('', methods=['GET'])
@requerir_rol('cualquiera')
def obtener_mensajes():
    # Obtenemos el ID del usuario logueado desde el token
    mi_id = get_jwt_identity()
    
    # Buscamos sus mensajes a través del repositorio
    mensajes = MensajeRepository.obtener_por_destinatario(mi_id)
    
    # El método to_dict() del modelo Mensaje ya formatea los datos como espera el cliente
    resultado = [m.to_dict() for m in mensajes]
    return jsonify(resultado), 200

# --- 2. ENVIAR UN MENSAJE ---
@bp_mensajes.route('', methods=['POST'])
@requerir_rol('cualquiera')
def enviar_mensaje():
    mi_id = get_jwt_identity()
    datos = request.get_json()

    destinatario_username = datos.get('destinatario_usuario')
    asunto = datos.get('asunto', 'Sin asunto')
    cuerpo = datos.get('cuerpo', '')
    adjunto = datos.get('adjunto', None)

    if not destinatario_username or not cuerpo:
        return jsonify(mensaje="Faltan datos obligatorios"), 400

    # 1. Buscamos el ID del destinatario a partir de su nombre de usuario
    destinatario_obj = UsuarioRepository.obtener_por_nombre_usuario(destinatario_username)
    if not destinatario_obj:
        return jsonify(mensaje=f"El usuario '{destinatario_username}' no existe"), 404

    # 2. Guardamos el mensaje en BD
    nuevo_msg = MensajeRepository.crear_mensaje(
        id_remitente=mi_id,
        id_destinatario=destinatario_obj.id,
        asunto=asunto,
        cuerpo=cuerpo,
        adjunto=adjunto
    )
    
    if nuevo_msg:
        return jsonify(mensaje="Mensaje enviado correctamente"), 201
    else:
        return jsonify(mensaje="Error interno al enviar el mensaje"), 500

# --- 3. ELIMINAR UN MENSAJE ---
@bp_mensajes.route('/<int:id_mensaje>', methods=['DELETE'])
@requerir_rol('cualquiera')
def eliminar_mensaje_ruta(id_mensaje):
    mi_id = get_jwt_identity()
    exito, mensaje = MensajeRepository.eliminar_mensaje(id_mensaje, mi_id)
    
    if exito:
        return jsonify(mensaje=mensaje), 200
    else:
        return jsonify(mensaje=mensaje), 400