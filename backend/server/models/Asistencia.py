from db import db

class Asistencia(db.Model):
    __tablename__ = 'asistencias'
    id = db.Column(db.Integer, primary_key=True)
    id_alumno = db.Column(db.Integer, db.ForeignKey('usuarios.id'), nullable=False)
    id_asignatura = db.Column(db.Integer, db.ForeignKey('asignaturas.id'), nullable=False)
    fecha = db.Column(db.Date, nullable=False)
    hora = db.Column(db.Time, nullable=False)
    tipo = db.Column(db.String(20), default='falta') # 'falta', 'retraso'
    justificada = db.Column(db.Boolean, default=False)
    justificante_texto = db.Column(db.Text)
    observaciones = db.Column(db.String(255))

    # Relaciones
    alumno = db.relationship('Usuario', foreign_keys=[id_alumno])
    asignatura = db.relationship('Asignatura', foreign_keys=[id_asignatura])
    
    def to_dict(self):
        nom_asignatura = self.asignatura.nombre if self.asignatura else "Desconocida"
        return {
            "id": self.id,
            "id_alumno": self.id_alumno,
            "id_asignatura": self.id_asignatura,
            "asignatura": nom_asignatura,
            "fecha": self.fecha.strftime('%d/%m/%Y'),
            "hora": self.hora.strftime('%H:%M') if self.hora else "08:00",
            "tipo": self.tipo,
            "justificada": bool(self.justificada),
            "justificante_texto": self.justificante_texto or "",
            "observaciones": self.observaciones or ""
        }