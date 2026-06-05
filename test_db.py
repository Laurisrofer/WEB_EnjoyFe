import sqlite3
import json

try:
    c = sqlite3.connect('backend/server/enjoyfe.db')
    users = c.execute("SELECT id, email, rol FROM usuarios WHERE rol='profesor'").fetchall()
    print("Profesores:", users)
except Exception as e:
    print(e)
