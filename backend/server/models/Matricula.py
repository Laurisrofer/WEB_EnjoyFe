from db import db

class Matricula(db.Model):
    __tablename__ = 'matriculas'
    
    id = db.Column(db.Integer, primary_key=True)
    id_alumno = db.Column(db.Integer, db.ForeignKey('usuarios.id'), nullable=False)
    id_asignatura = db.Column(db.Integer, db.ForeignKey('asignaturas.id'), nullable=False)
    nota_final = db.Column(db.Numeric(4, 2))
    observaciones_globales = db.Column(db.Text)

    # RELACIONES
    alumno = db.relationship('Usuario', foreign_keys=[id_alumno])
    asignatura = db.relationship('Asignatura', foreign_keys=[id_asignatura])
    calificaciones = db.relationship('Calificacion', backref='matricula', lazy=True)

    def to_dict(self):
        # Importamos Asistencia aquí dentro para evitar errores de importación circular
        from models.Asistencia import Asistencia
        
        nom_alumno = self.alumno.nombre_completo if self.alumno else "Desconocido"
        nom_asignatura = self.asignatura.nombre if self.asignatura else "Desconocida"
        nom_curso = "Desconocido"
        
        if self.asignatura and self.asignatura.curso:
            nom_curso = self.asignatura.curso.nombre

        # Buscamos las faltas de este alumno en esta asignatura específica
        faltas = Asistencia.query.filter_by(
            id_alumno=self.id_alumno, 
            id_asignatura=self.id_asignatura
        ).all()

        return {
            "id": self.id,
            "id_alumno": self.id_alumno,
            "alumno": nom_alumno,
            "asignatura": nom_asignatura,
            "curso": nom_curso,
            "nota": float(self.nota_final) if self.nota_final is not None else "-",
            # AÑADIDO: La clave que esperaba tu cliente
            "asistencias": [{"id": f.id, "tipo": f.tipo} for f in faltas]
        }