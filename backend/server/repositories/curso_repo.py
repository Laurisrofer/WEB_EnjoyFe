from db import db
from models.Curso import Curso
from models.Asignatura import Asignatura
from models.Horario import Horario
from models.Matricula import Matricula

class CursoRepository:
    
    @staticmethod
    def obtener_todos():
        return Curso.query.all()

    @staticmethod
    def obtener_por_id(id_curso):
        return Curso.query.get(id_curso)

    @staticmethod
    def _parsear_dia(dia_raw):
        """Convierte 'Lunes' en 1, 'Martes' en 2... para que SQL no falle"""
        if isinstance(dia_raw, str):
            dmap = {"Lunes": 1, "Martes": 2, "Miércoles": 3, "Miercoles": 3, "Jueves": 4, "Viernes": 5}
            return dmap.get(dia_raw.capitalize(), 1)
        return int(dia_raw) if dia_raw else 1

    @staticmethod
    def crear_completo(datos_curso):
        try:
            nuevo_curso = Curso(
                nombre=datos_curso['nombre'],# type: ignore
                descripcion=datos_curso.get('descripcion', ''),# type: ignore
                id_tutor=datos_curso.get('id_tutor')# type: ignore
            ) # type: ignore
            db.session.add(nuevo_curso)
            db.session.flush()

            if 'asignaturas' in datos_curso:
                for asig_data in datos_curso['asignaturas']:
                    nueva_asig = Asignatura(
                        nombre=asig_data['nombre'],# type: ignore
                        id_curso=nuevo_curso.id,# type: ignore
                        # AHORA SÍ LEEMOS EL PROFESOR
                        id_profesor=asig_data.get('profesor_id') # type: ignore
                    ) # type: ignore
                    db.session.add(nueva_asig)
                    db.session.flush()

                    horarios_data = asig_data.get('horario') or asig_data.get('horarios', [])
                    for h_data in horarios_data:
                        nuevo_h = Horario(
                            id_curso=nuevo_curso.id,# type: ignore
                            id_asignatura=nueva_asig.id,# type: ignore
                            dia_semana=CursoRepository._parsear_dia(h_data.get('dia_semana', h_data.get('dia'))),# type: ignore
                            hora_inicio=h_data.get('hora_inicio', h_data.get('inicio', '00:00')),# type: ignore
                            hora_fin=h_data.get('hora_fin', h_data.get('fin', '00:00'))# type: ignore
                        ) # type: ignore
                        db.session.add(nuevo_h)
            
            db.session.commit()
            return nuevo_curso
        except Exception as e:
            db.session.rollback()
            print(f"Error creando curso: {e}")
            return None

    @staticmethod
    def actualizar_completo(id_curso, datos_curso):
        curso = Curso.query.get(id_curso)
        if not curso: return False
            
        try:
            # 1. Actualizar datos base del curso
            if 'nombre' in datos_curso: curso.nombre = datos_curso['nombre']
            if 'descripcion' in datos_curso: curso.descripcion = datos_curso['descripcion']
            if 'id_tutor' in datos_curso: curso.id_tutor = datos_curso['id_tutor']

            # 2. Si vienen asignaturas, hacemos "borrón y cuenta nueva"
            if 'asignaturas' in datos_curso:
                # A) Borramos todo lo viejo (Horarios, Matriculas y Asignaturas)
                for asig_vieja in curso.asignaturas:
                    Horario.query.filter_by(id_asignatura=asig_vieja.id).delete()
                    Matricula.query.filter_by(id_asignatura=asig_vieja.id).delete()
                    db.session.delete(asig_vieja)
                
                # Forzamos el borrado en la BD antes de insertar lo nuevo
                db.session.flush() 

                # B) Insertamos todo lo nuevo desde cero
                for asig_data in datos_curso['asignaturas']:
                    nueva_asig = Asignatura(
                        nombre=asig_data['nombre'], # type: ignore
                        id_curso=curso.id, # type: ignore
                        id_profesor=asig_data.get('profesor_id') # type: ignore
                    ) # type: ignore
                    db.session.add(nueva_asig)
                    db.session.flush() # Obtenemos el nuevo ID generado
                    
                    horarios_data = asig_data.get('horario') or asig_data.get('horarios', [])
                    for h_data in horarios_data:
                        nuevo_h = Horario(
                            id_curso=curso.id, # type: ignore
                            id_asignatura=nueva_asig.id, # type: ignore
                            dia_semana=CursoRepository._parsear_dia(h_data.get('dia_semana', h_data.get('dia'))), # type: ignore
                            hora_inicio=h_data.get('hora_inicio', h_data.get('inicio', '00:00')), # type: ignore
                            hora_fin=h_data.get('hora_fin', h_data.get('fin', '00:00')) # type: ignore
                        ) # type: ignore
                        db.session.add(nuevo_h)
            
            db.session.commit()
            return True
        except Exception as e:
            db.session.rollback()
            print(f"Error actualizando curso: {e}")
            return False

    @staticmethod
    def eliminar(id_curso):
        curso = Curso.query.get(id_curso)
        if not curso: return False
        try:
            for asig in curso.asignaturas:
                Horario.query.filter_by(id_asignatura=asig.id).delete()
                Matricula.query.filter_by(id_asignatura=asig.id).delete()
                db.session.delete(asig)
            db.session.delete(curso)
            db.session.commit()
            return True
        except Exception as e:
            db.session.rollback()
            return False
        
    @staticmethod
    def buscar_asignatura_por_nombre(id_curso, nombre_asignatura):
        """
        Busca una asignatura específica dentro de un curso concreto.
        """
        return Asignatura.query.filter_by(id_curso=id_curso, nombre=nombre_asignatura).first()