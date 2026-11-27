<?php

require 'conexion.php';

date_default_timezone_set('America/Mexico_City');

//Variables que reciben los datos.

$folio = trim($_POST['folio'] ?? '');

$cantidad = intval($_POST['cantidad'] ?? 0);

$descripcion = trim(
    $_POST['descripcion'] ?? ''
);

$categoria = trim(
    $_POST['categoria'] ?? ''
);

$metros = floatval(
    $_POST['metros'] ?? 0
);

//Validacion de datos.
if (empty($folio)) {
    die("No se recibió el folio.");
}

if ($cantidad <= 0) {
    die("La cantidad no es válida.");
}

if (empty($descripcion)) {
    die("La descripción es obligatoria.");
}

if (empty($categoria)) {
    die("La categoría es obligatoria.");
}

if ($metros <= 0) {
    die("Los metros no son válidos.");
}
//Consulta  para buscar por folio.

$stmt = $conexion->prepare(
    "SELECT id_solicitante
     FROM solicitudes
     WHERE folio = ?
     LIMIT 1"
);

$stmt->bind_param("s", $folio);

$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {

    $stmt->close();
    $conexion->close();

    die("No se encontró una solicitud con ese folio.");
}


$solicitud = $resultado->fetch_assoc();

$id_solicitante = intval(
    $solicitud['id_solicitante']
);

$stmt->close();

//Guardado de material

$stmt = $conexion->prepare(
    "INSERT INTO material_solicitado (
        id_solicitante,
        folio,
        cantidad,
        descripcion,
        categoria,
        metros
    )
    VALUES (?, ?, ?, ?, ?, ?)"
);


$stmt->bind_param(
    "isissd",
    $id_solicitante,
    $folio,
    $cantidad,
    $descripcion,
    $categoria,
    $metros
);


if ($stmt->execute()) {

    echo "
    <!DOCTYPE html>
    <html>

    <head>

        <meta charset='UTF-8'>

        <title>
            Material agregado
        </title>

        <style>

            body {
                font-family: Arial;
                background: linear-gradient(
                    135deg,
                    #f8d4e8,
                    #a0c9eb,
                    #cac2f3
                );

                min-height: 100vh;

                display: flex;

                justify-content: center;

                align-items: center;
            }

            .mensaje {
                background: white;

                padding: 40px;

                border-radius: 15px;

                text-align: center;

                box-shadow:
                    0 4px 15px
                    rgba(0,0,0,.2);
            }

            h2 {
                color: #356cc0;
            }

            .folio {
                font-weight: bold;
                color: #1b67da;
                font-size: 22px;
                margin: 15px;
            }

            a {
                display: inline-block;
                background: #356cc0;
                color: white;
                text-decoration: none;
                padding: 12px 20px;
                border-radius: 8px;
            }

        </style>

    </head>

    <body>

        <div class='mensaje'>

            <h2>
                ¡Material agregado correctamente!
            </h2>

            <p>
                El material se agregó a la solicitud:
            </p>

            <div class='folio'>
                $folio
            </div>

            <a href='registro_material.php'>
                Ver materiales
            </a>

        </div>

    </body>

    </html>
    ";

} else {

    echo "Error al guardar el material: "
        . htmlspecialchars($stmt->error);
}


$stmt->close();
$conexion->close();

?>