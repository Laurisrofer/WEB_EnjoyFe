import mysql.connector
import random

palette_base = [
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
    cursor = conn.cursor(dictionary=True)
    
    # Obtener todos los cursos
    cursor.execute("SELECT id FROM cursos;")
    cursos = cursor.fetchall()
    
    total_asignaturas_actualizadas = 0
    
    for curso in cursos:
        id_curso = curso['id']
        
        # Obtener asignaturas del curso
        cursor.execute("SELECT id FROM asignaturas WHERE id_curso = %s;", (id_curso,))
        asignaturas = cursor.fetchall()
        
        if not asignaturas:
            continue
            
        # Mezclar la paleta para este curso
        colores_disponibles = list(palette_base)
        random.shuffle(colores_disponibles)
        
        # Asignar un color único a cada asignatura del curso
        for i, asig in enumerate(asignaturas):
            # Si hay más asignaturas que colores (raro), se repite la paleta
            color = colores_disponibles[i % len(colores_disponibles)]
            cursor.execute("UPDATE asignaturas SET color = %s WHERE id = %s;", (color, asig['id']))
            total_asignaturas_actualizadas += 1
            
    conn.commit()
    print(f"Colors successfully uniquely assigned per course. Updated {total_asignaturas_actualizadas} asignaturas.")
    
    cursor.close()
    conn.close()
except Exception as e:
    print(f"Error: {e}")
