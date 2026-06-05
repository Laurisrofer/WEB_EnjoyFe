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

bp_horarios = Blueprint('bp_horarios', __name__)

@bp_horarios.route('/mi-horario', methods=['GET'])
@jwt_required()
def obtener_mi_horario():
    from models.Horario import Horario
    from models.Matricula import Matricula
    from models.Asignatura import Asignatura
    from models.Curso import Curso
    from models.Usuario import Usuario

    usuario_id = get_jwt_identity()
    usuario = UsuarioRepository.obtener_por_id(usuario_id)

    if not usuario:
        return jsonify(mensaje="Usuario no encontrado"), 404

    rol = usuario.rol
    id_curso_param = request.args.get('id_curso', type=int)

    horarios = []
    lista_cursos = []
    curso_nombre = "Horario Escolar"

    # Caso 1: Admin o consulta con parámetro de curso general
    if rol == 'admin':
        cursos = Curso.query.all()
        lista_cursos = [{"id": c.id, "nombre": c.nombre} for c in cursos]
        
        id_curso = id_curso_param
        if not id_curso and cursos:
            id_curso = cursos[0].id
            
        if id_curso:
            curso = Curso.query.get(id_curso)
            if curso:
                curso_nombre = curso.nombre
            horarios = Horario.query.filter_by(id_curso=id_curso).all()

    # Caso 2: Alumno
    elif rol == 'alumno':
        mis_matriculas = Matricula.query.filter_by(id_alumno=usuario_id).all()
        if mis_matriculas:
            id_curso = mis_matriculas[0].asignatura.id_curso
            curso = Curso.query.get(id_curso)
            if curso:
                curso_nombre = curso.nombre
            horarios = Horario.query.filter_by(id_curso=id_curso).all()
        else:
            return jsonify(mensaje="Alumno no matriculado en ningún curso"), 400

    # Caso 3: Profesor
    elif rol == 'profesor':
        # Obtener los cursos donde imparte
        cursos_imparte = db.session.query(Curso).join(Asignatura).filter(Asignatura.id_profesor == usuario_id).distinct().all()
        lista_cursos = [{"id": c.id, "nombre": c.nombre} for c in cursos_imparte]
        
        # Añadir opción "Todos mis cursos" (id vacío)
        lista_cursos.insert(0, {"id": "", "nombre": "Todos mis cursos"})
        
        if id_curso_param:
            curso = Curso.query.get(id_curso_param)
            curso_nombre = curso.nombre if curso else "Mi Horario"
            mis_asignaturas = Asignatura.query.filter_by(id_profesor=usuario_id, id_curso=id_curso_param).all()
        else:
            curso_nombre = "Todos mis cursos"
            mis_asignaturas = Asignatura.query.filter_by(id_profesor=usuario_id).all()
            
        ids_asignaturas = [a.id for a in mis_asignaturas]
        if ids_asignaturas:
            horarios = Horario.query.filter(Horario.id_asignatura.in_(ids_asignaturas)).all()

    # Formatear la respuesta
    resultado = []
    for h in horarios:
        profesor_nombre = "Sin asignar"
        if h.asignatura.id_profesor:
            profesor = Usuario.query.get(h.asignatura.id_profesor)
            if profesor:
                profesor_nombre = profesor.nombre_completo

        resultado.append({
            "id": h.id,
            "asignatura": h.asignatura.nombre,
            "profesor": profesor_nombre,
            "curso": h.asignatura.curso.nombre,
            "dia_semana": h.dia_semana,  # 1 a 5
            "hora_inicio": h.hora_inicio.strftime('%H:%M') if h.hora_inicio else "00:00",
            "hora_fin": h.hora_fin.strftime('%H:%M') if h.hora_fin else "00:00",
            "color": h.asignatura.color if hasattr(h.asignatura, 'color') else "#3498db",
            "id_asignatura": h.asignatura.id
        })

    # Obtener el tutor del curso (si hay un curso específico)
    tutor_nombre = "Sin tutor asignado"
    if 'curso' in locals() and curso and curso.id_tutor:
        tutor = Usuario.query.get(curso.id_tutor)
        if tutor:
            tutor_nombre = tutor.nombre_completo

    return jsonify({
        "rol": rol,
        "curso_nombre": curso_nombre,
        "tutor_nombre": tutor_nombre,
        "horarios": resultado,
        "cursos_disponibles": lista_cursos
    }), 200


