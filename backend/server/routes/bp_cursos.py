from flask import Blueprint, jsonify, request
from repositories.curso_repo import CursoRepository
from auth import requerir_rol

bp_cursos = Blueprint('bp_cursos', __name__)

# --- 1. LISTAR CURSOS (Público o Autenticado) ---
@bp_cursos.route('', methods=['GET'])
def listar_cursos():
    # Llamamos al repositorio SQL
    cursos = CursoRepository.obtener_todos()
    # Convertimos los objetos Python a JSON con to_dict()
    resultado = [c.to_dict() for c in cursos]
    return jsonify(resultado), 200

# --- 2. CREAR CURSO (Solo Admin) ---
@bp_cursos.route('', methods=['POST'])
@requerir_rol('admin')
def crear_curso():
    datos = request.get_json()
    
    # Validación simple
    if not datos or 'nombre' not in datos:
        return jsonify(mensaje="Faltan datos obligatorios"), 400

    nuevo = CursoRepository.crear_completo(datos)
    
    if nuevo:
        return jsonify(mensaje="Curso creado correctamente", id=nuevo.id), 201
    else:
        return jsonify(mensaje="Error al crear el curso (quizás el nombre ya existe)"), 500

# --- 3. BORRAR CURSO (Solo Admin) ---
@bp_cursos.route('/<int:id_curso>', methods=['DELETE']) # OJO: <int:id_curso>
@requerir_rol('admin')
def borrar_curso(id_curso):
    # En SQL el ID es un entero (int), ya no usamos ObjectId
    exito = CursoRepository.eliminar(id_curso)
    if exito:
        return jsonify(mensaje="Curso eliminado"), 200
    else:
        return jsonify(mensaje="Curso no encontrado"), 404
    
    # --- 4. MODIFICAR CURSO (Solo Admin) ---
@bp_cursos.route('/<int:id_curso>', methods=['PUT'])
@requerir_rol('admin')
def modificar_curso(id_curso):
    datos = request.get_json()
    
    # Delegamos toda la lógica compleja de actualización anidada al repositorio
    if CursoRepository.actualizar_completo(id_curso, datos):
        return jsonify(mensaje="Curso modificado correctamente"), 200
    else:
        return jsonify(mensaje="Error al modificar el curso"), 500