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
    <link href="../assets/aneti-style.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Estilos específicos do dashboard */
        .dashboard-stats {
            margin-bottom: 2rem;
        }
        
        .quick-actions {
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="aneti-sidebar">
                    <div class="aneti-logo-container">
                        <img src="../../assets/logo-branca.png" alt="ANETI" class="aneti-logo">
                        <div>
                            <h4 class="aneti-brand-text">ANETI</h4>
                            <p class="aneti-brand-subtitle">Certificados</p>
                        </div>
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
                        <i class="fas fa-award me-2"></i>
                        Certificados
                    </a>
                    <a class="nav-link" href="templates.php">
                        <i class="fas fa-palette me-2"></i>
                        Modelos
                    </a>
                    <a class="nav-link text-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Sair
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <!-- Top Navbar -->
                <nav class="aneti-navbar navbar navbar-expand-lg">
                    <div class="container-fluid">
                        <h5 class="mb-0 aneti-heading">Dashboard</h5>
                        <div class="navbar-nav ms-auto">
                            <span class="navbar-text aneti-text">
                                <i class="fas fa-user me-2"></i>
                                <?php echo htmlspecialchars(getUsername()); ?>
                            </span>
                        </div>
                    </div>
                </nav>
                
                <!-- Dashboard Content -->
                <div class="container-fluid p-4">
                    <!-- Statistics Cards -->
                    <div class="row mb-4 dashboard-stats">
                        <div class="col-md-4 mb-3">
                            <div class="aneti-stat-card aneti-fade-in">
                                <i class="fas fa-graduation-cap aneti-stat-icon"></i>
                                <div class="aneti-stat-number"><?php echo $total_courses; ?></div>
                                <div class="aneti-stat-label">Cursos/Eventos</div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="aneti-stat-card aneti-fade-in" style="animation-delay: 0.1s;">
                                <i class="fas fa-users aneti-stat-icon"></i>
                                <div class="aneti-stat-number"><?php echo $total_participants; ?></div>
                                <div class="aneti-stat-label">Participantes</div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="aneti-stat-card aneti-fade-in" style="animation-delay: 0.2s;">
                                <i class="fas fa-certificate aneti-stat-icon"></i>
                                <div class="aneti-stat-number"><?php echo $total_certificates; ?></div>
                                <div class="aneti-stat-label">Certificados Emitidos</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Certificates -->
                    <div class="row">
                        <div class="col-12">
                            <div class="aneti-card">
                                <div class="aneti-card-header">
                                    <h5 class="mb-0 aneti-heading">
                                        <i class="fas fa-clock me-2"></i>
                                        Certificados Recentes
                                    </h5>
                                </div>
                                <div class="aneti-card-body">
                                    <?php if (empty($recent_certificates)): ?>
                                        <div class="text-center py-4">
                                            <i class="fas fa-certificate fa-3x aneti-text-secondary mb-3"></i>
                                            <p class="aneti-text">Nenhum certificado emitido ainda.</p>
                                            <a href="certificates.php" class="aneti-btn aneti-btn-primary">
                                                <i class="fas fa-plus me-2"></i>Emitir Primeiro Certificado
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="aneti-table">
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

