<?php
include 'config.php';
// Preparar dados do usuário a partir da sessão (se disponível)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_js = [];
if (!empty($_SESSION['usuario_id'])) {
    $user_js = [
        'id' => (int)($_SESSION['usuario_id'] ?? 0),
        'nome' => $_SESSION['usuario_nome'] ?? '',
        'email' => $_SESSION['usuario_email'] ?? '',
        'nickname' => $_SESSION['usuario_nickname'] ?? '',
        'foto' => $_SESSION['usuario_foto'] ?? '',
        'bio' => $_SESSION['usuario_bio'] ?? '',
        'rank' => $_SESSION['usuario_rank'] ?? '',
        'plataforma' => $_SESSION['usuario_plataforma'] ?? '',
        'nivel' => $_SESSION['usuario_nivel'] ?? 1,
        'matches' => $_SESSION['usuario_matches'] ?? 0,
        'jogos' => $_SESSION['usuario_jogos'] ?? 0,
        'online' => true,
        'data_criacao' => $_SESSION['usuario_data_criacao'] ?? ''
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JK Game Match - Meu Perfil</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php // Injetar dados da sessão antes dos scripts do perfil e reutilizar o HTML estático ?>
    <script src="session_user.js.php"></script>
    <?php include 'perfil.html'; ?>

</body>
</html>
