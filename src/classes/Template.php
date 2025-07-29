<?php
require_once '../config/database.php';

class Template {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM certificate_templates ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM certificate_templates WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function create($name, $description, $file_path, $fields_config) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO certificate_templates (name, description, file_path, fields_config, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([$name, $description, $file_path, json_encode($fields_config)]);
            
            return [
                'success' => true,
                'id' => $this->db->lastInsertId(),
                'message' => 'Modelo criado com sucesso!'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro ao criar modelo: ' . $e->getMessage()
            ];
        }
    }
    
    public function update($id, $name, $description, $file_path = null, $fields_config = null) {
        try {
            if ($file_path && $fields_config) {
                $stmt = $this->db->prepare("
                    UPDATE certificate_templates 
                    SET name = ?, description = ?, file_path = ?, fields_config = ?, updated_at = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$name, $description, $file_path, json_encode($fields_config), $id]);
            } else {
                $stmt = $this->db->prepare("
                    UPDATE certificate_templates 
                    SET name = ?, description = ?, updated_at = NOW() 
                    WHERE id = ?
                ");
                $stmt->execute([$name, $description, $id]);
            }
            
            return [
                'success' => true,
                'message' => 'Modelo atualizado com sucesso!'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro ao atualizar modelo: ' . $e->getMessage()
            ];
        }
    }
    
    public function delete($id) {
        try {
            // Verificar se o modelo está sendo usado por algum curso
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM courses WHERE template_id = ?");
            $stmt->execute([$id]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                return [
                    'success' => false,
                    'message' => 'Não é possível excluir este modelo pois ele está sendo usado por ' . $count . ' curso(s).'
                ];
            }
            
            // Obter caminho do arquivo para deletar
            $template = $this->getById($id);
            if ($template && file_exists('../../' . $template['file_path'])) {
                unlink('../../' . $template['file_path']);
            }
            
            $stmt = $this->db->prepare("DELETE FROM certificate_templates WHERE id = ?");
            $stmt->execute([$id]);
            
            return [
                'success' => true,
                'message' => 'Modelo excluído com sucesso!'
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Erro ao excluir modelo: ' . $e->getMessage()
            ];
        }
    }
    
    public function uploadFile($file) {
        $upload_dir = '../../templates/';
        
        // Criar diretório se não existir
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowed_types)) {
            return [
                'success' => false,
                'message' => 'Tipo de arquivo não permitido. Use apenas JPG, PNG ou PDF.'
            ];
        }
        
        if ($file['size'] > $max_size) {
            return [
                'success' => false,
                'message' => 'Arquivo muito grande. Tamanho máximo: 5MB.'
            ];
        }
        
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'template_' . time() . '_' . uniqid() . '.' . $extension;
        $file_path = $upload_dir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            return [
                'success' => true,
                'file_path' => 'templates/' . $filename,
                'message' => 'Arquivo enviado com sucesso!'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erro ao fazer upload do arquivo.'
            ];
        }
    }
    
    public function getDefaultFields() {
        return [
            'participant_name' => [
                'label' => 'Nome do Participante',
                'x' => 50,
                'y' => 45,
                'font_size' => 24,
                'font_weight' => 'bold',
                'color' => '#333333'
            ],
            'course_name' => [
                'label' => 'Nome do Curso',
                'x' => 50,
                'y' => 55,
                'font_size' => 18,
                'font_weight' => 'bold',
                'color' => '#666666'
            ],
            'workload' => [
                'label' => 'Carga Horária',
                'x' => 30,
                'y' => 65,
                'font_size' => 14,
                'font_weight' => 'normal',
                'color' => '#666666'
            ],
            'course_date' => [
                'label' => 'Data do Curso',
                'x' => 70,
                'y' => 65,
                'font_size' => 14,
                'font_weight' => 'normal',
                'color' => '#666666'
            ],
            'responsible' => [
                'label' => 'Responsável',
                'x' => 50,
                'y' => 80,
                'font_size' => 16,
                'font_weight' => 'bold',
                'color' => '#333333'
            ],
            'issue_date' => [
                'label' => 'Data de Emissão',
                'x' => 20,
                'y' => 90,
                'font_size' => 12,
                'font_weight' => 'normal',
                'color' => '#999999'
            ],
            'unique_code' => [
                'label' => 'Código de Validação',
                'x' => 80,
                'y' => 90,
                'font_size' => 12,
                'font_weight' => 'normal',
                'color' => '#999999'
            ]
        ];
    }
}
?>

