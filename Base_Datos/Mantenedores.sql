
//MANTENEDOR DE VETERINARIO
CREATE OR REPLACE PROCEDURE GESTIONAR_VETERINARIOS (
    p_operacion  VARCHAR2,
    p_id_veterinario  NUMBER DEFAULT NULL,
    p_nombre VARCHAR2 DEFAULT NULL,
    p_apellido1  VARCHAR2 DEFAULT NULL,
    p_apellido2  VARCHAR2 DEFAULT NULL,
    p_especialidad  VARCHAR2 DEFAULT NULL,
    p_telefono  NUMBER DEFAULT NULL,
    p_email  VARCHAR2 DEFAULT NULL
) IS
    -- Cursor para verificar si un veterinario existe
    CURSOR c_veterinario (id_vet NUMBER) IS
        SELECT ID_VETERINARIO
        FROM LAROATLB_VETERINARIO
        WHERE ID_VETERINARIO = id_vet;
        
    v_existente c_veterinario%ROWTYPE; -- Variable para manejar datos del cursor
BEGIN
    LOCK TABLE LAROATLB_VETERINARIO IN ROW EXCLUSIVE MODE;
    IF p_operacion = 'R' THEN
        -- Inserción
        INSERT INTO LAROATLB_VETERINARIO (
            ID_VETERINARIO, NOMBRE, APELLIDO1, APELLIDO2, ESPECIALIDAD, TELEFONO, EMAIL
        ) VALUES (
            p_id_veterinario, p_nombre, p_apellido1, p_apellido2, p_especialidad, p_telefono, p_email
        );
        DBMS_OUTPUT.PUT_LINE('Veterinario insertado correctamente.');
        
    ELSIF p_operacion = 'U' THEN
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
                EMAIL = p_email
            WHERE ID_VETERINARIO = p_id_veterinario;
            DBMS_OUTPUT.PUT_LINE('Veterinario actualizado correctamente.');
        ELSE
            DBMS_OUTPUT.PUT_LINE('No se encontró el veterinario con el ID proporcionado.');
        END IF;
        CLOSE c_veterinario;
        
    ELSIF p_operacion = 'D' THEN
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

