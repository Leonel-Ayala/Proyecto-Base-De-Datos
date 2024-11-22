-------------------
--CURSORES NECESARIOS PARA INSCRIBIR CLIENTE----PONER EN TABLITA AL MOMENTO DE INSCRIBIR, PARA DISPOSICION DE LA SECRETARIA

-- CURSOR DE REGION
CREATE OR REPLACE PROCEDURE LAROATLB_LISTAR_REGIONES (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT ID_REGION, NOMBRE_REGION
        FROM LAROATLB_REGION_CLIENTE;
END;
-------------
-- CURSOR DE COMUNA
CREATE OR REPLACE PROCEDURE LAROATLB_LISTAR_COMUNAS (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT ID_COMUNA, NOMBRE_COMUNA, ID_REGION
        FROM LAROATLB_COMUNA_CLIENTE;
END;

------------------------------------------


-------------------
--CURSORES NECESARIOS PARA GENERAR CITA ----PONER EN TABLITA AL MOMENTO DE INSCRIBIR, PARA DISPOSICION DE LA SECRETARIA

--------CURSOR DE MASCOTA PARA MOSTRAR LAS MASCOTAS DISPONIBLES
CREATE OR REPLACE PROCEDURE LAROATLB_LISTAR_MASCOTAS (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT ID_MASCOTA, NOMBRE
        FROM LAROATLB_MASCOTA
        
END;
--------CURSOR DE MASCOTA PARA MOSTRAR LAS MASCOTAS DISPONIBLES



