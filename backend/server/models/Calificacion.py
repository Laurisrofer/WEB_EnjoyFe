from db import db
from datetime import datetime

class Calificacion(db.Model):
    __tablename__ = 'calificaciones'
    id = db.Column(db.Integer, primary_key=True)
    id_matricula = db.Column(db.Integer, db.ForeignKey('matriculas.id'), nullable=False)
    nombre_actividad = db.Column(db.String(100), nullable=False)
    nota = db.Column(db.Numeric(4, 2))
    comentario = db.Column(db.Text)
    fecha_calificacion = db.Column(db.Date, default=datetime.utcnow)