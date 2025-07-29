# Sistema de Certificados

Sistema completo para emissão, consulta e validação de certificados digitais com painel administrativo e portal público de validação.

## 🎯 Funcionalidades

### 🔐 Painel Administrativo
- **Login protegido** com controle de acesso
- **Cadastro de cursos/eventos** (nome, carga horária, data, responsável, descrição)
- **Cadastro de participantes** com nome e e-mail
- **Controle de lista de presença** (upload CSV ou marcação manual)
- **Emissão de certificados** somente para participantes presentes
- **Consulta e gerenciamento** de certificados emitidos
- **Filtros avançados** por curso, participante, e-mail
- **Visualização e download** de certificados
- **Exportação de dados** em CSV
- **Códigos únicos** e links de validação para cada certificado

### 🌐 Portal Público de Validação
- **Validação por código** único do certificado
- **Consulta por e-mail** para ver todos os certificados de um participante
- **Visualização completa** dos dados do certificado
- **Download do certificado** em formato PDF/HTML
- **Interface responsiva** e moderna

### 📝 Controle de Presença
- **Marcação manual** no painel administrativo
- **Upload de arquivo CSV** com lista de presença
- **Formulário público** para "assinar presença" (opcional)
- **Validação obrigatória** antes da emissão do certificado

## 💻 Tecnologias Utilizadas

- **Backend:** PHP 8.1+ (sem frameworks)
- **Banco de Dados:** MySQL 8.0+
- **Frontend:** HTML5, CSS3, JavaScript (Vanilla)
- **Framework CSS:** Bootstrap 5.1.3
- **Ícones:** Font Awesome 6.0
- **Design:** Layout responsivo e moderno

## 📋 Requisitos do Sistema

- PHP 8.1 ou superior
- MySQL 8.0 ou superior (ou MariaDB equivalente)
- Extensões PHP: PDO, PDO_MySQL, mbstring, xml, curl
- Servidor web (Apache/Nginx) ou PHP built-in server para desenvolvimento

## 🚀 Instalação

### 1. Download e Extração
```bash
# Extrair os arquivos para o diretório do servidor web
unzip certificados_app.zip -d /var/www/html/
cd /var/www/html/certificados_app/
```

### 2. Configuração do Banco de Dados
Edite o arquivo `config.php` com suas configurações:

```php
// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'certificados_db');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
```

### 3. Instalação Automática
1. Acesse `http://seu-dominio.com/certificados_app/` no navegador
2. O sistema redirecionará automaticamente para a instalação
3. Clique em "Instalar Sistema" para criar o banco de dados e tabelas
4. Aguarde a confirmação de instalação bem-sucedida

### 4. Primeiro Acesso
**Credenciais do administrador padrão:**
- **Usuário:** admin
- **Senha:** admin123

⚠️ **IMPORTANTE:** Altere a senha padrão após o primeiro login!

## 📁 Estrutura do Projeto

```
certificados_app/
├── config.php                 # Configurações principais
├── install.php                # Instalador automático
├── index.php                  # Redirecionamento inicial
├── .htaccess                   # Configurações Apache
├── README.md                   # Esta documentação
├── src/
│   ├── admin/                  # Painel administrativo
│   │   ├── login.php          # Página de login
│   │   ├── dashboard.php      # Dashboard principal
│   │   ├── courses.php        # Gerenciamento de cursos
│   │   ├── participants.php   # Gerenciamento de participantes
│   │   ├── presences.php      # Controle de presença
│   │   ├── certificates.php   # Gerenciamento de certificados
│   │   └── logout.php         # Logout
│   ├── public/                 # Portal público
│   │   ├── index.php          # Página inicial
│   │   ├── validate.php       # Validação por código
│   │   ├── search.php         # Busca por e-mail
│   │   └── download.php       # Download de certificados
│   ├── classes/                # Classes PHP
│   │   ├── User.php           # Gerenciamento de usuários
│   │   ├── Course.php         # Gerenciamento de cursos
│   │   ├── Participant.php    # Gerenciamento de participantes
│   │   ├── Presence.php       # Controle de presença
│   │   └── Certificate.php    # Gerenciamento de certificados
│   ├── config/                 # Configurações
│   │   ├── database.php       # Configuração do banco
│   │   └── auth.php           # Sistema de autenticação
│   └── database/
│       └── schema.sql          # Esquema do banco de dados
├── certificates/               # Diretório para certificados gerados
└── uploads/                    # Diretório para uploads (CSV, etc.)
```

## 🔧 Configuração Avançada

### Configurações de Segurança
No arquivo `config.php`:

```php
// Timeout da sessão (em segundos)
define('SESSION_TIMEOUT', 7200); // 2 horas

// Máximo de tentativas de login
define('MAX_LOGIN_ATTEMPTS', 5);
```

### Configurações de Upload
```php
// Tamanho máximo de arquivo (em bytes)
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Extensões permitidas para upload
define('ALLOWED_EXTENSIONS', ['csv', 'txt']);
```

### Configurações de Certificados
```php
// Diretório para salvar certificados
define('CERTIFICATES_DIR', __DIR__ . '/certificates/');

// Template padrão para certificados
define('CERTIFICATE_TEMPLATE', 'default');
```

## 📖 Manual de Uso

### Para Administradores

#### 1. Acesso ao Painel
1. Acesse `http://seu-dominio.com/certificados_app/src/admin/login.php`
2. Faça login com suas credenciais
3. Você será redirecionado para o dashboard

#### 2. Cadastro de Cursos
1. No menu lateral, clique em "Cursos/Eventos"
2. Clique em "Novo Curso"
3. Preencha os dados: nome, carga horária, data, responsável, descrição
4. Clique em "Salvar"

#### 3. Cadastro de Participantes
1. No menu lateral, clique em "Participantes"
2. Clique em "Novo Participante"
3. Preencha nome e e-mail
4. Clique em "Salvar"

#### 4. Controle de Presença
**Opção 1 - Marcação Manual:**
1. Vá em "Lista de Presença"
2. Selecione o curso
3. Marque os participantes presentes
4. Clique em "Salvar Presenças"

**Opção 2 - Upload CSV:**
1. Prepare um arquivo CSV com colunas: nome, email, presente (1 ou 0)
2. Vá em "Lista de Presença"
3. Clique em "Upload CSV"
4. Selecione o arquivo e faça upload

#### 5. Emissão de Certificados
1. Vá em "Certificados"
2. Clique em "Gerar Certificados"
3. Selecione o curso
4. O sistema gerará certificados apenas para participantes presentes
5. Cada certificado receberá um código único

#### 6. Consulta e Gerenciamento
- **Filtros:** Use os filtros por curso, participante ou e-mail
- **Visualização:** Clique em "Ver" para visualizar um certificado
- **Download:** Clique em "Baixar" para fazer download
- **Exportação:** Use "Exportar CSV" para exportar dados

### Para o Público

#### 1. Validação por Código
1. Acesse `http://seu-dominio.com/certificados_app/src/public/`
2. Digite o código do certificado no campo "Validar Certificado"
3. Clique em "Validar Certificado"
4. Visualize os dados completos e faça download se necessário

#### 2. Consulta por E-mail
1. Na página inicial, clique em "Consultar por E-mail"
2. Digite seu e-mail
3. Clique em "Buscar Certificados"
4. Visualize todos os certificados emitidos para seu e-mail

## 🔒 Segurança

### Medidas Implementadas
- **Autenticação obrigatória** para o painel administrativo
- **Controle de sessão** com timeout configurável
- **Proteção contra SQL Injection** usando PDO prepared statements
- **Validação de entrada** em todos os formulários
- **Códigos únicos** para cada certificado
- **Headers de segurança** configurados no .htaccess
- **Proteção de arquivos sensíveis** (SQL, logs)

### Recomendações Adicionais
1. **Altere a senha padrão** imediatamente após a instalação
2. **Use HTTPS** em produção
3. **Configure backups regulares** do banco de dados
4. **Mantenha o PHP e MySQL atualizados**
5. **Configure permissões adequadas** nos diretórios
6. **Monitore logs de acesso** regularmente

## 🐛 Solução de Problemas

### Erro de Conexão com Banco
1. Verifique as configurações em `config.php`
2. Confirme se o MySQL está rodando
3. Verifique se o usuário tem permissões adequadas

### Erro de Permissões
```bash
# Configurar permissões adequadas
chmod 755 certificados_app/
chmod 777 certificados_app/certificates/
chmod 777 certificados_app/uploads/
```

### Erro de Extensões PHP
```bash
# Ubuntu/Debian
sudo apt install php-mysql php-pdo php-mbstring php-xml php-curl

# CentOS/RHEL
sudo yum install php-mysql php-pdo php-mbstring php-xml php-curl
```

### Problemas de Upload
1. Verifique o tamanho máximo em `config.php`
2. Confirme as permissões do diretório `uploads/`
3. Verifique as configurações do PHP (`upload_max_filesize`, `post_max_size`)

## 📞 Suporte

Para suporte técnico ou dúvidas sobre o sistema:

1. **Documentação:** Consulte este README
2. **Logs:** Verifique os logs do servidor web e PHP
3. **Configuração:** Revise o arquivo `config.php`
4. **Banco de Dados:** Verifique a conectividade e permissões

## 📄 Licença

Este sistema foi desenvolvido especificamente para uso interno. Todos os direitos reservados.

---

**Desenvolvido com ❤️ para facilitar a gestão de certificados digitais**

