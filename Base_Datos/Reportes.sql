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
            M.Nombre AS Mascota,
            C.Sala AS Sala,
            C.Fecha AS Fecha
        FROM 
            LAROATLB_Cita C
        JOIN 
            LAROATLB_Mascota M ON C.ID_Mascota = M.ID_Mascota
        WHERE 
            TRUNC(C.Fecha) = TRUNC(p_fecha) -- Filtrar por el día específico proporcionado
            AND C.ID_Veterinario = p_id_veterinario -- Filtrar por el ID del veterinario
        ORDER BY 
            C.Fecha; -- Ordenar por la fecha de la cita
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


----------------------------

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



