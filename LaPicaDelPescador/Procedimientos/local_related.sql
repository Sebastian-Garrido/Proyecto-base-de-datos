-- PARECE QUE TODAS ESTAS FUNCIONES ESTAN LISTAS

-- AGREGAR LOCAL
CREATE OR REPLACE PROCEDURE AgregarLocal
(
    p_horario_apertura IN DATE,
    p_horario_cierre IN DATE,
    p_region IN VARCHAR2,
    p_comuna IN VARCHAR2,
    p_calle IN VARCHAR2,
    p_ncalle IN VARCHAR2,
    p_activo IN NUMBER
)
AS
BEGIN
    INSERT INTO Local
    (
        LoHoraApertura,
        LoHoraCierre,
        LoRegion,
        LoComuna,
        LoCalle,
        LoNumeroCalle,
        LoActivo
    )
    VALUES
    (
        p_horario_apertura,
        p_horario_cierre,
        p_region,
        p_comuna,
        p_calle,
        p_ncalle,
        p_activo
    );
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20028, 'No se pudo realizar el pedido: ' || SQLERRM);
END AgregarLocal;

-- EDITAR LOCAL
CREATE OR REPLACE PROCEDURE EditarLocal
(
    p_LoID IN INTEGER,
    p_horario_apertura IN DATE,
    p_horario_cierre IN DATE,
    p_region IN VARCHAR2,
    p_comuna IN VARCHAR2,
    p_calle IN VARCHAR2,
    p_ncalle IN VARCHAR2,
    p_activo IN NUMBER
)
AS
BEGIN
    UPDATE Local
    SET
        LoHoraApertura = p_horario_apertura,
        LoHoraCierre = p_horario_cierre,
        LoRegion = p_region,
        LoComuna = p_comuna,
        LoCalle = p_calle
        LoNumeroCalle = p_ncalle,
        LoActivo = p_activo
    WHERE LoID = p_LoID;
    
    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20029, 'No se encontró un local con ese ID.');
    END IF;

    COMMIT;

EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20030, 'No se pudo editar el local: ' || SQLERRM);
END EditarLocal;


-- AGREGAR MESA
CREATE OR REPLACE PROCEDURE AgregarMesa
(
    p_MeNumeroInterno IN INTEGER,
    p_MeActivo IN NUMBER,
    p_Local_LoID IN INTEGER
)
AS
BEGIN
    INSERT INTO Mesa
    (
        MeID,
        MeNumeroInterno,
        Local_LoID
    )
    VALUES
    (
        NULL,
        p_MeNumeroInterno,
        p_Local_LoID
    );
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20031, 'No se pudo agregar la mesa: ' || SQLERRM);
END AgregarMesa;

-- EDITAR MESA
CREATE OR REPLACE PROCEDURE EditarMesa
(
    p_MeID              IN INTEGER,
    p_MeNumeroInterno   IN INTEGER,
    p_MeActivo          IN NUMBER
)
AS
BEGIN
    UPDATE Mesa
    SET
        MeNumeroInterno = p_MeNumeroInterno,
        MeActivo = p_MeActivo
    WHERE MeID = p_MeID;

    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20032, 'No se encontró una mesa con ese ID.');
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20033, 'No se pudo editar la mesa: ' || SQLERRM);
END