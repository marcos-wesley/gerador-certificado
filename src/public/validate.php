<?php
require_once '../classes/Certificate.php';

$certificate = new Certificate();
$cert_data = null;
$error_message = '';
$code = $_GET['code'] ?? '';

if (!empty($code)) {
    $cert_data = $certificate->getByCode($code);
    if (!$cert_data) {
        $error_message = 'Certificado não encontrado. Verifique se o código está correto.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validação de Certificado - Portal de Certificados</title>
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
        .validation-container {
            padding: 100px 0 50px;
        }
        .result-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-bottom: 30px;
        }
        .certificate-preview {
            border: 3px solid #667eea;
            border-radius: 15px;
            padding: 30px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            text-align: center;
            margin: 20px 0;
        }
        .certificate-title {
            font-size: 2.5rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 20px;
        }
        .participant-name {
            font-size: 1.8rem;
            font-weight: bold;
            color: #333;
            margin: 20px 0;
            text-decoration: underline;
        }
        .course-name {
            font-size: 1.3rem;
            font-weight: bold;
            color: #667eea;
            margin: 15px 0;
        }
        .certificate-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .status-valid {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        .status-invalid {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
        }
        .search-again {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
                        <a class="nav-link" href="search.php">Consultar por E-mail</a>
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
    <div class="validation-container">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <?php if ($cert_data): ?>
                        <!-- Valid Certificate -->
                        <div class="status-valid">
                            <h4 class="mb-0">
                                <i class="fas fa-check-circle me-2"></i>
                                Certificado Válido e Autêntico
                            </h4>
                        </div>
                        
                        <div class="result-card">
                            <div class="certificate-preview">
                                <div class="certificate-title">CERTIFICADO</div>
                                <p class="mb-3">Certificamos que</p>
                                <div class="participant-name"><?php echo htmlspecialchars($cert_data['participant_name']); ?></div>
                                <p class="mb-3">participou do curso/evento</p>
                                <div class="course-name"><?php echo htmlspecialchars($cert_data['course_name']); ?></div>
                                <p class="mb-3">
                                    com carga horária de <?php echo htmlspecialchars($cert_data['workload']); ?>,<br>
                                    realizado em <?php echo date('d/m/Y', strtotime($cert_data['course_date'])); ?>.
                                </p>
                                <div class="mt-4">
                                    <p><strong>Responsável:</strong> <?php echo htmlspecialchars($cert_data['responsible']); ?></p>
                                    <p><strong>Data de emissão:</strong> <?php echo date('d/m/Y', strtotime($cert_data['issue_date'])); ?></p>
                                </div>
                                <div class="mt-3">
                                    <small><strong>Código:</strong> <?php echo htmlspecialchars($cert_data['unique_code']); ?></small>
                                </div>
                            </div>
                            
                            <div class="certificate-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-user me-2"></i>Informações do Participante</h6>
                                        <p class="mb-1"><strong>Nome:</strong> <?php echo htmlspecialchars($cert_data['participant_name']); ?></p>
                                        <p class="mb-0"><strong>E-mail:</strong> <?php echo htmlspecialchars($cert_data['participant_email']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-graduation-cap me-2"></i>Informações do Curso</h6>
                                        <p class="mb-1"><strong>Curso:</strong> <?php echo htmlspecialchars($cert_data['course_name']); ?></p>
                                        <p class="mb-1"><strong>Carga Horária:</strong> <?php echo htmlspecialchars($cert_data['workload']); ?></p>
                                        <p class="mb-0"><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($cert_data['course_date'])); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <div class="d-flex gap-3 justify-content-center flex-wrap">
                                    <?php if (file_exists('../../' . $cert_data['file_path'])): ?>
                                        <a href="download.php?code=<?php echo $cert_data['unique_code']; ?>" 
                                           class="btn btn-success">
                                            <i class="fas fa-download me-2"></i>Baixar Certificado
                                        </a>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                                        <i class="fas fa-print me-2"></i>Imprimir
                                    </button>
                                    <button type="button" class="btn btn-outline-info" onclick="shareLink()">
                                        <i class="fas fa-share me-2"></i>Compartilhar
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                    <?php elseif (!empty($code)): ?>
                        <!-- Invalid Certificate -->
                        <div class="status-invalid">
                            <h4 class="mb-0">
                                <i class="fas fa-times-circle me-2"></i>
                                Certificado Não Encontrado
                            </h4>
                        </div>
                        
                        <div class="result-card">
                            <div class="text-center">
                                <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
                                <h3>Código Inválido</h3>
                                <p class="text-muted mb-4">
                                    O código <strong><?php echo htmlspecialchars($code); ?></strong> não foi encontrado em nossa base de dados.
                                </p>
                                <div class="alert alert-warning">
                                    <h6><i class="fas fa-info-circle me-2"></i>Possíveis causas:</h6>
                                    <ul class="mb-0 text-start">
                                        <li>Código digitado incorretamente</li>
                                        <li>Certificado ainda não foi emitido</li>
                                        <li>Código expirado ou inválido</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Search Again -->
                    <div class="search-again">
                        <h5 class="text-center mb-4">
                            <i class="fas fa-search me-2"></i>
                            Validar Outro Certificado
                        </h5>
                        <form action="validate.php" method="GET">
                            <div class="row align-items-end">
                                <div class="col-md-8 mb-3">
                                    <label for="code" class="form-label">Código do Certificado</label>
                                    <input type="text" class="form-control search-input" id="code" name="code" 
                                           placeholder="Ex: CERT-123456789" value="<?php echo htmlspecialchars($code); ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <button type="submit" class="btn btn-primary btn-search w-100">
                                        <i class="fas fa-search me-2"></i>Validar
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="text-muted mb-2">Ou</p>
                            <a href="search.php" class="btn btn-outline-secondary">
                                <i class="fas fa-envelope me-2"></i>Consultar por E-mail
                            </a>
                            <a href="index.php" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-home me-2"></i>Voltar ao Início
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-format certificate code input
        document.getElementById('code').addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase();
            e.target.value = value;
        });

        // Share link function
        function shareLink() {
            const url = window.location.href;
            if (navigator.share) {
                navigator.share({
                    title: 'Certificado Validado',
                    text: 'Confira este certificado validado',
                    url: url
                });
            } else {
                // Fallback: copy to clipboard
                navigator.clipboard.writeText(url).then(function() {
                    alert('Link copiado para a área de transferência!');
                });
            }
        }

        // Print styles
        const printStyles = `
            @media print {
                .navbar, .search-again, .btn { display: none !important; }
                .result-card { box-shadow: none; border: 1px solid #ddd; }
                body { background: white !important; }
            }
        `;
        const styleSheet = document.createElement("style");
        styleSheet.type = "text/css";
        styleSheet.innerText = printStyles;
        document.head.appendChild(styleSheet);
    </script>
</body>
</html>

