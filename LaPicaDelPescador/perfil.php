<?php
    include "php_scripts\configs_oracle\config_pdo.php"
?>

<?php
session_start();

if (!isset($_SESSION['TRID']) || !isset($_SESSION['TRRUN']) || !isset($_SESSION['TRNOMBRES']) || !isset($_SESSION['TRCARGO']) || !ISSET($_SESSION['LOCAL_LOID'])) {
    header("Location: index.php");
    exit;
}

$trid = $_SESSION['TRID'];
$trrun = $_SESSION['TRRUN'];
$trnombres = $_SESSION['TRNOMBRES'];
$trcargo = $_SESSION['TRCARGO'];


// Consulta datos del trabajador
$sql = "SELECT TRNOMBRES, TRTELEFONO, TRCORREO FROM Trabajador WHERE TRID = :trid";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':trid', $trid, PDO::PARAM_INT);
$stmt->execute();
$trabajador = $stmt->fetch(PDO::FETCH_ASSOC);

$nombre = $trabajador && $trabajador['TRNOMBRES'] ? $trabajador['TRNOMBRES'] : $trnombres;
$telefono = ($trabajador && $trabajador['TRTELEFONO']) ? $trabajador['TRTELEFONO'] : 'No aplica';
$correo = ($trabajador && $trabajador['TRCORREO']) ? $trabajador['TRCORREO'] : 'No aplica';

// Consulta la fecha de ingreso desde la tabla fechadetalle
$sql_fecha = "SELECT FEFECHAINGRESO FROM fechadetalle WHERE trabajador_trid = :trid";
$stmt_fecha = $conn->prepare($sql_fecha);
$stmt_fecha->bindParam(':trid', $trid, PDO::PARAM_INT);
$stmt_fecha->execute();
$row_fecha = $stmt_fecha->fetch(PDO::FETCH_ASSOC);
$fecha_ingreso = ($row_fecha && $row_fecha['FEFECHAINGRESO']) ? $row_fecha['FEFECHAINGRESO'] : '--';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario - La Pica del Pescador</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        .main-content {
            margin-top: 90px;
            position: relative;
        }
        .perfil-card {
            max-width: 500px;
            margin: 0 auto;
            border-radius: 1rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.07);
        }
        .wave {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100vw;
            min-width: 100%;
            height: 180px;
            z-index: -2;
            pointer-events: none;
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
                    <li class="nav-item">
                        <a class="nav-link d-flex flex-column text-center" href="administrar-local.php">
                            <i class="bi bi-gear-wide-connected my-2" style="font-size:1.2rem;"></i>
                            <span class="small">Locales</span>
                        </a>
                    </li>
                    <!-- Informes -->
                    <li class="nav-item">
                        <a class="nav-link d-flex flex-column text-center" href="informes.php">
                            <i class="bi bi-bar-chart-line-fill my-2" style="font-size:1.2rem;"></i>
                            <span class="small">Informes</span>
                        </a>
                    </li>
                    <!-- Personal y Usuarios -->
                    <li class="nav-item">
                        <a class="nav-link d-flex flex-column text-center" href="administrar-personal.php">
                            <i class="bi bi-people-fill my-2" style="font-size:1.2rem;"></i>
                            <span class="small">Personal</span>
                        </a>
                    </li>
                    
                    <!-- Empresas -->
                    <li class="nav-item">
                        <a class="nav-link d-flex flex-column text-center" href="administrar-empresas.php">
                            <i class="bi bi-building my-2" style="font-size:1.2rem;"></i>
                            <span class="small">Empresas</span>
                        </a>
                    </li>
                    <!-- Tomar orden y Órdenes activas -->
                    <li class="nav-item">
                        <a class="nav-link d-flex flex-column text-center" href="tomar-orden.php">
                            <i class="bi bi-journal-plus my-2" style="font-size:1.2rem;"></i>
                            <span class="small">Tomar orden</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex flex-column text-center" href="ordenes-activas.php">
                            <i class="bi bi-list-check my-2" style="font-size:1.2rem;"></i>
                            <span class="small">Órdenes</span>
                        </a>
                    </li>
                    <!-- Boleta y Boletas anteriores -->
                    <li class="nav-item">
                        <a class="nav-link d-flex flex-column text-center" href="imprimir-boleta.php">
                            <i class="bi bi-printer-fill my-2" style="font-size:1.2rem;"></i>
                            <span class="small">Generar Boleta</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex flex-column text-center" href="ver-boletas.php">
                            <i class="bi bi-receipt my-2" style="font-size:1.2rem;"></i>
                            <span class="small">Boletas anteriores</span>
                        </a>
                    </li>
                    <!-- Comanda y Productos -->
                    <li class="nav-item">
                        <a class="nav-link d-flex flex-column text-center" href="ver-comanda.php">
                            <i class="bi bi-card-list my-2" style="font-size:1.2rem;"></i>
                            <span class="small">Comanda</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex flex-column text-center" href="editar-productos.php">
                            <i class="bi bi-pencil-square my-2" style="font-size:1.2rem;"></i>
                            <span class="small">Productos</span>
                        </a>
                    </li>
                    <!-- Inicio (al final o al principio, según prefieras) -->
                    <li class="nav-item">
                        <a class="nav-link d-flex flex-column text-center" href="inicio.php">
                            <i class="bi bi-house-door-fill my-2" style="font-size:1.2rem;"></i>
                            <span class="small">Inicio</span>
                        </a>
                    </li>
                    <!-- Dropdown de usuario -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdownMenuLink"
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img
                                src="https://ui-avatars.com/api/?name=Usuario"
                                class="rounded-circle me-1"
                                height="28"
                                alt="usuario"
                                loading="lazy"
                            />
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

    <div class="container main-content">
        <h1 class="mb-4 text-center">Perfil de Usuario</h1>
        <div class="card perfil-card mb-4">
            <div class="card-header bg-primary text-white text-center">
                <span id="perfil-nombre"><?php echo htmlspecialchars($nombre); ?></span>
            </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-5">RUN:</dt>
                <dd class="col-7" id="perfil-run"><?php echo htmlspecialchars($trrun); ?></dd>

                <dt class="col-5">Fecha de ingreso:</dt>
                <dd class="col-7" id="perfil-fecha-ingreso"><?php echo htmlspecialchars($fecha_ingreso); ?></dd>

                <dt class="col-5">Cargo:</dt>
                <dd class="col-7" id="perfil-cargo"><?php echo htmlspecialchars($trcargo); ?></dd>

                <dt class="col-5">Correo electrónico:</dt>
                <dd class="col-7" id="perfil-correo"><?php echo htmlspecialchars($correo); ?></dd>

                <dt class="col-5">Teléfono:</dt>
                <dd class="col-7" id="perfil-telefono"><?php echo htmlspecialchars($telefono); ?></dd>
            </dl>
            <hr>
            <?php
            // Consulta el último registro de horas para el trabajador
            $sql_ultimo = "SELECT HOINGRESO, HOEGRESO FROM (
                SELECT HOINGRESO, HOEGRESO FROM HORASTRABAJADAS WHERE TRABAJADOR_TRID = :trid ORDER BY HOINGRESO DESC
            ) WHERE ROWNUM = 1";
            $stmt_ultimo = $conn->prepare($sql_ultimo);
            $stmt_ultimo->bindParam(':trid', $trid, PDO::PARAM_INT);
            $stmt_ultimo->execute();
            $ultimo = $stmt_ultimo->fetch(PDO::FETCH_ASSOC);
            $puede_entrada = (!$ultimo || ($ultimo['HOEGRESO'] !== null && $ultimo['HOEGRESO'] !== ''));
            $puede_salida = ($ultimo && $ultimo['HOINGRESO'] !== null && $ultimo['HOINGRESO'] !== '' && ($ultimo['HOEGRESO'] === null || $ultimo['HOEGRESO'] === ''));
            ?>
            <div class="d-flex justify-content-between">
                <form id="formEntrada" method="post" style="display:inline;">
                    <input type="hidden" name="marcar_entrada" value="1">
                    <button type="submit" class="btn btn-success" id="btnEntrada" <?php echo $puede_entrada ? '' : 'disabled'; ?>>Marcar entrada</button>
                </form>
                <form id="formSalida" method="post" style="display:inline;">
                    <input type="hidden" name="marcar_salida" value="1">
                    <button type="submit" class="btn btn-danger" id="btnSalida" <?php echo $puede_salida ? '' : 'disabled'; ?>>Marcar salida</button>
                </form>
            </div>
            <div class="mt-3">
                <table class="table table-bordered" id="tabla-registros">
                    <thead>
                        <tr>
                            <th>Hora de entrada</th>
                            <th>Hora de salida</th>
                            <th>Tiempo trabajado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Mostrar las horas de entrada del día actual para el trabajador
                        $registros = [];
                        $sql_horas = "SELECT TO_CHAR(HOINGRESO, 'HH24:MI:SS') AS HOINGRESO, TO_CHAR(HOEGRESO, 'HH24:MI:SS') AS HOEGRESO FROM HORASTRABAJADAS WHERE TRABAJADOR_TRID = :trid ORDER BY HOINGRESO";
                        $stmt_horas = $conn->prepare($sql_horas);
                        $stmt_horas->bindParam(':trid', $trid, PDO::PARAM_INT);
                        $stmt_horas->execute();
                        while ($row = $stmt_horas->fetch(PDO::FETCH_ASSOC)) {
                            $entrada = $row['HOINGRESO'] ? date('H:i:s', strtotime($row['HOINGRESO'])) : '--';
                            $salida = $row['HOEGRESO'] ? date('H:i:s', strtotime($row['HOEGRESO'])) : '--';
                            $tiempo = ($row['HOINGRESO'] && $row['HOEGRESO']) ?
                                (floor((strtotime($row['HOEGRESO']) - strtotime($row['HOINGRESO'])) / 3600) . 'h ' . floor(((strtotime($row['HOEGRESO']) - strtotime($row['HOINGRESO'])) % 3600) / 60) . 'm')
                                : '--';
                            echo "<tr><td>{$entrada}</td><td>{$salida}</td><td>{$tiempo}</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
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
        // El JS de la tabla ahora solo gestiona el botón de salida, la tabla se llena desde PHP
        document.getElementById('btnSalida').addEventListener('click', function() {
            const modalSalida = new bootstrap.Modal(document.getElementById('modalConfirmarSalida'));
            modalSalida.show();
        });
    </script>
<?php
// Backend: marcar entrada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_entrada'])) {
    $fechaHora = date('Y-m-d H:i:s');
    try {
        $sql_call = "CALL RTHEARTLESS.MarcarEntrada(TO_DATE(:hoingreso, 'YYYY-MM-DD HH24:MI:SS'), TO_DATE(:hofecharegistro, 'YYYY-MM-DD HH24:MI:SS'), :trid)";
        $stmt_call = $conn->prepare($sql_call);
        $stmt_call->bindParam(':hoingreso', $fechaHora);
        $stmt_call->bindParam(':hofecharegistro', $fechaHora);
        $stmt_call->bindParam(':trid', $trid, PDO::PARAM_INT);
        $stmt_call->execute();
        echo '<script>window.location.href = "perfil.php";</script>';
        exit;
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Error al marcar entrada: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}

// Backend: marcar salida
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marcar_salida'])) {
    $fechaHora = date('Y-m-d H:i:s');
    try {
        // Buscar el HOID del último registro abierto (egreso NULL) para este trabajador
        $sql_hoid = "SELECT HOID FROM (
            SELECT HOID FROM HORASTRABAJADAS WHERE TRABAJADOR_TRID = :trid AND HOEGRESO IS NULL AND HOINGRESO IS NOT NULL ORDER BY HOINGRESO DESC
        ) WHERE ROWNUM = 1";
        $stmt_hoid = $conn->prepare($sql_hoid);
        $stmt_hoid->bindParam(':trid', $trid, PDO::PARAM_INT);
        $stmt_hoid->execute();
        $row_hoid = $stmt_hoid->fetch(PDO::FETCH_ASSOC);
        if ($row_hoid && $row_hoid['HOID']) {
            $hoid = $row_hoid['HOID'];
            // Llamar al procedimiento almacenado para marcar salida
            $sql_call = "CALL RTHEARTLESS.MARCARSALIDA(:P_HOID, TO_DATE(:P_HOEGRESO, 'YYYY-MM-DD HH24:MI:SS'))";
            $stmt_call = $conn->prepare($sql_call);
            $stmt_call->bindParam(':P_HOID', $hoid, PDO::PARAM_INT);
            $stmt_call->bindParam(':P_HOEGRESO', $fechaHora);
            $stmt_call->execute();
            echo '<script>window.location.href = "perfil.php";</script>';
            exit;
        } else {
            echo '<div class="alert alert-danger">No se encontró registro abierto para marcar salida.</div>';
        }
    } catch (Exception $e) {
        echo '<div class="alert alert-danger">Error al marcar salida: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}
?>
</body>
</html>