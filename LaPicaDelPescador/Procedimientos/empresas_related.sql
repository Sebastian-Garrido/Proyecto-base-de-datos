-- PARECE QUE TODAS ESTAS FUNCIONES ESTAN LISTAS

--CREAR EMPRESA
CREATE OR REPLACE PROCEDURE CrearEmpresa
(
    p_EmRUT IN VARCHAR2,
    p_EmNombre IN VARCHAR2,
    p_EmCorreo IN VARCHAR2,
    p_EmTelefono IN INTEGER,
    p_EmRegion IN VARCHAR2,
    p_EmComuna IN VARCHAR2,
    p_EmCalle IN VARCHAR2,
    p_EmNumeroCalle IN VARCHAR2
)
AS
BEGIN
    INSERT IN Empresa
    (
        EmID,
        EmRUT,
        EmNombre,
        EmCorreo,
        EmTelefono,
        EmRegion,
        EmComuna,
        EmCalle,
        EmNumeroCalle
    )
    VALUES
    (
        NULL,
        p_EmRUT,
        p_EmNombre,
        p_EmCorreo,
        p_EmTelefono,
        p_EmRegion,
        p_EmComuna,
        p_EmCalle,
        p_EmNumeroCalle
    )
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20020, 'No se pudo agregar la empresa: ' || SQLERRM);
END CrearEmpresa;


--EDITAR EMPRESA
CREATE OR REPLACE PROCEDURE EditarEmpresa
(
    p_EmID IN INTEGER,
    p_EmRUT IN VARCHAR2,
    p_EmNombre IN VARCHAR2,
    p_EmCorreo IN VARCHAR2,
    p_EmTelefono IN INTEGER,
    p_EmRegion IN VARCHAR2,
    p_EmComuna IN VARCHAR2,
    p_EmCalle IN VARCHAR2,
    p_EmNumeroCalle IN VARCHAR2
)
AS
BEGIN
    UPDATE Empleado
    SET
        EmRUT = p_EmRUT,
        EmNombre = p_EmNombre,
        EmCorreo = p_EmCorreo,
        EmTelefono = p_EmTelefono,
        EmRegion = p_EmRegion,
        EmComuna = p_EmComuna,
        EmCalle = p_EmCalle,
        EmNumeroCalle = p_EmNumeroCalle
    WHERE EmID = p_EmID;

    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20021, 'No se encontro una empresa con ese ID.');
    END IF;

    COMMIT;

EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20022, 'No se pudo editar la empresa: ' || SQLERRM);
END EditarEmpresa;


