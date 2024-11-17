<?php
session_start();

// Define una lista de usuarios autorizados con roles
$usuarios_validos = [
    'superadmin' => ['password' => 'superadmin', 'role' => 'super_usuario'],
    'veterinario' => ['password' => 'veterinario', 'role' => 'veterinario'],
    'secretaria' => ['password' => 'secretaria', 'role' => 'secretaria']
];

// Verifica si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Comprueba si el usuario existe y la contraseña coincide
    if (isset($usuarios_validos[$username]) && $usuarios_validos[$username]['password'] === $password) {
        // Establece la sesión del usuario
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $usuarios_validos[$username]['role'];

        // Redirige a la página correspondiente según el rol
        switch ($_SESSION['role']) {
            case 'super_usuario':
                header('Location: super_usuario.php');
                break;
            case 'veterinario':
                header('Location: veterinario.php');
                break;
            case 'secretaria':
                header('Location: secretaria.php');
                break;
            default:
                echo "Error: Rol no reconocido.";
                session_destroy();
        }
    } else {
        // Credenciales incorrectas
        echo "Credenciales incorrectas. <a href='index.php'>Volver al inicio de sesión</a>";
        exit;
    }
} else {
    // Si no se envió el formulario, redirige al formulario de inicio de sesión
    header('Location: index.php');
    exit;
}
