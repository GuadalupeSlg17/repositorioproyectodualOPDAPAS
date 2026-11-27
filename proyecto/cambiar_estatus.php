<?php
session_start();
require 'conexion.php';
header('Content-Type: application/json');

$id         = intval($_POST['id'] ?? 0);
$folio      = $conexion->real_escape_string($_POST['folio'] ?? '');
$estatus    = $conexion->real_escape_string($_POST['estatus'] ?? '');
$comentario = $conexion->real_escape_string($_POST['comentario'] ?? '');
$usuario    = $_SESSION['usuario'] ?? 'sistema';

$permitidos = ['Pendiente', 'En Proceso', 'Aprobado', 'No Aprobado', 'Terminado'];

if ($id <= 0 || empty($estatus) || !in_array($estatus, $permitidos)) {
    echo json_encode(['ok' => false, 'error' => 'Datos inválidos']);
    exit;
}

// 1. Obtener estatus actual para el historial
$res = $conexion->query("SELECT estatus, folio FROM solicitudes WHERE id_solicitante = $id");
if (!$res || $res->num_rows == 0) {
    echo json_encode(['ok' => false, 'error' => 'Solicitud no encontrada']);
    exit;
}
$row = $res->fetch_assoc();
$anterior = $row['estatus'];
$folioDB  = $row['folio'] ?: $folio;

// 2. Si es el mismo estatus, no hacer nada
if ($anterior == $estatus) {
    echo json_encode(['ok' => false, 'error' => 'El estatus es igual al actual']);
    exit;
}

// 3. Iniciar transacción
$conexion->begin_transaction();

try {
    // Guardar en historial
    $hist = $conexion->prepare("INSERT INTO historial_estatus 
        (id_solicitante, folio, estatus_anterior, estatus_nuevo, comentario, usuario) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $hist->bind_param("isssss", $id, $folioDB, $anterior, $estatus, $comentario, $usuario);
    $hist->execute();

    // Actualizar la solicitud
    $sql = "UPDATE solicitudes SET estatus = '$estatus' WHERE id_solicitante = $id";
    $conexion->query($sql);

    $conexion->commit();
    echo json_encode(['ok' => true, 'anterior' => $anterior, 'nuevo' => $estatus]);

} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}

$conexion->close();