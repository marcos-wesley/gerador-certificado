<?php
require_once '../config/auth.php';
require_once '../classes/Course.php';
require_once '../classes/Template.php';

requireLogin();

$course = new Course();
$template = new Template();
$message = '';
$message_type = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'create') {
        $name = trim($_POST['name']);
        $workload = trim($_POST['workload']);
        $date = $_POST['date'];
        $responsible = trim($_POST['responsible']);
        $description = trim($_POST['description']);
        $template_id = !empty($_POST['template_id']) ? $_POST['template_id'] : null;
        
        if (!empty($name) && !empty($workload) && !empty($date) && !empty($responsible)) {
            $result = $course->create($name, $workload, $date, $responsible, $description, $template_id);
            if ($result) {
                $message = 'Curso criado com sucesso!';
                $message_type = 'success';
            } else {
                $message = 'Erro ao criar curso.';
                $message_type = 'danger';
            }
        } else {
            $message = 'Por favor, preencha todos os campos obrigatórios.';
            $message_type = 'warning';
        }
    } elseif ($action == 'update') {
        $id = $_POST['id'];
        $name = trim($_POST['name']);
        $workload = trim($_POST['workload']);
        $date = $_POST['date'];
        $responsible = trim($_POST['responsible']);
        $description = trim($_POST['description']);
        $template_id = !empty($_POST['template_id']) ? $_POST['template_id'] : null;
        
        if (!empty($name) && !empty($workload) && !empty($date) && !empty($responsible)) {
            $result = $course->update($id, $name, $workload, $date, $responsible, $description, $template_id);
            if ($result) {
                $message = 'Curso atualizado com sucesso!';
                $message_type = 'success';
            } else {
                $message = 'Erro ao atualizar curso.';
                $message_type = 'danger';
            }
        } else {
            $message = 'Por favor, preencha todos os campos obrigatórios.';
            $message_type = 'warning';
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'];
        $result = $course->delete($id);
        if ($result) {
            $message = 'Curso excluído com sucesso!';
            $message_type = 'success';
        } else {
            $message = 'Erro ao excluir curso.';
            $message_type = 'danger';
        }
    }
}

$courses = $course->getAll();
$edit_course = null;

if (isset($_GET['edit'])) {
    $edit_course = $course->getById($_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cursos/Eventos - Sistema de Certificados</title>
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
                        <a class="nav-link active" href="courses.php">
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
                        <h5 class="mb-0 aneti-heading">Cursos/Eventos</h5>
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
                    
                    <!-- Form -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-plus me-2"></i>
                                        <?php echo $edit_course ? 'Editar Curso/Evento' : 'Novo Curso/Evento'; ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="<?php echo $edit_course ? 'update' : 'create'; ?>">
                                        <?php if ($edit_course): ?>
                                            <input type="hidden" name="id" value="<?php echo $edit_course['id']; ?>">
                                        <?php endif; ?>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="name" class="form-label">Nome do Curso/Evento *</label>
                                                <input type="text" class="form-control" id="name" name="name" 
                                                       value="<?php echo $edit_course ? htmlspecialchars($edit_course['name']) : ''; ?>" required>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="workload" class="form-label">Carga Horária *</label>
                                                <input type="text" class="form-control" id="workload" name="workload" 
                                                       placeholder="Ex: 40 horas"
                                                       value="<?php echo $edit_course ? htmlspecialchars($edit_course['workload']) : ''; ?>" required>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="date" class="form-label">Data *</label>
                                                <input type="date" class="form-control" id="date" name="date" 
                                                       value="<?php echo $edit_course ? $edit_course['date'] : ''; ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="responsible" class="form-label">Responsável *</label>
                                                <input type="text" class="form-control" id="responsible" name="responsible" 
                                                       value="<?php echo $edit_course ? htmlspecialchars($edit_course['responsible']) : ''; ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="template_id" class="form-label">Modelo de Certificado</label>
                                                <select class="form-select" id="template_id" name="template_id">
                                                    <option value="">Modelo Padrão</option>
                                                    <?php 
                                                    $templates = $template->getAll();
                                                    foreach ($templates as $t): 
                                                    ?>
                                                        <option value="<?php echo $t['id']; ?>" 
                                                                <?php echo ($edit_course && $edit_course['template_id'] == $t['id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($t['name']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="description" class="form-label">Descrição</label>
                                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo $edit_course ? htmlspecialchars($edit_course['description']) : ''; ?></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex gap-2 mt-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>
                                                <?php echo $edit_course ? 'Atualizar' : 'Criar'; ?>
                                            </button>
                                            <?php if ($edit_course): ?>
                                                <a href="courses.php" class="btn btn-secondary">
                                                    <i class="fas fa-times me-2"></i>Cancelar
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- List -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-list me-2"></i>
                                        Cursos/Eventos Cadastrados
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($courses)): ?>
                                        <div class="text-center py-4">
                                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Nenhum curso/evento cadastrado ainda.</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nome</th>
                                                        <th>Carga Horária</th>
                                                        <th>Data</th>
                                                        <th>Responsável</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($courses as $c): ?>
                                                        <tr>
                                                            <td>
                                                                <strong><?php echo htmlspecialchars($c['name']); ?></strong>
                                                                <?php if (!empty($c['description'])): ?>
                                                                    <br><small class="text-muted"><?php echo htmlspecialchars($c['description']); ?></small>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?php echo htmlspecialchars($c['workload']); ?></td>
                                                            <td><?php echo date('d/m/Y', strtotime($c['date'])); ?></td>
                                                            <td><?php echo htmlspecialchars($c['responsible']); ?></td>
                                                                                               <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="?edit=<?php echo $c['id']; ?>" class="btn btn-outline-primary">
                                                                        <i class="fas fa-edit me-1"></i>Editar
                                                                    </a>
                                                                    <a href="../public/presence.php?id=<?php echo $c['id']; ?>" 
                                                                       class="btn btn-outline-success" target="_blank" 
                                                                       title="Link para lista de presença pública">
                                                                        <i class="fas fa-link me-1"></i>Presença
                                                                    </a>
                                                                    <button type="button" class="btn btn-outline-danger" 
                                                                            onclick="deleteCourse(<?php echo $c['id']; ?>, '<?php echo htmlspecialchars($c['name']); ?>')">
                                                                        <i class="fas fa-trash me-1"></i>Excluir
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

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir o curso/evento <strong id="courseName"></strong>?</p>
                    <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteCourse(id, name) {
            document.getElementById('deleteId').value = id;
            document.getElementById('courseName').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html>

