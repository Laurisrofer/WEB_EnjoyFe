from db import db
from models.Matricula import Matricula
from models.Asignatura import Asignatura
from models.Usuario import Usuario
from models.Asistencia import Asistencia
from models.Calificacion import Calificacion
from datetime import date

class MatriculaRepository:
    
    @staticmethod
    def obtener_por_usuario(id_usuario, rol):
        """
        Devuelve las matrículas según el rol:
        - Alumno: Solo las suyas.
        - Profesor: Las de sus alumnos en sus asignaturas.
        - Admin: Todas.
        """
        if rol == 'alumno':
            return Matricula.query.filter_by(id_alumno=id_usuario).all()
        
        elif rol == 'profesor':
            # Join complejo: Matrículas donde la asignatura la imparte este profe
            return Matricula.query.join(Asignatura).filter(Asignatura.id_profesor == id_usuario).all()
        
        else: # Admin
            return Matricula.query.all()

    @staticmethod
    def obtener_por_id(id_matricula):
        return Matricula.query.get(id_matricula)

    @staticmethod
    def matricular(id_alumno, id_asignatura):
        # Verificar si ya existe
        existe = Matricula.query.filter_by(id_alumno=id_alumno, id_asignatura=id_asignatura).first()
        if existe:
            return None, "El alumno ya está matriculado en esta asignatura"
        
        nueva = Matricula(
            id_alumno=id_alumno, # type: ignore
            id_asignatura=id_asignatura, # type: ignore
            nota_final=None # type: ignore
        )
        try:
            db.session.add(nueva)
            db.session.commit()
            return nueva, "Matriculado correctamente"
        except Exception as e:
            db.session.rollback()
            return None, str(e)

    @staticmethod
    def poner_nota(id_matricula, nueva_nota):
        matricula = Matricula.query.get(id_matricula)
        if not matricula:
            return False
        
        try:
            # 1. Actualizamos la nota global en la tabla Matrículas
            matricula.nota_final = nueva_nota
            
            # 2. CREAMOS EL REGISTRO EN LA TABLA CALIFICACIONES (Cumplimos el requisito)
            nueva_calificacion = Calificacion(
                id_matricula=id_matricula, # type: ignore
                nombre_actividad="Evaluación General", # type: ignore # Nombre automático para no tener que pedirlo en el cliente
                nota=nueva_nota, # type: ignore
                comentario="Nota final" # type: ignore
            )
            db.session.add(nueva_calificacion)
            
            # Guardamos ambas cosas a la vez
            db.session.commit()
            return True
            
        except Exception as e:
            db.session.rollback()
            print(f"Error al insertar calificación en BD: {e}")
            return False

    @staticmethod
    def poner_falta(id_matricula, tipo, observaciones=""):
        # Necesitamos saber el alumno y asignatura de esta matrícula
        matricula = Matricula.query.get(id_matricula)
        if not matricula:
            return False
        
        nueva_falta = Asistencia(
            id_alumno=matricula.id_alumno, # type: ignore
            id_asignatura=matricula.id_asignatura, # type: ignore
            fecha=date.today(), # type: ignore
            tipo=tipo, # type: ignore
            observaciones=observaciones # type: ignore
        )
        db.session.add(nueva_falta)
        db.session.commit()
        return True
    
    @staticmethod
    def eliminar(id_matricula):
        mat = Matricula.query.get(id_matricula)
        if mat:
            db.session.delete(mat)
            db.session.commit()
            return True
        return False
    ################################################################
    @staticmethod
    def obtener_asignaturas_por_usuario(id_usuario):
        # Buscamos todas las matrículas de este alumno (usando id_alumno)
        matriculas = Matricula.query.filter_by(id_alumno=id_usuario).all()
        # Devolvemos una lista con la asignatura de cada matrícula
        return [m.asignatura for m in matriculas]