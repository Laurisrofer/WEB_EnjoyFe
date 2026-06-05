from db import db

class Asignatura(db.Model):
    __tablename__ = 'asignaturas'
    
    id = db.Column(db.Integer, primary_key=True)
    nombre = db.Column(db.String(100), nullable=False)
    id_curso = db.Column(db.Integer, db.ForeignKey('cursos.id'), nullable=False)
    id_profesor = db.Column(db.Integer, db.ForeignKey('usuarios.id'))
    guia_docente = db.Column(db.Text, nullable=True)
    recursos_json = db.Column(db.Text, nullable=True)
    color = db.Column(db.String(20), default='#3498db')
    
    # --- RELACIONES EXPLÍCITAS ---
    
    # 1. Relación con Curso (Coincide con back_populates='curso' en Curso.py)
    curso = db.relationship('Curso', back_populates='asignaturas')
    
    # 2. Relación con Profesor (Unidireccional está bien, o usa back_populates si quieres lista en Usuario)
    profesor = db.relationship('Usuario', foreign_keys=[id_profesor])
    
    # 3. Relación con Horario (Coincide con back_populates='asignatura' en Horario.py)
    horarios = db.relationship('Horario', back_populates='asignatura', lazy=True)

    def to_dict(self):
        nombre_prof = self.profesor.nombre_completo if self.profesor else "Sin asignar"
        return {
            "id": self.id,
            "nombre": self.nombre,
            "id_profesor": self.id_profesor,
            "nombre_profesor": nombre_prof,
            "guia_docente": self.guia_docente,
            "recursos_json": self.recursos_json,
            "color": self.color,
            # Ahora 'self.horarios' existe físicamente en esta clase
            "horarios": [h.to_dict() for h in self.horarios] # type: ignore
        }