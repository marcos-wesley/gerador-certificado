<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Course.php';
require_once __DIR__ . '/Participant.php';
require_once __DIR__ . '/Presence.php';

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
        $file_path = $this->generateCertificatePDF($course_id, $participant_id, $unique_code);
        
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
    
    private function generateCertificatePDF($course_id, $participant_id, $unique_code) {
        $course = new Course();
        $participant = new Participant();
        
        $courseData = $course->getById($course_id);
        $participantData = $participant->getById($participant_id);
        
        if (!$courseData || !$participantData) {
            return false;
        }
        
        // Cria o diretório de certificados se não existir
        $certificates_dir = __DIR__ . '/../../certificates/';
        if (!is_dir($certificates_dir)) {
            mkdir($certificates_dir, 0755, true);
        }
        
        $filename = 'certificate_' . $unique_code . '.pdf';
        $file_path = $certificates_dir . $filename;
        
        // Gera o HTML do certificado
        $html = $this->generateCertificateHTML($courseData, $participantData, $unique_code);
        
        // Converte HTML para PDF usando DomPDF ou similar
        // Por simplicidade, vamos salvar como HTML por enquanto
        file_put_contents(str_replace('.pdf', '.html', $file_path), $html);
        
        return 'certificates/' . $filename;
    }
    
    private function generateCertificateHTML($courseData, $participantData, $unique_code) {
        $course_date = date('d/m/Y', strtotime($courseData['date']));
        $issue_date = date('d/m/Y');
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Certificado</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; margin: 50px; }
                .certificate { border: 5px solid #333; padding: 50px; margin: 20px; }
                .title { font-size: 36px; font-weight: bold; margin-bottom: 30px; }
                .subtitle { font-size: 24px; margin-bottom: 20px; }
                .content { font-size: 18px; line-height: 1.6; margin: 20px 0; }
                .participant { font-size: 28px; font-weight: bold; margin: 30px 0; text-decoration: underline; }
                .course { font-size: 22px; font-weight: bold; margin: 20px 0; }
                .footer { margin-top: 50px; font-size: 14px; }
                .code { position: absolute; bottom: 20px; right: 20px; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="certificate">
                <div class="title">CERTIFICADO</div>
                <div class="subtitle">Certificamos que</div>
                <div class="participant">' . htmlspecialchars($participantData['name']) . '</div>
                <div class="content">
                    participou do curso/evento<br>
                    <div class="course">' . htmlspecialchars($courseData['name']) . '</div>
                    com carga horária de ' . htmlspecialchars($courseData['workload']) . ',<br>
                    realizado em ' . $course_date . '.
                </div>
                <div class="footer">
                    <p>Responsável: ' . htmlspecialchars($courseData['responsible']) . '</p>
                    <p>Data de emissão: ' . $issue_date . '</p>
                </div>
                <div class="code">Código: ' . $unique_code . '</div>
            </div>
        </body>
        </html>';
        
        return $html;
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

