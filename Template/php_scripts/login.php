<?php

public function login($host, $dbname, $user, $password){
    try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8");
    // Opcional: Configurar errores para que lance excepciones
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $key = file_get_contents('key.txt')
    $hashedpw = hash_hmac('sha-256', $password, $key)
    $stmt = $db->prepare('SELECT * FROM Trabajador WHERE (TrRUN = ? OR TrCorreo = ?) AND TrContraseña = ?' )
    $stmt -> execute([$user, $user, $hashedpw])
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row){

    }


} catch (PDOException $e) {
    echo "Error de conexión: " . $e->getMessage();
}
}