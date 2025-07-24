<?php
    include "php_scripts\configs_oracle\config_pdo.php";

    // Procesar formulario agregar/editar local o mesa
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Local
        $horaApertura = $_POST['horaApertura'] ?? '';
        $horaCierre = $_POST['horaCierre'] ?? '';
        $region = $_POST['region'] ?? '';
        $comuna = $_POST['comuna'] ?? '';
        $calle = $_POST['calle'] ?? '';
        $numeroCalle = $_POST['numeroCalle'] ?? '';
        $activo = ($_POST['activo'] ?? '1') == '1' ? 1 : 0;
        if (isset($_POST['editar_local']) && $_POST['localId']) {
            $localId = $_POST['localId'];
            $sql = "CALL EDITARLOCAL(:P_LOID, TO_DATE(:P_HORARIO_APERTURA, 'HH24:MI'), TO_DATE(:P_HORARIO_CIERRE, 'HH24:MI'), :P_REGION, :P_COMUNA, :P_CALLE, :P_NCALLE, :P_ACTIVO)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':P_LOID', $localId, PDO::PARAM_INT);
            $stmt->bindParam(':P_HORARIO_APERTURA', $horaApertura);
            $stmt->bindParam(':P_HORARIO_CIERRE', $horaCierre);
            $stmt->bindParam(':P_REGION', $region);
            $stmt->bindParam(':P_COMUNA', $comuna);
            $stmt->bindParam(':P_CALLE', $calle);
            $stmt->bindParam(':P_NCALLE', $numeroCalle);
            $stmt->bindParam(':P_ACTIVO', $activo, PDO::PARAM_INT);
            $stmt->execute();
            echo '<script>window.location.href = "administrar-local.php";</script>';
            exit;
        } elseif (isset($_POST['agregar_local'])) {
            $sql = "CALL AGREGARLOCAL(TO_DATE(:P_HORARIO_APERTURA, 'HH24:MI'), TO_DATE(:P_HORARIO_CIERRE, 'HH24:MI'), :P_REGION, :P_COMUNA, :P_CALLE, :P_NCALLE, :P_ACTIVO)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':P_HORARIO_APERTURA', $horaApertura);
            $stmt->bindParam(':P_HORARIO_CIERRE', $horaCierre);
            $stmt->bindParam(':P_REGION', $region);
            $stmt->bindParam(':P_COMUNA', $comuna);
            $stmt->bindParam(':P_CALLE', $calle);
            $stmt->bindParam(':P_NCALLE', $numeroCalle);
            $stmt->bindParam(':P_ACTIVO', $activo, PDO::PARAM_INT);
            $stmt->execute();
            echo '<script>window.location.href = "administrar-local.php";</script>';
            exit;
        }
        // Mesa
        if (isset($_POST['agregar_mesa']) && isset($_POST['local_mesas']) && empty($_POST['mesaId'])) {
            $localIdMesa = intval($_POST['local_mesas']);
            $numeroInterno = intval($_POST['identificadorMesa']);
            $activoMesa = isset($_POST['activoMesa']) && $_POST['activoMesa'] == '1' ? 1 : 0;
            $sql = "CALL AGREGARMESA(:P_MENUMEROINTERNO, :P_MEACTIVO, :P_LOCAL_LOID)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':P_MENUMEROINTERNO', $numeroInterno, PDO::PARAM_INT);
            $stmt->bindParam(':P_MEACTIVO', $activoMesa, PDO::PARAM_INT);
            $stmt->bindParam(':P_LOCAL_LOID', $localIdMesa, PDO::PARAM_INT);
            $stmt->execute();
            echo '<script>window.location.href = "administrar-local.php?local_mesas=' . $localIdMesa . '";</script>';
            exit;
        }
        if (isset($_POST['editar_mesa']) && isset($_POST['mesaId']) && !empty($_POST['mesaId'])) {
            $mesaId = intval($_POST['mesaId']);
            $numeroInterno = intval($_POST['identificadorMesa']);
            $activoMesa = isset($_POST['activoMesa']) && $_POST['activoMesa'] == '1' ? 1 : 0;
            $sql = "CALL EDITARMESA(:P_MEID, :P_MENUMEROINTERNO, :P_MEACTIVO)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':P_MEID', $mesaId, PDO::PARAM_INT);
            $stmt->bindParam(':P_MENUMEROINTERNO', $numeroInterno, PDO::PARAM_INT);
            $stmt->bindParam(':P_MEACTIVO', $activoMesa, PDO::PARAM_INT);
            $stmt->execute();
            $localIdMesa = isset($_POST['local_mesas']) ? intval($_POST['local_mesas']) : '';
            echo '<script>window.location.href = "administrar-local.php?local_mesas=' . $localIdMesa . '";</script>';
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
    <title>Administrar Locales - La Pica del Pescador</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
        .main-content {
            margin-top: 90px;
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
        <h1 class="mb-4">Administrar Locales</h1>
        <!-- Tabla de locales -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Locales registrados
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="locales-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Horario</th>
                                <th>Región</th>
                                <th>Comuna</th>
                                <th>Calle</th>
                                <th>N° Calle</th>
                                <th>Activo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_locales = "SELECT LOID, TO_CHAR(LOHORAAPERTURA, 'HH24:MI') AS HORAAPERTURA, TO_CHAR(LOHORACIERRE, 'HH24:MI') AS HORACIERRE, LOREGION, LOCOMUNA, LOCALLE, LONUMEROCALLE, LOACTIVO FROM LOCAL ORDER BY LOID";
                            $stmt_locales = $conn->prepare($sql_locales);
                            $stmt_locales->execute();
                            while ($local = $stmt_locales->fetch(PDO::FETCH_ASSOC)) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($local['LOID']) . '</td>';
                                echo '<td>' . htmlspecialchars($local['HORAAPERTURA']) . ' - ' . htmlspecialchars($local['HORACIERRE']) . '</td>';
                                echo '<td>' . htmlspecialchars($local['LOREGION']) . '</td>';
                                echo '<td>' . htmlspecialchars($local['LOCOMUNA']) . '</td>';
                                echo '<td>' . htmlspecialchars($local['LOCALLE']) . '</td>';
                                echo '<td>' . htmlspecialchars($local['LONUMEROCALLE']) . '</td>';
                                echo '<td>' . ($local['LOACTIVO'] == 1 ? 'Si' : 'No') . '</td>';
                                echo '<td>';
                                echo '<button class="btn btn-warning btn-sm btn-editar-local" data-id="' . htmlspecialchars($local['LOID']) . '">Editar</button> ';
                                echo '<button class="btn btn-info btn-sm btn-gestionar-mesas" data-id="' . htmlspecialchars($local['LOID']) . '">Gestionar mesas</button>';
                                echo '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Formulario para editar/agregar local -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Editar / Agregar Local
            </div>
            <div class="card-body">
                <form id="form-local" method="post">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label for="localId" class="form-label">ID</label>
                            <input type="text" class="form-control" id="localId" name="localId" readonly>
                        </div>
                        <div class="col-md-3">
                            <label for="horaApertura" class="form-label">Hora de apertura</label>
                            <input type="time" class="form-control" id="horaApertura" name="horaApertura" required>
                        </div>
                        <div class="col-md-3">
                            <label for="horaCierre" class="form-label">Hora de cierre</label>
                            <input type="time" class="form-control" id="horaCierre" name="horaCierre" required>
                        </div>
                        <div class="col-md-2">
                            <label for="region" class="form-label">Región</label>
                            <select class="form-select" id="region" name="region" required>
                                <option value="">Selecciona región</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="comuna" class="form-label">Comuna</label>
                            <select class="form-select" id="comuna" name="comuna" required>
                                <option value="">Selecciona comuna</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="calle" class="form-label">Calle</label>
                            <input type="text" class="form-control" id="calle" name="calle" required>
                        </div>
                        <div class="col-md-1">
                            <label for="numeroCalle" class="form-label">N°</label>
                            <input type="text" class="form-control" id="numeroCalle" name="numeroCalle" required>
                        </div>
                        <div class="col-md-2">
                            <label for="activo" class="form-label">¿Activo?</label>
                            <select class="form-select" id="activo" name="activo">
                                <option value="1">Si</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-success" id="btnGuardarLocal">Guardar</button>
                            <input type="hidden" name="agregar_local" id="agregar_local" value="1">
                            <input type="hidden" name="editar_local" id="editar_local" value="">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Gestión de mesas del local seleccionado -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Mesas del local seleccionado
            </div>
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between">
                    <button class="btn btn-success" id="btnAgregarMesa">Añadir mesa</button>
                    <button class="btn btn-secondary" id="btnCerrarLocal" style="display:none;">Cerrar local</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="mesas-table">
                        <thead>
                            <tr>
                                <th>ID Mesa</th>
                                <th>Identificador interno</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
        <tbody id="mesas-table-body">
            <?php
            // Mostrar las mesas del local seleccionado si existe
            if (isset($_GET['local_mesas'])) {
                $localIdMesas = intval($_GET['local_mesas']);
                $sql_mesas = "SELECT MEID, MENUMEROINTERNO, MEACTIVO FROM MESALOCAL WHERE LOCAL_LOID = :localId ORDER BY MEID";
                $stmt_mesas = $conn->prepare($sql_mesas);
                $stmt_mesas->bindParam(':localId', $localIdMesas, PDO::PARAM_INT);
                $stmt_mesas->execute();
                while ($mesa = $stmt_mesas->fetch(PDO::FETCH_ASSOC)) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($mesa['MEID']) . '</td>';
                    echo '<td>Mesa ' . htmlspecialchars($mesa['MENUMEROINTERNO']) . '</td>';
                    echo '<td>' . ($mesa['MEACTIVO'] == 1 ? 'Si' : 'No') . '</td>';
                    echo '<td>';
                    echo '<button class="btn btn-warning btn-sm btn-editar-mesa" data-id="' . htmlspecialchars($mesa['MEID']) . '">Editar</button> ';
                    echo '</td>';
                    echo '</tr>';
                }
            }
            ?>
        </tbody>
                    </table>
                </div>
                <!-- Formulario para editar/agregar mesa -->
                <form id="form-mesa" class="row g-2 mt-3" style="display:none;" method="post">
                    <input type="hidden" name="local_mesas" id="inputLocalMesas" value="<?php echo isset($_GET['local_mesas']) ? intval($_GET['local_mesas']) : ''; ?>">
                    <div class="col-md-2">
                        <label for="mesaId" class="form-label">ID Mesa</label>
                        <input type="text" class="form-control" id="mesaId" name="mesaId" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="identificadorMesa" class="form-label">Identificador interno</label>
                        <input type="number" class="form-control" id="identificadorMesa" name="identificadorMesa" placeholder="Ej: 7" required min="1" step="1">
                    </div>
                    <div class="col-md-2">
                        <label for="activoMesa" class="form-label">¿Activa?</label>
                        <select class="form-select" id="activoMesa" name="activoMesa">
                            <option value="1">Si</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                    <input type="hidden" name="agregar_mesa" id="agregar_mesa" value="">
                    <input type="hidden" name="editar_mesa" id="editar_mesa" value="">
                    <div class="col-md-4 text-end align-self-end">
                        <button type="submit" class="btn" id="btnGuardarMesa">
                            Guardar mesa
                        </button>
                        <button type="button" class="btn btn-secondary" id="btnCancelarMesa">Cancelar</button>
                    </div>
                </form>
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
        // Gestión de mesas y edición visual de locales
        var RegionesYcomunas = {
            "regiones": [{
                "NombreRegion": "Arica y Parinacota",
                "comunas": ["Arica", "Camarones", "Putre", "General Lagos"]
            },
                {
                    "NombreRegion": "Tarapacá",
                    "comunas": ["Iquique", "Alto Hospicio", "Pozo Almonte", "Camiña", "Colchane", "Huara", "Pica"]
            },
                {
                    "NombreRegion": "Antofagasta",
                    "comunas": ["Antofagasta", "Mejillones", "Sierra Gorda", "Taltal", "Calama", "Ollagüe", "San Pedro de Atacama", "Tocopilla", "María Elena"]
            },
                {
                    "NombreRegion": "Atacama",
                    "comunas": ["Copiapó", "Caldera", "Tierra Amarilla", "Chañaral", "Diego de Almagro", "Vallenar", "Alto del Carmen", "Freirina", "Huasco"]
            },
                {
                    "NombreRegion": "Coquimbo",
                    "comunas": ["La Serena", "Coquimbo", "Andacollo", "La Higuera", "Paiguano", "Vicuña", "Illapel", "Canela", "Los Vilos", "Salamanca", "Ovalle", "Combarbalá", "Monte Patria", "Punitaqui", "Río Hurtado"]
            },
                {
                    "NombreRegion": "Valparaíso",
                    "comunas": ["Valparaíso", "Casablanca", "Concón", "Juan Fernández", "Puchuncaví", "Quintero", "Viña del Mar", "Isla de Pascua", "Los Andes", "Calle Larga", "Rinconada", "San Esteban", "La Ligua", "Cabildo", "Papudo", "Petorca", "Zapallar", "Quillota", "Calera", "Hijuelas", "La Cruz", "Nogales", "San Antonio", "Algarrobo", "Cartagena", "El Quisco", "El Tabo", "Santo Domingo", "San Felipe", "Catemu", "Llaillay", "Panquehue", "Putaendo", "Santa María", "Quilpué", "Limache", "Olmué", "Villa Alemana"]
            },
                {
                    "NombreRegion": "Región del Libertador Gral. Bernardo O’Higgins",
                    "comunas": ["Rancagua", "Codegua", "Coinco", "Coltauco", "Doñihue", "Graneros", "Las Cabras", "Machalí", "Malloa", "Mostazal", "Olivar", "Peumo", "Pichidegua", "Quinta de Tilcoco", "Rengo", "Requínoa", "San Vicente", "Pichilemu", "La Estrella", "Litueche", "Marchihue", "Navidad", "Paredones", "San Fernando", "Chépica", "Chimbarongo", "Lolol", "Nancagua", "Palmilla", "Peralillo", "Placilla", "Pumanque", "Santa Cruz"]
            },
                {
                    "NombreRegion": "Región del Maule",
                    "comunas": ["Talca", "ConsVtución", "Curepto", "Empedrado", "Maule", "Pelarco", "Pencahue", "Río Claro", "San Clemente", "San Rafael", "Cauquenes", "Chanco", "Pelluhue", "Curicó", "Hualañé", "Licantén", "Molina", "Rauco", "Romeral", "Sagrada Familia", "Teno", "Vichuquén", "Linares", "Colbún", "Longaví", "Parral", "ReVro", "San Javier", "Villa Alegre", "Yerbas Buenas"]
            },
                {
                    "NombreRegion": "Región del Biobío",
                    "comunas": ["Concepción", "Coronel", "Chiguayante", "Florida", "Hualqui", "Lota", "Penco", "San Pedro de la Paz", "Santa Juana", "Talcahuano", "Tomé", "Hualpén", "Lebu", "Arauco", "Cañete", "Contulmo", "Curanilahue", "Los Álamos", "Tirúa", "Los Ángeles", "Antuco", "Cabrero", "Laja", "Mulchén", "Nacimiento", "Negrete", "Quilaco", "Quilleco", "San Rosendo", "Santa Bárbara", "Tucapel", "Yumbel", "Alto Biobío", "Chillán", "Bulnes", "Cobquecura", "Coelemu", "Coihueco", "Chillán Viejo", "El Carmen", "Ninhue", "Ñiquén", "Pemuco", "Pinto", "Portezuelo", "Quillón", "Quirihue", "Ránquil", "San Carlos", "San Fabián", "San Ignacio", "San Nicolás", "Treguaco", "Yungay"]
            },
                {
                    "NombreRegion": "Región de la Araucanía",
                    "comunas": ["Temuco", "Carahue", "Cunco", "Curarrehue", "Freire", "Galvarino", "Gorbea", "Lautaro", "Loncoche", "Melipeuco", "Nueva Imperial", "Padre las Casas", "Perquenco", "Pitrufquén", "Pucón", "Saavedra", "Teodoro Schmidt", "Toltén", "Vilcún", "Villarrica", "Cholchol", "Angol", "Collipulli", "Curacautín", "Ercilla", "Lonquimay", "Los Sauces", "Lumaco", "Purén", "Renaico", "Traiguén", "Victoria", ]
            },
                {
                    "NombreRegion": "Región de Los Ríos",
                    "comunas": ["Valdivia", "Corral", "Lanco", "Los Lagos", "Máfil", "Mariquina", "Paillaco", "Panguipulli", "La Unión", "Futrono", "Lago Ranco", "Río Bueno"]
            },
                {
                    "NombreRegion": "Región de Los Lagos",
                    "comunas": ["Puerto Montt", "Calbuco", "Cochamó", "Fresia", "FruVllar", "Los Muermos", "Llanquihue", "Maullín", "Puerto Varas", "Castro", "Ancud", "Chonchi", "Curaco de Vélez", "Dalcahue", "Puqueldón", "Queilén", "Quellón", "Quemchi", "Quinchao", "Osorno", "Puerto Octay", "Purranque", "Puyehue", "Río Negro", "San Juan de la Costa", "San Pablo", "Chaitén", "Futaleufú", "Hualaihué", "Palena"]
            },
                {
                    "NombreRegion": "Región Aisén del Gral. Carlos Ibáñez del Campo",
                    "comunas": ["Coihaique", "Lago Verde", "Aisén", "Cisnes", "Guaitecas", "Cochrane", "O’Higgins", "Tortel", "Chile Chico", "Río Ibáñez"]
            },
                {
                    "NombreRegion": "Región de Magallanes y de la Antártica Chilena",
                    "comunas": ["Punta Arenas", "Laguna Blanca", "Río Verde", "San Gregorio", "Cabo de Hornos (Ex Navarino)", "Antártica", "Porvenir", "Primavera", "Timaukel", "Natales", "Torres del Paine"]
            },
                {
                    "NombreRegion": "Región Metropolitana de Santiago",
                    "comunas": ["Cerrillos", "Cerro Navia", "Conchalí", "El Bosque", "Estación Central", "Huechuraba", "Independencia", "La Cisterna", "La Florida", "La Granja", "La Pintana", "La Reina", "Las Condes", "Lo Barnechea", "Lo Espejo", "Lo Prado", "Macul", "Maipú", "Ñuñoa", "Pedro Aguirre Cerda", "Peñalolén", "Providencia", "Pudahuel", "Quilicura", "Quinta Normal", "Recoleta", "Renca", "San Joaquín", "San Miguel", "San Ramón", "Vitacura", "Puente Alto", "Pirque", "San José de Maipo", "Colina", "Lampa", "TilVl", "San Bernardo", "Buin", "Calera de Tango", "Paine", "Melipilla", "Alhué", "Curacaví", "María Pinto", "San Pedro", "Talagante", "El Monte", "Isla de Maipo", "Padre Hurtado", "Peñaflor"]
            }]
        };

        let localSeleccionado = null;
        document.addEventListener('DOMContentLoaded', function() {
            // Cascada región/comuna
            const regionSelect = document.getElementById('region');
            const comunaSelect = document.getElementById('comuna');
            function poblarRegiones() {
                regionSelect.innerHTML = '<option value="">Selecciona región</option>';
                RegionesYcomunas.regiones.forEach(function(region) {
                    const option = document.createElement('option');
                    option.value = region.NombreRegion;
                    option.textContent = region.NombreRegion;
                    regionSelect.appendChild(option);
                });
            }
            regionSelect.addEventListener('change', function() {
                comunaSelect.innerHTML = '<option value="">Selecciona comuna</option>';
                const region = RegionesYcomunas.regiones.find(r => r.NombreRegion === regionSelect.value);
                if (region) {
                    region.comunas.forEach(function(comuna) {
                        const option = document.createElement('option');
                        option.value = comuna;
                        option.textContent = comuna;
                        comunaSelect.appendChild(option);
                    });
                }
            });
            poblarRegiones();

            // Editar local: carga datos en el formulario y cambia el modo a edición
            document.querySelector('#locales-table').addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-editar-local')) {
                    const fila = e.target.closest('tr');
                    document.getElementById('localId').value = fila.children[0].textContent.trim();
                    const horario = fila.children[1].textContent.trim().split(' - ');
                    document.getElementById('horaApertura').value = horario[0];
                    document.getElementById('horaCierre').value = horario[1];
                    document.getElementById('region').value = fila.children[2].textContent.trim();
                    regionSelect.dispatchEvent(new Event('change'));
                    document.getElementById('comuna').value = fila.children[3].textContent.trim();
                    document.getElementById('calle').value = fila.children[4].textContent.trim();
                    document.getElementById('numeroCalle').value = fila.children[5].textContent.trim();
                    document.getElementById('activo').value = fila.children[6].textContent.trim() === 'Si' ? '1' : '0';
                    document.getElementById('agregar_local').value = '';
                    document.getElementById('editar_local').value = '1';
                    document.getElementById('form-local').scrollIntoView({behavior: 'smooth'});
                }
                // Gestionar mesas: recarga la tabla de mesas del local seleccionado
                if (e.target.classList.contains('btn-gestionar-mesas')) {
                    localSeleccionado = parseInt(e.target.getAttribute('data-id'));
                    window.location.href = 'administrar-local.php?local_mesas=' + localSeleccionado;
                }
            });
            // Al enviar el formulario de local, si hay localId, es edición; si no, es agregar
            document.getElementById('form-local').addEventListener('submit', function(e) {
                if (document.getElementById('localId').value) {
                    document.getElementById('agregar_local').value = '';
                    document.getElementById('editar_local').value = '1';
                } else {
                    document.getElementById('agregar_local').value = '1';
                    document.getElementById('editar_local').value = '';
                }
            });
            // Mostrar formulario para agregar mesa
            const btnAgregarMesa = document.getElementById('btnAgregarMesa');
            const formMesa = document.getElementById('form-mesa');
            const btnCancelarMesa = document.getElementById('btnCancelarMesa');
            const btnGuardarMesa = document.getElementById('btnGuardarMesa');
            if (btnAgregarMesa) {
                btnAgregarMesa.addEventListener('click', function() {
                    document.getElementById('mesaId').value = '';
                    document.getElementById('identificadorMesa').value = '';
                    document.getElementById('activoMesa').value = '1';
                    document.getElementById('agregar_mesa').value = '1';
                    document.getElementById('editar_mesa').value = '';
                    btnGuardarMesa.classList.remove('btn-warning');
                    btnGuardarMesa.classList.add('btn-success');
                    btnGuardarMesa.textContent = 'Guardar mesa';
                    formMesa.style.display = 'flex';
                });
            }
            if (btnCancelarMesa) {
                btnCancelarMesa.addEventListener('click', function() {
                    formMesa.style.display = 'none';
                });
            }
            // Editar mesa: carga datos en el formulario y cambia el modo a edición
            document.getElementById('mesas-table-body').addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-editar-mesa')) {
                    const fila = e.target.closest('tr');
                    document.getElementById('mesaId').value = fila.children[0].textContent.trim();
                    document.getElementById('identificadorMesa').value = fila.children[1].textContent.trim().replace('Mesa ', '');
                    document.getElementById('activoMesa').value = fila.children[2].textContent.trim() === 'Si' ? '1' : '0';
                    document.getElementById('agregar_mesa').value = '';
                    document.getElementById('editar_mesa').value = '1';
                    btnGuardarMesa.classList.remove('btn-success');
                    btnGuardarMesa.classList.add('btn-warning');
                    btnGuardarMesa.textContent = 'Guardar cambios';
                    formMesa.style.display = 'flex';
                }
            });
            // Al enviar el formulario de mesa, determina si es agregar o editar
            formMesa.addEventListener('submit', function(e) {
                // Si hay mesaId, es edición; si no, es agregar
                if (document.getElementById('mesaId').value) {
                    document.getElementById('agregar_mesa').value = '';
                    document.getElementById('editar_mesa').value = '1';
                } else {
                    document.getElementById('editar_mesa').value = '';
                    document.getElementById('agregar_mesa').value = '1';
                }
            });
        });
        </script>
</body>
</html>