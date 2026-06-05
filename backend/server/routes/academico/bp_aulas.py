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

bp_aulas = Blueprint('bp_aulas', __name__)

@bp_aulas.route('/mis-cursos', methods=['GET'])
@jwt_required()
def obtener_mis_cursos():
    '''
    Obtiene los cursos en los que el profesor imparte asignaturas o es tutor.
    '''
    usuario_id = get_jwt_identity()
    usuario = Usuario.query.get(usuario_id)
    
    if not usuario or usuario.rol != 'profesor':
        return jsonify(mensaje="Acceso denegado"), 403
        
    # Cursos donde imparte (tiene asignaturas)
    cursos_imparte = db.session.query(Curso).join(Asignatura).filter(Asignatura.id_profesor == usuario_id).distinct().all()
    
    # Cursos donde es tutor
    cursos_tutor = Curso.query.filter_by(id_tutor=usuario_id).all()
    
    # Combinar sin duplicados
    todos_cursos = {c.id: c for c in cursos_imparte}
    for c in cursos_tutor:
        todos_cursos[c.id] = c
        
    resultado = []
    for c in todos_cursos.values():
        c_dict = {
            'id': c.id,
            'nombre': c.nombre,
            'descripcion': c.descripcion,
            'es_tutor': c.id_tutor == usuario_id
        }
        resultado.append(c_dict)
        
    return jsonify(resultado), 200


@bp_aulas.route('/mis-alumnos', methods=['GET'])
@jwt_required()
def obtener_mis_alumnos():
    '''
    Obtiene los alumnos matriculados en un curso o asignatura específica.
    Param: id_curso (opcional), id_asignatura (opcional)
    '''
    usuario_id = get_jwt_identity()
    usuario = Usuario.query.get(usuario_id)
    
    if not usuario or usuario.rol != 'profesor':
        return jsonify(mensaje="Acceso denegado"), 403
        
    id_curso = request.args.get('id_curso')
    id_asignatura = request.args.get('id_asignatura')
    
    query = db.session.query(Usuario).join(Matricula, Usuario.id == Matricula.id_alumno)
    
    if id_asignatura:
        # Verificar que el profesor imparta esta asignatura
        asig = Asignatura.query.filter_by(id=id_asignatura, id_profesor=usuario_id).first()
        if not asig:
            return jsonify(mensaje="No impartes esta asignatura"), 403
        query = query.filter(Matricula.id_asignatura == id_asignatura)
    elif id_curso:
        # Alumnos matriculados en asignaturas de este curso
        query = query.join(Asignatura, Matricula.id_asignatura == Asignatura.id).filter(Asignatura.id_curso == id_curso)
    else:
        return jsonify(mensaje="Debes proporcionar id_curso o id_asignatura"), 400
        
    alumnos = query.filter(Usuario.rol == 'alumno', Usuario.estado == 'activo').distinct().order_by(Usuario.nombre_completo).all()
    
    resultado = []
    for alu in alumnos:
        resultado.append({
            'id': alu.id,
            'nombre_completo': alu.nombre_completo,
            'email': alu.email,
            'dni': alu.dni
        })
        
    return jsonify(resultado), 200


