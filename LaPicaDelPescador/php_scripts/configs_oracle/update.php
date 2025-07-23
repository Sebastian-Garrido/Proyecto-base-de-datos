<?php include 'config.php'; ?>

<form method="POST">
    ID: <input type="number" name="id" required><br>
    Nuevo Nombre: <input type="text" name="name" required><br>
    Nuevo Correo: <input type="email" name="email" required><br>
    <button type="submit">Actualizar Usuario</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "UPDATE USERS SET NAME = :name, EMAIL = :email WHERE ID = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $_POST['id']);
    oci_bind_by_name($stmt, ":name", $_POST['name']);
    oci_bind_by_name($stmt, ":email", $_POST['email']);

    if (oci_execute($stmt)) {
        echo "✅ Usuario actualizado.";
    } else {
        echo "❌ Error: " . oci_error($stmt)['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);
}
?>

