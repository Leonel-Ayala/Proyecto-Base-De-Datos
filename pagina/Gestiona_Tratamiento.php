<?php
// Incluye tu archivo de conexión a la base de datos
include('conexion.php');

// Acción que se tomará según el botón presionado
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

// Cuando el usuario selecciona "Ver Tratamientos"
if ($accion == 'R') {
    // Llamar al procedimiento para consultar tratamientos
    $sql = "BEGIN LAROATLB_GESTIONAR_TRATAMIENTOS('R'); END;";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);

    // Mostrar resultados del procedimiento
    echo "<h3>Listado de Tratamientos</h3>";
    echo "<table border='1'>
            <tr>
                <th>ID Tratamiento</th>
                <th>Descripción</th>
                <th>Fecha</th>
                <th>ID Mascota</th>
                <th>ID Veterinario</th>
            </tr>";

    // Imprimir los datos en una tabla
    while ($row = oci_fetch_assoc($stmt)) {
        echo "<tr>
                <td>" . $row['ID_TRATAMIENTO'] . "</td>
                <td>" . $row['DESCRIPCION'] . "</td>
                <td>" . $row['FECHA'] . "</td>
                <td>" . $row['ID_MASCOTA'] . "</td>
                <td>" . $row['ID_VETERINARIO'] . "</td>
              </tr>";
    }
    echo "</table>";
}

// Cuando el usuario selecciona "Agregar Tratamiento"
elseif ($accion == 'C') {
    // Si se ha enviado el formulario con los datos del nuevo tratamiento
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $descripcion = $_POST['descripcion'];
        $fecha = $_POST['fecha'];
        $id_mascota = $_POST['id_mascota'];
        $id_veterinario = $_POST['id_veterinario'];

        // Llamar al procedimiento para agregar un nuevo tratamiento
        $sql = "BEGIN LAROATLB_GESTIONAR_TRATAMIENTOS('C', NULL, :descripcion, :fecha, :id_mascota, :id_veterinario); END;";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':descripcion', $descripcion);
        oci_bind_by_name($stmt, ':fecha', $fecha);
        oci_bind_by_name($stmt, ':id_mascota', $id_mascota);
        oci_bind_by_name($stmt, ':id_veterinario', $id_veterinario);
        oci_execute($stmt);

        echo "<p>Tratamiento agregado correctamente.</p>";
    }

    // Formulario para agregar tratamiento
    echo '<form method="POST" action="">
            <label>Descripción:</label><br><input type="text" name="descripcion" required><br>
            <label>Fecha:</label><br><input type="date" name="fecha" required><br>
            <label>ID Mascota:</label><br><input type="number" name="id_mascota" required><br>
            <label>ID Veterinario:</label><br><input type="number" name="id_veterinario" required><br>
            <button type="submit">Agregar Tratamiento</button>
        </form>';
}

// Cuando el usuario selecciona "Actualizar Tratamiento"
elseif ($accion == 'U') {
    // Aquí se podrá manejar la actualización (similar a lo anterior)
    echo "<h3>Actualizar Tratamiento</h3>";
    // Procesar formulario para actualizar tratamiento con ID
}

// Cuando el usuario selecciona "Eliminar Tratamiento"
elseif ($accion == 'D') {
    // Aquí se podrá manejar la eliminación
    echo "<h3>Eliminar Tratamiento</h3>";
    // Procesar formulario para eliminar tratamiento con ID
}
?>

<!-- Botones para accionar las operaciones CRUD -->
<form method="POST">
    <button name="accion" value="R">Ver Tratamientos</button>
    <button name="accion" value="C">Agregar Tratamiento</button>
    <button name="accion" value="U">Actualizar Tratamiento</button>
    <button name="accion" value="D">Eliminar Tratamiento</button>
</form>
