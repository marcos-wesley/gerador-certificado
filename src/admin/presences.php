<?php
require_once '../config/auth.php';
require_once '../classes/Course.php';
require_once '../classes/Participant.php';
require_once '../classes/Presence.php';

requireLogin();

$course = new Course();
$participant = new Participant();
$presence = new Presence();
$message = '';
$message_type = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'mark_presence') {
        $course_id = $_POST['course_id'];
        $participant_id = $_POST['participant_id'];
        $is_present = isset($_POST['is_present']) ? 1 : 0;
        
        $result = $presence->markPresence($course_id, $participant_id, $is_present);
        if ($result) {
            $message = 'Presença atualizada com sucesso!';
            $message_type = 'success';
        } else {
            $message = 'Erro ao atualizar presença.';
            $message_type = 'danger';
        }
    } elseif ($action == 'add_participant') {
        $course_id = $_POST['course_id'];
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        
        if (!empty($name) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result = $presence->addParticipantToCourse($course_id, $name, $email);
            if ($result) {
                $message = 'Participante adicionado e marcado como presente!';
                $message_type = 'success';
            } else {
                $message = 'Erro ao adicionar participante.';
                $message_type = 'danger';
            }
        } else {
            $message = 'Por favor, preencha nome e e-mail válidos.';
            $message_type = 'warning';
        }
    } elseif ($action == 'upload_csv') {
        $course_id = $_POST['course_id'];
        
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
            $result = $participant->importFromCSV($_FILES['csv_file']['tmp_name'], $course_id);
            
            if ($result['imported'] > 0) {
                $message = "Importados {$result['imported']} participantes com sucesso!";
                $message_type = 'success';
                
                if (!empty($result['errors'])) {
                    $message .= " Erros: " . implode(', ', $result['errors']);
                }
            } else {
                $message = 'Nenhum participante foi importado. Verifique o formato do arquivo.';
                $message_type = 'warning';
            }
        } else {
            $message = 'Erro no upload do arquivo.';
            $message_type = 'danger';
        }
    }
}

$courses = $course->getAll();
$selected_course_id = $_GET['course_id'] ?? ($_POST['course_id'] ?? '');
$presences = [];

if ($selected_course_id) {
    $presences = $presence->getPresencesByCourse($selected_course_id);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Presença - Sistema de Certificados</title>
    <link href="../../assets/aneti-style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="aneti-sidebar">
                    <div class="aneti-logo-container">
                        <img src="../../assets/logo-branca.png" alt="ANETI" class="aneti-logo">
                    </div>
                    
                    <nav class="nav flex-column px-3">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="courses.php">
                            <i class="fas fa-graduation-cap me-2"></i>Cursos/Eventos
                        </a>
                        <a class="nav-link" href="participants.php">
                            <i class="fas fa-users me-2"></i>Participantes
                        </a>
                        <a class="nav-link active" href="presences.php">
                            <i class="fas fa-check-circle me-2"></i>Lista de Presença
                        </a>
                        <a class="nav-link" href="certificates.php">
                            <i class="fas fa-award me-2"></i>Certificados
                        </a>
                        <a class="nav-link" href="templates.php">
                            <i class="fas fa-palette me-2"></i>Modelos
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
                        <h5 class="mb-0 aneti-heading">Lista de Presença</h5>
                        <div class="navbar-nav ms-auto">
                            <span class="navbar-text aneti-text">
                                <i class="fas fa-user me-2"></i>
                                <?php echo htmlspecialchars(getUsername()); ?>
                            </span>
                        </div>
                    </div>
                </nav>
                
                <!-- Content -->
                <div class="container-fluid p-4">
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($message); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Course Selection -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-graduation-cap me-2"></i>
                                        Selecionar Curso/Evento
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="GET">
                                        <div class="row align-items-end">
                                            <div class="col-md-8 mb-3">
                                                <label for="course_id" class="form-label">Curso/Evento</label>
                                                <select class="form-select" id="course_id" name="course_id" required>
                                                    <option value="">Selecione um curso/evento</option>
                                                    <?php foreach ($courses as $c): ?>
                                                        <option value="<?php echo $c['id']; ?>" 
                                                                <?php echo $selected_course_id == $c['id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($c['name']); ?> - <?php echo date('d/m/Y', strtotime($c['date'])); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-search me-2"></i>Carregar Lista
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($selected_course_id): ?>
                        <!-- Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fas fa-tools me-2"></i>
                                            Ações Rápidas
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#addParticipantModal">
                                                    <i class="fas fa-user-plus me-2"></i>Adicionar Participante
                                                </button>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <button type="button" class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#uploadCsvModal">
                                                    <i class="fas fa-upload me-2"></i>Importar Lista CSV
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Presence List -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="fas fa-list-check me-2"></i>
                                            Lista de Presença
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if (empty($presences)): ?>
                                            <div class="text-center py-4">
                                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">Nenhum participante na lista de presença ainda.</p>
                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addParticipantModal">
                                                    <i class="fas fa-plus me-2"></i>Adicionar Primeiro Participante
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Participante</th>
                                                            <th>E-mail</th>
                                                            <th class="text-center">Presente</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($presences as $p): ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($p['name']); ?></td>
                                                                <td><?php echo htmlspecialchars($p['email']); ?></td>
                                                                <td class="text-center">
                                                                    <form method="POST" style="display: inline;">
                                                                        <input type="hidden" name="action" value="mark_presence">
                                                                        <input type="hidden" name="course_id" value="<?php echo $selected_course_id; ?>">
                                                                        <input type="hidden" name="participant_id" value="<?php echo $p['participant_id']; ?>">
                                                                        <input type="checkbox" class="form-check-input presence-checkbox" 
                                                                               name="is_present" 
                                                                               <?php echo $p['is_present'] ? 'checked' : ''; ?>
                                                                               onchange="this.form.submit()">
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            
                                            <div class="mt-3">
                                                <?php 
                                                $present_count = count(array_filter($presences, function($p) { return $p['is_present']; }));
                                                $total_count = count($presences);
                                                ?>
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    <strong><?php echo $present_count; ?></strong> de <strong><?php echo $total_count; ?></strong> participantes marcados como presentes.
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Participant Modal -->
    <div class="modal fade" id="addParticipantModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Participante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_participant">
                        <input type="hidden" name="course_id" value="<?php echo $selected_course_id; ?>">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome Completo</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            O participante será automaticamente marcado como presente.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Adicionar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Upload CSV Modal -->
    <div class="modal fade" id="uploadCsvModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Importar Lista de Presença via CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="upload_csv">
                        <input type="hidden" name="course_id" value="<?php echo $selected_course_id; ?>">
                        
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">Arquivo CSV</label>
                            <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                        </div>
                        
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>Formato do arquivo:</h6>
                            <p class="mb-2">O arquivo CSV deve conter as colunas: <strong>Nome, E-mail</strong></p>
                            <p class="mb-0 text-muted"><small>Exemplo:<br>João Silva, joao@email.com<br>Maria Santos, maria@email.com</small></p>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Todos os participantes importados serão automaticamente marcados como presentes.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Importar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

