<?php
session_start();
require 'conexion.php';

// Obtener datos del formulario
$folio      = $conexion->real_escape_string($_POST['folio'] ?? '');
$nuevo      = $conexion->real_escape_string($_POST['nuevo_estatus'] ?? '');
$comentario = $conexion->real_escape_string($_POST['comentario'] ?? '');

// Estatus permitidos
$permitidos = [
    'Pendiente',
    'En Proceso',
    'Aprobado',
    'No Aprobado',
    'Terminado'
];

// Validar datos
if (
    empty($folio) ||
    empty($nuevo) ||
    !in_array($nuevo, $permitidos)
) {
    header('Location: autorizar.php?error=datos_invalidos');
    exit;
}

// Obtener estatus actual de la solicitud
$res = $conexion->query(
    "SELECT * FROM solicitudes WHERE folio = '$folio'"
);

if (!$res || $res->num_rows == 0) {
    header('Location: autorizar.php?error=no_encontrado');
    exit;
}

// Obtener información de la solicitud
$row = $res->fetch_assoc();

$anterior = $row['estatus'];
$folioDB  = $row['folio'];

// Verificar que el nuevo estatus sea diferente
if ($anterior == $nuevo) {
    header(
        'Location: autorizar.php?buscar_folio=' .
        urlencode($folio) .
        '&error=mismo'
    );
    exit;
}

// Usuario que realiza el cambio
$usuario = $_SESSION['usuario'] ?? 'Direccion general';

// ----------------------------------------
// ACTUALIZAR TABLA SOLICITUDES
// ----------------------------------------

$actualizarSolicitud = $conexion->query(
    "UPDATE solicitudes 
     SET estatus = '$nuevo' 
     WHERE folio = '$folio'"
);

if (!$actualizarSolicitud) {
    die("Error al actualizar solicitudes: " . $conexion->error);
}

// ----------------------------------------
// ACTUALIZAR TABLA ESTATUS_SOLICITUD
// ----------------------------------------

$actualizarEstatus = $conexion->query(
    "UPDATE estatus_solicitud 
     SET estatus = '$nuevo',
         comentario = '$comentario'
     WHERE folio = '$folio'"
);

if (!$actualizarEstatus) {
    die("Error al actualizar estatus_solicitud: " . $conexion->error);
}

// ----------------------------------------
// GUARDAR HISTORIAL
// ----------------------------------------

$historial = $conexion->query(
    "INSERT INTO historial_estatus
    (
        folio,
        estatus_anterior,
        estatus_nuevo,
        comentario,
        usuario
    )
    VALUES
    (
        '$folioDB',
        '$anterior',
        '$nuevo',
        '$comentario',
        '$usuario'
    )"
);

if (!$historial) {
    die("Error al guardar historial: " . $conexion->error);
}

// ----------------------------------------
// REGRESAR A AUTORIZAR
// ----------------------------------------

header(
    'Location: autorizar.php?buscar_folio=' .
    urlencode($folio) .
    '&exito=1'
);

exit;
?>