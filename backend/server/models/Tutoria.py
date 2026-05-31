from db import db

class Tutoria(db.Model):
    __tablename__ = 'tutorias'
    id = db.Column(db.Integer, primary_key=True)
    id_profesor = db.Column(db.Integer, db.ForeignKey('usuarios.id'), nullable=False)
    id_alumno = db.Column(db.Integer, db.ForeignKey('usuarios.id'), nullable=False)
    fecha = db.Column(db.Date, nullable=False)
    hora = db.Column(db.Time, nullable=False)
    asunto = db.Column(db.String(150))
    estado = db.Column(db.String(20), default='Pendiente')
    notas_reunion = db.Column(db.Text)

    profesor = db.relationship('Usuario', foreign_keys=[id_profesor])
    alumno = db.relationship('Usuario', foreign_keys=[id_alumno])

    def to_dict(self):
        return {
            "id": self.id,
            "profesor": self.profesor.nombre_completo,
            "alumno": self.alumno.nombre_completo,
            "fecha": str(self.fecha),
            "hora": str(self.hora),
            "estado": self.estado
        }