<?php
header('Content-Type: text/html; charset=utf-8');

// Configuración de conexión
$host = 'localhost';
$port = '1521';
$dbname = 'XE';
$username = 'sol';
$password = '123';

// Conexión OCI para listar
$conn_oci = oci_connect($username, $password, "//{$host}:{$port}/{$dbname}");

if (!$conn_oci) {
    $e = oci_error();
    die("Error de conexión OCI: " . $e['message']);
}

// Conexión PDO para insertar, actualizar y eliminar
$dsn = "oci:dbname=//{$host}:{$port}/{$dbname}";
try {
    $conn_pdo = new PDO($dsn, $username, $password);
    $conn_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión PDO: " . $e->getMessage());
}

$error = '';
session_start();

$CITAS = [];
// Listar usando OCI
$cursor = oci_new_cursor($conn_oci);
$stmt = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_CITAS(:p_cursor); END;");
oci_bind_by_name($stmt, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
oci_execute($stmt);
oci_execute($cursor);
while ($row = oci_fetch_assoc($cursor)) {
    $CITAS[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    try {
        if ($action === 'insertar') {
            // Insertar usando PDO
            $fecha = date('d-M-Y', strtotime($_POST['fecha']));
            $sala = $_POST['sala'];
            $id_mascota = $_POST['id_mascota'];
            $id_veterinario = $_POST['id_veterinario'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_CITAS('C', NULL, :fecha, :sala, :id_mascota, :id_veterinario); END;");
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':sala', $sala);
            $stmt->bindParam(':id_mascota', $id_mascota);
            $stmt->bindParam(':id_veterinario', $id_veterinario);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Cita ingresada exitosamente!';
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'actualizar') {
            // Actualizar usando PDO
            $id_cita = $_POST['id_cita'];
            $fecha = $_POST['fecha'];
            $sala = $_POST['sala'];
            $id_mascota = $_POST['id_mascota'];
            $id_veterinario = $_POST['id_veterinario'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_CITAS('U', :id_cita, :fecha, :sala, :id_mascota, :id_veterinario); END;");
            $stmt->bindParam(':id_cita', $id_cita);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':sala', $sala);
            $stmt->bindParam(':id_mascota', $id_mascota);
            $stmt->bindParam(':id_veterinario', $id_veterinario);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Cita actualizada exitosamente!';
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'eliminar') {
            // Eliminar usando PDO
            $id_cita = $_POST['id_cita'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_CITAS('D', :id_cita, NULL, NULL, NULL, NULL); END;");
            $stmt->bindParam(':id_cita', $id_cita);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Cita eliminada exitosamente!';
            header("Location: {$_SERVER['PHP_SELF']}");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Citas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('template/nav_header.php'); ?>

    <div class="container mt-4">
        <h1>Gestión de Citas</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if ($_SERVER['REQUEST_METHOD'] =='GET' && isset($_SESSION['flash_message'])) : ?>
                    <div class="alert alert-success alert-dismissible">
                        <h4 class="alert-heading">Operación realizada con éxito</h4>
                        <?php echo iconv(mb_detect_encoding($_SESSION['flash_message'], mb_detect_order(), true), "UTF-8", $_SESSION['flash_message']);  ?>
                        <?php  unset($_SESSION['flash_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
        <div class="mb-3">
            <button class="btn btn-primary" onclick="showForm('insertar')">Insertar</button>
        </div>

        <?php if (!empty($CITAS)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Sala</th>
                        <th>Mascota</th>
                        <th>Veterinario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($CITAS as $cita): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cita['ID_CITA']); ?></td>
                            <td><?php echo htmlspecialchars($cita['FECHA']); ?></td>
                            <td><?php echo htmlspecialchars($cita['SALA']); ?></td>
                            <td><?php echo htmlspecialchars($cita['MASCOTA']); ?></td>
                            <td><?php echo htmlspecialchars($cita['VETERINARIO']); ?></td>
                            <td>
                                <button class="btn btn-info" onclick="showForm('actualizar', <?php echo htmlspecialchars(json_encode($cita)); ?>)">Editar</button>
                                <button class="btn btn-danger" onclick="showForm('eliminar', { ID_CITA: '<?php echo htmlspecialchars($cita['ID_CITA']); ?>' })">Eliminar</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning">No hay citas registradas.</div>
        <?php endif; ?>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Formulario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showForm(action, data = {}) {
            let formHtml = '';
            if (action === 'insertar') {
                formHtml = `
                    <form method="POST">
                        <input type="hidden" name="action" value="insertar">
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required>
                        </div>
                        <div class="mb-3">
                            <label for="sala" class="form-label">Sala</label>
                            <input type="number" class="form-control" id="sala" name="sala" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_mascota" class="form-label">ID Mascota</label>
                            <input type="number" class="form-control" id="id_mascota" name="id_mascota" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_veterinario" class="form-label">ID Veterinario</label>
                            <input type="number" class="form-control" id="id_veterinario" name="id_veterinario" required>
                        </div>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </form>`;
            } else if (action === 'actualizar') {
                formHtml = `
                    <form method="POST">
                        <input type="hidden" name="action" value="actualizar">
                        <input type="hidden" name="id_cita" value="${data.ID_CITA}">
                        <div class="mb-3">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="${data.FECHA}" required>
                        </div>
                        <div class="mb-3">
                            <label for="sala" class="form-label">Sala</label>
                            <input type="number" class="form-control" id="sala" name="sala" value="${data.SALA}" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_mascota" class="form-label">ID Mascota</label>
                            <input type="number" class="form-control" id="id_mascota" name="id_mascota" value="${data.MASCOTA}" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_veterinario" class="form-label">ID Veterinario</label>
                            <input type="number" class="form-control" id="id_veterinario" name="id_veterinario" value="${data.VETERINARIO}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Actualizar</button>
                    </form>`;
            } else if (action === 'eliminar') {
                formHtml = `
                    <form method="POST">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="id_cita" value="${data.ID_CITA}">
                        <p>¿Estás seguro de que deseas eliminar esta cita?</p>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>`;
            }
            document.getElementById('modalContent').innerHTML = formHtml;
            new bootstrap.Modal(document.getElementById('formModal')).show();
        }
    </script>
</body>
</html>