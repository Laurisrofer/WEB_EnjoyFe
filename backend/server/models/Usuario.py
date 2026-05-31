from db import db

class Usuario(db.Model):
    __tablename__ = 'usuarios'

    id = db.Column(db.Integer, primary_key=True)
    nombre_usuario = db.Column(db.String(50), unique=True, nullable=False)
    password_hash = db.Column(db.String(255), nullable=False)
    nombre_completo = db.Column(db.String(100), nullable=False)
    email = db.Column(db.String(100), unique=True, nullable=False)
    rol = db.Column(db.String(20), nullable=False)
    estado = db.Column(db.String(20), default='activo')
    fecha_registro = db.Column(db.DateTime, server_default=db.func.now())
    fecha_ultimo_login = db.Column(db.DateTime, nullable=True)
    primer_inicio = db.Column(db.Boolean, default=True)
    
    # Nuevo campo añadido para el perfil
    dni = db.Column(db.String(20), nullable=True, unique=True)

    def __init__(self, **kwargs):
        super().__init__(**kwargs)