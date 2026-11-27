<?php
session_start();
require_once 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre   = trim($_POST['nombre'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol      = $_POST['rol'] ?? '';

    // Validar que no haya campos vacíos
    if (empty($nombre) || empty($correo) || empty($password) || empty($rol)) {
        echo "<script>alert('Por favor completa todos los campos.'); window.history.back();</script>";
        exit;
    }

    // 1. Verificar si el correo ya existe en la tabla usuario usando MySQLi
    $checkStmt = $conexion->prepare("SELECT id FROM usuario WHERE email = ?");
    if ($checkStmt) {
        $checkStmt->bind_param("s", $correo);
        $checkStmt->execute();
        $checkStmt->store_result();
        
        if ($checkStmt->num_rows > 0) {
            echo "<script>alert('Este correo electrónico ya está registrado.'); window.history.back();</script>";
            $checkStmt->close();
            exit;
        }
        $checkStmt->close();
    }

    // 2. Encriptar la contraseña de manera segura
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // 3. Insertar el nuevo usuario en la base de datos usando MySQLi
    $stmt = $conexion->prepare("INSERT INTO usuario (nombre_usuario, email, contraseña, rol) VALUES (?, ?, ?, ?)");
    
    if ($stmt) {
        $stmt->bind_param("ssss", $nombre, $correo, $passwordHash, $rol);
        
        if ($stmt->execute()) {
            echo "<script>alert('¡Registro exitoso! Ya puedes iniciar sesión.'); window.location.href='inicio_sesion.html';</script>";
        } else {
            echo "<script>alert('Hubo un error al registrar el usuario.'); window.history.back();</script>";
        }
        
        $stmt->close();
    } else {
        echo "<script>alert('Error en el sistema al preparar el registro.'); window.history.back();</script>";
    }

} else {
    header("Location: registro.php");
    exit;
}
?>