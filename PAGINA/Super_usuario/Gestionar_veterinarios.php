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

$VETERINARIOS = [];
// Listar usando OCI
$cursor = oci_new_cursor($conn_oci);
$stmt = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_VETERINARIOS(:p_cursor); END;");
oci_bind_by_name($stmt, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
oci_execute($stmt);
oci_execute($cursor);
while ($row = oci_fetch_assoc($cursor)) {
    $VETERINARIOS[] = $row;
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
            $especialidad = $_POST['especialidad'];
            $telefono = $_POST['telefono'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_VETERINARIOS('C', NULL, :nombre, :apellido1, :apellido2, :especialidad, :telefono); END;");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido1', $apellido1);
            $stmt->bindParam(':apellido2', $apellido2);
            $stmt->bindParam(':especialidad', $especialidad);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Veterinario Ingresado Exitosamente!';

            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'actualizar') {
            // Actualizar usando PDO
            $id_veterinario = $_POST['id_veterinario'];
            $nombre = $_POST['nombre'];
            $apellido1 = $_POST['apellido1'];
            $apellido2 = $_POST['apellido2'];
            $especialidad = $_POST['especialidad'];
            $telefono = $_POST['telefono'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_VETERINARIOS('U', :id_veterinario, :nombre, :apellido1, :apellido2, :especialidad, :telefono); END;");
            $stmt->bindParam(':id_veterinario', $id_veterinario);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido1', $apellido1);
            $stmt->bindParam(':apellido2', $apellido2);
            $stmt->bindParam(':especialidad', $especialidad);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Veterinario Actualizado Exitosamente!';
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'eliminar') {
            // Eliminar usando PDO
            $id_veterinario = $_POST['id_veterinario'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_VETERINARIOS('D', :id_veterinario, NULL, NULL, NULL, NULL, NULL); END;");
            $stmt->bindParam(':id_veterinario', $id_veterinario);
            $stmt->execute();
            $_SESSION['flash_message'] = '¡Veterinario Eliminado Exitosamente!';

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
    <title>Gestión de Veterinarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('../nav_header.php'); ?>

    <!-- Contenedor principal -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1>Gestión de Veterinarios</h1>
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
                <!-- Tabla para mostrar veterinarios -->
                <?php if (!empty($VETERINARIOS)): ?>
                    <table class="table table-bordered table-sm" id="listado-veterinarios">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido 1</th>
                                <th>Apellido 2</th>
                                <th>Especialidad</th>
                                <th>Teléfono</th>
                                <th>Correo</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($VETERINARIOS as $VETERINARIO): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($VETERINARIO['ID_VETERINARIO']); ?></td>
                                    <td><?php echo htmlspecialchars($VETERINARIO['NOMBRE']); ?></td>
                                    <td><?php echo htmlspecialchars($VETERINARIO['APELLIDO1']); ?></td>
                                    <td><?php echo htmlspecialchars($VETERINARIO['APELLIDO2']); ?></td>
                                    <td><?php echo htmlspecialchars($VETERINARIO['ESPECIALIDAD']); ?></td>
                                    <td><?php echo htmlspecialchars($VETERINARIO['TELEFONO']); ?></td>
                                    <td><?php echo htmlspecialchars($VETERINARIO['EMAIL']); ?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            
                                        <button class="btn btn-info me-2" onclick="showForm('actualizar', { 
                                            ID_VETERINARIO: '<?= htmlspecialchars($VETERINARIO['ID_VETERINARIO']); ?>',
                                            NOMBRE: '<?= htmlspecialchars($VETERINARIO['NOMBRE']); ?>',
                                            APELLIDO1: '<?= htmlspecialchars($VETERINARIO['APELLIDO1']); ?>',
                                            APELLIDO2: '<?= htmlspecialchars($VETERINARIO['APELLIDO2']); ?>',
                                            ESPECIALIDAD: '<?= htmlspecialchars($VETERINARIO['ESPECIALIDAD']); ?>',
                                            TELEFONO: '<?= htmlspecialchars($VETERINARIO['TELEFONO']); ?>',
                                            EMAIL: '<?= htmlspecialchars($VETERINARIO['EMAIL']); ?>'
                                        })">Editar</button>

                                        <button class="btn btn-danger" onclick="showForm('eliminar', {
                                            ID_VETERINARIO: '<?= htmlspecialchars($VETERINARIO['ID_VETERINARIO']); ?>'
                                        })">Eliminar</button>
                                        
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning text-center">No hay veterinarios registrados.</div>
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

        function showForm(action, veterinarioData = {}) {
            console.log(action, veterinarioData)
            let formHtml = '';
            if (action === 'insertar') {
                formHtml = `
                    <h2>Insertar Veterinario</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="insertar">
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
                            <input type="text" class="form-control" id="apellido2" name="apellido2">
                        </div>
                        <div class="mb-3">
                            <label for="especialidad" class="form-label">Especialidad</label>
                            <input type="text" class="form-control" id="especialidad" name="especialidad">
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required>
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
                            <input type="hidden" class="form-control" id="id_veterinario" name="id_veterinario" value="${veterinarioData.ID_VETERINARIO}" required>
                        </div>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="${veterinarioData.NOMBRE}" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido1" class="form-label">Apellido 1</label>
                            <input type="text" class="form-control" id="apellido1" name="apellido1" value="${veterinarioData.APELLIDO1}" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido2" class="form-label">Apellido 2</label>
                            <input type="text" class="form-control" id="apellido2" name="apellido2" value="${veterinarioData.APELLIDO2}" required>
                        </div>
                        <div class="mb-3">
                            <label for="especialidad" class="form-label">Especialidad</label>
                            <input type="text" class="form-control" id="especialidad" name="especialidad" value="${veterinarioData.ESPECIALIDAD}" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" value="${veterinarioData.TELEFONO}" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-warning">Actualizar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                `;
                
                document.getElementById('exampleModalLabel').innerHTML = 'Actualizar Veterinario';
            }
            else if (action == 'eliminar', veterinarioData){
                console.log('aca')
                formHtml = `<form method="POST" class="form-inline">
                                <input type="hidden" name="action" value="eliminar">
                                    <input type="hidden" class="form-control" name="id_veterinario" value="${veterinarioData.ID_VETERINARIO}" required>
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                            </form>`
                document.getElementById('exampleModalLabel').innerHTML = 'Eliminar Veterinario';
            }

            //document.getElementById('formContainer').innerHTML = formHtml;
            document.getElementById('modalContent').innerHTML = formHtml;

            var myModal = new bootstrap.Modal(document.getElementById('exampleModal'))
            myModal.show()
            //document.getElementById("listado-veterinarios").style.display="none"
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