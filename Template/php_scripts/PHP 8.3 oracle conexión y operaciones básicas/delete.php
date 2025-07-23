<?php include 'config.php'; ?>

<form method="POST">
    ID a eliminar: <input type="number" name="id" required><br>
    <button type="submit">Eliminar</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "DELETE FROM USERS WHERE ID = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $_POST['id']);

    if (oci_execute($stmt)) {
        echo "🗑️ Usuario eliminado.";
    } else {
        echo "❌ Error: " . oci_error($stmt)['message'];
    }

    oci_free_statement($stmt);
    oci_close($conn);
}
?>
