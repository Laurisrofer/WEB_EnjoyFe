from application import app
from db import db
from models.Evento import Evento

with app.app_context():
    eventos = Evento.query.all()
    for e in eventos:
        print(f"ID: {e.id}, Tipo: {e.tipo}, Fecha: {type(e.fecha)} - {e.fecha}")
