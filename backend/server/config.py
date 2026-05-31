import os
from datetime import timedelta

class Config:
    """Configuración base para la aplicación Flask"""
    
    # Clave secreta para la generación de tokens JWT
    JWT_SECRET_KEY = os.environ.get('JWT_SECRET_KEY') or 'clave-super-secreta-proyecto-final'
    JWT_ACCESS_TOKEN_EXPIRES = timedelta(hours=2)
    
    # Configuración de la base de datos MySQL (con el parche de Pure Python)
    SQLALCHEMY_DATABASE_URI = os.environ.get('DATABASE_URL') or 'mysql+mysqlconnector://root:@localhost:3306/Enjoyfe'
    SQLALCHEMY_TRACK_MODIFICATIONS = False
    
    # Opciones del motor de SQLAlchemy. Forzamos al driver a NO usar la extensión de C que falla
    SQLALCHEMY_ENGINE_OPTIONS = {
        "connect_args": {
            "use_pure": True
        }
    }

    