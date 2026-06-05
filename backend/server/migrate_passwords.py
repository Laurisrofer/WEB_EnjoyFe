from application import app, db
from models.Usuario import Usuario
from werkzeug.security import generate_password_hash

def migrar_contrasenas():
    with app.app_context():
        usuarios = Usuario.query.all()
        modificados = 0
        
        for u in usuarios:
            # Si el hash no empieza por scrypt:, significa que es texto plano, o un hash SHA-256 antiguo.
            if not u.password_hash.startswith('scrypt:'):
                # Reseteamos todas las contraseñas antiguas/texto plano a "1234" usando el nuevo método seguro.
                u.password_hash = generate_password_hash('1234')
                modificados += 1
                print(f"Migrada contraseña de: {u.nombre_usuario}")
                
        if modificados > 0:
            db.session.commit()
            print(f"¡Éxito! Se han migrado {modificados} contraseñas.")
        else:
            print("Todas las contraseñas ya utilizan scrypt. No hay nada que migrar.")

if __name__ == '__main__':
    migrar_contrasenas()
