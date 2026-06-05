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

bp_notas = Blueprint('bp_notas', __name__)

@bp_notas.route('/mis-notas', methods=['GET'])
@jwt_required()
def obtener_mis_notas():
    from models.Matricula import Matricula
    from models.Calificacion import Calificacion
    from models.Usuario import Usuario
    from models.Curso import Curso

    usuario_id = get_jwt_identity()
    mis_matriculas = Matricula.query.filter_by(id_alumno=usuario_id).all()
    
    curso_nombre = "Sin curso asignado"
    if mis_matriculas:
        id_curso = mis_matriculas[0].asignatura.id_curso if (mis_matriculas[0].asignatura) else None
        if id_curso:
            curso = Curso.query.get(id_curso)
            if curso:
                curso_nombre = curso.nombre

    resultado = []
    for m in mis_matriculas:
        profesor_nombre = "Sin asignar"
        if m.asignatura and m.asignatura.id_profesor:
            profesor = Usuario.query.get(m.asignatura.id_profesor)
            if profesor:
                profesor_nombre = profesor.nombre_completo

        califs = Calificacion.query.filter_by(id_matricula=m.id).all()
        lista_califs = []
        for c in califs:
            lista_califs.append({
                "id": c.id,
                "actividad": c.nombre_actividad,
                "nota": float(c.nota) if c.nota is not None else None,
                "comentario": c.comentario or "",
                "fecha": c.fecha_calificacion.strftime('%d/%m/%Y')
            })

        resultado.append({
            "id_matricula": m.id,
            "asignatura": m.asignatura.nombre if m.asignatura else "Desconocida",
            "profesor": profesor_nombre,
            "nota_final": float(m.nota_final) if m.nota_final is not None else None,
            "observaciones_globales": m.observaciones_globales or "",
            "calificaciones": lista_califs
        })

    return jsonify({
        "curso": curso_nombre,
        "asignaturas": resultado
    }), 200


@bp_notas.route('/notas-profesores', methods=['GET'])
@jwt_required()
def obtener_notas_profesores():
    from models.Curso import Curso
    from models.Asignatura import Asignatura
    from models.Matricula import Matricula
    from models.Usuario import Usuario

    usuario_id = get_jwt_identity()
    usuario = UsuarioRepository.obtener_por_id(usuario_id)
    
    if not usuario:
        return jsonify(mensaje="Usuario no encontrado"), 404

    rol = usuario.rol
    cursos_dict = {}

    if rol == 'admin':
        asignaturas = Asignatura.query.all()
    elif rol == 'profesor':
        asignaturas = Asignatura.query.filter_by(id_profesor=usuario_id).all()
    else:
        return jsonify(mensaje="No autorizado"), 403

    for asig in asignaturas:
        curso = asig.curso
        if not curso:
            continue
            
        if curso.id not in cursos_dict:
            cursos_dict[curso.id] = {
                "id": curso.id,
                "nombre": curso.nombre,
                "asignaturas": []
            }

        matriculas = Matricula.query.filter_by(id_asignatura=asig.id).all()
        alumnos_lista = []
        for m in matriculas:
            from models.Calificacion import Calificacion
            califs = Calificacion.query.filter_by(id_matricula=m.id).all()
            lista_califs = [{
                "id": c.id,
                "actividad": c.nombre_actividad,
                "nota": float(c.nota) if c.nota is not None else None,
                "comentario": c.comentario or "",
                "fecha": c.fecha_calificacion.strftime('%d/%m/%Y')
            } for c in califs]

            alumnos_lista.append({
                "id_matricula": m.id,
                "id_alumno": m.id_alumno,
                "nombre": m.alumno.nombre_completo if m.alumno else "Desconocido",
                "nota_final": float(m.nota_final) if m.nota_final is not None else None,
                "observaciones_globales": m.observaciones_globales or "",
                "calificaciones": lista_califs
            })

        cursos_dict[curso.id]["asignaturas"].append({
            "id": asig.id,
            "nombre": asig.nombre,
            "alumnos": alumnos_lista
        })

    return jsonify(list(cursos_dict.values())), 200


@bp_notas.route('/guardar-calificacion', methods=['POST'])
@jwt_required()
def guardar_calificacion():
    from db import db
    from models.Calificacion import Calificacion
    from datetime import datetime

    datos = request.get_json()
    if not datos or not datos.get('id_matricula') or not datos.get('nombre_actividad'):
        return jsonify(mensaje="Datos insuficientes"), 400

    id_calif = datos.get('id_calificacion')
    id_matricula = datos.get('id_matricula')
    nombre_actividad = datos.get('nombre_actividad')
    nota = datos.get('nota')
    comentario = datos.get('comentario', '')
    
    fecha_str = datos.get('fecha')
    if fecha_str:
        try:
            fecha_val = datetime.strptime(fecha_str, '%Y-%m-%d').date()
        except ValueError:
            fecha_val = datetime.utcnow().date()
    else:
        fecha_val = datetime.utcnow().date()

    if id_calif:
        calif = Calificacion.query.get(id_calif)
        if not calif:
            return jsonify(mensaje="Calificación no encontrada"), 404
        calif.nombre_actividad = nombre_actividad
        calif.nota = nota
        calif.comentario = comentario
        calif.fecha_calificacion = fecha_val
    else:
        calif = Calificacion(
            id_matricula=id_matricula,
            nombre_actividad=nombre_actividad,
            nota=nota,
            comentario=comentario,
            fecha_calificacion=fecha_val
        )
        db.session.add(calif)

    try:
        db.session.commit()
        return jsonify(mensaje="Calificación guardada correctamente", id=calif.id), 200
    except Exception as e:
        db.session.rollback()
        return jsonify(mensaje=f"Error al guardar: {str(e)}"), 500


@bp_notas.route('/guardar-nota-final', methods=['POST'])
@jwt_required()
def guardar_nota_final():
    from db import db
    from models.Matricula import Matricula

    datos = request.get_json()
    if not datos or not datos.get('id_matricula'):
        return jsonify(mensaje="Datos insuficientes"), 400

    id_matricula = datos.get('id_matricula')
    nota_final = datos.get('nota_final')
    observaciones = datos.get('observaciones_globales', '')

    matricula = Matricula.query.get(id_matricula)
    if not matricula:
        return jsonify(mensaje="Matrícula no encontrada"), 404

    matricula.nota_final = nota_final
    matricula.observaciones_globales = observaciones

    try:
        db.session.commit()
        return jsonify(mensaje="Nota final y observaciones guardadas correctamente"), 200
    except Exception as e:
        db.session.rollback()
        return jsonify(mensaje=f"Error al guardar: {str(e)}"), 500


@bp_notas.route('/calificacion/<int:id_calificacion>', methods=['DELETE'])
@jwt_required()
def eliminar_calificacion(id_calificacion):
    '''
    Elimina una calificación específica si pertenece a una asignatura del profesor.
    '''
    usuario_id = get_jwt_identity()
    
    # Verificar que la calificación exista
    calif = Calificacion.query.get(id_calificacion)
    if not calif:
        return jsonify(mensaje="Calificación no encontrada"), 404
        
    # Verificar que el profesor imparta la asignatura de esta calificación
    matricula = Matricula.query.get(calif.id_matricula)
    if not matricula:
        return jsonify(mensaje="Error de integridad: Matrícula no encontrada"), 500
        
    asignatura = Asignatura.query.filter_by(id=matricula.id_asignatura, id_profesor=usuario_id).first()
    
    # Si no la imparte, comprobamos si es admin
    if not asignatura:
        usuario = Usuario.query.get(usuario_id)
        if not usuario or usuario.rol != 'admin':
            return jsonify(mensaje="No tienes permiso para eliminar esta calificación"), 403
            
    db.session.delete(calif)
    db.session.commit()
    
    return jsonify(mensaje="Calificación eliminada correctamente"), 200


