<?php
// Configurações do Sistema de Certificados - EXEMPLO
// Copie este arquivo para config.php e ajuste as configurações conforme necessário

// Configurações do Banco de Dados
define('DB_HOST', 'localhost');                    // Host do banco de dados
define('DB_NAME', 'certificados_db');              // Nome do banco de dados
define('DB_USER', 'root');                         // Usuário do banco de dados
define('DB_PASS', '');                             // Senha do banco de dados

// Configurações do Sistema
define('SITE_NAME', 'Portal de Certificados');     // Nome do sistema
define('SITE_URL', 'http://localhost/certificados_app'); // URL base do sistema
define('ADMIN_EMAIL', 'admin@exemplo.com');        // E-mail do administrador

// Configurações de Segurança
define('SESSION_TIMEOUT', 7200);                   // Timeout da sessão em segundos (2 horas)
define('MAX_LOGIN_ATTEMPTS', 5);                   // Máximo de tentativas de login

// Configurações de Upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024);         // Tamanho máximo de arquivo (5MB)
define('ALLOWED_EXTENSIONS', ['csv', 'txt']);      // Extensões permitidas para upload

// Configurações de Certificados
define('CERTIFICATES_DIR', __DIR__ . '/certificates/'); // Diretório para certificados
define('CERTIFICATE_TEMPLATE', 'default');         // Template padrão para certificados

// Configurações de E-mail (para futuras implementações)
define('SMTP_HOST', 'smtp.gmail.com');            // Host SMTP
define('SMTP_PORT', 587);                         // Porta SMTP
define('SMTP_USER', 'seu-email@gmail.com');       // Usuário SMTP
define('SMTP_PASS', 'sua-senha');                 // Senha SMTP
define('SMTP_SECURE', 'tls');                     // Segurança SMTP (tls/ssl)

// Configurações de Desenvolvimento
define('DEBUG_MODE', false);                       // Modo debug (true/false)
define('LOG_ERRORS', true);                        // Log de erros (true/false)
define('LOG_FILE', __DIR__ . '/logs/system.log'); // Arquivo de log

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Função para verificar se o sistema está instalado
function isSystemInstalled() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

// Função para obter URL base
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['SCRIPT_NAME']);
    return $protocol . '://' . $host . $path;
}

// Função para redirecionar se não estiver instalado
function requireInstallation() {
    if (!isSystemInstalled()) {
        $current_page = basename($_SERVER['PHP_SELF']);
        if ($current_page !== 'install.php') {
            header('Location: ' . getBaseUrl() . '/install.php');
            exit;
        }
    }
}

// Função para log de erros (se habilitado)
function logError($message, $file = '', $line = '') {
    if (LOG_ERRORS) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] ERROR: $message";
        if ($file) $logMessage .= " in $file";
        if ($line) $logMessage .= " on line $line";
        $logMessage .= PHP_EOL;
        
        // Criar diretório de logs se não existir
        $logDir = dirname(LOG_FILE);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents(LOG_FILE, $logMessage, FILE_APPEND | LOCK_EX);
    }
}

// Função para debug (se habilitado)
function debugLog($message) {
    if (DEBUG_MODE) {
        error_log("[DEBUG] " . $message);
    }
}
?>

