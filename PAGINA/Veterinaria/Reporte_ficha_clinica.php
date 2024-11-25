<?php
header('Content-Type: text/html; charset=utf-8');

// Configuración de conexión
$host = 'localhost';
$port = '1521';
$dbname = 'XE';
$username = 'vetsol';
$password = 'oracle';

// Conexión OCI
$conn_oci = oci_connect($username, $password, "//{$host}:{$port}/{$dbname}");
if (!$conn_oci) {
    $e = oci_error();
    die("Error de conexión OCI: " . $e['message']);
}

// Variables para almacenar resultados
$htmlFicha = '';
$htmlTratamientos = '';

// Función para obtener contenido desde los cursores
function obtenerContenidoDesdeCursor($cursor) {
    $html = '';
    while (($row = oci_fetch_array($cursor, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
        foreach ($row as $key => $value) {
            $html .= '<div class="list-group-item">';
            $html .= '<strong>' . htmlspecialchars($key, ENT_QUOTES) . ':</strong> ';
            $html .= htmlspecialchars($value, ENT_QUOTES);
            $html .= '</div>';
        }
    }
    return $html;
}

// Función para separar los tratamientos y usar el ID del tratamiento
function obtenerTratamientosSeparados($cursor) {
    $html = '';
    while (($row = oci_fetch_array($cursor, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
        // Usar el ID del tratamiento (suponiendo que está en el campo "ID_TRATAMIENTO")
        $idTratamiento = $row['ID_TRATAMIENTO'] ?? 'No disponible';  // Cambiar el nombre del campo si es necesario

        $html .= '<div class="card mb-3">';
        $html .= '<div class="card-body">';
        $html .= '<h5 class="card-title">Tratamiento ID: ' . htmlspecialchars($idTratamiento, ENT_QUOTES) . '</h5>';
        foreach ($row as $key => $value) {
            // Evitar mostrar el ID_TRATAMIENTO en el cuerpo de la tarjeta
            if ($key != 'ID_TRATAMIENTO') {
                $html .= '<p class="card-text"><strong>' . htmlspecialchars($key, ENT_QUOTES) . ':</strong> ';
                $html .= htmlspecialchars($value, ENT_QUOTES) . '</p>';
            }
        }
        $html .= '</div>';
        $html .= '</div>';
    }
    return $html;
}

// Comprobación si se presionó el botón
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['listar'])) {
    $idMascota = $_POST['id_mascota'];

    // Procedimiento almacenado
    $sql = "BEGIN LAROATLB_OBTENER_FICHA_COMPLETA(:p_id_mascota, :p_ficha, :p_tratamientos); END;";
    $stmt = oci_parse($conn_oci, $sql);

    // Vincular parámetros
    oci_bind_by_name($stmt, ':p_id_mascota', $idMascota);
    $fichaCursor = oci_new_cursor($conn_oci);
    $tratamientosCursor = oci_new_cursor($conn_oci);
    oci_bind_by_name($stmt, ':p_ficha', $fichaCursor, -1, OCI_B_CURSOR);
    oci_bind_by_name($stmt, ':p_tratamientos', $tratamientosCursor, -1, OCI_B_CURSOR);

    // Ejecutar procedimiento
    if (oci_execute($stmt)) {
        // Abrir cursores
        oci_execute($fichaCursor);
        oci_execute($tratamientosCursor);

        // Obtener contenido
        $htmlFicha = obtenerContenidoDesdeCursor($fichaCursor);
        $htmlTratamientos = obtenerTratamientosSeparados($tratamientosCursor);

        // Cerrar cursores
        oci_free_statement($fichaCursor);
        oci_free_statement($tratamientosCursor);
    } else {
        $e = oci_error($stmt);
        $htmlFicha = "Error al ejecutar el procedimiento: " . htmlspecialchars($e['message'], ENT_QUOTES);
    }

    oci_free_statement($stmt);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obtener Ficha Completa</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include("nav_veterinario.php"); ?>
    <div class="container mt-4">
        <h1>Obtener Ficha Completa y Tratamientos</h1>
        <form method="post" class="form-inline mb-4">
            <label for="id_mascota" class="mr-2">ID de la Mascota:</label>
            <input type="number" id="id_mascota" name="id_mascota" class="form-control mr-2" required>
            <button type="submit" name="listar" class="btn btn-primary">Listar</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['listar'])): ?>
            <div class="resultados">
                <h2>Ficha Completa de la Mascota</h2>
                <div class="list-group">
                    <?= $htmlFicha; ?>
                </div>
                <h2 class="mt-4">Tratamientos</h2>
                <div class="mb-4">
                    <?= $htmlTratamientos; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cerrarSesion() {
            alert("Has cerrado sesión exitosamente.");
            window.location.href='../hola.php';  // Redirigir a la página de login o inicio
        }
    </script>
</body>
</html>
