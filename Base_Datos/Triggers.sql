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

CREATE OR REPLACE TRIGGER LAROATLB_ID_CALLE_CLIENTE
  BEFORE INSERT 
  ON LAROATLB_CALLE_CLIENTE
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_CALLE),0)+1 INTO :NEW.ID_CALLE
  FROM LAROATLB_CALLE_CLIENTE;
END;

CREATE OR REPLACE TRIGGER LAROATLB_ID_PRODUCTO
  BEFORE INSERT 
  ON LAROATLB_PRODUCTO
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_PRODUCTO),0)+1 INTO :NEW.ID_PRODUCTO
  FROM LAROATLB_PRODUCTO;
END;

CREATE OR REPLACE TRIGGER LAROATLB_ID_LOG_LOGIN
  BEFORE INSERT 
  ON LAROATLB_LOG_LOGIN
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_LOGIN),0)+1 INTO :NEW.ID_LOGIN
  FROM LAROATLB_LOG_LOGIN;
END;

CREATE OR REPLACE TRIGGER LAROATLB_ID_USUARIOS
  BEFORE INSERT 
  ON LAROATLB_USUARIOS
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_USUARIO),0)+1 INTO :NEW.ID_USUARIO
  FROM LAROATLB_USUARIOS;
END;

CREATE OR REPLACE TRIGGER LAROATLB_ID_PRODUCTO
  BEFORE INSERT 
  ON LAROATLB_PRODUCTO
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_PRODUCTO),0)+1 INTO :NEW.ID_PRODUCTO
  FROM LAROATLB_PRODUCTO;
END;

CREATE OR REPLACE TRIGGER LAROATLB_ID_COMUNA_CLIENTE
  BEFORE INSERT 
  ON LAROATLB_COMUNA_CLIENTE
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_COMUNA),0)+1 INTO :NEW.ID_COMUNA
  FROM LAROATLB_COMUNA_CLIENTE;
END;


CREATE OR REPLACE TRIGGER LAROATLB_ID_SECRETARIA
  BEFORE INSERT
  ON LAROATLB_SECRETARIA
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_SECRE),0)+1 INTO :NEW.ID_SECRE
  FROM LAROATLB_SECRETARIA;
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

CREATE OR REPLACE TRIGGER LAROATLB_ID_REGION
  BEFORE INSERT 
  ON LAROATLB_REGION_CLIENTE
  FOR EACH ROW
DECLARE
BEGIN
  -- Obtener el valor máximo de ID_REGION y sumarle 1 para el nuevo registro
  SELECT NVL(MAX(ID_REGION), 0) + 1 INTO :NEW.ID_REGION
    FROM LAROATLB_REGION_CLIENTE;
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

CREATE OR REPLACE TRIGGER LAROATLB_ID_LOG_LOGIN
  BEFORE INSERT
  ON LAROATLB_LOG_LOGIN
  FOR EACH ROW
DECLARE
BEGIN
  SELECT NVL(MAX(ID_LOGIN),0)+1 INTO :NEW.ID_LOGIN
  FROM LAROATLB_LOG_LOGIN;
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

CREATE OR REPLACE TRIGGER LAROATLB_MAYUS_USUARIOS
BEFORE INSERT
ON LAROATLB_USUARIOS
FOR EACH ROW
BEGIN
    :NEW.NOMBRE_USUARIO := UPPER(:NEW.NOMBRE_USUARIO);
END;



--------------------------------------------------------------
--TRIGGER DESCUENTO DE STOCK

CREATE OR REPLACE TRIGGER TRG_DESCONTAR_STOCK
AFTER INSERT ON LAROATLB_DETALLE_PRODUCTO_TRATAMIENTO
FOR EACH ROW
BEGIN
    -- Actualizar el stock del producto
    UPDATE LAROATLB_PRODUCTO
    SET STOCK = STOCK - :NEW.CANTIDAD
    WHERE ID_PRODUCTO = :NEW.ID_PRODUCTO;
END;


