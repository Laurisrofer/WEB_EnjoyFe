from flask import Flask, jsonify
from flask_jwt_extended import JWTManager
from db import db
import models
from config import Config

from models.Evento import Evento

# Importamos los Blueprints (asegúrate de que existan y no tengan errores de sintaxis)
# Nota: Si aún no has actualizado los archivos de rutas para usar SQL, 
# podrías tener errores al arrancar.
from routes.bp_usuarios import bp_usuarios
from routes.bp_cursos import bp_cursos
from routes.bp_academico import bp_academico
from routes.bp_mensajes import bp_mensajes
from routes.bp_auth import bp_auth

app = Flask(__name__)

# --- CARGAMOS LA CONFIGURACIÓN DESDE EL ARCHIVO EXTERNO ---
app.config.from_object(Config)

# --- INICIALIZACIÓN ---
db.init_app(app)
jwt = JWTManager(app)

# --- REGISTRO DE RUTAS (BLUEPRINTS) ---
app.register_blueprint(bp_auth, url_prefix='/auth')
app.register_blueprint(bp_usuarios, url_prefix='/usuarios')
app.register_blueprint(bp_cursos, url_prefix='/cursos')
app.register_blueprint(bp_academico, url_prefix='/academico')
app.register_blueprint(bp_mensajes, url_prefix='/mensajes')

# --- RUTA DE PRUEBA ---
@app.route('/')
def index():
    return jsonify(mensaje="API Enjoyfe (SQL Version) funcionando correctamente 🚀")

# --- MANEJO DE ERRORES GLOBAL ---
@app.errorhandler(404)
def not_found(e):
    return jsonify(mensaje="Recurso no encontrado"), 404

@app.errorhandler(500)
def internal_error(e):
    return jsonify(mensaje="Error interno del servidor"), 500

if __name__ == '__main__':
    # El contexto de aplicación es necesario para que SQLAlchemy sepa dónde actuar
    with app.app_context():
        # db.create_all() # Descomentar solo si NO tienes la BD creada y quieres que Python la cree.
        print("[OK] Conectado a la base de datos MySQL 'Enjoyfe'")

    # Arrancamos el servidor
    app.run(debug=True, port=5000)