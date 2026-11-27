<?php
require 'conexion.php';

function getEstatusBadge($estatus) {
    $colores = [
        'Pendiente'    => ['bg' => '#f1c40f', 'text' => '#7d6608', 'hex' => '#F59E0B'],
        'En Proceso'   => ['bg' => '#3498db', 'text' => '#ffffff', 'hex' => '#3B82F6'],
        'Aprobado'     => ['bg' => '#2ecc71', 'text' => '#ffffff', 'hex' => '#10B981'],
        'No Aprobado'  => ['bg' => '#e74c3c', 'text' => '#ffffff', 'hex' => '#EF4444'],
        'Terminado'    => ['bg' => '#5d6d7e', 'text' => '#ffffff', 'hex' => '#6B7280'],
    ];

    $c = $colores[$estatus] ?? [
        'bg' => '#95a5a6',
        'text' => '#fff',
        'hex' => '#9CA3AF'
    ];

    return sprintf(
        '<span class="badge-semaforo" style="background:%s; color:%s; box-shadow: 0 0 8px %s55;">● %s</span>',
        $c['bg'],
        $c['text'],
        $c['hex'],
        htmlspecialchars($estatus)
    );
}

// RESUMEN DEL SEMÁFORO
$resumenSemaforo = [];
$sqlResumen = "SELECT estatus, COUNT(*) as total FROM solicitudes GROUP BY estatus";
$resumenResult = $conexion->query($sqlResumen);
if ($resumenResult) {
    while ($row = $resumenResult->fetch_assoc()) {
        $resumenSemaforo[$row['estatus']] = $row['total'];
    }
}

// BUSCADOR Y FILTROS
$campo         = $_GET['campo'] ?? '';
$buscar        = $_GET['buscar'] ?? '';
$filtroEstatus = $_GET['filtro_estatus'] ?? '';

$sql = "SELECT * FROM solicitudes WHERE 1=1";

if (!empty($buscar)) {
    $buscar_seguro = $conexion->real_escape_string($buscar);
    if ($campo == 'id') {
        $sql .= " AND id_solicitante = " . intval($buscar);
    } elseif ($campo == 'folio') {
        $sql .= " AND folio LIKE '%$buscar_seguro%'";
    } elseif ($campo == 'solicitante') {
        $sql .= " AND nombre_solicitante LIKE '%$buscar_seguro%'";
    } elseif ($campo == 'comunidad') {
        $sql .= " AND nombre_comunidad LIKE '%$buscar_seguro%'";
    } elseif ($campo == 'region') {
        $sql .= " AND nombre_region LIKE '%$buscar_seguro%'";
    } elseif ($campo == 'calle') {
        $sql .= " AND calle_beneficiada LIKE '%$buscar_seguro%'";
    }
}

if (!empty($filtroEstatus)) {
    $estatus_seguro = $conexion->real_escape_string($filtroEstatus);
    $sql .= " AND estatus = '$estatus_seguro'";
}

$sql .= " ORDER BY fecha_solicitud DESC";
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Control - Solicitudes</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: linear-gradient(135deg, #f8d4e8 0%, #a0c9eb 30%, #b7cce7 60%, #cac2f3 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .container { max-width: 1300px; margin: 0 auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        h2 { color: #333; border-bottom: 3px solid #1257b1; padding-bottom: 10px; margin-bottom: 20px; }
        .btn-nuevo { display: inline-block; background-color: #356cc0; color: white; padding: 10px 15px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-bottom: 20px; }
        .btn-nuevo:hover { background-color: #2b59bb; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background-color: #2972df83; color: white; }
        tr:hover { background-color: #f1f1f1; }
        .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-agua { background-color: #e3f2fd; color: #0d47a1; }
        .badge-drenaje { background-color: #efebe9; color: #4e342e; }
        .text-empty { text-align: center; color: #777; padding: 30px; }
        .logo { width: 100px; height: auto; }
        .btn-accion { display: inline-block; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px; font-weight: bold; margin-right: 5px; transition: opacity 0.2s; }
        .btn-accion:hover { opacity: 0.85; }
        .btn-editar { background-color: #9de6cd; color: #212529; }
        .btn-eliminar { background-color: #df7983; color: white; }
        .btn-oficio { background-color: #27a4ec; color: white; }
        .buscador { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; align-items: center; background: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #e9ecef; }
        .buscador select, .buscador input[type="text"] { padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }
        .buscador button { background-color: #1257b1; color: white; border: none; padding: 10px 18px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 14px; }
        .btn-limpiar { background-color: #6c757d !important; }
        .semaforo-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 24px; }
        .semaforo-card { background: white; border-radius: 10px; padding: 16px; text-align: center; border-top: 4px solid transparent; box-shadow: 0 2px 6px rgba(0,0,0,0.06); transition: all 0.2s ease; cursor: pointer; text-decoration: none; color: inherit; }
        .semaforo-card.activo { outline: 2px solid #1257b1; outline-offset: 2px; }
        .semaforo-card.pendiente { border-top-color: #f1c40f; }
        .semaforo-card.proceso { border-top-color: #3498db; }
        .semaforo-card.aprobado { border-top-color: #2ecc71; }
        .semaforo-card.rechazado { border-top-color: #e74c3c; }
        .semaforo-card.terminado { border-top-color: #5d6d7e; }
        .semaforo-card .luz { width: 36px; height: 36px; border-radius: 50%; margin: 0 auto 8px; }
        .pendiente .luz { background: #f1c40f; box-shadow: 0 0 12px #f1c40f88; }
        .proceso .luz { background: #3498db; box-shadow: 0 0 12px #3498db88; }
        .aprobado .luz { background: #2ecc71; box-shadow: 0 0 12px #2ecc7188; }
        .rechazado .luz { background: #e74c3c; box-shadow: 0 0 12px #e74c3c88; }
        .terminado .luz { background: #5d6d7e; box-shadow: 0 0 12px #5d6d7e88; }
        .semaforo-card h4 { margin: 0; font-size: 13px; text-transform: uppercase; color: #555; }
        .semaforo-card .count { font-size: 28px; font-weight: 700; margin: 6px 0; }
        .badge-semaforo { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; display: inline-block; white-space: nowrap; }
        .folio-badge { display: inline-block; background: #1257b1; color: white; padding: 6px 12px; border-radius: 6px; font-family: 'Courier New', monospace; font-weight: bold; font-size: 14px; }
        .material-item { border: 1px solid #ddd; border-radius: 8px; padding: 8px; margin-bottom: 8px; background: #f8f9ff; }
    </style>
</head>
<body>

<div class="container">
    <img src="logo.png" alt="Logo" class="logo">
    <h2>Registro de Oficios</h2>

    <!-- SEMÁFORO -->
    <div class="semaforo-grid">
        <a href="?filtro_estatus=Pendiente" class="semaforo-card pendiente <?= $filtroEstatus == 'Pendiente' ? 'activo' : '' ?>">
            <div class="luz"></div>
            <h4>Pendiente</h4>
            <div class="count"><?= $resumenSemaforo['Pendiente'] ?? 0 ?></div>
            <small>Espera de revisión por Dirección General</small>
        </a>
        <a href="?filtro_estatus=En Proceso" class="semaforo-card proceso <?= $filtroEstatus == 'En Proceso' ? 'activo' : '' ?>">
            <div class="luz"></div>
            <h4>En Proceso</h4>
            <div class="count"><?= $resumenSemaforo['En Proceso'] ?? 0 ?></div>
            <small>Preparando material</small>
        </a>
        <a href="?filtro_estatus=Aprobado" class="semaforo-card aprobado <?= $filtroEstatus == 'Aprobado' ? 'activo' : '' ?>">
            <div class="luz"></div>
            <h4>Aprobado</h4>
            <div class="count"><?= $resumenSemaforo['Aprobado'] ?? 0 ?></div>
            <small>Oficio emitido</small>
        </a>
        <a href="?filtro_estatus=No Aprobado" class="semaforo-card rechazado <?= $filtroEstatus == 'No Aprobado' ? 'activo' : '' ?>">
            <div class="luz"></div>
            <h4>No Aprobado</h4>
            <div class="count"><?= $resumenSemaforo['No Aprobado'] ?? 0 ?></div>
            <small>Rechazada</small>
        </a>
        <a href="?filtro_estatus=Terminado" class="semaforo-card terminado <?= $filtroEstatus == 'Terminado' ? 'activo' : '' ?>">
            <div class="luz"></div>
            <h4>Terminado</h4>
            <div class="count"><?= $resumenSemaforo['Terminado'] ?? 0 ?></div>
            <small>Entrega completada</small>
        </a>
    </div>

    <!-- BUSCADOR -->
    <form method="GET" action="" class="buscador">
        <select name="campo">
            <option value="folio" <?= $campo == 'folio' ? 'selected' : '' ?>>Folio</option>
            <option value="solicitante" <?= $campo == 'solicitante' ? 'selected' : '' ?>>Solicitante</option>
            <option value="comunidad" <?= $campo == 'comunidad' ? 'selected' : '' ?>>Comunidad</option>
            <option value="region" <?= $campo == 'region' ? 'selected' : '' ?>>Región</option>
            <option value="calle" <?= $campo == 'calle' ? 'selected' : '' ?>>Calle</option>
        </select>
        <input type="text" name="buscar" placeholder="Escribe aquí para buscar..." value="<?= htmlspecialchars($buscar) ?>">
        <?php if (!empty($filtroEstatus)): ?>
            <input type="hidden" name="filtro_estatus" value="<?= htmlspecialchars($filtroEstatus) ?>">
        <?php endif; ?>
        <button type="submit">Buscar</button>
        <?php if (!empty($buscar) || !empty($filtroEstatus)): ?>
            <a href="?" class="btn-accion btn-limpiar" style="color:white;">✕ Limpiar todo</a>
        <?php endif; ?>
    </form>

    <a href="index.php" class="btn-nuevo">Ir al inicio</a>

    <!-- TABLA -->
    <table>
        <thead>
            <tr>
                <th>Folio</th>
                <th>Solicitante</th>
                <th>Cargo</th>
                <th>Teléfono</th>
                <th>Calle</th>
                <th>Material</th>
                <th>Región / Comunidad</th>
                <th>Fecha</th>
                <th>Estatus</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <?php while ($fila = $resultado->fetch_assoc()): ?>
                <?php
                $idSolicitud = isset($fila['id_solicitante']) ? intval($fila['id_solicitante']) : 0;
                $folioMostrar = !empty($fila['folio']) ? $fila['folio'] : 'SIN-FOLIO';
                ?>
                <tr>
                    <td><span class="folio-badge"><?= htmlspecialchars($folioMostrar) ?></span></td>
                    <td><?= htmlspecialchars($fila['nombre_solicitante'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fila['puesto'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fila['telefono'] ?? '') ?></td>
                    <td><?= htmlspecialchars($fila['calle_beneficiada'] ?? '') ?></td>
                    <td>
                    <?php
                    $stmtMateriales = $conexion->prepare("SELECT cantidad, descripcion, categoria, metros FROM material_solicitado WHERE folio = ? ORDER BY id_material ASC");
                    if ($stmtMateriales) {
                        $stmtMateriales->bind_param("s", $fila['folio']);
                        $stmtMateriales->execute();
                        $materialesResultado = $stmtMateriales->get_result();

                        if ($materialesResultado && $materialesResultado->num_rows > 0) {
                            while ($material = $materialesResultado->fetch_assoc()) {
                                $clase_categoria = (strtolower(trim($material['categoria'])) == 'agua potable') ? 'badge-agua' : 'badge-drenaje';
                                ?>
                                <div class="material-item">
                                    <b>Cant:</b> <?= htmlspecialchars($material['cantidad']) ?><br>
                                    <b>Desc:</b> <?= htmlspecialchars($material['descripcion']) ?><br>
                                    <span class="badge <?= $clase_categoria ?>"><?= htmlspecialchars($material['categoria']) ?></span><br>
                                    <b>Mts:</b> <?= number_format((float)$material['metros'], 2) ?>
                                </div>
                                <?php
                            }
                        } else {
                            $clase_cat1 = (($fila['categoria_mat1'] ?? '') == 'agua potable') ? 'badge-agua' : 'badge-drenaje';
                            ?>
                            <b>Cant:</b> <?= htmlspecialchars($fila['cantidad_mat1'] ?? '') ?><br>
                            <b>Desc:</b> <?= htmlspecialchars($fila['descripcion_mat1'] ?? '') ?><br>
                            <span class="badge <?= $clase_cat1 ?>"><?= htmlspecialchars($fila['categoria_mat1'] ?? '') ?></span><br>
                            <b>Mts:</b> <?= number_format((float)($fila['metros_mat1'] ?? 0), 2) ?>
                            <?php
                        }
                        $stmtMateriales->close();
                    }
                    ?>
                    </td>
                    <td>
                        <b>Región:</b> <?= htmlspecialchars($fila['nombre_region'] ?? '') ?><br>
                        <b>Comunidad:</b> <?= htmlspecialchars($fila['nombre_comunidad'] ?? '') ?>
                    </td>
                    <td><?= htmlspecialchars($fila['fecha_solicitud'] ?? '') ?></td>
                    <td><?= getEstatusBadge($fila['estatus'] ?? 'Pendiente') ?></td>
                    <td>
                        <?php if ($idSolicitud > 0): ?>
                            <a href="editar.php?id=<?= $idSolicitud ?>" class="btn-accion btn-editar">Editar</a>
                            <a href="eliminar.php?id=<?= $idSolicitud ?>" class="btn-accion btn-eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar esta solicitud?');">Eliminar</a>
                        <?php else: ?>
                            <span style="color:#999; font-size:12px;">Sin ID</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="11" class="text-empty">
                    <?= !empty($buscar) || !empty($filtroEstatus) ? 'No se encontraron resultados para tu búsqueda.' : 'No hay solicitudes registradas.' ?>
                </td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>
<?php $conexion->close(); ?>
