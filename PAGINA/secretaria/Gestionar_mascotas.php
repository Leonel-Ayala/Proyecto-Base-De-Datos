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

$MASCOTAS = [];
$CLIENTES = [];
$RAZAS = [];

// Listar usando OCI
function listarDatos($conn_oci, $procedure_name, &$result_array) {
    $cursor = oci_new_cursor($conn_oci);
    $stmt = oci_parse($conn_oci, "BEGIN $procedure_name(:p_cursor); END;");
    oci_bind_by_name($stmt, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
    oci_execute($stmt);
    oci_execute($cursor);
    while ($row = oci_fetch_assoc($cursor)) {
        $result_array[] = $row;
    }
    oci_free_statement($stmt);
    oci_free_statement($cursor);
}

listarDatos($conn_oci, "LAROATLB_LISTAR_MASCOTAS", $MASCOTAS);
listarDatos($conn_oci, "LAROATLB_LISTAR_CLIENTES", $CLIENTES);
listarDatos($conn_oci, "LAROATLB_LISTAR_RAZAS", $RAZAS);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    try {
        if ($action === 'insertar') {
            // Insertar usando PDO
            $nombre = $_POST['nombre'];
            $edad = $_POST['edad'];
            $id_cliente = $_POST['id_cliente'];
            $id_raza = $_POST['id_raza'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_MASCOTAS('C', NULL, :nombre, :edad, :id_cliente, :id_raza); END;");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':edad', $edad);
            $stmt->bindParam(':id_cliente', $id_cliente);
            $stmt->bindParam(':id_raza', $id_raza);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Mascota ingresada exitosamente!';
        } elseif ($action === 'actualizar') {
            // Actualizar usando PDO
            $id_mascota = $_POST['id_mascota'];
            $nombre = $_POST['nombre'];
            $edad = $_POST['edad'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_MASCOTAS('U', :id_mascota, :nombre, :edad, NULL, NULL); END;");
            $stmt->bindParam(':id_mascota', $id_mascota);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':edad', $edad);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Mascota actualizada exitosamente!';
        } elseif ($action === 'eliminar') {
            // Eliminar usando PDO
            $id_mascota = $_POST['id_mascota'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_MASCOTAS('D', :id_mascota, NULL, NULL, NULL, NULL); END;");
            $stmt->bindParam(':id_mascota', $id_mascota);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Mascota eliminada exitosamente!';
        }
        header("Location: {$_SERVER['PHP_SELF']}");
        exit;
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
    <title>Gestión de Mascotas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('nav_secre.php'); ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1>Gestión de Mascotas</h1>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash_message']) ?></div>
                    <?php unset($_SESSION['flash_message']); ?>
                <?php endif; ?>
            </div>
            <div class="col-12 mb-3">
                <button class="btn btn-primary" onclick="showForm('insertar')">Insertar</button>
            </div>
            <div class="col-12">
                <table class="table table-bordered">
                    <thead class="table">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Edad</th>
                            <th>Cliente</th>
                            <th>Raza</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($MASCOTAS as $mascota): ?>
                            <tr>
                                <td><?= htmlspecialchars($mascota['ID_MASCOTA']) ?></td>
                                <td><?= htmlspecialchars($mascota['NOMBRE']) ?></td>
                                <td><?= htmlspecialchars($mascota['EDAD']) ?></td>
                                <td><?= htmlspecialchars($mascota['CLIENTE']) ?></td>
                                <td><?= htmlspecialchars($mascota['NOMBRE_RAZA']) ?></td>
                                <td>
                                    <button class="btn btn-info" onclick="showForm('actualizar', <?= htmlspecialchars(json_encode($mascota)) ?>)">Editar</button>
                                    <button class="btn btn-danger" onclick="showForm('eliminar', { ID_MASCOTA: '<?= $mascota['ID_MASCOTA'] ?>', NOMBRE: '<?= $mascota['NOMBRE'] ?>' })">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal para formularios -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showForm(action, mascotaData = {}) {
            let formHtml = '';
            let modalTitle = '';

            if (action === 'insertar') {
                modalTitle = 'Insertar Mascota';
                formHtml = `
                    <form method="POST">
                        <input type="hidden" name="action" value="insertar">
                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Edad</label>
                            <input type="number" name="edad" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Cliente</label>
                            <select name="id_cliente" class="form-select">
                                <?php foreach ($CLIENTES as $cliente): ?>
                                    <option value="<?= $cliente['ID_CLIENTE'] ?>"><?= htmlspecialchars($cliente['NOMBRE']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Raza</label>
                            <select name="id_raza" class="form-select">
                                <?php foreach ($RAZAS as $raza): ?>
                                    <option value="<?= $raza['ID_RAZA'] ?>"><?= htmlspecialchars($raza['NOMBRE_RAZA']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </form>
                `;
            } else if (action === 'actualizar') {
                modalTitle = 'Actualizar Mascota';
                formHtml = `
                    <form method="POST">
                        <input type="hidden" name="action" value="actualizar">
                        <input type="hidden" name="id_mascota" value="${mascotaData.ID_MASCOTA}">
                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" name="nombre" class="form-control" value="${mascotaData.NOMBRE}" required>
                        </div>
                        <div class="mb-3">
                            <label>Edad</label>
                            <input type="number" name="edad" class="form-control" value="${mascotaData.EDAD}" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </form>
                `;
            } else if (action === 'eliminar') {
                modalTitle = 'Eliminar Mascota';
                formHtml = `
                    <form method="POST">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="id_mascota" value="${mascotaData.ID_MASCOTA}">
                        <p>¿Estás seguro de que deseas eliminar la mascota <strong>${mascotaData.NOMBRE}</strong>?</p>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </form>
                `;
            }

            document.getElementById('modalContent').innerHTML = formHtml;
            document.getElementById('exampleModalLabel').textContent = modalTitle;

            const myModal = new bootstrap.Modal(document.getElementById('exampleModal'));
            myModal.show();
        }
    </script>
    <script>
        function cerrarSesion() {
            alert("Has cerrado sesión exitosamente.");
            window.location.href='../hola.php';  // Redirigir a la página de login o inicio
        }
    </script>
</body>
</html>