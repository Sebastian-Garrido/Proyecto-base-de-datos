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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boletas Anteriores - La Pica del Pescador</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        .boleta-preview {
            background: #f8f9fa;
            border-radius: 1rem;
            padding: 2rem;
            max-width: 400px;
            margin: 0 auto 2rem auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.07);
            position: relative;
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
        .main-content {
            margin-top: 90px;
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

    <div class="container main-content">
        <h1 class="mb-4">Boletas Anteriores</h1>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Buscar Boletas
            </div>
            <div class="card-body">
                <form class="row g-3" id="form-busqueda">
                    <div class="col-md-3">
                        <label for="fechaBoleta" class="form-label">Fecha</label>
                        <input type="date" class="form-control" id="fechaBoleta" name="fechaBoleta">
                    </div>
                    <div class="col-md-3">
                        <label for="platoBoleta" class="form-label">Contiene plato</label>
                        <input type="text" class="form-control" id="platoBoleta" name="platoBoleta" placeholder="Ej: Paila Marina, Empanada">
                    </div>
                    <div class="col-md-3">
                        <label for="idBoleta" class="form-label">ID Boleta</label>
                        <input type="number" class="form-control" id="idBoleta" name="idBoleta" placeholder="Ej: 12345">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tipo de documento</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="boleta" id="tipoBoleta" name="tipoDocumento[]" checked>
                            <label class="form-check-label" for="tipoBoleta">Boleta</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="factura" id="tipoFactura" name="tipoDocumento[]" checked>
                            <label class="form-check-label" for="tipoFactura">Factura</label>
                        </div>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-success">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Resultados de boletas -->
        <div id="boletas-resultados" class="row justify-content-center">
        <?php
        // Obtener el local del trabajador en sesión
        $sql_local = "SELECT LOCAL_LOID FROM TRABAJADOR WHERE TRID = :trid";
        $stmt_local = $conn->prepare($sql_local);
        $stmt_local->bindParam(':trid', $_SESSION['TRID'], PDO::PARAM_INT);
        $stmt_local->execute();
        $local_loid = $stmt_local->fetchColumn();

        // Filtros del formulario
        $where = " WHERE D.DTCOMPLETADA = 1 AND M.LOCAL_LOID = :local_loid";
        $params = array(':local_loid' => $local_loid);
        if (isset($_GET['fechaBoleta']) && $_GET['fechaBoleta'] !== '') {
            $where .= " AND TRUNC(D.DTFECHAEMISION) = TO_DATE(:fechaBoleta, 'YYYY-MM-DD')";
            $params[':fechaBoleta'] = $_GET['fechaBoleta'];
        }
        if (isset($_GET['idBoleta']) && $_GET['idBoleta'] !== '') {
            $where .= " AND D.DTNUMEROORDEN = :idBoleta";
            $params[':idBoleta'] = $_GET['idBoleta'];
        }
        if (isset($_GET['tipoDocumento']) && is_array($_GET['tipoDocumento'])) {
            $tipos = [];
            foreach ($_GET['tipoDocumento'] as $tipo) {
                if ($tipo === 'boleta') $tipos[] = 0;
                if ($tipo === 'factura') $tipos[] = 1;
            }
            if (count($tipos) > 0) {
                $where .= " AND D.DTTIPO IN (" . implode(',', $tipos) . ")";
            }
        }
        // Plato: buscar por nombre en los detalles
        $platoBoleta = isset($_GET['platoBoleta']) ? trim($_GET['platoBoleta']) : '';
        $sql_boletas = "
            SELECT D.*, P.PENUMERO, M.MENUMEROINTERNO, D.DTFECHAEMISION
            FROM DOCTRIB D
            JOIN PEDIDO P ON D.PEDIDO_PENUMERO = P.PENUMERO
            JOIN MESALOCAL M ON P.MESALOCAL_MEID = M.MEID
            $where
            ORDER BY D.DTFECHAEMISION DESC
        ";
        $stmt_boletas = $conn->prepare($sql_boletas);
        foreach ($params as $key => $value) {
            $stmt_boletas->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt_boletas->execute();
        $boletas = $stmt_boletas->fetchAll(PDO::FETCH_ASSOC);
        // Filtrar por plato si corresponde
        if ($platoBoleta !== '') {
            $boletas = array_filter($boletas, function($boleta) use ($conn, $platoBoleta) {
                $sql_det = "SELECT PR.PRNOMBRE FROM DETALLEPEDIDO DP JOIN PRODUCTO PR ON DP.PRODUCTO_PRID = PR.PRID WHERE DP.PEDIDO_PENUMERO = :pedido";
                $stmt_det = $conn->prepare($sql_det);
                $stmt_det->bindParam(':pedido', $boleta['PEDIDO_PENUMERO'], PDO::PARAM_INT);
                $stmt_det->execute();
                $detalles = $stmt_det->fetchAll(PDO::FETCH_COLUMN);
                foreach ($detalles as $nombre) {
                    if (stripos($nombre, $platoBoleta) !== false) return true;
                }
                return false;
            });
        }
        if ($boletas && count($boletas) > 0) {
            $colCount = 0;
            foreach ($boletas as $boleta) {
                $pedido_numero = $boleta['PEDIDO_PENUMERO'];
                // Mesa
                $mesa = $boleta['MENUMEROINTERNO'];
                // Garzón
                $sql_garzon = "SELECT TRNOMBRES, TRAPELLIDOPATERNO, TRAPELLIDOMATERNO FROM TRABAJADOR WHERE TRID = :trid";
                $stmt_garzon = $conn->prepare($sql_garzon);
                $stmt_garzon->bindParam(':trid', $boleta['TRABAJADOR_TRID'], PDO::PARAM_INT);
                $stmt_garzon->execute();
                $garzon = $stmt_garzon->fetch(PDO::FETCH_ASSOC);
                $garzon_nombre = $garzon ? $garzon['TRNOMBRES'] . ' ' . $garzon['TRAPELLIDOPATERNO'] . ' ' . $garzon['TRAPELLIDOMATERNO'] : '--';
                // Detalles de productos
                $sql_detalles = "SELECT DP.DEPCANTIDAD, DP.DEPPRECIOUNITARIO, PR.PRNOMBRE FROM DETALLEPEDIDO DP JOIN PRODUCTO PR ON DP.PRODUCTO_PRID = PR.PRID WHERE DP.PEDIDO_PENUMERO = :pedido";
                $stmt_detalles = $conn->prepare($sql_detalles);
                $stmt_detalles->bindParam(':pedido', $pedido_numero, PDO::PARAM_INT);
                $stmt_detalles->execute();
                $detalles = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);
                // Calcular subtotal
                $subtotal = 0;
                foreach ($detalles as $d) {
                    $subtotal += $d['DEPCANTIDAD'] * $d['DEPPRECIOUNITARIO'];
                }
                $propina = round($subtotal * 0.10);
                $total = ($boleta['DTPAGOPROPINA'] == 1) ? $subtotal + $propina : $subtotal;
                $fecha = $boleta['DTFECHAEMISION'];
                $tipoDoc = ($boleta['DTTIPO'] == 1) ? 'Factura' : 'Boleta';
                // Si tiene empresa asociada, mostrar nombre y RUT y cambiar tipoDoc a Factura
                $empresaInfo = '';
                if (!empty($boleta['EMPRESA_EMID'])) {
                    $sql_empresa = "SELECT EMNOMBRE, EMRUT FROM EMPRESA WHERE EMID = :emid";
                    $stmt_empresa = $conn->prepare($sql_empresa);
                    $stmt_empresa->bindParam(':emid', $boleta['EMPRESA_EMID'], PDO::PARAM_INT);
                    $stmt_empresa->execute();
                    $empresa = $stmt_empresa->fetch(PDO::FETCH_ASSOC);
                    if ($empresa) {
                        $empresaInfo = '<div class="mb-2"><strong>Empresa:</strong> ' . htmlspecialchars($empresa['EMNOMBRE']) . '<br><strong>RUT:</strong> ' . htmlspecialchars($empresa['EMRUT']) . '</div>';
                        $tipoDoc = 'Factura';
                    }
                }
        ?>
        <div class="col-md-4 d-flex align-items-stretch mb-4">
            <div class="card boleta-preview shadow-lg w-100">
                <div class="card-header bg-primary text-white text-center fs-5">
                    <?php echo $tipoDoc; ?> N° <?php echo htmlspecialchars($boleta['DTNUMEROORDEN']); ?> - <?php echo htmlspecialchars($fecha); ?>
                </div>
                <div class="card-body">
                    <?php echo $empresaInfo; ?>
                    <p><strong>Mesa:</strong> <?php echo htmlspecialchars($mesa); ?></p>
                    <p><strong>Garzón:</strong> <?php echo htmlspecialchars($garzon_nombre); ?></p>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Precio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detalles as $d) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($d['PRNOMBRE']); ?></td>
                                <td class="text-end"><?php echo htmlspecialchars($d['DEPCANTIDAD']); ?></td>
                                <td class="text-end">$<?php echo number_format($d['DEPPRECIOUNITARIO'], 0, ',', '.'); ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2" class="text-end">Total</th>
                                <th class="text-end">$<?php echo number_format($subtotal, 0, ',', '.'); ?></th>
                            </tr>
                            <tr>
                                <th colspan="2" class="text-end">Propina sugerida (10%)</th>
                                <th class="text-end">$<?php echo number_format($propina, 0, ',', '.'); ?></th>
                            </tr>
                            <tr>
                                <th colspan="2" class="text-end">Total con propina</th>
                                <th class="text-end">$<?php echo number_format($total, 0, ',', '.'); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                    <div class="mt-2">
                        <div class="text-success"><strong>Total pagado:</strong> $<?php echo number_format($total, 0, ',', '.'); ?></div>
                        <div class="text-success"><strong>Vuelto:</strong> $0</div>
                        <div>
                            <strong>Propina pagada:</strong>
                            <span class="<?php echo ($boleta['DTPAGOPROPINA'] == 1) ? 'text-success' : 'text-danger'; ?>"><?php echo ($boleta['DTPAGOPROPINA'] == 1) ? 'Sí' : 'No'; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
                $colCount++;
                if ($colCount % 3 == 0) {
                    echo '</div><div class="row justify-content-center mb-4">';
                }
            }
        } else {
            echo '<div class="alert alert-info">No hay boletas/facturas finalizadas en este local.</div>';
        }
        ?>
        </div>
    </div>

    <svg class="wave" viewBox="0 0 1440 180" preserveAspectRatio="none">
        <path fill="#4fc3f7" fill-opacity="0.6" d="M0,120 C360,180 1080,60 1440,120 L1440,180 L0,180 Z"></path>
        <path fill="#81d4fa" fill-opacity="0.5" d="M0,140 C400,100 1040,180 1440,140 L1440,180 L0,180 Z"></path>
        <path fill="#b3e0ff" fill-opacity="0.4" d="M0,180 C400,160 1040,180 1440,180 L1440,180 L0,180 Z"></path>
    </svg>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        // Aquí puedes agregar JS para filtrar boletas si lo haces en frontend
        document.getElementById('form-busqueda').addEventListener('submit', function(e) {
            e.preventDefault();
            // Aquí iría la lógica de filtrado con JS o AJAX si lo necesitas
            // Si usas PHP, procesa el formulario en el backend
        });
    </script>
    <script src="js/darkmode.js"></script>
</body>
</html>