<?php
require_once '../config/auth.php';
require_once '../classes/Course.php';
require_once '../classes/Participant.php';
require_once '../classes/Certificate.php';

requireLogin();

$course = new Course();
$participant = new Participant();
$certificate = new Certificate();

$total_courses = count($course->getAll());
$total_participants = count($participant->getAll());
$total_certificates = count($certificate->getAll());

$recent_certificates = $certificate->getAll();
$recent_certificates = array_slice($recent_certificates, 0, 5); // Últimos 5
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Certificados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-card-2 {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .stat-card-3 {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <div class="p-4">
                        <h4><i class="fas fa-certificate me-2"></i>Certificados</h4>
                        <small>Painel Administrativo</small>
                    </div>
                    
                    <nav class="nav flex-column px-3">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="courses.php">
                            <i class="fas fa-graduation-cap me-2"></i>Cursos/Eventos
                        </a>
                        <a class="nav-link" href="participants.php">
                            <i class="fas fa-users me-2"></i>Participantes
                        </a>
                        <a class="nav-link" href="presences.php">
                            <i class="fas fa-check-circle me-2"></i>Lista de Presença
                        </a>
                        <a class="nav-link" href="certificates.php">
                            <i class="fas fa-award me-2"></i>Certificados
                        </a>
                        <hr class="my-3">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Sair
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <!-- Top Navbar -->
                <nav class="navbar navbar-expand-lg navbar-light">
                    <div class="container-fluid">
                        <h5 class="mb-0">Dashboard</h5>
                        <div class="navbar-nav ms-auto">
                            <span class="navbar-text">
                                <i class="fas fa-user me-2"></i>
                                Bem-vindo, <?php echo htmlspecialchars(getUsername()); ?>
                            </span>
                        </div>
                    </div>
                </nav>
                
                <!-- Dashboard Content -->
                <div class="container-fluid p-4">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <div class="card stat-card">
                                <div class="card-body text-center">
                                    <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                                    <h3><?php echo $total_courses; ?></h3>
                                    <p class="mb-0">Cursos/Eventos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card stat-card-2">
                                <div class="card-body text-center">
                                    <i class="fas fa-users fa-3x mb-3"></i>
                                    <h3><?php echo $total_participants; ?></h3>
                                    <p class="mb-0">Participantes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card stat-card-3">
                                <div class="card-body text-center">
                                    <i class="fas fa-certificate fa-3x mb-3"></i>
                                    <h3><?php echo $total_certificates; ?></h3>
                                    <p class="mb-0">Certificados Emitidos</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Certificates -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-clock me-2"></i>
                                        Certificados Recentes
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($recent_certificates)): ?>
                                        <div class="text-center py-4">
                                            <i class="fas fa-certificate fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Nenhum certificado emitido ainda.</p>
                                            <a href="certificates.php" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Emitir Primeiro Certificado
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Código</th>
                                                        <th>Participante</th>
                                                        <th>Curso</th>
                                                        <th>Data de Emissão</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($recent_certificates as $cert): ?>
                                                        <tr>
                                                            <td>
                                                                <code><?php echo htmlspecialchars($cert['unique_code']); ?></code>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($cert['participant_name']); ?></td>
                                                            <td><?php echo htmlspecialchars($cert['course_name']); ?></td>
                                                            <td><?php echo date('d/m/Y H:i', strtotime($cert['issue_date'])); ?></td>
                                                            <td>
                                                                <a href="../public/validate.php?code=<?php echo $cert['unique_code']; ?>" 
                                                                   class="btn btn-sm btn-outline-primary" target="_blank">
                                                                    <i class="fas fa-eye me-1"></i>Ver
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-center mt-3">
                                            <a href="certificates.php" class="btn btn-primary">
                                                <i class="fas fa-list me-2"></i>Ver Todos os Certificados
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-bolt me-2"></i>
                                        Ações Rápidas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <a href="courses.php?action=new" class="btn btn-outline-primary w-100 py-3">
                                                <i class="fas fa-plus fa-2x mb-2 d-block"></i>
                                                Novo Curso
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="participants.php?action=new" class="btn btn-outline-success w-100 py-3">
                                                <i class="fas fa-user-plus fa-2x mb-2 d-block"></i>
                                                Novo Participante
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="presences.php" class="btn btn-outline-warning w-100 py-3">
                                                <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                                                Marcar Presença
                                            </a>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <a href="certificates.php?action=generate" class="btn btn-outline-info w-100 py-3">
                                                <i class="fas fa-certificate fa-2x mb-2 d-block"></i>
                                                Gerar Certificado
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

