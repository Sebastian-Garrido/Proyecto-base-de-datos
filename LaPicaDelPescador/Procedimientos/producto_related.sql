-- PARECE QUE TODAS ESTAS FUNCIONES ESTAN LISTAS

-- AGREGAR PRODUCTO
CREATE OR REPLACE PROCEDURE AgregarProducto(
    p_nombre           IN VARCHAR2,
    p_descripcion      IN VARCHAR2,
    p_precio           IN INTEGER,
    p_tipo             IN VARCHAR2, -- 'Envasado' o 'Preparado'
    p_enstock          IN INTEGER DEFAULT NULL,
    p_enmarca          IN VARCHAR2 DEFAULT NULL,
    p_disponibilidad   IN NUMBER DEFAULT NULL
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
        PrTipo
    )
    VALUES 
    (
        NULL, 
        p_nombre, 
        p_descripcion, 
        p_precio, 
        p_tipo
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


-- EDITAR PRODUCTOS
CREATE OR REPLACE PROCEDURE EditarProducto(
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
        PrTipo = p_tipo
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
