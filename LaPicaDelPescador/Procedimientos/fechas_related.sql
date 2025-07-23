-- PARECE QUE TODAS ESTAS FUNCIONES ESTAN LISTAS

--CREAR DETALLE DE FECHA PARA TRABAJADOR CUANDO INGRESA A LA EMPRESA
CREATE OR REPLACE PROCEDURE CrearFechaDetalle
(
    p_FeFechaIngreso IN DATE,
    p_TrID IN INTEGER
)
AS
BEGIN
    INSERT INTO FechaDetalle
    (
        FeID,
        FeFechaIngreso,
        FeFechaEgreso,
        Trabajador_TrID
    )
    VALUES
    (
        NULL,
        p_FeFechaIngreso,
        NULL,
        p_TrID
    );
    
    UPDATE Trabajador
    SET
        TrVigente = 1
    WHERE TrID = p_TrID;

    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20023, 'No se encontró un trabajador con ese ID.');
    END IF;

    COMMIT;

EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20024, 'No se pudo agregar el detalle de la fecha de usuario: ' || SQLERRM);
END CrearFechaDetalle;


--EGRESAR A UN TRABAJADOR DE LA EMPRESA
CREATE OR REPLACE PROCEDURE EgresarFechaDetalle
(
    p_FeID IN INTEGER,
    p_FeFechaEgreso IN DATE,
    p_TrID IN INTEGER
)
AS
BEGIN
    UPDATE FechaDetalle
    SET 
        FeFechaEgreso = p_FeFechaEgreso
    WHERE FeID = p_FeID;

    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20025, 'No se encontró un detalle de fecha con ese ID.');
    END IF;

    UPDATE Trabajador
    SET
        TrVigente = 0
    WHERE TrID = p_TrID;

    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20026, 'No se encontró un trabajador con ese ID.');
    END IF;
    
    COMMIT;

EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20027, 'No se pudo egresar al trabajador: ' || SQLERRM);
END EgresarFechaDetalle;
