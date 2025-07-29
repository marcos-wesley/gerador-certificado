<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/Course.php';
require_once __DIR__ . '/Participant.php';
require_once __DIR__ . '/Template.php';
require_once __DIR__ . '/../../vendor/autoload.php'; // Para Dompdf

use Dompdf\Dompdf;
use Dompdf\Options;

class CertificateGenerator {
    private $conn;
    private $templateClass;
    private $courseClass;
    private $participantClass;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->templateClass = new Template();
        $this->courseClass = new Course();
        $this->participantClass = new Participant();
    }

    public function generateCertificatePDF($course_id, $participant_id, $unique_code) {
        $courseData = $this->courseClass->getById($course_id);
        $participantData = $this->participantClass->getById($participant_id);

        if (!$courseData || !$participantData) {
            return false;
        }

        // Obter o modelo de certificado associado ao curso ou o modelo padrão
        $templateData = null;
        if (!empty($courseData['template_id'])) {
            $templateData = $this->templateClass->getById($courseData['template_id']);
        }

        // Se não houver modelo específico ou o modelo não for encontrado, usar o padrão
        if (!$templateData) {
            // Buscar o modelo padrão (assumindo que o ID 1 é o padrão)
            $templateData = $this->templateClass->getById(1);
            if (!$templateData) {
                // Se não houver modelo padrão, retornar erro
                return false;
            }
        }

        $template_file_path = __DIR__ . '/../../' . $templateData['file_path'];
        $fields_config = json_decode($templateData['fields_config'], true);

        // Cria o diretório de certificados se não existir
        $certificates_dir = __DIR__ . '/../../certificates/';
        if (!is_dir($certificates_dir)) {
            mkdir($certificates_dir, 0755, true);
        }

        $filename = 'certificate_' . $unique_code . '.pdf';
        $file_path = $certificates_dir . $filename;

        // Renderiza o HTML do certificado com base no modelo e nos dados
        $html = $this->renderCertificateHTML($template_file_path, $fields_config, $courseData, $participantData, $unique_code);

        // Configurações do Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'sans-serif');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape'); // Ou 'portrait' dependendo do modelo
        $dompdf->render();

        // Salva o PDF
        file_put_contents($file_path, $dompdf->output());

        return 'certificates/' . $filename;
    }

    private function renderCertificateHTML($template_file_path, $fields_config, $courseData, $participantData, $unique_code) {
        $course_date = date('d/m/Y', strtotime($courseData['date']));
        $issue_date = date('d/m/Y');

        // Carrega o conteúdo do arquivo de modelo
        $template_content = file_get_contents($template_file_path);

        // Substituições básicas para o modelo padrão
        $replacements = [
            '{{participant_name}}' => htmlspecialchars($participantData['name']),
            '{{course_name}}' => htmlspecialchars($courseData['name']),
            '{{workload}}' => htmlspecialchars($courseData['workload']),
            '{{course_date}}' => $course_date,
            '{{responsible}}' => htmlspecialchars($courseData['responsible']),
            '{{issue_date}}' => $issue_date,
            '{{unique_code}}' => $unique_code
        ];

        $html = str_replace(array_keys($replacements), array_values($replacements), $template_content);

        // Adicionar estilos para posicionamento dos campos dinâmicos (se o modelo for HTML)
        // Esta parte pode ser mais complexa dependendo da flexibilidade do modelo
        // Por enquanto, vamos assumir que o modelo já tem placeholders ou que é uma imagem de fundo

        return $html;
    }
}
?>

