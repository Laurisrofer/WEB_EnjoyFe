const meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
        let fecha_actual = new Date();
        let dia_actual = fecha_actual.getDate();
        let mes_actual = fecha_actual.getMonth();
        let anio_actual = fecha_actual.getFullYear();
        const modal = document.getElementById("modalEvento");
        const fecha_input = document.getElementById("fechaInput");

        function generar_calendario(mes, anio) {
            const body_calendario = document.getElementById("calendar-body");
            const texto_mes_anio = document.getElementById("mes-anio");
            body_calendario.innerHTML = "";
            texto_mes_anio.innerText = meses[mes] + " " + anio;
            let primer_dia = new Date(anio, mes, 1).getDay();
            primer_dia = primer_dia === 0 ? 6 : primer_dia - 1; 
            const dias_en_mes = new Date(anio, mes + 1, 0).getDate();
            for (let i = 0; i < primer_dia; i++) {
                const celda = document.createElement("div");
                celda.classList.add("calendar-day", "empty");
                body_calendario.appendChild(celda);
            }
            for (let i = 1; i <= dias_en_mes; i++) {
                const celda = document.createElement("div");
                celda.classList.add("calendar-day", "filled");
                celda.innerText = i;
                celda.onclick = () => {
                    document.getElementById("editIdInput").value = "";
                    document.getElementById("formEvento").reset();
                    const mes_format = (mes + 1).toString().padStart(2, '0');
                    const dia_str = i.toString().padStart(2, '0');
                    fecha_input.value = `${anio}-${mes_format}-${dia_str}`;
                    if(document.getElementById("modalTitle")) document.getElementById("modalTitle").innerText = "Nuevo Evento";
                    modal.style.display = "flex";
                };
                body_calendario.appendChild(celda);
            }
        }
        function cambiar_mes(dir) {
            mes_actual += dir;
            if (mes_actual < 0) { mes_actual = 11; anio_actual--; }
            else if (mes_actual > 11) { mes_actual = 0; anio_actual++; }
            generar_calendario(mes_actual, anio_actual);
        }
        function preparar_edicion(id, titulo, fecha, hora, tipo, cursoId = "") {
            if(document.getElementById("modalTitle")) document.getElementById("modalTitle").innerText = "Editar Evento";
            document.getElementById("editIdInput").value = id;
            document.getElementById("tituloInput").value = titulo;
            document.getElementById("horaInput").value = hora;
            document.getElementById("tipoInput").value = tipo;
            const partes = fecha.split('/');
            document.getElementById("fechaInput").value = `${partes[2]}-${partes[1]}-${partes[0]}`;
            if (document.getElementById("cursoInput")) {
                document.getElementById("cursoInput").value = cursoId || "";
            }
            const tipoGroup = document.getElementById("tipoGroup");
            if (tipoGroup) tipoGroup.style.display = 'block';
            document.getElementById("horaInput").parentElement.style.display = 'block';
            document.getElementById("horaInput").required = true;

            toggleCamposEvento();
            modal.style.display = "flex";
        }

        function abrirModalAnuncio() {
            if(document.getElementById("modalTitle")) document.getElementById("modalTitle").innerText = "Nuevo Anuncio";
            document.getElementById("formEvento").reset();
            document.getElementById("editIdInput").value = "";
            const mes_format = (mes_actual + 1).toString().padStart(2, '0');
            const dia_str = dia_actual.toString().padStart(2, '0');
            fecha_input.value = `${anio_actual}-${mes_format}-${dia_str}`;
            document.getElementById("tipoInput").value = "anuncio";
            
            const tipoGroup = document.getElementById("tipoGroup");
            if (tipoGroup) tipoGroup.style.display = 'none';
            document.getElementById("horaInput").parentElement.style.display = 'none';
            document.getElementById("horaInput").required = false;

            toggleCamposEvento();
            modal.style.display = "flex";
        }

        function preparar_edicion_anuncio(id, titulo, descripcion, fecha, cursoId = "") {
            if(document.getElementById("modalTitle")) document.getElementById("modalTitle").innerText = "Editar Anuncio";
            document.getElementById("formEvento").reset();
            document.getElementById("editIdInput").value = id;
            document.getElementById("tituloInput").value = titulo;
            const partes = fecha.split('/');
            document.getElementById("fechaInput").value = `${partes[2]}-${partes[1]}-${partes[0]}`;
            document.getElementById("horaInput").value = "00:00";
            document.getElementById("tipoInput").value = "anuncio";
            if (document.getElementById("descInput")) {
                document.getElementById("descInput").value = descripcion;
            }
            if (document.getElementById("cursoInput")) {
                document.getElementById("cursoInput").value = cursoId || "";
            }

            const tipoGroup = document.getElementById("tipoGroup");
            if (tipoGroup) tipoGroup.style.display = 'none';
            document.getElementById("horaInput").parentElement.style.display = 'none';
            document.getElementById("horaInput").required = false;

            toggleCamposEvento();
            modal.style.display = "flex";
        }

        function toggleCamposEvento() {
            const tipo = document.getElementById("tipoInput").value;
            const cursoGroup = document.getElementById("cursoGroup");
            const descGroup = document.getElementById("descGroup");
            const cursoInput = document.getElementById("cursoInput");
            
            if (cursoGroup) {
                if (tipo === 'anuncio') {
                    cursoGroup.style.display = "block";
                    if (!window.EnjoyfeConfig.esAdmin) {
                        if(document.querySelector('.opcion-ninguno')) document.querySelector('.opcion-ninguno').style.display = 'none';
                        if(document.querySelector('.opcion-selecciona')) document.querySelector('.opcion-selecciona').style.display = 'block';
                        if(cursoInput) {
                            cursoInput.required = true;
                            if(cursoInput.value === "") cursoInput.value = "";
                        }
                    }
                } else if (tipo === 'entrega' || tipo === 'examen') {
                    cursoGroup.style.display = "block";
                    if (!window.EnjoyfeConfig.esAdmin) {
                        if(document.querySelector('.opcion-ninguno')) document.querySelector('.opcion-ninguno').style.display = 'none';
                        if(document.querySelector('.opcion-selecciona')) document.querySelector('.opcion-selecciona').style.display = 'block';
                        if(cursoInput) cursoInput.required = true;
                    }
                } else {
                    cursoGroup.style.display = "none";
                    if (!window.EnjoyfeConfig.esAdmin) {
                        if(document.querySelector('.opcion-ninguno')) document.querySelector('.opcion-ninguno').style.display = 'block';
                        if(document.querySelector('.opcion-selecciona')) document.querySelector('.opcion-selecciona').style.display = 'none';
                        if(cursoInput) {
                            cursoInput.required = false;
                            cursoInput.value = "";
                        }
                    }
                }
            }
            if (descGroup) {
                if (tipo === 'anuncio') {
                    descGroup.style.display = "block";
                } else {
                    descGroup.style.display = "none";
                }
            }
        }

        function cerrar_modal() { modal.style.display = "none"; document.getElementById("formEvento").reset(); document.getElementById("editIdInput").value = ""; }
        function guardar_evento(e) {
            e.preventDefault();
            const id = document.getElementById("editIdInput").value;
            const url = id !== "" ? "acciones/editar_evento.php" : "acciones/guardar_evento.php";
            const data = {
                id: id,
                titulo: document.getElementById("tituloInput").value,
                fecha: document.getElementById("fechaInput").value,
                hora: document.getElementById("horaInput").value,
                tipo: document.getElementById("tipoInput").value
            };
            const cursoInput = document.getElementById("cursoInput");
            if (cursoInput && cursoInput.value) {
                data.id_curso = cursoInput.value;
            }
            const descInput = document.getElementById("descInput");
            if (descInput && descInput.value) {
                data.descripcion = descInput.value;
            }

            fetch(url, { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(data) })
            .then(() => window.location.reload());
        }
        function actualizarEstadosAnunciosDashboard() {
            let readNotifs = JSON.parse(localStorage.getItem('read_notifications') || '[]');
            document.querySelectorAll('.evento-item.anuncio').forEach(el => {
                const id = el.getAttribute('data-id');
                const notifId = `ann_${id}`;
                const estadoEl = document.getElementById(`anuncio_estado_${id}`);
                if (estadoEl) {
                    if (readNotifs.includes(notifId)) {
                        estadoEl.innerHTML = '<span class="estado-leido">Leído</span>';
                        el.style.opacity = '0.7';
                    } else {
                        estadoEl.innerHTML = '<span class="estado-nuevo">🔵 Nuevo</span>';
                        el.style.opacity = '1';
                    }
                }
            });
        }

        function verDetalleAnuncioDashboard(element) {
            const id = element.getAttribute('data-id');
            const titulo = element.getAttribute('data-titulo');
            const descripcion = element.getAttribute('data-descripcion');
            const fecha = element.getAttribute('data-fecha');
            
            if (typeof marcarComoLeida === 'function') {
                marcarComoLeida(`ann_${id}`);
            }
            
            actualizarEstadosAnunciosDashboard();
            
            if (typeof mostrarModalNotif === 'function') {
                mostrarModalNotif(titulo, descripcion, fecha, '📢 Anuncio del centro');
            } else {
                alert(`📢 ANUNCIO DEL CENTRO\n\nTítulo: ${titulo}\n\nDetalle: ${descripcion}`);
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            generar_calendario(mes_actual, anio_actual);
            actualizarEstadosAnunciosDashboard();
        });