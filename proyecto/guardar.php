<?php
require 'conexion.php';

date_default_timezone_set('America/Mexico_City');

// Funcion que genera el folio
function generarFolio($conexion)
{
    $prefijo = 'DG';
    $fechaHoy = date('dmY');

    $sql = "SELECT MAX(id_solicitante) AS ultimo_id FROM solicitudes";
    $resultado = $conexion->query($sql);

    if ($resultado && $fila = $resultado->fetch_assoc()) {
        $ultimo_id = (int)$fila['ultimo_id'];
        $numero = $ultimo_id + 1;
    } else {
        $numero = 1;
    }

    $consecutivo = str_pad($numero, 3, '0', STR_PAD_LEFT);
    return $prefijo . '-' . $fechaHoy . $consecutivo;
}

// Datos del solicitante
$nombre_solicitante = trim($_POST['nombre_solicitante'] ?? '');
$puesto             = trim($_POST['puesto'] ?? '');
$telefono           = trim($_POST['telefono'] ?? '');
$calle_beneficiada  = trim($_POST['calle_beneficiada'] ?? '');
$nombre_comunidad   = trim($_POST['comunidad'] ?? '');

// Validacion de los datos del solicitante
if (empty($nombre_solicitante) || empty($nombre_comunidad)) {
    die("
        <h2>Error</h2>
        <p>Debes completar el nombre del solicitante y la comunidad.</p>
        <a href='formulario.php'>Regresar</a>
    ");
}

// Conversion de id al nombre region
$regiones_map = [
    1 => 'I HUITZIZILAPAN',
    2 => 'II TLALMIMILOLPAN',
    3 => 'III ATARASQUILLO',
    4 => 'IV AMEYALCO',
    5 => 'V LERMA',
    6 => 'VI TULTEPEC',
    7 => 'VII PERALTA-XOCHICUAUTLA-ANALCO',
    8 => 'VIII PARTIDAS - PARQUE INDUSTRIAL'
];

$id_region_num = intval($_POST['nombre_region'] ?? 0);
$nombre_region = $regiones_map[$id_region_num] ?? '';

if (empty($nombre_region)) {
    die("
        <h2>Error</h2>
        <p>Debes seleccionar una región.</p>
        <a href='formulario.php'>Regresar</a>
    ");
}

// Variable que recibe los materiales
$materiales = $_POST['materiales'] ?? [];

// Valida que por lo menos haya un material
if (!is_array($materiales) || count($materiales) === 0) {
    die("
        <h2>Error</h2>
        <p>Debes agregar por lo menos un material.</p>
        <a href='formulario.php'>Regresar</a>
    ");
}

// Validacion del material
$materiales_validos = [];

foreach ($materiales as $indice => $material) {
    $cantidad    = intval($material['cantidad'] ?? 0);
    $descripcion = trim($material['descripcion'] ?? '');
    $categoria   = trim($material['categoria'] ?? '');
    $metros      = floatval($material['metros'] ?? 0);

    // Valida la cantidad
    if ($cantidad <= 0) {
        die("
            <h2>Error en material</h2>
            <p>La cantidad del material " . ($indice + 1) . " no es válida.</p>
            <a href='formulario.php'>Regresar</a>
        ");
    }

    // Valida la descripcion
    if (empty($descripcion)) {
        die("
            <h2>Error en material</h2>
            <p>Debes escribir la descripción del material " . ($indice + 1) . ".</p>
            <a href='formulario.php'>Regresar</a>
        ");
    }

    // Valida la categoria
    if ($categoria !== 'drenaje' && $categoria !== 'agua potable') {
        die("
            <h2>Error en material</h2>
            <p>Debes seleccionar una categoría para el material " . ($indice + 1) . ".</p>
            <a href='formulario.php'>Regresar</a>
        ");
    }

    // Valida los metros
    if ($metros <= 0) {
        if ($categoria === 'drenaje') {
            $metros = $cantidad * 0.9;
        } elseif ($categoria === 'agua potable') {
            $metros = $cantidad * 0.6;
        }
    }

    $materiales_validos[] = [
        'cantidad'    => $cantidad,
        'descripcion' => $descripcion,
        'categoria'   => $categoria,
        'metros'      => $metros
    ];
}

// Generacion de folio
$folio = generarFolio($conexion);

// Primer material
$primer_material = $materiales_validos[0];
$cantidad1    = $primer_material['cantidad'];
$descripcion1 = $primer_material['descripcion'];
$categoria1   = $primer_material['categoria'];
$metros1      = $primer_material['metros'];

// Inicia la transaccion
$conexion->begin_transaction();

try {
    // Guarda la solicitud
    $stmt = $conexion->prepare("
        INSERT INTO solicitudes (
            folio,
            nombre_solicitante,
            puesto,
            telefono,
            calle_beneficiada,
            cantidad_mat1,
            descripcion_mat1,
            categoria_mat1,
            metros_mat1,
            nombre_region,
            nombre_comunidad
        )
        VALUES (?,?,?,?,?,?,?,?,?,?,?)
    ");

    if (!$stmt) {
        throw new Exception("Error al preparar la solicitud: " . $conexion->error);
    }

    $stmt->bind_param(
        "sssssissdss",
        $folio,
        $nombre_solicitante,
        $puesto,
        $telefono,
        $calle_beneficiada,
        $cantidad1,
        $descripcion1,
        $categoria1,
        $metros1,
        $nombre_region,
        $nombre_comunidad
    );

    if (!$stmt->execute()) {
        throw new Exception("Error al guardar la solicitud: " . $stmt->error);
    }

    $id_solicitante = $conexion->insert_id;
    $stmt->close();

    // Inserccion de materiales en tabla relacionada
    $stmtMaterial = $conexion->prepare("
        INSERT INTO material_solicitado (
            id_solicitante,
            folio,
            cantidad,
            descripcion,
            categoria,
            metros
        )
        VALUES (?,?,?,?,?,?)
    ");

    if (!$stmtMaterial) {
        throw new Exception("Error al preparar los materiales: " . $conexion->error);
    }

    foreach ($materiales_validos as $material) {
        $cantidad    = $material['cantidad'];
        $descripcion = $material['descripcion'];
        $categoria   = $material['categoria'];
        $metros      = $material['metros'];

        $stmtMaterial->bind_param(
            "isissd",
            $id_solicitante,
            $folio,
            $cantidad,
            $descripcion,
            $categoria,
            $metros
        );

        if (!$stmtMaterial->execute()) {
            throw new Exception("Error al guardar un material: " . $stmtMaterial->error);
        }
    }

    $stmtMaterial->close();
    $conexion->commit();

    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Solicitud guardada</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #f8d4e8, #a0c9eb, #cac2f3);
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 0;
            }

            .mensaje {
                background: white;
                padding: 35px;
                border-radius: 15px;
                text-align: center;
                box-shadow: 0 5px 20px rgba(0,0,0,0.15);
                width: 90%;
                max-width: 500px;
            }

            h2 {
                color: #356cc0;
            }

            .folio {
                font-size: 24px;
                font-weight: bold;
                color: #1b67da;
                margin: 20px 0;
            }

            .acciones {
                display: flex;
                flex-direction: column;
                gap: 10px;
                margin-top: 15px;
            }

            a {
                display: inline-block;
                padding: 12px 20px;
                background: #356cc0;
                color: white;
                text-decoration: none;
                border-radius: 8px;
                font-weight: bold;
                transition: background 0.3s;
            }

            a:hover {
                background: #274d91;
            }

            a.btn-oficio {
                background-color: #27a4ec;
            }

            a.btn-oficio:hover {
                background-color: #1a82be;
            }
        </style>
    </head>
    <body>
        <div class="mensaje">
            <h2>¡Solicitud guardada correctamente!</h2>
            <p>Se registraron <strong><?= count($materiales_validos) ?></strong> material(es).</p>

            <div class="folio">
                Folio: <?= htmlspecialchars($folio) ?>
        </div>
    </body>
    </html>
    <?php

} catch (Exception $e) {
    $conexion->rollback();

    echo "
        <h2>Error al guardar</h2>
        <p>No se pudo guardar la solicitud.</p>
        <p>" . htmlspecialchars($e->getMessage()) . "</p>
        <a href='formulario.php'>Regresar al formulario</a>
    ";
}

$conexion->close();
?>