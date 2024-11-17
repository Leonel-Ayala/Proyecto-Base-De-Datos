<?php
session_start();
session_destroy(); // Destruye todas las sesiones
header('Location: hola.php'); // Redirige al formulario de inicio de sesión
exit;
?>