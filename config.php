<?php
// Configurações do Sistema de Certificados

// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'certificados_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configurações do Sistema
define('SITE_NAME', 'Portal de Certificados');
define('SITE_URL', 'http://localhost/certificados_app');
define('ADMIN_EMAIL', 'admin@exemplo.com');

// Configurações de Segurança
define('SESSION_TIMEOUT', 7200); // 2 horas em segundos
define('MAX_LOGIN_ATTEMPTS', 5);

// Configurações de Upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['csv', 'txt']);

// Configurações de Certificados
define('CERTIFICATES_DIR', __DIR__ . '/certificates/');
define('CERTIFICATE_TEMPLATE', 'default');

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
?>

