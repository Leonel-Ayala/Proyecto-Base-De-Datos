-- MANTENEDOR DE DETALLE PRODUCTO TRATAMIENTO

CREATE OR REPLACE PROCEDURE LAROATLB_REGISTRAR_DETALLE_TRATAMIENTO (
    P_ID_MASCOTA    IN NUMBER,
    P_ID_PRODUCTO   IN NUMBER,
    P_CANTIDAD      IN NUMBER
)
IS
    NUM_TRATA NUMBER;
BEGIN
    -- Obtener el ID_TRATAMIENTO más reciente para la mascota y la fecha actual
    SELECT ID_TRATAMIENTO
    INTO NUM_TRATA
    FROM LAROATLB_TRATAMIENTO
    WHERE ID_MASCOTA = p_id_mascota
      AND TRUNC(FECHA) = TRUNC(SYSDATE) -- Asegurar que sea de la fecha actual
    ORDER BY FECHA DESC
    FETCH FIRST 1 ROWS ONLY;

    -- Insertar en la tabla de detalle
    INSERT INTO LAROATLB_DETALLE_PRODUCTO_TRATAMIENTO (
        ID_TRATAMIENTO, 
        ID_PRODUCTO, 
        CANTIDAD
    ) VALUES (
        NUM_TRATA,
        P_ID_PRODUCTO,
        P_CANTIDAD
    );
    

EXCEPTION
    WHEN NO_DATA_FOUND THEN
        p_resultado := 'No se encontró un tratamiento para la mascota especificada en la fecha actual.';
    WHEN OTHERS THEN
        p_resultado := 'Error al registrar el detalle: ' || SQLERRM;
END;
