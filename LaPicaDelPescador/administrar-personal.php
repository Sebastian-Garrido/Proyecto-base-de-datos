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
    <title>Administrar Personal - La Pica del Pescador</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/navbar.css">
    <style>
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
                        <a class="nav-link d-flex flex-column text-center active" href="administrar-personal.php">
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
        <h1 class="mb-4">Administrar Personal</h1>
        <!-- Formulario para agregar personal -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Agregar personal
            </div>
            <div class="card-body">
                <form>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label for="run" class="form-label">RUN*</label>
                            <input type="number" class="form-control" id="run" required>
                        </div>
                        <div class="col-md-2">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="number" class="form-control" id="telefono">
                        </div>
                        <div class="col-md-3">
                            <label for="correo" class="form-label">Correo electrónico</label>
                            <input type="text" class="form-control" id="correo">
                        </div>
                        <div class="col-md-2">
                            <label for="cargo" class="form-label">Cargo*</label>
                            <select class="form-select" id="cargo" required>
                              <option value="">Selecciona cargo</option>
                              <option value="Garzón">Garzón</option>
                              <option value="Cajero">Cajero</option>
                              <option value="Jefe de cocina">Jefe de cocina</option>
                              <option value="Cocinero">Cocinero</option>
                              <option value="Copero">Copero</option>
                              <option value="Administrador">Administrador</option>
                              <option value="Bodeguero">Bodeguero</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="text" class="form-control" id="password">
                        </div>
                        <div class="col-md-2">
                            <label for="fechaNacimiento" class="form-label">Fecha de nacimiento*</label>
                            <input type="date" class="form-control" id="fechaNacimiento" required>
                        </div>
                        <div class="col-md-2">
                            <label for="fechaIngreso" class="form-label">Fecha de ingreso*</label>
                            <input type="date" class="form-control" id="fechaIngreso" required>
                        </div>
                        <div class="col-md-2">
                            <label for="fechaEgreso" class="form-label">Fecha de egreso</label>
                            <input type="date" class="form-control" id="fechaEgreso">
                        </div>
                        <div class="col-md-2">
                            <label for="sueldoHora" class="form-label">Sueldo por hora*</label>
                            <input type="number" class="form-control" id="sueldoHora" required>
                        </div>
                        <div class="col-md-2">
                            <label for="nombres" class="form-label">Nombres*</label>
                            <input type="text" class="form-control" id="nombres" required>
                        </div>
                        <div class="col-md-2">
                            <label for="apellidoPaterno" class="form-label">Apellido paterno*</label>
                            <input type="text" class="form-control" id="apellidoPaterno" required>
                        </div>
                        <div class="col-md-2">
                            <label for="apellidoMaterno" class="form-label">Apellido materno*</label>
                            <input type="text" class="form-control" id="apellidoMaterno" required>
                        </div>
                        <div class="col-md-2">
                            <label for="vigencia" class="form-label">Vigente*</label>
                            <select class="form-select" id="vigencia" required>
                                <option value="si">Sí</option>
                                <option value="no">No</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="regionCascada" class="form-label">Región*</label>
                            <select class="form-select" id="regionCascada" required>
                                <option value="">Selecciona región</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="comunaCascada" class="form-label">Comuna*</label>
                            <select class="form-select" id="comunaCascada" required>
                                <option value="">Selecciona comuna</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="localCascada" class="form-label">Local*</label>
                            <select class="form-select" id="localCascada" required>
                                <option value="">Selecciona local</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="calle" class="form-label">Calle*</label>
                            <input type="text" class="form-control" id="calle" required>
                        </div>
                        <div class="col-md-2">
                            <label for="numeroCalle" class="form-label">Número Calle*</label>
                            <input type="text" class="form-control" id="numeroCalle" required>
                        </div>
                        <div class="col-md-4">
                            <label for="direccionAdicional" class="form-label">Dirección adicional</label>
                            <input type="text" class="form-control" id="direccionAdicional">
                        </div>
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-success">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Tabla de personal existente -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Personal registrado
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>RUN</th>
                                <th>Nombres</th>
                                <th>Apellido paterno</th>
                                <th>Apellido materno</th>
                                <th>Teléfono</th>
                                <th>Correo electrónico</th>
                                <th>Cargo</th>
                                <th>Fecha de nacimiento</th>
                                <th>Fechas</th>
                                <th>Sueldo por hora</th>
                                <th>Vigente</th>
                                <th>Región</th>
                                <th>Comuna</th>
                                <th>Calle</th>
                                <th>Número Calle</th>
                                <th>Local</th>
                                <th>Dirección adicional</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>12345678</td>
                                <td>Juan</td>
                                <td>Pérez</td>
                                <td>González</td>
                                <td>912345678</td>
                                <td>juan.perez@email.com</td>
                                <td>Garzón</td>
                                <td>1990-01-01</td>
                                <td>
                                    <button class="btn btn-info btn-sm mostrarFechas" data-id="1">Mostrar fechas</button>
                                </td>
                                <td>$5000</td>
                                <td>Sí</td>
                                <td>Tarapacá</td>
                                <td>Iquique</td>
                                <td>Calle 123</td>
                                <td>456</td>
                                <td>Local 1</td>
                                <td>Depto 2, Edificio Central</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-warning btn-sm editarPersonal" data-id="1">Editar</button>
                                        <button class="btn btn-outline-primary btn-sm cambiarPassword" data-id="1">Cambiar contraseña</button>
                                    </div>
                                </td>
                            </tr>
                            <!-- Más filas según personal registrado -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Tabla de fechas de ingreso/egreso dinámica -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                Fechas de ingreso y egreso
            </div>
            <div class="card-body">
                <div class="mb-3 d-flex justify-content-between">
                    <button class="btn btn-success" id="btnAgregarIngreso" disabled>Añadir ingreso</button>
                    <button class="btn btn-primary" id="btnAgregarEgreso" disabled>Añadir egreso</button>
                    <button class="btn btn-secondary" id="btnCerrarFechas" disabled>Cerrar fechas</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle w-100" id="fechas-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha de ingreso</th>
                                <th>Fecha de egreso</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- JS rellena aquí las fechas -->
                        </tbody>
                    </table>
                </div>
                <form id="form-fecha" class="row g-2 mt-3" style="display:none;">
                <div class="col-md-6">
                    <input type="date" class="form-control" id="inputFecha" required>
                </div>
                <div class="col-md-6 text-end">
                    <button type="submit" class="btn btn-success" id="btnGuardarFecha">Guardar</button>
                    <button type="button" class="btn btn-secondary" id="btnCancelarFecha">Cancelar</button>
                </div>
            </form>
            <div class="text-center text-muted mt-3" id="fechas-personal-placeholder">Seleccione un usuario para ver sus fechas de ingreso y egreso.</div>
            </div>
        </div>
    </div>
    
    <!-- Onda decorativa inferior -->

    <!-- Modal para editar personal (fuera de main-content) -->
    <div class="modal fade" id="modalEditarPersonal" tabindex="-1" aria-labelledby="modalEditarPersonalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalEditarPersonalLabel">Editar datos de personal</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <form id="formEditarPersonal">
            <div class="modal-body">
              <div class="row g-3">
                <div class="col-md-2">
                  <label class="form-label">ID</label>
                  <input type="text" class="form-control" id="editId" disabled>
                </div>
                <div class="col-md-2">
                  <label class="form-label">RUN*</label>
                  <input type="number" class="form-control" id="editRun" required>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Teléfono</label>
                  <input type="number" class="form-control" id="editTelefono">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Correo electrónico</label>
                  <input type="text" class="form-control" id="editCorreo">
                </div>
                <div class="col-md-2">
                  <label class="form-label">Cargo*</label>
                  <input type="text" class="form-control" id="editCargo" required>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Fecha de nacimiento*</label>
                  <input type="date" class="form-control" id="editFechaNacimiento" required>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Sueldo por hora*</label>
                  <input type="number" class="form-control" id="editSueldoHora" required>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Nombres*</label>
                  <input type="text" class="form-control" id="editNombres" required>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Apellido paterno*</label>
                  <input type="text" class="form-control" id="editApellidoPaterno" required>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Apellido materno*</label>
                  <input type="text" class="form-control" id="editApellidoMaterno" required>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Región*</label>
                  <select class="form-select" id="editRegion" required>
                    <option value="">Selecciona región</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Comuna*</label>
                  <select class="form-select" id="editComuna" required>
                    <option value="">Selecciona comuna</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Local*</label>
                  <select class="form-select" id="editLocal" required>
                    <option value="">Selecciona local</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Calle*</label>
                  <input type="text" class="form-control" id="editCalle" required>
                </div>
                <div class="col-md-2">
                  <label class="form-label">Número Calle*</label>
                  <input type="text" class="form-control" id="editNumeroCalle" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Dirección adicional</label>
                  <input type="text" class="form-control" id="editDireccionAdicional">
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal para cambiar contraseña -->
    <div class="modal fade" id="modalCambiarPassword" tabindex="-1" aria-labelledby="modalCambiarPasswordLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalCambiarPasswordLabel">Cambiar contraseña</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <form id="formCambiarPassword">
            <div class="modal-body">
              <div class="mb-3">
                <label for="nuevaPassword" class="form-label">Nueva contraseña</label>
                <div class="input-group">
                  <input type="password" class="form-control" id="nuevaPassword" required>
                  <button class="btn btn-outline-secondary" type="button" id="togglePassword" tabindex="-1">
                    <i class="bi bi-eye" id="iconPasswordEye"></i>
                  </button>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Guardar</button>
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
    // --- Modal editar personal ---
    document.querySelectorAll('.editarPersonal').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            // Simulación de datos
            const regionNombre = 'Tarapacá';
            const comunaNombre = 'Iquique';
            const localNombre = 'Local 1';

            document.getElementById('editId').value = id;
            document.getElementById('editRun').value = '12345678';
            document.getElementById('editTelefono').value = '912345678';
            document.getElementById('editCorreo').value = 'juan.perez@email.com';
            document.getElementById('editCargo').value = 'Garzón';
            document.getElementById('editFechaNacimiento').value = '1990-01-01';
            document.getElementById('editSueldoHora').value = '5000';
            document.getElementById('editNombres').value = 'Juan';
            document.getElementById('editApellidoPaterno').value = 'Pérez';
            document.getElementById('editApellidoMaterno').value = 'González';

            // Cargar regiones y seleccionar la correcta
            cargarRegionesEdicion();
            const regionIdx = RegionesYcomunas.regiones.findIndex(r => r.NombreRegion === regionNombre);
            document.getElementById('editRegion').value = regionIdx;

            // Cargar comunas y seleccionar la correcta
            editComunaSelect.innerHTML = '<option value="">Selecciona comuna</option>';
            if (regionIdx !== -1) {
            RegionesYcomunas.regiones[regionIdx].comunas.forEach(function(comuna) {
                const option = document.createElement('option');
                option.value = comuna;
                option.textContent = comuna;
                editComunaSelect.appendChild(option);
            });
            document.getElementById('editComuna').value = comunaNombre;
            }

            // Cargar locales y seleccionar el correcto
            const editLocalSelect = document.getElementById('editLocal');
            editLocalSelect.innerHTML = '<option value="">Selecciona local</option>';
            locales.forEach(function(local) {
            const option = document.createElement('option');
            option.value = local;
            option.textContent = local;
            editLocalSelect.appendChild(option);
            });
            editLocalSelect.value = localNombre;

            document.getElementById('editCalle').value = 'Calle 123';
            document.getElementById('editNumeroCalle').value = '456';
            document.getElementById('editDireccionAdicional').value = 'Depto 2, Edificio Central';

            var modal = new bootstrap.Modal(document.getElementById('modalEditarPersonal'));
            modal.show();
        });
        });

    document.getElementById('formEditarPersonal').onsubmit = function(e) {
      e.preventDefault();
      // Aquí iría la lógica para guardar los cambios
      var modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarPersonal'));
      modal.hide();
      alert('Cambios guardados (simulado)');
    };

    // --- Modal cambiar contraseña ---
    document.querySelectorAll('.cambiarPassword').forEach(btn => {
      btn.addEventListener('click', function() {
        // Simulación: solo para el usuario 1
        document.getElementById('nuevaPassword').value = '';
        var modal = new bootstrap.Modal(document.getElementById('modalCambiarPassword'));
        modal.show();
      });
    });
    document.getElementById('formCambiarPassword').onsubmit = function(e) {
      e.preventDefault();
      // Aquí iría la lógica para guardar la nueva contraseña
      var modal = bootstrap.Modal.getInstance(document.getElementById('modalCambiarPassword'));
      modal.hide();
      alert('Contraseña cambiada (simulado)');
    };

    // Mostrar/ocultar contraseña en modal
    document.getElementById('togglePassword').addEventListener('click', function() {
      const input = document.getElementById('nuevaPassword');
      const icon = document.getElementById('iconPasswordEye');
      if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
      } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
      }
    });
    </script>
    
    <script>
    // --- Gestión de fechas de ingreso/egreso estilo tabla y render tipo mesas ---
    const fechasPorPersonal = {
        1: [
            { ingreso: '2020-05-01', egreso: '2021-01-01' },
            { ingreso: '2022-03-15', egreso: null }
        ]
    };
    let personalSeleccionado = null;
    let tipoFecha = null; // 'ingreso' o 'egreso'

    function renderFechas() {
        const tbody = document.querySelector('#fechas-table tbody');
        tbody.innerHTML = '';
        const placeholder = document.getElementById('fechas-personal-placeholder');
        const btnIngreso = document.getElementById('btnAgregarIngreso');
        const btnEgreso = document.getElementById('btnAgregarEgreso');
        const btnCerrar = document.getElementById('btnCerrarFechas');

        if (!personalSeleccionado || !fechasPorPersonal[personalSeleccionado]) {
            placeholder.style.display = '';
            btnIngreso.disabled = true;
            btnEgreso.disabled = true;
            btnCerrar.disabled = true;
            tbody.innerHTML = '';
            return;
        }
        placeholder.style.display = 'none';
        btnCerrar.disabled = false;
        const fechas = fechasPorPersonal[personalSeleccionado];
        fechas.forEach((f, idx) => {
            tbody.innerHTML += `
                <tr>
                    <td>${idx+1}</td>
                    <td>${f.ingreso || ''}</td>
                    <td>${f.egreso || ''}</td>
                </tr>
            `;
        });
        // Botones habilitados según estado
        const puedeAgregarIngreso = fechas.length === 0 || fechas[fechas.length-1].egreso;
        btnIngreso.disabled = !puedeAgregarIngreso;
        const idxSinEgreso = fechas.findIndex(f => !f.egreso);
        btnEgreso.disabled = idxSinEgreso === -1;

        btnIngreso.onclick = function() {
            tipoFecha = 'ingreso';
            document.getElementById('form-fecha').style.display = '';
            document.getElementById('inputFecha').value = '';
            document.getElementById('btnGuardarFecha').className = 'btn btn-success';
        };
        btnEgreso.onclick = function() {
            tipoFecha = 'egreso';
            document.getElementById('form-fecha').style.display = '';
            document.getElementById('inputFecha').value = '';
            document.getElementById('btnGuardarFecha').className = 'btn btn-primary';
        };
        btnCerrar.onclick = function() {
            personalSeleccionado = null;
            tipoFecha = null;
            document.getElementById('form-fecha').style.display = 'none';
            renderFechas();
        };
        document.getElementById('btnCancelarFecha').onclick = function() {
            tipoFecha = null;
            document.getElementById('form-fecha').style.display = 'none';
        };
        document.getElementById('form-fecha').onsubmit = function(e) {
            e.preventDefault();
            const fecha = document.getElementById('inputFecha').value;
            if (!fecha || !personalSeleccionado) return;
            if (tipoFecha === 'ingreso') {
                if (!fechasPorPersonal[personalSeleccionado]) fechasPorPersonal[personalSeleccionado] = [];
                fechasPorPersonal[personalSeleccionado].push({ ingreso: fecha, egreso: null });
            } else if (tipoFecha === 'egreso') {
                const fechas = fechasPorPersonal[personalSeleccionado];
                const idx = fechas.findIndex(f => !f.egreso);
                if (idx !== -1) fechas[idx].egreso = fecha;
            }
            tipoFecha = null;
            document.getElementById('form-fecha').style.display = 'none';
            renderFechas();
        };
    }

    // Listener para mostrar fechas
    function setMostrarFechasListeners() {
        document.querySelectorAll('.mostrarFechas').forEach(btn => {
            btn.onclick = function() {
                personalSeleccionado = this.getAttribute('data-id');
                tipoFecha = null;
                document.getElementById('form-fecha').style.display = 'none';
                renderFechas();
            };
        });
    }

    // Inicializa listeners y tabla vacía
    setMostrarFechasListeners();
    renderFechas();
    </script>

    <script>
    // Datos de Tarapacá: comunas, ciudades y locales
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
                    "NombreRegion": "Región de Magallanes y de la AntárVca Chilena",
                    "comunas": ["Punta Arenas", "Laguna Blanca", "Río Verde", "San Gregorio", "Cabo de Hornos (Ex Navarino)", "AntárVca", "Porvenir", "Primavera", "Timaukel", "Natales", "Torres del Paine"]
            },
                {
                    "NombreRegion": "Región Metropolitana de Santiago",
                    "comunas": ["Cerrillos", "Cerro Navia", "Conchalí", "El Bosque", "Estación Central", "Huechuraba", "Independencia", "La Cisterna", "La Florida", "La Granja", "La Pintana", "La Reina", "Las Condes", "Lo Barnechea", "Lo Espejo", "Lo Prado", "Macul", "Maipú", "Ñuñoa", "Pedro Aguirre Cerda", "Peñalolén", "Providencia", "Pudahuel", "Quilicura", "Quinta Normal", "Recoleta", "Renca", "San Joaquín", "San Miguel", "San Ramón", "Vitacura", "Puente Alto", "Pirque", "San José de Maipo", "Colina", "Lampa", "TilVl", "San Bernardo", "Buin", "Calera de Tango", "Paine", "Melipilla", "Alhué", "Curacaví", "María Pinto", "San Pedro", "Talagante", "El Monte", "Isla de Maipo", "Padre Hurtado", "Peñaflor"]
            }]
        };

        const locales = ["Local 1", "Local 2", "Local 3"];

        const regionSelect = document.getElementById('regionCascada');
        const comunaSelect = document.getElementById('comunaCascada');
        const localSelect = document.getElementById('localCascada');

        function cargarRegiones() {
            regionSelect.innerHTML = '<option value="">Selecciona región</option>';
            RegionesYcomunas.regiones.forEach(function(region, idx) {
                const option = document.createElement('option');
                option.value = idx;
                option.textContent = region.NombreRegion;
                regionSelect.appendChild(option);
            });
        }

        regionSelect.addEventListener('change', function() {
            comunaSelect.innerHTML = '<option value="">Selecciona comuna</option>';
            localSelect.innerHTML = '<option value="">Selecciona local</option>';
            const regionIdx = regionSelect.value;
            if (regionIdx !== "" && RegionesYcomunas.regiones[regionIdx]) {
                RegionesYcomunas.regiones[regionIdx].comunas.forEach(function(comuna) {
                    const option = document.createElement('option');
                    option.value = comuna;
                    option.textContent = comuna;
                    comunaSelect.appendChild(option);
                });
            }
        });

        comunaSelect.addEventListener('change', function() {
            localSelect.innerHTML = '<option value="">Selecciona local</option>';
            locales.forEach(function(local) {
                const option = document.createElement('option');
                option.value = local;
                option.textContent = local;
                localSelect.appendChild(option);
            });
        });

        cargarRegiones();

        // --- Cascada para edición de región y comuna ---
        const editRegionSelect = document.getElementById('editRegion');
        const editComunaSelect = document.getElementById('editComuna');

        // Cargar regiones en el select de edición
        function cargarRegionesEdicion() {
            editRegionSelect.innerHTML = '<option value="">Selecciona región</option>';
            RegionesYcomunas.regiones.forEach(function(region, idx) {
                const option = document.createElement('option');
                option.value = idx;
                option.textContent = region.NombreRegion;
                editRegionSelect.appendChild(option);
            });
        }

        // Cargar comunas según la región seleccionada en edición
        editRegionSelect.addEventListener('change', function() {
            editComunaSelect.innerHTML = '<option value="">Selecciona comuna</option>';
            const regionIdx = editRegionSelect.value;
            if (regionIdx !== "" && RegionesYcomunas.regiones[regionIdx]) {
                RegionesYcomunas.regiones[regionIdx].comunas.forEach(function(comuna) {
                    const option = document.createElement('option');
                    option.value = comuna;
                    option.textContent = comuna;
                    editComunaSelect.appendChild(option);
                });
            }
        });

        // Inicializar regiones en edición al cargar la página
        cargarRegionesEdicion();
</script>

</body>
</html>