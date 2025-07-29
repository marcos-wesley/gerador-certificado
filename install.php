<?php
require_once 'config.php';
require_once 'src/config/database.php';

// Se já estiver instalado, redireciona
if (isSystemInstalled()) {
    header('Location: src/public/index.php');
    exit;
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    
    // Cria o banco de dados
    if ($database->createDatabase()) {
        // Cria as tabelas
        if ($database->createTables()) {
            $message = 'Sistema instalado com sucesso!';
            $message_type = 'success';
        } else {
            $message = 'Erro ao criar tabelas!';
            $message_type = 'danger';
        }
    } else {
        $message = 'Erro ao criar banco de dados!';
        $message_type = 'danger';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .install-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 600px;
            width: 100%;
        }
        .install-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .install-body {
            padding: 2rem;
        }
        .btn-install {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="install-container">
        <div class="install-header">
            <i class="fas fa-cog fa-3x mb-3"></i>
            <h3><?php echo SITE_NAME; ?></h3>
            <p class="mb-0">Instalação do Sistema</p>
        </div>
        <div class="install-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($message_type !== 'success'): ?>
                <div class="mb-4">
                    <h5>Requisitos do Sistema</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            PHP 7.4+
                            <span class="badge bg-<?php echo version_compare(PHP_VERSION, '7.4.0', '>=') ? 'success' : 'danger'; ?> rounded-pill">
                                <?php echo PHP_VERSION; ?>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            MySQL/MariaDB
                            <span class="badge bg-<?php echo extension_loaded('pdo_mysql') ? 'success' : 'danger'; ?> rounded-pill">
                                <?php echo extension_loaded('pdo_mysql') ? 'OK' : 'Não disponível'; ?>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            PDO Extension
                            <span class="badge bg-<?php echo extension_loaded('pdo') ? 'success' : 'danger'; ?> rounded-pill">
                                <?php echo extension_loaded('pdo') ? 'OK' : 'Não disponível'; ?>
                            </span>
                        </li>
                    </ul>
                </div>
                
                <div class="mb-4">
                    <h5>Configurações do Banco de Dados</h5>
                    <div class="alert alert-info">
                        <small>
                            <strong>Host:</strong> <?php echo DB_HOST; ?><br>
                            <strong>Banco:</strong> <?php echo DB_NAME; ?><br>
                            <strong>Usuário:</strong> <?php echo DB_USER; ?>
                        </small>
                    </div>
                    <p class="text-muted">
                        <small>Para alterar essas configurações, edite o arquivo <code>config.php</code></small>
                    </p>
                </div>
                
                <form method="POST">
                    <button type="submit" class="btn btn-primary btn-install w-100">
                        <i class="fas fa-download me-2"></i>Instalar Sistema
                    </button>
                </form>
            <?php else: ?>
                <div class="text-center">
                    <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
                    <h4>Instalação Concluída!</h4>
                    
                    <div class="alert alert-success">
                        <h6>Credenciais do Administrador:</h6>
                        <p class="mb-1"><strong>Usuário:</strong> admin</p>
                        <p class="mb-0"><strong>Senha:</strong> admin123</p>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="src/admin/login.php" class="btn btn-primary">
                            <i class="fas fa-user-shield me-2"></i>Acessar Painel Administrativo
                        </a>
                        <a href="src/public/index.php" class="btn btn-outline-primary">
                            <i class="fas fa-globe me-2"></i>Acessar Portal Público
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

