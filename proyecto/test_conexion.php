<?php
$host = 'localhost';
$user = 'root';
$pass = '';  // XAMPP por defecto no tiene contraseña
$db   = 'entrega_materiales';  // <-- PON EL NOMBRE REAL DE TU BD

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die('ERROR DE CONEXIÓN: ' . $conexion->connect_error);
}

echo 'CONEXIÓN EXITOSA a la base de datos: ' . $db . '<br><br>';

// Verificar si existe la tabla solicitudes
$res = $conexion->query("SHOW TABLES LIKE 'solicitudes'");
if ($res && $res->num_rows > 0) {
    echo 'Tabla "solicitudes" existe<br>';
} else {
    echo 'Tabla "solicitudes" NO existe<br>';
}

// Verificar si existe la tabla historial_estatus
$res2 = $conexion->query("SHOW TABLES LIKE 'historial_estatus'");
if ($res2 && $res2->num_rows > 0) {
    echo 'Tabla "historial_estatus" existe<br>';
} else {
    echo 'Tabla "historial_estatus" NO existe<br>';
}

// Verificar si el campo folio existe
$res3 = $conexion->query("SHOW COLUMNS FROM solicitudes LIKE 'folio'");
if ($res3 && $res3->num_rows > 0) {
    echo 'Campo "folio" existe en solicitudes<br>';
} else {
    echo 'Campo "folio" NO existe en solicitudes<br>';
}

$conexion->close();
?>