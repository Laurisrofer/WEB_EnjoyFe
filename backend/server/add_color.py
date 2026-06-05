import mysql.connector

try:
    conn = mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="Enjoyfe"
    )
    cursor = conn.cursor()
    
    try:
        cursor.execute("ALTER TABLE asignaturas ADD COLUMN color VARCHAR(20) DEFAULT '#3498db';")
        print("Column 'color' added successfully.")
    except mysql.connector.Error as err:
        if err.errno == 1060:
            print("Column 'color' already exists.")
        else:
            print(f"Error: {err}")
    
    conn.commit()
    conn.close()
except Exception as e:
    print(f"Connection error: {e}")
