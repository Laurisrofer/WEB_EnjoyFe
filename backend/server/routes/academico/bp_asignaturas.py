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

bp_asignaturas = Blueprint('bp_asignaturas', __name__)

@bp_asignaturas.route('/mis-asignaturas', methods=['GET'])
@jwt_required()
def obtener_mis_asignaturas():
    usuario_id = get_jwt_identity()
    usuario = Usuario.query.get(usuario_id)
    
    if usuario and usuario.rol == 'profesor':
        asignaturas = Asignatura.query.filter_by(id_profesor=usuario_id).all()
    elif usuario and usuario.rol == 'admin':
        asignaturas = Asignatura.query.all()
    else:
        asignaturas = MatriculaRepository.obtener_asignaturas_por_usuario(usuario_id)
    
    
    lista_asignaturas = []
    for asig in asignaturas:
        profesor = None
        if asig.id_profesor:
            profesor = UsuarioRepository.obtener_por_id(asig.id_profesor)
            
        nombre_profe = profesor.nombre_completo if profesor else "Profesor sin asignar"
        
        lista_asignaturas.append({
            "id": asig.id,
            "nombre": asig.nombre,
            "id_curso": asig.id_curso,
            "profesor": nombre_profe 
        })
        
    return jsonify(lista_asignaturas), 200


def generar_guia_docente(nombre_asignatura):
    unidades = []
    evaluacion = (
        "<ul>"
        "<li><strong>60%</strong> Exámenes teóricos y prácticos escritos.</li>"
        "<li><strong>30%</strong> Proyectos prácticos de laboratorio/programación.</li>"
        "<li><strong>10%</strong> Asistencia activa y entrega de tareas.</li>"
        "</ul>"
    )
    
    nombre_lower = nombre_asignatura.lower()
    
    if "sistemas" in nombre_lower:
        unidades = [
            "Introducción a los sistemas informáticos y arquitectura física.",
            "Instalación de sistemas operativos libres y propietarios.",
            "Gestión del almacenamiento, particionamiento y sistemas de archivos.",
            "Administración avanzada de usuarios, permisos y automatización (scripts).",
            "Configuración básica de red local y servicios básicos."
        ]
        descripcion = "Esta asignatura introduce al estudiante en el funcionamiento del hardware, la instalación y administración de sistemas operativos y la gestión de recursos de red locales."
    elif "bases de datos" in nombre_lower or "datos" in nombre_lower:
        unidades = [
            "Modelado de datos: el modelo Entidad-Relación y diseño de esquemas.",
            "Paso al modelo relacional y normalización de bases de datos.",
            "Lenguaje SQL (DDL): creación y modificación de bases de datos.",
            "Lenguaje SQL (DML): consultas complejas, agrupamientos, joins y subconsultas.",
            "Programación en bases de datos: procedimientos, triggers y transacciones."
        ]
        descripcion = "Asignatura centrada en el diseño conceptual, lógico y físico de bases de datos, con especial énfasis en el lenguaje SQL y la integridad de los datos."
    elif "programaci" in nombre_lower:
        unidades = [
            "Fundamentos de la programación: variables, tipos, operadores y condicionales.",
            "Bucles y estructuras de control iterativo estructurado.",
            "Programación orientada a objetos (POO): herencia, encapsulamiento y polimorfismo.",
            "Manejo de excepciones, depuración y manipulación de archivos físicos.",
            "Estructuras de datos complejas, listas dinámicas y colecciones."
        ]
        descripcion = "Fundamentos del desarrollo de software utilizando el paradigma orientado a objetos. Se capacita al alumno para escribir código eficiente, limpio y mantenible."
    elif "lenguajes de marcas" in nombre_lower or "marcas" in nombre_lower:
        unidades = [
            "Estructura de la información digital: XML y JSON.",
            "Marcado web semántico con HTML5.",
            "Estilización de portales con CSS3 (Flexbox y Grid CSS).",
            "Validación y definición de esquemas XML (XSD).",
            "Conversión de datos estructurados (XPath/XSLT)."
        ]
        descripcion = "Tratamiento de la información en formato digital mediante lenguajes de marcas, diseño web responsive y almacenamiento de datos estructurados no relacionales."
    elif "entornos de desarrollo" in nombre_lower or "desarrollo" in nombre_lower:
        unidades = [
            "Ciclo de vida del software e IDEs de programación modernos.",
            "Uso y configuración del sistema de control de versiones Git.",
            "Metodologías de prueba: pruebas unitarias (JUnit) y TDD.",
            "Depuración de programas y refactorización de código legado.",
            "Introducción al modelado UML y patrones de diseño."
        ]
        descripcion = "Aprenderás a optimizar el flujo de trabajo del desarrollador mediante control de versiones, refactorización de código, depuración y pruebas unitarias de software."
    elif "ingl" in nombre_lower:
        unidades = [
            "Terminología técnica informática, glosario y siglas comunes.",
            "Redacción de correos formales y documentación de software.",
            "Habilidades orales para reuniones internacionales.",
            "Simulación de entrevistas de trabajo en inglés.",
            "Comprensión de especificaciones técnicas oficiales."
        ]
        descripcion = "Perfeccionamiento de la competencia lingüística en inglés orientada al ámbito profesional tecnológico y de soporte técnico internacional."
    elif "itinerario" in nombre_lower or "empleabilidad" in nombre_lower:
        unidades = [
            "Autoconocimiento, mercado laboral tecnológico y búsqueda de empleo.",
            "Proceso de selección y diseño del Curriculum Vitae y LinkedIn.",
            "El contrato de trabajo, tipos de nóminas y seguridad social.",
            "Prevención de riesgos laborales en el sector de la ofimática.",
            "Fomento del emprendimiento: plan de negocio paso a paso."
        ]
        descripcion = "Orientación laboral para facilitar la incorporación del alumno al mercado laboral técnico de desarrollo o administración de sistemas."
    elif "digitaliza" in nombre_lower:
        unidades = [
            "La cuarta revolución industrial e Industria 4.0.",
            "Tecnologías clave: Inteligencia Artificial, Big Data e IoT.",
            "Cloud Computing y almacenamiento moderno.",
            "Seguridad de datos e infraestructura digital.",
            "Metodologías ágiles en procesos de modernización."
        ]
        descripcion = "Visión general de las tecnologías disruptivas y su aplicación en la modernización de los sectores productivos contemporáneos."
    elif "sostenibilidad" in nombre_lower:
        unidades = [
            "El desarrollo sostenible y los Objetivos de Desarrollo Sostenible (ODS).",
            "Economía circular y reducción de huella ecológica informática.",
            "Consumo energético eficiente de servidores y centros de datos (Green IT).",
            "Gestión sostenible de residuos electrónicos (e-waste).",
            "Políticas ecológicas en empresas de software."
        ]
        descripcion = "Fomento del desarrollo sostenible, la economía verde y las mejores prácticas ecológicas en el ámbito de la informática de sistemas."
    else:
        unidades = [
            "Introducción general y bases teóricas del temario.",
            "Análisis de requerimientos y casos prácticos de estudio.",
            "Implementación técnica y configuración de la tecnología.",
            "Pruebas de correcto funcionamiento y despliegue del proyecto.",
            "Tendencias de la industria y mantenimiento a largo plazo."
        ]
        descripcion = f"Guía docente orientada a la capacitación técnica e integral del alumno en la materia de '{nombre_asignatura}'."

    unidades_html = "".join([f"<li>{u}</li>" for u in unidades])
    
    html = (
        f"<h4>Descripción de la asignatura</h4>"
        f"<p>{descripcion}</p>"
        f"<h4>Bloques temáticos</h4>"
        f"<ul>{unidades_html}</ul>"
        f"<h4>Criterios de evaluación</h4>"
        f"{evaluacion}"
    )
    return html


@bp_asignaturas.route('/asignatura', methods=['POST'])
@jwt_required()
@requerir_rol('admin')
def crear_asignatura():
    datos = request.get_json()
    if not datos or 'nombre' not in datos or 'id_curso' not in datos:
        return jsonify(mensaje="Faltan datos obligatorios"), 400
        
    nueva = Asignatura(
        nombre=datos['nombre'],
        id_curso=datos['id_curso'],
        id_profesor=datos.get('id_profesor'),
        guia_docente=datos.get('guia_docente'),
        recursos_json=datos.get('recursos_json'),
        color=datos.get('color', '#3498db')
    )
    db.session.add(nueva)
    db.session.flush() # Para obtener el ID

    from models.Horario import Horario
    if 'horarios' in datos and isinstance(datos['horarios'], list):
        for h in datos['horarios']:
            nuevo_h = Horario(
                id_curso=nueva.id_curso,
                id_asignatura=nueva.id,
                dia_semana=h.get('dia_semana', 1),
                hora_inicio=h.get('hora_inicio', '00:00'),
                hora_fin=h.get('hora_fin', '00:00')
            )
            db.session.add(nuevo_h)

    db.session.commit()
    return jsonify(mensaje="Asignatura creada", id=nueva.id), 201


@bp_asignaturas.route('/asignatura/<int:id_asignatura>', methods=['PUT'])
@jwt_required()
@requerir_rol('admin')
def modificar_asignatura(id_asignatura):
    datos = request.get_json()
    asig = Asignatura.query.get(id_asignatura)
    if not asig: return jsonify(mensaje="No encontrada"), 404
    
    if 'nombre' in datos: asig.nombre = datos['nombre']
    if 'id_curso' in datos: asig.id_curso = datos['id_curso']
    if 'id_profesor' in datos: asig.id_profesor = datos['id_profesor']
    if 'guia_docente' in datos: asig.guia_docente = datos['guia_docente']
    if 'recursos_json' in datos: asig.recursos_json = datos['recursos_json']
    if 'color' in datos: asig.color = datos['color']
    
    from models.Horario import Horario
    if 'horarios' in datos and isinstance(datos['horarios'], list):
        # Borramos los horarios actuales
        Horario.query.filter_by(id_asignatura=asig.id).delete()
        # Insertamos los nuevos
        for h in datos['horarios']:
            nuevo_h = Horario(
                id_curso=asig.id_curso,
                id_asignatura=asig.id,
                dia_semana=h.get('dia_semana', 1),
                hora_inicio=h.get('hora_inicio', '00:00'),
                hora_fin=h.get('hora_fin', '00:00')
            )
            db.session.add(nuevo_h)
    
    db.session.commit()
    return jsonify(mensaje="Asignatura modificada"), 200


@bp_asignaturas.route('/asignatura/<int:id_asignatura>', methods=['DELETE'])
@jwt_required()
@requerir_rol('admin')
def borrar_asignatura(id_asignatura):
    asig = Asignatura.query.get(id_asignatura)
    if not asig: return jsonify(mensaje="No encontrada"), 404
    
    from models.Horario import Horario
    from models.Matricula import Matricula
    
    Horario.query.filter_by(id_asignatura=id_asignatura).delete()
    Matricula.query.filter_by(id_asignatura=id_asignatura).delete()
    
    db.session.delete(asig)
    db.session.commit()
    return jsonify(mensaje="Asignatura borrada"), 200


@bp_asignaturas.route('/asignatura/<int:id_asignatura>', methods=['GET'])
@jwt_required()
def obtener_detalle_asignatura(id_asignatura):
    asig = Asignatura.query.get(id_asignatura)
    
    if not asig:
        return jsonify(mensaje="Asignatura no encontrada"), 404
        
    profesor = None
    if asig.id_profesor:
        profesor = UsuarioRepository.obtener_por_id(asig.id_profesor)
    nombre_profe = profesor.nombre_completo if profesor else "Profesor sin asignar"

    if hasattr(asig, 'guia_docente') and asig.guia_docente:
        guia_docente = asig.guia_docente
    else:
        guia_docente = generar_guia_docente(asig.nombre)

    import json
    recursos_json = []
    if hasattr(asig, 'recursos_json') and asig.recursos_json:
        try:
            recursos_json = json.loads(asig.recursos_json)
        except Exception:
            pass

    if not recursos_json:
        recursos_json = [
            {"titulo": "Acceso a Classroom", "url": "https://classroom.google.com"},
            {"titulo": "Carpeta de Apuntes (Google Drive)", "url": "https://drive.google.com"}
        ]

    return jsonify({
        "id": asig.id,
        "nombre": asig.nombre,
        "profesor": nombre_profe,
        "guia_docente": guia_docente,
        "recursos": recursos_json,
        "enlaces_interes": []
    }), 200


@bp_asignaturas.route('/guia-docente/<int:id_asignatura>', methods=['PUT'])
@jwt_required()
def actualizar_guia_docente(id_asignatura):
    '''
    Actualiza la guía docente (descripción) de una asignatura.
    '''
    usuario_id = get_jwt_identity()
    datos = request.get_json()
    
    if not datos or 'guia_docente' not in datos:
        return jsonify(mensaje="Faltan datos obligatorios"), 400
        
    # Verificar permisos (ser el profesor de la asignatura o admin)
    usuario = Usuario.query.get(usuario_id)
    asignatura = Asignatura.query.get(id_asignatura)
    
    if not asignatura:
        return jsonify(mensaje="Asignatura no encontrada"), 404
        
    if asignatura.id_profesor != usuario_id and usuario.rol != 'admin':
        return jsonify(mensaje="No tienes permiso para editar esta guía docente"), 403
        
    # Guardamos en la base de datos (requiere añadir el campo guia_docente a Asignatura)
    # Si el modelo Asignatura no tiene guia_docente, por ahora lo simulamos
    if hasattr(asignatura, 'guia_docente'):
        asignatura.guia_docente = datos['guia_docente']
        db.session.commit()
        return jsonify(mensaje="Guía docente actualizada"), 200
    else:
        return jsonify(mensaje="Error: El campo guia_docente no existe en la base de datos"), 501


@bp_asignaturas.route('/recursos-asignatura/<int:id_asignatura>', methods=['PUT'])
@jwt_required()
def actualizar_recursos(id_asignatura):
    import json
    usuario_id = get_jwt_identity()
    datos = request.get_json()
    
    if not datos or 'recursos' not in datos:
        return jsonify(mensaje="Faltan datos obligatorios"), 400
        
    usuario = Usuario.query.get(usuario_id)
    asignatura = Asignatura.query.get(id_asignatura)
    
    if not asignatura:
        return jsonify(mensaje="Asignatura no encontrada"), 404
        
    if asignatura.id_profesor != usuario_id and usuario.rol != 'admin':
        return jsonify(mensaje="No tienes permiso para editar recursos"), 403
        
    if hasattr(asignatura, 'recursos_json'):
        asignatura.recursos_json = json.dumps(datos['recursos'])
        db.session.commit()
        return jsonify(mensaje="Recursos actualizados"), 200
    else:
        return jsonify(mensaje="Error: El campo recursos_json no existe"), 501


