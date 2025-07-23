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
                        <a class="nav-link d-flex flex-column text-center active" href="ver-boletas.php">
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
            <!-- Ejemplo de boleta, puedes generar dinámicamente con PHP -->
            <div class="col-md-6 mb-4">
                <div class="card boleta-preview shadow-lg">
                    <div class="card-header bg-primary text-white text-center fs-5">
                        Boleta N° 12345 - 2025-07-20
                    </div>
                    <div class="card-body">
                        <p><strong>Mesa:</strong> 7</p>
                        <p><strong>Garzón:</strong> Juan Pérez</p>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-end">Cantidad</th>
                                    <th class="text-end">Precio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Paila Marina</td>
                                    <td class="text-end">2</td>
                                    <td class="text-end">$12.000</td>
                                </tr>
                                <tr>
                                    <td>Empanada de Mariscos</td>
                                    <td class="text-end">1</td>
                                    <td class="text-end">$3.500</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2" class="text-end">Total</th>
                                    <th class="text-end">$15.500</th>
                                </tr>
                                <tr>
                                    <th colspan="2" class="text-end">Propina sugerida (10%)</th>
                                    <th class="text-end">$1.550</th>
                                </tr>
                                <tr>
                                    <th colspan="2" class="text-end">Total con propina</th>
                                    <th class="text-end">$17.050</th>
                                </tr>
                            </tfoot>
                        </table>
                        <div class="mt-2">
                            <div class="text-success"><strong>Total pagado:</strong> $17.050</div>
                            <div class="text-success"><strong>Vuelto:</strong> $0</div>
                            <div>
                                <strong>Propina pagada:</strong>
                                <span class="text-success">Sí</span>
                                <!-- Si no pagó propina, cambia por: <span class="text-danger">No</span> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Puedes duplicar este bloque con PHP para mostrar más boletas -->
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