<?php
require_once __DIR__ . '/../config/database.php';

class Course {
    private $conn;
    private $table_name = "courses";
    
    public $id;
    public $name;
    public $workload;
    public $date;
    public $responsible;
    public $description;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function create($name, $workload, $date, $responsible, $description = '', $template_id = null) {
        $query = "INSERT INTO " . $this->table_name . " (name, workload, date, responsible, description, template_id) VALUES (:name, :workload, :date, :responsible, :description, :template_id)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':workload', $workload);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':responsible', $responsible);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':template_id', $template_id);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    public function getAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return false;
    }
    
    public function update($id, $name, $workload, $date, $responsible, $description = '', $template_id = null) {
        $query = "UPDATE " . $this->table_name . " SET name = :name, workload = :workload, date = :date, responsible = :responsible, description = :description, template_id = :template_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':workload', $workload);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':responsible', $responsible);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':template_id', $template_id);
        
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
}
?>

