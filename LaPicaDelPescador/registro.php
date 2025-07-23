<?php
    include "php_scripts\configs_oracle\config_pdo.php"
?>

<?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $run = $_POST['rut'];
        $nueva_contrasena = $_POST['password'];
        $key = file_get_contents('php_scripts\key.txt');
        $nueva_contrasena = hash_hmac('sha256', $nueva_contrasena, $key);

        try {
            $sql = "BEGIN RegistrarContraseña(:p_run, :p_contrasena); END;";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':p_run', $run, PDO::PARAM_INT);
            $stmt->bindParam(':p_contrasena', $nueva_contrasena, PDO::PARAM_STR);
            $stmt->execute();
            // Redirige para limpiar el POST
            header("Location: registro.php?exito=1");
            exit;
        } catch (PDOException $e) {
            header("Location: registro.php?error=1");
            exit;
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - La Pica del Pescador</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            width: 100vw;
            height: 100vh;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #a2d9ff 0%, #e0f7fa 100%);
            background-repeat: no-repeat;
            position: relative;
            overflow-x: hidden;
        }
        .bg-blur {
            position: fixed;
            top: 0; left: 0; width: 100vw; height: 100vh;
            z-index: 0;
            background: url('background.jpg') center center/cover no-repeat;
            filter: blur(8px) brightness(0.7);
        }
        .h-custom {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 0;
            position: relative;
            z-index: 3;
            width: 100vw;
            box-sizing: border-box;
        }

        section.vh-100 {
            width: 100vw;
            height: 100vh;
            /* Eliminar centrado forzado */
            padding: 0;
        }

        .login-card {
            background: rgba(255,255,255,0.97);
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.18);
            padding: 2.5rem 2.5rem;
            z-index: 4;
            max-width: 420px;
            width: 100%;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        @media (max-width: 600px) {
            .h-custom { min-height: 100vh; margin-top: 0; }
            .login-card { padding: 1.2rem 0.5rem; max-width: 98vw; }
        }

        .wave {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100vw;
            min-width: 100%;
            height: 80px;
            z-index: 10;
            pointer-events: none;
        }
        
    </style>
</head>
<body>
<div class="bg-blur"></div>
<section class="vh-100 d-flex align-items-center justify-content-center">
  <div class="h-custom">
    <div class="login-card">
      <form id="registroForm" method="POST" style="width:100%;">
        <div class="text-center mb-4">
          <h2 style="font-size:2rem;font-weight:700;">Registro de Usuario</h2>
        </div>
        <!-- RUT input -->
        <div class="form-outline mb-3">
          <label class="form-label" for="rut" style="font-weight:500;">RUT</label>
          <input type="text" id="rut" name="rut" class="form-control" placeholder="12345678" required />
        </div>
        <!-- Password input -->
        <div class="form-outline mb-3">
          <label class="form-label" for="password" style="font-weight:500;">Contraseña</label>
          <input type="password" id="password" name="password" class="form-control" placeholder="Contraseña" required />
        </div>
        <div class="text-center mt-4 pt-2">
          <button type="submit" class="btn btn-success w-100" style="font-size:1.1rem;">Registrarse</button>
          <p class="small fw-bold mt-2 pt-1 mb-0">¿Ya tienes cuenta? <a href="index.html" class="link-primary">Inicia sesión</a></p>
        </div>
      </form>
    </div>
  </div>
</section>

<svg class="wave" viewBox="0 0 1440 80" preserveAspectRatio="none" style="height:80px;">
    <path fill="#4fc3f7" fill-opacity="0.6" d="M0,40 C360,80 1080,0 1440,40 L1440,80 L0,80 Z"></path>
    <path fill="#81d4fa" fill-opacity="0.5" d="M0,60 C400,20 1040,80 1440,60 L1440,80 L0,80 Z"></path>
    <path fill="#b3e0ff" fill-opacity="0.4" d="M0,80 C400,60 1040,80 1440,80 L1440,80 L0,80 Z"></path>
</svg>

<script src="js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('registroForm').addEventListener('submit', function(e) {
});
</script>
</body>
</html>
