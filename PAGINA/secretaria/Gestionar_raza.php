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

$ESPECIES = [];
// Listar usando OCI
$cursor = oci_new_cursor($conn_oci);
$stmt = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_ESPECIES(:p_cursor); END;");
oci_bind_by_name($stmt, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
oci_execute($stmt);
oci_execute($cursor);
while ($row = oci_fetch_assoc($cursor)) {
    $ESPECIES[] = $row;
}

$RAZAS = [];
// Listar usando OCI
$cursor = oci_new_cursor($conn_oci);
$stmt = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_RAZAS(:p_cursor); END;");
oci_bind_by_name($stmt, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
oci_execute($stmt);
oci_execute($cursor);
while ($row = oci_fetch_assoc($cursor)) {
    $RAZAS[] = $row;
    
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    try {
        if ($action === 'insertar') {
            $nombre_raza = $_POST['nombre_raza'];
            $id_especie = $_POST['id_especie'];
            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_RAZAS('C', NULL, :nombre_raza, :id_especie); END;");
            $stmt->bindParam(':nombre_raza', $nombre_raza);
            $stmt->bindParam(':id_especie', $id_especie);
            $stmt->execute();
            $_SESSION['flash_message'] = '¡Raza Ingresada Exitosamente!';
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'actualizar') {
            $id_raza = $_POST['id_raza'];
            $nombre_raza = $_POST['nombre_raza'];
            var_dump($id_raza);
            var_dump($_POST); // Esto imprimirá todos los valores del formulario

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_RAZAS('U', :id_raza, :nombre_raza, NULL); END;");
            $stmt->bindParam(':id_raza', $id_raza);
            $stmt->bindParam(':nombre_raza', $nombre_raza);
            $stmt->execute();
            $_SESSION['flash_message'] = '¡Raza Actualizada Exitosamente!';
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'eliminar') {
            $id_raza = $_POST['id_raza'];
            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_RAZAS('D', :id_raza, NULL, NULL); END;");
            $stmt->bindParam(':id_raza', $id_raza);
            $stmt->execute();
            $_SESSION['flash_message'] = '¡Raza Eliminada Exitosamente!';
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
    <title>Gestión de Razas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('nav_secre.php'); ?>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1>Gestión de Razas</h1>
            </div>
            <div class="col-12">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <h4 class="alert-heading">Ha ocurrido un error!</h4>
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-success alert-dismissible">
                        <h4 class="alert-heading">Operación realizada con éxito</h4>
                        <?php echo htmlspecialchars($_SESSION['flash_message']); ?>
                        <?php unset($_SESSION['flash_message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-12">
                <div class="button-container mb-3">
                    <button class="btn btn-primary" onclick="showForm('insertar')">Insertar</button>
                </div>
            </div>
            <div class="col-12">
                <?php if (!empty($RAZAS)): ?>
                    <table class="table table-bordered table-sm" id="listado-razas">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Nombre Especie</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($RAZAS as $RAZA): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($RAZA['ID_RAZA']); ?></td>
                                    <td><?php echo htmlspecialchars($RAZA['NOMBRE_RAZA']); ?></td>
                                    <td><?php echo htmlspecialchars($RAZA['NOMBRE_ESPECIE']); ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-info me-2" onclick="showForm('actualizar',{}, { 
                                            ID_RAZA: '<?php echo htmlspecialchars($RAZA['ID_RAZA']); ?>',
                                            NOMBRE_RAZA: '<?php echo htmlspecialchars($RAZA['NOMBRE_RAZA']); ?>',
                                        })"
                                        >Editar</button>
                                        <button class="btn btn-danger" onclick="showForm('eliminar',{}, {
                                            ID_RAZA: '<?php echo htmlspecialchars($RAZA['ID_RAZA']); ?>'
                                        })">Eliminar</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning text-center">No hay Razas registradas.</div>
                <?php endif; ?>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel"></h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="modalContent"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
        <!-- Scripts -->
    
    <script>
        function cerrarSesion() {
            alert("Has cerrado sesión exitosamente.");
            window.location.href = '../hola.php'; // Redirige a la página de login
        }
        function showForm(action, especieData = {}, razaData ={}) {
            let formHtml = '';
            let especieHtml = '';  // Variable para generar las opciones del <select>

            // Generar las opciones del select de regiones
            <?php foreach ($ESPECIES as $ESPECIE): ?>
                especieHtml += `<option value="<?php echo $ESPECIE['ID_ESPECIE']; ?>" ${especieData.ID_ESPECIE == "<?php echo $ESPECIE['ID_ESPECIE']; ?>" ? 'selected' : ''}>
                                    <?php echo $ESPECIE['NOMBRE_ESPECIE']; ?>
                                </option>`;
            <?php endforeach; ?>
            if (action === 'insertar') {
                formHtml = `
                
                    <form method="POST">
                        <input type="hidden" name="action" value="insertar">
                        <div class="mb-3">
                            <label for="nombre_raza" class="form-label">Nombre Raza</label>
                            <input type="text" class="form-control" id="nombre_raza" name="nombre_raza" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_especie" class="form-label">Seleccionar especie</label>
                            <select class="form-control" id="id_especie" name="id_especie" required>
                                <option value="" disabled selected>Seleccione una especie</option>
                                ${especieHtml}  <!-- Aquí se insertan las opciones de especie -->
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Insertar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                `;
                document.getElementById('exampleModalLabel').innerHTML = 'Insertar Especie';
            } else if (action === 'actualizar') {
                formHtml = `
                    <form method="POST">
                        <input type="hidden" name="action" value="actualizar">
                        <input type="hidden" name="id_raza" value="${razaData.ID_RAZA}"required>
                        <div class="mb-3">
                            <label for="nombre_raza" class="form-label">Nombre Raza</label>
                            <input type="text" class="form-control" id="nombre_raza" name="nombre_raza" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-warning">Actualizar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                `;
                document.getElementById('exampleModalLabel').innerHTML = 'Actualizar raza';
            } else if (action === 'eliminar') {
                formHtml = `
                    <form method="POST">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="id_raza" value="${razaData.ID_RAZA}"required>
                        <p>¿Estás seguro de que deseas eliminar esta raza?</p>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                `;
                document.getElementById('exampleModalLabel').innerHTML = 'Eliminar raza';
            }
            document.getElementById('modalContent').innerHTML = formHtml;
            const myModal = new bootstrap.Modal(document.getElementById('exampleModal'));
            myModal.show();
        }
    </script>
</body>
</html>
