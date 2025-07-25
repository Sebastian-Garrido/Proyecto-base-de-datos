
<?php
    include "php_scripts\configs_oracle\config_pdo.php";

    // Procesar entrega de comanda
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entregar_conumero'])) {
        $conumero = intval($_POST['entregar_conumero']);
        $hora_final = date('Y-m-d H:i:s');
        try {
            $sql_update = "UPDATE COMANDA SET COESTADO = 1, COHORAFINAL = TO_DATE(:hofinal, 'YYYY-MM-DD HH24:MI:SS') WHERE CONUMERO = :conumero";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bindParam(':hofinal', $hora_final);
            $stmt_update->bindParam(':conumero', $conumero, PDO::PARAM_INT);
            $stmt_update->execute();
            header("Location: ver-comanda.php?entregada=1");
            exit;
        } catch (Exception $e) {
            $errorMsg = urlencode('Error al entregar comanda: ' . $e->getMessage());
            header("Location: ver-comanda.php?error=$errorMsg");
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
    <title>Ver Comanda - La Pica del Pescador</title>
    <meta http-equiv="refresh" content="20">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        body {
            min-height: 100vh;
            background: #fff;
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
            margin-top: 120px;
            z-index: 10;
            position: relative;
        }
        .navbar .btn:hover, .navbar .btn:focus {
            background-color: #1976d2;
            color: #fff;
            transition: background 0.2s, color 0.2s;
        }
        .comanda-card {
            min-width: 250px;
            max-width: 350px;
            margin-bottom: 2rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.07);
            outline: none;
            transition: box-shadow 0.2s, border-color 0.2s;
        }
        .comanda-card.selected, .comanda-card:focus {
            box-shadow: 0 0 0 4px #4fc3f7;
            border-color: #1976d2 !important;
        }
        .comanda-card .card-header {
            font-weight: bold;
            font-size: 1.1rem;
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
        <div class="row g-4" id="comandas-list">
            <?php
            // Obtener el local del trabajador en sesión
            $sql_local = "SELECT LOCAL_LOID FROM TRABAJADOR WHERE TRID = :trid";
            $stmt_local = $conn->prepare($sql_local);
            $stmt_local->bindParam(':trid', $_SESSION['TRID'], PDO::PARAM_INT);
            $stmt_local->execute();
            $local_loid = $stmt_local->fetchColumn();

            // Consultar comandas activas del local
            $sql = "
            SELECT
                C.CONUMERO,
                TO_CHAR(C.COHORAINICIO, 'YYYY-MM-DD HH24:MI:SS') AS COHORAINICIO,
                DP.PEDIDO_PENUMERO,
                DP.DEPCANTIDAD,
                DP.PRODUCTO_PRID,
                P.PRNOMBRE,
                PED.MESALOCAL_MEID,
                M.MENUMEROINTERNO
            FROM COMANDA C
            JOIN DETALLEPEDIDO DP ON C.DETALLEPEDIDO_DEPID = DP.DEPID
            JOIN PRODUCTO P ON DP.PRODUCTO_PRID = P.PRID
            JOIN PEDIDO PED ON DP.PEDIDO_PENUMERO = PED.PENUMERO
            JOIN MESALOCAL M ON PED.MESALOCAL_MEID = M.MEID
            WHERE M.LOCAL_LOID = :local_loid
            AND C.COESTADO = 0
            ORDER BY C.COHORAINICIO ASC
            ";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':local_loid', $local_loid, PDO::PARAM_INT);
            $stmt->execute();
            $comandas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($comandas)) {
                echo '<div class="col-12"><div class="alert alert-info text-center">No hay comandas activas para este local.</div></div>';
            } else {
                foreach ($comandas as $comanda) {
                    // Extraer hora real desde el string
                    $hora = '';
                    if (!empty($comanda['COHORAINICIO'])) {
                        $dt = DateTime::createFromFormat('Y-m-d H:i:s', $comanda['COHORAINICIO']);
                        $hora = $dt ? $dt->format('H:i') : htmlspecialchars($comanda['COHORAINICIO']);
                    }
                    echo '<div class="col-12 col-sm-6 col-md-4">';
                    echo '  <div class="card comanda-card border-primary" tabindex="0">';
                    echo '    <div class="card-header bg-primary text-white">Orden #' . htmlspecialchars($comanda['PEDIDO_PENUMERO']) . '</div>';
                    echo '    <div class="card-body">';
                    echo '      <h5 class="card-title mb-2">' . htmlspecialchars($comanda['PRNOMBRE']) . '</h5>';
                    echo '      <p class="mb-1"><strong>Cantidad:</strong> ' . htmlspecialchars($comanda['DEPCANTIDAD']) . '</p>';
                    echo '      <p class="mb-1"><strong>Mesa:</strong> ' . htmlspecialchars($comanda['MENUMEROINTERNO']) . '</p>';
                    echo '      <p class="mb-1"><strong>Hora pedido:</strong> ' . htmlspecialchars($hora) . '</p>';
                    echo '      <form method="POST" action="ver-comanda.php" class="d-grid">';
                    echo '        <input type="hidden" name="entregar_conumero" value="' . htmlspecialchars($comanda['CONUMERO']) . '">';
                    echo '        <button class="btn btn-success mt-3 w-100 entregar-btn" type="submit" onclick="return confirm(\'¿Marcar esta comanda como entregada?\')">Entregar</button>';
                    echo '      </form>';
                    echo '    </div>';
                    echo '  </div>';
                    echo '</div>';
                }
            }
            ?>
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = Array.from(document.querySelectorAll('.comanda-card'));
        let selectedIdx = 0;
        if (cards.length) {
            cards[selectedIdx].classList.add('selected');
            cards[selectedIdx].focus();
        }

        document.addEventListener('keydown', function(e) {
            if (!cards.length) return;
            // Flecha derecha o abajo
            if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                cards[selectedIdx].classList.remove('selected');
                selectedIdx = (selectedIdx + 1) % cards.length;
                cards[selectedIdx].classList.add('selected');
                cards[selectedIdx].focus();
                e.preventDefault();
            }
            // Flecha izquierda o arriba
            if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                cards[selectedIdx].classList.remove('selected');
                selectedIdx = (selectedIdx - 1 + cards.length) % cards.length;
                cards[selectedIdx].classList.add('selected');
                cards[selectedIdx].focus();
                e.preventDefault();
            }
            // Enter: entregar comanda seleccionada
            if (e.key === 'Enter') {
                const form = cards[selectedIdx].querySelector('form');
                if (form) {
                    form.submit();
                }
                e.preventDefault();
            }
        });
    });
    </script>
</body>
</html>