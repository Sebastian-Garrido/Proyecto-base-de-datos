<?php
include "configs_oracle/config_pdo.php";
header('Content-Type: application/json');
$id = $_GET['id'] ?? '';
if (!$id) {
    echo json_encode([]);
    exit;
}
$sql = "SELECT TRID, TRRUN, TRTELEFONO, TRCORREO, TRCARGO, TRFECHANACIMIENTO, TRSUELDOHORA, TRNOMBRES, TRAPELLIDOPATERNO, TRAPELLIDOMATERNO, TRREGION, TRCOMUNA, TRCALLE, TRNUMEROCALLE, TRDIRECCIONADICIONAL, LOCAL_LOID FROM TRABAJADOR WHERE TRID = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    // Formatear fecha para input type="date" solo si es válida
    $fecha = $row['TRFECHANACIMIENTO'];
    if (!empty($fecha)) {
        $timestamp = strtotime($fecha);
    } else {
        $row['TRFECHANACIMIENTO'] = null;
    }
    echo json_encode($row);
} else {
    echo json_encode([]);
}