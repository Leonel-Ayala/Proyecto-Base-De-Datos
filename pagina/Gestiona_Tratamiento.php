<?php
// Incluir la conexión a la base de datos
include('conexion.php');

// Variables para operaciones
$operacion = isset($_POST['operacion']) ? $_POST['operacion'] : '';
$id_tratamiento = isset($_POST['id_tratamiento']) ? $_POST['id_tratamiento'] : '';
$descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
$id_mascota = isset($_POST['id_mascota']) ? $_POST['id_mascota'] : '';
$id_veterinario = isset($_POST['id_veterinario']) ? $_POST['id_veterinario'] : '';

// Preparar el procedimiento almacenado
$stid = oci_parse($conn, 'BEGIN LAROATLB_GESTIONAR_TRATAMIENTOS(:operacion, :id_tratamiento, :descripcion, :fecha, :id_mascota, :id_veterinario); END;');

// Bind de los parámetros
oci_bind_by_name($stid, ':operacion', $operacion);
oci_bind_by_name($stid, ':id_tratamiento', $id_tratamiento);
oci_bind_by_name($stid, ':descripcion', $descripcion);
oci_bind_by_name($stid, ':fecha', $fecha);
oci_bind_by_name($stid, ':id_mascota', $id_mascota);
oci_bind_by_name($stid, ':id_veterinario', $id_veterinario);

// Ejecutar la operación
oci_execute($stid);

// Mostrar los tratamientos en caso de 'R'
if ($operacion == 'R') {
    echo "<h2>Tratamientos de la Mascota con ID $id_mascota</h2>";
    echo "<table border='1'>";
    echo "<tr><th>ID Tratamiento</th><th>Descripción</th><th>Fecha</th><th>Mascota</th><th>Veterinario</th></tr>";

    // Mostrar resultados
    while ($row = oci_fetch_assoc($stid)) {
        echo "<tr>";
        echo "<td>" . $row['ID_TRATAMIENTO'] . "</td>";
        echo "<td>" . $row['DESCRIPCION'] . "</td>";
        echo "<td>" . $row['FECHA'] . "</td>";
        echo "<td>" . $row['NOMBRE_MASCOTA'] . "</td>";
        echo "<td>" . $row['NOMBRE_VETERINARIO'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
}

// Formularios para crear, actualizar o eliminar
if ($operacion == 'C' || $operacion == 'U' || $operacion == 'D') {
    echo "<h2>Formulario para $operacion Tratamiento</h2>";

    if ($operacion == 'C') {
        echo "<form method='POST'>
                Descripción: <input type='text' name='descripcion' required><br>
                Fecha: <input type='date' name='fecha' required><br>
                ID Mascota: <input type='number' name='id_mascota' required><br>
                ID Veterinario: <input type='number' name='id_veterinario' required><br>
                <input type='hidden' name='operacion' value='C'>
                <input type='submit' value='Crear Tratamiento'>
              </form>";
    }

    if ($operacion == 'U' || $operacion == 'D') {
        echo "<form method='POST'>
                ID Tratamiento: <input type='number' name='id_tratamiento' required><br>";
        if ($operacion == 'U') {
            echo "Descripción: <input type='text' name='descripcion'><br>
                  Fecha: <input type='date' name='fecha'><br>
                  ID Mascota: <input type='number' name='id_mascota'><br>
                  ID Veterinario: <input type='number' name='id_veterinario'><br>";
        }
        echo "<input type='hidden' name='operacion' value='$operacion'>
              <input type='submit' value='" . ($operacion == 'U' ? 'Actualizar' : 'Eliminar') . "'>
              </form>";
    }
}

// Liberar recursos y cerrar la conexión
oci_free_statement($stid);
oci_close($conn);
?>

<!-- Botones para CRUD -->
<form method="POST">
    <input type="submit" name="operacion" value="R" />
    <input type="submit" name="operacion" value="C" />
    <input type="submit" name="operacion" value="U" />
    <input type="submit" name="operacion" value="D" />
</form>
