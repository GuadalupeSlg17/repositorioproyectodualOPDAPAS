<?php
header('Content-Type: application/json');
require 'conexion.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data['id'])) {
    $id = intval($data['id']);

    // Obtiene la información del elemento antes de borrarlo
    $stmt = $conexion->prepare("SELECT tipo, ruta_archivo FROM elementos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result()->fetch_assoc();

    if ($resultado) {
        // Si es un archivo físico, lo borra de uploads
        if ($resultado['tipo'] !== 'carpeta' && !empty($resultado['ruta_archivo'])) {
            if (file_exists($resultado['ruta_archivo'])) {
                unlink($resultado['ruta_archivo']);
            }
        }

        // 3. Si es carpeta, eliminar archivos físicos hijos
        if ($resultado['tipo'] === 'carpeta') {
            $stmtSub = $conexion->prepare("SELECT ruta_archivo FROM elementos WHERE parent_id = ? AND tipo != 'carpeta'");
            $stmtSub->bind_param("i", $id);
            $stmtSub->execute();
            $resSub = $stmtSub->get_result();
            while ($sub = $resSub->fetch_assoc()) {
                if (!empty($sub['ruta_archivo']) && file_exists($sub['ruta_archivo'])) {
                    unlink($sub['ruta_archivo']);
                }
            }

            // Borrar subelementos en BD
            $stmtDelSub = $conexion->prepare("DELETE FROM elementos WHERE parent_id = ?");
            $stmtDelSub->bind_param("i", $id);
            $stmtDelSub->execute();
        }

        // 4. Borrar el registro principal
        $stmtDelete = $conexion->prepare("DELETE FROM elementos WHERE id = ?");
        $stmtDelete->bind_param("i", $id);

        if ($stmtDelete->execute()) {
            echo json_encode(["status" => "success"]);
        } else {
            echo json_encode(["status" => "error", "mensaje" => "Error al eliminar en la BD"]);
        }
    } else {
        echo json_encode(["status" => "error", "mensaje" => "Elemento no encontrado"]);
    }
} else {
    echo json_encode(["status" => "error", "mensaje" => "ID no recibido"]);
}
?>