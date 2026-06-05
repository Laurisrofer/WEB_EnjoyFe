from application import app
from db import db

with app.app_context():
    try:
        from models.Usuario import Usuario
        from models.Curso import Curso
        from models.Asistencia import Asistencia
        from models.Matricula import Matricula
        from models.Asignatura import Asignatura
        from sqlalchemy import func

        total_profesores = Usuario.query.filter_by(rol='profesor').count()
        total_alumnos = Usuario.query.filter_by(rol='alumno').count()
        total_cursos = Curso.query.count()

        asistencia_por_curso = []
        alumnos_por_curso = []
        cursos = Curso.query.all()
        for c in cursos:
            num_alumnos = db.session.query(func.count(func.distinct(Matricula.id_alumno))).\
                join(Asignatura, Matricula.id_asignatura == Asignatura.id).\
                filter(Asignatura.id_curso == c.id).scalar() or 0
                
            alumnos_por_curso.append({"curso": c.nombre, "cantidad": num_alumnos})
            
            total_registros = db.session.query(func.count(Asistencia.id)).\
                join(Asignatura, Asistencia.id_asignatura == Asignatura.id).\
                filter(Asignatura.id_curso == c.id).scalar() or 0
        
        print("Success!")
        print(total_profesores, total_alumnos, total_cursos)
        print(alumnos_por_curso)
    except Exception as e:
        import traceback
        traceback.print_exc()
