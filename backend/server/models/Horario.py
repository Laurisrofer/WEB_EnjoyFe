from db import db

class Horario(db.Model):
    __tablename__ = 'horarios'
    id = db.Column(db.Integer, primary_key=True)
    id_curso = db.Column(db.Integer, db.ForeignKey('cursos.id'), nullable=False)
    id_asignatura = db.Column(db.Integer, db.ForeignKey('asignaturas.id'), nullable=False)
    dia_semana = db.Column(db.Integer, nullable=False)
    hora_inicio = db.Column(db.Time, nullable=False)
    hora_fin = db.Column(db.Time, nullable=False)

    # --- RELACIÓN EXPLÍCITA ---
    # Coincide con back_populates='horarios' en Asignatura.py
    asignatura = db.relationship('Asignatura', back_populates='horarios')

    def to_dict(self):
        dias = {1: "Lunes", 2: "Martes", 3: "Miércoles", 4: "Jueves", 5: "Viernes"}
        return {
            "dia": dias.get(self.dia_semana, "Desconocido"),
            "inicio": str(self.hora_inicio),
            "fin": str(self.hora_fin)
        }