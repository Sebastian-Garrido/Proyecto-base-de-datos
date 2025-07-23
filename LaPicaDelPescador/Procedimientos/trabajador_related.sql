-- PARECE QUE TODAS ESTAS FUNCIONES ESTAN LISTAS

-- AGREGAR UN TRABAJADOR
CREATE OR REPLACE PROCEDURE AgregarTrabajador(
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
BEGIN
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
    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20049, 'No se pudo agregar el trabajador: ' || SQLERRM);
END AgregarTrabajador;



-- MODIFICAR UN TRABAJADOR
CREATE OR REPLACE PROCEDURE ModificarTrabajador(
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


-- REGISTRAR INGRESO A HORARIO LABORAL
CREATE OR REPLACE PROCEDURE MarcarEntrada
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

-- REGISTRAR SALIDA
CREATE OR REPLACE PROCEDURE MarcarSalida
(
    p_HoID,
    p_HoEgreso
)
AS
BEGIN
    UPDATE HorasTrabajadas
    SET
        HoEgreso = p_HoEgreso
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

--CAMBIAR CONTRASEÑA
CREATE OR REPLACE PROCEDURE CambiarContraseña
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

--CAMBIAR CONTRASEÑA
CREATE OR REPLACE PROCEDURE RegistrarContraseña
(
    p_TrRUN IN INTEGER,
    p_TrContraseña IN VARCHAR2
)
AS
BEGIN
    UPDATE Trabajador
    SET
        TrContraseña = p_TrContraseña
    WHERE TrRUN = p_TrRUN AND TrContraseña = NULL;
    
    IF SQL%ROWCOUNT = 0 THEN
        RAISE_APPLICATION_ERROR(-20057, 'No se encontro un trabajador con este RUN.');
    END IF;

    COMMIT;
EXCEPTION
    WHEN OTHERS THEN
        ROLLBACK;
        RAISE_APPLICATION_ERROR(-20058, 'No se pudo registrar la contraseña para este trabajador: ' || SQLERRM);
END RegistrarContraseña;

