-- Triggers para PK
CREATE OR REPLACE TRIGGER LAROATLB_ID_CLIENTE
  BEFORE INSERT 
  ON LAROATLB_CLIENTE
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_CLIENTE),0)+1 INTO :NEW.ID_CLIENTE
  FROM LAROATLB_CLIENTE;
END;


CREATE OR REPLACE TRIGGER LAROATLB_ID_MASCOTA
  BEFORE INSERT 
  ON LAROATLB_MASCOTA
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_MASCOTA),0)+1 INTO :NEW.ID_MASCOTA
  FROM LAROATLB_MASCOTA;
END;


CREATE OR REPLACE TRIGGER LAROATLB_ID_CITA
  BEFORE INSERT 
  ON LAROATLB_CITA
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_CITA),0)+1 INTO :NEW.ID_CITA
  FROM LAROATLB_CITA;
END;


CREATE OR REPLACE TRIGGER LAROATLB_ID_VETERINARIO
  BEFORE INSERT 
  ON LAROATLB_VETERINARIO
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_VETERINARIO),0)+1 INTO :NEW.ID_VETERINARIO
  FROM LAROATLB_VETERINARIO;
END;

CREATE OR REPLACE TRIGGER LAROATLB_ID_TRATAMIENTO
  BEFORE INSERT 
  ON LAROATLB_TRATAMIENTO
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_TRATAMIENTO),0)+1 INTO :NEW.ID_TRATAMIENTO
  FROM LAROATLB_TRATAMIENTO;
END;

CREATE OR REPLACE TRIGGER LAROATLB_ID_HISTORIAL_MEDICO
  BEFORE INSERT 
  ON LAROATLB_HISTORIAL_MEDICO
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_HISTORIAL),0)+1 INTO :NEW.ID_HISTORIAL
  FROM LAROATLB_HISTORIAL_MEDICO;
END;


CREATE OR REPLACE TRIGGER LAROATLB_ID_RAZA
  BEFORE INSERT 
  ON LAROATLB_RAZA
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_RAZA),0)+1 INTO :NEW.ID_RAZA
  FROM LAROATLB_RAZA;
END;

CREATE OR REPLACE TRIGGER LAROATLB_ID_ESPECIE
  BEFORE INSERT 
  ON LAROATLB_ESPECIE
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_ESPECIE),0)+1 INTO :NEW.ID_ESPECIE
  FROM LAROATLB_ESPECIE;
END;

-- Triggers Mayus
CREATE OR REPLACE TRIGGER LAROATLB_MAYUS_CLIENTE
BEFORE INSERT
ON LAROATLB_CLIENTE
FOR EACH ROW
BEGIN
    :NEW.NOMBRE := UPPER(:NEW.NOMBRE);
    :NEW.APELLIDO1 := UPPER(:NEW.APELLIDO1);
    :NEW.APELLIDO2 := UPPER(:NEW.APELLIDO2);
END;

CREATE OR REPLACE TRIGGER LAAB_MAYUS_SECRE
BEFORE INSERT
ON LAROATLB_SECRETARIA
FOR EACH ROW
BEGIN
    :NEW.NOMBRE := UPPER(:NEW.NOMBRE);
    :NEW.APELLIDO1 := UPPER(:NEW.APELLIDO1);
    :NEW.APELLIDO2 := UPPER(:NEW.APELLIDO2);
    :NEW.EMAIL := UPPER(:NEW.EMAIL);
END;

CREATE OR REPLACE TRIGGER LAROATLB_MAYUS_VETE
BEFORE INSERT
ON LAROATLB_VETERINARIO
FOR EACH ROW
BEGIN
    :NEW.NOMBRE := UPPER(:NEW.NOMBRE);
    :NEW.APELLIDO1 := UPPER(:NEW.APELLIDO1);
    :NEW.APELLIDO2 := UPPER(:NEW.APELLIDO2);
    :NEW.EMAIL := UPPER(:NEW.EMAIL);
END;


--Genera correo vete

CREATE OR REPLACE TRIGGER LAROATLB_GENERA_CORREO_VETE
BEFORE INSERT ON LAROATLB_VETERINARIO
FOR EACH ROW
DECLARE
    CANTIDAD NUMBER := 0;
BEGIN

    SELECT COUNT(*) INTO CANTIDAD
    FROM LAROATLB_VETERINARIO
    WHERE (UPPER(SUBSTR(NOMBRE,1,2)) = UPPER(SUBSTR(:NEW.NOMBRE,1,2)))
      AND (UPPER(SUBSTR(APELLIDO1,3,2)) = UPPER(SUBSTR(:NEW.APELLIDO1,3,2)))
      AND (UPPER(APELLIDO2) = UPPER(:NEW.APELLIDO2));
      
    IF (CANTIDAD = 0) THEN
        :NEW.EMAIL := CONCAT(SUBSTR(:NEW.NOMBRE,1,2),CONCAT(SUBSTR(:NEW.APELLIDO1,3,2),CONCAT('.',CONCAT(:NEW.APELLIDO2,'@VETSOL.CL'))));
    ELSE 
        :NEW.EMAIL := CONCAT(SUBSTR(:NEW.NOMBRE,1,2),CONCAT(SUBSTR(:NEW.APELLIDO1,3,2),CONCAT('.',CONCAT(CONCAT(:NEW.APELLIDO2,TO_CHAR(CANTIDAD)),'@VETSOL.CL'))));
    END IF;
END;


CREATE OR REPLACE TRIGGER LAROATLB_ACTUALIZA_CORREO_VETE
AFTER UPDATE ON LAROATLB_VETERINARIO
FOR EACH ROW
DECLARE
    CANTIDAD NUMBER := 0;
BEGIN

    SELECT COUNT(*) INTO CANTIDAD
    FROM LAROATLB_VETERINARIO
    WHERE (UPPER(SUBSTR(NOMBRE,1,2)) = UPPER(SUBSTR(:NEW.NOMBRE,1,2)))
      AND (UPPER(SUBSTR(APELLIDO1,3,2)) = UPPER(SUBSTR(:NEW.APELLIDO1,3,2)))
      AND (UPPER(APELLIDO2) = UPPER(:NEW.APELLIDO2));
      
    IF (CANTIDAD = 0) THEN
        :NEW.EMAIL := CONCAT(SUBSTR(:NEW.NOMBRE,1,2),CONCAT(SUBSTR(:NEW.APELLIDO1,3,2),CONCAT('.',CONCAT(:NEW.APELLIDO2,'@VETSOL.CL'))));
    ELSE 
        :NEW.EMAIL := CONCAT(SUBSTR(:NEW.NOMBRE,1,2),CONCAT(SUBSTR(:NEW.APELLIDO1,3,2),CONCAT('.',CONCAT(CONCAT(:NEW.APELLIDO2,TO_CHAR(CANTIDAD)),'@VETSOL.CL'))));
    END IF;
END;

--Genera Correo Secre
CREATE OR REPLACE TRIGGER LAROATLB_GENERA_CORREO_SECRE
BEFORE INSERT  ON LAROATLB_SECRETARIA
FOR EACH ROW
DECLARE
    CANTIDAD NUMBER := 0;
BEGIN

    SELECT COUNT(*) INTO CANTIDAD
    FROM LAROATLB_SECRETARIA
    WHERE (UPPER(SUBSTR(NOMBRE,1,2)) = UPPER(SUBSTR(:NEW.NOMBRE,1,2)))
      AND (UPPER(SUBSTR(APELLIDO1,3,2)) = UPPER(SUBSTR(:NEW.APELLIDO1,3,2)))
      AND (UPPER(APELLIDO2) = UPPER(:NEW.APELLIDO2));
      
    IF (CANTIDAD = 0) THEN
        :NEW.EMAIL := CONCAT(SUBSTR(:NEW.NOMBRE,1,2),CONCAT(SUBSTR(:NEW.APELLIDO1,3,2),CONCAT('.',CONCAT(:NEW.APELLIDO2,'@VETSOL.CL'))));
    ELSE 
        :NEW.EMAIL := CONCAT(SUBSTR(:NEW.NOMBRE,1,2),CONCAT(SUBSTR(:NEW.APELLIDO1,3,2),CONCAT('.',CONCAT(CONCAT(:NEW.APELLIDO2,TO_CHAR(CANTIDAD)),'@VETSOL.CL'))));
    END IF;
END;

CREATE OR REPLACE TRIGGER LAROATLB_ACTUALIZA_CORREO_SECRE
AFTER UPDATE ON LAROATLB_SECRETARIA
FOR EACH ROW
DECLARE
    CANTIDAD NUMBER := 0;
BEGIN

    SELECT COUNT(*) INTO CANTIDAD
    FROM LAROATLB_SECRETARIA
    WHERE (UPPER(SUBSTR(NOMBRE,1,2)) = UPPER(SUBSTR(:NEW.NOMBRE,1,2)))
      AND (UPPER(SUBSTR(APELLIDO1,3,2)) = UPPER(SUBSTR(:NEW.APELLIDO1,3,2)))
      AND (UPPER(APELLIDO2) = UPPER(:NEW.APELLIDO2));
      
    IF (CANTIDAD = 0) THEN
        :NEW.EMAIL := CONCAT(SUBSTR(:NEW.NOMBRE,1,2),CONCAT(SUBSTR(:NEW.APELLIDO1,3,2),CONCAT('.',CONCAT(:NEW.APELLIDO2,'@VETSOL.CL'))));
    ELSE 
        :NEW.EMAIL := CONCAT(SUBSTR(:NEW.NOMBRE,1,2),CONCAT(SUBSTR(:NEW.APELLIDO1,3,2),CONCAT('.',CONCAT(CONCAT(:NEW.APELLIDO2,TO_CHAR(CANTIDAD)),'@VETSOL.CL'))));
    END IF;
END;


