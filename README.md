# Sistema de Certificados ANETI v2.0

Sistema completo de emissão, consulta e validação de certificados desenvolvido especialmente para a **ANETI (Associação Nacional de Especialistas em TI)**.

## 🚀 Funcionalidades Principais

### 🔐 Painel Administrativo
- **Login Seguro:** Sistema de autenticação protegido
- **Dashboard Intuitivo:** Estatísticas em tempo real com identidade visual ANETI
- **Gestão de Cursos:** Cadastro completo com associação de modelos personalizados
- **Gestão de Participantes:** Controle total de inscritos
- **Lista de Presença:** Upload CSV ou marcação manual + formulário público
- **Emissão de Certificados:** Individual ou em lote (apenas para presentes)
- **Gestão de Modelos:** Upload e configuração de templates personalizados
- **Consulta Avançada:** Filtros por curso, participante, e-mail, data
- **Exportação:** Dados em CSV para relatórios

### 🌐 Portal Público
- **Validação por Código:** Verificação instantânea de autenticidade
- **Consulta por E-mail:** Todos os certificados de um participante
- **Download Direto:** PDF dos certificados validados
- **Lista de Presença:** Formulário público para confirmação de participação
- **Design Responsivo:** Funciona perfeitamente em todos os dispositivos

### 🎨 Identidade Visual ANETI
- **Cores Oficiais:** Azul institucional (#1e3a8a) e paleta completa
- **Tipografia:** Inter (moderna e profissional)
- **Logotipo:** Integrado em locais estratégicos
- **Componentes:** Cards, botões e formulários com identidade ANETI

## 💻 Tecnologias Utilizadas

- **Backend:** PHP 8.1+ puro (sem frameworks)
- **Banco de Dados:** MySQL 8.0+ com PDO
- **Frontend:** HTML5, CSS3, JavaScript vanilla
- **Framework CSS:** Bootstrap 5.1.3 + CSS customizado ANETI
- **Ícones:** Font Awesome 6.0
- **Tipografia:** Google Fonts (Inter)

## 📋 Requisitos do Sistema

### Servidor
- **PHP:** 8.1 ou superior
- **MySQL:** 8.0 ou superior (ou MariaDB equivalente)
- **Servidor Web:** Apache/Nginx (ou PHP built-in para desenvolvimento)

### Extensões PHP Necessárias
- PDO
- PDO_MySQL
- mbstring
- xml
- curl
- gd (para manipulação de imagens)
- fileinfo (para upload de arquivos)

## 🚀 Instalação

### Método 1: Instalação Automática (Recomendado)

1. **Faça o download do sistema:**
```bash
# Extraia o arquivo ZIP no diretório do servidor web
unzip certificados_app_v2.0.zip -d /var/www/html/
```

2. **Configure as permissões:**
```bash
chmod -R 755 /var/www/html/certificados_app/
chmod -R 777 /var/www/html/certificados_app/certificates/
chmod -R 777 /var/www/html/certificados_app/templates/
```

3. **Acesse o instalador web:**
```
http://seudominio.com/certificados_app/install.php
```

4. **Siga as instruções do instalador:**
- Configure a conexão com o banco de dados
- Crie o usuário administrador
- O sistema será instalado automaticamente

### Método 2: Instalação Manual

1. **Configure o banco de dados:**
```sql
CREATE DATABASE certificados_aneti;
USE certificados_aneti;
SOURCE src/database/schema.sql;
```

2. **Configure o arquivo de configuração:**
```bash
cp config.example.php config.php
# Edite config.php com suas configurações
```

3. **Configure as permissões das pastas:**
```bash
chmod 777 certificates/
chmod 777 templates/
```

## ⚙️ Configuração

### Arquivo de Configuração (config.php)

```php
<?php
// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'certificados_aneti');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');

// Configurações do Sistema
define('SITE_URL', 'https://seudominio.com/certificados_app');
define('SITE_NAME', 'Sistema de Certificados ANETI');

// Configurações de Upload
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf']);
?>
```

### Configuração do Servidor Web

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Segurança
<Files "config.php">
    Order allow,deny
    Deny from all
</Files>
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    fastcgi_index index.php;
    include fastcgi_params;
}
```

## 🏗️ Estrutura do Projeto

```
certificados_app/
├── 📁 src/                      # Código fonte principal
│   ├── 📁 admin/               # Painel administrativo
│   │   ├── dashboard.php       # Dashboard principal
│   │   ├── courses.php         # Gestão de cursos
│   │   ├── participants.php    # Gestão de participantes
│   │   ├── presences.php       # Lista de presença
│   │   ├── certificates.php    # Emissão de certificados
│   │   ├── templates.php       # Gestão de modelos
│   │   └── login.php          # Sistema de login
│   ├── 📁 public/              # Portal público
│   │   ├── index.php          # Página inicial
│   │   ├── validate.php       # Validação de certificados
│   │   ├── search.php         # Busca por e-mail
│   │   ├── presence.php       # Lista de presença pública
│   │   └── download.php       # Download de certificados
│   ├── 📁 classes/             # Classes PHP
│   │   ├── User.php           # Gestão de usuários
│   │   ├── Course.php         # Gestão de cursos
│   │   ├── Participant.php    # Gestão de participantes
│   │   ├── Presence.php       # Controle de presença
│   │   ├── Certificate.php    # Emissão de certificados
│   │   └── Template.php       # Gestão de modelos
│   ├── 📁 config/              # Configurações
│   │   ├── database.php       # Conexão com banco
│   │   └── auth.php          # Sistema de autenticação
│   ├── 📁 assets/              # Recursos estáticos
│   │   ├── aneti-style.css    # CSS da identidade ANETI
│   │   ├── logo-branca.png    # Logo ANETI (branca)
│   │   └── logo-azul.png      # Logo ANETI (azul)
│   └── 📁 database/            # Scripts de banco
│       └── schema.sql         # Estrutura do banco
├── 📁 certificates/            # Certificados gerados
├── 📁 templates/               # Modelos de certificado
├── 📁 assets/                  # Recursos públicos
├── 📄 config.php              # Configuração principal
├── 📄 install.php             # Instalador automático
├── 📄 README.md               # Este arquivo
└── 📄 README_ATUALIZACAO.md   # Documentação das melhorias
```

## 👤 Credenciais Padrão

Após a instalação, use as credenciais padrão para acessar o painel administrativo:

- **URL:** `http://seudominio.com/certificados_app/src/admin/login.php`
- **Usuário:** `admin`
- **Senha:** `admin123`

> ⚠️ **IMPORTANTE:** Altere a senha padrão imediatamente após o primeiro login!

## 📖 Como Usar

### 1. Configuração Inicial

1. **Acesse o painel administrativo**
2. **Crie um novo curso:**
   - Vá em "Cursos/Eventos"
   - Preencha as informações (nome, carga horária, data, responsável)
   - Selecione um modelo de certificado (opcional)

3. **Configure modelos de certificado:**
   - Vá em "Modelos"
   - Faça upload de um template (imagem ou PDF)
   - Configure as posições dos campos dinâmicos

### 2. Gestão de Participantes

**Opção 1: Cadastro Manual**
- Vá em "Participantes"
- Clique em "Novo Participante"
- Preencha nome e e-mail

**Opção 2: Upload CSV**
- Prepare um arquivo CSV com colunas: nome, email
- Vá em "Lista de Presença"
- Faça upload do arquivo CSV

**Opção 3: Lista Pública**
- Cada curso gera um link único para presença
- Compartilhe o link: `seudominio.com/src/public/presence.php?id=123`
- Participantes preenchem seus próprios dados

### 3. Controle de Presença

- Acesse "Lista de Presença"
- Marque manualmente os participantes presentes
- Ou importe lista de presença via CSV
- Apenas participantes presentes podem receber certificados

### 4. Emissão de Certificados

**Individual:**
1. Vá em "Certificados"
2. Clique em "Gerar Certificado Individual"
3. Selecione o curso
4. Escolha o participante presente
5. Clique em "Gerar Certificado"

**Em Lote:**
1. Clique em "Gerar Certificados em Lote"
2. Selecione o curso
3. Todos os participantes presentes receberão certificados

### 5. Validação Pública

- Participantes podem validar certificados em:
  `seudominio.com/src/public/validate.php`
- Inserir código único ou buscar por e-mail
- Download direto do certificado

## 🔧 Personalização

### Modelos de Certificado

1. **Crie seu template:**
   - Use qualquer editor gráfico (Photoshop, Canva, etc.)
   - Deixe espaços em branco para os campos dinâmicos
   - Salve como PNG, JPG ou PDF

2. **Configure no sistema:**
   - Faça upload em "Modelos"
   - Defina as posições dos campos:
     - Nome do participante
     - Nome do curso
     - Carga horária
     - Data
     - Responsável
     - Código de validação

3. **Associe ao curso:**
   - Edite o curso desejado
   - Selecione o modelo na lista

### Identidade Visual

O sistema já vem configurado com a identidade visual da ANETI, mas você pode personalizar:

1. **Cores:** Edite `src/assets/aneti-style.css`
2. **Logotipo:** Substitua os arquivos em `assets/`
3. **Fontes:** Altere as importações do Google Fonts

## 🛡️ Segurança

### Medidas Implementadas

- **Autenticação:** Sistema de login com sessões seguras
- **Validação:** Sanitização de todos os dados de entrada
- **SQL Injection:** Uso de prepared statements (PDO)
- **XSS:** Escape de dados na saída (htmlspecialchars)
- **Upload:** Validação de tipos e tamanhos de arquivo
- **Acesso:** Controle de permissões por pasta

### Recomendações Adicionais

1. **SSL/HTTPS:** Use sempre certificado SSL em produção
2. **Backup:** Configure backups automáticos regulares
3. **Firewall:** Configure firewall do servidor
4. **Atualizações:** Mantenha PHP e MySQL atualizados
5. **Monitoramento:** Configure logs de erro e acesso

## 📊 Performance

### Otimizações Implementadas

- **AJAX:** Carregamento assíncrono de dados
- **Cache:** Cache de consultas frequentes
- **Compressão:** CSS e JS minificados
- **Imagens:** Otimização automática de uploads
- **Consultas:** Índices otimizados no banco

### Monitoramento

- **Logs:** Disponíveis em `/logs/`
- **Métricas:** Dashboard com estatísticas
- **Alertas:** Notificações de erro por e-mail (configurável)

## 🆘 Solução de Problemas

### Problemas Comuns

**1. Erro de conexão com banco de dados**
```
Solução: Verifique as credenciais em config.php
```

**2. Página em branco**
```
Solução: Ative display_errors no PHP e verifique os logs
```

**3. Upload de arquivo falha**
```
Solução: Verifique permissões das pastas e tamanho máximo
```

**4. Certificados não são gerados**
```
Solução: Verifique se o participante está marcado como presente
```

**5. Modal não carrega participantes**
```
Solução: Verifique se JavaScript está habilitado
```

### Logs do Sistema

```bash
# Logs de erro do PHP
tail -f /var/log/php/error.log

# Logs do servidor web
tail -f /var/log/apache2/error.log  # Apache
tail -f /var/log/nginx/error.log    # Nginx

# Logs do sistema (se configurado)
tail -f certificados_app/logs/system.log
```

## 🔄 Backup e Restauração

### Backup Automático

```bash
#!/bin/bash
# Script de backup diário

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/certificados"

# Backup do banco de dados
mysqldump -u usuario -p certificados_aneti > $BACKUP_DIR/db_$DATE.sql

# Backup dos arquivos
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/html/certificados_app/

# Manter apenas últimos 30 dias
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
```

### Restauração

```bash
# Restaurar banco de dados
mysql -u usuario -p certificados_aneti < backup_db.sql

# Restaurar arquivos
tar -xzf backup_files.tar.gz -C /var/www/html/
```

## 📈 Atualizações

### Histórico de Versões

**v2.0.0 (29/07/2025)**
- ✅ Correção na emissão de certificados individuais
- ✅ Sistema de gerenciamento de modelos
- ✅ Lista de presença pública
- ✅ Identidade visual ANETI completa
- ✅ Melhorias de performance e segurança

**v1.0.0**
- Sistema básico de certificados
- Painel administrativo
- Portal público de validação

### Como Atualizar

1. **Faça backup completo do sistema atual**
2. **Baixe a nova versão**
3. **Execute o script de atualização:**
```bash
php update.php
```
4. **Teste todas as funcionalidades**

## 🤝 Suporte

### Documentação
- **README Principal:** Este arquivo
- **Atualizações:** README_ATUALIZACAO.md
- **Changelog:** CHANGELOG.md

### Contato
- **Desenvolvedor:** Manus AI
- **Cliente:** ANETI - Associação Nacional de Especialistas em TI
- **Data:** 29/07/2025

### Recursos Adicionais
- Manual de identidade visual ANETI
- Templates de exemplo
- Scripts de automação
- Documentação da API (futura)

---

## 📄 Licença

Este sistema foi desenvolvido exclusivamente para a ANETI (Associação Nacional de Especialistas em TI). Todos os direitos reservados.

---

**Sistema de Certificados ANETI v2.0**  
*Desenvolvido com ❤️ pela equipe Manus AI*

