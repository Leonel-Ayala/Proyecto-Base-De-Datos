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

// Variables para el filtro de citas
$fecha = '';
$id_veterinario = '';

$VETERINARIOS = [];
$cursor = oci_new_cursor($conn_oci);
$stmt_veterinarios = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_VETERINARIOS(:p_cursor); END;");
oci_bind_by_name($stmt_veterinarios, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
oci_execute($stmt_veterinarios);
oci_execute($cursor);
while ($row = oci_fetch_assoc($cursor)) {
    $VETERINARIOS[] = $row;
}


// Filtrar citas por veterinario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['fecha']) && isset($_POST['id_veterinario'])) {
        $fecha = date('d-M-Y', strtotime($_POST['fecha']));
        $id_veterinario = $_POST['id_veterinario'];
        
        // Procedimiento almacenado: LAROATLB_LISTAR_CITAS_POR_VETERINARIO
        $cursor = oci_new_cursor($conn_oci);
        $stmt = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_CITAS_POR_VETERINARIO(:p_fecha, :p_id_veterinario, :p_cursor); END;");
        oci_bind_by_name($stmt, ':p_fecha', $fecha);
        oci_bind_by_name($stmt, ':p_id_veterinario', $id_veterinario);
        oci_bind_by_name($stmt, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
        oci_execute($stmt);
        oci_execute($cursor);
        
        // Recuperar las citas
        $citas = [];
        while ($row = oci_fetch_assoc($cursor)) {
            $citas[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filtro de Citas por Veterinario</title>
    <!-- Agregar Bootstrap (si no lo tienes) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include("nav_veterinario.php");
             ?>
<div class="container mt-5">
    <!-- Formulario de filtro -->
    <div class="col-12 mb-3">
    <form method="POST">
        <h2 class="text-center">Filtrar Citas por Veterinario</h2>
        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="fecha" name="fecha" required>
        </div>
        <div class="mb-3">
            <label for="id_veterinario" class="form-label">Veterinario</label>
            <select class="form-control" id="id_veterinario" name="id_veterinario" required>
                <option value="">Seleccione un veterinario</option>
                <?php foreach ($VETERINARIOS as $vet): ?>
                    <option value="<?php echo htmlspecialchars($vet['ID_VETERINARIO']); ?>">
                        <?php echo htmlspecialchars($vet['NOMBRE']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filtrar Citas</button>
    </form>
</div>


    <!-- Mostrar citas si se ha realizado el filtro -->
    <?php if (!empty($citas)): ?>
        <table class="table table-bordered table-sm" id="listado-citas">
            <thead>
                <tr>
                    <th>Mascota</th>
                    <th>Sala</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($citas as $cita): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cita['MASCOTA']); ?></td>
                        <td><?php echo htmlspecialchars($cita['SALA']); ?></td>
                        <td><?php echo htmlspecialchars($cita['FECHA']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="alert alert-warning text-center">No se encontraron citas para esta fecha y veterinario.</div>
    <?php endif; ?>
</div>

<!-- Agregar Bootstrap JS (si no lo tienes) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
        function cerrarSesion() {
            alert("Has cerrado sesión exitosamente.");
            window.location.href='../hola.php';  // Redirigir a la página de login o inicio
        }
    </script>
</body>
</html>