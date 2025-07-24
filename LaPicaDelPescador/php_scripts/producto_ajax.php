<?php
header('Content-Type: application/json');
session_start();
include '../php_scripts/configs_oracle/config_pdo.php';

if (!isset($_SESSION['LOCAL_LOID'])) {
    echo json_encode(['success' => false, 'msg' => 'Sesión no válida']);
    exit;
}

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
        $sql = "CALL AGREGARPRODUCTO(:P_NOMBRE, :P_DESCRIPCION, :P_PRECIO, :P_TIPO, :P_ENSTOCK, :P_ENMARCA, :P_DISPONIBILIDAD, :P_LOCAL_ID)";
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
        echo json_encode(['success' => true, 'msg' => $msg]);
        exit;
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
        $sql = "CALL EDITARPRODUCTO(:P_PRID, :P_NOMBRE, :P_DESCRIPCION, :P_PRECIO, :P_TIPO, :P_ENSTOCK, :P_ENMARCA, :P_DISPONIBILIDAD)";
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
        echo json_encode(['success' => true, 'msg' => $msg]);
        exit;
    } else {
        throw new Exception('Acción no válida');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'msg' => $e->getMessage()]);
    exit;
}
