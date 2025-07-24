<?php
    include "php_scripts\configs_oracle\config_pdo.php";

    // Procesar formulario agregar/editar empresa
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $emid = $_POST['emid'] ?? '';
        $emrut = $_POST['rutEmpresa'] ?? '';
        $emnombre = $_POST['nombreEmpresa'] ?? '';
        $emcorreo = $_POST['correoEmpresa'] ?? '';
        $emtelefono = $_POST['telefonoEmpresa'] ?? '';
        $emregion = $_POST['regionSucursal'] ?? '';
        $emcomuna = $_POST['comunaSucursal'] ?? '';
        $emcalle = $_POST['calleSucursal'] ?? '';
        $emnumerocalle = $_POST['numeroSucursal'] ?? '';
        if (isset($_POST['editar_empresa']) && $emid) {
            $sql = "CALL EDITAREMPRESA(:P_EMID, :P_EMRUT, :P_EMNOMBRE, :P_EMCORREO, :P_EMTELEFONO, :P_EMREGION, :P_EMCOMUNA, :P_EMCALLE, :P_EMNUMEROCALLE)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':P_EMID', $emid, PDO::PARAM_INT);
            $stmt->bindParam(':P_EMRUT', $emrut);
            $stmt->bindParam(':P_EMNOMBRE', $emnombre);
            $stmt->bindParam(':P_EMCORREO', $emcorreo);
            $stmt->bindParam(':P_EMTELEFONO', $emtelefono, PDO::PARAM_INT);
            $stmt->bindParam(':P_EMREGION', $emregion);
            $stmt->bindParam(':P_EMCOMUNA', $emcomuna);
            $stmt->bindParam(':P_EMCALLE', $emcalle);
            $stmt->bindParam(':P_EMNUMEROCALLE', $emnumerocalle);
            $stmt->execute();
        } elseif (isset($_POST['agregar_empresa'])) {
            $sql = "CALL CREAREMPRESA(:P_EMRUT, :P_EMNOMBRE, :P_EMCORREO, :P_EMTELEFONO, :P_EMREGION, :P_EMCOMUNA, :P_EMCALLE, :P_EMNUMEROCALLE)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':P_EMRUT', $emrut);
            $stmt->bindParam(':P_EMNOMBRE', $emnombre);
            $stmt->bindParam(':P_EMCORREO', $emcorreo);
            $stmt->bindParam(':P_EMTELEFONO', $emtelefono, PDO::PARAM_INT);
            $stmt->bindParam(':P_EMREGION', $emregion);
            $stmt->bindParam(':P_EMCOMUNA', $emcomuna);
            $stmt->bindParam(':P_EMCALLE', $emcalle);
            $stmt->bindParam(':P_EMNUMEROCALLE', $emnumerocalle);
            $stmt->execute();
        }
        echo '<script>window.location.href = "administrar-empresas.php";</script>';
        exit;
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
    <title>Administrar Empresas - La Pica del Pescador</title>
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
        <h1 class="mb-4">Administrar Empresas</h1>
        <!-- Formulario para agregar/editar empresa -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Agregar/Editar empresa
            </div>
            <div class="card-body">
                <form id="form-empresa" method="post">
                    <input type="hidden" name="emid" id="emid">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="nombreEmpresa" class="form-label">Nombre empresa</label>
                            <input type="text" class="form-control" id="nombreEmpresa" name="nombreEmpresa" required>
                        </div>
                        <div class="col-md-3">
                            <label for="rutEmpresa" class="form-label">RUT</label>
                            <input type="text" class="form-control" id="rutEmpresa" name="rutEmpresa" placeholder="Ej: 76.123.456-7" required>
                        </div>
                        <div class="col-md-3">
                            <label for="correoEmpresa" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="correoEmpresa" name="correoEmpresa" required>
                        </div>
                        <div class="col-md-2">
                            <label for="telefonoEmpresa" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefonoEmpresa" name="telefonoEmpresa" required>
                        </div>
                        <div class="col-md-2">
                            <label for="numeroSucursal" class="form-label">N° Sucursal</label>
                            <input type="text" class="form-control" id="numeroSucursal" name="numeroSucursal" required>
                        </div>
                        <div class="col-md-3">
                            <label for="calleSucursal" class="form-label">Calle sucursal</label>
                            <input type="text" class="form-control" id="calleSucursal" name="calleSucursal" required>
                        </div>
                        <div class="col-md-2">
                            <label for="regionSucursal" class="form-label">Región</label>
                            <select class="form-select" id="regionSucursal" name="regionSucursal" required>
                                <option value="">Selecciona región</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="comunaSucursal" class="form-label">Comuna</label>
                            <select class="form-select" id="comunaSucursal" name="comunaSucursal" required>
                                <option value="">Selecciona comuna</option>
                            </select>
                        </div>
                        <input type="hidden" name="agregar_empresa" id="agregar_empresa" value="1">
                        <input type="hidden" name="editar_empresa" id="editar_empresa" value="">
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-success" id="btnGuardarEmpresa">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Tabla de empresas existentes -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                Empresas registradas
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>RUT</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>N° Sucursal</th>
                                <th>Calle</th>
                                <th>Comuna</th>
                                <th>Región</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_empresas = "SELECT EMID, EMRUT, EMNOMBRE, EMCORREO, EMTELEFONO, EMREGION, EMCOMUNA, EMCALLE, EMNUMEROCALLE FROM EMPRESA ORDER BY EMID";
                            $stmt_empresas = $conn->prepare($sql_empresas);
                            $stmt_empresas->execute();
                            while ($empresa = $stmt_empresas->fetch(PDO::FETCH_ASSOC)) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($empresa['EMNOMBRE']) . '</td>';
                                echo '<td>' . htmlspecialchars($empresa['EMRUT']) . '</td>';
                                echo '<td>' . htmlspecialchars($empresa['EMCORREO']) . '</td>';
                                echo '<td>' . htmlspecialchars($empresa['EMTELEFONO']) . '</td>';
                                echo '<td>' . htmlspecialchars($empresa['EMNUMEROCALLE']) . '</td>';
                                echo '<td>' . htmlspecialchars($empresa['EMCALLE']) . '</td>';
                                echo '<td>' . htmlspecialchars($empresa['EMCOMUNA']) . '</td>';
                                echo '<td>' . htmlspecialchars($empresa['EMREGION']) . '</td>';
                                echo '<td>';
                                echo '<div class="d-flex gap-2">';
                                echo '<button class="btn btn-warning btn-sm btn-editar-empresa" data-id="' . htmlspecialchars($empresa['EMID']) . '" data-nombre="' . htmlspecialchars($empresa['EMNOMBRE']) . '" data-rut="' . htmlspecialchars($empresa['EMRUT']) . '" data-correo="' . htmlspecialchars($empresa['EMCORREO']) . '" data-telefono="' . htmlspecialchars($empresa['EMTELEFONO']) . '" data-region="' . htmlspecialchars($empresa['EMREGION']) . '" data-comuna="' . htmlspecialchars($empresa['EMCOMUNA']) . '" data-calle="' . htmlspecialchars($empresa['EMCALLE']) . '" data-numero="' . htmlspecialchars($empresa['EMNUMEROCALLE']) . '">Editar</button>';
                                echo '</div>';
                                echo '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
    <script>
    // ...existing code...
    // Lógica para edición de empresa
    document.addEventListener('DOMContentLoaded', function() {
        const formEmpresa = document.getElementById('form-empresa');
        const btnGuardarEmpresa = document.getElementById('btnGuardarEmpresa');
        document.querySelectorAll('.btn-editar-empresa').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.getElementById('emid').value = btn.getAttribute('data-id');
                document.getElementById('nombreEmpresa').value = btn.getAttribute('data-nombre');
                document.getElementById('rutEmpresa').value = btn.getAttribute('data-rut');
                document.getElementById('correoEmpresa').value = btn.getAttribute('data-correo');
                document.getElementById('telefonoEmpresa').value = btn.getAttribute('data-telefono');
                document.getElementById('regionSucursal').value = btn.getAttribute('data-region');
                // Disparar el evento para poblar comunas
                regionSelect.dispatchEvent(new Event('change'));
                document.getElementById('comunaSucursal').value = btn.getAttribute('data-comuna');
                document.getElementById('calleSucursal').value = btn.getAttribute('data-calle');
                document.getElementById('numeroSucursal').value = btn.getAttribute('data-numero');
                document.getElementById('agregar_empresa').value = '';
                document.getElementById('editar_empresa').value = '1';
                btnGuardarEmpresa.textContent = 'Guardar cambios';
                btnGuardarEmpresa.classList.remove('btn-success');
                btnGuardarEmpresa.classList.add('btn-warning');
                formEmpresa.scrollIntoView({behavior: 'smooth'});
            });
        });
        // Al enviar el formulario, si hay emid, es edición; si no, es agregar
        formEmpresa.addEventListener('submit', function(e) {
            if (document.getElementById('emid').value) {
                document.getElementById('agregar_empresa').value = '';
                document.getElementById('editar_empresa').value = '1';
                btnGuardarEmpresa.textContent = 'Guardar cambios';
                btnGuardarEmpresa.classList.remove('btn-success');
                btnGuardarEmpresa.classList.add('btn-warning');
            } else {
                document.getElementById('agregar_empresa').value = '1';
                document.getElementById('editar_empresa').value = '';
                btnGuardarEmpresa.textContent = 'Guardar';
                btnGuardarEmpresa.classList.remove('btn-warning');
                btnGuardarEmpresa.classList.add('btn-success');
            }
        });
    });
    </script>
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
    // Usa el objeto RegionesYcomunas que pegaste
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

        const regionSelect = document.getElementById('regionSucursal');
        const comunaSelect = document.getElementById('comunaSucursal');

        // Llenar regiones
        function poblarRegiones() {
            regionSelect.innerHTML = '<option value="">Selecciona región</option>';
            RegionesYcomunas.regiones.forEach(function(region, idx) {
                const option = document.createElement('option');
                option.value = region.NombreRegion;
                option.textContent = region.NombreRegion;
                regionSelect.appendChild(option);
            });
        }

        // Llenar comunas según región
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

        // Inicializar regiones al cargar
        poblarRegiones();
    </script>
</body>
</html>
