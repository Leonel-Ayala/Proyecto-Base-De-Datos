------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
-- MANTENEDOR PARA VETERINARIO

create or replace  PROCEDURE LAROATLB_GESTIONAR_VETERINARIOS (
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
create or replace PROCEDURE LAROATLB_GESTIONAR_SECRETARIAS (
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

create or replace  PROCEDURE LAROATLB_GESTIONAR_CLIENTES (
    p_operacion   VARCHAR2,
    p_id_cliente  NUMBER DEFAULT NULL,
    p_rut         NUMBER DEFAULT NULL,
    p_nombre      VARCHAR2 DEFAULT NULL,
    p_apellido1   VARCHAR2 DEFAULT NULL,
    p_apellido2   VARCHAR2 DEFAULT NULL,
    p_telefono    NUMBER DEFAULT NULL,
    p_nombre_calle    VARCHAR2 DEFAULT NULL,
    p_numero_casa    NUMBER DEFAULT NULL
)
IS
    p_id_calle NUMBER := 0;
BEGIN
    LOCK TABLE LAROATLB_CLIENTE IN ROW EXCLUSIVE MODE;

    -- Ajuste: Obtener la primera fila encontrada explícitamente

    IF UPPER(p_operacion) = 'C' THEN
        -- Inserción de un nuevo cliente
        SELECT ID_CALLE 
        INTO p_id_calle
        FROM LAROATLB_CALLE_CLIENTE
        WHERE NOMBRE_CALLE = p_nombre_calle 
          AND NUMERO_CASA = p_numero_casa
        FETCH FIRST 1 ROWS ONLY; -- Devuelve solo la primera fila
        INSERT INTO LAROATLB_CLIENTE (
            RUT, NOMBRE, APELLIDO1, APELLIDO2, TELEFONO, ID_CALLE
        ) VALUES (
            p_rut, p_nombre, p_apellido1, p_apellido2, p_telefono, p_id_calle
        );
    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización de un cliente existente
        UPDATE LAROATLB_CLIENTE
        SET RUT = p_rut,
            NOMBRE = p_nombre,
            APELLIDO1 = p_apellido1,
            APELLIDO2 = p_apellido2,
            TELEFONO = p_telefono

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

create or replace  PROCEDURE LAROATLB_GESTIONAR_REGIONES (
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
             NOMBRE_REGION
        ) VALUES (
             p_nombre_region
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
create or replace  PROCEDURE LAROATLB_GESTIONAR_COMUNAS (
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
            NOMBRE_COMUNA, ID_REGION
        ) VALUES (
            p_nombre_comuna, p_id_region
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
create or replace  PROCEDURE LAROATLB_GESTIONAR_CALLES (
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
             NOMBRE_CALLE, NUMERO_CASA, ID_COMUNA
        ) VALUES (
            p_nombre_calle, p_numero_calle, p_id_comuna
        );

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización de una calle existente
        UPDATE LAROATLB_CALLE_CLIENTE
        SET NOMBRE_CALLE = p_nombre_calle,
            NUMERO_CASA = p_numero_calle,
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

create or replace  PROCEDURE LAROATLB_GESTIONAR_RAZAS (
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
             NOMBRE_RAZA, ID_ESPECIE
        ) VALUES (
             p_nombre_raza, p_id_especie
        );

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización de una raza existente
        UPDATE LAROATLB_RAZA
        SET NOMBRE_RAZA = p_nombre_raza
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
            NOMBRE_ESPECIE
        ) VALUES (
            p_nombre_especie
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
create or replace  PROCEDURE LAROATLB_GESTIONAR_MASCOTAS (
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
             NOMBRE, EDAD, ID_CLIENTE, ID_RAZA
        ) VALUES (
             p_nombre, p_edad, p_id_cliente, p_id_raza
        );

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Actualización de una mascota existente
        UPDATE LAROATLB_MASCOTA
        SET NOMBRE = p_nombre,
            EDAD = p_edad
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




----------------------------------------------------------------------------------------
--MANTENEDOR DE PRODUCTOS

create or replace  PROCEDURE LAROATLB_GESTIONAR_PRODUCTOS (
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
            NOMBRE_PRODUCTO, STOCK
        ) VALUES (
            p_nombre_producto, p_stock
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
-- MANTENEDOR DE USUARIOS
create or replace  PROCEDURE LAROATLB_GESTIONAR_USUARIOS (
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
             NOMBRE_USUARIO, ROL_USUARIO, CONTRA_USUARIO
        ) VALUES (
             p_nombre_usuario, p_rol_usuario, p_contra_usuario
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

