<?php
require_once '../classes/Certificate.php';

$certificate = new Certificate();
$certificates = [];
$email = $_GET['email'] ?? '';

if (!empty($email)) {
    $certificates = $certificate->getByEmail($email);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta por E-mail - Portal de Certificados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-custom {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: white !important;
        }
        .search-container {
            padding: 100px 0 50px;
        }
        .search-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-bottom: 30px;
        }
        .certificate-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }
        .certificate-card:hover {
            transform: translateY(-5px);
        }
        .certificate-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .search-input {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
        }
        .search-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-search {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
        }
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        .certificate-code {
            background: #f8f9fa;
            padding: 8px 12px;
            border-radius: 5px;
            font-family: monospace;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-certificate me-2"></i>
                Portal de Certificados
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="validate.php">Validar Certificado</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../admin/login.php">
                            <i class="fas fa-user-shield me-1"></i>Área Administrativa
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="search-container">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <!-- Search Form -->
                    <div class="search-card">
                        <div class="text-center mb-4">
                            <i class="fas fa-envelope fa-3x text-primary mb-3"></i>
                            <h3>Consultar Certificados por E-mail</h3>
                            <p class="text-muted">Digite seu e-mail para ver todos os certificados emitidos</p>
                        </div>
                        
                        <form method="GET">
                            <div class="row align-items-end">
                                <div class="col-md-8 mb-3">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input type="email" class="form-control search-input" id="email" name="email" 
                                           placeholder="seu@email.com" value="<?php echo htmlspecialchars($email); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <button type="submit" class="btn btn-primary btn-search w-100">
                                        <i class="fas fa-search me-2"></i>Buscar
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <a href="index.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Voltar ao Início
                            </a>
                        </div>
                    </div>
                    
                    <?php if (!empty($email)): ?>
                        <?php if (!empty($certificates)): ?>
                            <!-- Results -->
                            <div class="search-card">
                                <div class="certificate-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-certificate me-2"></i>
                                        Certificados encontrados para: <?php echo htmlspecialchars($email); ?>
                                    </h5>
                                </div>
                                
                                <div class="row">
                                    <?php foreach ($certificates as $cert): ?>
                                        <div class="col-md-6 mb-4">
                                            <div class="certificate-card">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h6 class="mb-0">
                                                        <i class="fas fa-award text-primary me-2"></i>
                                                        Certificado
                                                    </h6>
                                                    <span class="certificate-code"><?php echo htmlspecialchars($cert['unique_code']); ?></span>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <h5 class="text-primary mb-2"><?php echo htmlspecialchars($cert['course_name']); ?></h5>
                                                    <p class="text-muted mb-1">
                                                        <i class="fas fa-user me-2"></i>
                                                        <?php echo htmlspecialchars($cert['participant_name']); ?>
                                                    </p>
                                                    <p class="text-muted mb-1">
                                                        <i class="fas fa-clock me-2"></i>
                                                        Carga horária: <?php echo htmlspecialchars($cert['workload']); ?>
                                                    </p>
                                                    <p class="text-muted mb-1">
                                                        <i class="fas fa-calendar me-2"></i>
                                                        Curso: <?php echo date('d/m/Y', strtotime($cert['course_date'])); ?>
                                                    </p>
                                                    <p class="text-muted mb-0">
                                                        <i class="fas fa-certificate me-2"></i>
                                                        Emitido: <?php echo date('d/m/Y', strtotime($cert['issue_date'])); ?>
                                                    </p>
                                                </div>
                                                
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <a href="validate.php?code=<?php echo $cert['unique_code']; ?>" 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="fas fa-eye me-1"></i>Visualizar
                                                    </a>
                                                    <?php if (file_exists('../../' . $cert['file_path'])): ?>
                                                        <a href="download.php?code=<?php echo $cert['unique_code']; ?>" 
                                                           class="btn btn-success btn-sm">
                                                            <i class="fas fa-download me-1"></i>Baixar
                                                        </a>
                                                    <?php endif; ?>
                                                    <button type="button" class="btn btn-outline-info btn-sm" 
                                                            onclick="copyCode('<?php echo $cert['unique_code']; ?>')">
                                                        <i class="fas fa-copy me-1"></i>Código
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong><?php echo count($certificates); ?></strong> certificado(s) encontrado(s) para este e-mail.
                                    </div>
                                </div>
                            </div>
                            
                        <?php else: ?>
                            <!-- No Results -->
                            <div class="search-card">
                                <div class="no-results">
                                    <i class="fas fa-search fa-5x mb-4"></i>
                                    <h4>Nenhum certificado encontrado</h4>
                                    <p class="mb-4">
                                        Não foram encontrados certificados para o e-mail <strong><?php echo htmlspecialchars($email); ?></strong>.
                                    </p>
                                    <div class="alert alert-warning">
                                        <h6><i class="fas fa-info-circle me-2"></i>Possíveis motivos:</h6>
                                        <ul class="mb-0 text-start">
                                            <li>E-mail digitado incorretamente</li>
                                            <li>Ainda não há certificados emitidos para este e-mail</li>
                                            <li>Certificados podem ter sido emitidos com outro e-mail</li>
                                        </ul>
                                    </div>
                                    <div class="mt-4">
                                        <a href="validate.php" class="btn btn-primary me-2">
                                            <i class="fas fa-search me-2"></i>Validar por Código
                                        </a>
                                        <button type="button" class="btn btn-outline-secondary" onclick="searchAgain()">
                                            <i class="fas fa-redo me-2"></i>Tentar Novamente
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyCode(code) {
            navigator.clipboard.writeText(code).then(function() {
                // Create a temporary toast notification
                const toast = document.createElement('div');
                toast.className = 'alert alert-success position-fixed';
                toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 250px;';
                toast.innerHTML = '<i class="fas fa-check me-2"></i>Código copiado!';
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 3000);
            });
        }
        
        function searchAgain() {
            document.getElementById('email').focus();
            document.getElementById('email').select();
        }
        
        // Auto-focus on email input if no email is provided
        <?php if (empty($email)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });
        <?php endif; ?>
    </script>
</body>
</html>

