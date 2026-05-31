from flask import Blueprint, jsonify, request
from flask_jwt_extended import jwt_required, get_jwt_identity
from repositories.usuario_repo import UsuarioRepository
from repositories.matricula_repo import MatriculaRepository
from repositories.evento_repo import EventoRepository
from models.Asignatura import Asignatura
from models.Curso import Curso

bp_academico = Blueprint('bp_academico', __name__)

@bp_academico.route('/dashboard-info', methods=['GET'])
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
    eventos_raw = EventoRepository.obtener_eventos_dashboard(usuario_id, id_curso_principal)
    if eventos_raw is None:
        eventos_raw = []
    # ------------------------------------------
    
    eventos_formateados = []
    for e in eventos_raw:
        eventos_formateados.append({
            "id": e.id,
            "titulo": e.titulo,
            "fecha": e.fecha.strftime('%d/%m/%Y'),
            "hora": str(e.hora) if e.hora else "00:00",
            "tipo": e.tipo,
            "descripcion": e.descripcion or ""
        })

    return jsonify({
        "nombre": usuario.nombre_completo,
        "rol": usuario.rol,
        "curso": curso_nombre,
        "tutor": tutor_nombre,
        "eventos": eventos_formateados
    }), 200

@bp_academico.route('/mis-asignaturas', methods=['GET'])
@jwt_required()
def obtener_mis_asignaturas():
    usuario_id = get_jwt_identity()
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

@bp_academico.route('/asignatura/<int:id_asignatura>', methods=['GET'])
@jwt_required()
def obtener_detalle_asignatura(id_asignatura):
    asig = Asignatura.query.get(id_asignatura)
    
    if not asig:
        return jsonify(mensaje="Asignatura no encontrada"), 404
        
    profesor = None
    if asig.id_profesor:
        profesor = UsuarioRepository.obtener_por_id(asig.id_profesor)
    nombre_profe = profesor.nombre_completo if profesor else "Profesor sin asignar"

    guia_docente = generar_guia_docente(asig.nombre)

    return jsonify({
        "id": asig.id,
        "nombre": asig.nombre,
        "profesor": nombre_profe,
        "guia_docente": guia_docente,
        "recursos": [
            {"titulo": "Acceso a Classroom", "url": "https://classroom.google.com"},
            {"titulo": "Carpeta de Apuntes (Google Drive)", "url": "https://drive.google.com"}
        ],
        "enlaces_interes": [
            {"titulo": "Material audiovisual de apoyo (YouTube)", "url": "https://www.youtube.com/watch?v=dQw4w9WgXcQ"}
        ]
    }), 200

# === ENDPOINTS DE EVENTOS ===
@bp_academico.route('/crear-evento', methods=['POST'])
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
        'id_usuario': usuario_id 
    }

    exito = EventoRepository.crear_evento(datos_evento)
    
    if exito:
        return jsonify(mensaje="Evento creado correctamente"), 201
    else:
        return jsonify(mensaje="Error al guardar el evento en la base de datos"), 500
    
@bp_academico.route('/borrar-evento/<int:id_evento>', methods=['DELETE'])
@jwt_required()
def api_borrar_evento(id_evento):
    usuario_id = get_jwt_identity()
    if EventoRepository.borrar_evento(id_evento, usuario_id):
        return jsonify(mensaje="Evento borrado"), 200
    return jsonify(mensaje="Error al borrar"), 404

@bp_academico.route('/editar-evento/<int:id_evento>', methods=['PUT'])
@jwt_required()
def api_editar_evento(id_evento):
    usuario_id = get_jwt_identity()
    datos = request.get_json()
    
    if EventoRepository.actualizar_evento(id_evento, datos, usuario_id):
        return jsonify(mensaje="Evento actualizado correctamente"), 200
    
    return jsonify(mensaje="Error al actualizar el evento"), 404

# === NUEVOS ENDPOINTS PARA EL PERFIL DE USUARIO ===

@bp_academico.route('/perfil', methods=['GET'])
@jwt_required()
def obtener_perfil():
    usuario_id = get_jwt_identity()
    usuario = UsuarioRepository.obtener_por_id(usuario_id)
    
    if not usuario:
        return jsonify(mensaje="Usuario no encontrado"), 404

    # Extraemos el curso igual que en el dashboard
    asignaturas = MatriculaRepository.obtener_asignaturas_por_usuario(usuario_id)
    curso_nombre = "Sin curso asignado"
    if asignaturas:
        curso = Curso.query.get(asignaturas[0].id_curso)
        if curso:
            curso_nombre = curso.nombre

    return jsonify({
        "nombre": usuario.nombre_completo,
        "rol": usuario.rol,
        "curso": curso_nombre,
        "dni": usuario.dni if hasattr(usuario, 'dni') and usuario.dni else "No especificado",
        "email": usuario.email
    }), 200

@bp_academico.route('/perfil', methods=['PUT'])
@jwt_required()
def actualizar_perfil():
    usuario_id = get_jwt_identity()
    datos = request.get_json()

    contrasena_actual = datos.get('contrasena_actual')
    contrasena_nueva = datos.get('contrasena_nueva')
    contrasena_repetida = datos.get('contrasena_repetida')

    # Validaciones básicas antes de tocar la base de datos
    if not contrasena_actual or not contrasena_nueva or not contrasena_repetida:
        return jsonify(mensaje="Todos los campos son obligatorios"), 400

    if contrasena_nueva != contrasena_repetida:
        return jsonify(mensaje="Las contraseñas nuevas no coinciden"), 400

    # Llamada al repositorio seguro
    exito, mensaje = UsuarioRepository.actualizar_contrasena(usuario_id, contrasena_actual, contrasena_nueva)
    
    if exito:
        return jsonify(mensaje=mensaje), 200
    else:
        return jsonify(mensaje=mensaje), 400

@bp_academico.route('/notificaciones-recientes', methods=['GET'])
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
        Evento.fecha >= limite_fecha
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

@bp_academico.route('/mi-horario', methods=['GET'])
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

    # Caso 1: Admin o consulta con parámetro de curso
    if rol == 'admin' or id_curso_param:
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
        curso_nombre = "Mi Horario Docente"
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
            "hora_fin": h.hora_fin.strftime('%H:%M') if h.hora_fin else "00:00"
        })

    return jsonify({
        "rol": rol,
        "curso_nombre": curso_nombre,
        "horarios": resultado,
        "cursos_disponibles": lista_cursos
    }), 200

# === ENDPOINTS DE NOTAS/CALIFICACIONES ===

@bp_academico.route('/mis-notas', methods=['GET'])
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

@bp_academico.route('/notas-profesores', methods=['GET'])
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

@bp_academico.route('/guardar-calificacion', methods=['POST'])
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

@bp_academico.route('/guardar-nota-final', methods=['POST'])
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


# === NUEVOS ENDPOINTS DE ASISTENCIAS ===

@bp_academico.route('/mis-asistencias', methods=['GET'])
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


@bp_academico.route('/solicitar-justificacion', methods=['POST'])
@jwt_required()
def solicitar_justificacion():
    from db import db
    from models.Asistencia import Asistencia

    usuario_id = get_jwt_identity()
    datos = request.get_json()

    if not datos or not datos.get('id_asistencia') or not datos.get('justificante_texto'):
        return jsonify(mensaje="Datos insuficientes"), 400

    id_asistencia = datos.get('id_asistencia')
    justificante_texto = datos.get('justificante_texto')

    asistencia = Asistencia.query.filter_by(id=id_asistencia, id_alumno=usuario_id).first()
    if not asistencia:
        return jsonify(mensaje="Falta/retraso no encontrado"), 404

    asistencia.justificante_texto = justificante_texto
    asistencia.justificada = False  # Queda en estado "pendiente"

    try:
        db.session.commit()
        return jsonify(mensaje="Justificación enviada correctamente"), 200
    except Exception as e:
        db.session.rollback()
        return jsonify(mensaje=f"Error al enviar la justificación: {str(e)}"), 500


@bp_academico.route('/asistencia-curso', methods=['GET'])
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


@bp_academico.route('/guardar-asistencias', methods=['POST'])
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


@bp_academico.route('/justificaciones-pendientes', methods=['GET'])
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


@bp_academico.route('/resolver-justificacion', methods=['POST'])
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