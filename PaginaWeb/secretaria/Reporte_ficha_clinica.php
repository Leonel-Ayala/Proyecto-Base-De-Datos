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
        $idTratamiento = $row['ID_TRATAMIENTO'] ?? 'No disponible';

        $html .= '<div class="card mb-3">';
        $html .= '<div class="card-body">';
        $html .= '<h5 class="card-title">Tratamiento ID: ' . htmlspecialchars($idTratamiento, ENT_QUOTES) . '</h5>';
        foreach ($row as $key => $value) {
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

// Obtener datos de mascotas para el <select>
function obtenerOpcionesMascotas($conn_oci) {
    $html = '';
    $sql = "BEGIN LAROATLB_LISTAR_MASCOTAS(:p_cursor); END;";
    $stmt = oci_parse($conn_oci, $sql);

    // Crear cursor
    $cursor = oci_new_cursor($conn_oci);
    oci_bind_by_name($stmt, ':p_cursor', $cursor, -1, OCI_B_CURSOR);

    // Ejecutar procedimiento
    if (oci_execute($stmt)) {
        oci_execute($cursor);

        // Generar las opciones del select
        while (($row = oci_fetch_array($cursor, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
            $idMascota = htmlspecialchars($row['ID_MASCOTA'], ENT_QUOTES);
            $nombreMascota = htmlspecialchars($row['NOMBRE'], ENT_QUOTES);
            $edadMascota = htmlspecialchars($row['EDAD'], ENT_QUOTES);
            $cliente = htmlspecialchars($row['CLIENTE'], ENT_QUOTES);
            $raza = htmlspecialchars($row['NOMBRE_RAZA'], ENT_QUOTES);

            $html .= "<option value='{$idMascota}'>";
            $html .= "{$nombreMascota} - {$cliente} - {$raza} (Edad: {$edadMascota})";
            $html .= "</option>";
        }

        oci_free_statement($cursor);
    } else {
        $e = oci_error($stmt);
        $html = "<option>Error al ejecutar el procedimiento: " . htmlspecialchars($e['message'], ENT_QUOTES) . "</option>";
    }

    oci_free_statement($stmt);
    return $html;
}

// Obtener las opciones para el select
$opcionesMascotas = obtenerOpcionesMascotas($conn_oci);

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
    <?php include("nav_secre.php"); ?>
    <div class="container mt-4">
        <h1 class="text-center">Obtener Ficha Completa y Tratamientos</h1>
        <form method="post" class="form-inline mb-4">
            <label for="id_mascota" class="mr-2">Selecciona una mascota:</label>
            <select name="id_mascota" id="id_mascota" class="form-control mr-2" required>
                <option value="">-- Selecciona una mascota --</option>
                <?= $opcionesMascotas; ?>
            </select>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
