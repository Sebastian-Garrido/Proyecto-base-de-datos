<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$username = "sgarridoa";
$password = "sgarridoa_DB2025";
$connection_string = "oci:dbname=//magallanes.icci-unap.cl:1521/FREEPDB1;charset=UTF8";

try {
    $conn = new PDO($connection_string, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}
?>