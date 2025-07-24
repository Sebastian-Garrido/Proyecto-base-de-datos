<?php
    include "php_scripts\configs_oracle\config_pdo.php"
?>

<?php
session_start();

if (!isset($_SESSION['TRID']) || !isset($_SESSION['TRRUN']) || !isset($_SESSION['TRNOMBRES']) || !isset($_SESSION['TRCARGO']) || !ISSET($_SESSION['LOCAL_LOID'])) {
    header("Location: index.php");
    exit;
}

?>

<?php
// Obtener el local del trabajador en sesión
$sql_local = "SELECT LOCAL_LOID FROM TRABAJADOR WHERE TRID = :trid";
$stmt_local = $conn->prepare($sql_local);
$stmt_local->bindParam(':trid', $_SESSION['TRID'], PDO::PARAM_INT);
$stmt_local->execute();
$local_loid = $stmt_local->fetchColumn();

// Consultar solo los pedidos activos del local correspondiente
$sql = "
SELECT P.PENUMERO, P.MESALOCAL_MEID, M.MENUMEROINTERNO, M.LOCAL_LOID, P.TRABAJADOR_TRID, T.TRNOMBRES, T.TRAPELLIDOPATERNO, T.TRAPELLIDOMATERNO
FROM PEDIDO P
JOIN MESALOCAL M ON P.MESALOCAL_MEID = M.MEID
JOIN TRABAJADOR T ON P.TRABAJADOR_TRID = T.TRID
WHERE P.PEESTADO = 0 AND M.LOCAL_LOID = :local_loid
ORDER BY P.PENUMERO DESC
";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':local_loid', $local_loid, PDO::PARAM_INT);
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Órdenes Activas - La Pica del Pescador</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        body {
            min-height: 100vh;
            overflow-x: hidden;
        }
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
            margin-top: 100px;
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
                        <a class="nav-link d-flex flex-column text-center " href="administrar-personal.php">
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
                        <a class="nav-link d-flex flex-column text-center active" href="ordenes-activas.php">
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

    <!-- Contenido principal -->
    <div class="container main-content">
        <h1 class="mb-4">Órdenes Activas</h1>
        <div class="row" id="ordenes-container">
            <!-- Card de ejemplo -->
            <?php
            foreach ($pedidos as $pedido) {
                $pe_numero = $pedido['PENUMERO'];
                $sql_det = "
                    SELECT DP.DEPCANTIDAD, PR.PRNOMBRE
                    FROM DETALLEPEDIDO DP
                    JOIN PRODUCTO PR ON DP.PRODUCTO_PRID = PR.PRID
                    WHERE DP.PEDIDO_PENUMERO = :pe_numero
                ";
                $stmt_det = $conn->prepare($sql_det);
                $stmt_det->bindParam(':pe_numero', $pe_numero, PDO::PARAM_INT);
                $stmt_det->execute();
                $productos = $stmt_det->fetchAll(PDO::FETCH_ASSOC);

                // Renderiza la card:
                $nombre_trabajador = $pedido['TRNOMBRES'] . ' ' . $pedido['TRAPELLIDOPATERNO'] . ' ' . $pedido['TRAPELLIDOMATERNO'];
                $header = "Orden #{$pedido['PENUMERO']} - Mesa {$pedido['MENUMEROINTERNO']} - $nombre_trabajador";
                echo '<div class="col-md-4 mb-4">';
                echo '  <div class="card shadow">';
                echo '    <div class="card-header bg-primary text-white">' . htmlspecialchars($header) . '</div>';
                echo '    <div class="card-body">';
                echo '      <ul class="list-group mb-3">';
                foreach ($productos as $prod) {
                    echo '<li class="list-group-item d-flex justify-content-between align-items-center">'
                        . htmlspecialchars($prod['PRNOMBRE']) .
                        '<span class="badge bg-secondary rounded-pill">' . htmlspecialchars($prod['DEPCANTIDAD']) . '</span></li>';
                }
                echo '      </ul>';
                echo '      <div class="d-grid gap-2">';
                echo '        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editarOrdenModal">Editar</button>';
                echo '        <button class="btn btn-success" type="button">Finalizar pedido</button>';
                echo '      </div>';
                echo '    </div>';
                echo '  </div>';
                echo '</div>';
            }
            ?>
            <!-- Puedes duplicar este bloque para más órdenes -->
        </div>
    </div>

    <!-- Modal para editar orden -->
    <div class="modal fade" id="editarOrdenModal" tabindex="-1" aria-labelledby="editarOrdenModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editarOrdenModalLabel">Editar Orden #12345 - Mesa 5</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
          <div class="modal-body">
            <h6>Platillos en la orden</h6>
            <ul class="list-group mb-3" id="platillos-lista">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Paila Marina
                    <span>
                        <span class="badge bg-secondary rounded-pill me-2">2</span>
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Empanada de Mariscos
                    <span>
                        <span class="badge bg-secondary rounded-pill me-2">1</span>
                    </span>
                </li>
            </ul>
            <hr>
            <h6>Añadir platillo</h6>
            <div class="input-group mb-3">
                <select class="form-select" id="nuevo-platillo">
                    <option value="">Selecciona un platillo</option>
                    <option>Paila Marina</option>
                    <option>Empanada de Mariscos</option>
                    <option>Jugo Natural</option>
                    <option>Reineta Frita</option>
                </select>
                <input type="number" class="form-control" id="cantidad-platillo" min="1" value="1" style="max-width: 80px;">
                <button class="btn btn-success" type="button">Añadir</button>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary">Guardar Cambios</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Onda decorativa inferior -->
    <svg class="wave" viewBox="0 0 1440 180" preserveAspectRatio="none">
        <path fill="#4fc3f7" fill-opacity="0.6" d="M0,120 C360,180 1080,60 1440,120 L1440,180 L0,180 Z"></path>
        <path fill="#81d4fa" fill-opacity="0.5" d="M0,140 C400,100 1040,180 1440,140 L1440,180 L0,180 Z"></path>
        <path fill="#b3e0ff" fill-opacity="0.4" d="M0,180 C400,160 1040,180 1440,180 L1440,180 L0,180 Z"></path>
    </svg>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/darkmode.js"></script>
</body>
</html>