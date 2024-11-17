<?php 
$host = 'localhost';
$port = '1521';
$dbname = 'XE'; // Cambia según tu configuración
$username = 'HR';
$password = '123';
//include('links.js');

try {
    $dsn = "oci:dbname=//$host:$port/$dbname;charset=UTF8";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
// Manejar la acción
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'insertar') {
        $area_trabajo = $_POST['area_trabajo'];
        $stmt = $conn->prepare("BEGIN C_ROLES(:area_trabajo); END;");
        $stmt->bindParam(':area_trabajo', $area_trabajo);
        $stmt->execute();
        echo "Registro insertado exitosamente.";
    } elseif ($action === 'listar') {
        $stmt = $conn->query("SELECT ID_ROL, AREA_TRABAJO FROM Roles");
        echo "<table><tr><th>ID</th><th>Área de Trabajo</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr><td>{$row['ID_ROL']}</td><td>{$row['AREA_TRABAJO']}</td></tr>";
        }
        echo "</table>";
    } elseif ($action === 'borrar') {
        $ID_ROL = $_POST['id_rol'];
        $stmt = $conn->prepare("DELETE FROM Roles WHERE ID_ROL = :ID_ROL");
        $stmt->bindParam(':ID_ROL', $ID_ROL);
        $stmt->execute();
        echo "Registro borrado exitosamente.";
    } elseif ($action === 'actualizar') {
        $ID_ROL = $_POST['id_rol'];
        $area_trabajo = $_POST['area_trabajo'];
        $stmt = $conn->prepare("UPDATE Roles SET AREA_TRABAJO = :area_trabajo WHERE ID_Veterinario = :ID_ROL");
        $stmt->bindParam(':ID_ROL', $ID_ROL);
        $stmt->bindParam(':area_trabajo', $area_trabajo);
        $stmt->execute();
        echo "Registro actualizado exitosamente.";
    }
    echo "<button onclick=redireccionRoles() > Volver </button>";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Roles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .button-container {
            margin: 20px 0;
        }
        .form-container {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Gestión de Roles</h1>
    <div class="button-container">
        <button class="btn btn-primary" onclick="showForm('insertar')">Insertar</button>
        <button class="btn btn-primary" onclick="submitForm('listar')">Listar</button>
        <button class="btn btn-danger" onclick="showForm('borrar')">Borrar</button>
        <button class="btn btn-warning" onclick="showForm('actualizar')">Actualizar</button>
    </div>

    <div id="formContainer" class="form-container"></div>
    <div id="output"></div>
</div>

<script>
    function showForm(action) {
        const container = document.getElementById('formContainer');
        let html = '';

        if (action === 'insertar') {
            html = `
                <form method="POST" action="roles.php">
                    <input type="hidden" name="action" value="insertar">
                    <div class="mb-3">
                        <label for="area_trabajo" class="form-label">Área de Trabajo:</label>
                        <input type="text" id="area_trabajo" name="area_trabajo" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Insertar</button>
                </form>
            `;
        } else if (action === 'borrar') {
            html = `
                <form method="POST" action="roles.php">
                    <input type="hidden" name="action" value="borrar">
                    <div class="mb-3">
                        <label for="id_rol" class="form-label">ID del Rol a borrar:</label>
                        <input type="number" id="id_rol" name="id_rol" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-danger">Borrar</button>
                </form>
            `;
        } else if (action === 'actualizar') {
            html = `
                <form method="POST" action="roles.php">
                    <input type="hidden" name="action" value="actualizar">
                    <div class="mb-3">
                        <label for="id_rol" class="form-label">ID del Rol a actualizar:</label>
                        <input type="number" id="id_rol" name="id_rol" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="area_trabajo" class="form-label">Nueva Área de Trabajo:</label>
                        <input type="text" id="area_trabajo" name="area_trabajo" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning">Actualizar</button>
                </form>
            `;
        }

        container.innerHTML = html;
    }

    function submitForm(action) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'roles.php';

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