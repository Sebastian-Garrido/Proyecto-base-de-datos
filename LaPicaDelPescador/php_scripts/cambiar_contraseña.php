<?php
include "configs_oracle/config_pdo.php";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambiar_contraseña'])) {
    $trid = $_POST['trid'] ?? '';
    $password = $_POST['password'] ?? '';
    if ($trid && $password) {
        $sql = "CALL CAMBIARCONTRASEÑA(:P_TRID, :P_TRCONTRASEÑA)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':P_TRID', $trid, PDO::PARAM_INT);
        $stmt->bindParam(':P_TRCONTRASEÑA', $password);
        $stmt->execute();
        echo 'ok';
        exit;
    }
}
echo 'error';
