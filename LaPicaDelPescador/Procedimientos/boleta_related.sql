-- CREAR DOCUMENTO TRIBUTARIO
CREATE OR REPLACE PROCEDURE CrearDocTrib(
    p_fecha_emision      IN DATE,
    p_hora_emision       IN DATE,
    p_vuelto             IN INTEGER,
    p_pago_propina       IN NUMBER,
    p_descuento          IN INTEGER DEFAULT NULL,
    p_tipo               IN VARCHAR2,
    p_pedido_numero      IN INTEGER,
    p_trabajador_id      IN INTEGER,
    p_empresa_id         IN INTEGER DEFAULT NULL
)
AS
BEGIN
    INSERT INTO DocTrib (
        DTNumeroOrden,
        DTFechaEmision,
        DTHoraEmision,
        DTVuelto,
        DTPagoPropina,
        DTDescuento,
        DTCompletada,
        DTTipo,
        Pedido_PeNumero,
        Trabajador_TrID,
        Empresa_EmID
    ) VALUES (
        NULL,                  -- DTNumeroOrden autoincremental por trigger
        p_fecha_emision,
        p_hora_emision,
        p_vuelto,
        p_pago_propina,
        p_descuento,
        0,                      -- No completada aun
        p_tipo,
        p_pedido_numero,
        p_trabajador_id,
        p_empresa_id
    );
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20001, 'No se pudo crear el documento tributario: ' || SQLERRM);
END CrearDocTrib;


-- MODIFICAR SI INCLUYE PROPINA
CREATE OR REPLACE PROCEDURE ModificarPropina
(
    p_DTNumeroOrden INTEGER,
    p_DTPagoPropina NUMBER
)
AS
BEGIN
    UPDATE DocTrib
    SET DTPagoPropina = p_DTPagoPropina
    WHERE DTNumeroOrden = p_DTNumeroOrden;

    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20002, 'No se encontró el documento tributario con ese número de orden.');
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20003, 'No se pudo modificar la propina: ' || SQLERRM);
END ModificarPropina;

-- Modificar Descuento
CREATE OR REPLACE PROCEDURE ModificarDescuento
(
    p_DTNumeroOrden INTEGER,
    p_DTDescuento INTEGER
)
AS
BEGIN
    UPDATE DocTrib
    SET DTDescuento = p_DTDescuento
    WHERE DTNumeroOrden = p_DTNumeroOrden

    IF SQL%ROWCOUNT = 0 THEN
    RAISE_APPLICATION_ERROR(-20004, 'No se encontró el documento tributario con ese número de orden.');
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20005, 'No se pudo modificar el descuento: ' || SQLERRM);
END ModificarDescuento;

-- ELIMINAR DESCUENTO
CREATE OR REPLACE PROCEDURE EliminarDescuento
(
    p_DTNumeroOrden INTEGER,
)
AS
BEGIN
    UPDATE DocTrib
    SET DTDescuento = 0
    WHERE DTNumeroOrden = p_DTNumeroOrden

    IF SQL%ROWCOUNT = 0 THEN
    RAISE_APPLICATION_ERROR(-20006, 'No se encontró el documento tributario con ese número de orden.');
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20007, 'No se pudo eliminar el descuento: ' || SQLERRM);
END EliminarDescuento;

-- AGREGAR DATOS DE PAGO

CREATE OR REPLACE PROCEDURE AgregarDatoPago
(
    p_DTNumeroOrden      IN INTEGER,
    p_DaPCantidadAbonada IN INTEGER,
    p_MetodoPago_MPID    IN INTEGER
)
AS
BEGIN
    INSERT INTO DatoPago (
        DaPID,
        DaPCantidadAbonada,
        MetodoPago_MPID,
        DocTrib_DTNumeroOrden
    ) VALUES (
        NULL, -- DaPID autoincremental por trigger
        p_DaPCantidadAbonada,
        p_MetodoPago_MPID,
        p_DTNumeroOrden
    );

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20008, 'No se pudo agregar el dato de pago: ' || SQLERRM);
END AgregarDatoPago;

-- ELIMINAR DATOS DE PAGO
CREATE OR REPLACE PROCEDURE EliminarDatoPago
(
    p_DaPID     IN INTEGER
)
AS
BEGIN
    DELETE FROM DatoPago
    WHERE DaPID = p_DaPID;

    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20009, 'No se encontró el dato de pago con ese ID.');
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20010, 'No se pudo eliminar el dato de pago: ' || SQLERRM);
END EliminarDatoPago;

-- UPDATEAR A BOLETA
CREATE OR REPLACE PROCEDURE CambiarABoleta
(
    p_DTNumeroOrden IN INTEGER
)
AS
BEGIN
    UPDATE DocTrib
    SET 
        DTTipo = 'Boleta'
    WHERE DTNumeroOrden = p_DTNumeroOrden;
    
    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20011, 'No se encontró el un documento tributario con ese ID.');
    END IF;
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20012, 'No se pudo eliminar el dato de pago: ' || SQLERRM);
END CambiarABoleta;

-- UPDATEAR A FACTURA
CREATE OR REPLACE PROCEDURE CambiarAFactura
(
    p_DTNumeroOrden IN INTEGER
)
AS
BEGIN
    UPDATE DocTrib
    SET 
        DTTipo = 'Factura'
    WHERE DTNumeroOrden = p_DTNumeroOrden;
    
    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20013, 'No se encontró el un documento tributario con ese ID.');
    END IF;
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20014, 'No se pudo eliminar el dato de pago: ' || SQLERRM);
END CambiarAFactura;


-- SELECCIONAR EMPRESA
CREATE OR REPLACE PROCEDURE SeleccionarEmpresa
(
    p_DTNumeroOrden     IN INTEGER,
    p_EmID              IN INTEGER
)
AS
BEGIN
    UPDATE DocTrib
    SET Empresa_EmID = p_EmID
    WHERE DTNumeroOrden = p_DTNumeroOrden;

    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20015, 'No se encontró el documento tributario con ese número de orden.');
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20016, 'No se pudo seleccionar la empresa: ' || SQLERRM);
END SeleccionarEmpresa;