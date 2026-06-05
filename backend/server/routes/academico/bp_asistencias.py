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

bp_asistencias = Blueprint('bp_asistencias', __name__)

@bp_asistencias.route('/mis-asistencias', methods=['GET'])
@jwt_required()
def obtener_mis_asistencias():
    from models.Asistencia import Asistencia
    from models.Matricula import Matricula
    from models.Curso import Curso

    usuario_id = get_jwt_identity()

    # Listar faltas y retrasos
    faltas = Asistencia.query.filter_by(id_alumno=usuario_id).order_by(Asistencia.fecha.desc(), Asistencia.hora.desc()).all()

    # Calcular estadísticas
    totales_faltas = Asistencia.query.filter_by(id_alumno=usuario_id, tipo='falta').count()
    totales_retrasos = Asistencia.query.filter_by(id_alumno=usuario_id, tipo='retraso').count()
    justificadas = Asistencia.query.filter_by(id_alumno=usuario_id, justificada=True).count()
    
    # Pendientes de aprobación: justificante_texto no nulo pero justificada=False
    pendientes = Asistencia.query.filter(
        Asistencia.id_alumno == usuario_id,
        Asistencia.justificada == False,
        Asistencia.justificante_texto != None,
        Asistencia.justificante_texto != ''
    ).count()

    injustificadas = totales_faltas + totales_retrasos - justificadas - pendientes

    lista_faltas = [f.to_dict() for f in faltas]

    # Curso del alumno
    mis_matriculas = Matricula.query.filter_by(id_alumno=usuario_id).all()
    curso_nombre = "Sin curso asignado"
    if mis_matriculas:
        id_curso = mis_matriculas[0].asignatura.id_curso if mis_matriculas[0].asignatura else None
        if id_curso:
            curso = Curso.query.get(id_curso)
            if curso:
                curso_nombre = curso.nombre

    return jsonify({
        "curso": curso_nombre,
        "total_faltas": totales_faltas,
        "total_retrasos": totales_retrasos,
        "justificadas": justificadas,
        "pendientes": pendientes,
        "injustificadas": injustificadas,
        "asistencias": lista_faltas
    }), 200


@bp_asistencias.route('/solicitar-justificacion', methods=['POST'])
@jwt_required()
def solicitar_justificacion():
    from db import db
    from models.Asistencia import Asistencia

    usuario_id = get_jwt_identity()
    datos = request.get_json()

    if not datos or not datos.get('id_asistencia'):
        return jsonify(mensaje="Datos insuficientes"), 400

    id_asistencia = datos.get('id_asistencia')
    justificante_texto = datos.get('justificante_texto', '')

    asistencia = Asistencia.query.filter_by(id=id_asistencia, id_alumno=usuario_id).first()
    if not asistencia:
        return jsonify(mensaje="Falta/retraso no encontrado"), 404

    if justificante_texto == '':
        asistencia.justificante_texto = None
    else:
        asistencia.justificante_texto = justificante_texto
        
    asistencia.justificada = False  # Queda en estado "pendiente" o sin justificar

    try:
        db.session.commit()
        return jsonify(mensaje="Justificación enviada correctamente"), 200
    except Exception as e:
        db.session.rollback()
        return jsonify(mensaje=f"Error al enviar la justificación: {str(e)}"), 500


@bp_asistencias.route('/asistencia-curso', methods=['GET'])
@jwt_required()
def obtener_asistencia_curso():
    from models.Horario import Horario
    from models.Matricula import Matricula
    from models.Asistencia import Asistencia
    from datetime import datetime

    id_curso = request.args.get('id_curso', type=int)
    id_asignatura = request.args.get('id_asignatura', type=int)
    fecha_str = request.args.get('fecha')  # YYYY-MM-DD
    hora_str = request.args.get('hora')   # HH:MM

    if not id_curso or not id_asignatura or not fecha_str:
        return jsonify(mensaje="Parámetros inválidos"), 400

    try:
        fecha_val = datetime.strptime(fecha_str, '%Y-%m-%d').date()
    except ValueError:
        return jsonify(mensaje="Formato de fecha inválido"), 400

    # Obtener el día de la semana (1 = Lunes, 5 = Viernes)
    # Python isocalendar: Monday=1, ..., Sunday=7
    dia_semana = fecha_val.isocalendar()[2]
    
    # Obtener sesiones programadas desde horarios
    sesiones = Horario.query.filter_by(id_curso=id_curso, id_asignatura=id_asignatura, dia_semana=dia_semana).all()
    lista_sesiones = [{
        "id": s.id,
        "hora_inicio": s.hora_inicio.strftime('%H:%M'),
        "hora_fin": s.hora_fin.strftime('%H:%M')
    } for s in sesiones]

    # Obtener alumnos matriculados en la asignatura
    matriculas = Matricula.query.filter_by(id_asignatura=id_asignatura).all()
    
    # Obtener registros de asistencia guardados en esa fecha y hora
    asistencias_grabadas = {}
    if hora_str:
        try:
            if len(hora_str) == 5:
                hora_val = datetime.strptime(hora_str, '%H:%M').time()
            else:
                hora_val = datetime.strptime(hora_str, '%H:%M:%S').time()
                
            records = Asistencia.query.filter_by(
                id_asignatura=id_asignatura,
                fecha=fecha_val,
                hora=hora_val
            ).all()
            for r in records:
                asistencias_grabadas[r.id_alumno] = {
                    "id_asistencia": r.id,
                    "tipo": r.tipo,
                    "justificada": bool(r.justificada),
                    "justificante_texto": r.justificante_texto or "",
                    "observaciones": r.observaciones or ""
                }
        except ValueError:
            pass

    alumnos_lista = []
    for m in matriculas:
        reg = asistencias_grabadas.get(m.id_alumno, {
            "id_asistencia": None,
            "tipo": "asistencia",  # Por defecto presente
            "justificada": False,
            "justificante_texto": "",
            "observaciones": ""
        })
        alumnos_lista.append({
            "id_alumno": m.id_alumno,
            "nombre": m.alumno.nombre_completo if m.alumno else "Desconocido",
            "asistencia": reg
        })

    return jsonify({
        "sesiones_programadas": lista_sesiones,
        "alumnos": alumnos_lista
    }), 200


@bp_asistencias.route('/guardar-asistencias', methods=['POST'])
@jwt_required()
def guardar_asistencias():
    from db import db
    from models.Asistencia import Asistencia
    from datetime import datetime

    datos = request.get_json()
    if not datos or not datos.get('id_asignatura') or not datos.get('fecha') or not datos.get('hora'):
        return jsonify(mensaje="Datos insuficientes"), 400

    id_asignatura = datos.get('id_asignatura')
    fecha_str = datos.get('fecha')
    hora_str = datos.get('hora')

    try:
        fecha_val = datetime.strptime(fecha_str, '%Y-%m-%d').date()
        if len(hora_str) == 5:
            hora_val = datetime.strptime(hora_str, '%H:%M').time()
        else:
            hora_val = datetime.strptime(hora_str, '%H:%M:%S').time()
    except ValueError:
        return jsonify(mensaje="Formatos de fecha/hora inválidos"), 400

    asistencias_lista = datos.get('asistencias', [])

    try:
        for item in asistencias_lista:
            id_alumno = item.get('id_alumno')
            tipo = item.get('tipo')  # 'asistencia' (presente), 'falta', 'retraso'
            justificada = item.get('justificada', False)
            observaciones = item.get('observaciones', '')

            # Comprobar si ya existe un registro de falta/retraso para esa sesión
            registro = Asistencia.query.filter_by(
                id_alumno=id_alumno,
                id_asignatura=id_asignatura,
                fecha=fecha_val,
                hora=hora_val
            ).first()

            if tipo == 'asistencia':
                # Si está presente, eliminamos cualquier registro de falta/retraso existente
                if registro:
                    db.session.delete(registro)
            else:
                # Si es falta o retraso, creamos o actualizamos
                if registro:
                    registro.tipo = tipo
                    registro.justificada = justificada
                    registro.observaciones = observaciones
                else:
                    nuevo_reg = Asistencia(
                        id_alumno=id_alumno,
                        id_asignatura=id_asignatura,
                        fecha=fecha_val,
                        hora=hora_val,
                        tipo=tipo,
                        justificada=justificada,
                        observaciones=observaciones
                    )
                    db.session.add(nuevo_reg)

        db.session.commit()
        return jsonify(mensaje="Asistencia guardada correctamente"), 200
    except Exception as e:
        db.session.rollback()
        return jsonify(mensaje=f"Error al guardar la asistencia: {str(e)}"), 500


@bp_asistencias.route('/justificaciones-pendientes', methods=['GET'])
@jwt_required()
def obtener_justificaciones_pendientes():
    from models.Asistencia import Asistencia
    from models.Asignatura import Asignatura
    from models.Usuario import Usuario

    usuario_id = get_jwt_identity()
    usuario = Usuario.query.get(usuario_id)
    if not usuario:
        return jsonify(mensaje="Usuario no encontrado"), 404

    rol = usuario.rol
    
    if rol == 'admin':
        query = Asistencia.query
    elif rol == 'profesor':
        mis_asig_ids = [a.id for a in Asignatura.query.filter_by(id_profesor=usuario_id).all()]
        query = Asistencia.query.filter(Asistencia.id_asignatura.in_(mis_asig_ids))
    else:
        return jsonify(mensaje="No autorizado"), 403

    # Buscar justificada=False pero justificante_texto relleno
    pendientes = query.filter(
        Asistencia.justificada == False,
        Asistencia.justificante_texto != None,
        Asistencia.justificante_texto != ''
    ).order_by(Asistencia.fecha.desc()).all()

    resultado = []
    for p in pendientes:
        resultado.append({
            "id": p.id,
            "id_alumno": p.id_alumno,
            "alumno": p.alumno.nombre_completo if p.alumno else "Desconocido",
            "id_asignatura": p.id_asignatura,
            "asignatura": p.asignatura.nombre if p.asignatura else "Desconocida",
            "fecha": p.fecha.strftime('%d/%m/%Y'),
            "hora": p.hora.strftime('%H:%M') if p.hora else "08:00",
            "tipo": p.tipo,
            "justificante": p.justificante_texto
        })

    return jsonify(resultado), 200


@bp_asistencias.route('/resolver-justificacion', methods=['POST'])
@jwt_required()
def resolver_justificacion():
    from db import db
    from models.Asistencia import Asistencia

    datos = request.get_json()
    if not datos or not datos.get('id_asistencia') or not datos.get('resolucion'):
        return jsonify(mensaje="Datos insuficientes"), 400

    id_asistencia = datos.get('id_asistencia')
    resolucion = datos.get('resolucion')  # 'aprobar' o 'rechazar'

    asistencia = Asistencia.query.get(id_asistencia)
    if not asistencia:
        return jsonify(mensaje="Falta/retraso no encontrado"), 404

    if resolucion == 'aprobar':
        asistencia.justificada = True
    elif resolucion == 'rechazar':
        asistencia.justificada = False
        asistencia.justificante_texto = ""  # Limpiamos justificante al rechazar

    try:
        db.session.commit()
        return jsonify(mensaje="Justificación procesada correctamente"), 200
    except Exception as e:
        db.session.rollback()
        return jsonify(mensaje=f"Error al guardar los cambios: {str(e)}"), 500


