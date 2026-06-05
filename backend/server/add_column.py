from application import app
from db import db
from sqlalchemy import text

with app.app_context():
    try:
        db.session.execute(text("ALTER TABLE asignaturas ADD COLUMN recursos_json TEXT NULL;"))
        db.session.commit()
        print("Columna recursos_json añadida correctamente.")
    except Exception as e:
        print(f"La columna ya existe o hubo un error: {e}")
