from flask import Flask, jsonify
from flask_jwt_extended import JWTManager
from db import db
import models
from config import Config

from models.Evento import Evento

# ==========================================
# IMPORTACIÓN DE RUTAS (BLUEPRINTS)
# ==========================================
# Los Blueprints de Flask nos permiten dividir nuestra API en distintos módulos.
# En lugar de tener miles de líneas aquí, cada archivo gestiona su propio "trozo" de la API.
from routes.bp_usuarios import bp_usuarios
from routes.bp_cursos import bp_cursos
from routes.academico.bp_dashboard import bp_dashboard
from routes.academico.bp_asignaturas import bp_asignaturas
from routes.academico.bp_eventos import bp_eventos
from routes.academico.bp_perfil import bp_perfil
from routes.academico.bp_notas import bp_notas
from routes.academico.bp_asistencias import bp_asistencias
from routes.academico.bp_horarios import bp_horarios
from routes.academico.bp_aulas import bp_aulas
from routes.bp_mensajes import bp_mensajes
from routes.bp_auth import bp_auth

# ==========================================
# CREACIÓN DEL SERVIDOR FLASK
# ==========================================
app = Flask(__name__)

# --- CARGAMOS LA CONFIGURACIÓN DESDE EL ARCHIVO EXTERNO ---
app.config.from_object(Config)

# --- INICIALIZACIÓN DE LA BASE DE DATOS Y SEGURIDAD ---
db.init_app(app) # Conecta el ORM SQLAlchemy con Flask
jwt = JWTManager(app) # Inicializa el gestor de tokens para la seguridad de sesiones

# --- REGISTRO DE RUTAS (BLUEPRINTS) ---
# Aquí le decimos al servidor qué URL pertenece a qué módulo.
# Por ejemplo, cualquier petición a /auth/... la gestionará bp_auth.
app.register_blueprint(bp_auth, url_prefix='/auth')
app.register_blueprint(bp_usuarios, url_prefix='/usuarios')
app.register_blueprint(bp_cursos, url_prefix='/cursos')
app.register_blueprint(bp_dashboard, url_prefix='/academico')
app.register_blueprint(bp_asignaturas, url_prefix='/academico')
app.register_blueprint(bp_eventos, url_prefix='/academico')
app.register_blueprint(bp_perfil, url_prefix='/academico')
app.register_blueprint(bp_notas, url_prefix='/academico')
app.register_blueprint(bp_asistencias, url_prefix='/academico')
app.register_blueprint(bp_horarios, url_prefix='/academico')
app.register_blueprint(bp_aulas, url_prefix='/academico')
app.register_blueprint(bp_mensajes, url_prefix='/mensajes')

# --- RUTA DE PRUEBA DE ESTADO ---
# Un endpoint simple para comprobar si el backend está vivo.
@app.route('/')
def index():
    return jsonify(mensaje="API Enjoyfe (SQL Version) funcionando correctamente 🚀")

# ==========================================
# MANEJO DE ERRORES GLOBAL
# ==========================================
@app.errorhandler(404)
def not_found(e):
    return jsonify(mensaje="Recurso no encontrado. La ruta especificada no existe en la API."), 404

@app.errorhandler(500)
def internal_error(e):
    return jsonify(mensaje="Error interno del servidor. Revisa los logs de Python."), 500

# ==========================================
# ARRANQUE DEL SERVIDOR
# ==========================================
if __name__ == '__main__':
    # El contexto de aplicación es necesario para que SQLAlchemy pueda conectarse a MySQL
    with app.app_context():
        # db.create_all() # Esto crearía las tablas desde cero, pero ya usamos SQL nativo.
        print("[OK] Conectado a la base de datos MySQL 'Enjoyfe'")

    # Arrancamos el servidor en el puerto 5000 con modo debug activado
    app.run(debug=True, port=5000)