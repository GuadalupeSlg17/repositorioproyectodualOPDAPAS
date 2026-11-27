<?php
require 'conexion.php';

// BUSCADOR Y FILTROS
$campo         = $_GET['campo'] ?? '';
$buscar        = $_GET['buscar'] ?? '';
$filtroEstatus = $_GET['filtro_estatus'] ?? '';

// Construir la consulta SQL unificada para ambas tablas (solicitudes y estatus_solicitud)
$where = "WHERE (s.estatus IN ('Aprobado') 
             OR e.estatus IN ('Aprobado'))";

if (!empty($buscar)) {
    $buscar_limpio = mysqli_real_escape_string($conexion, $buscar);
    if ($campo == 'folio') {
        $where .= " AND s.folio LIKE '%$buscar_limpio%'";
    } elseif ($campo == 'comunidad') {
        $where .= " AND s.nombre_comunidad LIKE '%$buscar_limpio%'";
    } elseif ($campo == 'region') {
        $where .= " AND s.nombre_region LIKE '%$buscar_limpio%'";
    } else {
        $where .= " AND (s.folio LIKE '%$buscar_limpio%' OR s.nombre_comunidad LIKE '%$buscar_limpio%' OR s.nombre_solicitante LIKE '%$buscar_limpio%')";
    }
}

// Usamos LEFT JOIN para que no se pierdan registros y COALESCE para sincronizar estatus
$query = "SELECT s.*, COALESCE(e.estatus, s.estatus) AS estatus_real, e.fecha_actualizacion 
          FROM solicitudes s 
          LEFT JOIN estatus_solicitud e ON s.id_solicitante = e.id_solicitante 
          $where 
          ORDER BY s.fecha_solicitud DESC";

$resultado = mysqli_query($conexion, $query);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Visualización de archivos autorizados</title>
    <link rel="icon" type="image/png" href="favicon.png">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(
                135deg,
                #f8d4e8 0%,
                #a0c9eb 30%,
                #b7cce7 60%,
                #cac2f3 100%
            );
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            margin: 0;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            overflow-x: auto;
        }

        h2 {
            color: #333;
            border-bottom: 3px solid #1257b1;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
            vertical-align: top;
        }

        th {
            background-color: #356cc0;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-autorizado {
            background-color: #d4edda;
            color: #155724;
        }

        .logo {
            width: 100px;
            height: auto;
            margin-bottom: 10px;
        }
        
        .buscador select, .buscador input[type="text"] { 
            padding: 10px 12px; 
            border: 1px solid #ccc; 
            border-radius: 6px; 
            font-size: 14px; 
        }
        .buscador button { 
            background-color: #1257b1; 
            color: white; 
            border: none; 
            padding: 10px 18px; 
            border-radius: 6px; 
            font-weight: bold; 
            cursor: pointer; 
            font-size: 14px; 
        }
        .btn-limpiar { 
            background-color: #6c757d !important; 
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            display: inline-block;
        }
        .btn-pdf {
            background-color: #17a2b8;
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            margin-right: 5px;
            display: inline-block;
        }
        .btn-pdf:hover {
            background-color: #138496;
        }
    </style>
</head>

<body>

<div class="container">
    <img src="logo.png" alt="Logo" class="logo">

    <h2>Convenios y oficios liberados</h2>
    
    <form method="GET" action="" class="buscador">
        <select name="campo">
            <option value="folio" <?= $campo == 'folio' ? 'selected' : '' ?>>Folio</option>
            <option value="comunidad" <?= $campo == 'comunidad' ? 'selected' : '' ?>>Comunidad</option>
            <option value="region" <?= $campo == 'region' ? 'selected' : '' ?>>Región</option>
        </select>
        <input type="text" name="buscar" placeholder="Escribe aquí para buscar..." value="<?= htmlspecialchars($buscar) ?>">
        <button type="submit">Buscar</button>
        <?php if (!empty($buscar)): ?>
            <a href="?" class="btn-limpiar">✕ Limpiar todo</a>
        <?php endif; ?>
    </form>

    <h3>Documentos autorizados</h3>
    <table>
        <thead>
            <tr>
                <th>Nº</th>
                <th>Solicitante / Beneficiario</th>
                <th>Folio</th>
                <th>Comunidad</th>
                <th>Fecha de liberación</th>
                <th>Archivos PDF (Convenio / Oficio)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if ($resultado && mysqli_num_rows($resultado) > 0) {
                $contador = 1;
                while ($row = mysqli_fetch_assoc($resultado)) {
                    $folio = htmlspecialchars($row['folio']);
                    $nombre = htmlspecialchars($row['nombre_solicitante']);
                    $comunidad = htmlspecialchars($row['nombre_comunidad']);
                    $fecha = date('d/m/Y', strtotime($row['fecha_solicitud']));
            ?>
                <tr>
                    <td><?= $contador++ ?></td>
                    <td><?= $nombre ?> (<em><?= htmlspecialchars($row['puesto']) ?></em>)</td>
                    <td><strong><?= $folio ?></strong></td>
                    <td><?= $comunidad ?></td>
                    <td><?= $fecha ?></td>
                    <td>
                        <a href="oficio_liberado.php?folio=<?= $folio ?>&tipo=oficio" target="_blank" class="btn-pdf">📄 Ver Oficio</a>
                        <a href="convenio.php?folio=<?= $folio ?>&tipo=convenio" target="_blank" class="btn-pdf" style="background-color: #28a745;">📜 Ver Convenio</a>
                    </td>
                </tr>
            <?php 
                }
            } else { 
            ?>
                <tr>
                    <td colspan="6" class="text-empty" style="text-align: center; color: #777; padding: 20px;">No se encontraron registros de convenios u oficios liberados.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

</body>
</html>