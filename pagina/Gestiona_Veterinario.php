<?php
// Incluye tu archivo de conexión a la base de datos
include('conexion.php');

// Acción que se tomará según el botón presionado
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

// Cuando el usuario selecciona "Ver Veterinarios"
if ($accion == 'R') {
    // Llamar al procedimiento para consultar veterinarios
    $sql = "BEGIN LAROATLB_GESTIONAR_VETERINARIOS('R'); END;";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);

    // Mostrar resultados del procedimiento
    echo "<h3>Listado de Veterinarios</h3>";
    echo "<table border='1'>
            <tr>
                <th>ID Veterinario</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Especialidad</th>
                <th>Teléfono</th>
                <th>Email</th>
            </tr>";

    // Imprimir los datos en una tabla
    while ($row = oci_fetch_assoc($stmt)) {
        echo "<tr>
                <td>" . $row['ID_VETERINARIO'] . "</td>
                <td>" . $row['NOMBRE'] . " " . $row['APELLIDO1'] . " " . $row['APELLIDO2'] . "</td>
                <td>" . $row['ESPECIALIDAD'] . "</td>
                <td>" . $row['TELEFONO'] . "</td>
                <td>" . $row['EMAIL'] . "</td>
              </tr>";
    }
    echo "</table>";
}

// Cuando el usuario selecciona "Agregar Veterinario"
elseif ($accion == 'C') {
    // Si se ha enviado el formulario con los datos del nuevo veterinario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $nombre = $_POST['nombre'];
        $apellido1 = $_POST['apellido1'];
        $apellido2 = $_POST['apellido2'];
        $especialidad = $_POST['especialidad'];
        $telefono = $_POST['telefono'];

        // Llamar al procedimiento para agregar un nuevo veterinario
        $sql = "BEGIN LAROATLB_GESTIONAR_VETERINARIOS('C', NULL, :nombre, :apellido1, :apellido2, :especialidad, :telefono); END;";
        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':nombre', $nombre);
        oci_bind_by_name($stmt, ':apellido1', $apellido1);
        oci_bind_by_name($stmt, ':apellido2', $apellido2);
        oci_bind_by_name($stmt, ':especialidad', $especialidad);
        oci_bind_by_name($stmt, ':telefono', $telefono);
        oci_execute($stmt);

        echo "<p>Veterinario agregado correctamente.</p>";
    }

    // Formulario para agregar veterinario
    echo '<form method="POST" action="">
            <label>Nombre:</label><br><input type="text" name="nombre" required><br>
            <label>Apellido 1:</label><br><input type="text" name="apellido1" required><br>
            <label>Apellido 2:</label><br><input type="text" name="apellido2"><br>
            <label>Especialidad:</label><br><input type="text" name="especialidad"><br>
            <label>Teléfono:</label><br><input type="number" name="telefono" required><br>
            <button type="submit">Agregar Veterinario</button>
        </form>';
}

// Cuando el usuario selecciona "Actualizar Veterinario"
elseif ($accion == 'U') {
    // Aquí se podrá manejar la actualización (similar a lo anterior)
    echo "<h3>Actualizar Veterinario</h3>";
    // Procesar formulario para actualizar veterinario con ID
}

// Cuando el usuario selecciona "Eliminar Veterinario"
elseif ($accion == 'D') {
    // Aquí se podrá manejar la eliminación
    echo "<h3>Eliminar Veterinario</h3>";
    // Procesar formulario para eliminar veterinario con ID
}
?>

<!-- Botones para accionar las operaciones CRUD -->
<form method="POST">
    <button name="accion" value="R">Ver Veterinarios</button>
    <button name="accion" value="C">Agregar Veterinario</button>
    <button name="accion" value="U">Actualizar Veterinario</button>
    <button name="accion" value="D">Eliminar Veterinario</button>
</form>
