from db import db
from datetime import datetime

class UsuarioHistorico(db.Model):
    __tablename__ = 'usuarios_historico'
    id_historico = db.Column(db.Integer, primary_key=True)
    id_original = db.Column(db.Integer, nullable=False)
    nombre_usuario = db.Column(db.String(50))
    rol = db.Column(db.String(20))
    email = db.Column(db.String(100))
    fecha_borrado = db.Column(db.DateTime, default=datetime.utcnow)