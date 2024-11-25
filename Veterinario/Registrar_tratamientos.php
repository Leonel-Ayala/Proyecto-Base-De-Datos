<?php
header('Content-Type: text/html; charset=utf-8');

// Configuración de conexión
$host = 'localhost';
$port = '1521';
$dbname = 'XE';
$username = 'vetsol';
$password = 'oracle';

// Conexión OCI para listar
$conn_oci = oci_connect($username, $password, "//{$host}:{$port}/{$dbname}");
if (!$conn_oci) {
    $e = oci_error();
    die("Error de conexión OCI: " . $e['message']);
}

// Conexión PDO para inserciones, actualizaciones y eliminaciones
$dsn = "oci:dbname=//{$host}:{$port}/{$dbname}";
try {
    $conn_pdo = new PDO($dsn, $username, $password);
    $conn_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión PDO: " . $e->getMessage());
}

session_start();

$error = '';
$success = '';

// Comprobación de los datos recibidos
$id_mascota = $_POST['id_mascota'] ?? null;
$descripcion = $_POST['descripcion'] ?? null;
$id_producto = $_POST['id_producto'] ?? null;
$cantidad = $_POST['cantidad'] ?? null;
$nombre_veterinario = $_POST['nombre_veterinario'] ?? null;

if (!$id_mascota || !$descripcion || !$id_producto || !$cantidad || !$nombre_veterinario) {
    $error = "Todos los campos son obligatorios.";
} else {
    try {
        // Iniciar transacción
        $conn_pdo->beginTransaction();

        // Paso 1: Obtener el ID del veterinario
        $query_veterinario = "SELECT ID_VETERINARIO FROM LAROATLB_VETERINARIO WHERE Nombre = :nombre_veterinario";
        $stmt_veterinario = $conn_pdo->prepare($query_veterinario);
        $stmt_veterinario->bindParam(":nombre_veterinario", $nombre_veterinario);
        $stmt_veterinario->execute();
        $row = $stmt_veterinario->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            throw new Exception("No se encontró un veterinario con ese nombre.");
        }

        $id_veterinario = $row['ID_VETERINARIO'];

        // Paso 2: Insertar el tratamiento
        $query_tratamiento = "
            INSERT INTO LAROATLB_TRATAMIENTO (DESCRIPCION, FECHA, ID_MASCOTA, ID_VETERINARIO)
            VALUES (:descripcion, SYSDATE, :id_mascota, :id_veterinario)
        ";

        $stmt_tratamiento = $conn_pdo->prepare($query_tratamiento);
        $stmt_tratamiento->bindParam(":descripcion", $descripcion);
        $stmt_tratamiento->bindParam(":id_mascota", $id_mascota);
        $stmt_tratamiento->bindParam(":id_veterinario", $id_veterinario);

        // Ejecutar el insert del tratamiento
        $stmt_tratamiento->execute();

        // Paso 3: Recuperar el ID del tratamiento generado
        $query_id_tratamiento = "
            SELECT ID_TRATAMIENTO 
            FROM LAROATLB_TRATAMIENTO 
            WHERE ID_MASCOTA = :id_mascota 
              AND ID_VETERINARIO = :id_veterinario 
              AND DESCRIPCION = :descripcion 
            ORDER BY ID_TRATAMIENTO DESC
            FETCH FIRST 1 ROWS ONLY
        ";

        $stmt_id_tratamiento = $conn_pdo->prepare($query_id_tratamiento);
        $stmt_id_tratamiento->bindParam(":id_mascota", $id_mascota);
        $stmt_id_tratamiento->bindParam(":id_veterinario", $id_veterinario);
        $stmt_id_tratamiento->bindParam(":descripcion", $descripcion);
        $stmt_id_tratamiento->execute();
        $row_tratamiento = $stmt_id_tratamiento->fetch(PDO::FETCH_ASSOC);

        if (!$row_tratamiento) {
            throw new Exception("Error al recuperar el ID del tratamiento.");
        }

        $id_tratamiento = $row_tratamiento['ID_TRATAMIENTO'];

        // Paso 4: Insertar en la tabla detalle
        $query_detalle = "
            INSERT INTO LAROATLB_DETALLE_PRODUCTO_TRATAMIENTO (ID_TRATAMIENTO, ID_PRODUCTO, CANTIDAD)
            VALUES (:id_tratamiento, :id_producto, :cantidad)
        ";

        $stmt_detalle = $conn_pdo->prepare($query_detalle);
        $stmt_detalle->bindParam(":id_tratamiento", $id_tratamiento);
        $stmt_detalle->bindParam(":id_producto", $id_producto);
        $stmt_detalle->bindParam(":cantidad", $cantidad);

        // Ejecutar el insert del detalle
        $stmt_detalle->execute();

        // Confirmar la transacción
        $conn_pdo->commit();
        $success = "El tratamiento y su detalle se registraron correctamente.";

    } catch (Exception $e) {
        $conn_pdo->rollBack();
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Tratamiento para Mascota</title>
</head>
<body>
    <?php include("nav_veterinario.php"); ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Registrar Tratamiento para Mascota</h1>

        <div class="card shadow p-4">
            <form id="tratamientoForm" method="POST">
                <!-- Mensajes de error y éxito -->
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success" role="alert"><?php echo $success; ?></div>
                <?php endif; ?>

                <!-- ID de Mascota -->
                <div class="mb-3">
                    <label for="id_mascota" class="form-label">ID de Mascota:</label>
                    <input type="text" id="id_mascota" name="id_mascota" class="form-control" required>
                </div>

                <!-- Descripción del Tratamiento -->
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción del Tratamiento:</label>
                    <input type="text" id="descripcion" name="descripcion" class="form-control" required>
                </div>

                <!-- ID del Producto -->
                <div class="mb-3">
                    <label for="id_producto" class="form-label">ID del Producto:</label>
                    <input type="text" id="id_producto" name="id_producto" class="form-control" required>
                </div>

                <!-- Cantidad -->
                <div class="mb-3">
                    <label for="cantidad" class="form-label">Cantidad:</label>
                    <input type="number" id="cantidad" name="cantidad" class="form-control" required>
                </div>

                <!-- Nombre del Veterinario -->
                <div class="mb-3">
                    <label for="nombre_veterinario" class="form-label">Nombre del Veterinario:</label>
                    <input type="text" id="nombre_veterinario" name="nombre_veterinario" class="form-control" required>
                </div>

                <!-- Botón de enviar -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Registrar Tratamiento</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function cerrarSesion() {
            alert("Has cerrado sesión exitosamente.");
            window.location.href = '../hola.php'; // Redirige a la página de login
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
