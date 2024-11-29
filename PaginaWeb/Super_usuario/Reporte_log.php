 <?php
header('Content-Type: text/html; charset=utf-8');

// Configuracion de conexion
$host = 'localhost';
$port = '1521';
$dbname = 'XE';
$username = 'vetsol';
$password = 'oracle';

// Conexion OCI para listar
$conn_oci = oci_connect($username, $password, "//{$host}:{$port}/{$dbname}");

if (!$conn_oci) {
    $e = oci_error();
    die("Error de conexion OCI: " . $e['message']);
}

// Ejecutar el procedimiento almacenado y obtener los resultados
$logs = []; // Array para almacenar los resultados
try {
    // Preparar el procedimiento
    $stmt = oci_parse($conn_oci, "BEGIN LAROATLB_AGRUPAR_INGRESOS(:p_cursor); END;");
    
    // Crear un cursor para recibir los datos
    $cursor = oci_new_cursor($conn_oci);
    oci_bind_by_name($stmt, ':p_cursor', $cursor, -1, OCI_B_CURSOR);

    // Ejecutar el procedimiento
    oci_execute($stmt);
    oci_execute($cursor, OCI_DEFAULT);

    // Obtener los resultados del cursor
    while ($row = oci_fetch_assoc($cursor)) {
        $logs[] = $row;
    }

    // Liberar recursos
    oci_free_statement($stmt);
    oci_free_statement($cursor);
    oci_close($conn_oci);
} catch (Exception $e) {
    die("Error al ejecutar el procedimiento: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log de Ingresos Agrupados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('../nav_header.php')?>
    <div class="container mt-4">
        <h1 class="text-center">Log de Ingresos Agrupados</h1>
        <div class="col-12">
            <!-- Tabla para mostrar ingresos agrupados -->
            <?php if (!empty($logs)): ?>
                <table class="table table-bordered table-sm" id="listado-ingresos">
                    <thead>
                        <tr>
                            <th class="text-center">Nombre de Usuario</th>
                            <th class="text-center">Cantidad de Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="text-center"><?php echo htmlspecialchars($log['NOMBRE_INGRESO']); ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($log['CANTIDAD_INGRESOS']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    No hay datos disponibles.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cerrarSesion() {
            alert("Has cerrado sesión exitosamente.");
            window.location.href = '../hola.php'; // Redirige a la página de login
        }
    </script>