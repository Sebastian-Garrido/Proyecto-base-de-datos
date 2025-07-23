-- PARECE QUE TODAS ESTAS FUNCIONES ESTAN LISTAS

--CREAR COMANDA
CREATE OR REPLACE PROCEDURE CrearComanda
(
    p_CoHoraInicio IN DATE,
    p_DePID IN INTEGER
)
AS
BEGIN
    INSERT INTO Comanda
    (
        CoNumero,
        CoEstado,
        CoHoraInicio,
        CoHoraFinal,
        DetallePedido_DePID
    )
    VALUES
    (
        NULL,
        0, -- ESTADO = 0 SIGNIFICA NO FINALIZADA
        p_CoHoraInicio,
        NULL
        p_DePID
    );
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20017, 'No se pudo crear comanda: ' || SQLERRM);
END CrearComanda;


-- FINALIZAR COMANDA
CREATE OR REPLACE PROCEDURE FinalizarComanda
(
    p_CoNumero IN INTEGER,
    p_CoHoraFinal IN DATE
)
AS
BEGIN
    UPDATE Comanda
    SET
        CoHoraFinal = p_CoHoraFinal,
        CoEstado = 1 --ESTADO = 1 SIGNIFICA FINALIZADA
    WHERE CoNumero = p_CoNumero;
   
   IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20018, 'No se encontró una comanda con ese ID.');
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20019, 'No se pudo finalizar la comanda: ' || SQLERRM);
END FinalizarComanda;


