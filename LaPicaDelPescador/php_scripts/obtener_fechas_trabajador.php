<?php
include "configs_oracle/config_pdo.php";
header('Content-Type: application/json');

$trid = isset($_GET['trid']) ? intval($_GET['trid']) : 0;
$result = [];
if ($trid > 0) {
    $sql = "SELECT FEID, FEFECHAINGRESO, FEFECHAEGRESO FROM FECHADETALLE WHERE TRABAJADOR_TRID = :trid ORDER BY FEFECHAINGRESO";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':trid', $trid, PDO::PARAM_INT);
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = [
            'FEID' => $row['FEID'],
            'FEFECHAINGRESO' => $row['FEFECHAINGRESO'],
            'FEFECHAEGRESO' => $row['FEFECHAEGRESO']
        ];
    }
}
echo json_encode($result);
