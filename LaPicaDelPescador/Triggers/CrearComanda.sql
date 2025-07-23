-- TRIGGER PARA CREAR COMANDA AL AGREGAR DETALLE DE PEDIDO
CREATE OR REPLACE TRIGGER CrearComandaAlAgregarDetalle
AFTER INSERT ON DetallePedido
FOR EACH ROW
BEGIN
    -- Crear una comanda por cada detalle de pedido insertado
    CrearComanda(
        p_CoHoraInicio => SYSDATE,
        p_DePID        => :NEW.DePID
    );
END;
