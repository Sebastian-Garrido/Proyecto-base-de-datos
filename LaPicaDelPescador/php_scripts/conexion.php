<?php

class Conexion {
    //HOST DONDE SE ENCUENTRA LA BASE DE DATOS
    private $host = 'localhost';
    //LA BASE DE DATOS EN SI
    private $database = 'XEDB1';
    //ESTOS DATOS SE LOS TIENEN QUE DAR DE MAGALLANES.
    private $username = 'RTHEARTLESS';
    private $password = 'admin';
    private $port = "1521"; // Puerto por defecto de PostgreSQL, ajustar si es necesario
    private $connection;
    private static $instance = null;
    
    //instancia unica para todo el programa, solo usaremos getInstance cada vez
    //que queramos acceder a la base de datos.
    private function __construct() {
        $this->connect();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function connect() {
        try {
            //TODO:AJUSTAR ESTE PDO A MAGALLANES (PGSQL)
            //$dsn = "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4";
            $dsn = "oci:host={$this->host};port={$this->port};dbname={$this->database};user={$this->username};password={$this->password}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            //DESCOMENTAR ESTA LINEA PARA VERIFICAR LA CONEXIÓN
            //echo "Conectando a la base de datos {$this->database} en {$this->host}...\n";
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function select($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Error en consulta SELECT: " . $e->getMessage());
        }
    }
    
    public function selectOne($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("Error en consulta SELECT: " . $e->getMessage());
        }
    }
    
    public function execute($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception("Error en consulta: " . $e->getMessage());
        }
    }
    
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    private function __clone() {}
    //private function __wakeup() {}
}

?>