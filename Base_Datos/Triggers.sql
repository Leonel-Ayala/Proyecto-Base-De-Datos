----------------------------TRIGGERS------------------------------------------------------------
------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------
------------------------------------------------------------------------------------------------

create or replace TRIGGER LAROATLB_ID_CALLE_CLIENTE
  BEFORE INSERT 
  ON LAROATLB_CALLE_CLIENTE
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_CALLE),0)+1 INTO :NEW.ID_CALLE
  FROM LAROATLB_CALLE_CLIENTE;
END;

------------------------------------------------------------------------------------------------
create or replace TRIGGER LAROATLB_ID_CITA
  BEFORE INSERT 
  ON LAROATLB_CITA
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_CITA),0)+1 INTO :NEW.ID_CITA
  FROM LAROATLB_CITA;
END;

------------------------------------------------------------------------------------------------

create or replace TRIGGER LAROATLB_ID_CLIENTE
  BEFORE INSERT 
  ON LAROATLB_CLIENTE
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_CLIENTE),0)+1 INTO :NEW.ID_CLIENTE
  FROM LAROATLB_CLIENTE;
END;

------------------------------------------------------------------------------------------------
create or replace TRIGGER LAROATLB_ID_COMUNA_CLIENTE
  BEFORE INSERT 
  ON LAROATLB_COMUNA_CLIENTE
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_COMUNA),0)+1 INTO :NEW.ID_COMUNA
  FROM LAROATLB_COMUNA_CLIENTE;
END;


------------------------------------------------------------------------------------------------
create or replace TRIGGER LAROATLB_ID_ESPECIE
  BEFORE INSERT 
  ON LAROATLB_ESPECIE
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_ESPECIE),0)+1 INTO :NEW.ID_ESPECIE
  FROM LAROATLB_ESPECIE;
END;


------------------------------------------------------------------------------------------------
create or replace TRIGGER LAROATLB_ID_LOG_LOGIN
  BEFORE INSERT 
  ON LAROATLB_LOG_LOGIN
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_LOGIN),0)+1 INTO :NEW.ID_LOGIN
  FROM LAROATLB_LOG_LOGIN;
END;


------------------------------------------------------------------------------------------------
create or replace TRIGGER LAROATLB_ID_MASCOTA
  BEFORE INSERT 
  ON LAROATLB_MASCOTA
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_MASCOTA),0)+1 INTO :NEW.ID_MASCOTA
  FROM LAROATLB_MASCOTA;
END;

------------------------------------------------------------------------------------------------
create or replace TRIGGER LAROATLB_ID_PRODUCTO
  BEFORE INSERT 
  ON LAROATLB_PRODUCTO
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_PRODUCTO),0)+1 INTO :NEW.ID_PRODUCTO
  FROM LAROATLB_PRODUCTO;
END;


------------------------------------------------------------------------------------------------
create or replace TRIGGER LAROATLB_ID_RAZA
  BEFORE INSERT 
  ON LAROATLB_RAZA
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_RAZA),0)+1 INTO :NEW.ID_RAZA
  FROM LAROATLB_RAZA;
END;


------------------------------------------------------------------------------------------------
create or replace TRIGGER LAROATLB_ID_REGION
  BEFORE INSERT 
  ON LAROATLB_REGION_CLIENTE
  FOR EACH ROW
DECLARE
BEGIN
  -- Obtener el valor máximo de ID_REGION y sumarle 1 para el nuevo registro
  SELECT NVL(MAX(ID_REGION), 0) + 1 INTO :NEW.ID_REGION
    FROM LAROATLB_REGION_CLIENTE;
END;


------------------------------------------------------------------------------------------------

create or replace TRIGGER LAROATLB_ID_SECRETARIA
  BEFORE INSERT
  ON LAROATLB_SECRETARIA
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_SECRE),0)+1 INTO :NEW.ID_SECRE
  FROM LAROATLB_SECRETARIA;
END;

------------------------------------------------------------------------------------------------
create or replace TRIGGER LAROATLB_ID_TRATAMIENTO
  BEFORE INSERT 
  ON LAROATLB_TRATAMIENTO
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_TRATAMIENTO),0)+1 INTO :NEW.ID_TRATAMIENTO
  FROM LAROATLB_TRATAMIENTO;
END;

------------------------------------------------------------------------------------------------
create or replace TRIGGER LAROATLB_ID_USUARIOS
  BEFORE INSERT 
  ON LAROATLB_USUARIOS
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_USUARIO),0)+1 INTO :NEW.ID_USUARIO
  FROM LAROATLB_USUARIOS;
END;

------------------------------------------------------------------------------------------------
create or replace TRIGGER LAROATLB_ID_VETERINARIO
  BEFORE INSERT 
  ON LAROATLB_VETERINARIO
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_VETERINARIO),0)+1 INTO :NEW.ID_VETERINARIO
  FROM LAROATLB_VETERINARIO;
END;

------------------------------------------------------------------------------------------------
create or replace TRIGGER LAROATLB_MAYUS_CLIENTE
BEFORE INSERT OR UPDATE
ON LAROATLB_CLIENTE
FOR EACH ROW
BEGIN
    :NEW.NOMBRE := UPPER(:NEW.NOMBRE);
    :NEW.APELLIDO1 := UPPER(:NEW.APELLIDO1);
    :NEW.APELLIDO2 := UPPER(:NEW.APELLIDO2);
END;


------------------------------------------------------------------------------------------------
create or replace TRIGGER LAROATLB_MAYUS_ESPECIE
BEFORE INSERT OR UPDATE
ON LAROATLB_ESPECIE
FOR EACH ROW
BEGIN
    :NEW.NOMBRE_ESPECIE := UPPER(:NEW.NOMBRE_ESPECIE);
END;

------------------------------------------------------------------------------------------------
create or replace TRIGGER LAROATLB_MAYUS_MASCOTA
BEFORE INSERT OR UPDATE
ON LAROATLB_MASCOTA
FOR EACH ROW
BEGIN
    :NEW.NOMBRE := UPPER(:NEW.NOMBRE);
END;


------------------------------------------------------------------------------------------------
create or replace TRIGGER LAROATLB_MAYUS_PRODU
BEFORE INSERT 
ON LAROATLB_PRODUCTO
FOR EACH ROW
BEGIN
    :NEW.NOMBRE_PRODUCTO := UPPER(:NEW.NOMBRE_PRODUCTO);
END;


------------------------------------------------------------------------------------------------
create or replace TRIGGER LAROATLB_MAYUS_RAZA
BEFORE INSERT OR UPDATE
ON LAROATLB_RAZA
FOR EACH ROW
BEGIN
    :NEW.NOMBRE_RAZA := UPPER(:NEW.NOMBRE_RAZA);
END;



------------------------------------------------------------------------------------------------
create or replace TRIGGER LAROATLB_MAYUS_SECRE
BEFORE INSERT OR UPDATE
ON LAROATLB_SECRETARIA
FOR EACH ROW
BEGIN
    :NEW.NOMBRE := UPPER(:NEW.NOMBRE);
    :NEW.APELLIDO1 := UPPER(:NEW.APELLIDO1);
    :NEW.APELLIDO2 := UPPER(:NEW.APELLIDO2);
    :NEW.EMAIL := UPPER(:NEW.EMAIL);
END;


------------------------------------------------------------------------------------------------

create or replace TRIGGER LAROATLB_MAYUS_VETE
BEFORE INSERT OR UPDATE
ON LAROATLB_VETERINARIO
FOR EACH ROW
BEGIN
    :NEW.NOMBRE := UPPER(:NEW.NOMBRE);
    :NEW.APELLIDO1 := UPPER(:NEW.APELLIDO1);
    :NEW.APELLIDO2 := UPPER(:NEW.APELLIDO2);
    :NEW.EMAIL := UPPER(:NEW.EMAIL);
    :NEW.ESPECIALIDAD := UPPER(:NEW.ESPECIALIDAD);
END;
-------
