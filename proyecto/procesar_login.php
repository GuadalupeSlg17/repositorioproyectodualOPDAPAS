<?php
session_start();
require_once 'conexion.php'; // Carga la conexión MySQLi ($conexion)

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo    = trim($_POST['correo'] ?? '');
    $password  = $_POST['contraseña'] ?? '';
    $rol       = $_POST['rol'] ?? '';

    if (empty($correo) || empty($password) || empty($rol)) {
        echo "<script>alert('Por favor ingresa todos los datos.'); window.history.back();</script>";
        exit;
    }

    // Consulta usando MySQLi con sentencia preparada para mayor seguridad
    $stmt = $conexion->prepare("SELECT * FROM usuario WHERE email = ? AND rol = ?");
    
    if ($stmt) {
        $stmt->bind_param("ss", $correo, $rol);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $usuario = $resultado->fetch_assoc();

        // Verificamos si existe el usuario y si la contraseña coincide
        if ($usuario && password_verify($password, $usuario['contraseña'])) {
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['nombre_usuario'] = $usuario['nombre_usuario'];
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['rol'] = $usuario['rol'];

            // Redirección según el rol seleccionado
            if ($rol === 'Direccion_General' || $rol === 'direccion_general') {
                header("Location: panel_direccion.php");
            } else {
                header("Location: panel_infraestructura.php");
            }
            exit;
        } else {
            echo "<script>alert('Correo, contraseña o rol incorrectos.'); window.history.back();</script>";
        }
        
        $stmt->close();
    } else {
        echo "<script>alert('Error en la consulta del sistema.'); window.history.back();</script>";
    }
}
?>