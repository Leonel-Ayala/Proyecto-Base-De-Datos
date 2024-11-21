-- Consulta De citas Veterinario

CREATE OR REPLACE PROCEDURE LAROATLB_LISTAR_CITAS_POR_VETERINARIO (
    p_fecha        DATE,
    p_id_veterinario NUMBER,
    p_cursor       OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT 
            V.Nombre || ' ' || V.Apellido1 || ' ' || V.Apellido2 AS Veterinario,
            COUNT(C.ID_Cita) AS Total_Citas
        FROM 
            LAROATLB_Cita C
        JOIN 
            LAROATLB_Veterinario V ON C.ID_Veterinario = V.ID_Veterinario
        WHERE 
            TRUNC(C.Fecha) = TRUNC(p_fecha) -- Filtrar por el día específico proporcionado
            AND C.ID_Veterinario = p_id_veterinario -- Filtrar por el ID del veterinario
        GROUP BY 
            V.Nombre, V.Apellido1, V.Apellido2
        ORDER BY 
            Total_Citas DESC;
END;



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
// Incluir el archivo de conexión a la base de datos
include '../includes/db_connection.php';

// Verificar si se recibió el ID de la mascota desde el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_mascota'])) {
    $id_mascota = intval($_POST['id_mascota']); // Asegurarse de que sea un número entero

    // Consultar la información de la ficha médica
    $query_ficha = "
        SELECT
            M.ID_MASCOTA,
            M.NOMBRE AS NOMBRE_MASCOTA,
            M.EDAD,
            C.NOMBRE || ' ' || C.APELLIDO AS CLIENTE,
            R.NOMBRE_RAZA AS RAZA,
            E.NOMBRE_ESPECIE AS ESPECIE
        FROM
            LAROATLB_MASCOTA M
        INNER JOIN
            LAROATLB_CLIENTE C ON M.ID_CLIENTE = C.ID_CLIENTE
        INNER JOIN
            LAROATLB_RAZA R ON M.ID_RAZA = R.ID_RAZA
        INNER JOIN
            LAROATLB_ESPECIE E ON R.ID_ESPECIE = E.ID_ESPECIE
        WHERE
            M.ID_MASCOTA = :id_mascota";

    // Preparar y ejecutar la consulta
    $stmt_ficha = oci_parse($conn, $query_ficha);
    oci_bind_by_name($stmt_ficha, ':id_mascota', $id_mascota);
    oci_execute($stmt_ficha);

    // Obtener los resultados de la ficha médica
    $ficha = oci_fetch_assoc($stmt_ficha);

    // Consultar los tratamientos relacionados con la mascota
    $query_tratamientos = "
        SELECT
            T.ID_TRATAMIENTO,
            T.FECHA_INICIO,
            T.FECHA_FIN,
            T.DESCRIPCION
        FROM
            LAROATLB_TRATAMIENTO T
        WHERE
            T.ID_MASCOTA = :id_mascota";

    $stmt_tratamientos = oci_parse($conn, $query_tratamientos);
    oci_bind_by_name($stmt_tratamientos, ':id_mascota', $id_mascota);
    oci_execute($stmt_tratamientos);

    // Cerrar el statement de la ficha
    oci_free_statement($stmt_ficha);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha Médica de Mascota</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <h1>Ficha Médica de Mascota</h1>

    <!-- Formulario para ingresar el ID de la mascota -->
    <form method="POST" action="ficha_mascota.php">
        <label for="id_mascota">Ingrese el ID de la Mascota:</label>
        <input type="number" id="id_mascota" name="id_mascota" required>
        <button type="submit">Consultar</button>
    </form>

    <?php if (isset($ficha)): ?>
        <h2>Información de la Mascota</h2>
        <table border="1">
            <tr><th>ID Mascota</th><td><?php echo $ficha['ID_MASCOTA']; ?></td></tr>
            <tr><th>Nombre</th><td><?php echo $ficha['NOMBRE_MASCOTA']; ?></td></tr>
            <tr><th>Edad</th><td><?php echo $ficha['EDAD']; ?></td></tr>
            <tr><th>Cliente</th><td><?php echo $ficha['CLIENTE']; ?></td></tr>
            <tr><th>Raza</th><td><?php echo $ficha['RAZA']; ?></td></tr>
            <tr><th>Especie</th><td><?php echo $ficha['ESPECIE']; ?></td></tr>
        </table>

        <h2>Tratamientos Relacionados</h2>
        <table border="1">
            <tr>
                <th>ID Tratamiento</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Descripción</th>
            </tr>
            <?php while ($tratamiento = oci_fetch_assoc($stmt_tratamientos)): ?>
                <tr>
                    <td><?php echo $tratamiento['ID_TRATAMIENTO']; ?></td>
                    <td><?php echo $tratamiento['FECHA_INICIO']; ?></td>
                    <td><?php echo $tratamiento['FECHA_FIN']; ?></td>
                    <td><?php echo $tratamiento['DESCRIPCION']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <p>No se encontró información para el ID de la mascota ingresado.</p>
    <?php endif; ?>

    <?php
    // Cerrar el statement de tratamientos
    if (isset($stmt_tratamientos)) {
        oci_free_statement($stmt_tratamientos);
    }

    // Cerrar la conexión a la base de datos
    oci_close($conn);
    ?>
</body>
</html>

--------------------------------------------------------------------------

--- REPORTE PRODUCTOS MAS USADOS

CREATE OR REPLACE PROCEDURE LAROATLB_Reporte_Productos_Mas_Usados (
    p_cursor OUT SYS_REFCURSOR
)
AS
BEGIN
    OPEN p_cursor FOR
        SELECT 
            P.ID_Producto,
            P.Nombre_Producto,
            SUM(DPT.Cantidad) AS Total_Usado
        FROM 
            LAROATLB_Producto P
        JOIN 
            LAROATLB_Detalle_Producto_Tratamiento DPT
            ON P.ID_Producto = DPT.ID_Producto
        GROUP BY 
            P.ID_Producto, P.Nombre_Producto
        ORDER BY 
            Total_Usado DESC;
END;
------------------------------------------
---idea gpt de llamado en pagina web

<?php
// Conexión a la base de datos Oracle
$conn = oci_connect('usuario', 'contraseña', 'localhost/XE');

if (!$conn) {
    $error = oci_error();
    echo "Error al conectar: " . $error['message'];
    exit;
}

// Preparar la llamada al procedimiento
$sql = 'BEGIN LAROATLB_Reporte_Productos_Mas_Usados(:cursor); END;';
$stmt = oci_parse($conn, $sql);

// Declarar el cursor de salida
$cursor = oci_new_cursor($conn);

// Vincular el cursor al procedimiento
oci_bind_by_name($stmt, ':cursor', $cursor, -1, OCI_B_CURSOR);

// Ejecutar el procedimiento
if (!oci_execute($stmt)) {
    $error = oci_error($stmt);
    echo "Error al ejecutar el procedimiento: " . $error['message'];
    exit;
}

// Ejecutar el cursor
oci_execute($cursor);

// Mostrar los datos en una tabla HTML
echo "<table border='1'>";
echo "<tr><th>ID Producto</th><th>Nombre Producto</th><th>Total Usado</th></tr>";

while (($row = oci_fetch_assoc($cursor)) != false) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['ID_PRODUCTO']) . "</td>";
    echo "<td>" . htmlspecialchars($row['NOMBRE_PRODUCTO']) . "</td>";
    echo "<td>" . htmlspecialchars($row['TOTAL_USADO']) . "</td>";
    echo "</tr>";
}

echo "</table>";

// Liberar recursos
oci_free_statement($stmt);
oci_free_statement($cursor);
oci_close($conn);
?>
-------------------------------------------------------------------------------------



