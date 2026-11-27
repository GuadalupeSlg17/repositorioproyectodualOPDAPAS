<?php
require 'conexion.php';

// Nombre del archivo de listado (ajústalo si es diferente)
$archivo_listado = 'ver_registro material.php';

// Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: $archivo_listado");
    exit;
}

$id = intval($_GET['id']);

// Obtener datos del material
$stmt = $conexion->prepare("SELECT * FROM material_solicitado WHERE id_material = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Material no encontrado.");
}

$material = $resultado->fetch_assoc();
$stmt->close();
$folioMostrar = !empty($solicitud['folio']) ? $solicitud['folio'] : 'SIN FOLIO';
// Procesar actualización
$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $folio          = $_POST['folio'] ?? '';
    $cantidad_1     = $_POST['cantidad_1'] ?? '';
    $descripcion_1  = $_POST['descripcion_1'] ?? '';
    $categoria_1    = $_POST['categoria_1'] ?? '';
    $metros_1       = $_POST['metros_1'] ?? '';
    $cantidad_2     = $_POST['cantidad_2'] ?? '';
    $descripcion_2  = $_POST['descripcion_2'] ?? '';
    $categoria_2    = $_POST['categoria_2'] ?? '';
    $metros_2       = $_POST['metros_2'] ?? '';

    $update = $conexion->prepare("UPDATE material_solicitado SET 
        id_solicitante = ?,folio = ?, cantidad_1 = ?, descripcion_1 = ?, categoria_1 = ?, metros_1 = ?,
        cantidad_2 = ?, descripcion_2 = ?, categoria_2 = ?, metros_2 = ?
        WHERE id_material = ?");
        
    $update->bind_param("isiisdissds", 
        $id_solicitante,$folio, $cantidad_1, $descripcion_1, $categoria_1, $metros_1,
        $cantidad_2, $descripcion_2, $categoria_2, $metros_2, $id);

    if ($update->execute()) {
        $mensaje = '<div style="background:#d4edda;color:#155724;padding:12px;border-radius:6px;margin-bottom:15px;">✅ Material actualizado correctamente.</div>';
        // Recargar datos
        $stmt = $conexion->prepare("SELECT * FROM material_solicitado WHERE id_material = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $material = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    } else {
        $mensaje = '<div style="background:#f8d7da;color:#721c24;padding:12px;border-radius:6px;margin-bottom:15px;">❌ Error al actualizar: ' . $conexion->error . '</div>';
    }
    $update->close();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Material</title>
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
        .container {
            width: 100%;
            max-width: 800px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        h2 {
            color: #333;
            border-bottom: 3px solid #1257b1;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #444;
            font-size: 14px;
        }
        input[type="text"], input[type="number"], select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            box-sizing: border-box;
            font-family: inherit;
        }
        textarea {
            resize: vertical;
            min-height: 50px;
        }
        select {
            background: white;
        }
        .row {
            display: flex;
            gap: 15px;
        }
        .row .form-group {
            flex: 1;
        }
        .btn-guardar {
            background-color: #28a745;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 15px;
        }
        .btn-guardar:hover {
            background-color: #218838;
        }
        .btn-volver {
            display: inline-block;
            background-color: #6c757d;
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            margin-left: 10px;
            font-size: 14px;
        }
        .btn-volver:hover {
            background-color: #5a6268;
        }
        .logo {
            width: 100px;
            height: auto;
            margin-bottom: 10px;
        }
        .seccion-material {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #1257b1;
        }
        .seccion-material h3 {
            margin-top: 0;
            color: #1257b1;
            font-size: 16px;
        }
    </style>
</head>
<body>
<div class="container">
    <img src="logo.png" alt="Logo" class="logo">
    <h2>✏️ Editar Material #<?= $id ?></h2>

    <?= $mensaje ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>Folio</label>
            <input type="text" name="folio" value="<?= htmlspecialchars($solicitud['folio'] ?? '') ?>">        
        </div>

        <!-- MATERIAL 1 -->
        <div class="seccion-material">
            <h3>🔧 Material 1</h3>
            <div class="row">
                <div class="form-group">
                    <label>Cantidad</label>
                    <input type="number" name="cantidad_1" value="<?= htmlspecialchars($material['cantidad_1']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Metros</label>
                    <input type="text" name="metros_1" value="<?= htmlspecialchars($material['metros_1']) ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion_1" required><?= htmlspecialchars($material['descripcion_1']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Categoría</label>
                <select name="categoria_1" required>
                    <option value="agua potable" <?= $material['categoria_1'] == 'agua potable' ? 'selected' : '' ?>>Agua Potable</option>
                    <option value="drenaje" <?= $material['categoria_1'] == 'drenaje' ? 'selected' : '' ?>>Drenaje</option>
                    <option value="ninguna" <?= $material['categoria_1'] == 'ninguna' ? 'selected' : '' ?>>Ninguna</option>
                </select>
            </div>
        </div>

        <!-- MATERIAL 2 -->
        <div class="seccion-material">
            <h3>🔧 Material 2 (Opcional)</h3>
            <div class="row">
                <div class="form-group">
                    <label>Cantidad</label>
                    <input type="number" name="cantidad_2" value="<?= htmlspecialchars($material['cantidad_2']) ?>">
                </div>
                <div class="form-group">
                    <label>Metros</label>
                    <input type="text" name="metros_2" value="<?= htmlspecialchars($material['metros_2']) ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Descripción</label>
                <textarea name="descripcion_2"><?= htmlspecialchars($material['descripcion_2']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Categoría</label>
                <select name="categoria_2">
                    <option value="">-- Seleccionar --</option>
                    <option value="agua potable" <?= $material['categoria_2'] == 'agua potable' ? 'selected' : '' ?>>Agua Potable</option>
                    <option value="drenaje" <?= $material['categoria_2'] == 'drenaje' ? 'selected' : '' ?>>Drenaje</option>
                    <option value="ninguna" <?= $material['categoria_2'] == 'ninguna' ? 'selected' : '' ?>>Ninguna</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn-guardar">💾 Guardar Cambios</button>
        <a href="<?= $archivo_listado ?>" class="btn-volver">← Cancelar y Volver</a>
    </form>
</div>
</body>
</html>
<?php $conexion->close(); ?>