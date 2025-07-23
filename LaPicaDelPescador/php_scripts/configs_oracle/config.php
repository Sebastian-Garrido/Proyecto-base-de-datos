<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$username = "RTHEARTLESS";
$password = "admin";
$connection_string = "//magallanes.icci-unap.cl:1521/FREEPDB1";

$conn = oci_connect($username, $password, $connection_string);

if (!$conn) {
    $e = oci_error();
    die("❌ Error de conexión: " . $e['message']);
}

echo "✅ Conexión exitosa a Oracle";
oci_close($conn);
?>
