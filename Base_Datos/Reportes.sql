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
--REPORTE  MAYOR CANTIDAD DE INGRESOS

CREATE OR REPLACE PROCEDURE LAROATLB_AGRUPAR_INGRESOS (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT 
            NOMBRE_INGRESO,
            COUNT(*) AS CANTIDAD_INGRESOS
        FROM 
            LAROATLB_LOG_LOGIN
        GROUP BY 
            NOMBRE_INGRESO
        ORDER BY 
            CANTIDAD_INGRESOS DESC; -- Opcional: Ordenar por cantidad de ingresos
END;

-------------------------------------------------------------------------------------



