-- GESTOR DE DETALLE PRODUCTO TRATAMIENTO

create or replace PROCEDURE LAROATLB_REGISTRAR_DETALLE_TRATAMIENTO (
    P_ID_MASCOTA     NUMBER,
    P_ID_PRODUCTO    NUMBER,
    P_CANTIDAD       NUMBER
)
IS
    NUM_TRATA NUMBER;
BEGIN
    -- Obtener el ID_TRATAMIENTO más reciente para la mascota y la fecha actual
    SELECT ID_TRATAMIENTO
    INTO NUM_TRATA
    FROM LAROATLB_TRATAMIENTO
    WHERE ID_MASCOTA = P_ID_MASCOTA
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
        RAISE_APPLICATION_ERROR(-20001,'No se encontró un tratamiento para la mascota especificada en la fecha actual');
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001,'Error al registrar el detalle: ' || SQLERRM);
END;


-----------------------------------------------------------------------------------------

create or replace PROCEDURE LAROATLB_LISTAR_DETALLE_PRODUCTO_TRATAMIENTO (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT ID_TRATAMIENTO, ID_PRODUCTO, CANTIDAD
        FROM LAROATLB_DETALLE_PRODUCTO_TRATAMIENTO
        ORDER BY 1 ASC;
END;


-----------------------------------------------------------------------------------------
---- GESTOR DE TRATAMIENTO

create or replace PROCEDURE LAROATLB_INSERTA_TRATAMIENTOS (
    p_descripcion     VARCHAR2 DEFAULT NULL,
    p_id_mascota      NUMBER DEFAULT NULL,
    p_id_veterinario  NUMBER DEFAULT NULL
)
IS
BEGIN
    LOCK TABLE LAROATLB_TRATAMIENTO IN ROW EXCLUSIVE MODE;
        INSERT INTO LAROATLB_TRATAMIENTO (
            DESCRIPCION, FECHA, ID_MASCOTA, ID_VETERINARIO
        ) VALUES (
            p_descripcion, SYSDATE, p_id_mascota, p_id_veterinario
        );


    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Error en el procedimiento: ' || SQLERRM);
END;
-----------------------------------------------------------------------------------------------
--------------------------------------
----- CURSOR TRATAMIENTO
create or replace PROCEDURE LAROATLB_LISTAR_TRATAMIENTOS (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT ID_TRATAMIENTO, DESCRIPCION, FECHA, ID_MASCOTA, ID_VETERINARIO
        FROM LAROATLB_TRATAMIENTO
        ORDER BY 1 ASC;
END;

-----------------------------------------------------------------------------------------
---- GESTOR DE CITA


create or replace PROCEDURE LAROATLB_GESTIONAR_CITAS (
    p_operacion    VARCHAR2,
    p_id_cita      NUMBER DEFAULT NULL,
    p_fecha        DATE DEFAULT NULL,
    p_sala         NUMBER DEFAULT NULL,
    p_id_mascota   NUMBER DEFAULT NULL,
    p_id_veterinario NUMBER DEFAULT NULL
)
IS
BEGIN
    LOCK TABLE LAROATLB_CITA IN ROW EXCLUSIVE MODE;
    IF UPPER(p_operacion) = 'C' THEN
        -- Inserción de nueva cita
        INSERT INTO LAROATLB_CITA (
             FECHA, SALA, ID_MASCOTA, ID_VETERINARIO
        ) VALUES (
             p_fecha, p_sala, p_id_mascota, p_id_veterinario
        );

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización de una cita existente
        UPDATE LAROATLB_CITA
        SET FECHA = p_fecha,
            SALA = p_sala,
            ID_MASCOTA = p_id_mascota,
            ID_VETERINARIO = p_id_veterinario
        WHERE ID_CITA = p_id_cita;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Eliminación de una cita
        DELETE FROM LAROATLB_CITA
        WHERE ID_CITA = p_id_cita;

    ELSE
        RAISE_APPLICATION_ERROR(-20002, 'Operación no válida. Use "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Error en el procedimiento: ' || SQLERRM);
END;
--------------------------------------------------------------------
---------CURSOR DE CITA

create or replace PROCEDURE LAROATLB_LISTAR_CITAS (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT c.ID_CITA, c.FECHA, c.SALA, m.NOMBRE AS MASCOTA, v.NOMBRE AS VETERINARIO
        FROM LAROATLB_CITA c
        JOIN LAROATLB_MASCOTA m ON c.ID_MASCOTA = m.ID_MASCOTA
        JOIN LAROATLB_VETERINARIO v ON c.ID_VETERINARIO = v.ID_VETERINARIO
        ORDER BY 1 ASC;
END;


--------------------------------------------
----------------------------------
--- GESTOR LOG LOGIN
------------------------------------------------------------------------------------------------

create or replace PROCEDURE LAROATLB_INGRESA_LOG_LOGIN(
    P_NOMBRE_USUARIO VARCHAR2)
IS
BEGIN
    INSERT INTO LAROATLB_LOG_LOGIN(ID_LOGIN,NOMBRE_INGRESO,FECHA_INGRESO)
    VALUES(SEQ_LAROATLB_LOG_LOGIN.NEXTVAL, P_NOMBRE_USUARIO,SYSDATE);
END;


--------------------------------------------
----------------------------------
--- GESTIONA EL STOCK DEL PRODUCTO USADO
create or replace TRIGGER LAROATLB_DESCONTAR_STOCK
BEFORE INSERT ON LAROATLB_DETALLE_PRODUCTO_TRATAMIENTO
FOR EACH ROW
DECLARE
    v_stock_actual NUMBER;
BEGIN
    -- Recuperar el stock actual del producto
    SELECT STOCK
    INTO v_stock_actual
    FROM LAROATLB_PRODUCTO
    WHERE ID_PRODUCTO = :NEW.ID_PRODUCTO;

    -- Verificar si la cantidad a restar es válida
    IF v_stock_actual >= :NEW.CANTIDAD THEN
        -- Actualizar el stock del producto
        UPDATE LAROATLB_PRODUCTO
        SET STOCK = STOCK - :NEW.CANTIDAD
        WHERE ID_PRODUCTO = :NEW.ID_PRODUCTO;
    ELSE
        -- Si no hay suficiente stock, lanzar un error
        RAISE_APPLICATION_ERROR(-20001, 'No hay suficiente stock para el producto ' || :NEW.ID_PRODUCTO);
    END IF;
END;
