<?php
header('Content-Type: application/json');
require 'conexion.php';

$folio = isset($_GET['folio']) ? trim($_GET['folio']) : '';
$parent_id = isset($_GET['parent_id']) && $_GET['parent_id'] !== '' ? intval($_GET['parent_id']) : NULL;

if (empty($folio)) {
    echo json_encode([]);
    exit;
}

if ($parent_id === NULL) {
    $stmt = $conexion->prepare("SELECT * FROM elementos WHERE folio = ? AND parent_id IS NULL ORDER BY tipo ASC, nombre ASC");
    $stmt->bind_param("s", $folio);
} else {
    $stmt = $conexion->prepare("SELECT * FROM elementos WHERE folio = ? AND parent_id = ? ORDER BY tipo ASC, nombre ASC");
    $stmt->bind_param("si", $folio, $parent_id);
}

$stmt->execute();
$resultado = $stmt->get_result();
echo json_encode($resultado->fetch_all(MYSQLI_ASSOC));
?>