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

bp_eventos = Blueprint('bp_eventos', __name__)

@bp_eventos.route('/crear-evento', methods=['POST'])
@jwt_required()
def api_crear_evento():
    usuario_id = get_jwt_identity()
    datos = request.get_json()

    if not datos or not datos.get('titulo') or not datos.get('fecha'):
        return jsonify(mensaje="Faltan datos obligatorios"), 400

    datos_evento = {
        'titulo': datos.get('titulo'),
        'fecha': datos.get('fecha'),
        'hora': datos.get('hora'), # Añadimos la hora aquí
        'tipo': datos.get('tipo', 'personal'),
        'id_usuario': usuario_id,
        'descripcion': datos.get('descripcion'),
        'id_curso': datos.get('id_curso')
    }

    exito = EventoRepository.crear_evento(datos_evento)
    
    if exito:
        return jsonify(mensaje="Evento creado correctamente"), 201
    else:
        return jsonify(mensaje="Error al guardar el evento en la base de datos"), 500


@bp_eventos.route('/borrar-evento/<int:id_evento>', methods=['DELETE'])
@jwt_required()
def api_borrar_evento(id_evento):
    usuario_id = get_jwt_identity()
    usuario = UsuarioRepository.obtener_por_id(usuario_id)
    if not usuario:
        return jsonify(mensaje="Usuario no encontrado"), 404
        
    if EventoRepository.borrar_evento(id_evento, usuario_id, usuario.rol):
        return jsonify(mensaje="Evento borrado"), 200
    return jsonify(mensaje="Error al borrar"), 404


@bp_eventos.route('/editar-evento/<int:id_evento>', methods=['PUT'])
@jwt_required()
def api_editar_evento(id_evento):
    usuario_id = get_jwt_identity()
    usuario = UsuarioRepository.obtener_por_id(usuario_id)
    if not usuario:
        return jsonify(mensaje="Usuario no encontrado"), 404
        
    datos = request.get_json()
    
    if EventoRepository.actualizar_evento(id_evento, datos, usuario_id, usuario.rol):
        return jsonify(mensaje="Evento actualizado correctamente"), 200
    
    return jsonify(mensaje="Error al actualizar el evento"), 404


@bp_eventos.route('/notificaciones-recientes', methods=['GET'])
@jwt_required()
def obtener_notificaciones_recientes():
    from models.Mensaje import Mensaje
    from models.Calificacion import Calificacion
    from models.Matricula import Matricula
    from datetime import datetime, timedelta

    usuario_id = get_jwt_identity()

    # 1. Mensajes no leídos
    mensajes_no_leidos = Mensaje.query.filter_by(id_destinatario=usuario_id, leido=False).all()
    lista_mensajes = []
    for m in mensajes_no_leidos:
        lista_mensajes.append({
            "id": m.id,
            "de": m.remitente.nombre_completo if m.remitente else "Desconocido",
            "asunto": m.asunto,
            "fecha": m.fecha.strftime('%d/%m/%Y %H:%M')
        })

    # 2. Calificaciones recientes (últimas 48 horas)
    mis_matriculas = Matricula.query.filter_by(id_alumno=usuario_id).all()
    ids_matriculas = [m.id for m in mis_matriculas]

    lista_calificaciones = []
    if ids_matriculas:
        limite_fecha = datetime.utcnow().date() - timedelta(days=2)
        calificaciones_recientes = Calificacion.query.filter(
            Calificacion.id_matricula.in_(ids_matriculas),
            Calificacion.fecha_calificacion >= limite_fecha
        ).all()
        for c in calificaciones_recientes:
            lista_calificaciones.append({
                "id": c.id,
                "asignatura": c.matricula.asignatura.nombre if (c.matricula and c.matricula.asignatura) else "Desconocida",
                "actividad": c.nombre_actividad,
                "nota": float(c.nota) if c.nota is not None else 0.0,
                "fecha": c.fecha_calificacion.strftime('%d/%m/%Y')
            })

    # 3. Asistencias recientes (últimas 48 horas)
    from models.Asistencia import Asistencia
    lista_asistencias = []
    limite_fecha = datetime.utcnow().date() - timedelta(days=2)
    asistencias_recientes = Asistencia.query.filter(
        Asistencia.id_alumno == usuario_id,
        Asistencia.fecha >= limite_fecha,
        Asistencia.tipo != 'asistencia'
    ).all()
    for a in asistencias_recientes:
        asig = Asignatura.query.get(a.id_asignatura)
        lista_asistencias.append({
            "id": a.id,
            "asignatura": asig.nombre if asig else "Desconocida",
            "tipo": a.tipo,
            "fecha": a.fecha.strftime('%d/%m/%Y'),
            "justificada": bool(a.justificada)
        })

    # 4. Anuncios/Noticias recientes (últimas 48 horas)
    from models.Evento import Evento
    lista_anuncios = []
    id_curso = None
    if ids_matriculas:
        first_mat = Matricula.query.filter_by(id_alumno=usuario_id).first()
        id_curso = first_mat.asignatura.id_curso if (first_mat and first_mat.asignatura) else None

    query_anuncios = Evento.query.filter(
        Evento.tipo == 'anuncio',
        Evento.fecha >= limite_fecha,
        Evento.id_usuario != usuario_id
    )
    if id_curso:
        query_anuncios = query_anuncios.filter((Evento.id_curso == id_curso) | (Evento.id_curso.is_(None)))

    anuncios_recientes = query_anuncios.all()
    for a in anuncios_recientes:
        lista_anuncios.append({
            "id": a.id,
            "titulo": a.titulo,
            "fecha": a.fecha.strftime('%d/%m/%Y'),
            "descripcion": a.descripcion or ""
        })

    return jsonify({
        "mensajes": lista_mensajes,
        "calificaciones": lista_calificaciones,
        "asistencias": lista_asistencias,
        "anuncios": lista_anuncios,
        "total_alertas": len(lista_mensajes) + len(lista_calificaciones) + len(lista_asistencias) + len(lista_anuncios)
    }), 200


