import sys
import os

file_path = r"c:\xampp\htdocs\enjoyfe_web\backend\server\routes\bp_academico.py"
with open(file_path, "a", encoding="utf-8") as f:
    f.write("""

# === ENDPOINTS DE PROFESOR ===

@bp_academico.route('/mis-cursos', methods=['GET'])
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

@bp_academico.route('/mis-alumnos', methods=['GET'])
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

@bp_academico.route('/calificacion/<int:id_calificacion>', methods=['DELETE'])
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

@bp_academico.route('/guia-docente/<int:id_asignatura>', methods=['PUT'])
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

@bp_academico.route('/estadisticas-profesor', methods=['GET'])
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
        # Calcular % aprobados
        matriculas = Matricula.query.filter_by(id_asignatura=asig.id).all()
        total_alumnos = len(matriculas)
        aprobados = 0
        
        for m in matriculas:
            # Una nota final mayor o igual a 5 es aprobado. Si no hay nota final, miramos la media
            if m.nota_final is not None:
                if float(m.nota_final) >= 5.0:
                    aprobados += 1
            else:
                # Calcular media de calificaciones parciales si existen
                califs = Calificacion.query.filter_by(id_matricula=m.id).all()
                if califs:
                    media = sum(float(c.nota) for c in califs) / len(califs)
                    if media >= 5.0:
                        aprobados += 1
                        
        porcentaje_aprobados = (aprobados / total_alumnos * 100) if total_alumnos > 0 else 0
        
        # Calcular % asistencia media
        # Total de clases = total de asistencias (presentes + faltas + retrasos) para esta asignatura
        asistencias = Asistencia.query.filter_by(id_asignatura=asig.id).all()
        total_registros = len(asistencias)
        # Consideramos 'presente' como falta de registro o tipo 'retraso' si queremos, pero en Enjoyfe 
        # tipo es 'falta' o 'retraso'. Si no hay registro, asumimos presente (así funciona el sistema actual).
        # Para ser precisos, calculamos la tasa de faltas y restamos.
        faltas = sum(1 for a in asistencias if a.tipo == 'falta')
        retrasos = sum(1 for a in asistencias if a.tipo == 'retraso')
        
        # Necesitamos saber cuántas sesiones en total hubo, estimamos por el número max de faltas de un alumno
        # o devolvemos datos brutos
        
        resultados.append({
            'id_asignatura': asig.id,
            'nombre_asignatura': asig.nombre,
            'total_alumnos': total_alumnos,
            'aprobados': aprobados,
            'porcentaje_aprobados': round(porcentaje_aprobados, 1),
            'total_faltas_registradas': faltas,
            'total_retrasos_registrados': retrasos
        })
        
    return jsonify(resultados), 200

""")
