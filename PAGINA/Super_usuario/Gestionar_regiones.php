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

$REGIONES = [];

// Listar usando OCI
$cursor = oci_new_cursor($conn_oci);
$stmt = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_REGIONES(:p_cursor); END;");
oci_bind_by_name($stmt, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
oci_execute($stmt);
oci_execute($cursor);
while ($row = oci_fetch_assoc($cursor)) {
    $REGIONES[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    try {
        if ($action === 'listar') {
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'insertar') {
            // Insertar usando PDO
            $nombre_region = $_POST['nombre_region'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_REGIONES('C', NULL, :nombre_region); END;");
            $stmt->bindParam(':nombre_region', $nombre_region);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Región Ingresada Exitosamente!';

            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'actualizar') {
            // Actualizar usando PDO
            $id_region = $_POST['id_region'];
            $nombre_region = $_POST['nombre_region'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_REGIONES('U', :id_region, :nombre_region); END;");
            $stmt->bindParam(':id_region', $id_region);
            $stmt->bindParam(':nombre_region', $nombre_region);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Región Actualizada Exitosamente!';
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'eliminar') {
            // Eliminar usando PDO
            $id_region = $_POST['id_region'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_REGIONES('D', :id_region, NULL); END;");
            $stmt->bindParam(':id_region', $id_region);
            $stmt->execute();
            $_SESSION['flash_message'] = '¡Región Eliminada Exitosamente!';

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
    <title>Gestión de Regiones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('../nav_header.php'); ?>

    <!-- Contenedor principal -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1>Gestión de Regiones</h1>
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
                <!-- Tabla para mostrar regiones -->
                <?php if (!empty($REGIONES)): ?>
                    <table class="table table-bordered table-sm" id="listado-regiones">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($REGIONES as $REGION): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($REGION['ID_REGION']); ?></td>
                                    <td><?php echo htmlspecialchars($REGION['NOMBRE_REGION']); ?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            
                                        <button class="btn btn-info me-2" onclick="showForm('actualizar', { 
                                            ID_REGION: '<?= htmlspecialchars($REGION['ID_REGION']); ?>',
                                            NOMBRE_REGION: '<?= htmlspecialchars($REGION['NOMBRE_REGION']); ?>',
                                        })">Editar</button>

                                        <button class="btn btn-danger" onclick="showForm('eliminar', {
                                            ID_REGION: '<?= htmlspecialchars($REGION['ID_REGION']); ?>'
                                        })">Eliminar</button>
                                        
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning text-center">No hay regiones registrados.</div>
                <?php endif; ?>
            </div>  


            <!-- Button trigger modal -->


            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
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

        function showForm(action, regionData = {}) {
            console.log(action, regionData)
            let formHtml = '';
            if (action === 'insertar') {
                formHtml = `
                    <h2>Insertar Region</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="insertar">
                        <div class="mb-3">
                            <label for="nombre_region" class="form-label">Nombre Region</label>
                            <input type="text" class="form-control" id="nombre_region" name="nombre_region" required>
                        </div>
                        <button type="submit" class="btn btn-success">Insertar</button>
                        <button class="btn btn-primary" onclick="submitForm('listar')">Volver</button>
                    </form>
                `;
            } else if (action === 'actualizar') {
                formHtml = `
                    <form method="POST">
                        <input type="hidden" name="action" value="actualizar">
                        <div class="mb-3">
                            <input type="hidden" class="form-control" id="id_region" name="id_region" value="${regionData.ID_REGION}" required>
                        </div>
                        <div class="mb-3">
                            <label for="nombre_region" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre_region" name="nombre_region" value="${regionData.NOMBRE_REGION}" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-warning">Actualizar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                `;
                
                document.getElementById('exampleModalLabel').innerHTML = 'Actualizar Veterinario';
            }
            else if (action == 'eliminar', regionData){
                console.log('aca')
                formHtml = `<form method="POST" class="form-inline">
                                <input type="hidden" name="action" value="eliminar">
                                    <input type="hidden" class="form-control" name="id_region" value="${regionData.ID_REGION}" required>
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                            </form>`
                document.getElementById('exampleModalLabel').innerHTML = 'Eliminar Region';
            }

            //document.getElementById('formContainer').innerHTML = formHtml;
            document.getElementById('modalContent').innerHTML = formHtml;

            var myModal = new bootstrap.Modal(document.getElementById('exampleModal'))
            myModal.show()
            //document.getElementById("listado-regiones").style.display="none"
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