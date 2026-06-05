from flask import Blueprint, jsonify, request
from flask_jwt_extended import jwt_required, get_jwt_identity
from auth import requerir_rol
from repositories.usuario_repo import UsuarioRepository
from repositories.matricula_repo import MatriculaRepository
from repositories.evento_repo import EventoRepository
from models.Asignatura import Asignatura
from models.Curso import Curso
from models.Usuario import Usuario
from models.Matricula import Matricula
from models.Calificacion import Calificacion
from models.Asistencia import Asistencia
from db import db
import os
import json

bp_perfil = Blueprint('bp_perfil', __name__)

@bp_perfil.route('/perfil', methods=['GET'])
@jwt_required()
def obtener_perfil():
    usuario_id = get_jwt_identity()
    usuario = UsuarioRepository.obtener_por_id(usuario_id)
    
    if not usuario:
        return jsonify(mensaje="Usuario no encontrado"), 404

    # Extraemos el curso igual que en el dashboard
    asignaturas = MatriculaRepository.obtener_asignaturas_por_usuario(usuario_id)
    curso_nombre = "Sin curso asignado"
    if asignaturas:
        curso = Curso.query.get(asignaturas[0].id_curso)
        if curso:
            curso_nombre = curso.nombre

    tutoria_curso = Curso.query.filter_by(id_tutor=usuario.id).first() if usuario.rol == 'profesor' else None

    return jsonify({
        "nombre": usuario.nombre_completo,
        "rol": usuario.rol,
        "curso": curso_nombre,
        "dni": usuario.dni if hasattr(usuario, 'dni') and usuario.dni else "No especificado",
        "email": usuario.email,
        "tutoria": tutoria_curso.nombre if tutoria_curso else "Ninguna"
    }), 200


@bp_perfil.route('/perfil', methods=['PUT'])
@jwt_required()
def actualizar_perfil():
    usuario_id = get_jwt_identity()
    datos = request.get_json()

    contrasena_actual = datos.get('contrasena_actual')
    contrasena_nueva = datos.get('contrasena_nueva')
    contrasena_repetida = datos.get('contrasena_repetida')

    # Validaciones básicas antes de tocar la base de datos
    if not contrasena_actual or not contrasena_nueva or not contrasena_repetida:
        return jsonify(mensaje="Todos los campos son obligatorios"), 400

    if contrasena_nueva != contrasena_repetida:
        return jsonify(mensaje="Las contraseñas nuevas no coinciden"), 400

    # Llamada al repositorio seguro
    exito, mensaje = UsuarioRepository.actualizar_contrasena(usuario_id, contrasena_actual, contrasena_nueva)
    
    if exito:
        return jsonify(mensaje=mensaje), 200
    else:
        return jsonify(mensaje=mensaje), 400


