<?php
// ============================================
// JK Game Match - Index Principal
// Redireciona para login ou dashboard
// ============================================

include 'config.php';

// Se usuário já está logado, redireciona para dashboard
if (isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id'])) {
    header("Location: dashboard.html");
    exit();
}

// Caso contrário, redireciona para página de boas-vindas
header("Location: welcome.php");
exit();

?>
