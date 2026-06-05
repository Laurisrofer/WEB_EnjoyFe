import ast
import re

with open('backend/server/routes/bp_academico.py', 'r', encoding='utf-8') as f:
    source_lines = f.readlines()

with open('backend/server/routes/bp_academico.py', 'r', encoding='utf-8') as f:
    source = f.read()

tree = ast.parse(source)

groups = {
    'bp_dashboard': ['obtener_info_dashboard', 'obtener_estadisticas_admin', 'obtener_estadisticas_profesor'],
    'bp_asignaturas': ['obtener_mis_asignaturas', 'generar_guia_docente', 'crear_asignatura', 'modificar_asignatura', 'borrar_asignatura', 'obtener_detalle_asignatura', 'actualizar_guia_docente', 'actualizar_recursos'],
    'bp_eventos': ['api_crear_evento', 'api_borrar_evento', 'api_editar_evento', 'obtener_notificaciones_recientes'],
    'bp_perfil': ['obtener_perfil', 'actualizar_perfil'],
    'bp_notas': ['obtener_mis_notas', 'obtener_notas_profesores', 'guardar_calificacion', 'guardar_nota_final', 'eliminar_calificacion'],
    'bp_asistencias': ['obtener_mis_asistencias', 'solicitar_justificacion', 'obtener_asistencia_curso', 'guardar_asistencias', 'obtener_justificaciones_pendientes', 'resolver_justificacion'],
    'bp_horarios': ['obtener_mi_horario'],
    'bp_aulas': ['obtener_mis_cursos', 'obtener_mis_alumnos']
}

header_imports = """from flask import Blueprint, jsonify, request
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
"""

for bp_name, functions in groups.items():
    bp_code = header_imports + f"\n{bp_name} = Blueprint('{bp_name}', __name__)\n\n"
    
    for node in tree.body:
        if isinstance(node, ast.FunctionDef) and node.name in functions:
            start_line = node.lineno
            if getattr(node, 'decorator_list', []):
                start_line = node.decorator_list[0].lineno
            end_line = getattr(node, 'end_lineno', node.lineno)
            
            # Python AST lineno is 1-indexed. Array is 0-indexed.
            func_source = "".join(source_lines[start_line-1:end_line])
            func_source = func_source.replace('@bp_academico.', f'@{bp_name}.')
            bp_code += func_source + "\n\n"
                
    with open(f'backend/server/routes/academico/{bp_name}.py', 'w', encoding='utf-8') as f:
        f.write(bp_code)

print("Split completed successfully with decorators.")
