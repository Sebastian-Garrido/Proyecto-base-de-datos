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
    <title>Imprimir Boleta - La Pica del Pescador</title>
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

    <!-- Contenido principal -->
    <div class="container main-content">
        <h1 class="mb-4">Imprimir Boleta o Factura</h1>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Buscar Orden
            </div>
            <div class="card-body">
                <form class="row g-3 mb-2" method="get" action="imprimir-boleta.php">
                    <div class="col-md-6">
                        <label for="numeroOrden" class="form-label">Número de Orden</label>
                        <input type="text" class="form-control" id="numeroOrden" name="numeroOrden" placeholder="Ingrese número de orden" value="<?php echo isset($_GET['numeroOrden']) ? htmlspecialchars($_GET['numeroOrden']) : ''; ?>">
                    </div>
                    <div class="col-md-4 align-self-end">
                        <button type="submit" class="btn btn-success w-100">Buscar por Orden</button>
                    </div>
                </form>
                <form class="row g-3" method="get" action="imprimir-boleta.php">
                    <div class="col-md-6">
                        <label for="numeroMesa" class="form-label">Número interno de Mesa</label>
                        <input type="text" class="form-control" id="numeroMesa" name="numeroMesa" placeholder="Ingrese número interno de mesa" value="<?php echo isset($_GET['numeroMesa']) ? htmlspecialchars($_GET['numeroMesa']) : ''; ?>">
                    </div>
                    <div class="col-md-4 align-self-end">
                        <button type="submit" class="btn btn-info w-100">Buscar por Mesa</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Vista previa de Documento tributario -->
        <div class="container-fluid px-0">
            <?php
            // Obtener el local del trabajador en sesión
            $sql_local = "SELECT LOCAL_LOID FROM TRABAJADOR WHERE TRID = :trid";
            $stmt_local = $conn->prepare($sql_local);
            $stmt_local->bindParam(':trid', $_SESSION['TRID'], PDO::PARAM_INT);
            $stmt_local->execute();
            $local_loid = $stmt_local->fetchColumn();

            // Buscar boletas pendientes del local, filtrando por número de orden y/o número interno de mesa si se ingresan
            $sql_boletas = "
                SELECT D.*
                FROM DOCTRIB D
                JOIN PEDIDO P ON D.PEDIDO_PENUMERO = P.PENUMERO
                JOIN MESALOCAL M ON P.MESALOCAL_MEID = M.MEID
                WHERE D.DTCOMPLETADA = 0 AND M.LOCAL_LOID = :local_loid";
            $params = array(':local_loid' => $local_loid);
            if (isset($_GET['numeroOrden']) && $_GET['numeroOrden'] !== '') {
                $sql_boletas .= " AND D.PEDIDO_PENUMERO = :numeroOrden";
                $params[':numeroOrden'] = $_GET['numeroOrden'];
            }
            if (isset($_GET['numeroMesa']) && $_GET['numeroMesa'] !== '') {
                $sql_boletas .= " AND M.MENUMEROINTERNO = :numeroMesa";
                $params[':numeroMesa'] = $_GET['numeroMesa'];
            }
            $sql_boletas .= " ORDER BY D.DTFECHAEMISION DESC";
            $stmt_boletas = $conn->prepare($sql_boletas);
            foreach ($params as $key => $value) {
                $stmt_boletas->bindValue($key, $value, PDO::PARAM_INT);
            }
            $stmt_boletas->execute();
            $boletas = $stmt_boletas->fetchAll(PDO::FETCH_ASSOC);
            if ($boletas && count($boletas) > 0) {
                $colCount = 0;
                echo '<div class="row justify-content-center mb-4">';
                foreach ($boletas as $boleta) {
                    $pedido_numero = $boleta['PEDIDO_PENUMERO'];
                    // Mesa
                    $sql_mesa = "SELECT M.MENUMEROINTERNO FROM PEDIDO P JOIN MESALOCAL M ON P.MESALOCAL_MEID = M.MEID WHERE P.PENUMERO = :pedido";
                    $stmt_mesa = $conn->prepare($sql_mesa);
                    $stmt_mesa->bindParam(':pedido', $pedido_numero, PDO::PARAM_INT);
                    $stmt_mesa->execute();
                    $mesa = $stmt_mesa->fetchColumn();
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
            ?>
            <div class="col-md-4 d-flex align-items-stretch mb-4">
                <div id="doc-tributario" class="card boleta-preview shadow-lg w-100">
                    <div class="card-header bg-primary text-white text-center fs-4">
                        Documento Tributario N° <?php echo htmlspecialchars($pedido_numero); ?>
                    </div>
                    <div class="card-body">
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
                                    <th colspan="2" class="text-end">Subtotal</th>
                                    <th class="text-end" id="subtotal-boleta">$<?php echo number_format($subtotal, 0, ',', '.'); ?></th>
                                </tr>
                                <tr>
                                    <th colspan="2" class="text-end">Propina sugerida (10%)</th>
                                    <th class="text-end" id="propina-sugerida">$<?php echo number_format($propina, 0, ',', '.'); ?></th>
                                </tr>
                                <tr>
                                    <th colspan="2" class="text-end">Total</th>
                                    <th class="text-end" id="total-card">$<?php echo number_format($total, 0, ',', '.'); ?></th>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="text-center mt-3">
                            <button class="btn btn-outline-primary me-2" onclick="abrirModalPago('boleta', <?php echo $pedido_numero; ?>, <?php echo $subtotal; ?>, <?php echo $propina; ?>, <?php echo $total; ?>)">Boleta</button>
                            <button class="btn btn-outline-secondary" onclick="abrirModalPago('factura', <?php echo $pedido_numero; ?>, <?php echo $subtotal; ?>, <?php echo $propina; ?>, <?php echo $total; ?>)">Factura</button>
                        </div>
                    </div>

    <?php
    // Obtener métodos de pago
    $metodos_pago = [];
    $stmt_mp = $conn->query("SELECT MPMEDIODEPAGO FROM METODOPAGO ORDER BY MPMEDIODEPAGO ASC");
    if ($stmt_mp) {
        $metodos_pago = $stmt_mp->fetchAll(PDO::FETCH_ASSOC);
    }
    // Obtener empresas
    $empresas = [];
    $stmt_emp = $conn->query("SELECT EMID, EMNOMBRE, EMRUT, EMTELEFONO, EMCORREO, EMCALLE, EMNUMEROCALLE FROM EMPRESA ORDER BY EMNOMBRE ASC");
    if ($stmt_emp) {
        $empresas = $stmt_emp->fetchAll(PDO::FETCH_ASSOC);
    }
    ?>
    <div class="modal fade" id="modalPago" tabindex="-1" aria-labelledby="modalPagoLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalPagoLabel">Registrar Pago</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <form id="formPago">
              <input type="hidden" id="modal-pedido-numero" name="modal-pedido-numero">
              <div id="empresa-select-modal" style="display:none;">
                <label for="empresa-modal" class="form-label">Empresa</label>
                <select class="form-select mb-2" id="empresa-modal" name="empresa-modal">
                  <option value="">Seleccione empresa...</option>
                  <?php foreach ($empresas as $idx => $emp) { ?>
                    <option value="<?php echo $idx; ?>"><?php echo htmlspecialchars($emp['EMNOMBRE']); ?></option>
                  <?php } ?>
                </select>
                <div class="mb-2">
                  <strong>RUT:</strong> <span id="factura-empresa-rut">---</span><br>
                  <strong>Teléfono:</strong> <span id="factura-empresa-telefono">---</span><br>
                  <strong>Correo:</strong> <span id="factura-empresa-correo">---</span><br>
                  <strong>Dirección:</strong> <span id="factura-empresa-direccion">---</span>
                </div>
              </div>
              <div class="mb-3">
                <label for="descuentoManual" class="form-label">Descuento a aplicar</label>
                <input type="number" class="form-control" id="descuentoManual" name="descuentoManual" min="0" value="0">
                <div id="descuento-error" class="text-danger small" style="display:none;"></div>
              </div>
              <div class="mb-3">
                <label for="metodo-pago" class="form-label">Método de pago</label>
                <select class="form-select" id="metodo-pago" name="metodo-pago" required>
                  <option value="">Seleccione método...</option>
                  <?php foreach ($metodos_pago as $mp) { ?>
                    <option value="<?php echo htmlspecialchars($mp['MPMEDIODEPAGO']); ?>"><?php echo htmlspecialchars($mp['MPMEDIODEPAGO']); ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="monto-abono" class="form-label">Monto a abonar</label>
                <input type="number" class="form-control" id="monto-abono" name="monto-abono" min="1" required>
                <div id="monto-error" class="text-danger small" style="display:none;"></div>
              </div>
              <div class="mb-3">
                <input type="checkbox" id="incluirPropina" checked> <label for="incluirPropina">Incluir propina sugerida (<span id="propina-sugerida-modal"></span>)</label>
              </div>
              <div class="mb-3">
                <button type="submit" class="btn btn-success">Registrar abono</button>
              </div>
              <div id="lista-abonos"></div>
              <div class="mt-3">
                <span>Total abonado: <b id="total-abonado">0</b></span><br>
                <span>Restante: <b id="restante">0</b></span><br>
                <span id="vuelto"></span>
              </div>
              <div class="mt-3" id="info-pago"></div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-primary" id="btnImprimirModal" disabled>Imprimir</button>
          </div>
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
                echo '</div>';
            } else {
                echo '<div class="alert alert-info">No hay boletas pendientes por imprimir en este local.</div>';
            }
            ?>
        </div>
    </div>

    <!-- ...onda decorativa... -->
    <svg class="wave" viewBox="0 0 1440 180" preserveAspectRatio="none">
        <path fill="#4fc3f7" fill-opacity="0.6" d="M0,120 C360,180 1080,60 1440,120 L1440,180 L0,180 Z"></path>
        <path fill="#81d4fa" fill-opacity="0.5" d="M0,140 C400,100 1040,180 1440,140 L1440,180 L0,180 Z"></path>
        <path fill="#b3e0ff" fill-opacity="0.4" d="M0,180 C400,160 1040,180 1440,180 L1440,180 L0,180 Z"></path>
    </svg>
    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
    // --- Configuración de totales ---
    let abonos = [];
    let incluirPropina = true;
    let modoPago = "boleta";
    let pagoCompleto = false;
    let descuentoManual = 0;
    let descuentoAplicado = 0;
    let descuentoBloqueado = false;
    let subtotalSinPropina = 0;
    let propinaSugerida = 0;
    let subtotalConPropina = 0;
    let pedidoSeleccionado = null;

    // Empresas desde PHP
    const empresasData = <?php echo json_encode($empresas); ?>;

    function abrirModalPago(tipo, pedido_numero, subtotal, propina, total) {
      modoPago = tipo;
      pedidoSeleccionado = pedido_numero;
      subtotalSinPropina = subtotal;
      propinaSugerida = propina;
      subtotalConPropina = subtotal + propina;
      document.getElementById('modal-pedido-numero').value = pedido_numero;
      document.getElementById('empresa-select-modal').style.display = (tipo === 'factura') ? '' : 'none';
      document.getElementById('modalPagoLabel').textContent = tipo === 'factura' ? "Registrar Pago Factura" : "Registrar Pago Boleta";
      document.getElementById('btnImprimirModal').textContent = tipo === 'factura' ? "Imprimir Factura" : "Imprimir Boleta";
      document.getElementById('propina-sugerida-modal').textContent = propinaSugerida.toLocaleString();
      document.getElementById('formPago').reset();
      abonos = [];
      actualizarAbonos();
      const modal = new bootstrap.Modal(document.getElementById('modalPago'));
      modal.show();
    }

    document.getElementById('empresa-modal').addEventListener('change', function() {
      const idx = this.value;
      if (empresasData[idx]) {
        const empresa = empresasData[idx];
        document.getElementById('factura-empresa-rut').textContent = empresa.EMRUT;
        document.getElementById('factura-empresa-telefono').textContent = empresa.EMTELEFONO;
        document.getElementById('factura-empresa-correo').textContent = empresa.EMCORREO;
    document.getElementById('factura-empresa-direccion').textContent = (empresa.EMCALLE ? empresa.EMCALLE : "") + (empresa.EMNUMEROCALLE ? " " + empresa.EMNUMEROCALLE : "");
      } else {
        document.getElementById('factura-empresa-rut').textContent = "---";
        document.getElementById('factura-empresa-telefono').textContent = "---";
        document.getElementById('factura-empresa-correo').textContent = "---";
        document.getElementById('factura-empresa-direccion').textContent = "---";
      }
      actualizarBotonesImprimir();
    });

    function getTotalAPagar() {
      const base = incluirPropina ? subtotalConPropina : subtotalSinPropina;
      return Math.max(base - (descuentoAplicado || descuentoManual), 0);
    }

    function actualizarBotonesImprimir() {
      const empresaValida = document.getElementById('empresa-modal').value !== "";
      if (modoPago === "factura") {
        document.getElementById('btnImprimirModal').disabled = !(pagoCompleto && empresaValida);
      } else {
        document.getElementById('btnImprimirModal').disabled = !pagoCompleto;
      }
    }

    function actualizarAbonos() {
      const lista = document.getElementById('lista-abonos');
      lista.innerHTML = '';
      let total = 0;
      abonos.forEach((abono, idx) => {
        total += abono.monto;
        const div = document.createElement('div');
        div.className = "alert alert-secondary py-2 d-flex justify-content-between align-items-center mb-2";
        div.innerHTML = `<span><strong>${abono.metodo}</strong> - $${abono.monto.toLocaleString()}</span><button class="btn btn-sm btn-danger" onclick="eliminarAbono(${idx})">Eliminar</button>`;
        lista.appendChild(div);
      });
      document.getElementById('total-abonado').textContent = total.toLocaleString();
      const tienePagos = abonos.length > 0;
      document.getElementById('descuentoManual').disabled = tienePagos;
      document.getElementById('incluirPropina').disabled = tienePagos || descuentoAplicado > 0;
      const totalAPagar = getTotalAPagar();
      let restante = Math.max(totalAPagar - total, 0);
      document.getElementById('restante').textContent = restante.toLocaleString();
      let vuelto = total > totalAPagar ? total - totalAPagar : 0;
      document.getElementById('vuelto').textContent = vuelto > 0 ? `Vuelto: $${vuelto.toLocaleString()}` : '';
      let info = `<span>Subtotal sin propina: <b>$${subtotalSinPropina.toLocaleString()}</b></span><br>`;
      info += `<span>Subtotal con propina: <b>$${subtotalConPropina.toLocaleString()}</b> (propina sugerida $${propinaSugerida.toLocaleString()})</span>`;
      if (!incluirPropina && total >= subtotalSinPropina) {
        info += `<br><span class="text-warning">Has pagado el subtotal sin propina.</span>`;
      }
      if (incluirPropina && total >= subtotalConPropina) {
        info += `<br><span class="text-success">¡Pago completo incluyendo propina!</span>`;
      }
      document.getElementById('info-pago').innerHTML = info;
      pagoCompleto = total >= totalAPagar;
      actualizarBotonesImprimir();
    }

    function eliminarAbono(idx) {
      abonos.splice(idx, 1);
      actualizarAbonos();
      if (abonos.length === 0 && descuentoAplicado === 0) {
        document.getElementById('incluirPropina').disabled = false;
        document.getElementById('descuentoManual').disabled = false;
      } else {
        document.getElementById('incluirPropina').disabled = true;
      }
    }

    document.getElementById('formPago').addEventListener('submit', function(e) {
      e.preventDefault();
      const metodo = document.getElementById('metodo-pago').value;
      const monto = parseInt(document.getElementById('monto-abono').value, 10);
      const totalAPagar = getTotalAPagar();
      const totalAbonado = abonos.reduce((sum, abono) => sum + abono.monto, 0);
      const restante = Math.max(totalAPagar - totalAbonado, 0);
      const errorDiv = document.getElementById('monto-error');
      errorDiv.style.display = 'none';
      errorDiv.textContent = '';
      if (!metodo || isNaN(monto) || monto < 1) return;
      abonos.push({metodo, monto});
      document.getElementById('formPago').reset();
      errorDiv.style.display = 'none';
      actualizarAbonos();
      document.getElementById('incluirPropina').disabled = true;
      document.getElementById('descuentoManual').disabled = true;
    });

    document.getElementById('descuentoManual').addEventListener('input', function() {
      if (descuentoBloqueado) return;
      const valor = parseInt(this.value, 10) || 0;
      const totalBase = incluirPropina ? subtotalConPropina : subtotalSinPropina;
      const errorDiv = document.getElementById('descuento-error');
      const propinaCheckbox = document.getElementById('incluirPropina');
      if (this.value !== "" && valor > 0) {
        propinaCheckbox.disabled = true;
      } else {
        propinaCheckbox.disabled = false;
      }
      if (valor < 0) {
        errorDiv.textContent = "El descuento no puede ser negativo.";
        errorDiv.style.display = 'block';
        this.value = 0;
        descuentoManual = 0;
      } else if (valor > totalBase) {
        errorDiv.textContent = "El descuento no puede ser mayor al total.";
        errorDiv.style.display = 'block';
        this.value = totalBase;
        descuentoManual = totalBase;
      } else {
        errorDiv.style.display = 'none';
        descuentoManual = valor;
      }
      actualizarAbonos();
    });

    document.getElementById('incluirPropina').addEventListener('change', function() {
      incluirPropina = this.checked;
      actualizarAbonos();
    });
    </script>
    <script src="js/darkmode.js"></script>



</body>
</html>