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

        // Cria o diretório de certificados se não existir
        $certificates_dir = __DIR__ . '/../../certificates/';
        if (!is_dir($certificates_dir)) {
            mkdir($certificates_dir, 0755, true);
        }

        $filename = 'certificate_' . $unique_code . '.pdf';
        $file_path = $certificates_dir . $filename;

        // Renderiza o HTML do certificado com base no modelo e nos dados
        $html = $this->renderCertificateHTML($templateData, $courseData, $participantData, $unique_code);

        // Configurações do Dompdf para A4 horizontal
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'sans-serif');
        $options->set('dpi', 150); // Garantir qualidade mínima de 150dpi

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape'); // A4 horizontal (29,7cm x 21cm)
        $dompdf->render();

        // Salva o PDF
        file_put_contents($file_path, $dompdf->output());

        return 'certificates/' . $filename;
    }

    private function renderCertificateHTML($templateData, $courseData, $participantData, $unique_code) {
        $course_date = date('d/m/Y', strtotime($courseData['date']));
        $issue_date = date('d/m/Y');
        
        // Formatar nome do participante com primeira letra maiúscula
        $participant_name = $this->formatParticipantName($participantData['name']);
        
        // Verificar se há imagem de fundo personalizada
        $background_image = '';
        if (!empty($templateData['file_path']) && file_exists(__DIR__ . '/../../' . $templateData['file_path'])) {
            $background_image = 'data:image/' . pathinfo($templateData['file_path'], PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents(__DIR__ . '/../../' . $templateData['file_path']));
        }

        $html = '<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado - ' . htmlspecialchars($participant_name) . '</title>
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes:wght@400&family=Montserrat:wght@400;700;900&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Sansation:ital,wght@0,300;0,400;0,700;1,300;1,400;1,700&display=swap" rel="stylesheet">
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 297mm;
            height: 210mm;
            font-family: "Roboto", Arial, sans-serif;
            background-color: #ffffff;
            position: relative;
        }

        .background-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 297mm;
            height: 210mm;
            object-fit: cover;
            z-index: 0;
        }

        .certificate-container {
            width: 297mm;
            height: 210mm;
            position: absolute;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            z-index: 2;
            padding: 0 20mm;
            box-sizing: border-box;
        }

        
        .content {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 250mm;
            padding-bottom: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        
        .certificate-title {
            margin-top: 25mm; /* ou ajuste fino, como 15mm */
            margin-bottom: 10px;
            font-family:  "Sansation", sans-serif;
            font-weight: 900;
            font-size: 80px;
            color: #012d6a;
            letter-spacing: 3px;
            text-transform: uppercase;
            line-height: 1;
}
        }
        
        .certificate-text {
            font-family: "Roboto", Arial, sans-serif;
            font-size: 25px;
            color: #000000ff;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .participant-name {
            font-family: "Great Vibes", cursive;
            font-size: 130px;
            color: #012d6a;
            margin: 5px 0;
            font-weight: 400;
            line-height: 1.2;
            text-align:center;
        }
        
        .course-info {
            font-family: "Roboto", Arial, sans-serif;
            font-size: 16px;
            color: #000000ff;
            margin: 20px 0;
            line-height: 1.8;
        }
        
        .course-name {
            font-weight: 700;
            font-size: 60px;
            margin: 10px 0;
            color: #012d6a;
        }
        
        .details-row {
            display: flex;
            justify-content: space-between;
            margin: 15px 0;
            font-size: 20px;
            color: #000000ff;
        }
        
        .responsible-section {
            margin-top: 40px;
            font-size: 16px;
            color: #ffffffff;
        }
        
        .responsible-name {
            font-weight: 700;
            margin-top: 10px;
            border-top: 2px solid #ffffffff;
            padding-top: 5px;
            display: inline-block;
            min-width: 200px;
        }
        
        .footer-info {
            position: absolute;
            bottom: 15mm;
            display: flex;
            justify-content: space-between;
            font-size: 20px;
            color: #ffffffff;
            z-index: 10;
            margin-bottom: -60px;
            text-align:center;
        }
        
        .validation-code {
            font-family: "Courier New", monospace;
            font-weight: bold;
            color: #ffffffff;
            font-size: 20px;
            background-color: rgba(0, 0, 0, 0.9);
            padding: 5px;
            border-radius: 3px;
            margin-bottom: -70px;
            text-align:center;
        }
        
        /* Estilos para quando não há imagem de fundo */
        .no-background {
            border: 8px solid #012d6a;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        }
        
        .no-background::before {
            content: "";
            position: absolute;
            top: 15mm;
            left: 15mm;
            right: 15mm;
            bottom: 15mm;
            border: 2px solid #012d6a;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="certificate-container' . (empty($background_image) ? ' no-background' : '') . '">
        ' . (!empty($background_image) ? '<img src="' . $background_image . '" alt="Background" class="background-image">' : '') . '
        
        <div class="content">
            <h1 class="certificate-title">Certificado <br> de Participação</h1>
            
            <p class="certificate-text">
                Certificamos que
            </p>
            
            <div class="participant-name" style="font-size: ' . $this->calculateFontSize($participant_name) . 'px;">
                ' . htmlspecialchars($participant_name) . '
            </div>

            
            <p class="certificate-text">
                concluiu com sucesso o curso/evento
            </p>
            
            <div class="course-name">
                ' . htmlspecialchars($courseData['name']) . '
            </div>
            
            <div class="course-info">
                <div class="details-row">
                    <span><strong>Carga Horária:</strong> ' . htmlspecialchars($courseData['workload']) . '</span>
                    <span><strong>Data de Realização:</strong> ' . $course_date . '</span> <br/> Promovido pela: <strong>ANETI - Associação Nacional dos Especialistas em T.I</strong>
                </div>
            </div>
            
        </div>
        
        <div class="footer-info">
            <span>Data de Emissão: ' . $issue_date . '</span>
            <span class="validation-code">Código de Validação: ' . $unique_code . '</span>
        </div>
    </div>
</body>
</html>';

        return $html;
    }
    
    private function formatParticipantName($name) {
        // Converter para minúsculas e depois capitalizar apenas a primeira letra de cada palavra
        $name = mb_strtolower(trim($name), 'UTF-8');
        $words = explode(' ', $name);
        $formatted_words = [];
        
        foreach ($words as $word) {
            if (strlen($word) > 0) {
                $formatted_words[] = mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($word, 1, null, 'UTF-8');
            }
        }
        
        return implode(' ', $formatted_words);
    }

    private function calculateFontSize($name) {
    $length = mb_strlen($name, 'UTF-8');

    if ($length <= 25) return 130;
    if ($length <= 35) return 120;
    if ($length <= 45) return 110;
    if ($length <= 55) return 100;
    return 60; // acima de 55 caracteres
}

}
?>

