<?php
// dashboard_check.php
// Verifica se usuário está logado, caso contrário redireciona

session_start();

if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
    header("Location: index.html");
    exit();
}

// Se chegou aqui, usuário está autenticado
?>
