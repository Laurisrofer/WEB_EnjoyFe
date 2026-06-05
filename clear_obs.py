import sqlite3
import os

db_path = 'backend/server/enjoyfe.db'
if os.path.exists(db_path):
    conn = sqlite3.connect(db_path)
    cursor = conn.cursor()
    cursor.execute("UPDATE matricula SET observaciones_globales = '' WHERE observaciones_globales LIKE 'Estudiante sobresaliente%'")
    conn.commit()
    conn.close()
    print("Updated database.")
else:
    print("Database not found.")
