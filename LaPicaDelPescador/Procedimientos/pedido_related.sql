-- PARECE QUE TODAS ESTAS FUNCIONES ESTAN LISTAS

--Crear Pedido
CREATE OR REPLACE PROCEDURE CrearPedido
(
    p_peFechaEmision IN DATE,
    p_peHoraEmision IN DATE,
    p_MesaID IN INTEGER,
    p_TrabajadorID IN INTEGER
    
)
AS
BEGIN
    INSERT INTO Pedido
    (
        PeNumero,
        PeFechaEmision,
        PeHoraEmision,
        PeEstado,
        MeID,
        TrID
    )
    VALUES
    (
        NULL,
        p_peFechaEmision,
        p_peHoraEmision,
        0,  -- EN CURSO
        p_MesaID,
        p_TrabajadorID
    );
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20040, 'No se pudo realizar el pedido: ' || SQLERRM);
END CrearPedido;


--FINALIZAR PEDIDO
CREATE OR REPLACE PROCEDURE FinalizarPedido
(
    p_PeNumero
)
AS
BEGIN
    UPDATE Pedido
    SET
        PeEstado = 1 --PEDIDO FINALIZADO
    WHERE PeNumero = p_PeNumero;

    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20041, 'No se encontró una comanda con ese ID.');
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20042, 'No se pudo finalizar la comanda: ' || SQLERRM);
END FinalizarPedido;



--CREAR DETALLE PEDIDO (usado en la creacion de un pedido asi como en la edicion de un pedido)
CREATE OR REPLACE PROCEDURE CrearDetallePedido
(
    p_DePCantidad IN INTEGER,
    p_DePPrecioUnitario IN INTEGER,
    p_PeNumero IN INTEGER,
    p_PrID IN INTEGER
)
AS
BEGIN
    INSERT INTO DetallePedido
    (
        DePID,
        DePCantidad,
        DePPrecioUnitario,
        Pedido_PeNumero,
        Producto_PrID
    )
    VALUES
    (
        NULL,
        p_DePCantidad,
        p_DePPrecioUnitario,
        p_PeNumero,
        p_PrID
    );
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20043, 'No se pudo añadir detalle de pedido: ' || SQLERRM);
END CrearDetallePedido;

