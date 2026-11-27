<?php
require 'conexion.php';

// Nombre del archivo de listado (ajústalo si es diferente)
$archivo_listado = 'ver_registro_material.php';

// Validar ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: $archivo_listado");
    exit;
}

$id = intval($_GET['id']);

// Verificar que existe
$check = $conexion->prepare("SELECT id_material FROM material_solicitado WHERE id_material = ?");
$check->bind_param("i", $id);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    $check->close();
    die("Material no encontrado.");
}
$check->close();

// Eliminar
$stmt = $conexion->prepare("DELETE FROM material_solicitado WHERE id_material = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: $archivo_listado?eliminado=1");
    exit;
} else {
    header("Location: $archivo_listado?error=1");
    exit;
}

$stmt->close();
$conexion->close();
?>