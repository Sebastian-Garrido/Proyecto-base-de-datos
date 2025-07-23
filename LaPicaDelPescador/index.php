<?php
    include "php_scripts\configs_oracle\config_pdo.php"
?>

<?php
session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $run = $_POST['run'];
        $password = $_POST['password'];
        $key = file_get_contents('php_scripts/key.txt');
        $hashed = hash_hmac('sha256', $password, $key);

        // Buscar trabajador por RUN y contraseña
        $sql = "SELECT TrID, TrRUN, TrNombres, TrCargo, Local_LoID FROM Trabajador WHERE TrRUN = :run AND TrContraseña = :pass";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':run', $run, PDO::PARAM_INT);
        $stmt->bindParam(':pass', $hashed, PDO::PARAM_STR);
        $stmt->execute();

        $trabajador = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($trabajador) {
            // Guardar datos en sesión
            $_SESSION['TRID'] = $trabajador['TRID'];
            $_SESSION['TRRUN'] = $trabajador['TRRUN'];
            $_SESSION['TRNOMBRES'] = $trabajador['TRNOMBRES'];
            $_SESSION['TRCARGO'] = $trabajador['TRCARGO'];
            $_SESSION['LOCAL_LOID'] = $trabajador['LOCAL_LOID'];
            header("Location: inicio.php");
            exit;
        } else {
            $error = "RUN o contraseña incorrectos.";
        }
    }
?>

<?php

if (isset($_SESSION['TRID']) || isset($_SESSION['TRRUN']) || isset($_SESSION['TRNOMBRES']) || isset($_SESSION['TRCARGO']) || ISSET($_SESSION['LOCAL_LOID'])) {
    header("Location: inicio.php");
    exit;
}
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - La Pica del Pescador</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #a2d9ff 0%, #e0f7fa 100%);
            background-repeat: no-repeat;
            position: relative;
            overflow-x: hidden;
        }
        a[data-bs-toggle="modal"] {
            cursor: pointer;
        }
        /* Fondo blurry */
        .bg-blur {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            z-index: 0;
            background: url('background.jpg') center center/cover no-repeat;
            filter: blur(8px) brightness(0.7);
        }
        .wave {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100vw;
            min-width: 100%;
            height: 80px;
            z-index: 2;
            pointer-events: none;
        }
        .h-custom {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 40px;
            position: relative;
            z-index: 3;
        }
        .login-card {
            background: rgba(255,255,255,0.90);
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.18);
            padding: 2.5rem 2rem;
            z-index: 4;
        }
        @media (max-width: 600px) {
            .wave { height: 180px; }
            .h-custom { min-height: 100vh; margin-top: 20px; }
            .login-card { padding: 1.5rem 0.5rem; }
        }
        
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>

<?php if (isset($error)): ?>
  <div class="alert alert-danger alert-dismissible fade show position-fixed w-100" style="top:0;left:0;z-index:9999;" role="alert">
    <?php echo $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
  </div>
<?php endif; ?>

<div class="bg-blur"></div>
<section class="vh-100 d-flex align-items-center justify-content-center">
  <div class="container h-custom">
    <div class="row justify-content-center align-items-center h-100">
      <div class="col-md-9 col-lg-6 col-xl-5 d-none d-md-block">
        <img src="para_el_login.png"
          class="img-fluid"
          alt="Sample image"
          style="max-width: 100%; height: auto;">
      </div>
      <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-0">
        <div class="login-card">
          <form method="POST">
            <div class="text-center mb-4">
              <h2>La Pica del Pescador</h2>
            </div>
            <!-- RUN input -->
            <div class="form-outline mb-4">
              <input type="number" id="form3Example3" name="run" class="form-control form-control-lg"
                placeholder="12345678" required />
              <label class="form-label" for="form3Example3">RUN (sin puntos ni número verificador)</label>
            </div>
            <!-- Password input -->
            <div class="form-outline mb-3">
              <input type="password" id="form3Example4" name="password" class="form-control form-control-lg"
                placeholder="Contraseña" required />
              <label class="form-label" for="form3Example4">Contraseña</label>
            </div>
            <div class="d-flex justify-content-end align-items-center mb-3">
                <a class="text-body" data-bs-toggle="modal" data-bs-target="#avisoModal">¿Olvidaste tu contraseña?</a>
            </div>
            <div class="text-center text-lg-start mt-4 pt-2">
              <button type="submit" class="btn btn-primary btn-lg w-100"
                style="padding-left: 2.5rem; padding-right: 2.5rem;">Entrar</button>
              <p class="small fw-bold mt-2 pt-1 mb-0">¿No tienes cuenta? <a href="registro.php"
                  class="link-danger">Regístrate</a></p>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <svg class="wave" viewBox="0 0 1440 80" preserveAspectRatio="none" style="height:80px;">
    <path fill="#4fc3f7" fill-opacity="0.6" d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z"></path>
    <path fill="#81d4fa" fill-opacity="0.5" d="M0,60 C400,20 1040,80 1440,60 L1440,80 L0,80 Z"></path>
    <path fill="#b3e0ff" fill-opacity="0.4" d="M0,80 C400,60 1040,80 1440,80 L1440,80 L0,80 Z"></path>
</svg>
<!-- Modal Bootstrap FUERA del form y de la card -->
<div class="modal fade" id="avisoModal" tabindex="-1" aria-labelledby="avisoModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="avisoModalLabel">Aviso</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        Por favor contacta a tu administrador.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
      </div>
    </div>
  </div>
</div>
    <path fill="#4fc3f7" fill-opacity="0.6" d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z"></path>
    <path fill="#81d4fa" fill-opacity="0.5" d="M0,60 C400,20 1040,80 1440,60 L1440,80 L0,80 Z"></path>
    <path fill="#b3e0ff" fill-opacity="0.4" d="M0,80 C400,60 1040,80 1440,80 L1440,80 L0,80 Z"></path>
  </svg>
</section>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>