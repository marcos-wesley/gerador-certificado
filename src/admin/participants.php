<?php
require_once '../config/auth.php';
require_once '../classes/Participant.php';

requireLogin();

$participant = new Participant();
$message = '';
$message_type = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'create') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        
        if (!empty($name) && !empty($email)) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result = $participant->create($name, $email);
                if ($result) {
                    $message = 'Participante criado com sucesso!';
                    $message_type = 'success';
                } else {
                    $message = 'Erro ao criar participante.';
                    $message_type = 'danger';
                }
            } else {
                $message = 'Por favor, insira um e-mail válido.';
                $message_type = 'warning';
            }
        } else {
            $message = 'Por favor, preencha todos os campos.';
            $message_type = 'warning';
        }
    } elseif ($action == 'update') {
        $id = $_POST['id'];
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        
        if (!empty($name) && !empty($email)) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result = $participant->update($id, $name, $email);
                if ($result) {
                    $message = 'Participante atualizado com sucesso!';
                    $message_type = 'success';
                } else {
                    $message = 'Erro ao atualizar participante.';
                    $message_type = 'danger';
                }
            } else {
                $message = 'Por favor, insira um e-mail válido.';
                $message_type = 'warning';
            }
        } else {
            $message = 'Por favor, preencha todos os campos.';
            $message_type = 'warning';
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'];
        $result = $participant->delete($id);
        if ($result) {
            $message = 'Participante excluído com sucesso!';
            $message_type = 'success';
        } else {
            $message = 'Erro ao excluir participante.';
            $message_type = 'danger';
        }
    }
}

$participants = $participant->getAll();
$edit_participant = null;

if (isset($_GET['edit'])) {
    $edit_participant = $participant->getById($_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participantes - Sistema de Certificados</title>
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
                        <a class="nav-link active" href="participants.php">
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
                        <h5 class="mb-0 aneti-heading">Participantes</h5>
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
                                        <i class="fas fa-user-plus me-2"></i>
                                        <?php echo $edit_participant ? 'Editar Participante' : 'Novo Participante'; ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST">
                                        <input type="hidden" name="action" value="<?php echo $edit_participant ? 'update' : 'create'; ?>">
                                        <?php if ($edit_participant): ?>
                                            <input type="hidden" name="id" value="<?php echo $edit_participant['id']; ?>">
                                        <?php endif; ?>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="name" class="form-label">Nome Completo *</label>
                                                <input type="text" class="form-control" id="name" name="name" 
                                                       value="<?php echo $edit_participant ? htmlspecialchars($edit_participant['name']) : ''; ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="email" class="form-label">E-mail *</label>
                                                <input type="email" class="form-control" id="email" name="email" 
                                                       value="<?php echo $edit_participant ? htmlspecialchars($edit_participant['email']) : ''; ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-2"></i>
                                                <?php echo $edit_participant ? 'Atualizar' : 'Criar'; ?>
                                            </button>
                                            <?php if ($edit_participant): ?>
                                                <a href="participants.php" class="btn btn-secondary">
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
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="fas fa-list me-2"></i>
                                        Participantes Cadastrados
                                    </h5>
                                    <div>
                                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
                                            <i class="fas fa-upload me-2"></i>Importar CSV
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($participants)): ?>
                                        <div class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Nenhum participante cadastrado ainda.</p>
                                        </div>
                                    <?php else: ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th>Nome</th>
                                                        <th>E-mail</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($participants as $p): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($p['name']); ?></td>
                                                            <td><?php echo htmlspecialchars($p['email']); ?></td>
                                                            <td>
                                                                <div class="btn-group btn-group-sm">
                                                                    <a href="participants.php?edit=<?php echo $p['id']; ?>" 
                                                                       class="btn btn-outline-primary">
                                                                        <i class="fas fa-edit"></i>
                                                                    </a>
                                                                    <button type="button" class="btn btn-outline-danger" 
                                                                            onclick="deleteParticipant(<?php echo $p['id']; ?>, '<?php echo htmlspecialchars($p['name']); ?>')">
                                                                        <i class="fas fa-trash"></i>
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
                    <p>Tem certeza que deseja excluir o participante <strong id="participantName"></strong>?</p>
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

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Importar Participantes via CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>O arquivo CSV deve conter as colunas: <strong>Nome, E-mail</strong></p>
                    <p class="text-muted"><small>Exemplo:<br>João Silva, joao@email.com<br>Maria Santos, maria@email.com</small></p>
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="import_csv">
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">Arquivo CSV</label>
                            <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Importar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteParticipant(id, name) {
            document.getElementById('deleteId').value = id;
            document.getElementById('participantName').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html>

