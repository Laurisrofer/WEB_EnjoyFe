import os
import sys

sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from application import app
from db import db

def upgrade():
    with app.app_context():
        connection = db.engine.connect()
        trans = connection.begin()
        try:
            print("Verificando columnas existentes en la tabla 'asistencias'...")
            # Comprobar si las columnas ya existen
            result = connection.execute(db.text("SHOW COLUMNS FROM asistencias"))
            columns = [row[0] for row in result]
            
            if 'hora' not in columns:
                print("Añadiendo columna 'hora' a la tabla 'asistencias'...")
                # La ponemos como NULL o con un valor por defecto para no romper registros existentes
                connection.execute(db.text("ALTER TABLE asistencias ADD COLUMN hora TIME DEFAULT '08:00:00' AFTER fecha"))
                print("Columna 'hora' añadida con éxito.")
            else:
                print("La columna 'hora' ya existe.")
                
            if 'justificante_texto' not in columns:
                print("Añadiendo columna 'justificante_texto' a la tabla 'asistencias'...")
                connection.execute(db.text("ALTER TABLE asistencias ADD COLUMN justificante_texto TEXT AFTER justificada"))
                print("Columna 'justificante_texto' añadida con éxito.")
            else:
                print("La columna 'justificante_texto' ya existe.")
                
            trans.commit()
            print("✅ Base de datos actualizada correctamente.")
        except Exception as e:
            trans.rollback()
            print(f"❌ Error al actualizar la base de datos: {e}")
        finally:
            connection.close()

if __name__ == '__main__':
    upgrade()
