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

$REGIONES = [];
$cursor_regiones = oci_new_cursor($conn_oci);
$stmt_regiones = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_REGIONES(:p_cursor); END;");
oci_bind_by_name($stmt_regiones, ':p_cursor', $cursor_regiones, -1, OCI_B_CURSOR);
oci_execute($stmt_regiones);
oci_execute($cursor_regiones);
while ($row = oci_fetch_assoc($cursor_regiones)) {
    $REGIONES[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    try {
        if ($action === 'listar_comunas') {
            // Si la acción es listar, simplemente recargar la página con las comunas ya cargadas
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'insertar_comuna') {
            // Insertar comuna usando PDO
            $nombre_comuna = $_POST['nombre_comuna'];
            $id_region = $_POST['id_region'];

            // Ejecutar el procedimiento para insertar la comuna
            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_COMUNAS('C', NULL, :nombre_comuna, :id_region); END;");
            $stmt->bindParam(':nombre_comuna', $nombre_comuna);
            $stmt->bindParam(':id_region', $id_region);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Comuna Ingresada Exitosamente!';
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'actualizar_comuna') {
            // Actualizar comuna usando PDO
            $id_comuna = $_POST['id_comuna'];
            $nombre_comuna = $_POST['nombre_comuna'];
            $id_region = $_POST['id_region'];

            // Ejecutar el procedimiento para actualizar la comuna
            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_COMUNAS('U', :id_comuna, :nombre_comuna, :id_region); END;");
            $stmt->bindParam(':id_comuna', $id_comuna);
            $stmt->bindParam(':nombre_comuna', $nombre_comuna);
            $stmt->bindParam(':id_region', $id_region);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Comuna Actualizada Exitosamente!';
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'eliminar_comuna') {
            // Eliminar comuna usando PDO
            $id_comuna = $_POST['id_comuna'];

            // Ejecutar el procedimiento para eliminar la comuna
            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_COMUNAS('D', :id_comuna, NULL, NULL); END;");
            $stmt->bindParam(':id_comuna', $id_comuna);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Comuna Eliminada Exitosamente!';
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
    <title>Gestión de Comunas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('../nav_header.php'); ?>

    <!-- Contenedor principal -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center">Gestión de Comunas</h1>
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
                    <button class="btn btn-primary" onclick="showForm('insertar')">Insertar</button>
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
                    <table class="table table-bordered table-sm" id="listado-comunas">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Id Región</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($COMUNAS as $COMUNA): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($COMUNA['ID_COMUNA']); ?></td>
                                    <td><?php echo htmlspecialchars($COMUNA['NOMBRE_COMUNA']); ?></td>
                                    <td><?php echo htmlspecialchars($COMUNA['ID_REGION']); ?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            
                                        <button class="btn btn-info me-2" onclick="showForm('actualizar', { 
                                            ID_COMUNA: '<?= htmlspecialchars($COMUNA['ID_COMUNA']); ?>',
                                            NOMBRE_COMUNA: '<?= htmlspecialchars($COMUNA['NOMBRE_COMUNA']); ?>',
                                            ID_REGION: '<?= htmlspecialchars($COMUNA['ID_REGION']); ?>',
                                        })">Editar</button>

                                        <button class="btn btn-danger" onclick="showForm('eliminar', {
                                            ID_COMUNA: '<?= htmlspecialchars($COMUNA['ID_COMUNA']); ?>'
                                        })">Eliminar</button>
                                        
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning text-center">No hay Comunas registrados.</div>
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

        function showForm(action, comunaData = {}, regionData = {}) {
    let formHtml = '';
    let regionsHtml = '';  // Variable para generar las opciones del <select>

    // Generar las opciones del select de regiones
    <?php foreach ($REGIONES as $region): ?>
        regionsHtml += `<option value="<?php echo $region['ID_REGION']; ?>" ${regionData.ID_REGION == "<?php echo $region['ID_REGION']; ?>" ? 'selected' : ''}>
                            <?php echo $region['NOMBRE_REGION']; ?>
                        </option>`;
    <?php endforeach; ?>

    if (action === 'insertar') {
        formHtml = `
            <h2>Insertar Comunas</h2>
            <form method="POST">
                <input type="hidden" name="action" value="insertar_comuna">
                <div class="mb-3">
                    <label for="nombre_comuna" class="form-label">Nombre Comuna</label>
                    <input type="text" class="form-control" id="nombre_comuna" name="nombre_comuna" required>
                </div>
                <div class="mb-3">
                    <label for="id_region" class="form-label">Seleccionar Región</label>
                    <select class="form-control" id="id_region" name="id_region" required>
                        <option value="" disabled selected>Seleccione una Región</option>
                        ${regionsHtml}  <!-- Aquí se insertan las opciones de regiones -->
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Insertar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Volver</button>
            </form>
        `;
        
    } else if (action === 'actualizar') {
        formHtml = `
            <h2>Actualizar Comuna</h2>
            <form method="POST">
                <input type="hidden" name="action" value="actualizar_comuna">
                <input type="hidden" class="form-control" id="id_comuna" name="id_comuna" value="${comunaData.ID_COMUNA}" required>
                <div class="mb-3">
                    <label for="nombre_comuna" class="form-label">Nombre Comuna</label>
                    <input type="text" class="form-control" id="nombre_comuna" name="nombre_comuna" value="${comunaData.NOMBRE_COMUNA}" required>
                </div>
                <div class="mb-3">
                    <label for="id_region" class="form-label">Seleccionar Región</label>
                    <select class="form-control" id="id_region" name="id_region" required>
                        <option value="" disabled>Seleccione una Región</option>
                        ${regionsHtml}  <!-- Aquí se insertan las opciones de regiones -->
                    </select>
                </div>
                <button type="submit" class="btn btn-warning">Actualizar</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            </form>
        `;
        
    } else if (action === 'eliminar') {
        formHtml = `
            <h2>Eliminar Comuna</h2>
            <form method="POST">
                <input type="hidden" name="action" value="eliminar_comuna">
                <input type="hidden" name="id_comuna" value="${comunaData.ID_COMUNA}" required>
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