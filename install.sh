#!/bin/bash

# Script de Instalação do Sistema de Certificados
# Autor: Equipe Manus
# Versão: 1.0.0

echo "=========================================="
echo "  Sistema de Certificados - Instalação"
echo "=========================================="
echo ""

# Verificar se está rodando como root
if [ "$EUID" -ne 0 ]; then
    echo "❌ Este script deve ser executado como root (sudo)"
    exit 1
fi

# Detectar sistema operacional
if [ -f /etc/debian_version ]; then
    OS="debian"
    echo "✅ Sistema detectado: Debian/Ubuntu"
elif [ -f /etc/redhat-release ]; then
    OS="redhat"
    echo "✅ Sistema detectado: CentOS/RHEL"
else
    echo "❌ Sistema operacional não suportado"
    exit 1
fi

echo ""
echo "🔧 Instalando dependências..."

# Instalar dependências baseado no SO
if [ "$OS" = "debian" ]; then
    apt update
    apt install -y apache2 php php-mysql php-pdo php-mbstring php-xml php-curl mysql-server
    systemctl enable apache2
    systemctl start apache2
    systemctl enable mysql
    systemctl start mysql
elif [ "$OS" = "redhat" ]; then
    yum update -y
    yum install -y httpd php php-mysql php-pdo php-mbstring php-xml php-curl mysql-server
    systemctl enable httpd
    systemctl start httpd
    systemctl enable mysqld
    systemctl start mysqld
fi

echo "✅ Dependências instaladas com sucesso!"
echo ""

# Configurar diretório web
WEB_DIR="/var/www/html/certificados"
echo "📁 Configurando diretório web: $WEB_DIR"

# Criar diretório se não existir
mkdir -p $WEB_DIR

# Copiar arquivos (assumindo que o script está no diretório do projeto)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cp -r $SCRIPT_DIR/* $WEB_DIR/

# Configurar permissões
chown -R www-data:www-data $WEB_DIR
chmod -R 755 $WEB_DIR
chmod -R 777 $WEB_DIR/certificates
chmod -R 777 $WEB_DIR/uploads

echo "✅ Arquivos copiados e permissões configuradas!"
echo ""

# Configurar MySQL
echo "🗄️  Configurando MySQL..."
mysql -e "CREATE DATABASE IF NOT EXISTS certificados_db;"
mysql -e "CREATE USER IF NOT EXISTS 'certificados'@'localhost' IDENTIFIED BY 'certificados123';"
mysql -e "GRANT ALL PRIVILEGES ON certificados_db.* TO 'certificados'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

echo "✅ Banco de dados configurado!"
echo ""

# Criar arquivo de configuração
echo "⚙️  Criando arquivo de configuração..."
cat > $WEB_DIR/config.php << EOF
<?php
// Configurações do Sistema de Certificados

// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'certificados_db');
define('DB_USER', 'certificados');
define('DB_PASS', 'certificados123');

// Configurações do Sistema
define('SITE_NAME', 'Portal de Certificados');
define('SITE_URL', 'http://localhost/certificados');
define('ADMIN_EMAIL', 'admin@exemplo.com');

// Configurações de Segurança
define('SESSION_TIMEOUT', 7200);
define('MAX_LOGIN_ATTEMPTS', 5);

// Configurações de Upload
define('MAX_FILE_SIZE', 5 * 1024 * 1024);
define('ALLOWED_EXTENSIONS', ['csv', 'txt']);

// Configurações de Certificados
define('CERTIFICATES_DIR', __DIR__ . '/certificates/');
define('CERTIFICATE_TEMPLATE', 'default');

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Função para verificar se o sistema está instalado
function isSystemInstalled() {
    try {
        \$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        \$stmt = \$pdo->query("SHOW TABLES LIKE 'users'");
        return \$stmt->rowCount() > 0;
    } catch (PDOException \$e) {
        return false;
    }
}

// Função para obter URL base
function getBaseUrl() {
    \$protocol = isset(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    \$host = \$_SERVER['HTTP_HOST'];
    \$path = dirname(\$_SERVER['SCRIPT_NAME']);
    return \$protocol . '://' . \$host . \$path;
}

// Função para redirecionar se não estiver instalado
function requireInstallation() {
    if (!isSystemInstalled()) {
        \$current_page = basename(\$_SERVER['PHP_SELF']);
        if (\$current_page !== 'install.php') {
            header('Location: ' . getBaseUrl() . '/install.php');
            exit;
        }
    }
}
?>
EOF

echo "✅ Arquivo de configuração criado!"
echo ""

# Configurar Apache Virtual Host (opcional)
echo "🌐 Configurando Virtual Host do Apache..."
cat > /etc/apache2/sites-available/certificados.conf << EOF
<VirtualHost *:80>
    ServerName certificados.local
    DocumentRoot $WEB_DIR
    
    <Directory $WEB_DIR>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/certificados_error.log
    CustomLog \${APACHE_LOG_DIR}/certificados_access.log combined
</VirtualHost>
EOF

# Habilitar site e módulos
a2ensite certificados.conf
a2enmod rewrite
systemctl reload apache2

echo "✅ Virtual Host configurado!"
echo ""

# Configurar firewall (se UFW estiver instalado)
if command -v ufw &> /dev/null; then
    echo "🔥 Configurando firewall..."
    ufw allow 80/tcp
    ufw allow 443/tcp
    echo "✅ Firewall configurado!"
    echo ""
fi

# Finalização
echo "=========================================="
echo "  ✅ Instalação Concluída com Sucesso!"
echo "=========================================="
echo ""
echo "📋 Informações importantes:"
echo "   • URL do sistema: http://localhost/certificados"
echo "   • URL alternativa: http://certificados.local (adicione ao /etc/hosts se necessário)"
echo "   • Diretório: $WEB_DIR"
echo "   • Banco de dados: certificados_db"
echo "   • Usuário DB: certificados"
echo "   • Senha DB: certificados123"
echo ""
echo "🔐 Credenciais do administrador padrão:"
echo "   • Usuário: admin"
echo "   • Senha: admin123"
echo ""
echo "⚠️  IMPORTANTE:"
echo "   1. Acesse o sistema e complete a instalação via web"
echo "   2. Altere a senha do administrador após o primeiro login"
echo "   3. Configure SSL/HTTPS para produção"
echo "   4. Faça backup regular do banco de dados"
echo ""
echo "📖 Para mais informações, consulte o README.md"
echo ""

# Verificar se os serviços estão rodando
echo "🔍 Verificando serviços..."
if systemctl is-active --quiet apache2; then
    echo "✅ Apache está rodando"
else
    echo "❌ Apache não está rodando"
fi

if systemctl is-active --quiet mysql; then
    echo "✅ MySQL está rodando"
else
    echo "❌ MySQL não está rodando"
fi

echo ""
echo "🎉 Instalação finalizada! Acesse o sistema no navegador."

