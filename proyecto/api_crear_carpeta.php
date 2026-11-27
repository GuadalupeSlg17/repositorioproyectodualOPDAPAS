<?php
header('Content-Type: application/json');
require 'conexion.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['nombre']) && !empty($data['folio'])) {
    $nombre = trim($data['nombre']);
    $folio = trim($data['folio']);
    $parent_id = !empty($data['parent_id']) ? intval($data['parent_id']) : NULL;

    $stmt = $conexion->prepare("INSERT INTO elementos (folio, nombre, tipo, parent_id) VALUES (?, ?, 'carpeta', ?)");
    $stmt->bind_param("ssi", $folio, $nombre, $parent_id);
    
    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Error al crear la carpeta"]);
    }
} else {
    echo json_encode(["status" => "error", "mensaje" => "Faltan datos requeridos"]);
}
?>