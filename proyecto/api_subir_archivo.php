<?php
header('Content-Type: application/json');
require 'conexion.php';

// Verificar que se haya enviado un archivo
if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(["status" => "error", "mensaje" => "No se recibió ningún archivo o hubo un error al cargarlo."]);
    exit;
}

$file = $_FILES['archivo'];
$nombre = $_POST['nombre'] ?? $file['name'];
$descripcion = $_POST['descripcion'] ?? '';
$folio = $_POST['folio'] ?? 'GENERAL';
$parent_id = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? intval($_POST['parent_id']) : null;

// Obtener extensión y peso
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$peso = $file['size'];

// Definir tipo de documento
$tipo = 'pdf';
if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
    $tipo = 'imagen';
}

// Generar un nombre único para la carpeta uploads
$directorio = 'uploads/';
if (!file_exists($directorio)) {
    mkdir($directorio, 0777, true);
}

$nombreGuardado = uniqid('file_') . '.' . $ext;
$rutaFinal = $directorio . $nombreGuardado;

// Mover el archivo físico a /uploads
if (move_uploaded_file($file['tmp_name'], $rutaFinal)) {
    // Insertar en la base de datos
    $stmt = $conexion->prepare("INSERT INTO elementos (nombre, descripcion, tipo, ruta_archivo, peso, folio, parent_id, fecha_creacion) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssssisi", $nombre, $descripcion, $tipo, $rutaFinal, $peso, $folio, $parent_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Error al guardar en la base de datos: " . $conexion->error]);
    }
} else {
    echo json_encode(["status" => "error", "mensaje" => "No se pudo mover el archivo a la carpeta uploads."]);
}
?>
