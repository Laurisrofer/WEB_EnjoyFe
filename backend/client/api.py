import requests

URL_BASE = "http://localhost:5000"

class ApiClient:
    def __init__(self):
        self.token = None
        self.rol = None
        self.usuario_actual = None

    def _get_headers(self):
        return {"Authorization": f"Bearer {self.token}"}

    # --- AUTENTICACIÓN ---
    def login(self, usuario, contrasena):
        try:
            resp = requests.post(f"{URL_BASE}/auth/login", json={"nombre_usuario": usuario, "contrasena": contrasena})
            if resp.status_code == 200:
                datos = resp.json()
                self.token = datos.get('token')
                self.rol = datos.get('rol')
                self.usuario_actual = usuario
                return True, "Login correcto"
            elif resp.status_code == 401:
                return False, "Usuario o contraseña incorrectos"
            else:
                return False, f"Error: {resp.status_code}"
        except:
            return False, "Error de conexión"

    # --- USUARIOS (Solo Director) ---
    def obtener_usuarios(self):
        resp = requests.get(f"{URL_BASE}/usuarios", headers=self._get_headers())
        return resp.json() if resp.status_code == 200 else []

    def crear_usuario(self, usuario, password, nombre, email, rol):
        datos = {
            "nombre_usuario": usuario, "contrasena": password,
            "nombre_completo": nombre, "email": email, "rol": rol
        }
        resp = requests.post(f"{URL_BASE}/usuarios", json=datos, headers=self._get_headers())
        return resp.status_code == 201, resp.json().get('mensaje')

    def modificar_usuario(self, id_usuario, datos_actualizados):
        """Envía solo los datos que han cambiado"""
        resp = requests.put(f"{URL_BASE}/usuarios/{id_usuario}", json=datos_actualizados, headers=self._get_headers())
        return resp.status_code == 200, resp.json().get('mensaje')

    def borrar_usuario(self, id_usuario):
        resp = requests.delete(f"{URL_BASE}/usuarios/{id_usuario}", headers=self._get_headers())
        return resp.status_code == 200, resp.json().get('mensaje')

    # --- CURSOS ---
    def obtener_cursos(self):
        resp = requests.get(f"{URL_BASE}/cursos", headers=self._get_headers())
        return resp.json() if resp.status_code == 200 else []

    def crear_curso_completo(self, datos_curso):
        """Recibe el objeto completo con asignaturas y horarios"""
        resp = requests.post(f"{URL_BASE}/cursos", json=datos_curso, headers=self._get_headers())
        return resp.status_code == 201, resp.json().get('mensaje')

    def modificar_curso(self, id_curso, datos_curso):
        resp = requests.put(f"{URL_BASE}/cursos/{id_curso}", json=datos_curso, headers=self._get_headers())
        return resp.status_code == 200, resp.json().get('mensaje')

    def borrar_curso(self, id_curso):
        resp = requests.delete(f"{URL_BASE}/cursos/{id_curso}", headers=self._get_headers())
        return resp.status_code == 200, resp.json().get('mensaje')

    # --- ACADÉMICO ---
    def obtener_matriculas(self):
        resp = requests.get(f"{URL_BASE}/academico/matriculas", headers=self._get_headers())
        return resp.json() if resp.status_code == 200 else []

    def poner_falta(self, id_matricula, tipo, observaciones):
        datos = {"id_matricula": id_matricula, "tipo": tipo, "observaciones": observaciones}
        resp = requests.post(f"{URL_BASE}/academico/asistencia", json=datos, headers=self._get_headers())
        return resp.status_code == 200, resp.json().get('mensaje')

    def calificar_alumno(self, id_matricula, nota):
        datos = {"id_matricula": id_matricula, "nota": nota}
        resp = requests.put(f"{URL_BASE}/academico/calificar", json=datos, headers=self._get_headers())
        return resp.status_code == 200, resp.json().get('mensaje')
    
    def matricular_alumno(self, id_alumno, id_curso, nombre_asignatura):
        datos = {
            "alumno_id": id_alumno, 
            "curso_id": id_curso, 
            "asignatura_nombre": nombre_asignatura
        }
        resp = requests.post(f"{URL_BASE}/academico/matriculas", json=datos, headers=self._get_headers())
        return resp.status_code == 201, resp.json().get('mensaje')
    
    def modificar_matricula(self, id_matricula, datos):
        """Permite cambiar nota o asignatura"""
        resp = requests.put(f"{URL_BASE}/academico/matriculas/{id_matricula}", json=datos, headers=self._get_headers())
        return resp.status_code == 200, resp.json().get('mensaje')

    def borrar_matricula(self, id_matricula):
        resp = requests.delete(f"{URL_BASE}/academico/matriculas/{id_matricula}", headers=self._get_headers())
        return resp.status_code == 200, resp.json().get('mensaje')

    # --- MENSAJERÍA ---
    def obtener_contactos(self):
        """Obtiene la lista simplificada de usuarios para mensajería"""
        try:
            resp = requests.get(f"{URL_BASE}/usuarios/contactos", headers=self._get_headers())
            return resp.json() if resp.status_code == 200 else []
        except:
            return []
        
    def obtener_mensajes(self):
        resp = requests.get(f"{URL_BASE}/mensajes", headers=self._get_headers())
        return resp.json() if resp.status_code == 200 else []

    def enviar_mensaje(self, destinatario, asunto, cuerpo):
        datos = {"destinatario_usuario": destinatario, "asunto": asunto, "cuerpo": cuerpo}
        resp = requests.post(f"{URL_BASE}/mensajes", json=datos, headers=self._get_headers())
        if resp.status_code == 201: return True, "Enviado"
        elif resp.status_code == 404: return False, "Usuario no encontrado"
        else: return False, "Error al enviar"