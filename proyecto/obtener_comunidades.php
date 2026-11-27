<?php
require 'conexion.php';

// Verificar que llegó el parámetro
if (!isset($_GET['id_region']) || empty($_GET['id_region'])) {
    echo json_encode([]);
    exit;
}

$id_region = intval($_GET['id_region']); // Convertir a número entero

// Buscar las comunidades directamente por id_region
$stmt = $conexion->prepare("SELECT nombre_comunidad FROM comunidades WHERE id_region = ? ORDER BY nombre_comunidad");
$stmt->bind_param("i", $id_region);
$stmt->execute();
$resultado = $stmt->get_result();

$comunidades = [];
while ($fila = $resultado->fetch_assoc()) {
    $comunidades[] = $fila;
}

header('Content-Type: application/json');
echo json_encode($comunidades);

$stmt->close();
$conexion->close();
?>