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

$SECRETARIAS = [];
// Listar usando OCI
$cursor = oci_new_cursor($conn_oci);
$stmt = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_SECRETARIAS(:p_cursor); END;");
oci_bind_by_name($stmt, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
oci_execute($stmt);
oci_execute($cursor);
while ($row = oci_fetch_assoc($cursor)) {
    $SECRETARIAS[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    try {
        if ($action === 'listar') {
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'insertar') {
            // Insertar usando PDO
            $nombre = $_POST['nombre'];
            $apellido1 = $_POST['apellido1'];
            $apellido2 = $_POST['apellido2'];
            $telefono = $_POST['telefono'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_SECRETARIAS('C', NULL, :nombre, :apellido1, :apellido2, :telefono); END;");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido1', $apellido1);
            $stmt->bindParam(':apellido2', $apellido2);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Secretaria Ingresada Exitosamente!';

            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'actualizar') {
            // Actualizar usando PDO
            $id_secre = $_POST['id_secre'];
            $nombre = $_POST['nombre'];
            $apellido1 = $_POST['apellido1'];
            $apellido2 = $_POST['apellido2'];
            $telefono = $_POST['telefono'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_SECRETARIAS('U', :id_secre, :nombre, :apellido1, :apellido2, :telefono); END;");
            $stmt->bindParam(':id_secre', $id_secre);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido1', $apellido1);
            $stmt->bindParam(':apellido2', $apellido2);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Secretaria Actualizada Exitosamente!';
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'eliminar') {
            // Eliminar usando PDO
            $id_secre = $_POST['id_secre'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_SECRETARIAS('D', :id_secre, NULL, NULL, NULL, NULL); END;");
            $stmt->bindParam(':id_secre', $id_secre);
            $stmt->execute();
            $_SESSION['flash_message'] = '¡Secretaria Eliminada Exitosamente!';

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
    <title>Gestión de Secretarias</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('../nav_header.php'); ?>

    <!-- Contenedor principal -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1>Gestión de Secretarias</h1>
            </div>
            <div class="col-12">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <h4 class="alert-heading">Ha ocurrido un error!</h4>
                        <?php echo iconv(mb_detect_encoding($error, mb_detect_order(), true), "UTF-8", $error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_SESSION['flash_message'])) : ?>
                    <div class="alert alert-success alert-dismissible">
                        <h4 class="alert-heading">Operación realizada con éxito</h4>
                        <?php echo iconv(mb_detect_encoding($_SESSION['flash_message'], mb_detect_order(), true), "UTF-8", $_SESSION['flash_message']); ?>
                        <?php unset($_SESSION['flash_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-12">
                <!-- Botones de acción -->
                <div class="button-container mb-3">
                    <button class="btn btn-primary" onclick="showForm('insertar')">Insertar</button>
                    <button class="btn btn-primary" onclick="submitForm('listar')">Listar</button>
                </div>
            </div>
            <div class="col-12">
                <!-- Tabla para mostrar secretarias -->
                <?php if (!empty($SECRETARIAS)): ?>
                    <table class="table table-bordered table-sm" id="listado-secretarias">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido 1</th>
                                <th>Apellido 2</th>
                                <th>Teléfono</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($SECRETARIAS as $SECRETARIA): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($SECRETARIA['ID_SECRE']); ?></td>
                                    <td><?php echo htmlspecialchars($SECRETARIA['NOMBRE']); ?></td>
                                    <td><?php echo htmlspecialchars($SECRETARIA['APELLIDO1']); ?></td>
                                    <td><?php echo htmlspecialchars($SECRETARIA['APELLIDO2']); ?></td>
                                    <td><?php echo htmlspecialchars($SECRETARIA['TELEFONO']); ?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button class="btn btn-info me-2" onclick="showForm('actualizar', { 
                                                ID_SECRE: '<?= htmlspecialchars($SECRETARIA['ID_SECRE']); ?>',
                                                NOMBRE: '<?= htmlspecialchars($SECRETARIA['NOMBRE']); ?>',
                                                APELLIDO1: '<?= htmlspecialchars($SECRETARIA['APELLIDO1']); ?>',
                                                APELLIDO2: '<?= htmlspecialchars($SECRETARIA['APELLIDO2']); ?>',
                                                TELEFONO: '<?= htmlspecialchars($SECRETARIA['TELEFONO']); ?>'
                                            })">Editar</button>

                                            <!-- Eliminación con confirmación -->
                                            <button class="btn btn-danger" onclick="confirmDelete(<?= htmlspecialchars($SECRETARIA['ID_SECRE']); ?>)">Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay secretarias registradas.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal para Confirmar Eliminación -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Está seguro de eliminar esta secretaria?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para insertar o editar -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Gestión de Secretaria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" method="POST" id="formSecretaria">
                        <input type="hidden" name="action" id="action">
                        <input type="hidden" name="id_secre" id="id_secre">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido1" class="form-label">Apellido 1</label>
                            <input type="text" class="form-control" id="apellido1" name="apellido1" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido2" class="form-label">Apellido 2</label>
                            <input type="text" class="form-control" id="apellido2" name="apellido2" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required>
                        </div>
                        <div class="mb-3 text-center">
                            <button type="submit" class="btn btn-primary">Enviar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts de Bootstrap y función para mostrar formularios -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentDeleteId = null;

        function confirmDelete(id) {
            currentDeleteId = id;
            const modal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            modal.show();
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (currentDeleteId) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'eliminar';
                form.appendChild(actionInput);
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id_secre';
                idInput.value = currentDeleteId;
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
        });

        function showForm(action, data = {}) {
            const form = document.getElementById('formSecretaria');
            document.getElementById('action').value = action;
            document.getElementById('id_secre').value = data.ID_SECRE || '';
            document.getElementById('nombre').value = data.NOMBRE || '';
            document.getElementById('apellido1').value = data.APELLIDO1 || '';
            document.getElementById('apellido2').value = data.APELLIDO2 || '';
            document.getElementById('telefono').value = data.TELEFONO || '';
            const modal = new bootstrap.Modal(document.getElementById('exampleModal'));
            modal.show();
        }

        function submitForm(action) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = action;
            form.appendChild(actionInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
    <script>
        function cerrarSesion() {
            alert("Has cerrado sesión exitosamente.");
            window.location.href = '../hola.php'; // Redirige a la página de login
        }
    </script>
</body>
</html>