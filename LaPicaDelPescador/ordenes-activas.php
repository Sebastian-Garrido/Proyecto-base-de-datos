
<?php
include "php_scripts\configs_oracle\config_pdo.php";
session_start();
if (!isset($_SESSION['TRID']) || !isset($_SESSION['TRRUN']) || !isset($_SESSION['TRNOMBRES']) || !isset($_SESSION['TRCARGO']) || !ISSET($_SESSION['LOCAL_LOID'])) {
    header("Location: index.php");
    exit;
}

// Procesar POST para finalizar pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar_pedido'])) {
    $pedido_finalizar = intval($_POST['finalizar_pedido']);
    try {
        $conn->beginTransaction();
        // Cambiar estado del pedido
        $stmt = $conn->prepare("UPDATE PEDIDO SET PEESTADO = 1 WHERE PENUMERO = :pedido");
        $stmt->bindParam(':pedido', $pedido_finalizar, PDO::PARAM_INT);
        $stmt->execute();

        // Obtener el trabajador que generó el pedido
        $sql_trabajador = "SELECT TRABAJADOR_TRID FROM PEDIDO WHERE PENUMERO = :pedido";
        $stmt_trabajador = $conn->prepare($sql_trabajador);
        $stmt_trabajador->bindParam(':pedido', $pedido_finalizar, PDO::PARAM_INT);
        $stmt_trabajador->execute();
        $trabajador_id = $stmt_trabajador->fetchColumn();

        // Crear boleta (doctrib) usando el procedimiento
        $fecha_emision = date('Y-m-d');
        $hora_emision = date('Y-m-d H:i:s');
        $vuelto = 0;
        $pago_propina = 1;
        $descuento = 0;
        $tipo = 'Boleta';
        $empresa_id = null;

        $stmt_boleta = $conn->prepare("CALL CREARDOCTRIB(TO_DATE(:P_FECHA_EMISION, 'YYYY-MM-DD'), TO_DATE(:P_HORA_EMISION, 'YYYY-MM-DD HH24:MI:SS'), :P_VUELTO, :P_PAGO_PROPINA, :P_DESCUENTO, :P_TIPO, :P_PEDIDO_NUMERO, :P_TRABAJADOR_ID, :P_EMPRESA_ID)");
        $stmt_boleta->bindParam(':P_FECHA_EMISION', $fecha_emision);
        $stmt_boleta->bindParam(':P_HORA_EMISION', $hora_emision);
        $stmt_boleta->bindParam(':P_VUELTO', $vuelto, PDO::PARAM_INT);
        $stmt_boleta->bindParam(':P_PAGO_PROPINA', $pago_propina, PDO::PARAM_INT);
        $stmt_boleta->bindParam(':P_DESCUENTO', $descuento, PDO::PARAM_INT);
        $stmt_boleta->bindParam(':P_TIPO', $tipo);
        $stmt_boleta->bindParam(':P_PEDIDO_NUMERO', $pedido_finalizar, PDO::PARAM_INT);
        $stmt_boleta->bindParam(':P_TRABAJADOR_ID', $trabajador_id, PDO::PARAM_INT);
        $stmt_boleta->bindParam(':P_EMPRESA_ID', $empresa_id);
        $stmt_boleta->execute();

        $conn->commit();
        header("Location: ordenes-activas.php?finalizado=1");
        exit;
    } catch (Exception $e) {
        $conn->rollBack();
        $errorMsg = urlencode('Error al finalizar pedido: ' . $e->getMessage());
        header("Location: ordenes-activas.php?error=$errorMsg");
        exit;
    }
}

// Procesar POST para agregar nuevos detalles a una orden
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pedido']) && isset($_POST['productos_json'])) {
    $pedido = intval($_POST['pedido']);
    $productos = json_decode($_POST['productos_json'], true);
    $errores = [];
    $conn->beginTransaction();
    try {
        foreach ($productos as $prod) {
            $cantidad = intval($prod['cantidad']);
            $precio_unitario = intval($prod['precio']);
            $prid = intval($prod['id']);
            $tipo = isset($prod['tipo']) ? $prod['tipo'] : '';

            // 1. Insertar detallepedido
            $stmt_det = $conn->prepare("CALL CREARDETALLEPEDIDO(:p_DePCantidad, :p_DePPrecioUnitario, :p_PeNumero, :p_PrID)");
            $stmt_det->bindParam(':p_DePCantidad', $cantidad, PDO::PARAM_INT);
            $stmt_det->bindParam(':p_DePPrecioUnitario', $precio_unitario, PDO::PARAM_INT);
            $stmt_det->bindParam(':p_PeNumero', $pedido, PDO::PARAM_INT);
            $stmt_det->bindParam(':p_PrID', $prid, PDO::PARAM_INT);
            $stmt_det->execute();

            // 2. Obtener el DePID recién insertado
            // Suponiendo que DePID es generado por una secuencia y es el último insertado para ese pedido y producto
            $sql_last_depid = "SELECT MAX(DEPid) AS DEPID FROM DETALLEPEDIDO WHERE PEDIDO_PENUMERO = :pedido AND PRODUCTO_PRID = :prid";
            $stmt_last_depid = $conn->prepare($sql_last_depid);
            $stmt_last_depid->bindParam(':pedido', $pedido, PDO::PARAM_INT);
            $stmt_last_depid->bindParam(':prid', $prid, PDO::PARAM_INT);
            $stmt_last_depid->execute();
            $depid_row = $stmt_last_depid->fetch(PDO::FETCH_ASSOC);
            $depid = $depid_row ? $depid_row['DEPID'] : null;

            // 3. Si es preparado, crear comanda solo para este detalle
            if ($tipo === 'Preparado' && $depid) {
                $hora_inicio = date('Y-m-d H:i:s');
                $stmt_comanda = $conn->prepare("CALL CREARCOMANDA(TO_DATE(:p_CoHoraInicio, 'YYYY-MM-DD HH24:MI:SS'), :p_DePID)");
                $stmt_comanda->bindParam(':p_CoHoraInicio', $hora_inicio);
                $stmt_comanda->bindParam(':p_DePID', $depid, PDO::PARAM_INT);
                $stmt_comanda->execute();
            }

            // 4. Si es envasado, bajar stock
            if ($tipo === 'Envasado') {
                $sql_update_stock = "UPDATE ENVASADO SET ENSTOCK = ENSTOCK - :cantidad WHERE PRID = :prid";
                $stmt_update_stock = $conn->prepare($sql_update_stock);
                $stmt_update_stock->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
                $stmt_update_stock->bindParam(':prid', $prid, PDO::PARAM_INT);
                $stmt_update_stock->execute();
            }
        }

        $conn->commit();
        header("Location: ordenes-activas.php?success=1");
        exit;
    } catch (Exception $e) {
        $conn->rollBack();
        $errorMsg = urlencode('Error al guardar detalles: ' . $e->getMessage());
        header("Location: ordenes-activas.php?error=$errorMsg");
        exit;
    }
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
        <h1 class="mb-4">Órdenes Activas</h1>
        <div class="row" id="ordenes-container">
            <?php
            if (empty($pedidos)) {
                echo '<div class="col-12"><div class="alert alert-info text-center">No hay órdenes activas en este local.</div></div>';
            } else {
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

                    $nombre_trabajador = $pedido['TRNOMBRES'] . ' ' . $pedido['TRAPELLIDOPATERNO'] . ' ' . $pedido['TRAPELLIDOMATERNO'];
                    $header = "Orden #{$pedido['PENUMERO']} - Mesa {$pedido['MENUMEROINTERNO']} - $nombre_trabajador";
                    // Botón Editar con data-*
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
                    echo '        <button class="btn btn-warning btn-editar-orden w-100" data-bs-toggle="modal" data-bs-target="#editarOrdenModal"'
                        . ' data-pedido="' . htmlspecialchars($pedido['PENUMERO']) . '"'
                        . ' data-mesa="' . htmlspecialchars($pedido['MENUMEROINTERNO']) . '"'
                        . ' data-trabajador="' . htmlspecialchars($nombre_trabajador) . '"'
                        . ' data-productos=\'' . htmlspecialchars(json_encode($productos)) . '\''
                        . '>Editar</button>';
                    // Botón finalizar pedido: formulario POST, ancho completo
                    echo '        <form method="POST" action="ordenes-activas.php" class="d-grid gap-2 mt-2">';
                    echo '          <input type="hidden" name="finalizar_pedido" value="' . htmlspecialchars($pedido['PENUMERO']) . '">';
                    echo '          <button class="btn btn-success w-100" type="submit" onclick="return confirm(\'¿Finalizar este pedido?\')">Finalizar pedido</button>';
                    echo '        </form>';
                    echo '      </div>';
                    echo '    </div>';
                    echo '  </div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>

    <!-- Modal para editar orden -->
    <div class="modal fade" id="editarOrdenModal" tabindex="-1" aria-labelledby="editarOrdenModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editarOrdenModalLabel">Editar Orden</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
          <div class="modal-body">
            <h6>Platillos en la orden</h6>
            <ul class="list-group mb-3" id="platillos-lista"></ul>
            <hr>
            <h6>Añadir platillo</h6>
            <div class="input-group mb-3">
                <select class="form-select" id="nuevo-platillo">
                    <option value="">Selecciona un platillo</option>
                    <?php
                    $sql_productos = "
                    SELECT P.PRID, P.PRNOMBRE, P.PRTIPO, P.PRPRECIO
                    FROM PRODUCTO P
                    LEFT JOIN PLATILLO_PREPARADO PP ON P.PRID = PP.PRID
                    LEFT JOIN ENVASADO E ON P.PRID = E.PRID
                    WHERE P.LOCAL_LOID = :local_loid
                    AND (
                        (PP.PRID IS NOT NULL AND PP.PPDISPONIBILIDAD = 1)
                        OR (E.PRID IS NOT NULL AND E.ENSTOCK > 0)
                        OR (PP.PRID IS NULL AND E.PRID IS NULL)
                    )
                    ORDER BY P.PRNOMBRE
                    ";
                    $stmt_productos = $conn->prepare($sql_productos);
                    $stmt_productos->bindParam(':local_loid', $local_loid, PDO::PARAM_INT);
                    $stmt_productos->execute();
                    $productos_local = $stmt_productos->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($productos_local as $prod) {
                        $label = htmlspecialchars($prod['PRNOMBRE']) . ' (' . htmlspecialchars($prod['PRTIPO']) . ') - $' . htmlspecialchars($prod['PRPRECIO']);
                        echo '<option value="' . htmlspecialchars($prod['PRID']) . '" data-nombre="' . htmlspecialchars($prod['PRNOMBRE']) . '" data-tipo="' . htmlspecialchars($prod['PRTIPO']) . '" data-precio="' . htmlspecialchars($prod['PRPRECIO']) . '">' . $label . '</option>';
                    }
                    ?>
                </select>
                <input type="number" class="form-control" id="cantidad-platillo" min="1" value="1" style="max-width: 80px;">
                <button class="btn btn-success" id="btn-agregar-platillo" type="button">Añadir</button>
            </div>
            <!-- Tabla de productos añadidos en el modal -->
            <div class="mt-3">
                <table class="table table-sm table-bordered" id="tabla-nuevos-platillos" style="display:none;">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
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
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const editarModal = document.getElementById('editarOrdenModal');
        let nuevosPlatillos = [];
        let pedidoActual = null;
        editarModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;
            const pedido = button.getAttribute('data-pedido');
            const mesa = button.getAttribute('data-mesa');
            const trabajador = button.getAttribute('data-trabajador');
            const productos = JSON.parse(button.getAttribute('data-productos'));
            pedidoActual = pedido;
            nuevosPlatillos = [];

            // Cambia el título del modal
            const modalTitle = editarModal.querySelector('.modal-title');
            modalTitle.textContent = `Editar Orden #${pedido} - Mesa ${mesa} - ${trabajador}`;

            // Llena la lista de platillos
            const lista = editarModal.querySelector('#platillos-lista');
            lista.innerHTML = '';
            productos.forEach(prod => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `${prod.PRNOMBRE}<span><span class="badge bg-secondary rounded-pill me-2">${prod.DEPCANTIDAD}</span></span>`;
                lista.appendChild(li);
            });

            // Limpiar tabla de nuevos platillos
            renderNuevosPlatillos();
        });

        // Lógica para añadir productos a la tabla de nuevos platillos
        const btnAgregar = document.getElementById('btn-agregar-platillo');
        const selectPlatillo = document.getElementById('nuevo-platillo');
        const inputCantidad = document.getElementById('cantidad-platillo');
        const tablaNuevos = document.getElementById('tabla-nuevos-platillos');
        const tbodyNuevos = tablaNuevos.querySelector('tbody');

        btnAgregar.addEventListener('click', function() {
            const prodId = selectPlatillo.value;
            if (!prodId) return;
            const cantidad = parseInt(inputCantidad.value);
            if (isNaN(cantidad) || cantidad < 1) return;
            const opt = selectPlatillo.options[selectPlatillo.selectedIndex];
            const nombre = opt.getAttribute('data-nombre');
            const tipo = opt.getAttribute('data-tipo');
            const precio = opt.getAttribute('data-precio');
            nuevosPlatillos.push({ id: prodId, nombre, tipo, precio, cantidad });
            renderNuevosPlatillos();
            selectPlatillo.selectedIndex = 0;
            inputCantidad.value = 1;
        });

        function renderNuevosPlatillos() {
            if (nuevosPlatillos.length === 0) {
                tablaNuevos.style.display = 'none';
                tbodyNuevos.innerHTML = '';
                return;
            }
            tablaNuevos.style.display = '';
            tbodyNuevos.innerHTML = '';
            nuevosPlatillos.forEach((prod, idx) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${prod.nombre}</td><td>${prod.cantidad}</td><td><button type="button" class="btn btn-danger btn-sm" data-idx="${idx}">Eliminar</button></td>`;
                tbodyNuevos.appendChild(tr);
            });
            // Botones eliminar
            tbodyNuevos.querySelectorAll('button[data-idx]').forEach(btn => {
                btn.onclick = function() {
                    const idx = parseInt(btn.getAttribute('data-idx'));
                    nuevosPlatillos.splice(idx, 1);
                    renderNuevosPlatillos();
                };
            });
        }

        // Guardar cambios: enviar nuevos productos al backend por POST tradicional
        const btnGuardar = editarModal.querySelector('.btn-primary');
        btnGuardar.addEventListener('click', function() {
            if (nuevosPlatillos.length === 0) return;
            // Crear y enviar formulario oculto
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'ordenes-activas.php';
            const inputPedido = document.createElement('input');
            inputPedido.type = 'hidden';
            inputPedido.name = 'pedido';
            inputPedido.value = pedidoActual;
            form.appendChild(inputPedido);
            const inputProductos = document.createElement('input');
            inputProductos.type = 'hidden';
            inputProductos.name = 'productos_json';
            inputProductos.value = JSON.stringify(nuevosPlatillos);
            form.appendChild(inputProductos);
            document.body.appendChild(form);
            form.submit();
        });
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const editarModal = document.getElementById('editarOrdenModal');
        editarModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;
            const pedido = button.getAttribute('data-pedido');
            const mesa = button.getAttribute('data-mesa');
            const trabajador = button.getAttribute('data-trabajador');
            const productos = JSON.parse(button.getAttribute('data-productos'));

            // Cambia el título del modal
            const modalTitle = editarModal.querySelector('.modal-title');
            modalTitle.textContent = `Editar Orden #${pedido} - Mesa ${mesa} - ${trabajador}`;

            // Llena la lista de platillos
            const lista = editarModal.querySelector('#platillos-lista');
            lista.innerHTML = '';
            productos.forEach(prod => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `${prod.PRNOMBRE}<span><span class="badge bg-secondary rounded-pill me-2">${prod.DEPCANTIDAD}</span></span>`;
                lista.appendChild(li);
            });
        });
    });
    </script>
</body>
</html>