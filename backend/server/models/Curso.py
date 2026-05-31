from db import db

class Curso(db.Model):
    __tablename__ = 'cursos'

    id = db.Column(db.Integer, primary_key=True)
    nombre = db.Column(db.String(100), nullable=False)
    descripcion = db.Column(db.Text, nullable=True)
    
    # La columna del tutor que añadimos
    id_tutor = db.Column(db.Integer, nullable=True)

    # Restauramos la relación con la tabla Asignaturas que se había borrado
    asignaturas = db.relationship('Asignatura', back_populates='curso', lazy=True)

    # Constructor
    def __init__(self, **kwargs):
        super().__init__(**kwargs)