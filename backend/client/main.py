import sys
from getpass import getpass
from api import ApiClient
from tabulate import tabulate

cliente = ApiClient()

def cabecera():
    rol_str = cliente.rol.upper() if cliente.rol else "SIN ROL"
    usuario_str = cliente.usuario_actual if cliente.usuario_actual else "DESCONOCIDO"
    print("\n" + "="*60)
    print(f"   ENJOYFE CLI - {usuario_str} ({rol_str})")
    print("="*60)

# ==========================================
#  FUNCIONES COMUNES (Visualización)
# ==========================================

def ver_cursos():
    print("\n📚 OFERTA EDUCATIVA")
    cursos = cliente.obtener_cursos()
    if not cursos:
        print("No hay cursos disponibles.")
        return
    
    tabla = []
    for c in cursos:
        lista_asigs = []
        for a in c.get('asignaturas', []):
            # Resolvemos el nombre del profesor
            nombre_prof = a.get('nombre_profesor', a.get('profesor_id', 'Sin Prof.'))
            
            # Formateamos el horario
            horario_str = " | ".join([f"{h['dia']} {h['inicio']}-{h['fin']}" for h in a.get('horario', [])])
            
            # Construimos la celda de la asignatura
            info_asig = f"• {a['nombre']}\n  Prof: {nombre_prof}\n  Horario: {horario_str}"
            lista_asigs.append(info_asig)
        
        # Unimos todas las asignaturas con separadores
        texto_asigs = "\n-------------------\n".join(lista_asigs)
        tabla.append([c['id'], c['nombre'], texto_asigs])
        
    print(tabulate(tabla, headers=["ID", "Curso", "Asignaturas, Profes y Horarios"], tablefmt="fancy_grid"))

def ver_matriculas_notas():
    datos = cliente.obtener_matriculas()
    if not datos:
        print("\n📭 No hay datos disponibles.")
        # Quitamos el return inmediato o ponemos pausa
        input("[Enter] para continuar...")
        return

    print(f"\n🎓 LISTADO ACADÉMICO ({len(datos)} registros)")
    tabla = []
    for d in datos:
        tabla.append([d['id'], d['alumno'], d['curso'], d['asignatura'], d['nota'], len(d['asistencias'])])
    
    print(tabulate(tabla, headers=["ID MATRÍCULA", "Alumno", "Curso", "Asignatura", "Nota", "Faltas"], tablefmt="simple"))
    # PAUSA IMPORTANTE:
    # Solo pausamos si no estamos dentro de otra función de selección (opcional, pero recomendado)

def menu_mensajeria():
    while True:
        print("\n📨 MENSAJERÍA INTERNA")
        print("1. Bandeja de Entrada")
        print("2. Redactar Mensaje (Nuevo)")
        print("0. Volver")
        op = input(">> Elige una opción: ")
        if not op: continue  # Si el usuario solo pulsa Enter, vuelve a pedir la opción
        
        if op == "1":
            msgs = cliente.obtener_mensajes()
            if not msgs:
                print("\n📭 Bandeja de entrada vacía.")
            else:
                # Mostramos los mensajes recibidos
                t = [[m['fecha'], m['de'], m['asunto'], m['cuerpo'][:40]+"..."] for m in msgs]
                print("\n[MENSÁJES RECIBIDOS]")
                print(tabulate(t, headers=["Fecha", "De", "Asunto", "Contenido"], tablefmt="grid"))
                input("\n[Enter] para volver al menú de mensajería...")

        elif op == "2":
            print("\n--- REDACTAR NUEVO MENSAJE ---")
            
            # --- CAMBIO: Usamos obtener_contactos en lugar de obtener_usuarios ---
            print("\n🔍 Buscando contactos...")
            contactos = cliente.obtener_contactos()
            
            if not contactos:
                print("⚠️ No se pudo cargar la lista de contactos.")
                dest = input("Nombre de usuario del destinatario: ")
            else:
                tabla_contactos = []
                for u in contactos:
                    if u['nombre_usuario'] != cliente.usuario_actual:
                        tabla_contactos.append([
                            u['nombre_usuario'], 
                            u.get('nombre_completo', '---'), 
                            u['rol'].upper()
                        ])
                
                print("\n[👥 CONTACTOS DISPONIBLES]")
                print(tabulate(tabla_contactos, headers=["Usuario", "Nombre", "Rol"], tablefmt="simple"))
                dest = input("\n>> Escribe el 'Usuario' del destinatario: ")
            # --- FIN CAMBIO ---

            asunto = input("Asunto: ")
            cuerpo = input("Mensaje: ")
            
            if not dest or not asunto or not cuerpo:
                print("❌ Error: Todos los campos son obligatorios.")
                continue

            ok, msg = cliente.enviar_mensaje(dest, asunto, cuerpo)
            if ok:
                print(f"\n✅ {msg}")
            else:
                print(f"\n❌ {msg}")
            input("\n[Enter] para continuar...")

        elif op == "0":
            break

# ==========================================
#  FUNCIONES DE DIRECTOR (ADMIN)
# ==========================================

def gestion_usuarios():
    while True:
        print("\n👥 GESTIÓN DE USUARIOS")
        usuarios = cliente.obtener_usuarios()
        mini_tabla = [[u['id'], u['nombre_usuario'], u['rol'], u.get('nombre_completo')] for u in usuarios]
        print(tabulate(mini_tabla, headers=["ID", "Usuario", "Rol", "Nombre"], tablefmt="simple"))
        
        print("\n1. Nuevo Usuario")
        print("2. Modificar Usuario")
        print("3. Borrar Usuario")
        print("0. Volver")
        op = input(">> ")
        
        if op == "1":
            print("\n--- ALTA ---")
            u = input("Login: ")
            p = getpass("Password: ")
            n = input("Nombre Completo: ")
            e = input("Email: ")
            r = input("Rol (admin/profesor/alumno): ")
            ok, msg = cliente.crear_usuario(u, p, n, e, r)
            print(f"✅ {msg}" if ok else f"❌ {msg}")

        elif op == "2":
            print("\n--- MODIFICAR ---")
            id_u = input("ID del usuario a modificar: ")
            print("(Deja en blanco lo que no quieras cambiar)")
            n = input("Nuevo Nombre Completo: ")
            e = input("Nuevo Email: ")
            r = input("Nuevo Rol: ")
            p = getpass("Nueva Password: ")
            
            datos = {}
            if n: datos['nombre_completo'] = n
            if e: datos['email'] = e
            if r: datos['rol'] = r
            if p: datos['contrasena'] = p
            
            if datos:
                ok, msg = cliente.modificar_usuario(id_u, datos)
                print(f"✅ {msg}" if ok else f"❌ {msg}")
            else:
                print("⚠️ No has introducido cambios.")

        elif op == "3":
            id_u = input("ID a borrar: ")
            if input("¿Seguro? (s/n): ") == 's':
                ok, msg = cliente.borrar_usuario(id_u)
                print(f"✅ {msg}" if ok else f"❌ {msg}")
        elif op == "0": break

def wizard_curso():
    """Asistente paso a paso para crear la estructura compleja de un curso"""
    print("\n--- ASISTENTE DE CREACIÓN DE CURSO ---")
    nombre = input("Nombre del Curso: ")
    # Hemos quitado la petición de descripción
    asignaturas = []
    
    # Pre-cargamos la lista de profesores
    todos_usuarios = cliente.obtener_usuarios()
    lista_profesores = [u for u in todos_usuarios if u['rol'] == 'profesor']
    
    while True:
        add_asig = input(f"\n¿Añadir asignatura a '{nombre}'? (s/n): ")
        if add_asig.lower() != 's': break
        
        nom_asig = input("  -> Nombre Asignatura: ")
        
        # LISTA DE PROFESORES
        print("\n  [👩‍🏫 LISTA DE PROFESORES DISPONIBLES]")
        if not lista_profesores:
            print("  ⚠️ No hay profesores registrados.")
            prof_id = input("  -> Introduce ID manualmente: ")
            nombre_profesor = "Desconocido"
        else:
            tabla_prof = [[p['id'], p.get('nombre_completo', p['nombre_usuario'])] for p in lista_profesores]
            print(tabulate(tabla_prof, headers=["ID", "Nombre Profesor"], tablefmt="simple"))
            
            prof_id = input("  -> Copia el ID del Profesor elegido: ")
            
            # Buscamos el nombre para guardarlo
            prof_elegido = next((p for p in lista_profesores if p['id'] == prof_id), None)
            nombre_profesor = prof_elegido.get('nombre_completo', prof_elegido['nombre_usuario']) if prof_elegido else "Desconocido"
        
        horarios = []
        while True:
            add_hor = input("    ¿Añadir horario? (s/n): ")
            if add_hor.lower() != 's': break
            dia = input("      Día (Lunes/Martes...): ")
            ini = input("      Hora Inicio (08:00): ")
            fin = input("      Hora Fin (09:00): ")
            horarios.append({"dia": dia, "inicio": ini, "fin": fin})
            
        asignaturas.append({
            "nombre": nom_asig,
            "profesor_id": prof_id, 
            "nombre_profesor": nombre_profesor,
            "horario": horarios
        })
    
    # Enviamos descripción vacía para cumplir con la API
    return {"nombre": nombre, "descripcion": "", "asignaturas": asignaturas}

def wizard_matricula():
    print("\n--- NUEVA MATRÍCULA ---")
    
    # 1. Elegir Alumno
    usuarios = cliente.obtener_usuarios()
    alumnos = [u for u in usuarios if u['rol'] == 'alumno']
    if not alumnos:
        print("⚠️ No hay alumnos registrados.")
        return
        
    print("\n[🧑‍🎓 LISTA DE ALUMNOS]")
    print(tabulate([[u['id'], u['nombre_usuario']] for u in alumnos], headers=["ID", "Alumno"]))
    id_alum = input(">> Copia ID Alumno: ")

    # 2. Elegir Curso
    cursos = cliente.obtener_cursos()
    if not cursos:
        print("⚠️ No hay cursos.")
        return
        
    print("\n[📚 LISTA DE CURSOS]")
    print(tabulate([[c['id'], c['nombre']] for c in cursos], headers=["ID", "Curso"]))
    id_curso = input(">> Copia ID Curso: ")
    
    # 3. Elegir Asignatura (del curso seleccionado)
    curso_elegido = next((c for c in cursos if str(c['id']) == id_curso), None)
    if not curso_elegido:
        print("ID de curso incorrecto.")
        return

    print(f"\n[📘 ASIGNATURAS DE {curso_elegido['nombre']}]")
    asigs = curso_elegido.get('asignaturas', [])
    for i, a in enumerate(asigs):
        print(f"{i+1}. {a['nombre']}")
    
    num_asig = input(">> Elige número de asignatura: ")
    try:
        nom_asig = asigs[int(num_asig)-1]['nombre']
        
        # Enviamos al servidor
        ok, msg = cliente.matricular_alumno(id_alum, id_curso, nom_asig)
        print(f"✅ {msg}" if ok else f"❌ {msg}")
        
    except (ValueError, IndexError):
        print("Selección incorrecta.")

def gestion_matriculas():
    """Menú CRUD completo de matrículas"""
    while True:
        print("\n🎓 GESTIÓN DE MATRÍCULAS")
        print("1. Ver todas las matrículas")
        print("2. Matricular Alumno (Nueva)")
        print("3. Modificar Matrícula (Nota o Asignatura)")
        print("4. Eliminar Matrícula")
        print("0. Volver")
        op = input(">> ")
        
        if op == "1": 
            ver_matriculas_notas()
            input("[Enter] para continuar...")
            
        elif op == "2": 
            wizard_matricula()
            
        elif op == "3":
            print("\n--- MODIFICAR MATRÍCULA ---")
            ver_matriculas_notas() # Mostramos lista para ver IDs
            print("")
            id_m = input("Copia el ID MATRÍCULA a modificar: ")
            
            print("¿Qué quieres cambiar?")
            print("1. Nota")
            print("2. Asignatura")
            sub_op = input(">> ")
            
            datos = {}
            if sub_op == "1":
                try:
                    datos['nota'] = float(input("Nueva Nota: "))
                except:
                    print("❌ Error: La nota debe ser un número.")
                    continue
                    
            elif sub_op == "2":
                # --- NUEVA LÓGICA DE SELECCIÓN DE ASIGNATURA ---
                cursos = cliente.obtener_cursos()
                if not cursos:
                    print("⚠️ No hay cursos disponibles.")
                    continue
                    
                print("\n[📚 LISTA DE CURSOS]")
                print(tabulate([[c['id'], c['nombre']] for c in cursos], headers=["ID", "Curso"]))
                id_curso = input(">> Copia ID del nuevo Curso: ")
                
                # Buscamos el curso asegurándonos de convertir a str()
                curso_elegido = next((c for c in cursos if str(c['id']) == id_curso), None)
                
                if not curso_elegido:
                    print("❌ Curso no encontrado.")
                    continue

                print(f"\n[📘 ASIGNATURAS DE {curso_elegido['nombre']}]")
                asigs = curso_elegido.get('asignaturas', [])
                for i, a in enumerate(asigs):
                    print(f"{i+1}. {a['nombre']}")
                
                num_asig = input(">> Elige número de asignatura: ")
                try:
                    nom_asig = asigs[int(num_asig)-1]['nombre']
                    datos['curso_id'] = id_curso
                    datos['asignatura_nombre'] = nom_asig
                except (ValueError, IndexError):
                    print("❌ Selección incorrecta.")
                    continue
                # --- FIN NUEVA LÓGICA ---
                
            else:
                print("Opción no válida.")
                continue
                
            ok, msg = cliente.modificar_matricula(id_m, datos)
            print(f"✅ {msg}" if ok else f"❌ {msg}")
            
        elif op == "4":
            print("\n--- ELIMINAR MATRÍCULA ---")
            ver_matriculas_notas()
            print("")
            id_m = input("Copia el ID MATRÍCULA a borrar: ")
            if input("¿Estás seguro? (s/n): ").lower() == 's':
                ok, msg = cliente.borrar_matricula(id_m)
                print(f"✅ {msg}" if ok else f"❌ {msg}")
                
        elif op == "0": break

def gestion_cursos():
    while True:
        ver_cursos()
        print("\n🏫 GESTIÓN DE CURSOS")
        print("1. Crear Curso (Con Asignaturas y Horarios)")
        print("2. Modificar Curso (Sobrescribir datos)")
        print("3. Borrar Curso")
        print("0. Volver")
        op = input(">> ")
        
        if op == "1":
            print("\n--- NUEVO CURSO ---")
            datos = wizard_curso()
            ok, msg = cliente.crear_curso_completo(datos)
            print(f"✅ {msg}" if ok else f"❌ {msg}")
            
        elif op == "2":
            print("\n--- MODIFICAR CURSO ---")
            id_c = input("ID del curso a modificar: ")
            print("⚠️ AVISO: Esto sobrescribirá las asignaturas actuales.")
            datos = wizard_curso()
            ok, msg = cliente.modificar_curso(id_c, datos)
            print(f"✅ {msg}" if ok else f"❌ {msg}")
            
        elif op == "3":
            id_c = input("ID Curso a borrar: ")
            if input("¿Seguro? (s/n): ") == 's':
                ok, msg = cliente.borrar_curso(id_c)
                print(f"✅ {msg}" if ok else f"❌ {msg}")
        elif op == "0": break

# ==========================================
#  FUNCIONES DE PROFESOR
# ==========================================

def poner_nota():
    # 1. Primero enseñamos la lista para que vea los IDs
    print("\n--- SELECCIONA UN ALUMNO ---")
    datos = cliente.obtener_matriculas() # El servidor ya filtra y manda solo MIS alumnos
    
    if not datos:
        print("📭 No tienes alumnos matriculados en tus asignaturas.")
        input("[Enter] para volver...")
        return

    # Mostramos tabla resumida
    tabla = [[d['id'], d['alumno'], d['asignatura'], d['nota']] for d in datos]
    print(tabulate(tabla, headers=["ID MATRÍCULA", "Alumno", "Asignatura", "Nota Actual"], tablefmt="simple"))
    
    print("\n📝 CALIFICAR")
    id_m = input("Copia el ID MATRÍCULA: ")
    try:
        nota = float(input("Nueva Nota (0-10): "))
        ok, msg = cliente.calificar_alumno(id_m, nota)
        print(f"✅ {msg}" if ok else f"❌ {msg}")
    except ValueError:
        print("❌ La nota debe ser un número.")
    input("[Enter] para continuar...")

def poner_falta():
    print("\n--- SELECCIONA UN ALUMNO ---")
    datos = cliente.obtener_matriculas()
    
    if not datos:
        print("📭 No tienes alumnos matriculados.")
        input("[Enter] para volver...")
        return

    tabla = [[d['id'], d['alumno'], d['asignatura'], len(d['asistencias'])] for d in datos]
    print(tabulate(tabla, headers=["ID MATRÍCULA", "Alumno", "Asignatura", "Num Faltas"], tablefmt="simple"))

    print("\n🚫 REGISTRAR AUSENCIA")
    id_m = input("Copia el ID MATRÍCULA: ")
    tipo = input("Tipo (falta/retraso): ")
    
    # Eliminamos el input de observaciones y lo enviamos vacío
    ok, msg = cliente.poner_falta(id_m, tipo, "") 
    
    print(f"✅ {msg}" if ok else f"❌ {msg}")
    input("[Enter] para continuar...")

# ==========================================
#  MENÚS PRINCIPALES
# ==========================================

def menu_director():
    while True:
        cabecera()
        print("1. Gestión de Usuarios (Altas/Bajas/Modificar)")
        print("2. Gestión de Cursos (Asignaturas y Horarios)")
        print("3. Gestión de Matrículas (Matricular y Ver Expedientes)") # <--- CAMBIO
        print("4. Mensajería")
        print("0. Salir")
        op = input("\nElige opción: ")
        if not op: continue  # Si el usuario solo pulsa Enter, vuelve a pedir la opción
        
        if op == "1": gestion_usuarios()
        elif op == "2": gestion_cursos()
        elif op == "3": gestion_matriculas() # <--- NUEVA FUNCIÓN
        elif op == "4": menu_mensajeria()
        elif op == "0": sys.exit()

def menu_profesor():
    while True:
        cabecera()
        print("1. Mis Cursos y Alumnos")
        print("2. Calificar Alumnos")
        print("3. Control de Asistencia")
        print("4. Mensajería")
        print("0. Salir")
        op = input("\nElige opción: ")
        if not op: continue  # Si el usuario solo pulsa Enter, vuelve a pedir la opción
        
        if op == "1": 
            ver_matriculas_notas()
            input("[Enter] para volver...")
        elif op == "2": poner_nota()
        elif op == "3": poner_falta()
        elif op == "4": menu_mensajeria()
        elif op == "0": sys.exit()

def menu_alumno():
    while True:
        cabecera()
        print("1. Mi Expediente")
        print("2. Oferta de Cursos")
        print("3. Mensajería")
        print("0. Salir")
        op = input("\nElige opción: ")
        if not op: continue  # Si el usuario solo pulsa Enter, vuelve a pedir la opción
        
        if op == "1": ver_matriculas_notas()
        elif op == "2": ver_cursos()
        elif op == "3": menu_mensajeria()
        elif op == "0": sys.exit()

# ==========================================
#  ARRANQUE
# ==========================================
def iniciar():
    while True:
        print("\n🔐 LOGIN ENJOYFE")
        u = input("Usuario: ")
        p = getpass("Contraseña: ")
        ok, msg = cliente.login(u, p)
        
        if ok:
            if cliente.rol == 'admin': menu_director()
            elif cliente.rol == 'profesor': menu_profesor()
            elif cliente.rol == 'alumno': menu_alumno()
            else: print("⚠️ Rol desconocido.")
        else:
            print(f"❌ {msg}")

if __name__ == "__main__":
    try:
        iniciar()
    except KeyboardInterrupt:
        print("\n👋 Saliendo...")