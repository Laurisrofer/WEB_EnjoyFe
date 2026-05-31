import os
import random
from datetime import datetime, timedelta
from application import app
from db import db
from models.Usuario import Usuario
from models.Asignatura import Asignatura
from models.Matricula import Matricula
from models.Calificacion import Calificacion
from models.Asistencia import Asistencia
from models.Mensaje import Mensaje

def generar_fecha_pasada(dias_atras_min, dias_atras_max):
    dias = random.randint(dias_atras_min, dias_atras_max)
    fecha = datetime.now() - timedelta(days=dias)
    return fecha

def seed_datos():
    with app.app_context():
        # 1. Buscar al usuario alu_laura
        alumno = Usuario.query.filter_by(nombre_usuario='alu_laura').first()
        if not alumno:
            print("El usuario 'alu_laura' no existe en la base de datos.")
            return

        # Buscar un profesor
        profesor = Usuario.query.filter_by(rol='profesor').first()
        if not profesor:
            profesor = alumno # fallback if no prof

        print(f"Generando datos realistas para el alumno: {alumno.nombre_usuario}...")

        # 2. Limpiar datos antiguos
        print("Limpiando datos antiguos...")
        Asistencia.query.filter_by(id_alumno=alumno.id).delete()
        Mensaje.query.filter_by(id_destinatario=alumno.id).delete()
        
        matriculas = Matricula.query.filter_by(id_alumno=alumno.id).all()
        for mat in matriculas:
            Calificacion.query.filter_by(id_matricula=mat.id).delete()
            
        db.session.commit()

        # Obtener asignaturas del alumno (usando sus matrículas)
        matriculas = Matricula.query.filter_by(id_alumno=alumno.id).all()
        if not matriculas:
            print("El alumno no tiene matrículas. Creando algunas...")
            asignaturas_disp = Asignatura.query.limit(4).all()
            for asig in asignaturas_disp:
                nueva_mat = Matricula(id_alumno=alumno.id, id_asignatura=asig.id)
                db.session.add(nueva_mat)
            db.session.commit()
            matriculas = Matricula.query.filter_by(id_alumno=alumno.id).all()

        # 3. Generar Asistencias Realistas
        print("Generando asistencias...")
        # Tomar 3 asignaturas distintas si es posible
        if len(matriculas) >= 3:
            asistencias_seed = [
                {'mat': matriculas[0], 'tipo': 'falta', 'justificada': True, 'observaciones': 'Cita médica', 'dias': 2, 'hora': '08:00'},
                {'mat': matriculas[1], 'tipo': 'falta', 'justificada': False, 'observaciones': '', 'dias': 10, 'hora': '10:00'},
                {'mat': matriculas[2], 'tipo': 'retraso', 'justificada': False, 'observaciones': 'Llegó 15 mins tarde', 'dias': 25, 'hora': '09:00'}
            ]
            
            for asis in asistencias_seed:
                fecha_obj = datetime.now() - timedelta(days=asis['dias'])
                hora_obj = datetime.strptime(asis['hora'], '%H:%M').time()
                
                nueva_asis = Asistencia(
                    id_alumno=alumno.id,
                    id_asignatura=asis['mat'].id_asignatura,
                    fecha=fecha_obj.date(),
                    hora=hora_obj,
                    tipo=asis['tipo'],
                    justificada=asis['justificada'],
                    observaciones=asis['observaciones']
                )
                db.session.add(nueva_asis)

        # 4. Generar Calificaciones Realistas
        print("Generando calificaciones...")
        actividades = ['Examen Parcial 1', 'Trabajo Práctico', 'Deberes semanales']
        
        for mat in matriculas:
            for act in actividades:
                nota = round(random.uniform(5.0, 9.5), 2)
                if 'Trabajo' in act:
                    nota = round(random.uniform(7.5, 10.0), 2)
                elif 'Examen' in act:
                    nota = round(random.uniform(4.5, 9.0), 2)
                
                nueva_cal = Calificacion(
                    id_matricula=mat.id,
                    nombre_actividad=act,
                    nota=nota,
                    fecha_calificacion=generar_fecha_pasada(5, 60).date(),
                    comentario=f"Buen esfuerzo." if nota > 7 else "Se necesita repasar."
                )
                db.session.add(nueva_cal)

        # 5. Generar Mensajes Realistas
        print("Generando mensajes...")
        mensajes_seed = [
            {'asunto': 'Información excursión fin de curso', 'mensaje': 'Estimados alumnos, os recuerdo que el próximo mes realizaremos la excursión.', 'dias': 2, 'leido': False},
            {'asunto': 'Notas del último trabajo', 'mensaje': 'Ya tenéis subidas las notas del trabajo grupal. En general muy buen nivel. ¡Felicidades!', 'dias': 10, 'leido': True},
            {'asunto': 'Material extra', 'mensaje': 'Os adjunto unos apuntes adicionales que no entran en el examen pero os servirán.', 'dias': 30, 'leido': True}
        ]
        
        for msg in mensajes_seed:
            nuevo_msg = Mensaje(
                id_remitente=profesor.id,
                id_destinatario=alumno.id,
                asunto=msg['asunto'],
                mensaje=msg['mensaje'],
                fecha=generar_fecha_pasada(msg['dias'], msg['dias']+2),
                adjunto=None,
                leido=msg['leido']
            )
            db.session.add(nuevo_msg)

        db.session.commit()
        print("¡Datos realistas generados con éxito!")

if __name__ == '__main__':
    seed_datos()
