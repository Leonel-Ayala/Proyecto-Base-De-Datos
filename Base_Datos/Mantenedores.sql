------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
-- MANTENEDOR PARA VETERINARIO

create or replace NONEDITIONABLE PROCEDURE LAROATLB_GESTIONAR_VETERINARIOS (
    p_operacion       VARCHAR2,
    p_id_veterinario  NUMBER DEFAULT NULL,
    p_nombre          VARCHAR2 DEFAULT NULL,
    p_apellido1       VARCHAR2 DEFAULT NULL,
    p_apellido2       VARCHAR2 DEFAULT NULL,
    p_especialidad    VARCHAR2 DEFAULT NULL,
    p_telefono        NUMBER DEFAULT NULL
) 
IS
    NUEVO_CORREO VARCHAR2(100);
BEGIN
    LOCK TABLE LAROATLB_VETERINARIO IN ROW EXCLUSIVE MODE;
    NUEVO_CORREO := LAROATLB_GENERA_CORREO_VETE(p_nombre, p_apellido1, p_apellido2);
    IF UPPER(p_operacion) = 'C' THEN
        -- Inserción de nuevo veterinario

        INSERT INTO LAROATLB_VETERINARIO (
            NOMBRE, APELLIDO1, APELLIDO2, ESPECIALIDAD, TELEFONO, EMAIL
        ) VALUES (
            p_nombre, p_apellido1, p_apellido2, p_especialidad, p_telefono, NUEVO_CORREO
        );
    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización de un veterinario

        UPDATE LAROATLB_VETERINARIO
        SET NOMBRE = p_nombre,
            APELLIDO1 = p_apellido1,
            APELLIDO2 = p_apellido2,
            ESPECIALIDAD = p_especialidad,
            TELEFONO = p_telefono,
            EMAIL= NUEVO_CORREO
        WHERE ID_VETERINARIO = p_id_veterinario;
    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Eliminación de veterinario
        DELETE FROM LAROATLB_VETERINARIO WHERE ID_VETERINARIO = p_id_veterinario;
    END IF;

    COMMIT;
EXCEPTION
    WHEN PROGRAM_ERROR THEN
        RAISE_APPLICATION_ERROR(-6501, 'ERROR DE PROGRAMA');
END;
------------------------
--CURSOR LISTAR VETERINARIO

CREATE OR REPLACE PROCEDURE LAROATLB_LISTAR_VETERINARIOS (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT ID_VETERINARIO, NOMBRE, APELLIDO1, APELLIDO2, ESPECIALIDAD, TELEFONO, EMAIL
        FROM LAROATLB_VETERINARIO;
END;

------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
--MANTENEDOR DE SECRETARIA
CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_SECRETARIAS (
    p_operacion       VARCHAR2,
    p_id_secretaria   NUMBER DEFAULT NULL,
    p_nombre          VARCHAR2 DEFAULT NULL,
    p_apellido1       VARCHAR2 DEFAULT NULL,
    p_apellido2       VARCHAR2 DEFAULT NULL,
    p_telefono        NUMBER DEFAULT NULL
) 
IS
    NUEVO_CORREO VARCHAR2(100);
BEGIN
    -- Bloquear la tabla para operaciones exclusivas
    LOCK TABLE LAROATLB_SECRETARIA IN ROW EXCLUSIVE MODE;
    NUEVO_CORREO := LAROATLB_GENERA_CORREO_SECRE(p_nombre, p_apellido1, p_apellido2);
    IF UPPER(p_operacion) = 'C' THEN
        -- Inserción
        
        INSERT INTO LAROATLB_SECRETARIA (
            NOMBRE, APELLIDO1, APELLIDO2, TELEFONO, EMAIL
        ) VALUES (
            p_nombre, p_apellido1, p_apellido2, p_telefono, NUEVO_CORREO
        );

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización
        
        UPDATE LAROATLB_SECRETARIA
        SET NOMBRE = p_nombre,
            APELLIDO1 = p_apellido1,
            APELLIDO2 = p_apellido2,
            TELEFONO = p_telefono,
            EMAIL = NUEVO_CORREO
        WHERE ID_SECRE = p_id_secretaria;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Eliminación
        DELETE FROM LAROATLB_SECRETARIA
        WHERE ID_SECRE = p_id_secretaria;

    ELSE
        -- Error: operación no reconocida
        RAISE_APPLICATION_ERROR(-20003, 'Operación no reconocida. Use "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;

EXCEPTION
    WHEN PROGRAM_ERROR THEN
        RAISE_APPLICATION_ERROR(-6501, 'ERROR DE PROGRAMA');
END;
------------------------
--- CURSOR PARA LISTAR SECRE

CREATE OR REPLACE PROCEDURE LAROATLB_LISTAR_SECRETARIAS (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT ID_SECRE, NOMBRE, APELLIDO1, APELLIDO2, TELEFONO, EMAIL
        FROM LAROATLB_SECRETARIA;
END;

------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------


--MANTENEDOR CLIENTE

CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_CLIENTES (
    p_operacion   VARCHAR2,
    p_id_cliente  NUMBER DEFAULT NULL,
    p_rut         NUMBER DEFAULT NULL,
    p_nombre      VARCHAR2 DEFAULT NULL,
    p_apellido1   VARCHAR2 DEFAULT NULL,
    p_apellido2   VARCHAR2 DEFAULT NULL,
    p_telefono    NUMBER DEFAULT NULL,
    p_id_calle    NUMBER DEFAULT NULL
)
IS
BEGIN
    LOCK TABLE LAROATLB_CLIENTE IN ROW EXCLUSIVE MODE;

    IF UPPER(p_operacion) = 'C' THEN
        -- Inserción de un nuevo cliente
        INSERT INTO LAROATLB_CLIENTE (
            ID_CLIENTE, RUT, NOMBRE, APELLIDO1, APELLIDO2, TELEFONO, ID_CALLE
        ) VALUES (
            p_id_cliente, p_rut, p_nombre, p_apellido1, p_apellido2, p_telefono, p_id_calle
        );
    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización de un cliente existente
        UPDATE LAROATLB_CLIENTE
        SET RUT = p_rut,
            NOMBRE = p_nombre,
            APELLIDO1 = p_apellido1,
            APELLIDO2 = p_apellido2,
            TELEFONO = p_telefono,
            ID_CALLE = p_id_calle
        WHERE ID_CLIENTE = p_id_cliente;
    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Eliminación de un cliente
        DELETE FROM LAROATLB_CLIENTE 
        WHERE ID_CLIENTE = p_id_cliente;
    ELSE
        RAISE_APPLICATION_ERROR(-20001, 'Operación no válida. Use "C", "U" o "D".');
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20002, 'Error al gestionar cliente: ' || SQLERRM);
END;


-------------------------------------------
--CURSOR DE CLIENTE

CREATE OR REPLACE PROCEDURE LAROATLB_LISTAR_CLIENTES (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT 
            CA.ID_CLIENTE,
            CA.RUT,
            CA.NOMBRE,
            CA.APELLIDO1,
            CA.APELLIDO2,
            CA.TELEFONO,
            CALLE.NOMBRE_CALLE || ',' || CALLE.NUMERO_CALLE||','||COM.NOMBRE_COMUNA||','||RE.NOMBRE_REGION  AS DIRECCION
        FROM 
            LAROATLB_CLIENTE CA
        JOIN 
            LAROATLB_CALLE_CLIENTE CALLE ON (CA.ID_CALLE = CALLE.ID_CALLE)
        JOIN 
            LAROATLB_COMUNA_CLIENTE COM ON CALLE.ID_COMUNA = COM.ID_COMUNA
        JOIN
            LAROATLB_REGION_CLIENTE RE ON COM.ID_REGION = RE.ID_REGION;
END;


------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------

--MANTENEDOR DE REGION

CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_REGIONES (
    p_operacion     VARCHAR2,
    p_id_region     NUMBER DEFAULT NULL,
    p_nombre_region VARCHAR2 DEFAULT NULL
)
IS
BEGIN
    LOCK TABLE LAROATLB_REGION_CLIENTE IN ROW EXCLUSIVE MODE;
    IF UPPER(p_operacion) = 'C' THEN
        -- Inserción de una nueva región
        INSERT INTO LAROATLB_REGION_CLIENTE (
            ID_REGION, NOMBRE_REGION
        ) VALUES (
            p_id_region, p_nombre_region
        );

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización de una región existente
        UPDATE LAROATLB_REGION_CLIENTE
        SET NOMBRE_REGION = p_nombre_region
        WHERE ID_REGION = p_id_region;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Eliminación de una región
        DELETE FROM LAROATLB_REGION_CLIENTE
        WHERE ID_REGION = p_id_region;

    ELSE
        RAISE_APPLICATION_ERROR(-20002, 'Operación no válida. Use "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Error en el procedimiento: ' || SQLERRM);
END;
--------------------------------------------}
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

------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
-- MANTENEDOR DE COMUNA
CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_COMUNAS (
    p_operacion     VARCHAR2,
    p_id_comuna     NUMBER DEFAULT NULL,
    p_nombre_comuna VARCHAR2 DEFAULT NULL,
    p_id_region     NUMBER DEFAULT NULL
)
IS
BEGIN
    LOCK TABLE LAROATLB_COMUNA_CLIENTE IN ROW EXCLUSIVE MODE;
    IF UPPER(p_operacion) = 'C' THEN
        -- Inserción de una nueva comuna
        INSERT INTO LAROATLB_COMUNA_CLIENTE (
            ID_COMUNA, NOMBRE_COMUNA, ID_REGION
        ) VALUES (
            p_id_comuna, p_nombre_comuna, p_id_region
        );

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización de una comuna existente
        UPDATE LAROATLB_COMUNA_CLIENTE
        SET NOMBRE_COMUNA = p_nombre_comuna,
            ID_REGION = p_id_region
        WHERE ID_COMUNA = p_id_comuna;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Eliminación de una comuna
        DELETE FROM LAROATLB_COMUNA_CLIENTE
        WHERE ID_COMUNA = p_id_comuna;

    ELSE
        RAISE_APPLICATION_ERROR(-20002, 'Operación no válida. Use "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Error en el procedimiento: ' || SQLERRM);
END;
----------------------------------------------------------------------------------
--- CURSOR DE COMUNA
CREATE OR REPLACE PROCEDURE LAROATLB_LISTAR_COMUNAS (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT ID_COMUNA, NOMBRE_COMUNA, ID_REGION
        FROM LAROATLB_COMUNA_CLIENTE;
END;


------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
-- MANTENEDOR DE CALLES CLIENTE
CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_CALLES (
    p_operacion     VARCHAR2,
    p_id_calle      NUMBER DEFAULT NULL,
    p_nombre_calle  VARCHAR2 DEFAULT NULL,
    p_numero_calle  NUMBER DEFAULT NULL,
    p_id_comuna     NUMBER DEFAULT NULL
)
IS
BEGIN
    LOCK TABLE LAROATLB_CALLE_CLIENTE IN ROW EXCLUSIVE MODE;
    IF UPPER(p_operacion) = 'C' THEN
        -- Inserción de una nueva calle
        INSERT INTO LAROATLB_CALLE_CLIENTE (
            ID_CALLE, NOMBRE_CALLE, NUMERO_CALLE, ID_COMUNA
        ) VALUES (
            p_id_calle, p_nombre_calle, p_numero_calle, p_id_comuna
        );

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización de una calle existente
        UPDATE LAROATLB_CALLE_CLIENTE
        SET NOMBRE_CALLE = p_nombre_calle,
            NUMERO_CALLE = p_numero_calle,
            ID_COMUNA = p_id_comuna
        WHERE ID_CALLE = p_id_calle;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Eliminación de una calle
        DELETE FROM LAROATLB_CALLE_CLIENTE
        WHERE ID_CALLE = p_id_calle;

    ELSE
        RAISE_APPLICATION_ERROR(-20002, 'Operación no válida. Use "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Error en el procedimiento: ' || SQLERRM);
END;
---------------------------------------------------------------------------------
---------CURSOR DER CALLE
CREATE OR REPLACE PROCEDURE LAROATLB_LISTAR_CALLES (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT ID_CALLE, NOMBRE_CALLE, NUMERO_CALLE, ID_COMUNA
        FROM LAROATLB_CALLE_CLIENTE;
END;


------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
--MANTENEDOR DE RAZA

CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_RAZAS (
    p_operacion    VARCHAR2,
    p_id_raza      NUMBER DEFAULT NULL,
    p_nombre_raza  VARCHAR2 DEFAULT NULL,
    p_id_especie   NUMBER DEFAULT NULL
)
IS
BEGIN
    LOCK TABLE LAROATLB_RAZA IN ROW EXCLUSIVE MODE;
    IF UPPER(p_operacion) = 'C' THEN
        -- Inserción de una nueva raza
        INSERT INTO LAROATLB_RAZA (
            ID_RAZA, NOMBRE_RAZA, ID_ESPECIE
        ) VALUES (
            p_id_raza, p_nombre_raza, p_id_especie
        );

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización de una raza existente
        UPDATE LAROATLB_RAZA
        SET NOMBRE_RAZA = p_nombre_raza,
            ID_ESPECIE = p_id_especie
        WHERE ID_RAZA = p_id_raza;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Eliminación de una raza
        DELETE FROM LAROATLB_RAZA
        WHERE ID_RAZA = p_id_raza;

    ELSE
        RAISE_APPLICATION_ERROR(-20002, 'Operación no válida. Use "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Error en el procedimiento: ' || SQLERRM);
END;

------------------------------------------
----------CURSOR DE RAZA
CREATE OR REPLACE PROCEDURE LAROATLB_LISTAR_RAZAS (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT r.ID_RAZA, r.NOMBRE_RAZA, e.NOMBRE_ESPECIE
        FROM LAROATLB_RAZA r
        JOIN LAROATLB_ESPECIE e ON r.ID_ESPECIE = e.ID_ESPECIE;
END;

------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
--MANTENEDOR DE ESPECIE

CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_ESPECIES (
    p_operacion    VARCHAR2,
    p_id_especie   NUMBER DEFAULT NULL,
    p_nombre_especie VARCHAR2 DEFAULT NULL
)
IS
BEGIN
    LOCK TABLE LAROATLB_ESPECIE IN ROW EXCLUSIVE MODE;
    IF UPPER(p_operacion) = 'C' THEN
        -- Inserción de una nueva especie
        INSERT INTO LAROATLB_ESPECIE (
            ID_ESPECIE, NOMBRE_ESPECIE
        ) VALUES (
            p_id_especie, p_nombre_especie
        );

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización de una especie existente
        UPDATE LAROATLB_ESPECIE
        SET NOMBRE_ESPECIE = p_nombre_especie
        WHERE ID_ESPECIE = p_id_especie;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Eliminación de una especie
        DELETE FROM LAROATLB_ESPECIE
        WHERE ID_ESPECIE = p_id_especie;

    ELSE
        RAISE_APPLICATION_ERROR(-20002, 'Operación no válida. Use "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Error en el procedimiento: ' || SQLERRM);
END;

----------------------------------------------------------
---CURSOR DE ESPECIE
CREATE OR REPLACE PROCEDURE LAROATLB_LISTAR_ESPECIES (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT ID_ESPECIE, NOMBRE_ESPECIE
        FROM LAROATLB_ESPECIE;
END;

------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
--MANTENEDOR DE MASCOTA
CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_MASCOTAS (
    p_operacion    VARCHAR2,
    p_id_mascota   NUMBER DEFAULT NULL,
    p_nombre       VARCHAR2 DEFAULT NULL,
    p_edad         NUMBER DEFAULT NULL,
    p_id_cliente   NUMBER DEFAULT NULL,
    p_id_raza      NUMBER DEFAULT NULL
)
IS
BEGIN
    LOCK TABLE LAROATLB_MASCOTA IN ROW EXCLUSIVE MODE;
    IF UPPER(p_operacion) = 'C' THEN
        -- Inserción de una nueva mascota
        INSERT INTO LAROATLB_MASCOTA (
            ID_MASCOTA, NOMBRE, EDAD, ID_CLIENTE, ID_RAZA
        ) VALUES (
            p_id_mascota, p_nombre, p_edad, p_id_cliente, p_id_raza
        );

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización de una mascota existente
        UPDATE LAROATLB_MASCOTA
        SET NOMBRE = p_nombre,
            EDAD = p_edad,
            ID_CLIENTE = p_id_cliente,
            ID_RAZA = p_id_raza
        WHERE ID_MASCOTA = p_id_mascota;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Eliminación de una mascota
        DELETE FROM LAROATLB_MASCOTA
        WHERE ID_MASCOTA = p_id_mascota;

    ELSE
        RAISE_APPLICATION_ERROR(-20002, 'Operación no válida. Use "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Error en el procedimiento: ' || SQLERRM);
END;
-------------------------------------------------------
--------CURSOR DE MASCOTA
CREATE OR REPLACE PROCEDURE LAROATLB_LISTAR_MASCOTAS (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT m.ID_MASCOTA, m.NOMBRE, m.EDAD, c.NOMBRE AS CLIENTE, r.NOMBRE_RAZA
        FROM LAROATLB_MASCOTA m
        JOIN LAROATLB_CLIENTE c ON m.ID_CLIENTE = c.ID_CLIENTE
        JOIN LAROATLB_RAZA r ON m.ID_RAZA = r.ID_RAZA;
END;


------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
--MANTENEDOR DE CITA

CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_CITAS (
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
            ID_CITA, FECHA, SALA, ID_MASCOTA, ID_VETERINARIO
        ) VALUES (
            p_id_cita, p_fecha, p_sala, p_id_mascota, p_id_veterinario
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

CREATE OR REPLACE PROCEDURE LAROATLB_LISTAR_CITAS (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT c.ID_CITA, c.FECHA, c.SALA, m.NOMBRE AS MASCOTA, v.NOMBRE AS VETERINARIO
        FROM LAROATLB_CITA c
        JOIN LAROATLB_MASCOTA m ON c.ID_MASCOTA = m.ID_MASCOTA
        JOIN LAROATLB_VETERINARIO v ON c.ID_VETERINARIO = v.ID_VETERINARIO;
END;



------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
--MANTENEDOR DE TRATAMIENTOS

CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_TRATAMIENTOS (
    p_operacion       VARCHAR2,
    p_id_tratamiento  NUMBER DEFAULT NULL,
    p_descripcion     VARCHAR2 DEFAULT NULL,
    p_fecha           DATE DEFAULT NULL,
    p_id_mascota      NUMBER DEFAULT NULL,
    p_id_veterinario  NUMBER DEFAULT NULL
)
IS
BEGIN
    LOCK TABLE LAROATLB_TRATAMIENTO IN ROW EXCLUSIVE MODE;
    IF UPPER(p_operacion) = 'C' THEN
        -- Inserción de nuevo tratamiento
        INSERT INTO LAROATLB_TRATAMIENTO (
            ID_TRATAMIENTO, DESCRIPCION, FECHA, ID_MASCOTA, ID_VETERINARIO
        ) VALUES (
            p_id_tratamiento, p_descripcion, p_fecha, p_id_mascota, p_id_veterinario
        );

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización de un tratamiento
        UPDATE LAROATLB_TRATAMIENTO
        SET DESCRIPCION = p_descripcion,
            FECHA = p_fecha,
            ID_MASCOTA = p_id_mascota,
            ID_VETERINARIO = p_id_veterinario
        WHERE ID_TRATAMIENTO = p_id_tratamiento;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Eliminación de tratamiento
        DELETE FROM LAROATLB_TRATAMIENTO
        WHERE ID_TRATAMIENTO = p_id_tratamiento;

    ELSE
        RAISE_APPLICATION_ERROR(-20002, 'Operación no válida. Use "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Error en el procedimiento: ' || SQLERRM);
END;
--------------------------------------
----- CURSOR TRATAMIENTO
CREATE OR REPLACE PROCEDURE LAROATLB_LISTAR_TRATAMIENTOS (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT ID_TRATAMIENTO, DESCRIPCION, FECHA, ID_MASCOTA, ID_VETERINARIO
        FROM LAROATLB_TRATAMIENTO;
END;



-------------------------------------------------------------------------------------------
----------------------------------------------------------------------------------------
--MANTENEDOR DE PRODUCTOS

create or replace PROCEDURE LAROATLB_GESTIONAR_PRODUCTOS (
    p_operacion       VARCHAR2,
    p_id_producto     NUMBER DEFAULT NULL,
    p_nombre_producto VARCHAR2 DEFAULT NULL,
    p_stock           NUMBER DEFAULT NULL
)
IS
BEGIN
    LOCK TABLE LAROATLB_PRODUCTO IN ROW EXCLUSIVE MODE;
    IF UPPER(p_operacion) = 'C' THEN
        -- Inserción de nuevo producto
        INSERT INTO LAROATLB_PRODUCTO (
            ID_PRODUCTO, NOMBRE_PRODUCTO, STOCK
        ) VALUES (
            p_id_producto, p_nombre_producto, p_stock
        );

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización de un producto
        UPDATE LAROATLB_PRODUCTO
        SET STOCK = p_stock
        WHERE ID_PRODUCTO = p_id_producto;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Eliminación de producto
        DELETE FROM LAROATLB_PRODUCTO
        WHERE ID_PRODUCTO = p_id_producto;

    ELSE
        RAISE_APPLICATION_ERROR(-20002, 'Operación no válida. Use "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Error en el procedimiento: ' || SQLERRM);
END;



-- CURSOR DE PRODUCTOS
CREATE OR REPLACE PROCEDURE LAROATLB_LISTAR_PRODUCTOS (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT ID_PRODUCTO, NOMBRE_PRODUCTO, STOCK
        FROM LAROATLB_PRODUCTO;
END;

---------------------------------------------------------------------------------
----------------------------------------------------------------------------------
-- MANTENEDOR DE DETALLE PRODUCTO TRATAMIENTO

CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_DETALLE_PRODUCTO_TRATAMIENTO (
    p_operacion       VARCHAR2,
    p_id_tratamiento  NUMBER DEFAULT NULL,
    p_id_producto     NUMBER DEFAULT NULL,
    p_cantidad        NUMBER DEFAULT NULL
)
IS
BEGIN
    LOCK TABLE LAROATLB_DETALLE_PRODUCTO_TRATAMIENTO IN ROW EXCLUSIVE MODE;
    IF UPPER(p_operacion) = 'C' THEN
        -- Inserción de un nuevo detalle de producto para un tratamiento
        INSERT INTO LAROATLB_DETALLE_PRODUCTO_TRATAMIENTO (
            ID_TRATAMIENTO, ID_PRODUCTO, CANTIDAD
        ) VALUES (
            p_id_tratamiento, p_id_producto, p_cantidad
        );

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización de un detalle de producto para un tratamiento
        UPDATE LAROATLB_DETALLE_PRODUCTO_TRATAMIENTO
        SET CANTIDAD = p_cantidad
        WHERE ID_TRATAMIENTO = p_id_tratamiento
          AND ID_PRODUCTO = p_id_producto;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Eliminación de un detalle de producto para un tratamiento
        DELETE FROM LAROATLB_DETALLE_PRODUCTO_TRATAMIENTO
        WHERE ID_TRATAMIENTO = p_id_tratamiento
          AND ID_PRODUCTO = p_id_producto;

    ELSE
        RAISE_APPLICATION_ERROR(-20002, 'Operación no válida. Use "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Error en el procedimiento: ' || SQLERRM);
END;
--------------------------------------------------
------CURSOR DETALLE

CREATE OR REPLACE PROCEDURE LAROATLB_LISTAR_DETALLE_PRODUCTO_TRATAMIENTO (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT ID_TRATAMIENTO, ID_PRODUCTO, CANTIDAD
        FROM LAROATLB_DETALLE_PRODUCTO_TRATAMIENTO;
END;

---------------------------------------------------------------------------------
----------------------------------------------------------------------------------
-- MANTENEDOR DE LOG LOGIN

CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_LOG_LOGIN (
    p_operacion       VARCHAR2,
    p_id_login        NUMBER DEFAULT NULL,
    p_nombre_ingreso  VARCHAR2 DEFAULT NULL,
    p_fecha_ingreso   DATE DEFAULT NULL
)
IS
BEGIN
    LOCK TABLE LAROATLB_LOG_LOGIN IN ROW EXCLUSIVE MODE;
    IF UPPER(p_operacion) = 'C' THEN
        -- Inserción de un nuevo registro en el log de login
        INSERT INTO LAROATLB_LOG_LOGIN (
            NOMBRE_INGRESO, FECHA_INGRESO
        ) VALUES (
            p_nombre_ingreso, p_fecha_ingreso
        );

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización de un registro existente en el log de login
        UPDATE LAROATLB_LOG_LOGIN
        SET NOMBRE_INGRESO = p_nombre_ingreso,
            FECHA_INGRESO = p_fecha_ingreso
        WHERE ID_LOGIN = p_id_login;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Eliminación de un registro del log de login
        DELETE FROM LAROATLB_LOG_LOGIN
        WHERE ID_LOGIN = p_id_login;

    ELSE
        RAISE_APPLICATION_ERROR(-20002, 'Operación no válida. Use "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Error en el procedimiento: ' || SQLERRM);
END;

---------------------------------------------------------
--------CURSOR LOG LOGIN

CREATE OR REPLACE PROCEDURE LAROATLB_LISTAR_LOG_LOGIN (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT ID_LOGIN, NOMBRE_INGRESO, FECHA_INGRESO
        FROM LAROATLB_LOG_LOGIN;
END;
---------------------------------------------------------------------------------
----------------------------------------------------------------------------------
-- MANTENEDOR DE USUARIOS
CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_USUARIOS (
    p_operacion      VARCHAR2,
    p_id_usuario     NUMBER DEFAULT NULL,
    p_nombre_usuario VARCHAR2 DEFAULT NULL,
    p_rol_usuario    NUMBER DEFAULT NULL,
    p_contra_usuario VARCHAR2 DEFAULT NULL
)
IS
BEGIN
    LOCK TABLE LAROATLB_USUARIOS IN ROW EXCLUSIVE MODE;
    IF UPPER(p_operacion) = 'C' THEN
        -- Inserción de un nuevo usuario
        INSERT INTO LAROATLB_USUARIOS (
            ID_USUARIO, NOMBRE_USUARIO, ROL_USUARIO, CONTRA_USUARIO
        ) VALUES (
            p_id_usuario, p_nombre_usuario, p_rol_usuario, p_contra_usuario
        );

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización de un usuario existente
        UPDATE LAROATLB_USUARIOS
        SET NOMBRE_USUARIO = p_nombre_usuario,
            ROL_USUARIO = p_rol_usuario,
            CONTRA_USUARIO = p_contra_usuario
        WHERE ID_USUARIO = p_id_usuario;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Eliminación de un usuario
        DELETE FROM LAROATLB_USUARIOS
        WHERE ID_USUARIO = p_id_usuario;

    ELSE
        RAISE_APPLICATION_ERROR(-20002, 'Operación no válida. Use "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Error en el procedimiento: ' || SQLERRM);
END;

---------------------
--CURSOR USUARIOS

CREATE OR REPLACE PROCEDURE LAROATLB_LISTAR_USUARIOS (
    p_cursor OUT SYS_REFCURSOR
)
IS
BEGIN
    OPEN p_cursor FOR
        SELECT ID_USUARIO, NOMBRE_USUARIO, ROL_USUARIO, CONTRA_USUARIO
        FROM LAROATLB_USUARIOS;
END;

