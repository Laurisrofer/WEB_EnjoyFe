from db import db
from datetime import datetime

class Mensaje(db.Model):
    __tablename__ = 'mensajes'
    
    id = db.Column(db.Integer, primary_key=True)
    id_remitente = db.Column(db.Integer, db.ForeignKey('usuarios.id'), nullable=False)
    id_destinatario = db.Column(db.Integer, db.ForeignKey('usuarios.id'), nullable=False)
    asunto = db.Column(db.String(150))
    mensaje = db.Column(db.Text)
    fecha = db.Column(db.DateTime, default=datetime.utcnow)
    leido = db.Column(db.Boolean, default=False)
    adjunto = db.Column(db.String(255), nullable=True)

    # Relaciones que inyectan listas en Usuario.py
    remitente = db.relationship('Usuario', foreign_keys=[id_remitente], backref='mensajes_enviados')
    destinatario = db.relationship('Usuario', foreign_keys=[id_destinatario], backref='mensajes_recibidos')

    def to_dict(self):
        return {
            "id": self.id,
            "de": self.remitente.nombre_usuario if self.remitente else "Desconocido",
            "para": self.destinatario.nombre_usuario if self.destinatario else "Desconocido",
            "asunto": self.asunto,
            "cuerpo": self.mensaje,
            "fecha": self.fecha.isoformat(),
            "leido": self.leido,
            "adjunto": self.adjunto
        }