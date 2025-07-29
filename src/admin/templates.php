<?php
require_once '../config/auth.php';
require_once '../classes/Template.php';

requireLogin();

$template = new Template();
$message = '';
$message_type = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action == 'create') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        
        // Upload do arquivo
        if (isset($_FILES['template_file']) && $_FILES['template_file']['error'] == 0) {
            $upload_result = $template->uploadFile($_FILES['template_file']);
            
            if ($upload_result['success']) {
                $fields_config = $template->getDefaultFields();
                $result = $template->create($name, $description, $upload_result['file_path'], $fields_config);
                
                if ($result['success']) {
                    $message = $result['message'];
                    $message_type = 'success';
                } else {
                    $message = $result['message'];
                    $message_type = 'danger';
                }
            } else {
                $message = $upload_result['message'];
                $message_type = 'danger';
            }
        } else {
            $message = 'Por favor, selecione um arquivo para o modelo.';
            $message_type = 'danger';
        }
    } elseif ($action == 'update') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        
        // Verificar se há novo arquivo
        if (isset($_FILES['template_file']) && $_FILES['template_file']['error'] == 0) {
            $upload_result = $template->uploadFile($_FILES['template_file']);
            
            if ($upload_result['success']) {
                $fields_config = $template->getDefaultFields();
                $result = $template->update($id, $name, $description, $upload_result['file_path'], $fields_config);
            } else {
                $message = $upload_result['message'];
                $message_type = 'danger';
            }
        } else {
            $result = $template->update($id, $name, $description);
        }
        
        if (isset($result)) {
            $message = $result['message'];
            $message_type = $result['success'] ? 'success' : 'danger';
        }
    } elseif ($action == 'delete') {
        $id = $_POST['id'];
        $result = $template->delete($id);
        $message = $result['message'];
        $message_type = $result['success'] ? 'success' : 'danger';
    }
}

$templates = $template->getAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modelos de Certificado - Sistema de Certificados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .sidebar {
            background: linear-gradient(180deg, #2c3e50 0%, #34495e 100%);
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: #bdc3c7;
            padding: 15px 20px;
            border-radius: 8px;
            margin: 5px 10px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: rgba(52, 152, 219, 0.2);
            color: #3498db;
            transform: translateX(5px);
        }
        .main-content {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            margin: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .btn-primary {
            background: linear-gradient(45deg, #3498db, #2980b9);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        .template-preview {
            max-width: 200px;
            max-height: 150px;
            object-fit: cover;
            border-radius: 10px;
        }
        .template-card {
            transition: all 0.3s ease;
        }
        .template-card:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <div class="p-3">
                    <h4 class="text-white mb-4">
                        <i class="fas fa-certificate me-2"></i>
                        Certificados
                    </h4>
                    <p class="text-muted small">Painel Administrativo</p>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link" href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        Dashboard
                    </a>
                    <a class="nav-link" href="courses.php">
                        <i class="fas fa-graduation-cap me-2"></i>
                        Cursos/Eventos
                    </a>
                    <a class="nav-link" href="participants.php">
                        <i class="fas fa-users me-2"></i>
                        Participantes
                    </a>
                    <a class="nav-link" href="presences.php">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Lista de Presença
                    </a>
                    <a class="nav-link" href="certificates.php">
                        <i class="fas fa-award me-2"></i>
                        Certificados
                    </a>
                    <a class="nav-link active" href="templates.php">
                        <i class="fas fa-palette me-2"></i>
                        Modelos
                    </a>
                    <a class="nav-link text-danger" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>
                        Sair
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10">
                <div class="main-content">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>
                            <i class="fas fa-palette me-3"></i>
                            Modelos de Certificado
                        </h2>
                        <div class="text-end">
                            <span class="text-muted">Bem-vindo, <?php echo $_SESSION['username']; ?></span>
                        </div>
                    </div>

                    <?php if ($message): ?>
                        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Botão Novo Modelo -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center">
                                    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#createModal">
                                        <i class="fas fa-plus me-2"></i>
                                        Novo Modelo de Certificado
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Modelos -->
                    <div class="row">
                        <?php foreach ($templates as $t): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card template-card h-100">
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <?php 
                                            $file_extension = pathinfo($t['file_path'], PATHINFO_EXTENSION);
                                            if (in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png'])): 
                                            ?>
                                                <img src="../../<?php echo $t['file_path']; ?>" 
                                                     alt="Preview" class="template-preview img-fluid">
                                            <?php else: ?>
                                                <div class="template-preview bg-light d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-file-pdf fa-3x text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <h5 class="card-title"><?php echo htmlspecialchars($t['name']); ?></h5>
                                        <p class="card-text text-muted small">
                                            <?php echo htmlspecialchars($t['description']); ?>
                                        </p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                Criado em: <?php echo date('d/m/Y H:i', strtotime($t['created_at'])); ?>
                                            </small>
                                        </p>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <div class="btn-group w-100">
                                            <button type="button" class="btn btn-outline-primary btn-sm" 
                                                    onclick="editTemplate(<?php echo $t['id']; ?>, '<?php echo htmlspecialchars($t['name']); ?>', '<?php echo htmlspecialchars($t['description']); ?>')">
                                                <i class="fas fa-edit me-1"></i>Editar
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                    onclick="deleteTemplate(<?php echo $t['id']; ?>, '<?php echo htmlspecialchars($t['name']); ?>')">
                                                <i class="fas fa-trash me-1"></i>Excluir
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if (empty($templates)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-palette fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">Nenhum modelo encontrado</h4>
                            <p class="text-muted">Clique em "Novo Modelo" para começar.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Novo Modelo de Certificado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome do Modelo</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="template_file" class="form-label">Arquivo do Modelo</label>
                            <input type="file" class="form-control" id="template_file" name="template_file" 
                                   accept=".jpg,.jpeg,.png,.pdf" required>
                            <div class="form-text">
                                Formatos aceitos: JPG, PNG, PDF. Tamanho máximo: 5MB.
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Dica:</strong> Após criar o modelo, você poderá configurar a posição dos campos dinâmicos (nome, curso, etc.) no certificado.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Criar Modelo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Modelo de Certificado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" id="edit_id">
                        
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Nome do Modelo</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Descrição</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_template_file" class="form-label">Novo Arquivo do Modelo (opcional)</label>
                            <input type="file" class="form-control" id="edit_template_file" name="template_file" 
                                   accept=".jpg,.jpeg,.png,.pdf">
                            <div class="form-text">
                                Deixe em branco para manter o arquivo atual. Formatos aceitos: JPG, PNG, PDF.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
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
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="delete_id">
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Tem certeza que deseja excluir o modelo "<span id="delete_name"></span>"?
                        </div>
                        
                        <p class="text-muted">Esta ação não pode ser desfeita.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir Modelo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editTemplate(id, name, description) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            
            const editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        }
        
        function deleteTemplate(id, name) {
            document.getElementById('delete_id').value = id;
            document.getElementById('delete_name').textContent = name;
            
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
</body>
</html>

