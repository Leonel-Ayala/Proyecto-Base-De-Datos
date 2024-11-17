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

    -- Cursor para mostrar todos los veterinarios
    CURSOR c_veterinarios_all IS
        SELECT ID_VETERINARIO, NOMBRE, APELLIDO1, APELLIDO2, ESPECIALIDAD, TELEFONO, EMAIL
        FROM LAROATLB_VETERINARIO;

    v_existente c_veterinario%ROWTYPE; -- Variable para manejar datos del cursor
BEGIN
    LOCK TABLE LAROATLB_VETERINARIO IN ROW EXCLUSIVE MODE;
    NUEVO_CORREO := LAROATLB_GENERA_CORREO_VETE(p_nombre, p_apellido1, p_apellido2);

    IF UPPER(p_operacion) = 'R' THEN
        -- Leer todos los registros
        DBMS_OUTPUT.PUT_LINE('--- LISTADO DE VETERINARIOS ---');
        FOR v_row IN c_veterinarios_all LOOP
            DBMS_OUTPUT.PUT_LINE('ID: ' || v_row.ID_VETERINARIO || 
                                 ', Nombre: ' || v_row.NOMBRE || ' ' || v_row.APELLIDO1 || ' ' || v_row.APELLIDO2 || 
                                 ', Especialidad: ' || v_row.ESPECIALIDAD || 
                                 ', Teléfono: ' || v_row.TELEFONO || 
                                 ', Email: ' || v_row.EMAIL);
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


------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
--MANTENEDOR DE ESPECIE


------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
--MANTENEDOR DE MASCOTA


------------------------------------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------------------------------------
--MANTENEDOR DE CITA


