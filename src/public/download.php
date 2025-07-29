<?php
require_once '../classes/Certificate.php';

$certificate = new Certificate();
$code = $_GET['code'] ?? '';

if (empty($code)) {
    header('Location: index.php');
    exit;
}

$cert_data = $certificate->getByCode($code);

if (!$cert_data) {
    header('Location: validate.php?code=' . urlencode($code));
    exit;
}

// Caminho do arquivo
$file_path = '../../' . $cert_data['file_path'];

// Verifica se o arquivo existe
if (!file_exists($file_path)) {
    // Se não existe PDF, gera HTML temporário
    $html_path = str_replace('.pdf', '.html', $file_path);
    if (file_exists($html_path)) {
        $file_path = $html_path;
        $filename = 'certificado_' . $cert_data['unique_code'] . '.html';
        $content_type = 'text/html';
    } else {
        // Gera certificado HTML na hora
        $html_content = generateCertificateHTML($cert_data);
        
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="certificado_' . $cert_data['unique_code'] . '.html"');
        echo $html_content;
        exit;
    }
} else {
    $filename = 'certificado_' . $cert_data['unique_code'] . '.pdf';
    $content_type = 'application/pdf';
}

// Download do arquivo
header('Content-Type: ' . $content_type);
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($file_path));
readfile($file_path);
exit;

function generateCertificateHTML($cert_data) {
    $course_date = date('d/m/Y', strtotime($cert_data['course_date']));
    $issue_date = date('d/m/Y');
    
    return '
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Certificado - ' . htmlspecialchars($cert_data['participant_name']) . '</title>
        <style>
            @page {
                size: A4 landscape;
                margin: 20mm;
            }
            body {
                font-family: "Times New Roman", serif;
                text-align: center;
                margin: 0;
                padding: 40px;
                background: #fff;
                color: #333;
            }
            .certificate {
                border: 8px solid #667eea;
                padding: 60px 40px;
                margin: 20px auto;
                max-width: 800px;
                min-height: 500px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            }
            .title {
                font-size: 48px;
                font-weight: bold;
                margin-bottom: 30px;
                color: #667eea;
                text-transform: uppercase;
                letter-spacing: 3px;
            }
            .subtitle {
                font-size: 24px;
                margin-bottom: 20px;
                color: #333;
            }
            .content {
                font-size: 20px;
                line-height: 1.8;
                margin: 30px 0;
                color: #333;
            }
            .participant {
                font-size: 32px;
                font-weight: bold;
                margin: 30px 0;
                text-decoration: underline;
                color: #667eea;
            }
            .course {
                font-size: 26px;
                font-weight: bold;
                margin: 25px 0;
                color: #333;
                font-style: italic;
            }
            .footer {
                margin-top: 50px;
                font-size: 16px;
                color: #666;
            }
            .signature-line {
                border-top: 2px solid #333;
                width: 300px;
                margin: 40px auto 10px;
            }
            .code {
                position: absolute;
                bottom: 20px;
                right: 20px;
                font-size: 12px;
                color: #999;
                font-family: monospace;
            }
            .validation-info {
                margin-top: 30px;
                font-size: 14px;
                color: #666;
                border-top: 1px solid #ddd;
                padding-top: 20px;
            }
            @media print {
                body { margin: 0; padding: 0; }
                .certificate { margin: 0; border: 8px solid #667eea; }
            }
        </style>
    </head>
    <body>
        <div class="certificate">
            <div class="title">Certificado</div>
            <div class="subtitle">Certificamos que</div>
            <div class="participant">' . htmlspecialchars($cert_data['participant_name']) . '</div>
            <div class="content">
                participou do curso/evento<br>
                <div class="course">' . htmlspecialchars($cert_data['course_name']) . '</div>
                com carga horária de ' . htmlspecialchars($cert_data['workload']) . ',<br>
                realizado em ' . $course_date . '.
            </div>
            <div class="footer">
                <div class="signature-line"></div>
                <p><strong>' . htmlspecialchars($cert_data['responsible']) . '</strong></p>
                <p>Responsável</p>
                <p style="margin-top: 30px;">Data de emissão: ' . $issue_date . '</p>
            </div>
            <div class="validation-info">
                <p><strong>Código de Validação:</strong> ' . htmlspecialchars($cert_data['unique_code']) . '</p>
                <p>Para validar este certificado, acesse: [URL_DO_PORTAL]/validate.php</p>
            </div>
        </div>
        <div class="code">Código: ' . htmlspecialchars($cert_data['unique_code']) . '</div>
    </body>
    </html>';
}
?>

