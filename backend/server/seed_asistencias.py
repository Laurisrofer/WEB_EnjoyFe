import os
import sys
import random
from datetime import date, time

sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from application import app
from db import db
from models.Matricula import Matricula
from models.Asistencia import Asistencia
from models.Usuario import Usuario

def seed():
    with app.app_context():
        print("Limpiando asistencias antiguas...")
        try:
            Asistencia.query.delete()
            db.session.commit()
        except Exception as e:
            db.session.rollback()
            print(f"Error limpiando asistencias: {e}")
            return

        # Obtener todos los alumnos
        alumnos = Usuario.query.filter_by(rol='alumno', estado='activo').all()
        if not alumnos:
            print("No se encontraron alumnos en la base de datos.")
            return

        print(f"Generando asistencias simuladas para {len(alumnos)} alumnos...")

        incidencias_base = [
            # 2 Justificadas
            {"fecha": date(2026, 5, 4), "hora": time(8, 0), "tipo": "falta", "justificada": True, "justificante_texto": None, "observaciones": "Visita médica certificada."},
            {"fecha": date(2026, 5, 6), "hora": time(9, 0), "tipo": "falta", "justificada": True, "justificante_texto": None, "observaciones": "Trámite oficial administrativo."},
            # 2 Injustificadas
            {"fecha": date(2026, 5, 11), "hora": time(8, 0), "tipo": "falta", "justificada": False, "justificante_texto": None, "observaciones": ""},
            {"fecha": date(2026, 5, 13), "hora": time(10, 0), "tipo": "falta", "justificada": False, "justificante_texto": None, "observaciones": ""},
            # 3 Retrasos (1 justificado, 1 injustificado, 1 pendiente)
            {"fecha": date(2026, 5, 18), "hora": time(11, 30), "tipo": "retraso", "justificada": True, "justificante_texto": None, "observaciones": "Autobús escolar demorado."},
            {"fecha": date(2026, 5, 20), "hora": time(9, 0), "tipo": "retraso", "justificada": False, "justificante_texto": None, "observaciones": ""},
            {"fecha": date(2026, 5, 25), "hora": time(10, 0), "tipo": "retraso", "justificada": False, "justificante_texto": "Retraso imprevisto en la línea de cercanías.", "observaciones": ""}
        ]

        for alu in alumnos:
            # Obtener las matrículas (asignaturas) de este alumno
            mats = Matricula.query.filter_by(id_alumno=alu.id).all()
            if not mats:
                continue

            # Para cada incidencia base, elegimos una asignatura aleatoria en la que esté matriculado
            for inc in incidencias_base:
                mat_elegida = random.choice(mats)
                
                f_obj = Asistencia(
                    id_alumno=alu.id,
                    id_asignatura=mat_elegida.id_asignatura,
                    fecha=inc["fecha"],
                    hora=inc["hora"],
                    tipo=inc["tipo"],
                    justificada=inc["justificada"],
                    justificante_texto=inc["justificante_texto"],
                    observaciones=inc["observaciones"]
                )
                db.session.add(f_obj)

        try:
            db.session.commit()
            print("¡Base de datos alimentada con éxito con faltas y retrasos simulados a nivel de alumno!")
        except Exception as e:
            db.session.rollback()
            print(f"Error al guardar asistencias simuladas: {e}")

if __name__ == '__main__':
    seed()
