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

// Conexión PDO para transacciones
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

// Listar veterinarios, productos y mascotas
$VETERINARIOS = [];
$PRODUCTOS = [];
$MASCOTAS = [];

// Veterinarios
$cursor_veterinarios = oci_new_cursor($conn_oci);
$stmt_veterinarios = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_VETERINARIOS(:p_cursor); END;");
oci_bind_by_name($stmt_veterinarios, ':p_cursor', $cursor_veterinarios, -1, OCI_B_CURSOR);
oci_execute($stmt_veterinarios);
oci_execute($cursor_veterinarios);
while ($row = oci_fetch_assoc($cursor_veterinarios)) {
    $VETERINARIOS[] = $row;
}

// Productos
$cursor_productos = oci_new_cursor($conn_oci);
$stmt_productos = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_PRODUCTOS(:p_cursor); END;");
oci_bind_by_name($stmt_productos, ':p_cursor', $cursor_productos, -1, OCI_B_CURSOR);
oci_execute($stmt_productos);
oci_execute($cursor_productos);
while ($row = oci_fetch_assoc($cursor_productos)) {
    $PRODUCTOS[] = $row;
}

// Mascotas
$cursor_mascotas = oci_new_cursor($conn_oci);
$stmt_mascotas = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_MASCOTAS(:p_cursor); END;");
oci_bind_by_name($stmt_mascotas, ':p_cursor', $cursor_mascotas, -1, OCI_B_CURSOR);
oci_execute($stmt_mascotas);
oci_execute($cursor_mascotas);
while ($row = oci_fetch_assoc($cursor_mascotas)) {
    $MASCOTAS[] = $row;
}

// Comprobación de los datos recibidos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_mascota = $_POST['id_mascota'] ?? null;
    $descripcion = $_POST['descripcion'] ?? null;
    $id_producto = $_POST['id_producto'] ?? null;
    $cantidad = $_POST['cantidad'] ?? null;
    $id_veterinario = $_POST['id_veterinario'] ?? null;

    if (!$id_mascota || !$descripcion || !$id_producto || !$cantidad || !$id_veterinario) {
        $error = "Todos los campos son obligatorios.";
    } else {
        try {
            // Iniciar transacción
            $conn_pdo->beginTransaction();

            // Paso 1: Insertar el tratamiento
            $query_tratamiento = "
                BEGIN LAROATLB_INSERTA_TRATAMIENTOS(:descripcion,:id_mascota, :id_veterinario); END;
            ";
            $stmt_tratamiento = $conn_pdo->prepare($query_tratamiento);
            $stmt_tratamiento->bindParam(":descripcion", $descripcion);
            $stmt_tratamiento->bindParam(":id_mascota", $id_mascota);
            $stmt_tratamiento->bindParam(":id_veterinario", $id_veterinario);
            $stmt_tratamiento->execute();

            // Paso 2: Insertar en la tabla detalle con ID_MASCOTA
            $query_detalle = "
                BEGIN LAROATLB_REGISTRAR_DETALLE_TRATAMIENTO(
                    :id_mascota, :id_producto, :cantidad
                ); END;
            ";
            $stmt_detalle = $conn_pdo->prepare($query_detalle);
            $stmt_detalle->bindParam(":id_mascota", $id_mascota); // ID_MASCOTA directamente
            $stmt_detalle->bindParam(":id_producto", $id_producto);
            $stmt_detalle->bindParam(":cantidad", $cantidad);
            $stmt_detalle->execute();

            // Confirmar la transacción
            $conn_pdo->commit();
            $success = "El tratamiento y su detalle se registraron correctamente.";
        } catch (Exception $e) {
            $conn_pdo->rollBack();
            $error = $e->getMessage();
        }
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

                <!-- Selección de Mascota -->
                <div class="mb-3">
                    <label for="id_mascota" class="form-label">Mascota:</label>
                    <select id="id_mascota" name="id_mascota" class="form-select" required>
                        <option value="">Seleccione una mascota</option>
                        <?php foreach ($MASCOTAS as $mascota): ?>
                            <option value="<?php echo $mascota['ID_MASCOTA']; ?>">
                                <?php echo $mascota['NOMBRE']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Descripción del Tratamiento -->
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción del Tratamiento:</label>
                    <input type="text" id="descripcion" name="descripcion" class="form-control" required>
                </div>

                <!-- Selección de Producto -->
                <div class="mb-3">
                    <label for="id_producto" class="form-label">Producto:</label>
                    <select id="id_producto" name="id_producto" class="form-select" required>
                        <option value="">Seleccione un producto</option>
                        <?php foreach ($PRODUCTOS as $producto): ?>
                            <option value="<?php echo $producto['ID_PRODUCTO']; ?>">
                                <?php echo $producto['NOMBRE_PRODUCTO']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Cantidad -->
                <div class="mb-3">
                    <label for="cantidad" class="form-label">Cantidad:</label>
                    <input type="number" id="cantidad" name="cantidad" class="form-control" required>
                </div>

                <!-- Selección de Veterinario -->
                <div class="mb-3">
                    <label for="id_veterinario" class="form-label">Veterinario:</label>
                    <select id="id_veterinario" name="id_veterinario" class="form-select" required>
                        <option value="">Seleccione un veterinario</option>
                        <?php foreach ($VETERINARIOS as $veterinario): ?>
                            <option value="<?php echo $veterinario['ID_VETERINARIO']; ?>">
                                <?php echo $veterinario['NOMBRE']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Botón de enviar -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Registrar Tratamiento</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cerrarSesion() {
            alert("Has cerrado sesión exitosamente.");
            window.location.href='../hola.php';  // Redirigir a la página de login o inicio
        }
    </script>
</body>
</html>