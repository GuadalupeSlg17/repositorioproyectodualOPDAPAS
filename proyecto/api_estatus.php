<?php
/**
 * api_estatus.php
 * API REST para gestionar estatus de solicitudes
 * Métodos: GET (consultar), POST (actualizar), PUT (cambiar estatus)
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');

require_once 'EstatusSolicitud.php';

// Configuración de base de datos (ajusta a tus credenciales)
$host = 'localhost';
$dbname = 'entrega_materiales';
$username = '';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

$modelo = new EstatusSolicitud($pdo);
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // ───────────────────────────────────────────────
    // GET  →  Consultar estatus (por folio o listado)
    // ───────────────────────────────────────────────
    case 'GET':
        if (isset($_GET['folio'])) {
            $data = $modelo->obtenerPorFolio($_GET['folio']);
            if ($data) {
                echo json_encode(['ok' => true, 'data' => $data]);
            } else {
                http_response_code(404);
                echo json_encode(['ok' => false, 'error' => 'Folio no encontrado']);
            }
        } elseif (isset($_GET['resumen'])) {
            echo json_encode(['ok' => true, 'data' => $modelo->resumenSemaforo()]);
        } else {
            $estatus = $_GET['estatus'] ?? null;
            $folio   = $_GET['buscar']  ?? null;
            echo json_encode(['ok' => true, 'data' => $modelo->listar($estatus, $folio)]);
        }
        break;

    // ───────────────────────────────────────────────
    // POST  →  Registrar nuevo estatus (pendiente)
    // ───────────────────────────────────────────────
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        $folio   = $input['folio']   ?? '';
        $usuario = $input['usuario'] ?? 'sistema';

        if (empty($folio)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'El folio es obligatorio']);
            break;
        }

        $ok = $modelo->registrar($folio, $usuario);
        echo json_encode(['ok' => $ok, 'mensaje' => $ok ? 'Estatus registrado' : 'Error al registrar']);
        break;

    // ───────────────────────────────────────────────
    // PUT  →  Actualizar estatus existente
    // ───────────────────────────────────────────────
    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        $folio       = $input['folio']       ?? '';
        $estatus     = $input['estatus']     ?? '';
        $comentario  = $input['comentario']  ?? '';
        $usuario     = $input['usuario']    ?? 'sistema';

        if (empty($folio) || empty($estatus)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'Folio y estatus son obligatorios']);
            break;
        }

        $resultado = $modelo->actualizar($folio, $estatus, $comentario, $usuario);
        if (!$resultado['ok']) {
            http_response_code(400);
        }
        echo json_encode($resultado);
        break;

    default:
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'Método no permitido']);
        break;
}