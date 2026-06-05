import ast
import re

with open('backend/server/routes/bp_academico.py', 'r', encoding='utf-8') as f:
    source = f.read()

# Parse the file into an AST
tree = ast.parse(source)

# We want to group functions by their blueprint name.
# Here's the mapping of function names to blueprints:
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

# The header will contain all imports and the new blueprint declaration.
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
    
    # Extract source code for each function
    for node in tree.body:
        if isinstance(node, ast.FunctionDef) and node.name in functions:
            # Reconstruct the decorators
            for decorator in node.decorator_list:
                # We do a simple string replace for @bp_academico.route -> @bp_name.route
                if isinstance(decorator, ast.Call) and isinstance(decorator.func, ast.Attribute):
                    if isinstance(decorator.func.value, ast.Name) and decorator.func.value.id == 'bp_academico':
                        decorator.func.value.id = bp_name
            
            # The easiest way to get the exact source of a node (including comments and decorators) is to use ast.get_source_segment
            # For Python 3.8+ ast.get_source_segment(source, node) gets the exact text.
            func_source = ast.get_source_segment(source, node)
            if func_source:
                # Fix decorators if they still have bp_academico
                func_source = func_source.replace('@bp_academico.', f'@{bp_name}.')
                bp_code += func_source + "\n\n"
                
    with open(f'backend/server/routes/academico/{bp_name}.py', 'w', encoding='utf-8') as f:
        f.write(bp_code)

print("Split completed successfully.")
