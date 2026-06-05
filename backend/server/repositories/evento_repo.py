from db import db
from models.Evento import Evento
from sqlalchemy import or_, and_

class EventoRepository:

    # ... (tus otras funciones como obtener_eventos_dashboard, etc.) ...

    @staticmethod
    def crear_evento(datos_evento):
        try:
            nuevo_evento = Evento(
                titulo=datos_evento['titulo'],# type: ignore
                descripcion=datos_evento.get('descripcion', ''),# type: ignore
                fecha=datos_evento['fecha'],# type: ignore
                hora=datos_evento.get('hora'),# type: ignore
                tipo=datos_evento.get('tipo', 'personal'),# type: ignore
                id_usuario=datos_evento.get('id_usuario'),# type: ignore
                id_curso=datos_evento.get('id_curso')# type: ignore
            )
            db.session.add(nuevo_evento)
            db.session.commit()
            return True
        except Exception as e:
            db.session.rollback()
            print(f"Error creando evento: {e}")
            return False

    @staticmethod
    def borrar_evento(id_evento, id_usuario, rol_usuario=''):
        # Buscamos el evento y nos aseguramos de que pertenezca al usuario (o admin puede todos)
        if rol_usuario == 'admin':
            evento = Evento.query.filter_by(id=id_evento).first()
        else:
            evento = Evento.query.filter_by(id=id_evento, id_usuario=id_usuario).first()
            
        if evento:
            db.session.delete(evento)
            db.session.commit()
            return True
        return False
    
    @staticmethod
    def obtener_eventos_dashboard(usuario_id, id_curso, rol_usuario=''):
        if rol_usuario == 'admin':
            return Evento.query.all()
            
        # Buscamos eventos que sean del usuario (personales) 
        # O que sean del curso (exámenes/entregas generales)
        # O anuncios globales del centro
        
        if id_curso is None:
            return Evento.query.filter(
                or_(
                    Evento.id_usuario == usuario_id,
                    and_(Evento.tipo == 'anuncio', Evento.id_curso.is_(None))
                )
            ).all()
            
        return Evento.query.filter(
            or_(
                Evento.id_usuario == usuario_id,
                Evento.id_curso == id_curso,
                and_(Evento.tipo == 'anuncio', Evento.id_curso.is_(None))
            )
        ).all()
    
    @staticmethod
    def actualizar_evento(id_evento, datos_evento, id_usuario, rol_usuario=''):
        # Buscamos el evento que pertenezca al usuario o admin
        if rol_usuario == 'admin':
            evento = Evento.query.filter_by(id=id_evento).first()
        else:
            evento = Evento.query.filter_by(id=id_evento, id_usuario=id_usuario).first()
            
        if evento:
            # Actualizamos solo si los datos vienen en el JSON
            evento.titulo = datos_evento.get('titulo', evento.titulo)
            evento.fecha = datos_evento.get('fecha', evento.fecha)
            evento.hora = datos_evento.get('hora', evento.hora)
            evento.tipo = datos_evento.get('tipo', evento.tipo)
            
            # Nuevos campos
            if 'descripcion' in datos_evento:
                evento.descripcion = datos_evento['descripcion']
            if 'id_curso' in datos_evento:
                # Si el id_curso viene como string vacío, lo guardamos como None (Global)
                val_curso = datos_evento['id_curso']
                evento.id_curso = None if val_curso == '' else val_curso
            
            db.session.commit()
            return True
        return False