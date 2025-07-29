<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function login($user_id, $username) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['login_time'] = time();
}

function logout() {
    session_unset();
    session_destroy();
}

function getUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

function getUsername() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}

// Verifica se a sessão expirou (opcional - 2 horas)
function checkSessionTimeout() {
    if (isset($_SESSION['login_time'])) {
        $timeout = 2 * 60 * 60; // 2 horas em segundos
        if (time() - $_SESSION['login_time'] > $timeout) {
            logout();
            return false;
        }
    }
    return true;
}
?>

