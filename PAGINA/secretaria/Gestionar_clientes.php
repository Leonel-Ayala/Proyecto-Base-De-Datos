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

$CLIENTES = [];

// Listar usando OCI
$cursor = oci_new_cursor($conn_oci);
$stmt = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_CLIENTES(:p_cursor); END;");
oci_bind_by_name($stmt, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
oci_execute($stmt);
oci_execute($cursor);

while ($row = oci_fetch_assoc($cursor)) {
    $CLIENTES[] = $row;
}

$COMUNAS = [];

// Listar usando OCI
$cursor = oci_new_cursor($conn_oci);
$stmt = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_COMUNAS(:p_cursor); END;");
oci_bind_by_name($stmt, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
oci_execute($stmt);
oci_execute($cursor);
while ($row = oci_fetch_assoc($cursor)) {
    $COMUNAS[] = $row;
}


$CALLES = [];

// Listar usando OCI
$cursor = oci_new_cursor($conn_oci);
$stmt = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_COMUNAS(:p_cursor); END;");
oci_bind_by_name($stmt, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
oci_execute($stmt);
oci_execute($cursor);

while ($row = oci_fetch_assoc($cursor)) {
    $CALLES[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    try {
        
        if ($action === 'listar') {
            // No se realiza acción adicional, ya que la lista se obtiene al cargar la página
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'insertar') {
            $conn_pdo->beginTransaction();
            $id_comuna = $_POST['id_comuna'];
            $nombre_calle = $_POST['nombre_calle'];
            $numero_casa = $_POST['numero_casa'];
            $stmt_calle = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_CALLES('C', NULL, :nombre_calle, :numero_casa, :id_comuna); END;");
            $stmt_calle->bindParam(":nombre_calle", $nombre_calle);
            $stmt_calle->bindParam(":numero_casa", $numero_casa);
            $stmt_calle->bindParam(":id_comuna", $id_comuna);
            $stmt_calle->execute();
            // Insertar usando PDO
            $nombre = $_POST['nombre'];
            $apellido1 = $_POST['apellido1'];
            $apellido2 = $_POST['apellido2'];
            $rut = $_POST['rut'];
            $telefono = $_POST['telefono'];
    


            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_CLIENTES('C', NULL, :rut, :nombre, :apellido1, :apellido2, :telefono, :nombre_calle, :numero_casa); END;");
            $stmt->bindParam(':rut', $rut);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido1', $apellido1);
            $stmt->bindParam(':apellido2', $apellido2);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':nombre_calle', $nombre_calle);
            $stmt->bindParam(':numero_casa', $numero_casa);
            $stmt->execute();
            
            $_SESSION['flash_message'] = '¡Cliente Ingresado Exitosamente!';
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'actualizar') {
            // Actualizar usando PDO
            $id_cliente = $_POST['id_cliente'];
            $nombre = $_POST['nombre'];
            $apellido1 = $_POST['apellido1'];
            $apellido2 = $_POST['apellido2'];
            $rut = $_POST['rut'];
            $telefono = $_POST['telefono'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_CLIENTES('U', :id_cliente, :rut, :nombre, :apellido1, :apellido2, :telefono, NULL, NULL); END;");
            $stmt->bindParam(':id_cliente', $id_cliente);
            $stmt->bindParam(':rut', $rut);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido1', $apellido1);
            $stmt->bindParam(':apellido2', $apellido2);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Cliente Actualizado Exitosamente!';
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'eliminar') {
            // Eliminar usando PDO
            $id_cliente = $_POST['id_cliente'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_CLIENTES('D', :id_cliente, NULL, NULL, NULL, NULL, NULL, NULL, NULL); END;");
            $stmt->bindParam(':id_cliente', $id_cliente);
            $stmt->execute();
            
            $_SESSION['flash_message'] = '¡Cliente Eliminado Exitosamente!';
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
    <title>Gestión de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('nav_secre.php'); ?>

    <!-- Contenedor principal -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1>Gestión de Clientes</h1>
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
                <?php if (!empty($CLIENTES)): ?>
                    <table class="table table-bordered table-sm" id="listado-clientes">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido 1</th>
                                <th>Apellido 2</th>
                                <th>Rut</th>
                                <th>Teléfono</th>
                                <th>Direccion</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($CLIENTES as $CLIENTE): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($CLIENTE['ID_CLIENTE']); ?></td>
                                    <td><?php echo htmlspecialchars($CLIENTE['NOMBRE']); ?></td>
                                    <td><?php echo htmlspecialchars($CLIENTE['APELLIDO1']); ?></td>
                                    <td><?php echo htmlspecialchars($CLIENTE['APELLIDO2']); ?></td>
                                    <td><?php echo htmlspecialchars($CLIENTE['RUT']); ?></td>
                                    <td><?php echo htmlspecialchars($CLIENTE['TELEFONO']); ?></td>
                                    <td><?php echo htmlspecialchars($CLIENTE['DIRECCION']); ?></td>

                                    <td class="text-center">
                                        <div class="btn-group">
                                            
                                        <button class="btn btn-info me-2" onclick="showForm('actualizar', { 
                                            ID_CLIENTE: '<?= htmlspecialchars($CLIENTE['ID_CLIENTE']); ?>',
                                            NOMBRE: '<?= htmlspecialchars($CLIENTE['NOMBRE']); ?>',
                                            APELLIDO1: '<?= htmlspecialchars($CLIENTE['APELLIDO1']); ?>',
                                            APELLIDO2: '<?= htmlspecialchars($CLIENTE['APELLIDO2']); ?>',
                                            RUT: '<?= htmlspecialchars($CLIENTE['RUT']); ?>',
                                            TELEFONO: '<?= htmlspecialchars($CLIENTE['TELEFONO']); ?>',
                                            ID_CALLE: '<?= isset($CLIENTE['ID_CALLE']) && $CLIENTE['ID_CALLE'] !== null ? htmlspecialchars($CLIENTE['ID_CALLE']) : ''; ?>',

                                            
                                        })">Editar</button>

                                        <button class="btn btn-danger" onclick="showForm('eliminar', {
                                            ID_CLIENTE: '<?= htmlspecialchars($CLIENTE['ID_CLIENTE']); ?>'
                                        })">Eliminar</button>
                                        
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning text-center">No hay clientes registrados.</div>
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

        function showForm(action, clienteData = {}, comunaData ={}, calleData ={}) {
            console.log(action, clienteData, comunaData, calleData)
            let formHtml = '';
            let comunaHtml = '';  // Variable para generar las opciones del <select>

    // Generar las opciones del select de regiones
            <?php foreach ($COMUNAS as $COMUNA): ?>
                comunaHtml += `<option value="<?php echo $COMUNA['ID_COMUNA']; ?>" ${comunaData.ID_CALLE == "<?php echo $COMUNA['ID_COMUNA']; ?>" ? 'selected' : ''}>
                                    <?php echo $COMUNA['NOMBRE_COMUNA']; ?>
                                </option>`;
            <?php endforeach; ?>
            if (action === 'insertar') {
                formHtml = `
                    <h2>Insertar Cliente</h2>
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
                            <label for="rut" class="form-label">Rut</label>
                            <input type="number" class="form-control" id="rut" name="rut">
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" required>
                        </div>
                        <div class="mb-3">
                            <label for="nombre_calle" class="form-label">Nombre calle</label>
                            <input type="text" class="form-control" id="nombre_calle" name="nombre_calle" required>
                        </div>
                        <div class="mb-3">
                            <label for="numero_casa" class="form-label">Numero casa</label>
                            <input type="text" class="form-control" id="numero_casa" name="numero_casa" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_comuna" class="form-label">Seleccionar Comuna</label>
                            <select class="form-control" id="id_comuna" name="id_comuna" required>
                                <option value="" disabled selected>Seleccione una Comuna</option>
                                ${comunaHtml}  <!-- Aquí se insertan las opciones de comunas -->
                            </select>
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
                            <input type="hidden" class="form-control" id="id_cliente" name="id_cliente" value="${clienteData.ID_CLIENTE}" required>
                        </div>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" value="${clienteData.NOMBRE}" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido1" class="form-label">Apellido 1</label>
                            <input type="text" class="form-control" id="apellido1" name="apellido1" value="${clienteData.APELLIDO1}" required>
                        </div>
                        <div class="mb-3">
                            <label for="apellido2" class="form-label">Apellido 2</label>
                            <input type="text" class="form-control" id="apellido2" name="apellido2" value="${clienteData.APELLIDO2}" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono" value="${clienteData.TELEFONO}" required>
                        </div>
                        <div class="mb-3">
                            <label for="Rut" class="form-label">Rut</label>
                            <input type="text" class="form-control" id="rut" name="rut" value="${clienteData.RUT}" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-warning">Actualizar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                `;
                
                document.getElementById('exampleModalLabel').innerHTML = 'Actualizar Cliente';
            }
            else if (action == 'eliminar', clienteData){
                console.log('aca')
                formHtml = `<form method="POST" class="form-inline">
                                <input type="hidden" name="action" value="eliminar">
                                    <input type="hidden" class="form-control" name="id_cliente" value="${clienteData.ID_CLIENTE}" required>
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                            </form>`
                document.getElementById('exampleModalLabel').innerHTML = 'Eliminar Cliente';
            }

            //document.getElementById('formContainer').innerHTML = formHtml;
            document.getElementById('modalContent').innerHTML = formHtml;

            var myModal = new bootstrap.Modal(document.getElementById('exampleModal'))
            myModal.show()
            //document.getElementById("listado-clientes").style.display="none"
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