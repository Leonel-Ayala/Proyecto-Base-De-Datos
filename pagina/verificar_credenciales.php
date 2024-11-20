<?php
session_start();
$host = 'localhost';
$port = '1521';
$dbname = 'XE'; 
$username = 'vetsol';
$password = 'oracle';

try {
    $dsn = "oci:dbname=//$host:$port/$dbname;charset=UTF8";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepara la consulta SQL
    $sql = "SELECT ROL_USUARIO FROM LAROATLB_USUARIOS 
            WHERE NOMBRE_USUARIO=:nombre_usuario 
            AND CONTRA_USUARIO=:contra_usuario";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nombre_usuario', $username);
    $stmt->bindParam(':contra_usuario', $password);
    
    $stmt->execute();

    // Verifica si se encontró el usuario
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Extrae el rol del usuario
        $rol_usuario = $row['ROL_USUARIO'];

        // Redirige según el rol
        switch ($rol_usuario) {
            case 'ADMIN':
                header('Location: Super_usuario/Super_Usuario.php');
                break;
            case 'VETERINARIO':
                header('Location: Veterinario/borrar.php');
                break;
            case 'SECRETARIA':
                header('Location: Secretaria/Secretaria.php');
                break;
            default:
                echo "Error: Rol no reconocido.";
        }
    } else {
        // Credenciales incorrectas
        echo "Credenciales incorrectas. <a href='index.php'>Volver al inicio de sesión</a>";
    }
} else {
    header('Location: index.php');
}
?>

