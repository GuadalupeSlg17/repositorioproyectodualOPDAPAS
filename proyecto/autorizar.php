<?php
require 'conexion.php';

$solicitud = null;
$materialesResultado = null;
$mensaje = '';

// ========== BUSCAR POR FOLIO ==========
if (isset($_GET['buscar_folio']) && !empty($_GET['buscar_folio'])) {
    $folio = trim(strval($_GET['buscar_folio']));
    
    // Consulta preparada a la solicitud principal
    $stmt = $conexion->prepare("SELECT * FROM solicitudes WHERE folio = ?");
    $stmt->bind_param("s", $folio);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows > 0) {
        $solicitud = $res->fetch_assoc();

        // Consulta preparada a la tabla de múltiples materiales
        $stmtMat = $conexion->prepare("SELECT * FROM material_solicitado WHERE folio = ? ORDER BY id_material ASC");
        $stmtMat->bind_param("s", $folio);
        $stmtMat->execute();
        $materialesResultado = $stmtMat->get_result();
        $stmtMat->close();
    } else {
        $mensaje = "<div class='alerta error'> No se encontró solicitud con folio: " . htmlspecialchars($folio) . "</div>";
    }
    $stmt->close();
}

// ========== COLORES DEL SEMÁFORO ==========
function getColorEstatus($estatus) {
    $colores = [
        'Pendiente'    => '#f1c40f',
        'En Proceso'   => '#3498db',
        'Aprobado'     => '#2ecc71',
        'No Aprobado'  => '#e74c3c',
        'Terminado'    => '#5d6d7e',
    ];
    return $colores[$estatus] ?? '#95a5a6';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="favicon.png">
    <title>Autorizar Solicitud</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #f8d4e8 0%, #a0c9eb 30%, #b7cce7 60%, #cac2f3 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 40px 20px;
        }
        .container {
            max-width: 700px;
            width: 100%;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        h2 {
            color: #333;
            border-bottom: 3px solid #1257b1;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .logo {
            width: 200px;  
            gap: 20px;
            height: auto;
        }    
        .btn-volver {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            margin-bottom: 20px;
        }
        
        /* ===== BUSCADOR ID ===== */
        .buscador-id {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }
        .buscador-id input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            text-align: center;
        }
        .buscador-id button {
            background: #1257b1;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        .buscador-id button:hover { background: #0e4590; }

        /* ===== TARJETA SOLICITUD ===== */
        .tarjeta-solicitud {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 24px;
            margin-bottom: 24px;
        }
        .tarjeta-solicitud h3 {
            margin-top: 0;
            color: #1257b1;
            font-size: 18px;
        }
        .dato {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .dato:last-child { border-bottom: none; }
        .dato-label { color: #666; font-weight: 600; }
        .dato-valor { color: #333; }

        /* ===== ESTATUS ACTUAL ===== */
        .estatus-actual {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 24px;
        }
        .estatus-actual h4 {
            margin: 0 0 10px 0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .estatus-actual .estatus-nombre {
            font-size: 28px;
            font-weight: bold;
        }

        /* ===== FORMULARIO CAMBIAR ESTATUS ===== */
        .form-autorizar {
            background: #f8f9fa;
            padding: 24px;
            border-radius: 10px;
        }
        .form-autorizar h3 {
            margin-top: 0;
            color: #333;
        }
        .grupo {
            margin-bottom: 16px;
        }
        .grupo label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #444;
            font-size: 14px;
        }
        .grupo select,
        .grupo input,
        .grupo textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }
        .grupo select:focus,
        .grupo input:focus,
        .grupo textarea:focus {
            outline: none;
            border-color: #1257b1;
        }
        .btn-autorizar {
            background: #2ecc71;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
        }
        .btn-autorizar:hover { background: #27ae60; }

        .alerta {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .alerta.ok { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alerta.error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    </style>
</head>
<body>
<div class="container">
    <a href="ver_solicitudes.php" class="btn-volver">← Volver al panel</a>
    <img src="logo.png" alt="Logo" class="logo">
    <h2> Autorizar / Cambiar Estatus</h2>

    <form method="GET" action="" class="buscador-id">
        <input type="text" name="buscar_folio" placeholder="Ingresa el folio del solicitante..." 
               value="<?= isset($_GET['buscar_folio']) ? htmlspecialchars($_GET['buscar_folio']) : '' ?>" 
               required autofocus>
        <button type="submit">Buscar</button>
    </form>

    <?= $mensaje ?>

    <?php if ($solicitud): ?>
        <?php $color = getColorEstatus($solicitud['estatus']); ?>

        <!-- ESTATUS ACTUAL -->
        <div class="estatus-actual" style="background: <?= $color ?>22; border: 2px solid <?= $color ?>;">
            <h4 style="color: <?= $color ?>;">Estatus Actual</h4>
            <div class="estatus-nombre" style="color: <?= $color ?>;"><?= htmlspecialchars($solicitud['estatus']) ?></div>
        </div>

        <!-- DATOS DE LA SOLICITUD -->
        <div class="tarjeta-solicitud">
            <h3> Solicitud #<?= htmlspecialchars($solicitud['folio']) ?></h3>
            <div class="dato">
                <span class="dato-label">Folio:</span>
                <span class="dato-valor" style="font-family:monospace; font-weight:bold; color:#1257b1;"><?= htmlspecialchars($solicitud['folio'] ?? 'SIN-FOLIO') ?></span>
            </div>
            <div class="dato">
                <span class="dato-label">Solicitante:</span>
                <span class="dato-valor"><?= htmlspecialchars($solicitud['nombre_solicitante']) ?></span>
            </div>
            <div class="dato">
                <span class="dato-label">Cargo:</span>
                <span class="dato-valor"><?= htmlspecialchars($solicitud['puesto']) ?></span>
            </div>
            <div class="dato">
                <span class="dato-label">Teléfono:</span>
                <span class="dato-valor"><?= htmlspecialchars($solicitud['telefono']) ?></span>
            </div>
            <div class="dato">
                <span class="dato-label">Calle:</span>
                <span class="dato-valor"><?= htmlspecialchars($solicitud['calle_beneficiada']) ?></span>
            </div>
            <div class="dato" style="flex-direction: column; align-items: flex-start; gap: 4px;">
                <span class="dato-label">Materiales Solicitados:</span>
                <div class="dato-valor" style="width: 100%; text-align: left;">
                    <?php if ($materialesResultado && $materialesResultado->num_rows > 0): ?>
                        <ul style="margin: 4px 0 0 0; padding-left: 18px; line-height: 1.5;">
                            <?php while ($mat = $materialesResultado->fetch_assoc()): ?>
                                <li>
                                    <?= htmlspecialchars($mat['descripcion']) ?> 
                                    (<b><?= htmlspecialchars($mat['cantidad']) ?></b> pzas, 
                                    <b><?= number_format((float)$mat['metros'], 2) ?></b> mts)
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <?= htmlspecialchars($solicitud['descripcion_mat1'] ?? 'Sin especificar') ?> 
                        (<?= $solicitud['cantidad_mat1'] ?? 0 ?> pzas, <?= $solicitud['metros_mat1'] ?? 0 ?> mts)
                    <?php endif; ?>
                </div>
            </div>
            <div class="dato">
                <span class="dato-label">Región:</span>
                <span class="dato-valor"><?= htmlspecialchars($solicitud['nombre_region']) ?></span>
            </div>
            <div class="dato">
                <span class="dato-label">Comunidad:</span>
                <span class="dato-valor"><?= htmlspecialchars($solicitud['nombre_comunidad']) ?></span>
            </div>
            <div class="dato">
                <span class="dato-label">Fecha de registro:</span>
                <span class="dato-valor"><?= htmlspecialchars($solicitud['fecha_solicitud']) ?></span>
            </div>
        </div>

        <form method="POST" action="procesar_autorizacion.php" class="form-autorizar">
            <h3>Cambiar Estatus</h3>
            <input type="hidden" name="folio" value="<?= htmlspecialchars($solicitud['folio'] ?? '') ?>">
            
            <div class="grupo">
                <label>Nuevo Estatus *</label>
                <select name="nuevo_estatus" required>
                    <option value="">Selecciona...</option>
                    <option value="Pendiente" <?= $solicitud['estatus'] == 'Pendiente' ? 'disabled' : '' ?>>🟡 Pendiente</option>
                    <option value="En Proceso" <?= $solicitud['estatus'] == 'En Proceso' ? 'disabled' : '' ?>>🔵 En Proceso</option>
                    <option value="Aprobado" <?= $solicitud['estatus'] == 'Aprobado' ? 'disabled' : '' ?>>🟢 Aprobado</option>
                    <option value="No Aprobado" <?= $solicitud['estatus'] == 'No Aprobado' ? 'disabled' : '' ?>>🔴 No Aprobado</option>
                    <option value="Terminado" <?= $solicitud['estatus'] == 'Terminado' ? 'disabled' : '' ?>>⚫ Terminado</option>
                </select>
            </div>
            
            <div class="grupo">
                <label>Comentario / Motivo</label>
                <textarea name="comentario" rows="3" placeholder="Ej: El oficio fue autorizado."></textarea>
            </div>
            
            <button type="submit" class="btn-autorizar"> Guardar Cambio de Estatus</button>
        </form>

    <?php elseif (!isset($_GET['buscar_folio'])): ?>
        <div style="text-align:center; color:#888; padding:40px;">
            <p style="font-size:18px;"> Ingresa un folio para buscar la solicitud</p>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
<?php $conexion->close(); ?>