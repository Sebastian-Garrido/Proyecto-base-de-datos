<?php
include 'config.php';

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "INSERT INTO USERS (ID, NAME, EMAIL) VALUES (USERS_SEQ.NEXTVAL, :name, :email)";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":name", $name);
    oci_bind_by_name($stmt, ":email", $email);

    if (oci_execute($stmt)) {
        echo "✅ Usuario creado.";
    } else {
        $e = oci_error($stmt);
        echo "❌ Error: " . $e['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);
}
?>
<form method="POST">
    Nombre: <input type="text" name="name" required><br>
    Email: <input type="email" name="email" required><br>
    <button type="submit">Crear</button>
</form>
