<?php
// Redireciona para a instalação se o sistema não estiver instalado
// ou para o portal público se já estiver instalado

require_once 'config.php';

if (isSystemInstalled()) {
    header('Location: src/public/index.php');
} else {
    header('Location: install.php');
}
exit;
?>

