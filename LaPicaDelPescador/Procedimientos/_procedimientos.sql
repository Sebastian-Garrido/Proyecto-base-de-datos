CREATE OR REPLACE PROCEDURE RTHEARTLESS.AgregarDatoPago
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

CREATE OR REPLACE PROCEDURE RTHEARTLESS.AgregarLocal
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

CREATE OR REPLACE PROCEDURE RTHEARTLESS.AgregarMesa
(
    p_MeNumeroInterno IN INTEGER,
    p_MeActivo IN NUMBER,
    p_Local_LoID IN INTEGER
)
AS
BEGIN
    INSERT INTO Mesalocal
    (
        MeID,
        MeNumeroInterno,
        MeActivo,
        Local_LoID
    )
    VALUES
    (
        NULL,
        p_MeNumeroInterno,
        p_MeActivo,
        p_Local_LoID
    );
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20031, 'No se pudo agregar la mesa: ' || SQLERRM);
END AgregarMesa;

CREATE OR REPLACE PROCEDURE RTHEARTLESS.AgregarProducto(
    p_nombre           IN VARCHAR2,
    p_descripcion      IN VARCHAR2,
    p_precio           IN INTEGER,
    p_tipo             IN VARCHAR2, -- 'Envasado' o 'Preparado'
    p_enstock          IN INTEGER DEFAULT NULL,
    p_enmarca          IN VARCHAR2 DEFAULT NULL,
    p_disponibilidad   IN NUMBER DEFAULT NULL,
    p_local_ID         IN INTEGER  
)
AS
    v_prid INTEGER;
BEGIN
    -- Insertar en Producto
    INSERT INTO Producto 
    (
        PrID, 
        PrNombre,
        PrDescripcion, 
        PrPrecio, 
        PrTipo,
        Local_LoID
    )
    VALUES 
    (
        NULL, 
        p_nombre, 
        p_descripcion, 
        p_precio, 
        p_tipo,
        p_local_ID
    );

    -- Obtener el último PrID insertado
    SELECT MAX(PrID) INTO v_prid FROM Producto WHERE PrNombre = p_nombre AND PrDescripcion = p_descripcion;

    -- Insertar en tabla hija según tipo
    IF p_tipo = 'Envasado' THEN
        INSERT INTO Envasado (PrID, EnStock, EnMarca)
        VALUES (v_prid, p_enstock, p_enmarca);
    ELSIF p_tipo = 'Preparado' THEN
        INSERT INTO Platillo_Preparado (PrID, PPDisponibilidad)
        VALUES (v_prid, p_disponibilidad);
    ELSE
        RAISE_APPLICATION_ERROR(-20044, 'Tipo de producto no válido');
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20045, 'No se pudo agregar el producto: ' || SQLERRM);
END AgregarProducto;

CREATE OR REPLACE PROCEDURE RTHEARTLESS.AgregarTrabajador(
    p_TrRUN                IN INTEGER,
    p_TrTelefono           IN INTEGER DEFAULT NULL,
    p_TrCorreo             IN VARCHAR2 DEFAULT NULL,
    p_TrCargo              IN VARCHAR2,
    p_TrContraseña         IN VARCHAR2 DEFAULT NULL,
    p_TrFechaNacimiento    IN DATE,
    p_TrSueldoHora         IN INTEGER,
    p_TrNombres            IN VARCHAR2,
    p_TrApellidoPaterno    IN VARCHAR2,
    p_TrApellidoMaterno    IN VARCHAR2,
    p_TrVigente            IN NUMBER,
    p_TrRegion             IN VARCHAR2,
    p_TrComuna             IN VARCHAR2,
    p_TrCalle              IN VARCHAR2,
    p_TrNumeroCalle        IN VARCHAR2,
    p_TrDireccionAdicional IN VARCHAR2 DEFAULT NULL,
    p_Local_LoID           IN INTEGER
)
AS
    v_trid INTEGER;
BEGIN
    -- Insertar trabajador
    INSERT INTO Trabajador (
        TrID,
        TrRUN,
        TrTelefono,
        TrCorreo,
        TrCargo,
        TrContraseña,
        TrFechaNacimiento,
        TrSueldoHora,
        TrNombres,
        TrApellidoPaterno,
        TrApellidoMaterno,
        TrVigente,
        TrRegion,
        TrComuna,
        TrCalle,
        TrNumeroCalle,
        TrDireccionAdicional,
        Local_LoID
    ) VALUES (
        NULL,
        p_TrRUN,
        p_TrTelefono,
        p_TrCorreo,
        p_TrCargo,
        p_TrContraseña,
        p_TrFechaNacimiento,
        p_TrSueldoHora,
        p_TrNombres,
        p_TrApellidoPaterno,
        p_TrApellidoMaterno,
        p_TrVigente,
        p_TrRegion,
        p_TrComuna,
        p_TrCalle,
        p_TrNumeroCalle,
        p_TrDireccionAdicional,
        p_Local_LoID
    );

    -- Obtener el ID recién insertado
    SELECT MAX(TrID) INTO v_trid FROM Trabajador WHERE TrRUN = p_TrRUN;

    -- Llamar a CrearFechaDetalle con la fecha actual y el ID
    RTHEARTLESS.CrearFechaDetalle(SYSDATE, v_trid);

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20049, 'No se pudo agregar el trabajador: ' || SQLERRM);
END AgregarTrabajador;

CREATE OR REPLACE PROCEDURE RTHEARTLESS.CambiarABoleta
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

CREATE OR REPLACE PROCEDURE RTHEARTLESS.CambiarAFactura
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

CREATE OR REPLACE PROCEDURE RTHEARTLESS.CambiarContraseña
(
    p_TrID IN INTEGER,
    p_TrContraseña IN VARCHAR2
)
AS
BEGIN
    UPDATE Trabajador
    SET
        TrContraseña = p_TrContraseña
    WHERE TrID = p_TrID;
    
    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20055, 'No se encontro un trabajador con este ID.');
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20056, 'No se pudo modificar la contraseña del trabajador: ' || SQLERRM);
END CambiarContraseña;

CREATE OR REPLACE PROCEDURE RTHEARTLESS.CrearComanda
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
        NULL,
        p_DePID
    );
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20017, 'No se pudo crear comanda: ' || SQLERRM);
END CrearComanda;

CREATE OR REPLACE PROCEDURE RTHEARTLESS.CrearDetallePedido
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

CREATE OR REPLACE PROCEDURE RTHEARTLESS.CrearDocTrib(
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

CREATE OR REPLACE PROCEDURE RTHEARTLESS.CrearEmpresa
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
    INSERT INTO Empresa
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
    );
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20020, 'No se pudo agregar la empresa: ' || SQLERRM);
END CrearEmpresa;

CREATE OR REPLACE PROCEDURE RTHEARTLESS.CrearFechaDetalle
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

CREATE OR REPLACE PROCEDURE RTHEARTLESS.CrearMetodoPago
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
    );
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20034, 'No se pudo agregar el metodo de pago: ' || SQLERRM);
END CrearMetodoPago;

CREATE OR REPLACE PROCEDURE RTHEARTLESS.CrearPago
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
    );
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20037, 'No se pudo añadir el pago: ' || SQLERRM);
END CrearPago;

CREATE OR REPLACE PROCEDURE RTHEARTLESS.CrearPedido
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
        MesaLocal_MeID,
        Trabajador_TrID
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

CREATE OR REPLACE PROCEDURE RTHEARTLESS.EditarEmpresa
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
    UPDATE Empresa
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

CREATE OR REPLACE PROCEDURE RTHEARTLESS.EditarLocal
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
        LoCalle = p_calle,
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

CREATE OR REPLACE PROCEDURE RTHEARTLESS.EditarMesa
(
    p_MeID              IN INTEGER,
    p_MeNumeroInterno   IN INTEGER,
    p_MeActivo          IN NUMBER
)
AS
BEGIN
    UPDATE MesaLocal
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
END EditarMesa;


-- PARECE QUE TODAS ESTAS FUNCIONES ESTAN LISTAS;

CREATE OR REPLACE PROCEDURE RTHEARTLESS.EditarMetodoPago
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
        RAISE_APPLICATION_ERROR(-20036, 'No se pudo editar el metodo de pago: ' || SQLERRM);
END EditarMetodoPago;

CREATE OR REPLACE PROCEDURE RTHEARTLESS.EditarProducto
(
    p_prid            IN INTEGER,
    p_nombre          IN VARCHAR2,
    p_descripcion     IN VARCHAR2,
    p_precio          IN INTEGER,
    p_tipo            IN VARCHAR2, -- 'Envasado' o 'Preparado'
    p_enstock         IN INTEGER DEFAULT NULL,
    p_enmarca         IN VARCHAR2 DEFAULT NULL,
    p_disponibilidad  IN NUMBER DEFAULT NULL
)
AS
    v_tipo_actual VARCHAR2(256);
BEGIN
    -- Obtener el tipo actual del producto
    SELECT PrTipo INTO v_tipo_actual FROM Producto WHERE PrID = p_prid;

    -- Actualizar Producto
    UPDATE Producto
    SET PrNombre = p_nombre,
        PrDescripcion = p_descripcion,
        PrPrecio = p_precio,
        PrTipo = p_tipo,
    WHERE PrID = p_prid;

    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20046, 'No se encontró el producto con ese ID.');
    END IF;

    -- Si el tipo cambió, eliminar el registro de la tabla hija anterior
    IF v_tipo_actual <> p_tipo THEN
        DELETE FROM Envasado WHERE PrID = p_prid;
        DELETE FROM Platillo_Preparado WHERE PrID = p_prid;
    END IF;

    -- Insertar o actualizar en la tabla hija según el tipo
    IF p_tipo = 'Envasado' THEN
        MERGE INTO Envasado e
        USING (SELECT p_prid AS PrID FROM dual) src
        ON (e.PrID = src.PrID)
        WHEN MATCHED THEN
            UPDATE SET EnStock = p_enstock, EnMarca = p_enmarca
        WHEN NOT MATCHED THEN
            INSERT (PrID, EnStock, EnMarca) VALUES (p_prid, p_enstock, p_enmarca);
    ELSIF p_tipo = 'Preparado' THEN
        MERGE INTO Platillo_Preparado pp
        USING (SELECT p_prid AS PrID FROM dual) src
        ON (pp.PrID = src.PrID)
        WHEN MATCHED THEN
            UPDATE SET PPDisponibilidad = p_disponibilidad
        WHEN NOT MATCHED THEN
            INSERT (PrID, PPDisponibilidad) VALUES (p_prid, p_disponibilidad);
    ELSE
        RAISE_APPLICATION_ERROR(-20047, 'Tipo de producto no válido');
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20048, 'No se pudo editar el producto: ' || SQLERRM);
END EditarProducto;

CREATE OR REPLACE PROCEDURE RTHEARTLESS.EgresarFechaDetalle
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

--CREATE OR REPLACE PROCEDURE RTHEARTLESS.EliminarDatoPago
--(
--    p_DaPID     IN INTEGER
--)
--AS
--BEGIN
--    DELETE FROM DatoPago
--    WHERE DaPID = p_DaPID;

--    IF SQL%ROWCOUNT = 0 THEN
--        RAISE_APPLICATION_ERROR(-20009, 'No se encontró el dato de pago con ese ID.');
--    END IF;

--    COMMIT;
--EXCEPTION
--   WHEN OTHERS THEN
--        ROLLBACK;
--        RAISE_APPLICATION_ERROR(-20010, 'No se pudo eliminar el dato de pago: ' || SQLERRM);
--END EliminarDatoPago;

CREATE OR REPLACE PROCEDURE RTHEARTLESS.EliminarDescuento
(
    p_DTNumeroOrden INTEGER
)
AS
BEGIN
    UPDATE DocTrib
    SET DTDescuento = 0
    WHERE DTNumeroOrden = p_DTNumeroOrden;

    IF SQL%ROWCOUNT = 0 THEN
    RAISE_APPLICATION_ERROR(-20006, 'No se encontró el documento tributario con ese número de orden.');
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20007, 'No se pudo eliminar el descuento: ' || SQLERRM);
END EliminarDescuento;

CREATE OR REPLACE PROCEDURE RTHEARTLESS.EliminarPago
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

CREATE OR REPLACE PROCEDURE RTHEARTLESS.FinalizarComanda
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

CREATE OR REPLACE PROCEDURE RTHEARTLESS.FinalizarPedido
(
    p_PeNumero IN INTEGER
)
AS
BEGIN
    UPDATE Pedido
    SET PeEstado = 1 --PEDIDO FINALIZADO
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

CREATE OR REPLACE PROCEDURE RTHEARTLESS.MarcarEntrada
(
    p_HoIngreso IN DATE,
    p_HoFechaRegistro IN DATE,
    p_TrID IN INTEGER
)
AS
BEGIN
    INSERT INTO HorasTrabajadas
    (
        HoID,
        HoIngreso,
        HoEgreso,
        HoFechaRegistro,
        Trabajador_TrID
    )
    VALUES
    (
        NULL,
        p_HoIngreso,
        NULL,
        p_HoFechaRegistro,
        p_TrID
    );
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20052, 'No se pudo registrar la hora: ' || SQLERRM);
END MarcarEntrada;

CREATE OR REPLACE PROCEDURE RTHEARTLESS.MarcarSalida
(
    p_HoID IN INTEGER,
    p_HoEgreso IN DATE
)
AS
BEGIN
    UPDATE HorasTrabajadas
    SET HoEgreso = p_HoEgreso
    WHERE HoID = p_HoID;

    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20053, 'No se encontro un horario para modificar con ese ID.');
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20054, 'No se pudo modificar el horario de trabajo: ' || SQLERRM);
END MarcarSalida;

CREATE OR REPLACE PROCEDURE RTHEARTLESS.ModificarDescuento
(
    p_DTNumeroOrden INTEGER,
    p_DTDescuento INTEGER
)
AS
BEGIN
    UPDATE DocTrib
    SET DTDescuento = p_DTDescuento
    WHERE DTNumeroOrden = p_DTNumeroOrden;

    IF SQL%ROWCOUNT = 0 THEN
    RAISE_APPLICATION_ERROR(-20004, 'No se encontró el documento tributario con ese número de orden.');
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20005, 'No se pudo modificar el descuento: ' || SQLERRM);
END ModificarDescuento;

CREATE OR REPLACE PROCEDURE RTHEARTLESS.ModificarPropina
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

CREATE OR REPLACE PROCEDURE RTHEARTLESS.ModificarTrabajador(
    p_trid              IN INTEGER,
    p_run               IN INTEGER,
    p_telefono          IN INTEGER,
    p_correo            IN VARCHAR2,
    p_cargo             IN VARCHAR2,
    p_birth             IN DATE,
    p_sueldo_hora       IN INTEGER,
    p_nombres           IN VARCHAR2,
    p_apellidop         IN VARCHAR2,
    p_apellidom         IN VARCHAR2,
    p_region            IN VARCHAR2,
    p_comuna            IN VARCHAR2,
    p_calle             IN VARCHAR2,
    p_local             IN INTEGER,
    p_numero_calle      IN VARCHAR2,
    p_direccion_adic    IN VARCHAR2
)
AS
BEGIN
    UPDATE Trabajador
    SET
        TrRUN = p_run,
        TrTelefono = p_telefono,
        TrCorreo = p_correo,
        TrCargo = p_cargo,
        TrFechaNacimiento = p_birth,
        TrSueldoHora = p_sueldo_hora,
        TrNombres  = p_nombres,
        TrApellidoPaterno  = p_apellidop,
        TrApellidoMaterno  = p_apellidom,
        TrRegion = p_region,
        TrComuna = p_comuna,
        Local_LoID = p_local,
        TrCalle = p_calle,
        TrNumeroCalle = p_numero_calle,
        TrDireccionAdicional = p_direccion_adic
    WHERE TrID = p_trid;

    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20050, 'No se encontró trabajador con ese ID.');
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20051, 'No se pudo modificar el trabajador: ' || SQLERRM);
END ModificarTrabajador;

CREATE OR REPLACE PROCEDURE RTHEARTLESS.SeleccionarEmpresa
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

--CAMBIAR CONTRASEÑA
CREATE OR REPLACE PROCEDURE RegistrarContraseña
(
    p_TrRUN IN INTEGER,
    p_TrContraseña IN VARCHAR2
)
AS
    v_actual VARCHAR2(256);
BEGIN
    -- Consultar la contraseña actual
    SELECT TrContraseña INTO v_actual FROM Trabajador WHERE TrRUN = p_TrRUN;

    IF v_actual IS NULL THEN
        UPDATE Trabajador
        SET TrContraseña = p_TrContraseña
        WHERE TrRUN = p_TrRUN;

        IF SQL%ROWCOUNT = 0 THEN
            RAISE_APPLICATION_ERROR(-20057, 'No se encontro un trabajador con este RUN.');
        END IF;
    ELSE
        RAISE_APPLICATION_ERROR(-20059, 'La contraseña ya fue registrada previamente.');
    END IF;

    COMMIT;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        RAISE_APPLICATION_ERROR(-20060, 'No existe trabajador con ese RUN.');
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20058, 'No se pudo registrar la contraseña para este trabajador: ' || SQLERRM);
END RegistrarContraseña;