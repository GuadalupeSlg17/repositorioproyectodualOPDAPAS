<?php
$host = "localhost";
$db   = "entrega_materiales";
$user = "root";
$pass = "";

// Crear la conexión usando MySQLi (con la variable $conexion que ya usaban tus archivos)
$conexion = new mysqli($host, $user, $pass, $db);

// Verificar si hubo un error de conexión
if ($conexion->connect_error) {
    die("<script>alert('Error de conexión a la Base de Datos: " . addslashes($conexion->connect_error) . "'); window.history.back();</script>");
}

// Configurar el juego de caracteres a utf8mb4
$conexion->set_charset("utf8mb4");
?>