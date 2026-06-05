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

bp_dashboard = Blueprint('bp_dashboard', __name__)

@bp_dashboard.route('/dashboard-info', methods=['GET'])
@jwt_required()
def obtener_info_dashboard():
    usuario_id = get_jwt_identity()
    usuario = UsuarioRepository.obtener_por_id(usuario_id)
    
    if not usuario:
        return jsonify(mensaje="Usuario no encontrado"), 404

    asignaturas = MatriculaRepository.obtener_asignaturas_por_usuario(usuario_id)
    
    curso_nombre = "Sin curso asignado"
    tutor_nombre = "Sin tutor asignado"
    id_curso_principal = None

    if asignaturas:
        id_curso_principal = asignaturas[0].id_curso
        curso = Curso.query.get(id_curso_principal)
        if curso:
            curso_nombre = curso.nombre
            if hasattr(curso, 'id_tutor') and curso.id_tutor:
                tutor = UsuarioRepository.obtener_por_id(curso.id_tutor)
                tutor_nombre = tutor.nombre_completo if tutor else "Sin tutor asignado"

    # --- AQUÍ ESTÁ EL CINTURÓN DE SEGURIDAD ---
    eventos_raw = EventoRepository.obtener_eventos_dashboard(usuario_id, id_curso_principal, usuario.rol)
    if eventos_raw is None:
        eventos_raw = []
    # ------------------------------------------
    
    eventos_formateados = []
    for e in eventos_raw:
        eventos_formateados.append({
            "id": e.id,
            "titulo": e.titulo,
            "fecha": e.fecha.strftime('%d/%m/%Y') if e.fecha else "",
            "hora": str(e.hora) if e.hora else "00:00",
            "tipo": e.tipo,
            "descripcion": e.descripcion or "",
            "es_propietario": str(e.id_usuario) == str(usuario_id),
            "id_curso": e.id_curso
        })

    return jsonify({
        "nombre": usuario.nombre_completo,
        "rol": usuario.rol,
        "curso": curso_nombre,
        "tutor": tutor_nombre,
        "eventos": eventos_formateados
    }), 200


@bp_dashboard.route('/admin-stats', methods=['GET'])
@jwt_required()
@requerir_rol('admin')
def obtener_estadisticas_admin():
    from models.Usuario import Usuario
    from models.Curso import Curso
    from models.Asistencia import Asistencia
    from sqlalchemy import func
    
    total_profesores = Usuario.query.filter_by(rol='profesor').count()
    total_alumnos = Usuario.query.filter_by(rol='alumno').count()
    total_cursos = Curso.query.count()
    
    # Asistencia por curso (calculando el porcentaje de "presente")
    asistencia_por_curso = []
    alumnos_por_curso = []
    cursos = Curso.query.all()
    for c in cursos:
        # Alumnos por curso (contando ids de alumnos distintos matriculados en asignaturas del curso)
        from models.Asignatura import Asignatura
        from models.Matricula import Matricula
        num_alumnos = db.session.query(func.count(func.distinct(Matricula.id_alumno))).\
            join(Asignatura, Matricula.id_asignatura == Asignatura.id).\
            filter(Asignatura.id_curso == c.id).scalar() or 0
            
        alumnos_por_curso.append({
            "curso": c.nombre,
            "cantidad": num_alumnos
        })
        
        # En Enjoyfe se estiman 40 sesiones por curso para cálculos de asistencia global
        faltas = db.session.query(func.count(Asistencia.id)).\
            join(Asignatura, Asistencia.id_asignatura == Asignatura.id).\
            filter(Asignatura.id_curso == c.id, Asistencia.tipo == 'falta').scalar() or 0
            
        max_clases = num_alumnos * 40
        asist_pct = max(0, ((max_clases - faltas) / max_clases) * 100) if max_clases > 0 else 0
        porcentaje = round(asist_pct, 1)
        asistencia_por_curso.append({
            "curso": c.nombre,
            "porcentaje": porcentaje
        })
        
    return jsonify({
        "total_profesores": total_profesores,
        "total_alumnos": total_alumnos,
        "total_cursos": total_cursos,
        "alumnos_por_curso": alumnos_por_curso,
        "asistencia_por_curso": asistencia_por_curso
    }), 200


@bp_dashboard.route('/estadisticas-profesor', methods=['GET'])
@jwt_required()
def obtener_estadisticas_profesor():
    '''
    Calcula % de aprobados y asistencia para las asignaturas de un profesor.
    '''
    usuario_id = get_jwt_identity()
    usuario = Usuario.query.get(usuario_id)
    
    if not usuario or usuario.rol != 'profesor':
        return jsonify(mensaje="Acceso denegado"), 403
        
    id_asignatura = request.args.get('id_asignatura')
    
    # Base query for subjects
    asig_query = Asignatura.query.filter_by(id_profesor=usuario_id)
    if id_asignatura:
        asig_query = asig_query.filter_by(id=id_asignatura)
        
    asignaturas = asig_query.all()
    if not asignaturas:
        return jsonify([]), 200
        
    resultados = []
    
    for asig in asignaturas:
        matriculas = Matricula.query.filter_by(id_asignatura=asig.id).all()
        total_alumnos = len(matriculas)
        aprobados = 0
        sum_notas = 0
        alumnos_con_nota = 0
        distribucion_notas = {"Suspenso": 0, "Suficiente": 0, "Bien": 0, "Notable": 0, "Sobresaliente": 0}
        
        for m in matriculas:
            # Calcular nota del alumno
            nota_alumno = None
            if m.nota_final is not None:
                nota_alumno = float(m.nota_final)
            else:
                califs_raw = Calificacion.query.filter_by(id_matricula=m.id).all()
                califs = [c for c in califs_raw if c.nota is not None]
                if califs:
                    nota_alumno = sum(float(c.nota) for c in califs) / len(califs)
            
            if nota_alumno is not None:
                sum_notas += nota_alumno
                alumnos_con_nota += 1
                if nota_alumno >= 5.0:
                    aprobados += 1
                
                # Para la campana de Gauss
                if nota_alumno < 5.0:
                    distribucion_notas["Suspenso"] += 1
                elif nota_alumno < 6.0:
                    distribucion_notas["Suficiente"] += 1
                elif nota_alumno < 7.0:
                    distribucion_notas["Bien"] += 1
                elif nota_alumno < 9.0:
                    distribucion_notas["Notable"] += 1
                else:
                    distribucion_notas["Sobresaliente"] += 1
                    
        porcentaje_aprobados = round((aprobados / total_alumnos * 100), 1) if total_alumnos > 0 else 0
        nota_media = round((sum_notas / alumnos_con_nota), 2) if alumnos_con_nota > 0 else 0
        
        # Calcular % asistencia media
        asistencias = Asistencia.query.filter_by(id_asignatura=asig.id).all()
        faltas = sum(1 for a in asistencias if a.tipo == 'falta')
        retrasos = sum(1 for a in asistencias if a.tipo == 'retraso')
        
        # En Enjoyfe se estiman 40 sesiones por curso para cálculos de asistencia global
        max_clases = total_alumnos * 40
        asist_pct = max(0, ((max_clases - faltas) / max_clases) * 100) if max_clases > 0 else 0
        
        curso = Curso.query.get(asig.id_curso)
        nombre_curso = curso.nombre if curso else "Sin curso"
        
        resultados.append({
            'id_asignatura': asig.id,
            'nombre_asignatura': asig.nombre,
            'id_curso': asig.id_curso,
            'nombre_curso': nombre_curso,
            'total_alumnos': total_alumnos,
            'aprobados': aprobados,
            'porcentaje_aprobados': round(porcentaje_aprobados, 1),
            'nota_media': round(nota_media, 2),
            'total_faltas_registradas': faltas,
            'total_retrasos_registrados': retrasos,
            'porcentaje_asistencia': round(asist_pct, 1),
            'distribucion_notas': distribucion_notas
        })
        
    return jsonify(resultados), 200


