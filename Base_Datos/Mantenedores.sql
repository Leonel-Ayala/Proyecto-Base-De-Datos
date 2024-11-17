------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
-- MANTENEDOR PARA VETERINARIO
CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_VETERINARIOS (
    p_operacion  VARCHAR2,
    p_id_veterinario  NUMBER DEFAULT NULL,
    p_nombre VARCHAR2 DEFAULT NULL,
    p_apellido1  VARCHAR2 DEFAULT NULL,
    p_apellido2  VARCHAR2 DEFAULT NULL,
    p_especialidad  VARCHAR2 DEFAULT NULL,
    p_telefono  NUMBER DEFAULT NULL
) 
IS
    NUEVO_CORREO VARCHAR2(100);

    -- Cursor para verificar si un veterinario existe
    CURSOR c_veterinario (id_vet NUMBER) IS
        SELECT ID_VETERINARIO
        FROM LAROATLB_VETERINARIO
        WHERE ID_VETERINARIO = id_vet;

    -- Cursor para mostrar todos los veterinarios (modificado para su uso en PHP)
    CURSOR c_veterinarios_all IS
        SELECT ID_VETERINARIO, NOMBRE, APELLIDO1, APELLIDO2, ESPECIALIDAD, TELEFONO, EMAIL
        FROM LAROATLB_VETERINARIO;

    v_existente c_veterinario%ROWTYPE; -- Variable para manejar datos del cursor
BEGIN
    LOCK TABLE LAROATLB_VETERINARIO IN ROW EXCLUSIVE MODE;
    NUEVO_CORREO := LAROATLB_GENERA_CORREO_VETE(p_nombre, p_apellido1, p_apellido2);

    IF UPPER(p_operacion) = 'R' THEN
        -- Leer todos los registros y retornar resultados en formato adecuado para PHP
        FOR v_row IN c_veterinarios_all LOOP
            -- Aquí se puede utilizar un método para almacenar los resultados en una tabla de PHP o JSON
            -- Los siguientes PRINT están en formato adecuado para ser interpretados por PHP
            DBMS_OUTPUT.PUT_LINE(v_row.ID_VETERINARIO || '|' || 
                                 v_row.NOMBRE || ' ' || v_row.APELLIDO1 || ' ' || v_row.APELLIDO2 || '|' || 
                                 v_row.ESPECIALIDAD || '|' || v_row.TELEFONO || '|' || v_row.EMAIL);
        END LOOP;

    ELSIF UPPER(p_operacion) = 'C' THEN
        -- Inserción
        INSERT INTO LAROATLB_VETERINARIO (
            NOMBRE, APELLIDO1, APELLIDO2, ESPECIALIDAD, TELEFONO, EMAIL
        ) VALUES (
            p_nombre, p_apellido1, p_apellido2, p_especialidad, p_telefono, NUEVO_CORREO
        );
        DBMS_OUTPUT.PUT_LINE('Veterinario insertado correctamente.');

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Verificar existencia
        OPEN c_veterinario(p_id_veterinario);
        FETCH c_veterinario INTO v_existente;
        IF c_veterinario%FOUND THEN
            -- Actualización
            UPDATE LAROATLB_VETERINARIO
            SET NOMBRE = p_nombre,
                APELLIDO1 = p_apellido1,
                APELLIDO2 = p_apellido2,
                ESPECIALIDAD = p_especialidad,
                TELEFONO = p_telefono,
                EMAIL = NUEVO_CORREO
            WHERE ID_VETERINARIO = p_id_veterinario;
            DBMS_OUTPUT.PUT_LINE('Veterinario actualizado correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró el veterinario con el ID proporcionado.');
        END IF;
        CLOSE c_veterinario;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Verificar existencia
        OPEN c_veterinario(p_id_veterinario);
        FETCH c_veterinario INTO v_existente;
        IF c_veterinario%FOUND THEN
            -- Eliminación
            DELETE FROM LAROATLB_VETERINARIO
            WHERE ID_VETERINARIO = p_id_veterinario;
            DBMS_OUTPUT.PUT_LINE('Veterinario eliminado correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró el veterinario con el ID proporcionado.');
        END IF;
        CLOSE c_veterinario;

    ELSE
        DBMS_OUTPUT.PUT_LINE('Operación no reconocida. Use "R", "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción (en caso de no estar en modo automático)
    COMMIT;
EXCEPTION
    WHEN PROGRAM_ERROR THEN
        RAISE_APPLICATION_ERROR(-6501, 'ERROR DE PROGRAMA');
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

    -- Cursor para verificar si una secretaria existe
    CURSOR c_secretaria (id_sec NUMBER) IS
        SELECT ID_SECRE
        FROM LAROATLB_SECRETARIA
        WHERE ID_SECRE = id_sec;

    -- Cursor para mostrar todas las secretarias
    CURSOR c_secretarias_all IS
        SELECT ID_SECRE, NOMBRE, APELLIDO1, APELLIDO2, TELEFONO, EMAIL
        FROM LAROATLB_SECRETARIA;

    v_existente c_secretaria%ROWTYPE; -- Variable para manejar datos del cursor
BEGIN
    LOCK TABLE LAROATLB_SECRETARIA IN ROW EXCLUSIVE MODE;
    NUEVO_CORREO := LAROATLB_GENERA_CORREO_SECRE(p_nombre, p_apellido1, p_apellido2);

    IF UPPER(p_operacion) = 'R' THEN
        -- Leer todos los registros
        DBMS_OUTPUT.PUT_LINE('--- LISTADO DE SECRETARIAS ---');
        FOR v_row IN c_secretarias_all LOOP
            DBMS_OUTPUT.PUT_LINE('ID: ' || v_row.ID_SECRE || 
                                 ', Nombre: ' || v_row.NOMBRE || ' ' || v_row.APELLIDO1 || ' ' || v_row.APELLIDO2 || 
                                 ', Teléfono: ' || v_row.TELEFONO || 
                                 ', Email: ' || v_row.EMAIL);
        END LOOP;

    ELSIF UPPER(p_operacion) = 'C' THEN
        -- Inserción
        INSERT INTO LAROATLB_SECRETARIA (
            NOMBRE, APELLIDO1, APELLIDO2, TELEFONO, EMAIL
        ) VALUES (
            p_nombre, p_apellido1, p_apellido2, p_telefono, NUEVO_CORREO
        );
        DBMS_OUTPUT.PUT_LINE('Secretaria insertada correctamente.');

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Verificar existencia
        OPEN c_secretaria(p_id_secretaria);
        FETCH c_secretaria INTO v_existente;
        IF c_secretaria%FOUND THEN
            -- Actualización
            UPDATE LAROATLB_SECRETARIA
            SET NOMBRE = p_nombre,
                APELLIDO1 = p_apellido1,
                APELLIDO2 = p_apellido2,
                TELEFONO = p_telefono,
                EMAIL = NUEVO_CORREO
            WHERE ID_SECRE = p_id_secretaria;
            DBMS_OUTPUT.PUT_LINE('Secretaria actualizada correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró la secretaria con el ID proporcionado.');
        END IF;
        CLOSE c_secretaria;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Verificar existencia
        OPEN c_secretaria(p_id_secretaria);
        FETCH c_secretaria INTO v_existente;
        IF c_secretaria%FOUND THEN
            -- Eliminación
            DELETE FROM LAROATLB_SECRETARIA
            WHERE ID_SECRE = p_id_secretaria;
            DBMS_OUTPUT.PUT_LINE('Secretaria eliminada correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró la secretaria con el ID proporcionado.');
        END IF;
        CLOSE c_secretaria;

    ELSE
        DBMS_OUTPUT.PUT_LINE('Operación no reconocida. Use "R", "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción (en caso de no estar en modo automático)
    COMMIT;
EXCEPTION
    
    WHEN PROGRAM_ERROR THEN
        RAISE_APPLICATION_ERROR(-6501, 'ERROR DE PROGRAMA');
END;

------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------


--MANTENEDOR CLIENTE
CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_CLIENTES (
    p_operacion  VARCHAR2,
    p_id_cliente NUMBER DEFAULT NULL,
    p_nombre VARCHAR2 DEFAULT NULL,
    p_apellido1 VARCHAR2 DEFAULT NULL,
    p_apellido2 VARCHAR2 DEFAULT NULL,
    p_id_region NUMBER DEFAULT NULL,
    p_telefono NUMBER DEFAULT NULL
)
IS
    -- Cursor para verificar si un cliente existe
    CURSOR c_cliente (id_cli NUMBER) IS
        SELECT ID_Cliente
        FROM LAROATLB_Cliente
        WHERE ID_Cliente = id_cli;

    -- Cursor para mostrar todos los clientes con el nombre de la región
    CURSOR c_clientes_all IS
        SELECT c.ID_Cliente, c.Nombre, c.Apellido1, c.Apellido2, 
               r.Nombre_Region, c.Telefono
        FROM LAROATLB_Cliente c
        JOIN LAROATLB_Region_Cliente r
        ON c.ID_Region = r.ID_Region;

    v_existente c_cliente%ROWTYPE; -- Variable para manejar datos del cursor
BEGIN
    LOCK TABLE LAROATLB_Cliente IN ROW EXCLUSIVE MODE;

    IF UPPER(p_operacion) = 'R' THEN
        -- Leer todos los registros
        DBMS_OUTPUT.PUT_LINE('--- LISTADO DE CLIENTES ---');
        FOR v_row IN c_clientes_all LOOP
            DBMS_OUTPUT.PUT_LINE('ID: ' || v_row.ID_Cliente || 
                                 ', Nombre: ' || v_row.Nombre || ' ' || v_row.Apellido1 || ' ' || v_row.Apellido2 || 
                                 ', Región: ' || v_row.Nombre_Region || 
                                 ', Teléfono: ' || v_row.Telefono);
        END LOOP;

    ELSIF UPPER(p_operacion) = 'C' THEN
        -- Inserción
        INSERT INTO LAROATLB_Cliente (
            Nombre, Apellido1, Apellido2, ID_Region, Telefono
        ) VALUES (
            p_nombre, p_apellido1, p_apellido2, p_id_region, p_telefono
        );
        DBMS_OUTPUT.PUT_LINE('Cliente insertado correctamente.');

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Verificar existencia
        OPEN c_cliente(p_id_cliente);
        FETCH c_cliente INTO v_existente;
        IF c_cliente%FOUND THEN
            -- Actualización
            UPDATE LAROATLB_Cliente
            SET Nombre = p_nombre,
                Apellido1 = p_apellido1,
                Apellido2 = p_apellido2,
                ID_Region = p_id_region,
                Telefono = p_telefono
            WHERE ID_Cliente = p_id_cliente;
            DBMS_OUTPUT.PUT_LINE('Cliente actualizado correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró el cliente con el ID proporcionado.');
        END IF;
        CLOSE c_cliente;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Verificar existencia
        OPEN c_cliente(p_id_cliente);
        FETCH c_cliente INTO v_existente;
        IF c_cliente%FOUND THEN
            -- Eliminación
            DELETE FROM LAROATLB_Cliente
            WHERE ID_Cliente = p_id_cliente;
            DBMS_OUTPUT.PUT_LINE('Cliente eliminado correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró el cliente con el ID proporcionado.');
        END IF;
        CLOSE c_cliente;

    ELSE
        DBMS_OUTPUT.PUT_LINE('Operación no reconocida. Use "R", "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Ocurrió un error: ' || SQLERRM);
END;


------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------

--MANTENEDOR DE REGION

CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_REGIONES (
    p_operacion      VARCHAR2,
    p_id_region      NUMBER DEFAULT NULL,
    p_nombre_region  VARCHAR2 DEFAULT NULL
)
IS
    -- Cursor para verificar si una región existe
    CURSOR c_region (id_reg NUMBER) IS
        SELECT ID_Region
        FROM LAROATLB_Region_Cliente
        WHERE ID_Region = id_reg;

    -- Cursor para mostrar todas las regiones
    CURSOR c_regiones_all IS
        SELECT ID_Region, Nombre_Region
        FROM LAROATLB_Region_Cliente;

    v_existente c_region%ROWTYPE; -- Variable para manejar datos del cursor
BEGIN
    LOCK TABLE LAROATLB_Region_Cliente IN ROW EXCLUSIVE MODE;

    IF UPPER(p_operacion) = 'R' THEN
        -- Leer todos los registros
        DBMS_OUTPUT.PUT_LINE('--- LISTADO DE REGIONES ---');
        FOR v_row IN c_regiones_all LOOP
            DBMS_OUTPUT.PUT_LINE('ID Región: ' || v_row.ID_Region || 
                                 ', Nombre de la Región: ' || v_row.Nombre_Region);
        END LOOP;

    ELSIF UPPER(p_operacion) = 'C' THEN
        -- Inserción
        INSERT INTO LAROATLB_Region_Cliente (
            Nombre_Region
        ) VALUES (
            p_nombre_region
        );
        DBMS_OUTPUT.PUT_LINE('Región insertada correctamente.');

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Verificar existencia
        OPEN c_region(p_id_region);
        FETCH c_region INTO v_existente;
        IF c_region%FOUND THEN
            -- Actualización
            UPDATE LAROATLB_Region_Cliente
            SET Nombre_Region = p_nombre_region
            WHERE ID_Region = p_id_region;
            DBMS_OUTPUT.PUT_LINE('Región actualizada correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró la región con el ID proporcionado.');
        END IF;
        CLOSE c_region;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Verificar existencia
        OPEN c_region(p_id_region);
        FETCH c_region INTO v_existente;
        IF c_region%FOUND THEN
            -- Eliminación
            DELETE FROM LAROATLB_Region_Cliente
            WHERE ID_Region = p_id_region;
            DBMS_OUTPUT.PUT_LINE('Región eliminada correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró la región con el ID proporcionado.');
        END IF;
        CLOSE c_region;

    ELSE
        DBMS_OUTPUT.PUT_LINE('Operación no reconocida. Use "R", "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Ocurrió un error: ' || SQLERRM);
END;

------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
-- MANTENEDOR DE COMUNA

CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_COMUNAS (
    p_operacion      VARCHAR2,
    p_id_comuna      NUMBER DEFAULT NULL,
    p_nombre_comuna  VARCHAR2 DEFAULT NULL,
    p_id_region      NUMBER DEFAULT NULL
)
IS
    -- Cursor para verificar si una comuna existe
    CURSOR c_comuna (id_comuna NUMBER) IS
        SELECT ID_COMUNA
        FROM LAROATLB_COMUNA_CLIENTE
        WHERE ID_COMUNA = id_comuna;

    -- Cursor para mostrar todas las comunas o filtradas por ID_REGION si se proporciona
    CURSOR c_comunas_all (id_region NUMBER) IS
        SELECT ID_COMUNA, NOMBRE_COMUNA, ID_REGION
        FROM LAROATLB_COMUNA_CLIENTE
        WHERE (id_region IS NULL OR ID_REGION = id_region);  -- Filtro por ID_REGION, si se proporciona

    v_existente c_comuna%ROWTYPE; -- Variable para manejar datos del cursor
BEGIN
    LOCK TABLE LAROATLB_COMUNA_CLIENTE IN ROW EXCLUSIVE MODE;

    IF UPPER(p_operacion) = 'R' THEN
        -- Leer todos los registros o filtrados por región
        DBMS_OUTPUT.PUT_LINE('--- LISTADO DE COMUNAS ---');
        FOR v_row IN c_comunas_all(p_id_region) LOOP
            DBMS_OUTPUT.PUT_LINE('ID Comuna: ' || v_row.ID_COMUNA || 
                                 ', Nombre de la Comuna: ' || v_row.NOMBRE_COMUNA ||
                                 ', ID Región: ' || v_row.ID_REGION);
        END LOOP;

    ELSIF UPPER(p_operacion) = 'C' THEN
        -- Inserción
        INSERT INTO LAROATLB_COMUNA_CLIENTE (
            NOMBRE_COMUNA,
            ID_REGION
        ) VALUES (
            p_nombre_comuna,
            p_id_region
        );
        DBMS_OUTPUT.PUT_LINE('Comuna insertada correctamente.');

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Verificar existencia
        OPEN c_comuna(p_id_comuna);
        FETCH c_comuna INTO v_existente;
        IF c_comuna%FOUND THEN
            -- Actualización
            UPDATE LAROATLB_COMUNA_CLIENTE
            SET NOMBRE_COMUNA = p_nombre_comuna,
                ID_REGION = p_id_region
            WHERE ID_COMUNA = p_id_comuna;
            DBMS_OUTPUT.PUT_LINE('Comuna actualizada correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró la comuna con el ID proporcionado.');
        END IF;
        CLOSE c_comuna;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Verificar existencia
        OPEN c_comuna(p_id_comuna);
        FETCH c_comuna INTO v_existente;
        IF c_comuna%FOUND THEN
            -- Eliminación
            DELETE FROM LAROATLB_COMUNA_CLIENTE
            WHERE ID_COMUNA = p_id_comuna;
            DBMS_OUTPUT.PUT_LINE('Comuna eliminada correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró la comuna con el ID proporcionado.');
        END IF;
        CLOSE c_comuna;

    ELSE
        DBMS_OUTPUT.PUT_LINE('Operación no reconocida. Use "R", "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Ocurrió un error: ' || SQLERRM);
END;

------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
-- MANTENEDOR DE CALLES CLIENTE
CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_CALLES (
    p_operacion      VARCHAR2,
    p_id_calle       NUMBER DEFAULT NULL,
    p_nombre_calle   VARCHAR2 DEFAULT NULL,
    p_numero_calle   NUMBER DEFAULT NULL,
    p_id_comuna      NUMBER DEFAULT NULL
)
IS
    -- Cursor para verificar si una calle existe
    CURSOR c_calle (id_calle NUMBER) IS
        SELECT ID_CALLE
        FROM LAROATLB_CALLE_CLIENTE
        WHERE ID_CALLE = id_calle;

    -- Cursor para mostrar todas las calles o filtradas por ID_COMUNA si se proporciona
    CURSOR c_calles_all (id_comuna NUMBER) IS
        SELECT c.ID_CALLE, c.NOMBRE_CALLE, c.NUMERO_CALLE, com.NOMBRE_COMUNA, reg.NOMBRE_REGION
        FROM LAROATLB_CALLE_CLIENTE c
        JOIN LAROATLB_COMUNA_CLIENTE com ON c.ID_COMUNA = com.ID_COMUNA
        JOIN LAROATLB_REGION_CLIENTE reg ON com.ID_REGION = reg.ID_REGION
        WHERE (id_comuna IS NULL OR c.ID_COMUNA = id_comuna);  -- Filtro por ID_COMUNA, si se proporciona

    v_existente c_calle%ROWTYPE; -- Variable para manejar datos del cursor
BEGIN
    LOCK TABLE LAROATLB_CALLE_CLIENTE IN ROW EXCLUSIVE MODE;

    IF UPPER(p_operacion) = 'R' THEN
        -- Leer todos los registros o filtrados por comuna
        DBMS_OUTPUT.PUT_LINE('--- LISTADO DE CALLES ---');
        FOR v_row IN c_calles_all(p_id_comuna) LOOP
            DBMS_OUTPUT.PUT_LINE('ID Calle: ' || v_row.ID_CALLE || 
                                 ', Nombre de la Calle: ' || v_row.NOMBRE_CALLE ||
                                 ', Número de la Calle: ' || v_row.NUMERO_CALLE ||
                                 ', Nombre de la Comuna: ' || v_row.NOMBRE_COMUNA ||
                                 ', Nombre de la Región: ' || v_row.NOMBRE_REGION);
        END LOOP;

    ELSIF UPPER(p_operacion) = 'C' THEN
        -- Inserción
        INSERT INTO LAROATLB_CALLE_CLIENTE (
            NOMBRE_CALLE,
            NUMERO_CALLE,
            ID_COMUNA
        ) VALUES (
            p_nombre_calle,
            p_numero_calle,
            p_id_comuna
        );
        DBMS_OUTPUT.PUT_LINE('Calle insertada correctamente.');

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Verificar existencia
        OPEN c_calle(p_id_calle);
        FETCH c_calle INTO v_existente;
        IF c_calle%FOUND THEN
            -- Actualización
            UPDATE LAROATLB_CALLE_CLIENTE
            SET NOMBRE_CALLE = p_nombre_calle,
                NUMERO_CALLE = p_numero_calle,
                ID_COMUNA = p_id_comuna
            WHERE ID_CALLE = p_id_calle;
            DBMS_OUTPUT.PUT_LINE('Calle actualizada correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró la calle con el ID proporcionado.');
        END IF;
        CLOSE c_calle;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Verificar existencia
        OPEN c_calle(p_id_calle);
        FETCH c_calle INTO v_existente;
        IF c_calle%FOUND THEN
            -- Eliminación
            DELETE FROM LAROATLB_CALLE_CLIENTE
            WHERE ID_CALLE = p_id_calle;
            DBMS_OUTPUT.PUT_LINE('Calle eliminada correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró la calle con el ID proporcionado.');
        END IF;
        CLOSE c_calle;

    ELSE
        DBMS_OUTPUT.PUT_LINE('Operación no reconocida. Use "R", "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Ocurrió un error: ' || SQLERRM);
END;

------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
--MANTENEDOR DE RAZA

CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_RAZAS (
    p_operacion      VARCHAR2,
    p_id_raza        NUMBER DEFAULT NULL,
    p_nombre_raza    VARCHAR2 DEFAULT NULL,
    p_id_especie     NUMBER DEFAULT NULL
)
IS
    -- Cursor para verificar si una raza existe
    CURSOR c_raza (id_raza NUMBER) IS
        SELECT ID_RAZA
        FROM LAROATLB_RAZA
        WHERE ID_RAZA = id_raza;

    -- Cursor para mostrar todas las razas con su especie asociada
    CURSOR c_razas_all IS
        SELECT r.ID_RAZA, r.NOMBRE_RAZA, e.NOMBRE_ESPECIE
        FROM LAROATLB_RAZA r
        JOIN LAROATLB_ESPECIE e ON r.ID_ESPECIE = e.ID_ESPECIE;

    v_existente c_raza%ROWTYPE; -- Variable para manejar datos del cursor
BEGIN
    LOCK TABLE LAROATLB_RAZA IN ROW EXCLUSIVE MODE;

    IF UPPER(p_operacion) = 'R' THEN
        -- Leer todas las razas con su especie asociada
        DBMS_OUTPUT.PUT_LINE('--- LISTADO DE RAZAS ---');
        FOR v_row IN c_razas_all LOOP
            DBMS_OUTPUT.PUT_LINE('ID Raza: ' || v_row.ID_RAZA || 
                                 ', Nombre de la Raza: ' || v_row.NOMBRE_RAZA ||
                                 ', Nombre de la Especie: ' || v_row.NOMBRE_ESPECIE);
        END LOOP;

    ELSIF UPPER(p_operacion) = 'C' THEN
        -- Inserción de Raza
        INSERT INTO LAROATLB_RAZA (
            NOMBRE_RAZA,
            ID_ESPECIE
        ) VALUES (
            p_nombre_raza,
            p_id_especie
        );
        DBMS_OUTPUT.PUT_LINE('Raza insertada correctamente.');

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Verificar existencia de la raza
        OPEN c_raza(p_id_raza);
        FETCH c_raza INTO v_existente;
        IF c_raza%FOUND THEN
            -- Actualización de Raza
            UPDATE LAROATLB_RAZA
            SET NOMBRE_RAZA = p_nombre_raza,
                ID_ESPECIE = p_id_especie
            WHERE ID_RAZA = p_id_raza;
            DBMS_OUTPUT.PUT_LINE('Raza actualizada correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró la raza con el ID proporcionado.');
        END IF;
        CLOSE c_raza;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Verificar existencia de la raza
        OPEN c_raza(p_id_raza);
        FETCH c_raza INTO v_existente;
        IF c_raza%FOUND THEN
            -- Eliminación de Raza
            DELETE FROM LAROATLB_RAZA
            WHERE ID_RAZA = p_id_raza;
            DBMS_OUTPUT.PUT_LINE('Raza eliminada correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró la raza con el ID proporcionado.');
        END IF;
        CLOSE c_raza;

    ELSE
        DBMS_OUTPUT.PUT_LINE('Operación no reconocida. Use "R", "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Ocurrió un error: ' || SQLERRM);
END;


------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
--MANTENEDOR DE ESPECIE

CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_ESPECIES (
    p_operacion      VARCHAR2,
    p_id_especie     NUMBER DEFAULT NULL,
    p_nombre_especie VARCHAR2 DEFAULT NULL
)
IS
    -- Cursor para verificar si una especie existe
    CURSOR c_especie (id_especie NUMBER) IS
        SELECT ID_ESPECIE
        FROM LAROATLB_ESPECIE
        WHERE ID_ESPECIE = id_especie;

    -- Cursor para mostrar todas las especies
    CURSOR c_especies_all IS
        SELECT ID_ESPECIE, NOMBRE_ESPECIE
        FROM LAROATLB_ESPECIE;

    v_existente c_especie%ROWTYPE; -- Variable para manejar datos del cursor
BEGIN
    LOCK TABLE LAROATLB_ESPECIE IN ROW EXCLUSIVE MODE;

    IF UPPER(p_operacion) = 'R' THEN
        -- Leer todas las especies
        DBMS_OUTPUT.PUT_LINE('--- LISTADO DE ESPECIES ---');
        FOR v_row IN c_especies_all LOOP
            DBMS_OUTPUT.PUT_LINE('ID Especie: ' || v_row.ID_ESPECIE || 
                                 ', Nombre de la Especie: ' || v_row.NOMBRE_ESPECIE);
        END LOOP;

    ELSIF UPPER(p_operacion) = 'C' THEN
        -- Inserción de Especie
        INSERT INTO LAROATLB_ESPECIE (
            NOMBRE_ESPECIE
        ) VALUES (
            p_nombre_especie
        );
        DBMS_OUTPUT.PUT_LINE('Especie insertada correctamente.');

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Verificar existencia de la especie
        OPEN c_especie(p_id_especie);
        FETCH c_especie INTO v_existente;
        IF c_especie%FOUND THEN
            -- Actualización de Especie
            UPDATE LAROATLB_ESPECIE
            SET NOMBRE_ESPECIE = p_nombre_especie
            WHERE ID_ESPECIE = p_id_especie;
            DBMS_OUTPUT.PUT_LINE('Especie actualizada correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró la especie con el ID proporcionado.');
        END IF;
        CLOSE c_especie;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Verificar existencia de la especie
        OPEN c_especie(p_id_especie);
        FETCH c_especie INTO v_existente;
        IF c_especie%FOUND THEN
            -- Eliminación de Especie
            DELETE FROM LAROATLB_ESPECIE
            WHERE ID_ESPECIE = p_id_especie;
            DBMS_OUTPUT.PUT_LINE('Especie eliminada correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró la especie con el ID proporcionado.');
        END IF;
        CLOSE c_especie;

    ELSE
        DBMS_OUTPUT.PUT_LINE('Operación no reconocida. Use "R", "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Ocurrió un error: ' || SQLERRM);
END;

------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
--MANTENEDOR DE MASCOTA
CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_MASCOTAS (
    p_operacion      VARCHAR2,
    p_id_mascota     NUMBER DEFAULT NULL,
    p_nombre_mascota VARCHAR2 DEFAULT NULL,
    p_edad           NUMBER DEFAULT NULL,
    p_id_cliente     NUMBER DEFAULT NULL,
    p_id_raza        NUMBER DEFAULT NULL
)
IS
    -- Cursor para verificar si una mascota existe
    CURSOR c_mascota (id_mascota NUMBER) IS
        SELECT ID_MASCOTA
        FROM LAROATLB_MASCOTA
        WHERE ID_MASCOTA = id_mascota;

    -- Cursor para mostrar todas las mascotas con su raza y cliente asociados
    CURSOR c_mascotas_all IS
        SELECT m.ID_MASCOTA, m.NOMBRE, m.EDAD, c.NOMBRE_CLIENTE, r.NOMBRE_RAZA
        FROM LAROATLB_MASCOTA m
        JOIN LAROATLB_CLIENTE c ON m.ID_CLIENTE = c.ID_CLIENTE
        JOIN LAROATLB_RAZA r ON m.ID_RAZA = r.ID_RAZA;

    v_existente c_mascota%ROWTYPE; -- Variable para manejar datos del cursor
BEGIN
    LOCK TABLE LAROATLB_MASCOTA IN ROW EXCLUSIVE MODE;

    IF UPPER(p_operacion) = 'R' THEN
        -- Leer todas las mascotas con su raza y cliente asociados
        DBMS_OUTPUT.PUT_LINE('--- LISTADO DE MASCOTAS ---');
        FOR v_row IN c_mascotas_all LOOP
            DBMS_OUTPUT.PUT_LINE('ID Mascota: ' || v_row.ID_MASCOTA || 
                                 ', Nombre de la Mascota: ' || v_row.NOMBRE ||
                                 ', Edad: ' || v_row.EDAD ||
                                 ', Nombre del Cliente: ' || v_row.NOMBRE_CLIENTE ||
                                 ', Nombre de la Raza: ' || v_row.NOMBRE_RAZA);
        END LOOP;

    ELSIF UPPER(p_operacion) = 'C' THEN
        -- Inserción de Mascota
        INSERT INTO LAROATLB_MASCOTA (
            NOMBRE,
            EDAD,
            ID_CLIENTE,
            ID_RAZA
        ) VALUES (
            p_nombre_mascota,
            p_edad,
            p_id_cliente,
            p_id_raza
        );
        DBMS_OUTPUT.PUT_LINE('Mascota insertada correctamente.');

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Verificar existencia de la mascota
        OPEN c_mascota(p_id_mascota);
        FETCH c_mascota INTO v_existente;
        IF c_mascota%FOUND THEN
            -- Actualización de Mascota
            UPDATE LAROATLB_MASCOTA
            SET NOMBRE = p_nombre_mascota,
                EDAD = p_edad,
                ID_CLIENTE = p_id_cliente,
                ID_RAZA = p_id_raza
            WHERE ID_MASCOTA = p_id_mascota;
            DBMS_OUTPUT.PUT_LINE('Mascota actualizada correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró la mascota con el ID proporcionado.');
        END IF;
        CLOSE c_mascota;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Verificar existencia de la mascota
        OPEN c_mascota(p_id_mascota);
        FETCH c_mascota INTO v_existente;
        IF c_mascota%FOUND THEN
            -- Eliminación de Mascota
            DELETE FROM LAROATLB_MASCOTA
            WHERE ID_MASCOTA = p_id_mascota;
            DBMS_OUTPUT.PUT_LINE('Mascota eliminada correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró la mascota con el ID proporcionado.');
        END IF;
        CLOSE c_mascota;

    ELSE
        DBMS_OUTPUT.PUT_LINE('Operación no reconocida. Use "R", "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Ocurrió un error: ' || SQLERRM);
END;


------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
--MANTENEDOR DE CITA

CREATE OR REPLACE PROCEDURE LAROATLB_GESTIONAR_CITAS (
    p_operacion      VARCHAR2,
    p_id_cita        NUMBER DEFAULT NULL,
    p_fecha          DATE DEFAULT NULL,
    p_sala           NUMBER DEFAULT NULL,
    p_id_mascota     NUMBER DEFAULT NULL,
    p_id_veterinario NUMBER DEFAULT NULL
)
IS
    -- Cursor para verificar si una cita existe
    CURSOR c_cita (id_cita NUMBER) IS
        SELECT c.ID_CITA, c.FECHA, c.SALA, c.ID_MASCOTA, c.ID_VETERINARIO,
               m.NOMBRE AS NOMBRE_MASCOTA, v.NOMBRE AS NOMBRE_VETERINARIO
        FROM LAROATLB_CITA c
        JOIN LAROATLB_MASCOTA m ON c.ID_MASCOTA = m.ID_MASCOTA
        JOIN LAROATLB_VETERINARIO v ON c.ID_VETERINARIO = v.ID_VETERINARIO
        WHERE c.ID_CITA = id_cita;

    -- Cursor para mostrar todas las citas
    CURSOR c_citas_all IS
        SELECT c.ID_CITA, c.FECHA, c.SALA, c.ID_MASCOTA,m.NOMBRE AS NOMBRE_MASCOTA, c.ID_VETERINARIO,
                v.NOMBRE AS NOMBRE_VETERINARIO
        FROM LAROATLB_CITA c
        JOIN LAROATLB_MASCOTA m ON c.ID_MASCOTA = m.ID_MASCOTA
        JOIN LAROATLB_VETERINARIO v ON c.ID_VETERINARIO = v.ID_VETERINARIO;

    v_existente c_cita%ROWTYPE; -- Variable para manejar datos del cursor
BEGIN
    LOCK TABLE LAROATLB_CITA IN ROW EXCLUSIVE MODE;

    IF UPPER(p_operacion) = 'R' THEN
        -- Leer todas las citas
        DBMS_OUTPUT.PUT_LINE('--- LISTADO DE CITAS ---');
        FOR v_row IN c_citas_all LOOP
            DBMS_OUTPUT.PUT_LINE('ID Cita: ' || v_row.ID_CITA || 
                                 ', Fecha: ' || v_row.FECHA || 
                                 ', Sala: ' || v_row.SALA || 
                                 ', Nombre Mascota: ' || v_row.NOMBRE_MASCOTA || 
                                 ', Nombre Veterinario: ' || v_row.NOMBRE_VETERINARIO);
        END LOOP;

    ELSIF UPPER(p_operacion) = 'C' THEN
        -- Inserción de Cita
        INSERT INTO LAROATLB_CITA (
            FECHA, SALA, ID_MASCOTA, ID_VETERINARIO
        ) VALUES (
            p_fecha, p_sala, p_id_mascota, p_id_veterinario
        );
        DBMS_OUTPUT.PUT_LINE('Cita insertada correctamente.');

    ELSIF UPPER(p_operacion) = 'U' THEN
        -- Verificar existencia de la cita
        OPEN c_cita(p_id_cita);
        FETCH c_cita INTO v_existente;
        IF c_cita%FOUND THEN
            -- Actualización de Cita
            UPDATE LAROATLB_CITA
            SET FECHA = p_fecha,
                SALA = p_sala,
                ID_MASCOTA = p_id_mascota,
                ID_VETERINARIO = p_id_veterinario
            WHERE ID_CITA = p_id_cita;
            DBMS_OUTPUT.PUT_LINE('Cita actualizada correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró la cita con el ID proporcionado.');
        END IF;
        CLOSE c_cita;

    ELSIF UPPER(p_operacion) = 'D' THEN
        -- Verificar existencia de la cita
        OPEN c_cita(p_id_cita);
        FETCH c_cita INTO v_existente;
        IF c_cita%FOUND THEN
            -- Eliminación de Cita
            DELETE FROM LAROATLB_CITA
            WHERE ID_CITA = p_id_cita;
            DBMS_OUTPUT.PUT_LINE('Cita eliminada correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró la cita con el ID proporcionado.');
        END IF;
        CLOSE c_cita;

    ELSE
        DBMS_OUTPUT.PUT_LINE('Operación no reconocida. Use "R", "C", "U" o "D".');
    END IF;

    -- Confirmar la transacción
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        RAISE_APPLICATION_ERROR(-20001, 'Ocurrió un error: ' || SQLERRM);
END;

