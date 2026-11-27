<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestor de archivos</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <style>
        * { box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
        body { background-color: #3f8edc; margin: 0; padding: 30px; display: flex; justify-content: center; }
        
        .modal-card { 
            background: #f8fafc; 
            border-radius: 16px; 
            padding: 30px; 
            width: 100%; 
            max-width: 850px; 
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1); 
            border: 1px solid #e2e8f0;
        }

        /* Header con título y logo */
        .header-section { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; }
        .header-title { color: #0284c7; font-size: 22px; font-weight: 700; margin: 0; }
        .logo { height: 45px; width: auto; }

        /* Formulario de campos superiores */
        .form-grid { display: flex; flex-direction: column; gap: 12px; max-width: 500px; margin: 0 auto 25px auto; }
        .form-group { display: flex; align-items: center; justify-content: flex-end; gap: 15px; }
        .form-group label { font-size: 14px; color: #64748b; font-weight: 500; text-align: right; width: 140px; }
        .form-control { 
            flex: 1; 
            padding: 8px 14px; 
            border: 1.5px solid #cbd5e1; 
            border-radius: 8px; 
            font-size: 14px; 
            outline: none; 
            background: white;
            transition: border-color 0.2s;
        }
        .form-control:focus { border-color: #0284c7; }
        select.form-control { appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill="%2364748b" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>'); background-repeat: no-repeat; background-position: right 8px center; }

        /* Barra de controles de navegación y búsqueda */
        .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; gap: 10px; }
        .nav-controls { display: flex; gap: 8px; align-items: center; }
        .icon-btn { background: #e2e8f0; border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; color: #475569; font-weight: bold; }
        .icon-btn:hover { background: #cbd5e1; }
        
        .search-box { position: relative; }
        .search-box input { padding: 6px 12px 6px 30px; border-radius: 20px; border: 1px solid #cbd5e1; font-size: 13px; outline: none; }
        .search-box::before { content: "🔍"; position: absolute; left: 10px; top: 50%; transform: translateY(-50%); font-size: 12px; color: #94a3b8; }

        /* Tabla de archivos */
        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        th, td { padding: 10px 14px; text-align: left; font-size: 13px; }
        th { color: #1e293b; font-weight: 600; border-bottom: 1px solid #e2e8f0; }
        td { color: #334155; border-bottom: 1px solid #f1f5f9; }
        tr.item-fila { cursor: pointer; }
        tr.item-fila:hover { background-color: #f1f5f9; }
        .folder-icon { margin-right: 6px; }

        .btn-eliminar {
            background: #fee2e2;
            color: #ef4444;
            border: none;
            padding: 4px 8px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
        }
        .btn-eliminar:hover { background: #fca5a5; color: #b91c1c; }

        /* Botones inferiores */
        .footer-actions { display: flex; justify-content: space-between; align-items: center; }
        .btn-group-left { display: flex; gap: 10px; }
        .btn-group-right { display: flex; gap: 10px; }
        
        .btn { padding: 8px 18px; border-radius: 8px; border: none; font-size: 13px; font-weight: 600; cursor: pointer; }
        .btn-light { background: #e2e8f0; color: #1e293b; }
        .btn-light:hover { background: #cbd5e1; }
        .btn-blue { background: #0066ff; color: white; }
        .btn-blue:hover { background: #0052cc; }
    </style>
</head>
<body>

<div class="modal-card">
   
    <div class="header-section">
        <h2 class="header-title">Gestor de archivos sobre solicitudes</h2>
        <img src="logo.png" alt="OPDAPAS" class="logo">
    </div>

    <div class="form-grid">
        <div class="form-group">
            <label>Nombre del archivo:</label>
            <input type="text" id="nombreArchivo" class="form-control" placeholder="Ej. Oficio de solicitud">
        </div>
        <div class="form-group">
            <label>Descripción:</label>
            <input type="text" id="descripcionArchivo" class="form-control">
        </div>
        <div class="form-group">
            <label>Guardar en carpeta:</label>
            <select id="selectCarpetaDestino" class="form-control">
                <option value="">📁 Carpeta Principal (Raíz)</option>
            </select>
        </div>
        <div class="form-group">
            <label>Tipo de formato:</label>
            <select id="tipoFormato" class="form-control">
                <option value="pdf">PDF</option>
                <option value="imagen">Imagen (JPG, PNG)</option>
            </select>
        </div>
    </div>

    <div class="toolbar">
        <div class="nav-controls">
            <button class="icon-btn" onclick="regresarCarpeta()">&lt;</button>
            <button class="icon-btn">&gt;</button>
            <select id="selectCarpetaRuta" class="form-control" style="width: auto; padding: 4px 10px;" onchange="cambiarCarpetaSelect(this.value)">
                <option value="">📁 Carpeta Principal (Raíz)</option>
            </select>
        </div>

        <div class="search-box">
            <input type="text" id="buscarInput" placeholder="Buscar" onkeyup="filtrarTabla()">
        </div>
    </div>

   
    <table>
        <thead>
            <tr>
                <th style="width: 45%;">Carpeta / Archivo</th>
                <th style="width: 20%;">Peso</th>
                <th style="width: 20%;">Fecha</th>
                <th style="width: 15%; text-align: center;">Acción</th>
            </tr>
        </thead>
        <tbody id="tablaArchivos"></tbody>
    </table>

    <!-- Botones de Acción Inferiores -->
    <div class="footer-actions">
        <div class="btn-group-left">
            <button class="btn btn-light" onclick="crearCarpeta()">+ Nueva carpeta</button>
            <button class="btn btn-light" onclick="document.getElementById('fileInput').click()">Seleccionar archivo</button>
            <input type="file" id="fileInput" accept="application/pdf, image/*" style="display:none" onchange="actualizarNombreInput()">
        </div>
        <div class="btn-group-right">
            <button class="btn btn-light" onclick="location.reload()">Cancelar</button>
            <button class="btn btn-blue" onclick="guardarArchivo()">Guardar</button>
        </div>
    </div>
</div>

<script>
const urlParams = new URLSearchParams(window.location.search);
const folioActual = urlParams.get('folio') || 'GENERAL';

let carpetaActualId = null;
let historial = [{ id: null, nombre: 'Carpeta Principal' }];
let todosLosElementos = [];

cargarArchivos();

function cargarArchivos() {
    let url = `api_obtener.php?folio=${encodeURIComponent(folioActual)}`;
    if (carpetaActualId !== null) url += `&parent_id=${carpetaActualId}`;

    fetch(url)
        .then(res => {
            if (!res.ok) throw new Error("Error en servidor");
            return res.json();
        })
        .then(data => {
            todosLosElementos = Array.isArray(data) ? data : [];
            renderizarTabla(todosLosElementos);
            actualizarSelectCarpetas(todosLosElementos);
        })
        .catch(err => {
            console.error("Error al cargar archivos:", err);
        });
}

function renderizarTabla(items) {
    const tbody = document.getElementById('tablaArchivos');
    tbody.innerHTML = '';

    if (carpetaActualId !== null) {
        const filaAtras = document.createElement('tr');
        filaAtras.className = 'item-fila';
        filaAtras.innerHTML = `<td colspan="4"><b>˅ 📁 .. (Regresar)</b></td>`;
        filaAtras.onclick = regresarCarpeta;
        tbody.appendChild(filaAtras);
    }

    if (!items || items.length === 0) {
        if (carpetaActualId === null) {
            tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; color:#94a3b8; padding:15px;">No hay carpetas o archivos guardados en este folio.</td></tr>`;
        }
        return;
    }

    items.forEach(item => {
        const tr = document.createElement('tr');
        tr.className = 'item-fila';

        let icono = '📁';
        if (item.tipo === 'pdf') icono = '📄';
        if (item.tipo === 'imagen') icono = '🖼️';

        let pesoTexto = item.tipo === 'carpeta' ? '-' : ((item.peso || 0) / (1024 * 1024)).toFixed(2) + ' MB';
        let fecha = item.fecha_creacion ? item.fecha_creacion.split(' ')[0] : '-';

        tr.innerHTML = `
            <td>
                <span class="folder-icon">${icono}</span> 
                ${item.tipo === 'carpeta' 
                    ? `<b>${item.nombre}</b>` 
                    : `<a href="${item.ruta_archivo}" target="_blank" style="color:#0284c7; text-decoration:none; font-weight:500;">${item.nombre}</a>`}
            </td>
            <td>${pesoTexto}</td>
            <td>${fecha}</td>
            <td style="text-align: center;">
                <button class="btn-eliminar" onclick="eliminarElemento(event, ${item.id}, '${item.nombre}', '${item.tipo}')">🗑️ Borrar</button>
            </td>
        `;

        tr.onclick = (e) => {
            if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') return;

            if (item.tipo === 'carpeta') {
                abrirCarpeta(item.id, item.nombre);
            } else if (item.ruta_archivo) {
                window.open(item.ruta_archivo, '_blank');
            }
        };

        tbody.appendChild(tr);
    });
}

function abrirCarpeta(id, nombre) {
    carpetaActualId = id;
    historial.push({ id: id, nombre: nombre });
    cargarArchivos();
}

function regresarCarpeta() {
    if (historial.length > 1) {
        historial.pop();
        carpetaActualId = historial[historial.length - 1].id;
        cargarArchivos();
    }
}

function actualizarSelectCarpetas(data) {
    const selectNav = document.getElementById('selectCarpetaRuta');
    const selectDestino = document.getElementById('selectCarpetaDestino');

    selectNav.innerHTML = '<option value="">📁 Carpeta Principal (Raíz)</option>';
    selectDestino.innerHTML = '<option value="">📁 Carpeta Principal (Raíz)</option>';

    if (Array.isArray(data)) {
        data.filter(i => i.tipo === 'carpeta').forEach(c => {
            // Select de la barra de navegación
            const optionNav = document.createElement('option');
            optionNav.value = c.id;
            optionNav.textContent = '📁 ' + c.nombre;
            if (c.id == carpetaActualId) optionNav.selected = true;
            selectNav.appendChild(optionNav);

            // Select del formulario de destino
            const optionDestino = document.createElement('option');
            optionDestino.value = c.id;
            optionDestino.textContent = '📁 ' + c.nombre;
            selectDestino.appendChild(optionDestino);
        });
    }

    // Selecciona por defecto la carpeta actual donde está parado el usuario
    if (carpetaActualId !== null) {
        selectDestino.value = carpetaActualId;
    }
}

function cambiarCarpetaSelect(id) {
    carpetaActualId = id ? parseInt(id) : null;
    cargarArchivos();
}

function crearCarpeta() {
    const nombre = prompt('Escribe el nombre de la nueva carpeta:');
    if (!nombre) return;

    fetch('api_crear_carpeta.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ nombre: nombre, folio: folioActual, parent_id: carpetaActualId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            cargarArchivos();
        } else {
            alert('Error al crear carpeta: ' + (data.mensaje || 'Error desconocido'));
        }
    })
    .catch(() => alert('Error de conexión con api_crear_carpeta.php'));
}

function actualizarNombreInput() {
    const fileInput = document.getElementById('fileInput');
    const nombreInput = document.getElementById('nombreArchivo');
    if (fileInput.files.length > 0 && !nombreInput.value) {
        const fileName = fileInput.files[0].name.replace(/\.[^/.]+$/, "");
        nombreInput.value = fileName;
    }
}

function guardarArchivo() {
    const fileInput = document.getElementById('fileInput');
    const nombre = document.getElementById('nombreArchivo').value;
    const descripcion = document.getElementById('descripcionArchivo').value;
    const destinoCarpetaId = document.getElementById('selectCarpetaDestino').value;

    if (fileInput.files.length === 0) {
        alert('Por favor selecciona un archivo escaneado (PDF o imagen).');
        return;
    }

    const formData = new FormData();
    formData.append('archivo', fileInput.files[0]);
    formData.append('nombre', nombre);
    formData.append('descripcion', descripcion);
    formData.append('folio', folioActual);

    // Usa la carpeta seleccionada en el menú "Guardar en carpeta:"
    if (destinoCarpetaId !== '') {
        formData.append('parent_id', destinoCarpetaId);
    }

    fetch('api_subir_archivo.php', { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Archivo guardado exitosamente.');
            document.getElementById('nombreArchivo').value = '';
            document.getElementById('descripcionArchivo').value = '';
            fileInput.value = '';
            cargarArchivos();
        } else {
            alert(data.mensaje || 'Error al guardar el archivo');
        }
    })
    .catch(() => alert('No se pudo comunicar con api_subir_archivo.php'));
}

function eliminarElemento(event, id, nombre, tipo) {
    event.stopPropagation(); // Evita abrir la carpeta/archivo al hacer clic en Borrar

    const mensaje = tipo === 'carpeta' 
        ? `¿Estás seguro de borrar la carpeta "${nombre}"? Se eliminarán también sus archivos.`
        : `¿Estás seguro de borrar el archivo "${nombre}"?`;

    if (!confirm(mensaje)) return;

    fetch('api_eliminar.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            cargarArchivos();
        } else {
            alert('Error al eliminar: ' + (data.mensaje || 'Error desconocido'));
        }
    })
    .catch(() => alert('Error de conexión con api_eliminar.php'));
}

function filtrarTabla() {
    const texto = document.getElementById('buscarInput').value.toLowerCase();
    const filtrados = todosLosElementos.filter(item => item.nombre.toLowerCase().includes(texto));
    renderizarTabla(filtrados);
}
</script>

</body>
</html>