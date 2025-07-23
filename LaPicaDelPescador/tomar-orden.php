<?php
include "php_scripts/configs_oracle/config_pdo.php";
session_start();
if (!isset($_SESSION['TRID']) || !isset($_SESSION['TRRUN']) || !isset($_SESSION['TRNOMBRES']) || !isset($_SESSION['TRCARGO']) || !isset($_SESSION['LOCAL_LOID'])) {
    header("Location: index.php");
    exit;
}

// Obtener el local asociado al trabajador actual
$trid = $_SESSION['TRID'];
$sql_local = "SELECT LOCAL_LOID FROM TRABAJADOR WHERE TRID = :trid";
$stmt_local = $conn->prepare($sql_local);
$stmt_local->bindParam(':trid', $trid, PDO::PARAM_INT);
$stmt_local->execute();
$local_loid = $stmt_local->fetchColumn();
// Obtener mesas disponibles del local
$sql_mesas = "SELECT MEID, MENUMEROINTERNO FROM MESALOCAL WHERE LOCAL_LOID = :local_loid AND MEACTIVO = 1 ORDER BY MENUMEROINTERNO";
$stmt_mesas = $conn->prepare($sql_mesas);
$stmt_mesas->bindParam(':local_loid', $local_loid, PDO::PARAM_INT);
$stmt_mesas->execute();
$mesas = $stmt_mesas->fetchAll(PDO::FETCH_ASSOC);

// Nombre y id del garzón actual
$garzon_nombre = $_SESSION['TRNOMBRES'] . ' ' . $_SESSION['TRAPELLIDOPATERNO'] . ' ' . $_SESSION['TRAPELLIDOMATERNO'];
$garzon_id = $_SESSION['TRID'];

// Obtener productos del local siempre y cuando esten disponibles segun su tipo
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
$productos = $stmt_productos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tomar Orden - La Pica del Pescador</title>
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
            margin-top: 90px;
            z-index: 10;
            position: relative;
        }
        .navbar .btn:hover, .navbar .btn:focus {
            background-color: #1976d2;
            color: #fff;
            transition: background 0.2s, color 0.2s;
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
                        <a class="nav-link d-flex flex-column text-center active" href="tomar-orden.php">
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

    <!-- Contenido principal -->
    <div class="container main-content">
        <h1 class="mb-4">Tomar Orden</h1>
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Nueva Orden
            </div>
            <div class="card-body">
                <form method="post" id="form-tomar-orden">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="mesa" class="form-label">Mesa</label>
                            <select class="form-select" id="mesa" name="mesa" required>
                                <option value="">Selecciona mesa</option>
                                <?php
                                foreach ($mesas as $mesa) {
                                    echo '<option value="' . htmlspecialchars($mesa['MEID']) . '">Mesa ' . htmlspecialchars($mesa['MENUMEROINTERNO']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="garzon" class="form-label">Garzón</label>
                            <input type="text" class="form-control" id="garzon" name="garzon_nombre" value="<?php echo htmlspecialchars($garzon_nombre); ?>" readonly>
                            <input type="hidden" name="garzon_id" value="<?php echo htmlspecialchars($garzon_id); ?>">
                        </div>
                    </div>
                    <hr>
                    <h5>Agregar productos</h5>
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label for="producto" class="form-label">Producto</label>
                            <select class="form-select" id="producto" name="producto">
                                <option value="">Selecciona un producto</option>
                                <?php
                                foreach ($productos as $prod) {
                                    $label = htmlspecialchars($prod['PRNOMBRE']) . ' (' . htmlspecialchars($prod['PRTIPO']) . ') - $' . htmlspecialchars($prod['PRPRECIO']);
                                    echo '<option value="' . htmlspecialchars($prod['PRID']) . '">' . $label . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="cantidad" class="form-label">Cantidad</label>
                            <input type="number" class="form-control" id="cantidad" name="cantidad" min="1" value="1">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-success w-100">Agregar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Tabla de productos agregados a la orden -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                Productos en la Orden
            </div>
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- JS rellena aquí los productos agregados -->
                    </tbody>
                </table>
                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary">Enviar Orden</button>
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
    // Asegurarse que el DOM esté cargado antes de asignar eventos
    window.addEventListener('DOMContentLoaded', function() {
        // --- Gestión local de productos agregados a la orden ---
        const selectProducto = document.getElementById('producto');
        const inputCantidad = document.getElementById('cantidad');
        const btnAgregar = document.querySelector('button.btn-success.w-100');
        const selectMesa = document.getElementById('mesa');
        // Buscar la tabla correcta de productos agregados
        const cards = document.querySelectorAll('.card');
        let tablaProductos = null;
        let tbodyProductos = null;
        for (let i = 0; i < cards.length; i++) {
            const card = cards[i];
            const header = card.querySelector('.card-header');
            if (header && header.textContent.includes('Productos en la Orden')) {
                tablaProductos = card.querySelector('table');
                tbodyProductos = tablaProductos ? tablaProductos.querySelector('tbody') : null;
                break;
            }
        }

        // Mapeo de productos: id -> {nombre, precio}
        const productosMap = {};
        // --- Generar el select de productos con el id correcto ---
        // En el PHP, asegúrate que el value del <option> sea el PRID
        // <option value="<?php echo htmlspecialchars($prod['PRID']); ?>">...</option>
        // En JS, verifica que los options tengan el value correcto
        // Además, agrega ENSTOCK si el producto es envasado
        for (let i = 0; i < selectProducto.options.length; i++) {
            const opt = selectProducto.options[i];
            console.log('Option', i, 'value:', opt.value, 'text:', opt.text);
            if (opt.value) {
                let nombre = opt.text;
                let precio = '';
                let tipo = '';
                let enstock = null;
                // Buscar el producto en el array PHP para obtener ENSTOCK si existe
                <?php
                // Generar un array JS con los datos de productos y ENSTOCK si existe
                $jsProductos = [];
                foreach ($productos as $prod) {
                    $jsProd = [
                        'id' => $prod['PRID'],
                        'nombre' => $prod['PRNOMBRE'],
                        'tipo' => $prod['PRTIPO'],
                        'precio' => $prod['PRPRECIO'],
                    ];
                    // Buscar ENSTOCK si existe en ENVASADO
                    $sql_env = "SELECT ENSTOCK FROM ENVASADO WHERE PRID = :prid";
                    $stmt_env = $conn->prepare($sql_env);
                    $stmt_env->bindParam(':prid', $prod['PRID'], PDO::PARAM_INT);
                    $stmt_env->execute();
                    $enstock = $stmt_env->fetchColumn();
                    if ($enstock !== false) {
                        $jsProd['enstock'] = (int)$enstock;
                    }
                    $jsProductos[] = $jsProd;
                }
                ?>
                const productosDatos = <?php echo json_encode($jsProductos); ?>;
                // Buscar el producto por id
                const datos = productosDatos.find(p => p.id == opt.value);
                if (datos) {
                    nombre = datos.nombre;
                    tipo = datos.tipo;
                    precio = datos.precio;
                    if ('enstock' in datos) enstock = datos.enstock;
                }
                productosMap[opt.value] = { nombre, tipo, precio, enstock };
            }
        }

        // Lista de productos agregados a la orden
        let productosOrden = [];

        function renderProductosOrden() {
            if (!tbodyProductos) return;
            tbodyProductos.innerHTML = '';
            productosOrden.forEach((prod, idx) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${prod.nombre}</td>
                    <td>${prod.cantidad}</td>
                    <td>$${prod.precio}</td>
                    <td><button type="button" class="btn btn-danger btn-sm" data-idx="${idx}">Eliminar</button></td>
                `;
                tbodyProductos.appendChild(tr);
            });
            selectMesa.disabled = productosOrden.length > 0;
            // Asignar evento eliminar a los botones
            if (tbodyProductos) {
                tbodyProductos.querySelectorAll('button[data-idx]').forEach(btn => {
                    btn.onclick = function() {
                        const idx = parseInt(btn.getAttribute('data-idx'));
                        productosOrden.splice(idx, 1);
                        renderProductosOrden();
                        actualizarEstadoAgregar();
                    };
                });
            }
        }

        function actualizarEstadoAgregar() {
            btnAgregar.disabled = (selectMesa.selectedIndex <= 0 || selectProducto.selectedIndex <= 0);
        }

        selectMesa.addEventListener('change', actualizarEstadoAgregar);
        selectProducto.addEventListener('change', actualizarEstadoAgregar);
        inputCantidad.addEventListener('input', actualizarEstadoAgregar);

        btnAgregar.addEventListener('click', function(e) {
            e.preventDefault();
            // El value del select es el id del producto
            const prodId = selectProducto.value;
            console.log('Valor selectProducto.value:', prodId);
            console.log('selectedIndex:', selectProducto.selectedIndex);
            if (!prodId) { console.log('No se seleccionó producto'); return; }
            const cantidad = parseInt(inputCantidad.value);
            if (isNaN(cantidad) || cantidad < 1 || !selectMesa.value) { console.log('Cantidad inválida o mesa no seleccionada'); return; }
            const prodData = productosMap[prodId];
            console.log('prodData:', prodData);
            if (!prodData) { console.log('Producto no encontrado en el mapeo'); return; }
            // Validar stock si es envasado
            if (prodData.tipo && prodData.tipo.toLowerCase().includes('envasado') && prodData.enstock !== null) {
                // Sumar cantidad si ya existe en la orden
                let cantidadTotal = cantidad;
                const existente = productosOrden.find(p => p.id === prodId);
                if (existente) cantidadTotal += existente.cantidad;
                if (cantidadTotal > prodData.enstock) {
                    alert('No puedes agregar más unidades que el stock disponible (' + prodData.enstock + ').');
                    return;
                }
            }
            // Si ya existe el producto, suma la cantidad
            const existente = productosOrden.find(p => p.id === prodId);
            if (existente) {
                existente.cantidad += cantidad;
            } else {
                productosOrden.push({ id: prodId, nombre: prodData.nombre, tipo: prodData.tipo, precio: prodData.precio, cantidad });
            }
            console.log('productosOrden:', productosOrden); // <-- Mostrar en consola la lista actual
            renderProductosOrden();
            selectProducto.selectedIndex = 0;
            inputCantidad.value = 1;
            actualizarEstadoAgregar();
        });

        actualizarEstadoAgregar();
        renderProductosOrden();
    });
    </script>
</body>
</html>