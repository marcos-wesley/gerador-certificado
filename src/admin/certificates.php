<?php
require_once '../config/auth.php';
require_once '../classes/Course.php';
require_once '../classes/Participant.php';
require_once '../classes/Certificate.php';
require_once '../classes/Presence.php';

requireLogin();

// Tratamento AJAX para carregar participantes
if (isset($_GET['ajax']) && $_GET['ajax'] == '1' && isset($_GET['course_id'])) {
    header('Content-Type: application/json');
    
    $presence = new Presence();
    $course_id = $_GET['course_id'];
    $present_participants = $presence->getPresentParticipants($course_id);
    
    echo json_encode(['participants' => $present_participants]);
    exit;
}

$course = new Course();
$participant = new Participant();
$certificate = new Certificate();
$presence = new Presence();
$message = '';
$message_type = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'generate') {
        $course_id = $_POST['course_id'];
        $participant_id = $_POST['participant_id'];
        
        $result = $certificate->create($course_id, $participant_id);
        if ($result['success']) {
            $message = 'Certificado gerado com sucesso! Código: ' . $result['unique_code'];
            $message_type = 'success';
        } else {
            $message = $result['message'];
            $message_type = 'danger';
        }
    } elseif ($action == 'generate_bulk') {
        $course_id = $_POST['course_id'];
        $present_participants = $presence->getPresentParticipants($course_id);
        
        $generated = 0;
        $errors = [];
        
        foreach ($present_participants as $p) {
            $result = $certificate->create($course_id, $p['participant_id']);
            if ($result['success']) {
                $generated++;
            } else {
                $errors[] = $p['name'] . ': ' . $result['message'];
            }
        }
        
        if ($generated > 0) {
            $message = "Gerados {$generated} certificados com sucesso!";
            $message_type = 'success';
            
            if (!empty($errors)) {
                $message .= " Erros: " . implode(', ', $errors);
            }
        } else {
            $message = 'Nenhum certificado foi gerado. Verifique se há participantes presentes.';
            $message_type = 'warning';
        }
    } elseif ($action == 'export_csv') {
        $filters = [
            'course_id' => $_POST['filter_course_id'] ?? '',
            'participant_email' => $_POST['filter_email'] ?? '',
            'participant_name' => $_POST['filter_name'] ?? ''
        ];
        
        $csv_data = $certificate->exportToCSV($filters);
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="certificados_' . date('Y-m-d') . '.csv"');
        echo $csv_data;
        exit;
    }
}

$courses = $course->getAll();
$filters = [
    'course_id' => $_GET['filter_course_id'] ?? '',
    'participant_email' => $_GET['filter_email'] ?? '',
    'participant_name' => $_GET['filter_name'] ?? ''
];

$certificates = $certificate->getAll($filters);
$selected_course_id = $_GET['course_id'] ?? '';
$present_participants = [];

if ($selected_course_id) {
    $present_participants = $presence->getPresentParticipants($selected_course_id);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificados - Sistema de Certificados</title>
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
                        <div>
                            <h4 class="aneti-brand-text">ANETI</h4>
                            <p class="aneti-brand-subtitle">Certificados</p>
                        </div>
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
                        <a class="nav-link" href="presences.php">
                            <i class="fas fa-check-circle me-2"></i>Lista de Presença
                        </a>
                        <a class="nav-link active" href="certificates.php">
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
                        <h5 class="mb-0 aneti-heading">Certificados</h5>
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
                    
                    <!-- Generate Certificates -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-plus me-2"></i>
                                        Gerar Certificados
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <button type="button" class="btn btn-primary w-100 py-3" data-bs-toggle="modal" data-bs-target="#generateModal">
                                                <i class="fas fa-certificate fa-2x mb-2 d-block"></i>
                                                Gerar Certificado Individual
                                            </button>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <button type="button" class="btn btn-success w-100 py-3" data-bs-toggle="modal" data-bs-target="#bulkGenerateModal">
                                                <i class="fas fa-layer-group fa-2x mb-2 d-block"></i>
                                                Gerar Certificados em Lote
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-filter me-2"></i>
                                        Filtros e Exportação
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="GET">
                                        <div class="row align-items-end">
                                            <div class="col-md-3 mb-3">
                                                <label for="filter_course_id" class="form-label">Curso</label>
                                                <select class="form-select" id="filter_course_id" name="filter_course_id">
                                                    <option value="">Todos os cursos</option>
                                                    <?php foreach ($courses as $c): ?>
                                                        <option value="<?php echo $c['id']; ?>" 
                                                                <?php echo $filters['course_id'] == $c['id'] ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($c['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="filter_name" class="form-label">Nome do Participante</label>
                                                <input type="text" class="form-control" id="filter_name" name="filter_name" 
                                                       value="<?php echo htmlspecialchars($filters['participant_name']); ?>">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="filter_email" class="form-label">E-mail</label>
                                                <input type="email" class="form-control" id="filter_email" name="filter_email" 
                                                       value="<?php echo htmlspecialchars($filters['participant_email']); ?>">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="d-flex gap-2">
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-search me-1"></i>Filtrar
                                                    </button>
                                                    <button type="button" class="btn btn-outline-success" onclick="exportCSV()">
                                                        <i class="fas fa-download me-1"></i>CSV
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Certificates List -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-list me-2"></i>
                                        Certificados Emitidos
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($certificates)): ?>
                                        <div class="text-center py-4">
                                            <i class="fas fa-certificate fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Nenhum certificado encontrado.</p>
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateModal">
                                                <i class="fas fa-plus me-2"></i>Gerar Primeiro Certificado
                                            </button>
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
                                                    <?php foreach ($certificates as $cert): ?>
                                                        <tr>
                                                            <td>
                                                                <code><?php echo htmlspecialchars($cert['unique_code']); ?></code>
                                                            </td>
                                                            <td>
                                                                <strong><?php echo htmlspecialchars($cert['participant_name']); ?></strong><br>
                                                                <small class="text-muted"><?php echo htmlspecialchars($cert['participant_email']); ?></small>
                                                            </td>
                                                            <td>
                                                                <?php echo htmlspecialchars($cert['course_name']); ?><br>
                                                                <small class="text-muted"><?php echo date('d/m/Y', strtotime($cert['course_date'])); ?></small>
                                                            </td>
                                                            <td><?php echo date('d/m/Y H:i', strtotime($cert['issue_date'])); ?></td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="../public/validate.php?code=<?php echo $cert['unique_code']; ?>" 
                                                                       class="btn btn-outline-primary" target="_blank">
                                                                        <i class="fas fa-eye me-1"></i>Ver
                                                                    </a>
                                                                    <button type="button" class="btn btn-outline-info" 
                                                                            onclick="copyToClipboard('<?php echo $cert['unique_code']; ?>')">
                                                                        <i class="fas fa-copy me-1"></i>Código
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Generate Individual Modal -->
    <div class="modal fade" id="generateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gerar Certificado Individual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="generate">
                        
                        <div class="mb-3">
                            <label for="course_id" class="form-label">Curso/Evento</label>
                            <select class="form-select" id="course_id" name="course_id" required onchange="loadParticipants(this.value)">
                                <option value="">Selecione um curso</option>
                                <?php foreach ($courses as $c): ?>
                                    <option value="<?php echo $c['id']; ?>">
                                        <?php echo htmlspecialchars($c['name']); ?> - <?php echo date('d/m/Y', strtotime($c['date'])); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="participant_id" class="form-label">Participante Presente</label>
                            <select class="form-select" id="participant_id" name="participant_id" required>
                                <option value="">Primeiro selecione um curso</option>
                            </select>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Apenas participantes marcados como presentes podem receber certificados.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Gerar Certificado</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bulk Generate Modal -->
    <div class="modal fade" id="bulkGenerateModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gerar Certificados em Lote</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="generate_bulk">
                        
                        <div class="mb-3">
                            <label for="bulk_course_id" class="form-label">Curso/Evento</label>
                            <select class="form-select" id="bulk_course_id" name="course_id" required>
                                <option value="">Selecione um curso</option>
                                <?php foreach ($courses as $c): ?>
                                    <option value="<?php echo $c['id']; ?>">
                                        <?php echo htmlspecialchars($c['name']); ?> - <?php echo date('d/m/Y', strtotime($c['date'])); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Atenção:</strong> Esta ação irá gerar certificados para TODOS os participantes marcados como presentes no curso selecionado.
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Certificados já emitidos para participantes não serão duplicados.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Gerar Todos os Certificados</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function loadParticipants(courseId) {
            const participantSelect = document.getElementById('participant_id');
            participantSelect.innerHTML = '<option value="">Carregando...</option>';
            
            if (!courseId) {
                participantSelect.innerHTML = '<option value="">Primeiro selecione um curso</option>';
                return;
            }
            
            // Fazer requisição AJAX para carregar participantes
            fetch('certificates.php?ajax=1&course_id=' + courseId)
                .then(response => response.json())
                .then(data => {
                    participantSelect.innerHTML = '<option value="">Selecione um participante</option>';
                    
                    if (data.participants && data.participants.length > 0) {
                        data.participants.forEach(participant => {
                            const option = document.createElement('option');
                            option.value = participant.participant_id;
                            option.textContent = participant.name + ' (' + participant.email + ')';
                            participantSelect.appendChild(option);
                        });
                    } else {
                        participantSelect.innerHTML = '<option value="">Nenhum participante presente encontrado</option>';
                    }
                })
                .catch(error => {
                    console.error('Erro ao carregar participantes:', error);
                    participantSelect.innerHTML = '<option value="">Erro ao carregar participantes</option>';
                });
        }
        
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Código copiado para a área de transferência!');
            });
        }
        
        function exportCSV() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'export_csv';
            form.appendChild(actionInput);
            
            // Adicionar filtros atuais
            const courseFilter = document.getElementById('filter_course_id').value;
            const nameFilter = document.getElementById('filter_name').value;
            const emailFilter = document.getElementById('filter_email').value;
            
            if (courseFilter) {
                const courseInput = document.createElement('input');
                courseInput.type = 'hidden';
                courseInput.name = 'filter_course_id';
                courseInput.value = courseFilter;
                form.appendChild(courseInput);
            }
            
            if (nameFilter) {
                const nameInput = document.createElement('input');
                nameInput.type = 'hidden';
                nameInput.name = 'filter_name';
                nameInput.value = nameFilter;
                form.appendChild(nameInput);
            }
            
            if (emailFilter) {
                const emailInput = document.createElement('input');
                emailInput.type = 'hidden';
                emailInput.name = 'filter_email';
                emailInput.value = emailFilter;
                form.appendChild(emailInput);
            }
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        }
        
        // Carregar participantes se um curso estiver selecionado
        <?php if ($selected_course_id && !empty($present_participants)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const participantSelect = document.getElementById('participant_id');
            participantSelect.innerHTML = '<option value="">Selecione um participante</option>';
            
            <?php foreach ($present_participants as $p): ?>
            const option = document.createElement('option');
            option.value = '<?php echo $p['participant_id']; ?>';
            option.textContent = '<?php echo htmlspecialchars($p['name']); ?> (<?php echo htmlspecialchars($p['email']); ?>)';
            participantSelect.appendChild(option);
            <?php endforeach; ?>
            
            document.getElementById('course_id').value = '<?php echo $selected_course_id; ?>';
        });
        <?php endif; ?>
    </script>
</body>
</html>

