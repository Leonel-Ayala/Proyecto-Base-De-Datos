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

// Conexión PDO para actualizar y eliminar
$dsn = "oci:dbname=//{$host}:{$port}/{$dbname}";
try {
    $conn_pdo = new PDO($dsn, $username, $password);
    $conn_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión PDO: " . $e->getMessage());
}

$error = '';
session_start();

$CALLES = [];

$COMUNAS = [];

// Listar Comunas usando OCI
$cursor_comunas = oci_new_cursor($conn_oci);
$stmt_comunas = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_COMUNAS(:p_cursor); END;");
oci_bind_by_name($stmt_comunas, ':p_cursor', $cursor_comunas, -1, OCI_B_CURSOR);
oci_execute($stmt_comunas);
oci_execute($cursor_comunas);
while ($row = oci_fetch_assoc($cursor_comunas)) {
    $COMUNAS[] = $row;
}

// Listar Calles usando OCI
$cursor_calles = oci_new_cursor($conn_oci);
$stmt_calles = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_CALLES(:p_cursor); END;");
oci_bind_by_name($stmt_calles, ':p_cursor', $cursor_calles, -1, OCI_B_CURSOR);
oci_execute($stmt_calles);
oci_execute($cursor_calles);
while ($row = oci_fetch_assoc($cursor_calles)) {
    $CALLES[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    try {
        if ($action === 'listar_calles') {
            // Si la acción es listar, simplemente recargar la página con las calles ya cargadas
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'actualizar_calle') {
            // Actualizar calle usando PDO
            $id_calle = $_POST['id_calle'];
            $nombre_calle = $_POST['nombre_calle'];
            $numero_casa = $_POST['numero_casa'];
            $id_comuna = $_POST['id_comuna'];

            // Ejecutar el procedimiento para actualizar la calle
            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_CALLES('U', :id_calle, :nombre_calle, :numero_casa, :id_comuna); END;");
            $stmt->bindParam(':id_calle', $id_calle);
            $stmt->bindParam(':nombre_calle', $nombre_calle);
            $stmt->bindParam(':numero_casa', $numero_casa);
            $stmt->bindParam(':id_comuna', $id_comuna);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Calle Actualizada Exitosamente!';
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'eliminar_calle') {
            // Eliminar calle usando PDO
            $id_calle = $_POST['id_calle'];

            // Ejecutar el procedimiento para eliminar la calle
            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_CALLES('D', :id_calle, NULL, NULL, NULL); END;");
            $stmt->bindParam(':id_calle', $id_calle);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Calle Eliminada Exitosamente!';
            header("Location: {$_SERVER['PHP_SELF']}");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Cerrar conexiones y liberar cursores
oci_free_statement($stmt_calles);
oci_free_statement($cursor_calles);
oci_close($conn_oci);
$conn_pdo = null;
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Calles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('../nav_header.php'); ?>

    <!-- Contenedor principal -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center">Gestión de Calles</h1>
            </div>
            <div class="col-12">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <h4 class="alert-heading">Ha ocurrido un error!</h4>
                        <?php echo iconv(mb_detect_encoding($error, mb_detect_order(), true), "UTF-8", $error);  ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
            </div>
            <div class="col-12">
                <!-- Botones de acción -->
                <div class="button-container mb-3">
                    <button class="btn btn-primary" onclick="submitForm('listar')">Listar</button>
                </div>
            </div>
            <div class="col-12">
                <!-- Contenedor para formularios -->
                <div id="formContainer" class="mb-3">
            </div>
            <div class="col-12">
                <!-- Tabla para mostrar comunas -->
                <?php if (!empty($COMUNAS)): ?>
                    <table class="table table-bordered table-sm" id="listado-calles">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre de calle</th>
                                <th>Numero de casa</th>
                                <th>ID_comuna</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($CALLES as $CALLE): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($CALLE['ID_CALLE']); ?></td>
                                    <td><?php echo htmlspecialchars($CALLE['NOMBRE_CALLE']); ?></td>
                                    <td><?php echo htmlspecialchars($CALLE['NUMERO_CASA']); ?></td>
                                    <td><?php echo htmlspecialchars($CALLE['ID_COMUNA']); ?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            
                                        <button class="btn btn-info me-2" onclick="showForm('actualizar', { 
                                            ID_CALLE: '<?= htmlspecialchars($CALLE['ID_CALLE']); ?>',
                                            NOMBRE_CALLE: '<?= htmlspecialchars($CALLE['NOMBRE_CALLE']); ?>',
                                            NUMERO_CASA: '<?= htmlspecialchars($CALLE['NUMERO_CASA']); ?>',
                                            ID_COMUNA: '<?= htmlspecialchars($CALLE['ID_COMUNA']); ?>',
                                        })">Editar</button>

                                        <button class="btn btn-danger" onclick="showForm('eliminar', {
                                            ID_CALLE: '<?= htmlspecialchars($CALLE['ID_CALLE']); ?>'
                                        })">Eliminar</button>
                                        
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning text-center">No hay Calles registrados.</div>
                <?php endif; ?>
            </div>  


            <!-- Button trigger modal -->


            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    ...
                </div>
                </div>
            </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cerrarSesion() {
            alert("Has cerrado sesión exitosamente.");
            window.location.href = '../hola.php'; // Redirige a la página de login
        }

        function showForm(action, calleData = {}, comunaData = {}) {
    let formHtml = '';
    let comunaHtml = '';  // Variable para generar las opciones del <select>

    // Generar las opciones del select de regiones
    <?php foreach ($COMUNAS as $COMUNA): ?>
        comunaHtml += `<option value="<?php echo $COMUNA['ID_COMUNA']; ?>" ${comunaData.ID_COMUNA == "<?php echo $COMUNA['ID_COMUNA']; ?>" ? 'selected' : ''}>
                            <?php echo $COMUNA['NOMBRE_COMUNA']; ?>
                        </option>`;
    <?php endforeach; ?>

    if (action === 'actualizar') {
        formHtml = `
            <h2>Actualizar Calle</h2>
            <form method="POST">
                <input type="hidden" name="action" value="actualizar_calle">
                <input type="hidden" class="form-control" id="id_calle" name="id_calle" value="${calleData.ID_CALLE}" required>
                <div class="mb-3">
                    <label for="nombre_calle" class="form-label">Nombre Calle</label>
                    <input type="text" class="form-control" id="nombre_calle" name="nombre_calle" value="${calleData.NOMBRE_CALLE}" required>
                </div>
                <div class="mb-3">
                    <label for="numero_casa" class="form-label">Numero Casa</label>
                    <input type="text" class="form-control" id="numero_casa" name="numero_casa" value="${calleData.NUMERO_CASA}" required>
                </div>
                <div class="mb-3">
                    <label for="id_comuna" class="form-label">Seleccionar Comuna</label>
                    <select class="form-control" id="id_comuna" name="id_comuna" required>
                        <option value="" disabled>Seleccione una Comuna</option>
                        ${comunaHtml}  <!-- Aquí se insertan las opciones de comunas -->
                    </select>
                </div>
                <button type="submit" class="btn btn-warning">Actualizar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </form>
        `;
    } else if (action === 'eliminar') {
        formHtml = `
            <h2>Eliminar Calle</h2>
            <form method="POST">
                <input type="hidden" name="action" value="eliminar_calle">
                <input type="hidden" name="id_calle" value="${calleData.ID_CALLE}" required>
                <button type="submit" class="btn btn-danger">Eliminar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </form>
        `;
    }

    document.getElementById('modalContent').innerHTML = formHtml;

    var myModal = new bootstrap.Modal(document.getElementById('exampleModal'));
    myModal.show();
}

        
        function submitForm(action) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `<input type="hidden" name="action" value="${action}">`;
            document.body.appendChild(form);
            form.submit();
        }
        
    </script>
</body>
</html>