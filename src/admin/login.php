<?php
require_once '../config/auth.php';
require_once '../classes/User.php';

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (!empty($username) && !empty($password)) {
        $user = new User();
        if ($user->login($username, $password)) {
            login($user->id, $user->username);
            header('Location: dashboard.php');
            exit;
        } else {
            $error_message = 'Usuário ou senha inválidos.';
        }
    } else {
        $error_message = 'Por favor, preencha todos os campos.';
    }
}

// Se já estiver logado, redireciona para o dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Certificados</title>
    <link href="../../assets/aneti-style.css" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-certificate fa-3x mb-3"></i>
            <h3>Sistema de Certificados</h3>
            <p class="mb-0">Painel Administrativo</p>
        </div>
        <div class="login-body">
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user me-2"></i>Usuário
                    </label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Senha
                    </label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-login w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Entrar
                </button>
            </form>
            
            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Usuário padrão: admin / Senha: admin123
                </small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

