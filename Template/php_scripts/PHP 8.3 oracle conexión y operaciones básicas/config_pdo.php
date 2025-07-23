<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$username = "RTHEARTLESS";
$password = "admin";
$connection_string = "oci:dbname=//localhost:1521/XEPDB1;charset=UTF8";

try {
    $conn = new PDO($connection_string, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexión exitosa a Oracle con PDO";
} catch (PDOException $e) {
    die("❌ Error de conexión: " . $e->getMessage());
}
?>