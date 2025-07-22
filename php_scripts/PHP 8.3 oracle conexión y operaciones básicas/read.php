<?php
include 'config.php';

$sql = "SELECT * FROM USERS ORDER BY ID";
$stmt = oci_parse($conn, $sql);
oci_execute($stmt);

echo "<h2>Lista de Usuarios</h2><table border='1'>";
echo "<tr><th>ID</th><th>Nombre</th><th>Email</th></tr>";
while ($row = oci_fetch_assoc($stmt)) {
    echo "<tr>";
    echo "<td>{$row['ID']}</td>";
    echo "<td>{$row['NAME']}</td>";
    echo "<td>{$row['EMAIL']}</td>";
    echo "</tr>";
}
echo "</table>";

oci_free_statement($stmt);
oci_close($conn);
?>
