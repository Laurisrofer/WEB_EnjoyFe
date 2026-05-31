from db import db
from datetime import datetime

class Evento(db.Model):
    __tablename__ = 'eventos'

    id = db.Column(db.Integer, primary_key=True)
    titulo = db.Column(db.String(100), nullable=False)
    descripcion = db.Column(db.Text, nullable=True)
    fecha = db.Column(db.Date, nullable=False)
    hora = db.Column(db.Time, nullable=True) # ¡NUEVA COLUMNA!
    tipo = db.Column(db.String(50), nullable=False, default='personal') 
    
    id_usuario = db.Column(db.Integer, db.ForeignKey('usuarios.id'), nullable=True)
    id_curso = db.Column(db.Integer, db.ForeignKey('cursos.id'), nullable=True)