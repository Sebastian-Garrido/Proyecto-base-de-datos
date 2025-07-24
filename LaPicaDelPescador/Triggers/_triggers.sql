-- TRIGGER PARA INSERT Y DELETE EN DATOPAGO
CREATE OR REPLACE TRIGGER ActualizarVueltoBoleta
AFTER INSERT OR DELETE ON DatoPago
FOR EACH ROW
DECLARE
    v_total_boleta INT := 0;
    v_total_pago INT := 0;
    v_pedido_numero INT;
    v_incluir_propina NUMBER;
    v_descuento INT;
BEGIN
    -- Obtener el pedido asociado a la boleta
    SELECT Pedido_PeNumero, DTPagoPropina, DTDescuento INTO v_pedido_numero, v_incluir_propina, v_descuento
    FROM DocTrib
    WHERE DTNumeroOrden = NVL(:NEW.DocTrib_DTNumeroOrden, :OLD.DocTrib_DTNumeroOrden);

    -- Calcular el total de la boleta (suma de los productos del pedido)
    SELECT SUM(DePCantidad * DePPrecioUnitario) INTO v_total_boleta
    FROM DetallePedido
    WHERE Pedido_PeNumero = v_pedido_numero;
    
    -- Si la boleta incluye propina, sumarla al total
    IF v_incluir_propina = 1 THEN
        SELECT DTPagoPropina INTO v_incluir_propina
        FROM DocTrib
        WHERE DTNumeroOrden = NVL(:NEW.DocTrib_DTNumeroOrden, :OLD.DocTrib_DTNumeroOrden);
        v_total_boleta := v_total_boleta + v_incluir_propina;
    END IF;

    -- Restar el descuento existente (independiente del valor)
    v_total_boleta := v_total_boleta - v_descuento;

    -- Calcular la sumatoria de todos los pagos asociados a la boleta
    SELECT NVL(SUM(DaPCantidadAbonada), 0) INTO v_total_pago
    FROM DatoPago
    WHERE DocTrib_DTNumeroOrden = NVL(:NEW.DocTrib_DTNumeroOrden, :OLD.DocTrib_DTNumeroOrden);

    -- Actualizar el vuelto
    UPDATE DocTrib
    SET DTVuelto = CASE WHEN v_total_pago > v_total_boleta THEN v_total_pago - v_total_boleta ELSE 0 END
    WHERE DTNumeroOrden = NVL(:NEW.DocTrib_DTNumeroOrden, :OLD.DocTrib_DTNumeroOrden);
END;


-- NO SE OCUPA, HASTA SABER PQ FALLA

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
