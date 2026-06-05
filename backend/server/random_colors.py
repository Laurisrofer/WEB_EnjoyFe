import mysql.connector
import random

palette = [
    '#ff9ff3', '#feca57', '#ff6b6b', '#48dbfb', '#1dd1a1',
    '#f368e0', '#ff9f43', '#ee5253', '#0abde3', '#10ac84',
    '#00d2d3', '#54a0ff', '#5f27cd', '#c8d6e5', '#576574',
    '#01a3a4', '#2e86de', '#341f97', '#8395a7', '#222f3e'
]

try:
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="Enjoyfe"
    )
    cursor = conn.cursor()
    
    cursor.execute("SELECT id FROM asignaturas;")
    asignaturas = cursor.fetchall()
    
    for (asig_id,) in asignaturas:
        color = random.choice(palette)
        cursor.execute("UPDATE asignaturas SET color = %s WHERE id = %s;", (color, asig_id))
    
    conn.commit()
    print(f"Colors successfully assigned to {len(asignaturas)} asignaturas.")
    
    cursor.close()
    conn.close()
except Exception as e:
    print(f"Error: {e}")
