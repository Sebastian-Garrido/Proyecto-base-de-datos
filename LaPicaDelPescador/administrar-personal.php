<?php
        include "php_scripts\configs_oracle\config_pdo.php";

        // Procesar formulario agregar personal y fechas ingreso/egreso
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Agregar trabajador
            if (isset($_POST['agregar_personal'])) {
                $run = $_POST['run'] ?? '';
                $telefono = $_POST['telefono'] ?? null;
                $correo = $_POST['correo'] ?? null;
                $cargo = $_POST['cargo'] ?? '';
                $password = $_POST['password'] ?? null;
                $fechaNacimiento = $_POST['fechaNacimiento'] ?? '';
                $sueldoHora = $_POST['sueldoHora'] ?? '';
                $nombres = $_POST['nombres'] ?? '';
                $apellidoPaterno = $_POST['apellidoPaterno'] ?? '';
                $apellidoMaterno = $_POST['apellidoMaterno'] ?? '';
                $vigente = ($_POST['vigencia'] ?? 'si') == 'si' ? 1 : 0;
                $region = $_POST['regionCascada'] ?? '';
                $comuna = $_POST['comunaCascada'] ?? '';
                $calle = $_POST['calle'] ?? '';
                $numeroCalle = $_POST['numeroCalle'] ?? '';
                $direccionAdicional = $_POST['direccionAdicional'] ?? null;
                $local = $_POST['localCascada'] ?? '';
                $sql = "CALL AgregarTrabajador(:p_TrRUN, :p_TrTelefono, :p_TrCorreo, :p_TrCargo, :p_TrContraseña, TO_DATE(:p_TrFechaNacimiento, 'YYYY-MM-DD'), :p_TrSueldoHora, :p_TrNombres, :p_TrApellidoPaterno, :p_TrApellidoMaterno, :p_TrVigente, :p_TrRegion, :p_TrComuna, :p_TrCalle, :p_TrNumeroCalle, :p_TrDireccionAdicional, :p_Local_LoID)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':p_TrRUN', $run, PDO::PARAM_INT);
                $stmt->bindParam(':p_TrTelefono', $telefono, PDO::PARAM_INT);
                $stmt->bindParam(':p_TrCorreo', $correo);
                $stmt->bindParam(':p_TrCargo', $cargo);
                $stmt->bindParam(':p_TrContraseña', $password);
                $stmt->bindParam(':p_TrFechaNacimiento', $fechaNacimiento);
                $stmt->bindParam(':p_TrSueldoHora', $sueldoHora, PDO::PARAM_INT);
                $stmt->bindParam(':p_TrNombres', $nombres);
                $stmt->bindParam(':p_TrApellidoPaterno', $apellidoPaterno);
                $stmt->bindParam(':p_TrApellidoMaterno', $apellidoMaterno);
                $stmt->bindParam(':p_TrVigente', $vigente, PDO::PARAM_INT);
                $stmt->bindParam(':p_TrRegion', $region);
                $stmt->bindParam(':p_TrComuna', $comuna);
                $stmt->bindParam(':p_TrCalle', $calle);
                $stmt->bindParam(':p_TrNumeroCalle', $numeroCalle);
                $stmt->bindParam(':p_TrDireccionAdicional', $direccionAdicional);
                $stmt->bindParam(':p_Local_LoID', $local, PDO::PARAM_INT);
                $stmt->execute();
                echo '<script>window.location.href = "administrar-personal.php";</script>';
                exit;
            }
            // Editar trabajador
            if (isset($_POST['editar_personal'])) {
                $trid = $_POST['editId'] ?? '';
                $run = $_POST['editRun'] ?? '';
                $telefono = $_POST['editTelefono'] ?? null;
                $correo = $_POST['editCorreo'] ?? null;
                $cargo = $_POST['editCargo'] ?? '';
                $fechaNacimiento = $_POST['editFechaNacimiento'] ?? '';
                $sueldoHora = $_POST['editSueldoHora'] ?? '';
                $nombres = $_POST['editNombres'] ?? '';
                $apellidoPaterno = $_POST['editApellidoPaterno'] ?? '';
                $apellidoMaterno = $_POST['editApellidoMaterno'] ?? '';
                $region = $_POST['editRegion'] ?? '';
                $comuna = $_POST['editComuna'] ?? '';
                $calle = $_POST['editCalle'] ?? '';
                $local = $_POST['editLocal'] ?? '';
                $numeroCalle = $_POST['editNumeroCalle'] ?? '';
                $direccionAdic = $_POST['editDireccionAdicional'] ?? null;
                $sql = "CALL ModificarTrabajador(:p_trid, :p_run, :p_telefono, :p_correo, :p_cargo, TO_DATE(:p_birth, 'YYYY-MM-DD'), :p_sueldo_hora, :p_nombres, :p_apellidop, :p_apellidom, :p_region, :p_comuna, :p_calle, :p_local, :p_numero_calle, :p_direccion_adic)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':p_trid', $trid, PDO::PARAM_INT);
                $stmt->bindParam(':p_run', $run, PDO::PARAM_INT);
                $stmt->bindParam(':p_telefono', $telefono, PDO::PARAM_INT);
                $stmt->bindParam(':p_correo', $correo);
                $stmt->bindParam(':p_cargo', $cargo);
                $stmt->bindParam(':p_birth', $fechaNacimiento);
                $stmt->bindParam(':p_sueldo_hora', $sueldoHora, PDO::PARAM_INT);
                $stmt->bindParam(':p_nombres', $nombres);
                $stmt->bindParam(':p_apellidop', $apellidoPaterno);
                $stmt->bindParam(':p_apellidom', $apellidoMaterno);
                $stmt->bindParam(':p_region', $region);
                $stmt->bindParam(':p_comuna', $comuna);
                $stmt->bindParam(':p_calle', $calle);
                $stmt->bindParam(':p_local', $local, PDO::PARAM_INT);
                $stmt->bindParam(':p_numero_calle', $numeroCalle);
                $stmt->bindParam(':p_direccion_adic', $direccionAdic);
                $stmt->execute();
                echo '<script>window.location.href = "administrar-personal.php";</script>';
                exit;
            }
            // Añadir ingreso
            if (isset($_POST['agregar_ingreso'])) {
    $trid = $_POST['trid_fecha'] ?? '';
    $fechaIngreso = $_POST['fecha_ingreso'] ?? '';
    if ($trid && $fechaIngreso) {
        $sql = "CALL CREARFECHADETALLE(TO_DATE(:P_FEFECHAINGRESO, 'YYYY-MM-DD'), :P_TRID)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':P_FEFECHAINGRESO', $fechaIngreso);
        $stmt->bindParam(':P_TRID', $trid, PDO::PARAM_INT);
        $stmt->execute();
    }
    echo '<script>window.location.href = "administrar-personal.php";</script>';
    exit;
            }
            // Añadir egreso
            if (isset($_POST['agregar_egreso'])) {
    $trid = $_POST['trid_fecha'] ?? '';
    $feid = $_POST['feid'] ?? '';
    $fechaEgreso = $_POST['fecha_egreso'] ?? '';
    if ($trid && $feid && $fechaEgreso) {
        $sql = "CALL EGRESARFECHADETALLE(:P_FEID, TO_DATE(:P_FEFECHAEGRESO, 'YYYY-MM-DD'), :P_TRID)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':P_FEID', $feid, PDO::PARAM_INT);
        $stmt->bindParam(':P_FEFECHAEGRESO', $fechaEgreso);
        $stmt->bindParam(':P_TRID', $trid, PDO::PARAM_INT);
        $stmt->execute();
    }
    echo '<script>window.location.href = "administrar-personal.php";</script>';
    exit;
            }
            // Cerrar fechas (solo refresca)
            if (isset($_POST['cerrar_fechas'])) {
                echo '<script>window.location.href = "administrar-personal.php";</script>';
                exit;
            }
        }
    ?>

    <?php
    session_start();

    if (!isset($_SESSION['TRID']) || !isset($_SESSION['TRRUN']) || !isset($_SESSION['TRNOMBRES']) || !isset($_SESSION['TRCARGO']) || !ISSET($_SESSION['LOCAL_LOID'])) {
        header("Location: index.php");
        exit;
    }

    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Administrar Personal - La Pica del Pescador</title>
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
        <link rel="stylesheet" href="css/navbar.css">
        <style>
            .wave {
                position: fixed;
                left: 0;
                bottom: 0;
                width: 100vw;
                min-width: 100%;
                height: 180px;
                z-index: 2;
                pointer-events: none;
            }
            .main-content {
                margin-top: 90px;
                z-index: 10;
                position: relative;
            }
        </style>
    </head>
    <body>
        <!-- Navbar superior -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container">
            <!-- Marca -->
            <a class="navbar-brand" href="#">
                <img src="icon.png" alt="Logo" style="height: 2.5rem;" id="logo-navbar">
            </a>
            <!-- Botón de menú responsive -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Menú colapsable -->
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
                    <?php
                    $cargo = isset($_SESSION['TRCARGO']) ? $_SESSION['TRCARGO'] : '';
                    // Elementos para administrador (todo)
                    if ($cargo === 'Administrador') {
                    ?>
                        <li class="nav-item"><a class="nav-link d-flex flex-column text-center" href="administrar-local.php"><i class="bi bi-gear-wide-connected my-2" style="font-size:1.2rem;"></i><span class="small">Locales</span></a></li>
                        <li class="nav-item"><a class="nav-link d-flex flex-column text-center" href="informes.php"><i class="bi bi-bar-chart-line-fill my-2" style="font-size:1.2rem;"></i><span class="small">Informes</span></a></li>
                        <li class="nav-item"><a class="nav-link d-flex flex-column text-center" href="administrar-personal.php"><i class="bi bi-people-fill my-2" style="font-size:1.2rem;"></i><span class="small">Personal</span></a></li>
                        <li class="nav-item"><a class="nav-link d-flex flex-column text-center" href="administrar-empresas.php"><i class="bi bi-building my-2" style="font-size:1.2rem;"></i><span class="small">Empresas</span></a></li>
                        <li class="nav-item"><a class="nav-link d-flex flex-column text-center" href="tomar-orden.php"><i class="bi bi-journal-plus my-2" style="font-size:1.2rem;"></i><span class="small">Tomar orden</span></a></li>
                        <li class="nav-item"><a class="nav-link d-flex flex-column text-center" href="ordenes-activas.php"><i class="bi bi-list-check my-2" style="font-size:1.2rem;"></i><span class="small">Órdenes</span></a></li>
                        <li class="nav-item"><a class="nav-link d-flex flex-column text-center" href="imprimir-boleta.php"><i class="bi bi-printer-fill my-2" style="font-size:1.2rem;"></i><span class="small">Generar Boleta</span></a></li>
                        <li class="nav-item"><a class="nav-link d-flex flex-column text-center" href="ver-boletas.php"><i class="bi bi-receipt my-2" style="font-size:1.2rem;"></i><span class="small">Boletas anteriores</span></a></li>
                        <li class="nav-item"><a class="nav-link d-flex flex-column text-center" href="ver-comanda.php"><i class="bi bi-card-list my-2" style="font-size:1.2rem;"></i><span class="small">Comanda</span></a></li>
                        <li class="nav-item"><a class="nav-link d-flex flex-column text-center" href="editar-productos.php"><i class="bi bi-pencil-square my-2" style="font-size:1.2rem;"></i><span class="small">Productos</span></a></li>
                    <?php
                    }
                    // Garzón: Tomar orden y Órdenes
                    if ($cargo === 'Garzón') {
                    ?>
                        <li class="nav-item"><a class="nav-link d-flex flex-column text-center" href="tomar-orden.php"><i class="bi bi-journal-plus my-2" style="font-size:1.2rem;"></i><span class="small">Tomar orden</span></a></li>
                        <li class="nav-item"><a class="nav-link d-flex flex-column text-center" href="ordenes-activas.php"><i class="bi bi-list-check my-2" style="font-size:1.2rem;"></i><span class="small">Órdenes</span></a></li>
                    <?php }
                    // Cocinero, Copero, Jefe de cocina: Comanda
                    if (in_array($cargo, ['Cocinero', 'Copero', 'Jefe de cocina'])) {
                    ?>
                        <li class="nav-item"><a class="nav-link d-flex flex-column text-center" href="ver-comanda.php"><i class="bi bi-card-list my-2" style="font-size:1.2rem;"></i><span class="small">Comanda</span></a></li>
                    <?php }
                    // Cajero: Generar boleta y boletas anteriores
                    if ($cargo === 'Cajero') {
                    ?>
                        <li class="nav-item"><a class="nav-link d-flex flex-column text-center" href="imprimir-boleta.php"><i class="bi bi-printer-fill my-2" style="font-size:1.2rem;"></i><span class="small">Generar Boleta</span></a></li>
                        <li class="nav-item"><a class="nav-link d-flex flex-column text-center" href="ver-boletas.php"><i class="bi bi-receipt my-2" style="font-size:1.2rem;"></i><span class="small">Boletas anteriores</span></a></li>
                    <?php }
                    // Bodeguero: Productos
                    if ($cargo === 'Bodeguero') {
                    ?>
                        <li class="nav-item"><a class="nav-link d-flex flex-column text-center" href="editar-productos.php"><i class="bi bi-pencil-square my-2" style="font-size:1.2rem;"></i><span class="small">Productos</span></a></li>
                    <?php }
                    // SIEMPRE: Inicio y usuario
                    ?>
                    <li class="nav-item"><a class="nav-link d-flex flex-column text-center active" href="inicio.php"><i class="bi bi-house-door-fill my-2" style="font-size:1.2rem;"></i><span class="small">Inicio</span></a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdownMenuLink"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="https://ui-avatars.com/api/?name=Usuario" class="rounded-circle me-1" height="28" alt="usuario" loading="lazy" />
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="perfil.php"><i class="bi bi-person me-2"></i>Mi cuenta</a></li>
                            <li>
                                <button class="dropdown-item" id="toggle-darkmode" type="button">
                                    <i class="bi bi-moon me-2" id="darkmode-icon"></i>Modo oscuro/claro
                                </button>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

        <!-- Contenido principal -->
        <div class="container main-content">
            <h1 class="mb-4">Administrar Personal</h1>
            <!-- Formulario para agregar personal -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    Agregar personal
                </div>
                <div class="card-body">
                    <form method="post" id="formAgregarPersonal">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label for="run" class="form-label">RUN*</label>
                                <input type="number" class="form-control" id="run" name="run" required>
                            </div>
                            <div class="col-md-2">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="number" class="form-control" id="telefono" name="telefono">
                            </div>
                            <div class="col-md-3">
                                <label for="correo" class="form-label">Correo electrónico</label>
                                <input type="text" class="form-control" id="correo" name="correo">
                            </div>
                            <div class="col-md-2">
                                <label for="cargo" class="form-label">Cargo*</label>
                                <select class="form-select" id="cargo" name="cargo" required>
                                <option value="">Selecciona cargo</option>
                                <option value="Garzón">Garzón</option>
                                <option value="Cajero">Cajero</option>
                                <option value="Jefe de cocina">Jefe de cocina</option>
                                <option value="Cocinero">Cocinero</option>
                                <option value="Copero">Copero</option>
                                <option value="Administrador">Administrador</option>
                                <option value="Bodeguero">Bodeguero</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="text" class="form-control" id="password" name="password">
                            </div>
                            <div class="col-md-2">
                                <label for="fechaNacimiento" class="form-label">Fecha de nacimiento*</label>
                                <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" required>
                            </div>
                            <div class="col-md-2">
                                <label for="fechaIngreso" class="form-label">Fecha de ingreso*</label>
                                <input type="date" class="form-control" id="fechaIngreso" name="fechaIngreso" required>
                            </div>
                            <div class="col-md-2">
                                <label for="fechaEgreso" class="form-label">Fecha de egreso</label>
                                <input type="date" class="form-control" id="fechaEgreso" name="fechaEgreso">
                            </div>
                            <div class="col-md-2">
                                <label for="sueldoHora" class="form-label">Sueldo por hora*</label>
                                <input type="number" class="form-control" id="sueldoHora" name="sueldoHora" required>
                            </div>
                            <div class="col-md-2">
                                <label for="nombres" class="form-label">Nombres*</label>
                                <input type="text" class="form-control" id="nombres" name="nombres" required>
                            </div>
                            <div class="col-md-2">
                                <label for="apellidoPaterno" class="form-label">Apellido paterno*</label>
                                <input type="text" class="form-control" id="apellidoPaterno" name="apellidoPaterno" required>
                            </div>
                            <div class="col-md-2">
                                <label for="apellidoMaterno" class="form-label">Apellido materno*</label>
                                <input type="text" class="form-control" id="apellidoMaterno" name="apellidoMaterno" required>
                            </div>
                            <div class="col-md-2">
                                <label for="vigencia" class="form-label">Vigente*</label>
                                <select class="form-select" id="vigencia" name="vigencia" required>
                                    <option value="si">Sí</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="regionCascada" class="form-label">Región*</label>
                                <select class="form-select" id="regionCascada" name="regionCascada" required>
                                    <option value="">Selecciona región</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="comunaCascada" class="form-label">Comuna*</label>
                                <select class="form-select" id="comunaCascada" name="comunaCascada" required>
                                    <option value="">Selecciona comuna</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="localCascada" class="form-label">Local*</label>
                                <select class="form-select" id="localCascada" name="localCascada" required>
                                    <option value="">Selecciona local</option>
                                    <?php
                                        $sql_locales = "SELECT LOID, LOREGION, LOCOMUNA, LOCALLE, LONUMEROCALLE FROM LOCAL WHERE LOACTIVO = 1 ORDER BY LOID";
                                        $stmt_locales = $conn->prepare($sql_locales);
                                        $stmt_locales->execute();
                                        while ($local = $stmt_locales->fetch(PDO::FETCH_ASSOC)) {
                                            $info = 'Local ' . htmlspecialchars($local['LOID']) . ': ' . htmlspecialchars($local['LOREGION']) . ', ' . htmlspecialchars($local['LOCOMUNA']) . ', ' . htmlspecialchars($local['LOCALLE']) . ' ' . htmlspecialchars($local['LONUMEROCALLE']);
                                            echo '<option value="' . htmlspecialchars($local['LOID']) . '">' . $info . '</option>';
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="calle" class="form-label">Calle*</label>
                                <input type="text" class="form-control" id="calle" name="calle" required>
                            </div>
                            <div class="col-md-2">
                                <label for="numeroCalle" class="form-label">Número Calle*</label>
                                <input type="text" class="form-control" id="numeroCalle" name="numeroCalle" required>
                            </div>
                            <div class="col-md-4">
                                <label for="direccionAdicional" class="form-label">Dirección adicional</label>
                                <input type="text" class="form-control" id="direccionAdicional" name="direccionAdicional">
                            </div>
                            <input type="hidden" name="agregar_personal" value="1">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-success">Guardar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Tabla de personal existente -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    Personal registrado
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>RUN</th>
                                    <th>Nombres</th>
                                    <th>Apellido paterno</th>
                                    <th>Apellido materno</th>
                                    <th>Teléfono</th>
                                    <th>Correo electrónico</th>
                                    <th>Cargo</th>
                                    <th>Fecha de nacimiento</th>
                                    <th>Fechas</th>
                                    <th>Sueldo por hora</th>
                                    <th>Vigente</th>
                                    <th>Región</th>
                                    <th>Comuna</th>
                                    <th>Calle</th>
                                    <th>Número Calle</th>
                                    <th>Local</th>
                                    <th>Dirección adicional</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql_trabajadores = "SELECT TRID, TRRUN, TRTELEFONO, TRCORREO, TRCARGO, TRFECHANACIMIENTO, TRSUELDOHORA, TRNOMBRES, TRAPELLIDOPATERNO, TRAPELLIDOMATERNO, TRVIGENTE, TRREGION, TRCOMUNA, TRCALLE, TRNUMEROCALLE, TRDIRECCIONADICIONAL, LOCAL_LOID FROM TRABAJADOR ORDER BY TRID";
                                $stmt_trabajadores = $conn->prepare($sql_trabajadores);
                                $stmt_trabajadores->execute();
                                while ($trabajador = $stmt_trabajadores->fetch(PDO::FETCH_ASSOC)) {
                                    // Formatear fecha de nacimiento correctamente
                                    $fecha_nac = $trabajador['TRFECHANACIMIENTO'];
                                    if ($fecha_nac) {
                                        // Si viene como YYYY-MM-DD HH:MM:SS.000 o similar, tomar solo los primeros 10 caracteres
                                        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $fecha_nac)) {
                                            $fecha_nac = substr($fecha_nac, 0, 10);
                                        } else if (preg_match('/^(\d{2})\/(\d{2})\/(\d{2,4})$/', $fecha_nac, $matches)) {
                                            // Si viene como DD/MM/YY o DD/MM/YYYY
                                            $anio = $matches[3];
                                            if (strlen($anio) == 2) {
                                                $anio_int = intval($anio);
                                                if ($anio_int < 30) {
                                                    $anio = '20' . str_pad($anio, 2, '0', STR_PAD_LEFT);
                                                } else {
                                                    $anio = '19' . str_pad($anio, 2, '0', STR_PAD_LEFT);
                                                }
                                            }
                                            $fecha_nac = $anio . '-' . str_pad($matches[2],2,'0',STR_PAD_LEFT) . '-' . str_pad($matches[1],2,'0',STR_PAD_LEFT);
                                        } else {
                                            // Si es timestamp u otro formato
                                            $fecha_nac = date('Y-m-d', strtotime($fecha_nac));
                                        }
                                    }
                                    echo '<tr>';
                                    echo '<td>' . htmlspecialchars($trabajador['TRID']) . '</td>';
                                    echo '<td>' . htmlspecialchars($trabajador['TRRUN']) . '</td>';
                                    echo '<td>' . htmlspecialchars($trabajador['TRNOMBRES']) . '</td>';
                                    echo '<td>' . htmlspecialchars($trabajador['TRAPELLIDOPATERNO']) . '</td>';
                                    echo '<td>' . htmlspecialchars($trabajador['TRAPELLIDOMATERNO']) . '</td>';
                                    echo '<td>' . htmlspecialchars($trabajador['TRTELEFONO']) . '</td>';
                                    echo '<td>' . htmlspecialchars($trabajador['TRCORREO']) . '</td>';
                                    echo '<td>' . htmlspecialchars($trabajador['TRCARGO']) . '</td>';
                                    echo '<td>' . htmlspecialchars($fecha_nac) . '</td>';
                                    echo '<td><button class="btn btn-info btn-sm mostrarFechas" data-id="' . htmlspecialchars($trabajador['TRID']) . '">Mostrar fechas</button></td>';
                                    echo '<td>$' . htmlspecialchars($trabajador['TRSUELDOHORA']) . '</td>';
                                    echo '<td>' . ($trabajador['TRVIGENTE'] == 1 ? 'Sí' : 'No') . '</td>';
                                    echo '<td>' . htmlspecialchars($trabajador['TRREGION']) . '</td>';
                                    echo '<td>' . htmlspecialchars($trabajador['TRCOMUNA']) . '</td>';
                                    echo '<td>' . htmlspecialchars($trabajador['TRCALLE']) . '</td>';
                                    echo '<td>' . htmlspecialchars($trabajador['TRNUMEROCALLE']) . '</td>';
                                    echo '<td>Local ' . htmlspecialchars($trabajador['LOCAL_LOID']) . '</td>';
                                    echo '<td>' . htmlspecialchars($trabajador['TRDIRECCIONADICIONAL']) . '</td>';
                                    echo '<td>';
                                    echo '<div class="d-flex gap-2">';
                                    echo '<button class="btn btn-warning btn-sm editarPersonal" data-id="' . htmlspecialchars($trabajador['TRID']) . '">Editar</button>';
                                    echo '<button class="btn btn-outline-primary btn-sm cambiarPassword" data-id="' . htmlspecialchars($trabajador['TRID']) . '">Cambiar contraseña</button>';
                                    echo '</div>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tabla de fechas de ingreso/egreso dinámica -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    Fechas de ingreso y egreso
                </div>
                <div class="card-body">
                    <div class="mb-3 d-flex justify-content-between">
                        <button class="btn btn-success" id="btnAgregarIngreso" disabled>Añadir ingreso</button>
                        <button class="btn btn-primary" id="btnAgregarEgreso" disabled>Añadir egreso</button>
                        <button class="btn btn-secondary" id="btnCerrarFechas" disabled>Cerrar fechas</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle w-100" id="fechas-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fecha de ingreso</th>
                                    <th>Fecha de egreso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- JS rellena aquí las fechas -->
                            </tbody>
                        </table>
                    </div>
                    <form id="form-fecha" class="row g-2 mt-3" style="display:none;">
                    <div class="col-md-6">
                        <input type="date" class="form-control" id="inputFecha" required>
                    </div>
                    <div class="col-md-6 text-end">
                        <button type="submit" class="btn btn-success" id="btnGuardarFecha">Guardar</button>
                        <button type="button" class="btn btn-secondary" id="btnCancelarFecha">Cancelar</button>
                    </div>
                </form>
                <div class="text-center text-muted mt-3" id="fechas-personal-placeholder">Seleccione un usuario para ver sus fechas de ingreso y egreso.</div>
                </div>
            </div>
        </div>
        
        <!-- Onda decorativa inferior -->

        <!-- Modal para editar personal (fuera de main-content) -->
        <div class="modal fade" id="modalEditarPersonal" tabindex="-1" aria-labelledby="modalEditarPersonalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarPersonalLabel">Editar datos de personal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formEditarPersonal" method="post">
                <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-2">
                    <label class="form-label">ID</label>
                    <input type="text" class="form-control" id="editId" name="editId" readonly>
                    </div>
                    <div class="col-md-2">
                    <label class="form-label">RUN*</label>
                    <input type="number" class="form-control" id="editRun" name="editRun" required>
                    </div>
                    <div class="col-md-2">
                    <label class="form-label">Teléfono</label>
                    <input type="number" class="form-control" id="editTelefono" name="editTelefono">
                    </div>
                    <div class="col-md-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="text" class="form-control" id="editCorreo" name="editCorreo">
                    </div>
                    <div class="col-md-2">
                    <label class="form-label">Cargo*</label>
                    <select class="form-select" id="editCargo" name="editCargo" required>
                        <option value="">Selecciona cargo</option>
                        <option value="Garzón">Garzón</option>
                        <option value="Cajero">Cajero</option>
                        <option value="Jefe de cocina">Jefe de cocina</option>
                        <option value="Cocinero">Cocinero</option>
                        <option value="Copero">Copero</option>
                        <option value="Administrador">Administrador</option>
                        <option value="Bodeguero">Bodeguero</option>
                    </select>
                    </div>
                    <div class="col-md-2">
                    <label class="form-label">Fecha de nacimiento*</label>
                    <input type="date" class="form-control" id="editFechaNacimiento" name="editFechaNacimiento" required>
                    </div>
                    <div class="col-md-2">
                    <label class="form-label">Sueldo por hora*</label>
                    <input type="number" class="form-control" id="editSueldoHora" name="editSueldoHora" required>
                    </div>
                    <div class="col-md-2">
                    <label class="form-label">Nombres*</label>
                    <input type="text" class="form-control" id="editNombres" name="editNombres" required>
                    </div>
                    <div class="col-md-2">
                    <label class="form-label">Apellido paterno*</label>
                    <input type="text" class="form-control" id="editApellidoPaterno" name="editApellidoPaterno" required>
                    </div>
                    <div class="col-md-2">
                    <label class="form-label">Apellido materno*</label>
                    <input type="text" class="form-control" id="editApellidoMaterno" name="editApellidoMaterno" required>
                    </div>
                    <div class="col-md-2">
                    <label class="form-label">Región*</label>
                    <select class="form-select" id="editRegion" name="editRegion" required>
                        <option value="">Selecciona región</option>
                    </select>
                    </div>
                    <div class="col-md-2">
                    <label class="form-label">Comuna*</label>
                    <select class="form-select" id="editComuna" name="editComuna" required>
                        <option value="">Selecciona comuna</option>
                    </select>
                    </div>
                    <div class="col-md-2">
                    <label class="form-label">Local*</label>
                    <select class="form-select" id="editLocal" name="editLocal" required>
                        <option value="">Selecciona local</option>
                        <?php
                            $sql_locales = "SELECT LOID, LOREGION, LOCOMUNA, LOCALLE, LONUMEROCALLE FROM LOCAL WHERE LOACTIVO = 1 ORDER BY LOID";
                            $stmt_locales = $conn->prepare($sql_locales);
                            $stmt_locales->execute();
                            while ($local = $stmt_locales->fetch(PDO::FETCH_ASSOC)) {
                                $info = 'Local ' . htmlspecialchars($local['LOID']) . ': ' . htmlspecialchars($local['LOREGION']) . ', ' . htmlspecialchars($local['LOCOMUNA']) . ', ' . htmlspecialchars($local['LOCALLE']) . ' ' . htmlspecialchars($local['LONUMEROCALLE']);
                                echo '<option value="' . htmlspecialchars($local['LOID']) . '">' . $info . '</option>';
                            }
                        ?>
                    </select>
                    </div>
                    <div class="col-md-2">
                    <label class="form-label">Calle*</label>
                    <input type="text" class="form-control" id="editCalle" name="editCalle" required>
                    </div>
                    <div class="col-md-2">
                    <label class="form-label">Número Calle*</label>
                    <input type="text" class="form-control" id="editNumeroCalle" name="editNumeroCalle" required>
                    </div>
                    <div class="col-md-4">
                    <label class="form-label">Dirección adicional</label>
                    <input type="text" class="form-control" id="editDireccionAdicional" name="editDireccionAdicional">
                    </div>
                </div>
                </div>
                <input type="hidden" name="editar_personal" value="1">
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
            </div>
        </div>
        </div>

        <!-- Modal para cambiar contraseña -->
        <div class="modal fade" id="modalCambiarPassword" tabindex="-1" aria-labelledby="modalCambiarPasswordLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCambiarPasswordLabel">Cambiar contraseña</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form id="formCambiarPassword">
                <div class="modal-body">
                <div class="mb-3">
                    <label for="nuevaPassword" class="form-label">Nueva contraseña</label>
                    <div class="input-group">
                    <input type="password" class="form-control" id="nuevaPassword" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                        <i class="bi bi-eye" id="iconPasswordEye"></i>
                    </button>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
            </div>
        </div>
        </div>
        <svg class="wave" viewBox="0 0 1440 180" preserveAspectRatio="none">
            <path fill="#4fc3f7" fill-opacity="0.6" d="M0,120 C360,180 1080,60 1440,120 L1440,180 L0,180 Z"></path>
            <path fill="#81d4fa" fill-opacity="0.5" d="M0,140 C400,100 1040,180 1440,140 L1440,180 L0,180 Z"></path>
            <path fill="#b3e0ff" fill-opacity="0.4" d="M0,180 C400,160 1040,180 1440,180 L1440,180 L0,180 Z"></path>
        </svg>
        <script src="js/bootstrap.bundle.min.js"></script>
        <script src="js/darkmode.js"></script>
        <script>
        // --- Modal editar personal ---
        document.querySelectorAll('.editarPersonal').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                fetch('php_scripts/obtener_trabajador.php?id=' + id)
                    .then(response => response.json())
                    .then(data => {
                        // Verificar que el id recibido corresponde al solicitado (o filtrar por RUN si lo prefieres)
                        if (String(data.TRID) === String(id)) {
                            document.getElementById('editId').value = data.TRID;
                            document.getElementById('editRun').value = data.TRRUN;
                            document.getElementById('editTelefono').value = data.TRTELEFONO;
                            document.getElementById('editCorreo').value = data.TRCORREO;
                            document.getElementById('editCargo').value = data.TRCARGO;
                            // Formateo robusto para input type='date'
                            let fechaNac = data.TRFECHANACIMIENTO;
                            let fechaFinal = '';
                            if (fechaNac) {
                                let d = new Date(fechaNac);
                                if (!isNaN(d.getTime())) {
                                    fechaFinal = d.toISOString().slice(0,10);
                                } else if (/^\d{4}-\d{2}-\d{2}/.test(fechaNac)) {
                                    fechaFinal = fechaNac.substring(0, 10);
                                } else if (/^\d{2}\/\d{2}\/\d{2,4}$/.test(fechaNac)) {
                                    const matches = fechaNac.match(/^(\d{2})\/(\d{2})\/(\d{2,4})$/);
                                    let anio = matches[3];
                                    if (anio.length === 2) {
                                        const anioInt = parseInt(anio, 10);
                                        if (anioInt < 30) {
                                            anio = '20' + anio.padStart(2, '0');
                                        } else {
                                            anio = '19' + anio.padStart(2, '0');
                                        }
                                    }
                                    fechaFinal = anio + '-' + matches[2].padStart(2,'0') + '-' + matches[1].padStart(2,'0');
                                }
                            }
                            document.getElementById('editFechaNacimiento').value = fechaFinal;
                            document.getElementById('editSueldoHora').value = data.TRSUELDOHORA;
                            document.getElementById('editNombres').value = data.TRNOMBRES;
                            document.getElementById('editApellidoPaterno').value = data.TRAPELLIDOPATERNO;
                            document.getElementById('editApellidoMaterno').value = data.TRAPELLIDOMATERNO;
                            cargarRegionesEdicion();
                            document.getElementById('editRegion').value = data.TRREGION;
                            const regionObj = RegionesYcomunas.regiones.find(r => r.NombreRegion === data.TRREGION);
                            editComunaSelect.innerHTML = '<option value="">Selecciona comuna</option>';
                            if (regionObj) {
                                regionObj.comunas.forEach(function(comuna) {
                                    const option = document.createElement('option');
                                    option.value = comuna;
                                    option.textContent = comuna;
                                    editComunaSelect.appendChild(option);
                                });
                                document.getElementById('editComuna').value = data.TRCOMUNA;
                            }
                            const editLocalSelect = document.getElementById('editLocal');
                            // El select de locales se llena por PHP, solo se debe seleccionar el valor correcto
                            editLocalSelect.value = data.LOCAL_LOID;
                            document.getElementById('editCalle').value = data.TRCALLE;
                            document.getElementById('editNumeroCalle').value = data.TRNUMEROCALLE;
                            document.getElementById('editDireccionAdicional').value = data.TRDIRECCIONADICIONAL;
                            var modal = new bootstrap.Modal(document.getElementById('modalEditarPersonal'));
                            modal.show();
                        } else {
                            alert('Error: los datos recibidos no corresponden al trabajador seleccionado.');
                        }
                    });
            });
        });

        // --- Modal cambiar contraseña ---
        let trabajadorIdCambio = null;
        document.querySelectorAll('.cambiarPassword').forEach(btn => {
            btn.addEventListener('click', function() {
                trabajadorIdCambio = this.getAttribute('data-id');
                document.getElementById('nuevaPassword').value = '';
                var modal = new bootstrap.Modal(document.getElementById('modalCambiarPassword'));
                modal.show();
            });
        });
        document.getElementById('formCambiarPassword').onsubmit = async function(e) {
            e.preventDefault();
            const password = document.getElementById('nuevaPassword').value;
            if (!password || !trabajadorIdCambio) return;
            // Obtener la key
            const keyResp = await fetch('php_scripts/key.txt');
            const key = await keyResp.text();
            // Hashear la contraseña
            const encoder = new TextEncoder();
            const keyBytes = encoder.encode(key.trim());
            const passwordBytes = encoder.encode(password);
            const cryptoKey = await window.crypto.subtle.importKey(
                'raw', keyBytes, { name: 'HMAC', hash: 'SHA-256' }, false, ['sign']
            );
            const signature = await window.crypto.subtle.sign('HMAC', cryptoKey, passwordBytes);
            const hashHex = Array.from(new Uint8Array(signature)).map(b => b.toString(16).padStart(2, '0')).join('');
            // Enviar al backend
            const formData = new FormData();
            formData.append('cambiar_contraseña', '1');
            formData.append('trid', trabajadorIdCambio);
            formData.append('password', hashHex);
            await fetch('php_scripts/cambiar_contraseña.php', { method: 'POST', body: formData });
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalCambiarPassword'));
            modal.hide();
            alert('Contraseña cambiada correctamente');
        };

        // Mostrar/ocultar contraseña en modal
        document.getElementById('togglePassword').addEventListener('click', function() {
        const input = document.getElementById('nuevaPassword');
        const icon = document.getElementById('iconPasswordEye');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
        });
        </script>
        
        <script>
        // --- Gestión de fechas de ingreso/egreso estilo tabla y render tipo mesas ---
        const fechasPorPersonal = {
            1: [
                { ingreso: '2020-05-01', egreso: '2021-01-01' },
                { ingreso: '2022-03-15', egreso: null }
            ]
        };
        let personalSeleccionado = null;
        let tipoFecha = null; // 'ingreso' o 'egreso'

        function renderFechas() {
            const tbody = document.querySelector('#fechas-table tbody');
            tbody.innerHTML = '';
            const placeholder = document.getElementById('fechas-personal-placeholder');
            const btnIngreso = document.getElementById('btnAgregarIngreso');
            const btnEgreso = document.getElementById('btnAgregarEgreso');
            const btnCerrar = document.getElementById('btnCerrarFechas');

            if (!personalSeleccionado) {
                placeholder.style.display = '';
                btnIngreso.disabled = true;
                btnEgreso.disabled = true;
                btnCerrar.disabled = true;
                tbody.innerHTML = '';
                return;
            }
            placeholder.style.display = 'none';
            btnCerrar.disabled = false;
            // Obtener fechas desde PHP
            let fechas = [];
            if (window.fechasPorPersonal && window.fechasPorPersonal[personalSeleccionado]) {
                fechas = window.fechasPorPersonal[personalSeleccionado];
            }
            fechas.forEach((f, idx) => {
                tbody.innerHTML += `
                    <tr>
                        <td>${idx+1}</td>
                        <td>${f.FEFECHAINGRESO ? f.FEFECHAINGRESO.substring(0,10) : ''}</td>
                        <td>${f.FEFECHAEGRESO ? f.FEFECHAEGRESO.substring(0,10) : ''}</td>
                    </tr>
                `;
            });
            // Lógica de habilitación de botones
            let puedeAgregarIngreso = false;
            let puedeAgregarEgreso = false;
            if (fechas.length === 0) {
                puedeAgregarIngreso = true;
            } else {
                const ultima = fechas[fechas.length-1];
                if (ultima.FEFECHAEGRESO) {
                    puedeAgregarIngreso = true;
                }
                const idxSinEgreso = fechas.findIndex(f => f.FEFECHAINGRESO && !f.FEFECHAEGRESO);
                if (idxSinEgreso !== -1) {
                    puedeAgregarEgreso = true;
                }
            }
            btnIngreso.disabled = !puedeAgregarIngreso;
            btnEgreso.disabled = !puedeAgregarEgreso;

            btnIngreso.onclick = function() {
                tipoFecha = 'ingreso';
                document.getElementById('form-fecha').style.display = '';
                document.getElementById('inputFecha').value = '';
                document.getElementById('btnGuardarFecha').className = 'btn btn-success';
            };
            btnEgreso.onclick = function() {
                tipoFecha = 'egreso';
                document.getElementById('form-fecha').style.display = '';
                document.getElementById('inputFecha').value = '';
                document.getElementById('btnGuardarFecha').className = 'btn btn-primary';
            };
            btnCerrar.onclick = function() {
                personalSeleccionado = null;
                tipoFecha = null;
                document.getElementById('form-fecha').style.display = 'none';
                // Enviar formulario para refrescar
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = "<input type='hidden' name='cerrar_fechas' value='1'>";
                document.body.appendChild(form);
                form.submit();
            };
            document.getElementById('btnCancelarFecha').onclick = function() {
                tipoFecha = null;
                document.getElementById('form-fecha').style.display = 'none';
            };
            document.getElementById('form-fecha').onsubmit = function(e) {
                e.preventDefault();
                const fecha = document.getElementById('inputFecha').value;
                if (!fecha || !personalSeleccionado) return;
                if (tipoFecha === 'ingreso') {
                    // Crear y enviar formulario POST
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `<input type='hidden' name='agregar_ingreso' value='1'><input type='hidden' name='trid_fecha' value='${personalSeleccionado}'><input type='hidden' name='fecha_ingreso' value='${fecha}'>`;
                    document.body.appendChild(form);
                    form.submit();
                } else if (tipoFecha === 'egreso') {
                    // Buscar FEID de la fecha sin egreso
                    const fechas = window.fechasPorPersonal[personalSeleccionado];
                    const idx = fechas.findIndex(f => f.FEFECHAINGRESO && !f.FEFECHAEGRESO);
                    if (idx !== -1) {
                        const feid = fechas[idx].FEID;
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.innerHTML = `<input type='hidden' name='agregar_egreso' value='1'><input type='hidden' name='trid_fecha' value='${personalSeleccionado}'><input type='hidden' name='feid' value='${feid}'><input type='hidden' name='fecha_egreso' value='${fecha}'>`;
                        document.body.appendChild(form);
                        form.submit();
                    }
                }
            };
        }
        function setMostrarFechasListeners() {
            document.querySelectorAll('.mostrarFechas').forEach(btn => {
                btn.onclick = function() {
                    personalSeleccionado = this.getAttribute('data-id');
                    tipoFecha = null;
                    document.getElementById('form-fecha').style.display = 'none';
                    // Obtener fechas desde PHP (inyectadas en window.fechasPorPersonal)
                    renderFechas();
                };
            });
        }
        setMostrarFechasListeners();
        renderFechas();
        // Inyectar fechas desde PHP en window.fechasPorPersonal
        window.fechasPorPersonal = {};
    <?php
    // Obtener todas las fechas por trabajador
    $sql_fechas = "SELECT FEID, FEFECHAINGRESO, FEFECHAEGRESO, TRABAJADOR_TRID FROM FECHADETALLE ORDER BY TRABAJADOR_TRID, FEFECHAINGRESO";
    $stmt_fechas = $conn->prepare($sql_fechas);
    $stmt_fechas->execute();
    $fechasPorPersonal = [];
    while ($f = $stmt_fechas->fetch(PDO::FETCH_ASSOC)) {
        $trid = $f['TRABAJADOR_TRID'];
        if (!isset($fechasPorPersonal[$trid])) $fechasPorPersonal[$trid] = [];
        $fechasPorPersonal[$trid][] = [
            'FEID' => $f['FEID'],
            'FEFECHAINGRESO' => $f['FEFECHAINGRESO'],
            'FEFECHAEGRESO' => $f['FEFECHAEGRESO']
        ];
    }
    foreach ($fechasPorPersonal as $trid => $fechasArr) {
        echo "window.fechasPorPersonal['$trid'] = ".json_encode($fechasArr).";\n";
    }
    ?>
    </script>

    <script>
    // Datos de Tarapacá: comunas, ciudades y locales
        var RegionesYcomunas = {
            "regiones": [{
                "NombreRegion": "Arica y Parinacota",
                "comunas": ["Arica", "Camarones", "Putre", "General Lagos"]
            },
                {
                    "NombreRegion": "Tarapacá",
                    "comunas": ["Iquique", "Alto Hospicio", "Pozo Almonte", "Camiña", "Colchane", "Huara", "Pica"]
            },
                {
                    "NombreRegion": "Antofagasta",
                    "comunas": ["Antofagasta", "Mejillones", "Sierra Gorda", "Taltal", "Calama", "Ollagüe", "San Pedro de Atacama", "Tocopilla", "María Elena"]
            },
                {
                    "NombreRegion": "Atacama",
                    "comunas": ["Copiapó", "Caldera", "Tierra Amarilla", "Chañaral", "Diego de Almagro", "Vallenar", "Alto del Carmen", "Freirina", "Huasco"]
            },
                {
                    "NombreRegion": "Coquimbo",
                    "comunas": ["La Serena", "Coquimbo", "Andacollo", "La Higuera", "Paiguano", "Vicuña", "Illapel", "Canela", "Los Vilos", "Salamanca", "Ovalle", "Combarbalá", "Monte Patria", "Punitaqui", "Río Hurtado"]
            },
                {
                    "NombreRegion": "Valparaíso",
                    "comunas": ["Valparaíso", "Casablanca", "Concón", "Juan Fernández", "Puchuncaví", "Quintero", "Viña del Mar", "Isla de Pascua", "Los Andes", "Calle Larga", "Rinconada", "San Esteban", "La Ligua", "Cabildo", "Papudo", "Petorca", "Zapallar", "Quillota", "Calera", "Hijuelas", "La Cruz", "Nogales", "San Antonio", "Algarrobo", "Cartagena", "El Quisco", "El Tabo", "Santo Domingo", "San Felipe", "Catemu", "Llaillay", "Panquehue", "Putaendo", "Santa María", "Quilpué", "Limache", "Olmué", "Villa Alemana"]
            },
                {
                    "NombreRegion": "Región del Libertador Gral. Bernardo O’Higgins",
                    "comunas": ["Rancagua", "Codegua", "Coinco", "Coltauco", "Doñihue", "Graneros", "Las Cabras", "Machalí", "Malloa", "Mostazal", "Olivar", "Peumo", "Pichidegua", "Quinta de Tilcoco", "Rengo", "Requínoa", "San Vicente", "Pichilemu", "La Estrella", "Litueche", "Marchihue", "Navidad", "Paredones", "San Fernando", "Chépica", "Chimbarongo", "Lolol", "Nancagua", "Palmilla", "Peralillo", "Placilla", "Pumanque", "Santa Cruz"]
            },
                {
                    "NombreRegion": "Región del Maule",
                    "comunas": ["Talca", "ConsVtución", "Curepto", "Empedrado", "Maule", "Pelarco", "Pencahue", "Río Claro", "San Clemente", "San Rafael", "Cauquenes", "Chanco", "Pelluhue", "Curicó", "Hualañé", "Licantén", "Molina", "Rauco", "Romeral", "Sagrada Familia", "Teno", "Vichuquén", "Linares", "Colbún", "Longaví", "Parral", "ReVro", "San Javier", "Villa Alegre", "Yerbas Buenas"]
            },
                {
                    "NombreRegion": "Región del Biobío",
                    "comunas": ["Concepción", "Coronel", "Chiguayante", "Florida", "Hualqui", "Lota", "Penco", "San Pedro de la Paz", "Santa Juana", "Talcahuano", "Tomé", "Hualpén", "Lebu", "Arauco", "Cañete", "Contulmo", "Curanilahue", "Los Álamos", "Tirúa", "Los Ángeles", "Antuco", "Cabrero", "Laja", "Mulchén", "Nacimiento", "Negrete", "Quilaco", "Quilleco", "San Rosendo", "Santa Bárbara", "Tucapel", "Yumbel", "Alto Biobío", "Chillán", "Bulnes", "Cobquecura", "Coelemu", "Coihueco", "Chillán Viejo", "El Carmen", "Ninhue", "Ñiquén", "Pemuco", "Pinto", "Portezuelo", "Quillón", "Quirihue", "Ránquil", "San Carlos", "San Fabián", "San Ignacio", "San Nicolás", "Treguaco", "Yungay"]
            },
                {
                    "NombreRegion": "Región de la Araucanía",
                    "comunas": ["Temuco", "Carahue", "Cunco", "Curarrehue", "Freire", "Galvarino", "Gorbea", "Lautaro", "Loncoche", "Melipeuco", "Nueva Imperial", "Padre las Casas", "Perquenco", "Pitrufquén", "Pucón", "Saavedra", "Teodoro Schmidt", "Toltén", "Vilcún", "Villarrica", "Cholchol", "Angol", "Collipulli", "Curacautín", "Ercilla", "Lonquimay", "Los Sauces", "Lumaco", "Purén", "Renaico", "Traiguén", "Victoria", ]
            },
                {
                    "NombreRegion": "Región de Los Ríos",
                    "comunas": ["Valdivia", "Corral", "Lanco", "Los Lagos", "Máfil", "Mariquina", "Paillaco", "Panguipulli", "La Unión", "Futrono", "Lago Ranco", "Río Bueno"]
            },
                {
                    "NombreRegion": "Región de Los Lagos",
                    "comunas": ["Puerto Montt", "Calbuco", "Cochamó", "Fresia", "FruVllar", "Los Muermos", "Llanquihue", "Maullín", "Puerto Varas", "Castro", "Ancud", "Chonchi", "Curaco de Vélez", "Dalcahue", "Puqueldón", "Queilén", "Quellón", "Quemchi", "Quinchao", "Osorno", "Puerto Octay", "Purranque", "Puyehue", "Río Negro", "San Juan de la Costa", "San Pablo", "Chaitén", "Futaleufú", "Hualaihué", "Palena"]
            },
                {
                    "NombreRegion": "Región Aisén del Gral. Carlos Ibáñez del Campo",
                    "comunas": ["Coihaique", "Lago Verde", "Aisén", "Cisnes", "Guaitecas", "Cochrane", "O’Higgins", "Tortel", "Chile Chico", "Río Ibáñez"]
            },
                {
                    "NombreRegion": "Región de Magallanes y de la AntárVca Chilena",
                    "comunas": ["Punta Arenas", "Laguna Blanca", "Río Verde", "San Gregorio", "Cabo de Hornos (Ex Navarino)", "AntárVca", "Porvenir", "Primavera", "Timaukel", "Natales", "Torres del Paine"]
            },
                {
                    "NombreRegion": "Región Metropolitana de Santiago",
                    "comunas": ["Cerrillos", "Cerro Navia", "Conchalí", "El Bosque", "Estación Central", "Huechuraba", "Independencia", "La Cisterna", "La Florida", "La Granja", "La Pintana", "La Reina", "Las Condes", "Lo Barnechea", "Lo Espejo", "Lo Prado", "Macul", "Maipú", "Ñuñoa", "Pedro Aguirre Cerda", "Peñalolén", "Providencia", "Pudahuel", "Quilicura", "Quinta Normal", "Recoleta", "Renca", "San Joaquín", "San Miguel", "San Ramón", "Vitacura", "Puente Alto", "Pirque", "San José de Maipo", "Colina", "Lampa", "TilVl", "San Bernardo", "Buin", "Calera de Tango", "Paine", "Melipilla", "Alhué", "Curacaví", "María Pinto", "San Pedro", "Talagante", "El Monte", "Isla de Maipo", "Padre Hurtado", "Peñaflor"]
            }]
        };

        // Los locales se cargan dinámicamente desde la base de datos en el select del formulario, no por array JS.

        const regionSelect = document.getElementById('regionCascada');
        const comunaSelect = document.getElementById('comunaCascada');
        const localSelect = document.getElementById('localCascada');

        function cargarRegiones() {
            regionSelect.innerHTML = '<option value="">Selecciona región</option>';
            RegionesYcomunas.regiones.forEach(function(region) {
                const option = document.createElement('option');
                option.value = region.NombreRegion;
                option.textContent = region.NombreRegion;
                regionSelect.appendChild(option);
            });
        }

        regionSelect.addEventListener('change', function() {
            comunaSelect.innerHTML = '<option value="">Selecciona comuna</option>';
            const regionNombre = regionSelect.value;
            const regionObj = RegionesYcomunas.regiones.find(r => r.NombreRegion === regionNombre);
            if (regionObj) {
                regionObj.comunas.forEach(function(comuna) {
                    const option = document.createElement('option');
                    option.value = comuna;
                    option.textContent = comuna;
                    comunaSelect.appendChild(option);
                });
            }
        });

        comunaSelect.addEventListener('change', function() {
            // El select de locales se llena por PHP, no por JS
        });

        cargarRegiones();

        // --- Cascada para edición de región y comuna ---
        const editRegionSelect = document.getElementById('editRegion');
        const editComunaSelect = document.getElementById('editComuna');

        // Cargar regiones en el select de edición
        function cargarRegionesEdicion() {
            editRegionSelect.innerHTML = '<option value="">Selecciona región</option>';
            RegionesYcomunas.regiones.forEach(function(region) {
                const option = document.createElement('option');
                option.value = region.NombreRegion;
                option.textContent = region.NombreRegion;
                editRegionSelect.appendChild(option);
            });
        }

        // Cargar comunas según la región seleccionada en edición
        editRegionSelect.addEventListener('change', function() {
            editComunaSelect.innerHTML = '<option value="">Selecciona comuna</option>';
            const regionNombre = editRegionSelect.value;
            const regionObj = RegionesYcomunas.regiones.find(r => r.NombreRegion === regionNombre);
            if (regionObj) {
                regionObj.comunas.forEach(function(comuna) {
                    const option = document.createElement('option');
                    option.value = comuna;
                    option.textContent = comuna;
                    editComunaSelect.appendChild(option);
                });
            }
        });

        // Inicializar regiones en edición al cargar la página
        cargarRegionesEdicion();
    </script>

    </body>
    </html>