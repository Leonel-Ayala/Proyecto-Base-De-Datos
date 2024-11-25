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

$USUARIOS = [];
// Listar usando OCI
$cursor = oci_new_cursor($conn_oci);
$stmt = oci_parse($conn_oci, "BEGIN LAROATLB_LISTAR_USUARIOS(:p_cursor); END;");
oci_bind_by_name($stmt, ':p_cursor', $cursor, -1, OCI_B_CURSOR);
oci_execute($stmt);
oci_execute($cursor);
while ($row = oci_fetch_assoc($cursor)) {
    $USUARIOS[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    try {
        if ($action === 'listar') {
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'insertar') {
            // Insertar usuario usando PDO
            $nombre_usuario = $_POST['nombre_usuario'];
            $rol_usuario = $_POST['rol_usuario'];
            $contra_usuario = $_POST['contra_usuario'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_USUARIOS('C', NULL, :nombre_usuario, :rol_usuario, :contra_usuario); END;");
            $stmt->bindParam(':nombre_usuario', $nombre_usuario);
            $stmt->bindParam(':rol_usuario', $rol_usuario);
            $stmt->bindParam(':contra_usuario', $contra_usuario);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Usuario Ingresado Exitosamente!';
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'actualizar') {
            // Actualizar usuario usando PDO
            $id_usuario = $_POST['id_usuario'];
            $nombre_usuario = $_POST['nombre_usuario'];
            $rol_usuario = $_POST['rol_usuario'];
            $contra_usuario = $_POST['contra_usuario'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_USUARIOS('U', :id_usuario, :nombre_usuario, :rol_usuario, :contra_usuario); END;");
            $stmt->bindParam(':id_usuario', $id_usuario);
            $stmt->bindParam(':nombre_usuario', $nombre_usuario);
            $stmt->bindParam(':rol_usuario', $rol_usuario);
            $stmt->bindParam(':contra_usuario', $contra_usuario);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Usuario Actualizado Exitosamente!';
            header("Location: {$_SERVER['PHP_SELF']}");
        } elseif ($action === 'eliminar') {
            // Eliminar usuario usando PDO
            $id_usuario = $_POST['id_usuario'];

            $stmt = $conn_pdo->prepare("BEGIN LAROATLB_GESTIONAR_USUARIOS('D', :id_usuario, NULL, NULL, NULL); END;");
            $stmt->bindParam(':id_usuario', $id_usuario);
            $stmt->execute();

            $_SESSION['flash_message'] = '¡Usuario Eliminado Exitosamente!';
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
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('../nav_header.php'); ?>

    <!-- Contenedor principal -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h1>Gestión de Usuarios</h1>
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
                <!-- Tabla para mostrar usuarios -->
                <?php if (!empty($USUARIOS)): ?>
                    <table class="table table-bordered table-sm" id="listado-usuarios">
                        <thead>
                            <tr>
                                <th>ID Usuario</th>
                                <th>Nombre Usuario</th>
                                <th>Rol Usuario</th>
                                <th>Contraseña Usuario</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($USUARIOS as $USUARIO): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($USUARIO['ID_USUARIO']); ?></td>
                                    <td><?php echo htmlspecialchars($USUARIO['NOMBRE_USUARIO']); ?></td>
                                    <td><?php echo htmlspecialchars($USUARIO['ROL_USUARIO']); ?></td>
                                    <td><?php echo htmlspecialchars($USUARIO['CONTRA_USUARIO']); ?></td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            
                                        <button class="btn btn-info me-2" onclick="showForm('actualizar', { 
                                            ID_USUARIO: '<?= htmlspecialchars($USUARIO['ID_USUARIO']); ?>',
                                            NOMBRE_USUARIO: '<?= htmlspecialchars($USUARIO['NOMBRE_USUARIO']); ?>',
                                            ROL_USUARIO: '<?= htmlspecialchars($USUARIO['ROL_USUARIO']); ?>',
                                            CONTRA_USUARIO: '<?= htmlspecialchars($USUARIO['CONTRA_USUARIO']); ?>',
                                        })">Editar</button>

                                        <button class="btn btn-danger" onclick="showForm('eliminar', {
                                            ID_USUARIO: '<?= htmlspecialchars($USUARIO['ID_USUARIO']); ?>'
                                        })">Eliminar</button>
                                        
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-warning text-center">No hay usuarios registrados.</div>
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

        function showForm(action, usuarioData = {}) {
            console.log(action, usuarioData)
            let formHtml = '';
            if (action === 'insertar') {
                formHtml = `
                    <h2>Insertar Usuario</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="insertar">
                        <div class="mb-3">
                            <label for="nombre_usuario" class="form-label">Nombre Usuario</label>
                            <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
                        </div>
                    <div class="mb-3">
                        <label for="rol_usuario" class="form-label">Rol Usuario</label>
                        <select class="form-control" id="rol_usuario" name="rol_usuario">
                            <option value="ADMIN">ADMIN</option>
                            <option value="SECRETARIA">SECRETARIA</option>
                            <option value="VETERINARIO">VETERINARIO</option>
                        </select>
                    </div>
                        <div class="mb-3">
                            <label for="contra_usuario" class="form-label">Contra Usuario</label>
                            <input type="text" class="form-control" id="contra_usuario" name="contra_usuario">
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
                            <input type="hidden" class="form-control" id="id_usuario" name="id_usuario" value="${usuarioData.ID_USUARIO}" required>
                        </div>
                        <div class="mb-3">
                            <label for="nombre_usuario" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" value="${usuarioData.NOMBRE_USUARIO}" required>
                        </div>
                        <div class="mb-3">
                            <label for="rol_usuario" class="form-label">Apellido 1</label>
                            <select class="form-control" id="rol_usuario" name="rol_usuario">
                                <option value="ADMIN" ${usuarioData.ROL_USUARIO === 'ADMIN' ? 'selected' : ''}>ADMIN</option>
                                <option value="SECRETARIA" ${usuarioData.ROL_USUARIO === 'SECRETARIA' ? 'selected' : ''}>SECRETARIA</option>
                                <option value="VETERINARIO" ${usuarioData.ROL_USUARIO === 'VETERINARIO' ? 'selected' : ''}>VETERINARIO</option>
                            </select>
                        </div>
                                    
                        <div class="mb-3">
                            <label for="contra_usuario" class="form-label">Apellido 2</label>
                            <input type="text" class="form-control" id="contra_usuario" name="contra_usuario" value="${usuarioData.CONTRA_USUARIO}" required>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-warning">Actualizar</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                `;
                
                document.getElementById('exampleModalLabel').innerHTML = 'Actualizar Usuario';
            }
            else if (action == 'eliminar', usuarioData){
                console.log('aca')
                formHtml = `<form method="POST" class="form-inline">
                                <input type="hidden" name="action" value="eliminar">
                                    <input type="hidden" class="form-control" name="id_usuario" value="${usuarioData.ID_USUARIO}" required>
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>

                            </form>`
                document.getElementById('exampleModalLabel').innerHTML = 'Eliminar Usuario';
            }

            //document.getElementById('formContainer').innerHTML = formHtml;
            document.getElementById('modalContent').innerHTML = formHtml;

            var myModal = new bootstrap.Modal(document.getElementById('exampleModal'))
            myModal.show()
            //document.getElementById("listado-usuarios").style.display="none"
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