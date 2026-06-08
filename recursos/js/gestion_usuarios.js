const modal = document.getElementById("modalUsuario");
        
        function mostrarMensaje(texto, es_error) {
            const caja = document.getElementById('caja_notificacion');
            caja.style.display = 'block';
            caja.innerText = texto;
            caja.className = es_error ? 'notificacion error' : 'notificacion exito';
            setTimeout(() => { caja.style.display = 'none'; }, 5000);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function abrirModalUsuario() {
            document.getElementById("formUsuario").reset();
            document.getElementById("editIdInput").value = "";
            document.getElementById("modalTitle").innerText = "Nuevo Usuario";
            
            document.getElementById("nombreUsuarioInput").disabled = false;
            document.getElementById("passwordInput").required = true;
            document.getElementById("helpPassword").style.display = "none";
            document.getElementById("labelPassword").innerText = "Contraseña";
            
            modal.style.display = "flex";
        }

        function prepararEdicion(usuario) {
            document.getElementById("formUsuario").reset();
            document.getElementById("editIdInput").value = usuario.id;
            document.getElementById("modalTitle").innerText = "Editar Usuario";
            
            document.getElementById("nombreUsuarioInput").value = usuario.nombre_usuario;
            // Opcional: Impedir cambiar el username una vez creado
            // document.getElementById("nombreUsuarioInput").disabled = true; 
            
            document.getElementById("nombreCompletoInput").value = usuario.nombre_completo;
            document.getElementById("dniInput").value = usuario.dni || "";
            document.getElementById("emailInput").value = usuario.email || "";
            document.getElementById("rolInput").value = usuario.rol;
            
            document.getElementById("passwordInput").required = false;
            document.getElementById("helpPassword").style.display = "block";
            document.getElementById("labelPassword").innerText = "Nueva Contraseña (Opcional)";
            
            modal.style.display = "flex";
        }

        function cerrarModal() {
            modal.style.display = "none";
        }

        function guardarUsuario(e) {
            e.preventDefault();
            const id = document.getElementById("editIdInput").value;
            const isEdit = id !== "";
            const url = isEdit ? `acciones/admin_editar_usuario.php?id=${id}` : `acciones/admin_crear_usuario.php`;
            
            const datos = {
                nombre_usuario: document.getElementById("nombreUsuarioInput").value,
                nombre_completo: document.getElementById("nombreCompletoInput").value,
                dni: document.getElementById("dniInput").value,
                email: document.getElementById("emailInput").value,
                rol: document.getElementById("rolInput").value,
                contrasena: document.getElementById("passwordInput").value
            };

            fetch(url, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(datos)
            })
            .then(res => res.json())
            .then(datos => {
                if (datos.exito) {
                    window.location.reload();
                } else {
                    cerrarModal();
                    mostrarMensaje(datos.mensaje || "Ocurrió un error", true);
                }
            })
            .catch(err => {
                cerrarModal();
                mostrarMensaje("Error de conexión", true);
            });
        }

        function confirmarEliminar(id) {
            mostrarConfirmacionGlobal(
                'Eliminar Usuario',
                '¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer y fallará si el usuario tiene datos asociados.',
                function() {
                    fetch(`acciones/admin_borrar_usuario.php?id=${id}`, { method: 'GET' })
                    .then(res => res.json())
                    .then(datos => {
                        if (datos.exito) {
                            window.location.reload();
                        } else {
                            mostrarMensaje(datos.mensaje || "Error al eliminar", true);
                        }
                    })
                    .catch(() => mostrarMensaje("Error de conexión", true));
                }
            );
        }

        function filtrarUsuarios() {
            const filtro = document.getElementById('filtroRol').value.toLowerCase();
            const filas = document.querySelectorAll('#tablaUsuarios tbody tr');
            
            filas.forEach(fila => {
                // En esta tabla, el rol está en la 5ª columna (índice 4) si el usuario existe
                const rolCell = fila.querySelector('td:nth-child(5)');
                if (rolCell) {
                    const rol = rolCell.textContent.trim().toLowerCase();
                    if (filtro === 'todos' || rol === filtro) {
                        fila.style.display = '';
                    } else {
                        fila.style.display = 'none';
                    }
                }
            });
        }