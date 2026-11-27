<?php
session_start(); // 1. Abrimos la sesión existente para poder manipularla

// 2. Limpiamos todas las variables de la sesión
$_SESSION = array();

// 3. Destruimos la sesión por completo en el servidor
session_destroy();

// 4. Redirigimos al usuario a la pantalla de inicio de sesión (index.php)
header("Location: inicio_sesion.html");
exit;
?>