from db import db
from datetime import datetime

class LogAcceso(db.Model):
    __tablename__ = 'logs_acceso'
    id = db.Column(db.Integer, primary_key=True)
    id_usuario = db.Column(db.Integer, db.ForeignKey('usuarios.id'), nullable=False)
    fecha_hora = db.Column(db.DateTime, default=datetime.utcnow)
    accion = db.Column(db.String(255), default='Inicio de sesión')