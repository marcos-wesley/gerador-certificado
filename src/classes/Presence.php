<?php
require_once __DIR__ . '/../config/database.php';

class Presence {
    private $conn;
    private $table_name = "presences";
    
    public $id;
    public $course_id;
    public $participant_id;
    public $is_present;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function markPresence($course_id, $participant_id, $is_present = true) {
        // Verifica se já existe um registro
        $existing = $this->getPresence($course_id, $participant_id);
        
        if ($existing) {
            // Atualiza o registro existente
            $query = "UPDATE " . $this->table_name . " SET is_present = :is_present WHERE course_id = :course_id AND participant_id = :participant_id";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':is_present', $is_present, PDO::PARAM_BOOL);
            $stmt->bindParam(':course_id', $course_id);
            $stmt->bindParam(':participant_id', $participant_id);
            
            return $stmt->execute();
        } else {
            // Cria um novo registro
            $query = "INSERT INTO " . $this->table_name . " (course_id, participant_id, is_present) VALUES (:course_id, :participant_id, :is_present)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':course_id', $course_id);
            $stmt->bindParam(':participant_id', $participant_id);
            $stmt->bindParam(':is_present', $is_present, PDO::PARAM_BOOL);
            
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
        }
        
        return false;
    }
    
    public function getPresence($course_id, $participant_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE course_id = :course_id AND participant_id = :participant_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':participant_id', $participant_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return false;
    }
    
    public function getPresencesByCourse($course_id) {
        $query = "SELECT p.*, pt.name, pt.email FROM " . $this->table_name . " p 
                  JOIN participants pt ON p.participant_id = pt.id 
                  WHERE p.course_id = :course_id 
                  ORDER BY pt.name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getPresentParticipants($course_id) {
        $query = "SELECT p.*, pt.name, pt.email FROM " . $this->table_name . " p 
                  JOIN participants pt ON p.participant_id = pt.id 
                  WHERE p.course_id = :course_id AND p.is_present = 1 
                  ORDER BY pt.name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function isParticipantPresent($course_id, $participant_id) {
        $presence = $this->getPresence($course_id, $participant_id);
        return $presence && $presence['is_present'] == 1;
    }
    
    public function addParticipantToCourse($course_id, $name, $email) {
        require_once __DIR__ . '/Participant.php';
        $participant = new Participant();
        
        $participant_id = $participant->create($name, $email);
        if ($participant_id) {
            return $this->markPresence($course_id, $participant_id, true);
        }
        
        return false;
    }
}
?>

