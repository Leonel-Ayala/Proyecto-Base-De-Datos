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

$PRODUCTOS = [];
// Listar productos usando OCI
$cursor = oci_new_cursor($conn_oci);
$stmt = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_PRODUCTOS(:p_cursor); END;");
oci_bind_by_name($stmt, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
oci_execute($stmt);
oci_execute($cursor);
while ($row = oci_fetch_assoc($cursor)) {
    $PRODUCTOS[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    try {
        if ($action === 'listar') {
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'insertar') {
            // Insertar producto usando PDO
            $nombre_producto = $_POST['nombre_producto'];
            $stock = $_POST['stock'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_PRODUCTOS('C', NULL, :nombre_producto, :stock); END;");
            $stmt->bindParam(':nombre_producto', $nombre_producto);
            $stmt->bindParam(':stock', $stock);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Producto Ingresado Exitosamente!';

            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'actualizar') {
            // Actualizar producto usando PDO
            $id_producto = $_POST['id_producto'];
            $stock = $_POST['stock'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_PRODUCTOS('U', :id_producto, NULL, :stock); END;");
            $stmt->bindParam(':id_producto', $id_producto);
            $stmt->bindParam(':stock', $stock);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Producto Actualizado Exitosamente!';
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'eliminar') {
            // Eliminar producto usando PDO
            $id_producto = $_POST['id_producto'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_PRODUCTOS('D', :id_producto, NULL, NULL); END;");
            $stmt->bindParam(':id_producto', $id_producto);
            $stmt->execute();
            $_SESSION['flash_message'] = '¡Producto Eliminado Exitosamente!';

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
    <title>Gestión de Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('../nav_header.php'); ?>

    <!-- Contenedor principal -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1>Gestión de Productos</h1>
            </div>
            <div class="col-12">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <h4 class="alert-heading">Ha ocurrido un error!</h4>
                        <?php echo iconv(mb_detect_encoding($error, mb_detect_order(), true), "UTF-8", $error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if ($_SERVER['REQUEST_METHOD'] =='GET' && isset($_SESSION['flash_message'])) : ?>
                    <div class="alert alert-success alert-dismissible">
                        <h4 class="alert-heading">Operación realizada con éxito</h4>
                        <?php echo iconv(mb_detect_encoding($_SESSION['flash_message'], mb_detect_order(), true), "UTF-8", $_SESSION['flash_message']); ?>
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
            </div>
            <div class="col-12">
                <!-- Tabla para mostrar productos -->
                <?php if (!empty($PRODUCTOS)): ?>
                    <table class="table table-bordered table-sm" id="listado-productos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Stock</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($PRODUCTOS as $PRODUCTO): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($PRODUCTO['ID_PRODUCTO']); ?></td>
                                    <td><?php echo htmlspecialchars($PRODUCTO['NOMBRE_PRODUCTO']); ?></td>
                                    <td><?php echo htmlspecialchars($PRODUCTO['STOCK']); ?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <button class="btn btn-info me-2" onclick="showForm('actualizar', { 
                                                ID_PRODUCTO: '<?= htmlspecialchars($PRODUCTO['ID_PRODUCTO']); ?>',
                                                NOMBRE_PRODUCTO: '<?= htmlspecialchars($PRODUCTO['NOMBRE_PRODUCTO']); ?>',
                                                STOCK: '<?= htmlspecialchars($PRODUCTO['STOCK']); ?>'
                                            })">Editar</button>

                                            <button class="btn btn-danger" onclick="showForm('eliminar', {
                                                ID_PRODUCTO: '<?= htmlspecialchars($PRODUCTO['ID_PRODUCTO']); ?>'
                                            })">Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning text-center">No hay productos registrados.</div>
                <?php endif; ?>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Productos</h1>
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
        function showForm(action, productoData = {}) {
            let formHtml = '';
            if (action === 'insertar') {
                formHtml = `
                    <h2>Insertar Producto</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="insertar">
                        <div class="mb-3">
                            <label for="nombre_producto" class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" required>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Agregar Producto</button>
                    </form>`;
            } else if (action === 'actualizar') {
                formHtml = `
                    <h2>Actualizar Producto</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="actualizar">
                        <input type="hidden" name="id_producto" value="${productoData.ID_PRODUCTO}">
                        <div class="mb-3">
                            <label for="nombre_producto" class="form-label">Nombre del Producto</label>
                            <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" value="${productoData.NOMBRE_PRODUCTO}" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" value="${productoData.STOCK}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Actualizar Producto</button>
                    </form>`;
            } else if (action === 'eliminar') {
                formHtml = `
                    <h2>Eliminar Producto</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="eliminar">
                        <input type="hidden" name="id_producto" value="${productoData.ID_PRODUCTO}">
                        <p>¿Está seguro que desea eliminar el producto?</p>
                        <button type="submit" class="btn btn-danger">Eliminar Producto</button>
                    </form>`;
            }

            document.getElementById('modalContent').innerHTML = formHtml;
            new bootstrap.Modal(document.getElementById('exampleModal')).show();
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

