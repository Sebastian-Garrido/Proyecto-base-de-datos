-- PARECE QUE TODAS ESTAS FUNCIONES ESTAN LISTAS

--CREAR METODO DE PAGO
CREATE OR REPLACE PROCEDURE CrearMetodoPago
(
    p_MedioDePago IN VARCHAR2
)
AS
BEGIN
    INSERT INTO MetodoPago
    (
        MPID,
        MPMedioDePago
    )
    VALUES
    (
        NULL,
        p_MedioDePago
    )
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20034, 'No se pudo agregar el metodo de pago: ' || SQLERRM);
END CrearMetodoPago;


--EDITAR METODO DE PAGO
CREATE OR REPLACE PROCEDURE EditarMetodoPago
(
    p_MPID IN INTEGER,
    p_MedioDePago IN VARCHAR2
)
AS
BEGIN
    UPDATE MetodoPago
    SET
        MPMedioDePago = p_MedioDePago
    WHERE MPID = p_MPID;

    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20035, 'No se encontró un metodo de pago con ese ID.');
    END IF;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20036, 'No se pudo editar el metodo de pago: ' || SQLERRM)
END EditarMetodoPago;


--AGREGAR PAGO
CREATE OR REPLACE PROCEDURE CrearPago
(
    p_CantidadAbonada IN INTEGER,
    p_MPID IN INTEGER,
    p_DTNumeroOrden IN INTEGER
)
AS 
BEGIN
    INSERT INTO DatoPago
    (
        DaPID,
        DaPCantidadAbonada,
        MetodoPago_MPID,
        DocTrib_DTNumeroOrden
    )
    VALUES
    (
        NULL,
        p_CantidadAbonada,
        p_MPID,
        p_DTNumeroOrden
    )
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20037, 'No se pudo añadir el pago: ' || SQLERRM)
END CrearPago;


--ELIMINAR PAGO
CREATE OR REPLACE PROCEDURE EliminarPago
(
    p_DaPID IN INTEGER,
    p_DTNumeroOrden IN INTEGER
)
AS
BEGIN
    DELETE FROM DatoPago
    WHERE DaPID = p_DaPID AND DocTrib_DTNumeroOrden = p_DTNumeroOrden;

    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20038, 'No se encontró un pago con esos datos.');
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20039, 'No se pudo eliminar el pago: ' || SQLERRM);
END EliminarPago;
