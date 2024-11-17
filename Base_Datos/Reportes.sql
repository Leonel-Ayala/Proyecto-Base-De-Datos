-- Consulta De citas Veterinario


SELECT 
    V.Nombre || ' ' || V.Apellido1 || ' ' || V.Apellido2 AS Veterinario,
    COUNT(C.ID_Cita) AS Total_Citas
FROM 
    LAROATLB_Cita C
JOIN 
    LAROATLB_Veterinario V ON C.ID_Veterinario = V.ID_Veterinario
WHERE 
    C.Fecha BETWEEN TO_DATE('2024-11-01', 'YYYY-MM-DD') AND TO_DATE('2024-11-30', 'YYYY-MM-DD')
GROUP BY 
    V.Nombre, V.Apellido1, V.Apellido2
ORDER BY 
    Total_Citas DESC;





-- Consulta Ficha Clinica Mascota
CREATE OR REPLACE PROCEDURE LAROATLB_OBTENER_FICHA_COMPLETA (
    p_id_mascota IN NUMBER,
    p_ficha OUT SYS_REFCURSOR,
    p_tratamientos OUT SYS_REFCURSOR
)
IS
BEGIN
    -- Consulta para la ficha de la mascota
    OPEN p_ficha FOR
        SELECT
            M.ID_MASCOTA,
            M.NOMBRE AS NOMBRE_MASCOTA,
            M.EDAD AS EDAD_MASCOTA,
            R.NOMBRE_RAZA,
            E.NOMBRE_ESPECIE,
            C.NOMBRE AS NOMBRE_CLIENTE,
            C.TELEFONO AS TELEFONO_CLIENTE,
            C.EMAIL AS EMAIL_CLIENTE
        FROM LAROATLB_MASCOTA M
        JOIN LAROATLB_RAZA R ON M.ID_RAZA = R.ID_RAZA
        JOIN LAROATLB_ESPECIE E ON R.ID_ESPECIE = E.ID_ESPECIE
        JOIN LAROATLB_CLIENTE C ON M.ID_CLIENTE = C.ID_CLIENTE
        WHERE M.ID_MASCOTA = p_id_mascota;

    -- Consulta para los tratamientos de la mascota
    OPEN p_tratamientos FOR
        SELECT
            T.ID_TRATAMIENTO,
            T.DESCRIPCION,
            T.FECHA
        FROM LAROATLB_TRATAMIENTO T
        WHERE T.ID_MASCOTA = p_id_mascota;
END;
/

----------------------------
-- Codigo idea de gpt pa su aplicacion
<?php
// Configuración de conexión a la base de datos
$host = 'tu_host';
$puerto = 'tu_puerto';
$nombre_bd = 'tu_base_de_datos';
$usuario = 'tu_usuario';
$contrasena = 'tu_contrasena';

// Obtener el ID de la mascota desde el formulario
$id_mascota = $_POST['id_mascota'];

try {
    // Conexión a la base de datos
    $conn = oci_connect($usuario, $contrasena, "$host:$puerto/$nombre_bd");

    if (!$conn) {
        $e = oci_error();
        throw new Exception($e['message']);
    }

    // Preparar la llamada al procedimiento
    $stid = oci_parse($conn, 'BEGIN LAROATLB_OBTENER_FICHA_COMPLETA(:id_mascota, :ficha, :tratamientos); END;');

    // Asignar los parámetros
    oci_bind_by_name($stid, ':id_mascota', $id_mascota);
    $ficha_cursor = oci_new_cursor($conn);
    $tratamientos_cursor = oci_new_cursor($conn);
    oci_bind_by_name($stid, ':ficha', $ficha_cursor, -1, OCI_B_CURSOR);
    oci_bind_by_name($stid, ':tratamientos', $tratamientos_cursor, -1, OCI_B_CURSOR);

    // Ejecutar el procedimiento
    oci_execute($stid);
    oci_execute($ficha_cursor);
    oci_execute($tratamientos_cursor);

    // Mostrar los datos de la ficha
    echo "<h1>Ficha Médica de la Mascota</h1>";
    echo "<table border='1'>
            <tr>
                <th>ID Mascota</th>
                <th>Nombre</th>
                <th>Edad</th>
                <th>Raza</th>
                <th>Especie</th>
                <th>Nombre del Dueño</th>
                <th>Teléfono</th>
                <th>Email</th>
            </tr>";

    while ($row = oci_fetch_assoc($ficha_cursor)) {
        echo "<tr>
                <td>{$row['ID_MASCOTA']}</td>
                <td>{$row['NOMBRE_MASCOTA']}</td>
                <td>{$row['EDAD_MASCOTA']}</td>
                <td>{$row['NOMBRE_RAZA']}</td>
                <td>{$row['NOMBRE_ESPECIE']}</td>
                <td>{$row['NOMBRE_CLIENTE']}</td>
                <td>{$row['TELEFONO_CLIENTE']}</td>
                <td>{$row['EMAIL_CLIENTE']}</td>
            </tr>";
    }
    echo "</table>";

    // Mostrar los tratamientos
    echo "<h2>Tratamientos Relacionados</h2>";
    echo "<table border='1'>
            <tr>
                <th>ID Tratamiento</th>
                <th>Descripción</th>
                <th>Fecha</th>
            </tr>";

    while ($row = oci_fetch_assoc($tratamientos_cursor)) {
        echo "<tr>
                <td>{$row['ID_TRATAMIENTO']}</td>
                <td>{$row['DESCRIPCION']}</td>
                <td>{$row['FECHA']}</td>
            </tr>";
    }
    echo "</table>";

    // Cerrar cursores y conexión
    oci_free_statement($stid);
    oci_free_statement($ficha_cursor);
    oci_free_statement($tratamientos_cursor);
    oci_close($conn);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
--------------------------------------------------------------------------




