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
    <link href="../assets/aneti-style.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #e0f2f7 0%, #cce7f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--aneti-navy);
        }
        .login-container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .login-header {
            background: var(--aneti-blue);
            color: white;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.05);
            transform: rotate(45deg);
            z-index: 0;
        }
        .login-header h3, .login-header p, .login-header i {
            position: relative;
            z-index: 1;
        }
        .login-body {
            padding: 2rem;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px 12px;
            transition: all 0.2s ease-in-out;
            font-size: 0.95rem;
        }
        .form-control:focus {
            border-color: var(--aneti-light-blue);
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }
        .btn-login {
            background: linear-gradient(45deg, var(--aneti-blue), var(--aneti-light-blue));
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(30, 58, 138, 0.3);
        }
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px;
            font-size: 0.95rem;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        .form-label {
            font-weight: 600;
            color: var(--aneti-navy);
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        .text-muted {
            color: var(--aneti-gray) !important;
        }
    </style>
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

