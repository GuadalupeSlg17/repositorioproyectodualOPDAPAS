<?php
require 'conexion.php';

// Validar que se recibió un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: panel.php');
    exit;
}

$id = intval($_GET['id']);

// Verificar que la solicitud existe antes de eliminar
$check = $conexion->prepare("SELECT id_solicitante FROM solicitudes WHERE id_solicitante = ?");
$check->bind_param("i", $id_solicitante);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    $check->close();
    die("Solicitud no encontrada.");
}
$check->close();

// Eliminar la solicitud
$stmt = $conexion->prepare("DELETE FROM solicitudes WHERE id_solicitante = ?");
$stmt->bind_param("i", $id_solicitante);

if ($stmt->execute()) {
    // Éxito: redirigir al panel
    header('Location: panel.php?eliminado=1');
    exit;
} else {
    // Error
    header('Location: panel.php?error=1');
    exit;
}

$stmt->close();
$conexion->close();
?>