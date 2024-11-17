
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
        DBMS_OUTPUT.PUT_LINE('Operación no reconocida. Use "R", "I", "U" o "D".');
    END IF;

    -- Confirmar la transacción (en caso de no estar en modo automático)
    COMMIT;
EXCEPTION
    WHEN PROGRAM_ERROR THEN
        RAISE_APPLICATION_ERROR(-6501, 'ERROR DE PROGRAMA');
END;


--MANTENEDOR DE SECRETARIA

create or replace PROCEDURE GESTIONAR_SECRETARIA (
    p_operacion  VARCHAR2,
    p_id_secretaria  NUMBER DEFAULT NULL,
    p_nombre VARCHAR2 DEFAULT NULL,
    p_apellido1  VARCHAR2 DEFAULT NULL,
    p_apellido2  VARCHAR2 DEFAULT NULL,
    p_especialidad  VARCHAR2 DEFAULT NULL,
    p_telefono  NUMBER DEFAULT NULL
) 
IS
    NUEVO_CORREO VARCHAR2(100);
    -- Cursor para verificar si un veterinario existe
    CURSOR c_secretaria (id_sec NUMBER) IS
        SELECT ID_SECRETARIA
        FROM LAROATLB_SECRETARIA
        WHERE ID_SECRETARIA = id_vet;

    v_existente c_secretaria%ROWTYPE; -- Variable para manejar datos del cursor
BEGIN
    LOCK TABLE LAROATLB_SECRETARIA IN ROW EXCLUSIVE MODE;
    NUEVO_CORREO := LAROATLB_GENERA_CORREO_SECRE(p_nombre,p_apellido1,p_apellido2);

    IF UPPER(p_operacion) = 'R' THEN
        -- Inserción
        INSERT INTO LAROATLB_SECRETARIA (
            NOMBRE, APELLIDO1, APELLIDO2, ESPECIALIDAD, TELEFONO, EMAIL
        ) VALUES (
            p_nombre, p_apellido1, p_apellido2, p_especialidad, p_telefono, NUEVO_CORREO
        );
        DBMS_OUTPUT.PUT_LINE('Secretaria insertada correctamente.');

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
        DBMS_OUTPUT.PUT_LINE('Operación no reconocida. Use "INSERTAR", "ACTUALIZAR" o "BORRAR".');
    END IF;

    -- Confirmar la transacción (en caso de no estar en modo automático)
    COMMIT;
EXCEPTION
     WHEN PROGRAM_ERROR THEN
        RAISE_APPLICATION_ERROR(-6501,'ERROR DE PROGRAMA');
END;
