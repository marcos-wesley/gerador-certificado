<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'certificados_db';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, 
                                  $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
    
    public function createDatabase() {
        try {
            // Conecta sem especificar o banco de dados
            $conn = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
            $conn->exec("set names utf8");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Cria o banco de dados se não existir
            $sql = "CREATE DATABASE IF NOT EXISTS " . $this->db_name . " CHARACTER SET utf8 COLLATE utf8_general_ci";
            $conn->exec($sql);
            
            return true;
        } catch(PDOException $exception) {
            echo "Database creation error: " . $exception->getMessage();
            return false;
        }
    }
    
    public function createTables() {
        $conn = $this->getConnection();
        
        if ($conn) {
            try {
                // Lê o arquivo SQL e executa
                $sql = file_get_contents(__DIR__ . '/../database/schema.sql');
                $conn->exec($sql);
                
                // Insere usuário administrador padrão
                $defaultUser = "INSERT IGNORE INTO users (username, password) VALUES ('admin', '" . password_hash('admin123', PASSWORD_DEFAULT) . "')";
                $conn->exec($defaultUser);
                
                return true;
            } catch(PDOException $exception) {
                echo "Table creation error: " . $exception->getMessage();
                return false;
            }
        }
        
        return false;
    }
}
?>

