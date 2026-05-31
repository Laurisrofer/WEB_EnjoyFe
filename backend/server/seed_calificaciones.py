import os
import sys
import random
from datetime import date

# Añadir el directorio actual al path
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from application import app
from db import db
from models.Matricula import Matricula
from models.Calificacion import Calificacion

def seed():
    with app.app_context():
        # Limpiar calificaciones previas para reiniciar el entorno de pruebas
        print("Borrando calificaciones antiguas...")
        try:
            Calificacion.query.delete()
            db.session.commit()
        except Exception as e:
            db.session.rollback()
            print(f"Error limpiando calificaciones: {e}")
            return
        
        matriculas = Matricula.query.all()
        if not matriculas:
            print("No se encontraron matrículas en la base de datos para calificar.")
            return
            
        print(f"Generando calificaciones aleatorias para {len(matriculas)} matrículas...")
        
        actividades = [
            {"nombre": "[1ª Eval] Examen Temático", "fecha": date(2025, 11, 20)},
            {"nombre": "[2ª Eval] Proyecto Práctico", "fecha": date(2026, 2, 28)},
            {"nombre": "[3ª Eval] Trabajo Final", "fecha": date(2026, 5, 20)},
        ]
        
        comments_high = [
            "Excelente trabajo, cumple con creces todos los objetivos y demuestra una gran atención al detalle.",
            "Magnífico desempeño en la prueba, conceptos muy claros y buena estructuración.",
            "Entrega impecable y puntual. Gran nivel técnico mostrado en la resolución del problema.",
            "Trabajo sobresaliente. Muy limpio y bien estructurado."
        ]
        
        comments_med = [
            "Buen trabajo general. Debes prestar más atención a los pequeños errores de formato y pulir detalles.",
            "Aprobado holgado. Has entendido el concepto principal, pero faltó profundizar en la segunda sección.",
            "Correcta ejecución. Se aprecian algunos despistes menores en la entrega, pero la base es sólida.",
            "Entrega correcta. Cumple con lo básico pero puede mejorarse la presentación."
        ]
        
        comments_low = [
            "No se han alcanzado los objetivos mínimos exigidos para esta entrega. Debes revisar las tutorías.",
            "Faltan apartados críticos por desarrollar y el planteamiento general contiene errores graves.",
            "Se requiere una revisión profunda de la materia. Se recomienda programar una tutoría de refuerzo.",
            "Entrega incompleta y con fallos en el razonamiento lógico. Requiere corrección."
        ]
        
        laura_matriculas_count = 0
        for m in matriculas:
            es_laura = m.alumno.nombre_usuario == 'alu_laura' if m.alumno else False
            
            notas_m = []
            for idx, act in enumerate(actividades):
                # Si es Laura y es su primera matrícula, forzamos suspensos (para ver rojo)
                if es_laura and laura_matriculas_count == 0:
                    nota = round(random.uniform(2.0, 4.8), 2)
                    comentario = random.choice(comments_low)
                else:
                    # Comportamiento general: 85% de aprobado/alto, 15% de suspenso
                    es_suspenso = random.random() < 0.15
                    if es_suspenso:
                        nota = round(random.uniform(2.0, 4.9), 2)
                        comentario = random.choice(comments_low)
                    else:
                        nota = round(random.uniform(5.0, 10.0), 2)
                        if nota >= 8.5:
                            comentario = random.choice(comments_high)
                        else:
                            comentario = random.choice(comments_med)
                
                notas_m.append(nota)
                
                # Crear la calificación
                calif = Calificacion(
                    id_matricula=m.id,
                    nombre_actividad=act["nombre"],
                    nota=nota,
                    comentario=comentario,
                    fecha_calificacion=act["fecha"]
                )
                db.session.add(calif)
            
            if es_laura:
                laura_matriculas_count += 1
            
            # Calcular nota final y observaciones globales
            nota_final = round(sum(notas_m) / len(notas_m), 1)
            m.nota_final = nota_final
            
            if nota_final >= 8.5:
                m.observaciones_globales = "Estudiante sobresaliente. Muestra una actitud proactiva, gran autonomía y excelente asimilación de competencias."
            elif nota_final >= 5.0:
                m.observaciones_globales = "Progreso adecuado durante el curso. Mantiene un rendimiento estable y demuestra interés en mejorar."
            else:
                m.observaciones_globales = "No logra alcanzar los objetivos de la asignatura. Requiere mayor esfuerzo y asistencia constante a clases de apoyo."
                
        try:
            db.session.commit()
            print("¡Base de datos alimentada con éxito con calificaciones simuladas!")
        except Exception as e:
            db.session.rollback()
            print(f"Error al guardar datos simulados: {e}")

if __name__ == '__main__':
    seed()
