<?php 
$host = 'localhost';
$port = '1521';
$dbname = 'XE'; 
$username = 'vetsol';
$password = 'oracle';

try {
    $dsn = "oci:dbname=//$host:$port/$dbname;charset=UTF8";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    try {
        if ($action === 'listar') {
            // Preparar la llamada al procedimiento almacenado
            $stmt = $conn->prepare("BEGIN LAROATLB_GESTIONAR_VETERINARIOS('R', NULL, NULL, NULL, NULL, NULL, NULL, :p_cursor); END;");
            
            // Crear el cursor como parámetro de salida
            $cursor = $conn->prepare('BEGIN :p_cursor := NULL; END;');
            $cursor->bindParam(':p_cursor', $p_cursor, PDO::PARAM_STR, 4000);
            
            // Ejecutar el procedimiento
            $stmt->execute();

            // Ahora, obtener los resultados desde el cursor
            $veterinarios = [];
            while ($row = oci_fetch_assoc($p_cursor)) {
                $veterinarios[] = $row;
            }

            if (empty($veterinarios)) {
                echo "<p>No hay veterinarios registrados.</p>";
            } else {
                echo "<table class='table'>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Apellido 1</th>
                                <th>Apellido 2</th>
                                <th>Especialidad</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>";
                foreach ($veterinarios as $veterinario) {
                    echo "<tr>
                            <td>{$veterinario['ID_VETERINARIO']}</td>
                            <td>{$veterinario['NOMBRE']}</td>
                            <td>{$veterinario['APELLIDO1']}</td>
                            <td>{$veterinario['APELLIDO2']}</td>
                            <td>{$veterinario['ESPECIALIDAD']}</td>
                            <td>{$veterinario['TELEFONO']}</td>
                            <td>{$veterinario['EMAIL']}</td>
                          </tr>";
                }
                echo "</tbody></table>";
            }
        } elseif ($action === 'insertar') {
            $nombre = $_POST['nombre'];
            $apellido1 = $_POST['apellido1'];
            $apellido2 = $_POST['apellido2'];
            $especialidad = $_POST['especialidad'];
            $telefono = $_POST['telefono'];

            $stmt = $conn->prepare("BEGIN LAROATLB_GESTIONAR_VETERINARIOS('C', NULL, :nombre, :apellido1, :apellido2, :especialidad, :telefono); END;");
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido1', $apellido1);
            $stmt->bindParam(':apellido2', $apellido2);
            $stmt->bindParam(':especialidad', $especialidad);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->execute();
            echo "Veterinario insertado exitosamente.";
            header("Location: ".$_SERVER['PHP_SELF']); // Redirige para evitar repetir la acción
            exit();
        } elseif ($action === 'actualizar') {
            $id_veterinario = $_POST['id_veterinario'];
            $nombre = $_POST['nombre'];
            $apellido1 = $_POST['apellido1'];
            $apellido2 = $_POST['apellido2'];
            $especialidad = $_POST['especialidad'];
            $telefono = $_POST['telefono'];

            $stmt = $conn->prepare("BEGIN LAROATLB_GESTIONAR_VETERINARIOS('U', :id_veterinario, :nombre, :apellido1, :apellido2, :especialidad, :telefono); END;");
            $stmt->bindParam(':id_veterinario', $id_veterinario);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellido1', $apellido1);
            $stmt->bindParam(':apellido2', $apellido2);
            $stmt->bindParam(':especialidad', $especialidad);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->execute();
            echo "Veterinario actualizado exitosamente.";
            header("Location: ".$_SERVER['PHP_SELF']); // Redirige para evitar repetir la acción
            exit();
        } elseif ($action === 'eliminar') {
            $id_veterinario = $_POST['id_veterinario'];

            $stmt = $conn->prepare("BEGIN LAROATLB_GESTIONAR_VETERINARIOS('D', :id_veterinario); END;");
            $stmt->bindParam(':id_veterinario', $id_veterinario);
            $stmt->execute();
            echo "Veterinario eliminado exitosamente.";
            header("Location: ".$_SERVER['PHP_SELF']); // Redirige para evitar repetir la acción
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
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
<div class="container">
    <h1>Gestión de Veterinarios</h1>
    <div class="button-container">
        <button class="btn btn-primary" onclick="showForm('insertar')">Insertar</button>
        <button class="btn btn-primary" onclick="submitForm('listar')">Listar</button>
        <button class="btn btn-danger" onclick="showForm('eliminar')">Eliminar</button>
        <button class="btn btn-warning" onclick="showForm('actualizar')">Actualizar</button>
    </div>
    <div id="formContainer" class="form-container"></div>
</div>

<script>
    function showForm(action) {
        const container = document.getElementById('formContainer');
        let html = '';

        if (action === 'insertar') {
            html = `
                <form method="POST">
                    <input type="hidden" name="action" value="insertar">
                    <input type="text" name="nombre" placeholder="Nombre" required>
                    <input type="text" name="apellido1" placeholder="Apellido 1" required>
                    <input type="text" name="apellido2" placeholder="Apellido 2">
                    <input type="text" name="especialidad" placeholder="Especialidad">
                    <input type="text" name="telefono" placeholder="Teléfono">
                    <button type="submit">Insertar</button>
                </form>
            `;
        } else if (action === 'eliminar') {
            html = `
                <form method="POST">
                    <input type="hidden" name="action" value="eliminar">
                    <input type="text" name="id_veterinario" placeholder="ID Veterinario" required>
                    <button type="submit">Eliminar</button>
                </form>
            `;
        } else if (action === 'actualizar') {
            html = `
                <form method="POST">
                    <input type="hidden" name="action" value="actualizar">
                    <input type="text" name="id_veterinario" placeholder="ID Veterinario" required>
                    <input type="text" name="nombre" placeholder="Nombre">
                    <input type="text" name="apellido1" placeholder="Apellido 1">
                    <input type="text" name="apellido2" placeholder="Apellido 2">
                    <input type="text" name="especialidad" placeholder="Especialidad">
                    <input type="text" name="telefono" placeholder="Teléfono">
                    <button type="submit">Actualizar</button>
                </form>
            `;
        }

        container.innerHTML = html;
    }

    function submitForm(action) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';

        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'action';
        input.value = action;
        form.appendChild(input);

        document.body.appendChild(form);
        form.submit();
    }
</script>
</body>
</html>

