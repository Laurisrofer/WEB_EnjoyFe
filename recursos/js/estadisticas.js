let datosNotas = null;
    let datosAsistencias = null;
    let chartInstancia = null;

    function extraerEvalStat(actividad) {
        if (actividad.startsWith('[1] ')) return '1';
        if (actividad.startsWith('[2] ')) return '2';
        if (actividad.startsWith('[3] ')) return '3';
        if (actividad.startsWith('[1ª Eval] ')) return '1';
        if (actividad.startsWith('[2ª Eval] ')) return '2';
        if (actividad.startsWith('[3ª Eval] ')) return '3';
        const match = actividad.match(/^\[(\d+)[^\]]*\]\s*/);
        if (match) return match[1];
        return '-';
    }

    function getColorNota(nota) {
        if (nota < 5) return 'var(--danger-color)';
        if (nota < 7) return '#e6a817';
        return 'var(--success-color)';
    }

    function getColorAsistencia(pct) {
        if (pct < 75) return 'var(--danger-color)';
        if (pct < 90) return '#e6a817';
        return 'var(--success-color)';
    }

    function setRosco(elementId, porcentaje, color) {
        const el = document.getElementById(elementId);
        const bg = getComputedStyle(document.documentElement).getPropertyValue('--border-color').trim() || '#e0e0e0';
        el.style.background = `conic-gradient(${color} 0% ${porcentaje}%, ${bg} ${porcentaje}% 100%)`;
    }

    const esAdmin = <?php echo ($rol_usuario === 'admin') ? 'true' : 'false'; ?>;

    function cargarEstadisticas() {
        if (esAdmin) {
            fetch('acciones/admin_stats.php')
            .then(r => r.ok ? r.json() : null)
            .then(data => {
                document.getElementById('loading_stats').style.display = 'none';
                if (!data) return;
                
                document.getElementById('admin_stats_container').style.display = 'block';
                document.getElementById('admin_alumnos').innerText = data.total_alumnos || 0;
                document.getElementById('admin_profes').innerText = data.total_profesores || 0;
                document.getElementById('admin_cursos').innerText = data.total_cursos || 0;
                
                // Gráfico 1: Usuarios (Donut)
                new Chart(document.getElementById('chartUsuarios'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Alumnos', 'Profesores'],
                        datasets: [{
                            data: [data.total_alumnos || 0, data.total_profesores || 0],
                            backgroundColor: ['#3498db', '#e74c3c'],
                            borderWidth: 0
                        }]
                    },
                    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') } } } }
                });

                // Gráfico 2: Alumnos por curso (Barras)
                if (data.alumnos_por_curso) {
                    new Chart(document.getElementById('chartAlumnos'), {
                        type: 'bar',
                        data: {
                            labels: data.alumnos_por_curso.map(c => c.curso),
                            datasets: [{
                                label: 'Alumnos Matriculados',
                                data: data.alumnos_por_curso.map(c => c.cantidad),
                                backgroundColor: 'rgba(52, 152, 219, 0.7)',
                                borderColor: '#3498db',
                                borderWidth: 1
                            }]
                        },
                        options: { 
                            responsive: true, 
                            scales: { 
                                y: { beginAtZero: true, ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color'), stepSize: 1 } },
                                x: { ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') } }
                            },
                            plugins: { legend: { display: false } }
                        }
                    });
                }

                // Gráfico 3: Asistencia Media (Barras horizontales)
                if (data.asistencia_por_curso) {
                    new Chart(document.getElementById('chartAsistencia'), {
                        type: 'bar',
                        data: {
                            labels: data.asistencia_por_curso.map(c => c.curso),
                            datasets: [{
                                label: '% Asistencia Media',
                                data: data.asistencia_por_curso.map(c => c.porcentaje),
                                backgroundColor: data.asistencia_por_curso.map(c => getColorAsistencia(c.porcentaje)),
                                borderWidth: 0
                            }]
                        },
                        options: { 
                            responsive: true, 
                            indexAxis: 'y',
                            scales: { 
                                x: { beginAtZero: true, max: 100, ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') } },
                                y: { ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') } }
                            },
                            plugins: { legend: { display: false } }
                        }
                    });
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('loading_stats').innerHTML = '<div style="color:var(--danger-color)">Error al cargar estadísticas.</div>';
            });
        } else if (window.EnjoyfeConfig.esProfesor) {
            fetch('acciones/estadisticas_profesor.php')
            .then(r => r.ok ? r.json() : null)
            .then(data => {
                datosNotas = data; // Usaremos esto para guardar la info del profesor
                
                document.getElementById('loading_stats').style.display = 'none';
                document.getElementById('stats_container').style.display = 'block';

                const select = document.getElementById('stats_asig_select');
                if (data && data.length > 0) {
                    document.getElementById('curso_selector_wrapper').style.display = 'block';
                    const cursoSelect = document.getElementById('stats_curso_select');
                    const cursosMap = new Map();
                    data.forEach(a => {
                        if (a.id_curso && a.nombre_curso) {
                            cursosMap.set(a.id_curso, a.nombre_curso);
                        }
                    });
                    
                    cursosMap.forEach((nombre, id) => {
                        const opt = document.createElement('option');
                        opt.value = id;
                        opt.textContent = nombre;
                        cursoSelect.appendChild(opt);
                    });
                    
                    filtrarAsignaturasPorCurso();
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('loading_stats').innerHTML = '<div style="color:var(--danger-color)">Error al cargar estadísticas.</div>';
            });
        } else {
            Promise.all([
                fetch('acciones/gestion_notas.php?action=get_mis_notas').then(r => r.ok ? r.json() : null),
                fetch('acciones/gestion_asistencias.php?action=get_mis_asistencias').then(r => r.ok ? r.json() : null)
            ])
            .then(([notas, asistencias]) => {
                datosNotas = notas;
                datosAsistencias = asistencias;

                document.getElementById('loading_stats').style.display = 'none';
                document.getElementById('stats_container').style.display = 'block';

                // Poblar select con asignaturas desde notas
                const select = document.getElementById('stats_asig_select');
                if (notas && notas.asignaturas) {
                    notas.asignaturas.forEach(a => {
                        const opt = document.createElement('option');
                        opt.value = a.asignatura;
                        opt.textContent = a.asignatura;
                        select.appendChild(opt);
                    });
                }
            })
            .catch(err => {
                console.error(err);
                document.getElementById('loading_stats').innerHTML = '<div style="color:var(--danger-color)">Error al cargar estadísticas.</div>';
            });
        }
    }

    function filtrarAsignaturasPorCurso() {
        if (!window.EnjoyfeConfig.esProfesor) return;
        
        const idCurso = document.getElementById('stats_curso_select').value;
        const selectAsig = document.getElementById('stats_asig_select');
        selectAsig.innerHTML = '<option value="">-- Selecciona asignatura --</option>';
        
        if (datosNotas) {
            datosNotas.forEach(a => {
                if (!idCurso || String(a.id_curso) === String(idCurso)) {
                    const opt = document.createElement('option');
                    opt.value = a.id_asignatura;
                    opt.textContent = a.nombre_asignatura;
                    selectAsig.appendChild(opt);
                }
            });
        }
        actualizarRoscos();
    }

    function actualizarRoscos() {
        const asigVal = document.getElementById('stats_asig_select').value;

        if (!asigVal) {
            document.getElementById('roscos_wrapper').style.display = 'none';
            document.getElementById('stats_placeholder').style.display = 'block';
            return;
        }

        document.getElementById('stats_placeholder').style.display = 'none';
        document.getElementById('roscos_wrapper').style.display = 'block';

        if (window.EnjoyfeConfig.esProfesor) {
            // asigVal es id_asignatura
            const asig = datosNotas.find(a => String(a.id_asignatura) === String(asigVal));
            if (!asig) return;

            // Rosco Notas: Nota media de la clase
            const mediaClase = asig.nota_media || 0;
            const colorMedia = getColorNota(mediaClase);
            const pctMedia = (mediaClase / 10) * 100;
            
            setRosco('rosco_notas', pctMedia, colorMedia);
            document.getElementById('rosco_notas_valor').textContent = asig.total_alumnos > 0 ? mediaClase.toFixed(1) : '-';
            document.getElementById('rosco_notas_valor').style.color = colorMedia;
            document.getElementById('rosco_notas').parentElement.previousElementSibling.textContent = 'Nota media de la clase';
            document.getElementById('rosco_notas').querySelector('.rosco-label').textContent = 'sobre 10';
            document.getElementById('rosco_notas_detalle').innerHTML = `Alumnos evaluados: ${asig.total_alumnos} <br> Aprobados: ${asig.porcentaje_aprobados}%`;

            // Rosco Asistencia: % de asistencia media de la clase
            document.getElementById('asistencia_titulo').textContent = 'Asistencia global';
            document.getElementById('asistencia_label').textContent = 'asistencia';
            
            const pctAsist = asig.total_alumnos > 0 ? asig.porcentaje_asistencia : 0;
            const colorAsist = getColorAsistencia(pctAsist);
            
            setRosco('rosco_asistencia', pctAsist, colorAsist);
            document.getElementById('rosco_asistencia_valor').textContent = asig.total_alumnos > 0 ? pctAsist + '%' : '-';
            document.getElementById('rosco_asistencia_valor').style.color = colorAsist;
            document.getElementById('rosco_asistencia_detalle').innerHTML = `Faltas de la clase: ${asig.total_faltas_registradas} <br> Retrasos: ${asig.total_retrasos_registrados || 0}`;
            
            // Render Chart
            document.getElementById('chart_detalles_wrapper').style.display = 'block';
            document.getElementById('chart_detalles_titulo').innerText = 'Distribución de Notas';
            if (chartInstancia) chartInstancia.destroy();
            
            if (asig.distribucion_notas) {
                const dist = asig.distribucion_notas;
                chartInstancia = new Chart(document.getElementById('chartDetalles'), {
                    type: 'bar',
                    data: {
                        labels: ['Suspenso', 'Suficiente', 'Bien', 'Notable', 'Sobresaliente'],
                        datasets: [{
                            label: 'Nº Alumnos',
                            data: [dist.Suspenso, dist.Suficiente, dist.Bien, dist.Notable, dist.Sobresaliente],
                            backgroundColor: [
                                'rgba(231, 76, 60, 0.7)',
                                'rgba(241, 196, 15, 0.7)',
                                'rgba(52, 152, 219, 0.7)',
                                'rgba(46, 204, 113, 0.7)',
                                'rgba(39, 174, 96, 0.9)'
                            ],
                            borderColor: [
                                '#e74c3c', '#f1c40f', '#3498db', '#2ecc71', '#27ae60'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: { 
                        responsive: true, maintainAspectRatio: false,
                        scales: { y: { beginAtZero: true, ticks: { stepSize: 1, color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') } }, x: { ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') } } },
                        plugins: { legend: { display: false } }
                    }
                });
            }
            
        } else {
            // Lógica existente de Alumno
            const asigNombre = asigVal;
            // --- ROSCO NOTAS ---
            let media = 0;
            let detalleHtml = '';
            if (datosNotas && datosNotas.asignaturas) {
                const asig = datosNotas.asignaturas.find(a => a.asignatura === asigNombre);
                if (asig && asig.calificaciones && asig.calificaciones.length > 0) {
                    const notas = asig.calificaciones.map(c => c.nota).filter(n => n !== null);
                    media = notas.reduce((a, b) => a + b, 0) / notas.length;

                    // Medias por evaluación
                    let tags = [];
                    for (let ev = 1; ev <= 3; ev++) {
                        const notasEv = asig.calificaciones
                            .filter(c => extraerEvalStat(c.actividad) === String(ev))
                            .map(c => c.nota)
                            .filter(n => n !== null);
                        if (notasEv.length > 0) {
                            const mediaEv = (notasEv.reduce((a, b) => a + b, 0) / notasEv.length).toFixed(1);
                            const colorEv = getColorNota(parseFloat(mediaEv));
                            tags.push(`<span class="tag-eval" style="color:${colorEv};">Eval ${ev}: ${mediaEv}</span>`);
                        }
                    }
                    detalleHtml = tags.join(' ');
                }
            }

            const colorNota = getColorNota(media);
            const pctNota = (media / 10) * 100;
            setRosco('rosco_notas', pctNota, colorNota);
            document.getElementById('rosco_notas_valor').textContent = media.toFixed(1);
            document.getElementById('rosco_notas_valor').style.color = colorNota;
            document.getElementById('rosco_notas_detalle').innerHTML = detalleHtml || '<span style="color:var(--text-muted);">Sin notas</span>';
            document.getElementById('rosco_notas').parentElement.previousElementSibling.textContent = 'Nota media'; // Titulo original
            document.getElementById('rosco_notas').querySelector('.rosco-label').textContent = 'sobre 10';

            // --- ROSCO ASISTENCIA ---
            let faltas = 0, retrasos = 0, justificadas = 0;
            if (datosAsistencias && datosAsistencias.asistencias) {
                datosAsistencias.asistencias.forEach(a => {
                    if (a.asignatura === asigNombre) {
                        if (a.tipo === 'Falta' || a.tipo === 'falta') faltas++;
                        if (a.tipo === 'Retraso' || a.tipo === 'retraso') retrasos++;
                        if (a.justificada === true) justificadas++;
                    }
                });
            }

            const clasesEstimadas = 40;
            const asistidas = Math.max(0, clasesEstimadas - faltas);
            const pctAsist = Math.round((asistidas / clasesEstimadas) * 100);
            const colorAsist = getColorAsistencia(pctAsist);

            document.getElementById('asistencia_titulo').textContent = 'Asistencia';
            document.getElementById('asistencia_label').textContent = 'asistencia';

            setRosco('rosco_asistencia', pctAsist, colorAsist);
            document.getElementById('rosco_asistencia_valor').textContent = pctAsist + '%';
            document.getElementById('rosco_asistencia_valor').style.color = colorAsist;

            let detParts = [];
            if (faltas > 0) detParts.push(faltas + (faltas === 1 ? ' falta' : ' faltas'));
            if (retrasos > 0) detParts.push(retrasos + (retrasos === 1 ? ' retraso' : ' retrasos'));
            if (justificadas > 0) detParts.push(justificadas + ' justif.');
            document.getElementById('rosco_asistencia_detalle').innerHTML = detParts.length > 0
                ? detParts.join(' · ')
                : '<span style="color:var(--success-color); font-weight:600;">Sin incidencias</span>';
                
            // Render Chart (Evolución de notas)
            document.getElementById('chart_detalles_wrapper').style.display = 'block';
            document.getElementById('chart_detalles_titulo').innerText = 'Evolución de Notas (por Evaluación)';
            if (chartInstancia) chartInstancia.destroy();
            
            // Recoger datos de evaluación extraídos previamente (en `tags`)
            let evalData = [null, null, null];
            if (datosNotas && datosNotas.asignaturas) {
                const asig = datosNotas.asignaturas.find(a => a.asignatura === asigNombre);
                if (asig && asig.calificaciones) {
                    for (let ev = 1; ev <= 3; ev++) {
                        const notasEv = asig.calificaciones
                            .filter(c => extraerEvalStat(c.actividad) === String(ev))
                            .map(c => c.nota)
                            .filter(n => n !== null);
                        if (notasEv.length > 0) {
                            evalData[ev - 1] = (notasEv.reduce((a, b) => a + b, 0) / notasEv.length).toFixed(2);
                        }
                    }
                }
            }
            
            chartInstancia = new Chart(document.getElementById('chartDetalles'), {
                type: 'line',
                data: {
                    labels: ['1ª Evaluación', '2ª Evaluación', '3ª Evaluación'],
                    datasets: [{
                        label: 'Nota Media',
                        data: evalData,
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.2)',
                        pointBackgroundColor: '#3498db',
                        pointRadius: 6,
                        borderWidth: 3,
                        tension: 0.3,
                        spanGaps: true
                    }]
                },
                options: { 
                    responsive: true, maintainAspectRatio: false,
                    scales: { 
                        y: { beginAtZero: true, max: 10, ticks: { stepSize: 1, color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') } },
                        x: { ticks: { color: getComputedStyle(document.documentElement).getPropertyValue('--text-color') } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }
    }

    cargarEstadisticas();