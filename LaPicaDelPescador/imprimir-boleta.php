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
                        <a class="nav-link d-flex flex-column text-center active" href="imprimir-boleta.php">
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
        <h1 class="mb-4">Imprimir Boleta o Factura</h1>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Buscar Orden
            </div>
            <div class="card-body">
                <form class="row g-3">
                    <div class="col-md-6">
                        <label for="numeroOrden" class="form-label">Número de Orden</label>
                        <input type="text" class="form-control" id="numeroOrden" placeholder="Ingrese número de orden" required>
                    </div>
                    <div class="col-md-4 align-self-end">
                        <button type="submit" class="btn btn-success w-100">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Vista previa de Documento tributario -->
        <div class="d-flex justify-content-center mb-4">
            <?php
            // Buscar la boleta pendiente (DTCOMPLETADA = 0)
            $sql_boleta = "SELECT * FROM DOCTRIB WHERE DTCOMPLETADA = 0 ORDER BY DTFECHAEMISION DESC FETCH FIRST 1 ROWS ONLY";
            $stmt_boleta = $conn->prepare($sql_boleta);
            $stmt_boleta->execute();
            $boleta = $stmt_boleta->fetch(PDO::FETCH_ASSOC);
            if ($boleta) {
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
            <div id="doc-tributario" class="card boleta-preview shadow-lg">
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
                </div>
            </div>
            <?php } else { ?>
            <div class="alert alert-info">No hay boletas pendientes por imprimir.</div>
            <?php } ?>
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
        const subtotalSinPropina = 15500;
        const propinaSugerida = 1550;
        const subtotalConPropina = subtotalSinPropina + propinaSugerida;
        let abonos = [];
        let incluirPropina = true;
        let modoPago = "boleta"; // "boleta" o "factura"
        let pagoCompleto = false;
        let descuentoManual = 0;
        let descuentoAplicado = 0;
        let descuentoBloqueado = false;

        // Empresas
        const empresasData = [
            {
                nombre: "Mariscos Iquique SpA",
                rut: "76.123.456-7",
                telefono: "+56 9 1111 2222",
                correo: "contacto@mariscos.cl",
                direccion: "Av. Costanera 1234, Iquique"
            },
            {
                nombre: "Mariscos Alto Hospicio Ltda.",
                rut: "77.987.654-3",
                telefono: "+56 9 3333 4444",
                correo: "altohospicio@mariscos.cl",
                direccion: "Calle Principal 456, Alto Hospicio"
            },
            {
                nombre: "Pescados del Norte S.A.",
                rut: "78.555.666-1",
                telefono: "+56 9 5555 6666",
                correo: "ventas@pescadosnorte.cl",
                direccion: "Ruta 5 Norte km 10, Iquique"
            }
        ];

        // Mostrar modal de pago según tipo
        function abrirModalPago(tipo) {
            modoPago = tipo;
            document.getElementById('empresa-select-modal').style.display = (tipo === 'factura') ? '' : 'none';
            document.getElementById('modalPagoLabel').textContent = tipo === 'factura' ? "Registrar Pago Factura" : "Registrar Pago Boleta";
            document.getElementById('btnImprimirModal').textContent = tipo === 'factura' ? "Imprimir Factura" : "Imprimir Boleta";
            document.getElementById('propina-sugerida-modal').textContent = propinaSugerida.toLocaleString();
            const modal = new bootstrap.Modal(document.getElementById('modalPago'));
            modal.show();
            actualizarAbonos();
        }

        document.getElementById('btnPagarBoleta').addEventListener('click', function() {
            abrirModalPago('boleta');
        });
        document.getElementById('btnPagarFactura').addEventListener('click', function() {
            abrirModalPago('factura');
        });

        document.getElementById('empresa-modal').addEventListener('change', function() {
            const idx = this.value;
            if (empresasData[idx]) {
                const empresa = empresasData[idx];
                document.getElementById('factura-empresa-nombre').textContent = empresa.nombre;
                document.getElementById('factura-empresa-rut').textContent = empresa.rut;
                document.getElementById('factura-empresa-telefono').textContent = empresa.telefono;
                document.getElementById('factura-empresa-correo').textContent = empresa.correo;
                document.getElementById('factura-empresa-direccion').textContent = empresa.direccion;
            } else {
                document.getElementById('factura-empresa-nombre').textContent = "---";
                document.getElementById('factura-empresa-rut').textContent = "---";
                document.getElementById('factura-empresa-telefono').textContent = "---";
                document.getElementById('factura-empresa-correo').textContent = "---";
                document.getElementById('factura-empresa-direccion').textContent = "---";
            }
        });

        // --- Pago y totales ---
        function getTotalAPagar() {
            const base = incluirPropina ? subtotalConPropina : subtotalSinPropina;
            return Math.max(base - (descuentoAplicado || descuentoManual), 0);
        }

        function actualizarBotonesImprimir() {
            const empresaValida = document.getElementById('empresa-modal').value !== "";
            // Si tienes dos botones:
            // document.getElementById('btnImprimirBoleta').disabled = !pagoCompleto;
            // document.getElementById('btnImprimirFactura').disabled = !(pagoCompleto && empresaValida);

            // Si tienes un solo botón y el modo es boleta/factura:
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
                div.innerHTML = `
                    <span><strong>${abono.metodo}</strong> - $${abono.monto.toLocaleString()}</span>
                    <button class="btn btn-sm btn-danger" onclick="eliminarAbono(${idx})">Eliminar</button>
                `;
                lista.appendChild(div);
            });
            document.getElementById('total-abonado').textContent = total.toLocaleString();

            // Controlar el descuento según si hay pagos
            const tienePagos = abonos.length > 0;
            document.getElementById('descuentoManual').disabled = tienePagos;
            document.getElementById('btnAgregarDescuento').disabled = tienePagos;

            // Deshabilitar botón de eliminar descuento si hay abonos
            document.getElementById('btnEliminarDescuento').disabled = tienePagos;

            // Si hay pagos, también bloquea la checkbox de propina
            document.getElementById('incluirPropina').disabled = tienePagos || descuentoAplicado > 0;

            // Calcular el total a pagar según propina
            const totalAPagar = getTotalAPagar();
            let restante = Math.max(totalAPagar - total, 0);
            document.getElementById('restante').textContent = restante.toLocaleString();

            // Mostrar vuelto si pagó de más
            let vuelto = total > totalAPagar ? total - totalAPagar : 0;
            document.getElementById('vuelto').textContent = vuelto > 0 ? `Vuelto: $${vuelto.toLocaleString()}` : '';

            // Mostrar info de pago
            let info = `<span>Subtotal sin propina: <b>$${subtotalSinPropina.toLocaleString()}</b></span><br>`;
            info += `<span>Subtotal con propina: <b>$${subtotalConPropina.toLocaleString()}</b> (propina sugerida $${propinaSugerida.toLocaleString()})</span>`;
            if (!incluirPropina && total >= subtotalSinPropina) {
                info += `<br><span class="text-warning">Has pagado el subtotal sin propina.</span>`;
            }
            if (incluirPropina && total >= subtotalConPropina) {
                info += `<br><span class="text-success">¡Pago completo incluyendo propina!</span>`;
            }
            document.getElementById('info-pago').innerHTML = info;

            // Mostrar total pagado y vuelto en la card si el pago está completo
            pagoCompleto = total >= totalAPagar;
            if (modoPago === "boleta" || modoPago == "factura") {
                document.getElementById('pago-doc-trib-info').style.display = pagoCompleto ? '' : 'none';
                document.getElementById('total-pagado-boleta').textContent = total.toLocaleString();
                document.getElementById('vuelto-boleta-monto').textContent = vuelto.toLocaleString();
            } else {
                document.getElementById('pago-doc-trib-info').style.display = 'none';
            }

            // Mostrar descuento en la card si está aplicado
            const filaDescuento = document.getElementById('fila-descuento');
            const descuentoCard = document.getElementById('descuento-card');
            if (descuentoAplicado > 0) {
                filaDescuento.style.display = '';
                descuentoCard.textContent = `$${descuentoAplicado.toLocaleString()}`;
            } else {
                filaDescuento.style.display = 'none';
                descuentoCard.textContent = '$0';
            }
            // Mostrar total en la card
            const totalCard = document.getElementById('total-card');
            totalCard.textContent = `$${getTotalAPagar().toLocaleString()}`;
            // Mostrar info de propina pagada en la card (previsualización)
            const infoPropinaCard = document.getElementById('info-propina-card');
            if (document.getElementById('incluirPropina').checked) {
                infoPropinaCard.innerHTML = '<span class="text-success fw-bold">Pago con propina</span>';
            } else {
                infoPropinaCard.innerHTML = '<span class="text-warning fw-bold">Pago sin propina</span>';
            }

            actualizarBotonesImprimir();
        }

        function eliminarAbono(idx) {
            abonos.splice(idx, 1);
            actualizarAbonos();
            // Si no hay abonos, permitir nuevamente descuento y propina SOLO si tampoco hay descuento aplicado
            if (abonos.length === 0 && descuentoAplicado === 0) {
                document.getElementById('incluirPropina').disabled = false;
                document.getElementById('descuentoManual').disabled = false;
                document.getElementById('btnAgregarDescuento').disabled = false;
            } else {
                // Si queda algún abono o descuento, mantener la checkbox bloqueada
                document.getElementById('incluirPropina').disabled = true;
            }
        }

        document.getElementById('empresa-modal').addEventListener('change', function() {
            actualizarBotonesImprimir();
        });

        document.getElementById('form-abono').addEventListener('submit', function(e) {
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

            if (metodo === "Tarjeta" && monto > restante) {
                errorDiv.textContent = "El monto pagado con tarjeta no puede ser mayor al total por pagar.";
                errorDiv.style.display = 'block';
                return;
            }

            abonos.push({metodo, monto});
            document.getElementById('form-abono').reset();
            errorDiv.style.display = 'none';
            actualizarAbonos();
            // Bloquear propina y descuento al agregar abono
            document.getElementById('incluirPropina').disabled = true;
            document.getElementById('descuentoManual').disabled = true;
            document.getElementById('btnAgregarDescuento').disabled = true;
        });

        document.getElementById('btnAgregarDescuento').addEventListener('click', function() {
            const valor = parseInt(document.getElementById('descuentoManual').value, 10) || 0;
            if (valor > 0) {
                descuentoAplicado = valor;
                document.getElementById('descuento-aplicado-monto').textContent = valor.toLocaleString();
                document.getElementById('descuento-aplicado').style.display = '';
                document.getElementById('descuentoManual').disabled = true;
                document.getElementById('btnAgregarDescuento').disabled = true;
                descuentoBloqueado = true;
                // Aquí iría la lógica para guardar el descuento en la base de datos con fetch/AJAX
            }
            actualizarAbonos();
        });

        // Botón para eliminar descuento
        document.getElementById('btnEliminarDescuento').addEventListener('click', function() {
            if (abonos.length > 0) return; // No hacer nada si hay abonos
            descuentoAplicado = 0;
            descuentoBloqueado = false;
            document.getElementById('descuento-aplicado').style.display = 'none';
            document.getElementById('descuentoManual').value = 0;
            document.getElementById('descuentoManual').disabled = false;
            document.getElementById('btnAgregarDescuento').disabled = false;
            // Si no hay abonos, desbloquear la checkbox, si hay abonos mantenerla bloqueada
            if (abonos.length === 0) {
                document.getElementById('incluirPropina').disabled = false;
            } else {
                document.getElementById('incluirPropina').disabled = true;
            }
            actualizarAbonos();
        });

        document.getElementById('incluirPropina').addEventListener('change', function() {
            incluirPropina = this.checked;
            actualizarAbonos();
        });

        document.getElementById('descuentoManual').addEventListener('input', function() {
            if (descuentoBloqueado) return;
            const valor = parseInt(this.value, 10) || 0;
            const totalBase = incluirPropina ? totalConPropina : totalSinPropina;
            const errorDiv = document.getElementById('descuento-error');
            const propinaCheckbox = document.getElementById('incluirPropina');
            if (this.value !== "" && valor > 0) {
                propinaCheckbox.disabled = true;
                // No modificar el estado checked
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

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('subtotal-boleta').textContent = `$${subtotalSinPropina.toLocaleString()}`;
            document.getElementById('propina-sugerida').textContent = `$${propinaSugerida.toLocaleString()}`;
            document.getElementById('propina-sugerida-modal').textContent = propinaSugerida.toLocaleString();
            actualizarAbonos();
        });
    </script>
    <script src="js/darkmode.js"></script>



</body>
</html>