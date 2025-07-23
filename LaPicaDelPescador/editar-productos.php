<?php
    include "php_scripts\configs_oracle\config_pdo.php"
?>

<?php
session_start();

if (!isset($_SESSION['TRID']) || !isset($_SESSION['TRRUN']) || !isset($_SESSION['TRNOMBRES']) || !isset($_SESSION['TRCARGO']) || !ISSET($_SESSION['LOCAL_LOID'])) {
    header("Location: index.php");
    exit;
}

// --- MANEJO DE FORMULARIO PARA AGREGAR/EDITAR PRODUCTO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $precio = isset($_POST['precio']) ? intval($_POST['precio']) : 0;
    $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : '';
    $local_loid = $_SESSION['LOCAL_LOID'];
    $msg = '';
    try {
        if ($accion === 'agregar') {
            $enstock = $enmarca = $disponibilidad = null;
            if ($tipo === 'envasado') {
                $enstock = isset($_POST['stock']) ? intval($_POST['stock']) : null;
                $enmarca = isset($_POST['marca']) ? $_POST['marca'] : null;
                $tipo_oracle = 'Envasado';
            } elseif ($tipo === 'platillo') {
                $disponibilidad = (isset($_POST['disponible']) && $_POST['disponible'] === 'true') ? 1 : 0;
                $tipo_oracle = 'Preparado';
            } else {
                throw new Exception('Tipo de producto no válido');
            }
            $sql = "CALL RTHEARTLESS.AGREGARPRODUCTO(:P_NOMBRE, :P_DESCRIPCION, :P_PRECIO, :P_TIPO, :P_ENSTOCK, :P_ENMARCA, :P_DISPONIBILIDAD, :P_LOCAL_ID)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':P_NOMBRE', $nombre);
            $stmt->bindParam(':P_DESCRIPCION', $descripcion);
            $stmt->bindParam(':P_PRECIO', $precio);
            $stmt->bindParam(':P_TIPO', $tipo_oracle);
            $stmt->bindParam(':P_ENSTOCK', $enstock);
            $stmt->bindParam(':P_ENMARCA', $enmarca);
            $stmt->bindParam(':P_DISPONIBILIDAD', $disponibilidad);
            $stmt->bindParam(':P_LOCAL_ID', $local_loid);
            $stmt->execute();
            $msg = 'Producto agregado correctamente.';
        } elseif ($accion === 'editar') {
            $prid = isset($_POST['id']) ? intval($_POST['id']) : null;
            $enstock = $enmarca = $disponibilidad = null;
            if ($tipo === 'envasado') {
                $enstock = isset($_POST['stock']) ? intval($_POST['stock']) : null;
                $enmarca = isset($_POST['marca']) ? $_POST['marca'] : null;
                $tipo_oracle = 'Envasado';
            } elseif ($tipo === 'platillo') {
                $disponibilidad = (isset($_POST['disponible']) && $_POST['disponible'] === 'true') ? 1 : 0;
                $tipo_oracle = 'Preparado';
            } else {
                throw new Exception('Tipo de producto no válido');
            }
            $sql = "CALL RTHEARTLESS.EDITARPRODUCTO(:P_PRID, :P_NOMBRE, :P_DESCRIPCION, :P_PRECIO, :P_TIPO, :P_ENSTOCK, :P_ENMARCA, :P_DISPONIBILIDAD)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':P_PRID', $prid);
            $stmt->bindParam(':P_NOMBRE', $nombre);
            $stmt->bindParam(':P_DESCRIPCION', $descripcion);
            $stmt->bindParam(':P_PRECIO', $precio);
            $stmt->bindParam(':P_TIPO', $tipo_oracle);
            $stmt->bindParam(':P_ENSTOCK', $enstock);
            $stmt->bindParam(':P_ENMARCA', $enmarca);
            $stmt->bindParam(':P_DISPONIBILIDAD', $disponibilidad);
            $stmt->execute();
            $msg = 'Producto editado correctamente.';
        }
    } catch (Exception $e) {
        $msg = 'Error: ' . $e->getMessage();
    }
    echo "<script>alert('{$msg}'); window.location.href='editar-productos.php';</script>";
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Productos - La Pica del Pescador</title>
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
                        <a class="nav-link d-flex flex-column text-center active" href="editar-productos.php">
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
        <h1 class="mb-4">Editar Productos</h1>
        <!-- Formulario para añadir/editar producto -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Añadir nuevo producto
            </div>
            <div class="card-body">
                <form id="form-producto" method="POST">
                    <input type="hidden" name="accion" id="accion" value="agregar">
                    <input type="hidden" name="id" id="producto-id" value="">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="nombre" class="form-label">Nombre*</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del producto" required>
                        </div>
                        <div class="col-md-4">
                            <label for="precio" class="form-label">Precio ($)*</label>
                            <input type="number" class="form-control" id="precio" name="precio" placeholder="Precio" min="0" required>
                        </div>
                        <div class="col-md-4">
                            <label for="tipo" class="form-label">Tipo*</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Selecciona tipo</option>
                                <option value="platillo">Platillo preparado</option>
                                <option value="envasado">Producto envasado</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="descripcion" class="form-label">Descripción*</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2" placeholder="Descripción del producto" required></textarea>
                        </div>
                        <div class="col-md-4 d-none" id="grupo-disponibilidad">
                            <label for="disponible" class="form-label">¿Disponible?*</label>
                            <select class="form-select" id="disponible" name="disponible" required>
                                <option value="">Selecciona</option>
                                <option value="true">Sí</option>
                                <option value="false">No</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-none" id="grupo-stock">
                            <label for="stock" class="form-label">Stock*</label>
                            <input type="number" class="form-control" id="stock" name="stock" min="0" required>
                        </div>
                        <div class="col-md-4 d-none" id="grupo-marca">
                            <label for="marca" class="form-label">Marca*</label>
                            <input type="text" class="form-control" id="marca" name="marca" required>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-success" id="btn-agregar">Agregar producto</button>
                            <button type="button" class="btn btn-warning ms-2 d-none" id="btn-guardar-cambios">Guardar cambios</button>
                            <button type="button" class="btn btn-secondary ms-2 d-none" id="btn-cancelar">Cancelar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Tabla de productos existentes -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                Productos registrados
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="tabla-productos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Tipo</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- AQUI SE AGREGAN ELEMENTOS DE FORMA DINAMICA -->
                            <?php
                                $local_loid = $_SESSION['LOCAL_LOID'];
                                $sql = "SELECT PrID, PrNombre, PrPrecio, PrTipo, PrDescripcion FROM Producto WHERE Local_LoID = :local_loid";
                                $stmt = $conn->prepare($sql);
                                $stmt->bindParam(':local_loid', $local_loid, PDO::PARAM_INT);
                                $stmt->execute();

                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $prid = $row['PRID'];
                                    $tipo = $row['PRTIPO'];
                                    $disponible = null;
                                    $stock = null;
                                    $marca = null;
                                    if ($tipo == 'Preparado') {
                                        $sqlPrep = "SELECT PPDisponibilidad FROM Platillo_Preparado WHERE PrID = :prid";
                                        $stmtPrep = $conn->prepare($sqlPrep);
                                        $stmtPrep->execute([':prid' => $prid]);
                                        $prep = $stmtPrep->fetch(PDO::FETCH_ASSOC);
                                        $disponible = $prep ? $prep['PPDISPONIBILIDAD'] : null;
                                    } elseif ($tipo == 'Envasado') {
                                        $sqlEnv = "SELECT EnStock, EnMarca FROM Envasado WHERE PrID = :prid";
                                        $stmtEnv = $conn->prepare($sqlEnv);
                                        $stmtEnv->execute([':prid' => $prid]);
                                        $env = $stmtEnv->fetch(PDO::FETCH_ASSOC);
                                        $stock = $env ? $env['ENSTOCK'] : null;
                                        $marca = $env ? $env['ENMARCA'] : null;
                                    }
                                    echo "<tr ";
                                    // Pasar los datos extra como atributos data-
                                    if ($tipo == 'Preparado') {
                                        echo "data-disponible='" . ($disponible ? 'true' : 'false') . "' ";
                                    } elseif ($tipo == 'Envasado') {
                                        echo "data-stock='" . htmlspecialchars($stock) . "' data-marca='" . htmlspecialchars($marca) . "' ";
                                    }
                                    echo ">";
                                    echo "<td>{$row['PRID']}</td>";
                                    echo "<td>{$row['PRNOMBRE']}</td>";
                                    echo "<td>$" . number_format($row['PRPRECIO'], 0, ',', '.') . "</td>";
                                    echo "<td>" . ($tipo == 'Preparado' ? 'Platillo preparado' : 'Producto envasado') . "</td>";
                                    echo "<td>{$row['PRDESCRIPCION']}</td>";
                                    echo "<td><button class='btn btn-warning btn-sm btn-editar'>Editar</button></td>";
                                    echo "</tr>";
                                }
                            ?>
                        </tbody>
                    </table>
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
    let productos = [];
    document.addEventListener("DOMContentLoaded", () => {

        // Obtener productos desde el tbody generado por PHP
        document.querySelectorAll("#tabla-productos tbody tr").forEach(tr => {
            const tds = tr.querySelectorAll("td");
            const tipo = tds[3].textContent.includes("Platillo") ? "platillo" : "envasado";
            let disponible = null, stock = null, marca = null;
            if (tipo === "platillo") {
                disponible = tr.dataset.disponible === "true";
            } else if (tipo === "envasado") {
                stock = tr.dataset.stock ? parseInt(tr.dataset.stock, 10) : null;
                marca = tr.dataset.marca || null;
            }
            productos.push({
                id: parseInt(tds[0].textContent, 10),
                nombre: tds[1].textContent,
                precio: parseInt(tds[2].textContent.replace(/\D/g, ""), 10),
                tipo,
                descripcion: tds[4].textContent,
                disponible,
                stock,
                marca
            });
        });

        let editandoId = null;

        // Mostrar/ocultar campos según tipo
        const tipoSelect = document.getElementById("tipo");
        const grupoDisponibilidad = document.getElementById("grupo-disponibilidad");
        const grupoStock = document.getElementById("grupo-stock");
        const grupoMarca = document.getElementById("grupo-marca");
        tipoSelect.addEventListener("change", function() {
            if (this.value === "platillo") {
                grupoDisponibilidad.classList.remove("d-none");
                grupoStock.classList.add("d-none");
                grupoMarca.classList.add("d-none");
                document.getElementById("disponible").required = true;
                document.getElementById("stock").required = false;
                document.getElementById("marca").required = false;
            } else if (this.value === "envasado") {
                grupoDisponibilidad.classList.add("d-none");
                grupoStock.classList.remove("d-none");
                grupoMarca.classList.remove("d-none");
                document.getElementById("disponible").required = false;
                document.getElementById("stock").required = true;
                document.getElementById("marca").required = true;
            } else {
                grupoDisponibilidad.classList.add("d-none");
                grupoStock.classList.add("d-none");
                grupoMarca.classList.add("d-none");
                document.getElementById("disponible").required = false;
                document.getElementById("stock").required = false;
                document.getElementById("marca").required = false;
            }
        });

        function renderProductos() {
            const tbody = document.querySelector("#tabla-productos tbody");
            tbody.innerHTML = "";
            productos.forEach(producto => {
                const tr = document.createElement("tr");
                let extra = "";
                if (producto.tipo === "platillo") {
                    extra = `<span class='badge bg-${producto.disponible ? "success" : "danger"} ms-2'>${producto.disponible ? "Disponible" : "No disponible"}</span>`;
                } else if (producto.tipo === "envasado") {
                    extra = `<span class='badge bg-info ms-2'>Stock: ${producto.stock ?? 0}</span> <span class='badge bg-secondary ms-2'>Marca: ${producto.marca ?? ""}</span>`;
                }
                tr.innerHTML = `
                    <td>${producto.id}</td>
                    <td>${producto.nombre}</td>
                    <td>$${producto.precio.toLocaleString()}</td>
                    <td>${producto.tipo === "platillo" ? "Platillo preparado" : "Producto envasado"}</td>
                    <td>${producto.descripcion} ${extra}</td>
                    <td>
                        <button class="btn btn-warning btn-sm btn-editar">Editar</button>
                    </td>
                `;
                tr.dataset.id = producto.id;
                tr.dataset.disponible = producto.disponible;
                tr.dataset.stock = producto.stock;
                tr.dataset.marca = producto.marca;
                tbody.appendChild(tr);
            });
        }

        // Quitar renderProductos() inicial, la tabla se llenará cuando cargarProductos() termine
        renderProductos();

        const form = document.getElementById("form-producto");
        const btnAgregar = document.getElementById("btn-agregar");
        const btnGuardar = document.getElementById("btn-guardar-cambios");
        const btnCancelar = document.getElementById("btn-cancelar");

        form.addEventListener("submit", function(e) {
            e.preventDefault();
            const nombre = document.getElementById("nombre").value.trim();
            const precio = parseInt(document.getElementById("precio").value, 10);
            const tipo = document.getElementById("tipo").value;
            const descripcion = document.getElementById("descripcion").value.trim();
            let disponible = null, stock = null, marca = null;
            if (!nombre || isNaN(precio) || !tipo || !descripcion) return;
            if (tipo === "platillo") {
                disponible = document.getElementById("disponible").value;
                if (disponible === "") return;
                disponible = disponible === "true";
            }
            if (tipo === "envasado") {
                stock = parseInt(document.getElementById("stock").value, 10);
                marca = document.getElementById("marca").value.trim();
                if (isNaN(stock) || stock < 0 || !marca) return;
            }
            // Enviar al backend
            const formData = new FormData();
            formData.append('nombre', nombre);
            formData.append('precio', precio);
            formData.append('tipo', tipo);
            formData.append('descripcion', descripcion);
            if (tipo === "platillo") formData.append('disponible', disponible ? 'true' : 'false');
            if (tipo === "envasado") {
                formData.append('stock', stock);
                formData.append('marca', marca);
            }
            if (editandoId === null) {
                formData.append('accion', 'agregar');
            } else {
                formData.append('accion', 'editar');
                formData.append('id', editandoId);
            }
            fetch(window.location.pathname, {
                method: 'POST',
                body: formData
            })
            .then(() => {
                alert('Operación realizada correctamente.');
                window.location.reload();
            });
        });

        btnCancelar.addEventListener("click", function() {
            editandoId = null;
            form.reset();
            btnAgregar.classList.remove("d-none");
            btnGuardar.classList.add("d-none");
            btnCancelar.classList.add("d-none");
        });

        document.querySelector("#tabla-productos tbody").addEventListener("click", function(e) {
            const tr = e.target.closest("tr");
            const id = parseInt(tr.dataset.id, 10);
            if (e.target.classList.contains("btn-editar")) {
                // Editar producto
                const prod = productos.find(p => p.id === id);
                if (prod) {
                    document.getElementById("nombre").value = prod.nombre;
                    document.getElementById("precio").value = prod.precio;
                    document.getElementById("tipo").value = prod.tipo;
                    document.getElementById("descripcion").value = prod.descripcion;
                    tipoSelect.dispatchEvent(new Event("change"));
                    if (prod.tipo === "platillo") {
                        document.getElementById("disponible").value = prod.disponible ? "true" : "false";
                    }
                    if (prod.tipo === "envasado") {
                        document.getElementById("stock").value = prod.stock ?? 0;
                        document.getElementById("marca").value = prod.marca ?? "";
                    }
                    editandoId = id;
                    btnAgregar.classList.add("d-none");
                    btnGuardar.classList.remove("d-none");
                    btnCancelar.classList.remove("d-none");
                }
            }
        });

        btnGuardar.addEventListener("click", function() {
            form.requestSubmit();
        });
    });
    </script>
</body>
</html>
