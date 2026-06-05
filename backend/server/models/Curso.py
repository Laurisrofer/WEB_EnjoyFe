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

    def to_dict(self):
        return {
            "id": self.id,
            "nombre": self.nombre,
            "descripcion": self.descripcion,
            "id_tutor": self.id_tutor
        }

    def to_dict_full(self):
        from models.Horario import Horario
        asigs_list = []
        for a in self.asignaturas:
            # Obtener horarios de esta asignatura
            horarios = Horario.query.filter_by(id_asignatura=a.id).all()
            h_list = []
            for h in horarios:
                h_list.append({
                    "id": h.id,
                    "dia_semana": h.dia_semana,
                    "hora_inicio": str(h.hora_inicio),
                    "hora_fin": str(h.hora_fin)
                })
            
            asigs_list.append({
                "id": a.id,
                "nombre": a.nombre,
                "id_profesor": a.id_profesor,
                "horarios": h_list
            })
            
        return {
            "id": self.id,
            "nombre": self.nombre,
            "descripcion": self.descripcion,
            "id_tutor": self.id_tutor,
            "asignaturas": asigs_list
        }