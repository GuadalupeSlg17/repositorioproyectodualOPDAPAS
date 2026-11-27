<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar material solicitado</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #f8d4e8 0%, #a0c9eb 30%, #b7cce7 60%, #cac2f3 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            margin: 0;
        }
        form {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 800px;
        }
        h2 {
            text-align: center;
            color: #1b67da;
            margin-bottom: 25px;
            font-size: 28px;
        }
        h3 {
            margin-top: 25px;
            margin-bottom: 15px;
            color: #444;
            border-bottom: 2px solid #2f56ac;
            padding-bottom: 5px;
        }
        .campo-bloque {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"], input[type="number"], select {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            outline: none;
            transition: 0.3s;
            font-size: 14px;
            width: 100%;
            box-sizing: border-box;
        }
        input:focus, select:focus {
            border-color: #3a6ddd;
            box-shadow: 0px 0px 5px rgba(34, 101, 177, 0.5);
        }
        .radio-group {
            display: inline-flex;
            align-items: center;
            margin-right: 20px;
            margin-top: 5px;
        }
        .radio-group input {
            margin-right: 6px;
        }
        button {
            margin-top: 30px;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: #4870bb;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
        }
        button:hover {
            background: #3a58b9;
        }
        .logo {
            width: 200px;  
            height: auto;
            display: block;
            margin: 0 auto 20px auto;
        }
        .btn-nuevo {
            display: inline-block;
            background-color: #356cc0;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

    <form action="guardar_material.php" method="POST">
        <img src="logo.png" alt="Logo" class="logo">
        
        <h2>Registrar Material Solicitado</h2>
        <a href="index.php" class="btn-nuevo">Ir al inicio</a>

        <h3>Datos de Identificación</h3>
        <div class="campo-bloque">
            <label>* Id material:</label>
            <input type="number" name="id_material" required>
        </div>
        
        <div class="campo-bloque">
            <label>* Folio:</label>
            <input type="text" name="folio" required>
        </div>

        <h3>Material 1</h3>
        <div class="campo-bloque">
            <label>* Cantidad:</label>
            <input type="number" id="cantidad_1" name="cantidad_1" required>
        </div>
        
        <div class="campo-bloque">
            <label>* Descripción:</label>
            <input type="text" name="descripcion_1" required>
        </div>
        
        <div class="campo-bloque">
            <label>* Categoría:</label>
            <div>
                <div class="radio-group">
                    <input type="radio" id="drenaje1" name="categoria_1" value="drenaje" checked required>
                    <label for="drenaje1" style="font-weight:normal;">Drenaje</label>
                </div>
                <div class="radio-group">
                    <input type="radio" id="agua1" name="categoria_1" value="agua potable">
                    <label for="agua1" style="font-weight:normal;">Agua potable</label>
                </div>
                <div class="radio-group">
                    <input type="radio" id="ninguna1" name="categoria_1" value="ninguna">
                    <label for="ninguna1" style="font-weight:normal;">Ninguna</label>
                </div>
            </div>
        </div>
        
        <div class="campo-bloque">
            <label>Mts:</label>
            <input type="number" step="0.01" id="metros_1" name="metros_1" required>
        </div>

        <h3>Material 2 (Opcional)</h3>
        <div class="campo-bloque">
            <label>Cantidad:</label>
            <input type="number" id="cantidad_2" name="cantidad_2">
        </div>
        
        <div class="campo-bloque">
            <label>Descripción:</label>
            <input type="text" name="descripcion_2">
        </div>
        
        <div class="campo-bloque">
            <label>Categoría:</label>
            <div>
                <div class="radio-group">
                    <input type="radio" id="drenaje2" name="categoria_2" value="drenaje" checked>
                    <label for="drenaje2" style="font-weight:normal;">Drenaje</label>
                </div>
                <div class="radio-group">
                    <input type="radio" id="agua2" name="categoria_2" value="agua potable">
                    <label for="agua2" style="font-weight:normal;">Agua potable</label>
                </div>
                <div class="radio-group">
                    <input type="radio" id="ninguna2" name="categoria_2" value="ninguna">
                    <label for="ninguna2" style="font-weight:normal;">Ninguna</label>
                </div>
            </div>
        </div>
        
        <div class="campo-bloque">
            <label>Mts (Calculado automáticamente):</label>
            <input type="number" step="0.01" id="metros_2" name="metros_2">
        </div>

        <button type="submit">Guardar registro</button>
    </form>

    <script>
        function calcularMetros() {
            // Cálculo Material 1
            const cantidad_1 = parseFloat(document.getElementById('cantidad_1').value) || 0;
            const categoria_1 = document.querySelector('input[name="categoria_1"]:checked');
            const metros_1 = document.getElementById('metros_1');
            
            if (categoria_1 && cantidad_1 > 0) {
                metros_1.value = (categoria_1.value === 'drenaje') ? (cantidad_1 * 0.9).toFixed(2) : (cantidad_1 * 0.6).toFixed(2);
            } else {
                metros_1.value = '';
            }

            // Cálculo Material 2
            const cantidad_2 = parseFloat(document.getElementById('cantidad_2').value) || 0;
            const categoria_2 = document.querySelector('input[name="categoria_2"]:checked');
            const metros_2 = document.getElementById('metros_2');
            
            if (categoria_2 && cantidad_2 > 0) {
                metros_2.value = (categoria_2.value === 'drenaje') ? (cantidad_2 * 0.9).toFixed(2) : (cantidad_2 * 0.6).toFixed(2);
            } else {
                metros_2.value = '';
            }
        }

        // Escuchadores de eventos para ejecutar la función
        document.getElementById('cantidad_1').addEventListener('input', calcularMetros);
        document.getElementById('cantidad_2').addEventListener('input', calcularMetros);
        document.querySelectorAll('input[name="categoria_1"]').forEach(el => el.addEventListener('change', calcularMetros));
        document.querySelectorAll('input[name="categoria_2"]').forEach(el => el.addEventListener('change', calcularMetros));
    </script>
</body>
</html>