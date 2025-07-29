<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Course.php';
require_once __DIR__ . '/Participant.php';
require_once __DIR__ . '/CertificateGenerator.php';

class Certificate {
    private $conn;
    private $table_name = "certificates";
    
    public $id;
    public $course_id;
    public $participant_id;
    public $unique_code;
    public $issue_date;
    public $file_path;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function generateUniqueCode() {
        return strtoupper(uniqid('CERT-', true));
    }
    
    public function create($course_id, $participant_id) {
        // Verifica se o participante está presente
        $presence = new Presence();
        if (!$presence->isParticipantPresent($course_id, $participant_id)) {
            return ['success' => false, 'message' => 'Participante não está marcado como presente'];
        }
        
        // Verifica se já existe certificado para este participante neste curso
        $existing = $this->getByCourseAndParticipant($course_id, $participant_id);
        if ($existing) {
            return ['success' => false, 'message' => 'Certificado já foi emitido para este participante'];
        }
        
        $unique_code = $this->generateUniqueCode();
        
        // Gera o PDF do certificado
        $generator = new CertificateGenerator();
        $file_path = $generator->generateCertificatePDF($course_id, $participant_id, $unique_code);
        
        if ($file_path) {
            $query = "INSERT INTO " . $this->table_name . " (course_id, participant_id, unique_code, file_path) VALUES (:course_id, :participant_id, :unique_code, :file_path)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':course_id', $course_id);
            $stmt->bindParam(':participant_id', $participant_id);
            $stmt->bindParam(':unique_code', $unique_code);
            $stmt->bindParam(':file_path', $file_path);
            
            if ($stmt->execute()) {
                return ['success' => true, 'certificate_id' => $this->conn->lastInsertId(), 'unique_code' => $unique_code];
            }
        }
        
        return ['success' => false, 'message' => 'Erro ao gerar certificado'];
    }
    
    public function getByCode($unique_code) {
        $query = "SELECT c.*, co.name as course_name, co.workload, co.date as course_date, co.responsible, 
                         p.name as participant_name, p.email as participant_email 
                  FROM " . $this->table_name . " c 
                  JOIN courses co ON c.course_id = co.id 
                  JOIN participants p ON c.participant_id = p.id 
                  WHERE c.unique_code = :unique_code LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':unique_code', $unique_code);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return false;
    }
    
    public function getByEmail($email) {
        $query = "SELECT c.*, co.name as course_name, co.workload, co.date as course_date, co.responsible, 
                         p.name as participant_name, p.email as participant_email 
                  FROM " . $this->table_name . " c 
                  JOIN courses co ON c.course_id = co.id 
                  JOIN participants p ON c.participant_id = p.id 
                  WHERE p.email = :email 
                  ORDER BY c.issue_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByCourseAndParticipant($course_id, $participant_id) {
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
    
    public function getAll($filters = []) {
        $where_conditions = [];
        $params = [];
        
        if (!empty($filters['course_id'])) {
            $where_conditions[] = "c.course_id = :course_id";
            $params[':course_id'] = $filters['course_id'];
        }
        
        if (!empty($filters['participant_email'])) {
            $where_conditions[] = "p.email LIKE :participant_email";
            $params[':participant_email'] = '%' . $filters['participant_email'] . '%';
        }
        
        if (!empty($filters['participant_name'])) {
            $where_conditions[] = "p.name LIKE :participant_name";
            $params[':participant_name'] = '%' . $filters['participant_name'] . '%';
        }
        
        $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
        
        $query = "SELECT c.*, co.name as course_name, co.workload, co.date as course_date, co.responsible, 
                         p.name as participant_name, p.email as participant_email 
                  FROM " . $this->table_name . " c 
                  JOIN courses co ON c.course_id = co.id 
                  JOIN participants p ON c.participant_id = p.id 
                  $where_clause
                  ORDER BY c.issue_date DESC";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    

    
    public function exportToCSV($filters = []) {
        $certificates = $this->getAll($filters);
        
        $csv_data = "Código,Participante,Email,Curso,Data do Curso,Carga Horária,Responsável,Data de Emissão\n";
        
        foreach ($certificates as $cert) {
            $csv_data .= '"' . $cert['unique_code'] . '",';
            $csv_data .= '"' . $cert['participant_name'] . '",';
            $csv_data .= '"' . $cert['participant_email'] . '",';
            $csv_data .= '"' . $cert['course_name'] . '",';
            $csv_data .= '"' . date('d/m/Y', strtotime($cert['course_date'])) . '",';
            $csv_data .= '"' . $cert['workload'] . '",';
            $csv_data .= '"' . $cert['responsible'] . '",';
            $csv_data .= '"' . date('d/m/Y H:i:s', strtotime($cert['issue_date'])) . '"';
            $csv_data .= "\n";
        }
        
        return $csv_data;
    }
}
?>

