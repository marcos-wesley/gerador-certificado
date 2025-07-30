<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal de Validação de Certificados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #012d6a 34%, #25a244 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .hero-section {
            padding: 100px 0 0 0;
            color: white;
            text-align: center;
        }

        .logo {
            width: 350px;
            margin-bottom: 50px;
        }

        .validation-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            transition: transform 0.3s ease;
        }
        .validation-card:hover {
            transform: translateY(-5px);
        }
        .search-input {
            border-radius: 15px;
            border: 2px solid #e9ecef;
            padding: 15px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .search-input:focus {
            border-color: #012d6a;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-search {
            background: linear-gradient(135deg, #012d6a 34%, #25a244 100%);
            border: none;
            border-radius: 15px;
            padding: 15px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 30px;
        }
        .feature-card:hover {
            transform: translateY(-5px);
        }
        .feature-icon {
            background: linear-gradient(135deg, #012d6a 34%, #25a244 100%);
            color: white;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
        }
        .footer {
            background: rgba(0, 0, 0, 0.1);
            color: white;
            padding: 40px 0;
            margin-top: 80px;
        }
        .navbar-custom {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        .navbar-custom .navbar-brand,
        .navbar-custom .nav-link {
            color: white !important;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-certificate me-2"></i>
                Portal de Certificados - ANETI
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#validacao">Validar Certificado</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#consulta">Consultar por E-mail</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                <img class="logo" src="../assets/img/logo-branca.png">
                    <h1 class="display-4 fw-bold mb-4">
                        <i class="fas fa-shield-alt me-3"></i>
                        Portal de Validação e Emissão de Certificados
                    </h1>
                    <p class="lead mb-5">
                        Verifique a autenticidade dos certificados emitidos pela ANETI de forma rápida e segura.
                        <br>Digite o código do certificado ou consulte por e-mail.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Validation Section -->
    <section id="validacao" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="validation-card">
                        <div class="text-center mb-4">
                            <div class="feature-icon mx-auto">
                                <i class="fas fa-search"></i>
                            </div>
                            <h3>Validar Certificado</h3>
                            <p class="text-muted">Digite o código único do certificado para verificar sua autenticidade</p>
                        </div>
                        
                        <form action="validate.php" method="GET">
                            <div class="mb-4">
                                <label for="code" class="form-label">Código do Certificado</label>
                                <input type="text" class="form-control search-input" id="code" name="code" 
                                       placeholder="Ex: CERT-123456789" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-search w-100">
                                <i class="fas fa-search me-2"></i>Validar Certificado
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Email Search Section -->
    <section id="consulta" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <div class="validation-card">
                        <div class="text-center mb-4">
                            <div class="feature-icon mx-auto">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <h3>Consultar por E-mail</h3>
                            <p class="text-muted">Digite seu e-mail para ver todos os certificados emitidos</p>
                        </div>
                        
                        <form action="search.php" method="GET">
                            <div class="mb-4">
                                <label for="email" class="form-label">E-mail</label>
                                <input type="email" class="form-control search-input" id="email" name="email" 
                                       placeholder="seu@email.com" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-search w-100">
                                <i class="fas fa-envelope me-2"></i>Buscar Certificados
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="text-white fw-bold">Como Funciona</h2>
                    <p class="text-white-50">Processo simples e seguro de validação</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <h5>1. Digite o Código</h5>
                        <p class="text-muted">Insira o código único presente no certificado que você deseja validar.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield"></i>
                        </div>
                        <h5>2. Verificação Automática</h5>
                        <p class="text-muted">Nosso sistema verifica automaticamente a autenticidade do certificado.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-download"></i>
                        </div>
                        <h5>3. Visualize e Baixe</h5>
                        <p class="text-muted">Visualize todas as informações e faça o download do certificado original.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-certificate me-2"></i>Portal de Certificados</h5>
                    <p class="mb-0">Sistema seguro de validação e consulta de certificados.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        <a href="../admin/login.php" class="text-white text-decoration-none">
                            <i class="fas fa-user-shield me-1"></i>Área Administrativa
                        </a>
                    </p>
                    <small class="text-white-50">© <?php echo date('Y'); ?> - Todos os direitos reservados</small>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Auto-format certificate code input
        document.getElementById('code').addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase();
            e.target.value = value;
        });
    </script>
</body>
</html>

