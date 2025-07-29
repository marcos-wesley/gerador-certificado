<?php
require_once '../config/database.php';
require_once '../classes/Course.php';
require_once '../classes/Participant.php';
require_once '../classes/Presence.php';

$course = new Course();
$participant = new Participant();
$presence = new Presence();
$message = '';
$message_type = '';

// Verificar se foi passado ID do curso
$course_id = $_GET['id'] ?? '';
$course_data = null;

if ($course_id) {
    $course_data = $course->getById($course_id);
    if (!$course_data) {
        $message = 'Curso não encontrado.';
        $message_type = 'danger';
    }
}

// Processar formulário de presença
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $course_data) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    
    if (!empty($name) && !empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Verificar se o participante já existe
        $existing_participant = $participant->getByEmail($email);
        
        if ($existing_participant) {
            $participant_id = $existing_participant['id'];
        } else {
            // Criar novo participante
            $participant_id = $participant->create($name, $email);
        }
        
        if ($participant_id) {
            // Marcar presença
            $result = $presence->markPresence($course_id, $participant_id, true);
            
            if ($result) {
                $message = 'Presença confirmada com sucesso! Você receberá o certificado após a conclusão do curso.';
                $message_type = 'success';
            } else {
                // Verificar se já estava marcado como presente
                $existing_presence = $presence->getPresence($course_id, $participant_id);
                if ($existing_presence && $existing_presence['is_present']) {
                    $message = 'Sua presença já foi confirmada anteriormente.';
                    $message_type = 'info';
                } else {
                    $message = 'Erro ao confirmar presença. Tente novamente.';
                    $message_type = 'danger';
                }
            }
        } else {
            $message = 'Erro ao processar dados. Tente novamente.';
            $message_type = 'danger';
        }
    } else {
        $message = 'Por favor, preencha todos os campos com dados válidos.';
        $message_type = 'warning';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Presença - <?php echo $course_data ? htmlspecialchars($course_data['name']) : 'Sistema de Certificados'; ?></title>
    <link href="../assets/aneti-style.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #e0f2f7 0%, #cce7f0 100%);
            min-height: 100vh;
            color: var(--aneti-navy);
        }
        
        .main-container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            backdrop-filter: blur(5px);
            margin: 30px auto;
            max-width: 700px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .header {
            background: var(--aneti-blue);
            color: white;
            padding: 30px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: rgba(255, 255, 255, 0.05);
            transform: rotate(45deg);
            z-index: 0;
        }
        
        .header h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 5px;
            position: relative;
            z-index: 1;
        }
        
        .header p {
            font-size: 1rem;
            opacity: 0.9;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        
        .content {
            padding: 30px 20px;
        }
        
        .course-info {
            background: #f0f4f8;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 4px solid var(--aneti-light-blue);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .course-info h3 {
            color: var(--aneti-blue);
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 1.5rem;
        }
        
        .course-detail {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            color: var(--aneti-navy);
            font-size: 0.95rem;
        }
        
        .course-detail i {
            width: 20px;
            margin-right: 8px;
            color: var(--aneti-light-blue);
        }
        
        .form-container {
            background: #ffffff;
            border-radius: 10px;
            padding: 25px;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 10px rgba(0,0,0,0.03);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--aneti-navy);
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        
        .form-control {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 0.95rem;
            transition: all 0.2s ease-in-out;
        }
        
        .form-control:focus {
            border-color: var(--aneti-light-blue);
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--aneti-blue), var(--aneti-light-blue));
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 15px;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(30, 58, 138, 0.3);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .footer {
            text-align: center;
            padding: 15px;
            color: var(--aneti-gray);
            border-top: 1px solid #e9ecef;
            font-size: 0.85rem;
        }
        
        .icon-large {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 15px;
        }
        
        .form-text {
            font-size: 0.8rem;
            color: var(--aneti-gray);
            margin-top: 5px;
        }
        
        /* Ajustes para o layout geral */
        .container {
            padding: 0;
        }
        
        /* Media Queries para responsividade */
        @media (max-width: 768px) {
            .main-container {
                margin: 15px;
            }
            .header h1 {
                font-size: 1.8rem;
            }
            .content {
                padding: 20px 15px;
            }
            .course-info h3 {
                font-size: 1.3rem;
            }
            .btn-primary {
                padding: 10px 20px;
                font-size: 0.95rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="main-container">
            <div class="header">
                <i class="fas fa-clipboard-check icon-large"></i>
                <h1>Lista de Presença</h1>
                <p>Confirme sua participação no evento</p>
            </div>
            
            <div class="content">
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>">
                        <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : ($message_type == 'info' ? 'info-circle' : ($message_type == 'warning' ? 'exclamation-triangle' : 'times-circle')); ?> me-2"></i>
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!$course_data): ?>
                    <div class="text-center">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h3 class="text-muted">Curso não encontrado</h3>
                        <p class="text-muted">Verifique se o link está correto ou entre em contato com o organizador.</p>
                    </div>
                <?php else: ?>
                    <div class="course-info">
                        <h3><?php echo htmlspecialchars($course_data['name']); ?></h3>
                        
                        <div class="course-detail">
                            <i class="fas fa-clock"></i>
                            <span><strong>Carga Horária:</strong> <?php echo htmlspecialchars($course_data['workload']); ?></span>
                        </div>
                        
                        <div class="course-detail">
                            <i class="fas fa-calendar"></i>
                            <span><strong>Data:</strong> <?php echo date('d/m/Y', strtotime($course_data['date'])); ?></span>
                        </div>
                        
                        <div class="course-detail">
                            <i class="fas fa-user-tie"></i>
                            <span><strong>Responsável:</strong> <?php echo htmlspecialchars($course_data['responsible']); ?></span>
                        </div>
                        
                        <?php if ($course_data['description']): ?>
                            <div class="course-detail">
                                <i class="fas fa-info-circle"></i>
                                <span><strong>Descrição:</strong> <?php echo htmlspecialchars($course_data['description']); ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-container">
                        <h4 class="mb-4 text-center">
                            <i class="fas fa-edit me-2"></i>
                            Confirmar Presença
                        </h4>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-2"></i>
                                    Nome Completo
                                </label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="Digite seu nome completo" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>
                                    E-mail
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="Digite seu e-mail" required>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Seu certificado será vinculado a este e-mail.
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-2"></i>
                                Confirmar Presença
                            </button>
                        </form>
                    </div>
                    
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>Importante:</strong> Após confirmar sua presença, você receberá o certificado automaticamente quando o curso for finalizado. O certificado poderá ser validado através do portal público.
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="footer">
                <p>
                    <i class="fas fa-shield-alt me-2"></i>
                    Sistema Seguro de Certificados
                </p>
                <small>Seus dados estão protegidos e serão usados apenas para emissão do certificado.</small>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

